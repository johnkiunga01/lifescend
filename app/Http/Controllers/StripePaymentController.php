<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\Attendee;
use App\Models\Invoice;
use App\Models\ActivityLog;
use App\Models\User;
use App\Mail\TicketPurchaseMail;
use App\Mail\AdminNotificationMail;
use App\Models\Organization;
use App\Notifications\TicketPurchasedNotification;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripePaymentController extends Controller
{
    public function createIntent(Request $request, string $slug)
    {
        $event = Event::query()->where('slug', $slug)->firstOrFail();

        $request->validate([
            'tickets' => 'required|array|min:1',
            'tickets.*.ticket_type_id' => 'required|distinct|exists:ticket_types,id',
            'tickets.*.quantity' => 'required|integer|min:1|max:10',
            'participants' => 'nullable|array',
            'participants.*.ticket_type_id' => 'required_with:participants|integer',
            'participants.*.name' => 'nullable|string|max:255',
            'participants.*.email' => 'nullable|email|max:255',
            'coupon_code' => 'nullable|string|max:50',
            'reservation_type' => 'nullable|in:individual,company',
            'company_name' => 'nullable|string|max:255',
            'company_details' => 'nullable|string|max:2000',
            'contact_person' => 'nullable|string|max:255',
            'attendees_later' => 'nullable|boolean',
        ]);

        $ticketTypes = $this->validateTicketSelectionForUser($event, Auth::user(), $request->tickets);

        $totalAmount = 0;

        foreach ($request->tickets as $entry) {
            $tt = $ticketTypes[$entry['ticket_type_id']];
            $qty = (int) $entry['quantity'];

            $remaining = $tt->quantity - $tt->sold_count;
            if ($qty > $remaining) {
                return response()->json(['error' => "Only {$remaining} '{$tt->name}' tickets available. You requested {$qty}."], 422);
            }

            if ($tt->max_per_user && $qty > $tt->max_per_user) {
                return response()->json(['error' => "Maximum {$tt->max_per_user} '{$tt->name}' tickets per customer."], 422);
            }

            $totalAmount += $tt->payablePrice() * $qty;
        }

        $reservation = $this->validateReservation(
            $request->input('reservation_type', 'individual'),
            $request->input('company_name'),
            $request->input('company_details'),
            $request->input('contact_person'),
            $request->boolean('attendees_later'),
            $request->input('participants', []),
            (int) collect($request->tickets)->sum('quantity'),
        );

        $discountAmount = 0;

        if ($request->coupon_code) {
            $coupon = Coupon::query()->where('code', strtoupper($request->coupon_code))->first();
            $error = $coupon ? $coupon->validationError($event->id, Auth::id()) : 'Coupon code not found.';
            if ($error) {
                return response()->json(['error' => $error], 422);
            }
            $discountAmount = $coupon->calculateDiscount($totalAmount);
            if ($discountAmount <= 0) {
                return response()->json(['error' => 'Coupon does not apply to this order.'], 422);
            }
        }

        $finalAmount = max(0, $totalAmount - $discountAmount);
        $amountCents = (int) round($finalAmount * 100);

        if (!config('services.stripe.secret')) {
            return response()->json(['error' => 'Stripe secret key is not configured. Please set STRIPE_SECRET in your .env file.'], 500);
        }

        $currency = collect($ticketTypes)->first(fn ($type) => $type->payablePrice() > 0)?->checkoutCurrency() ?? 'USD';
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $intent = PaymentIntent::create([
                'amount' => max(50, $amountCents),
                'currency' => strtolower($currency),
                'metadata' => [
                    'event_id' => $event->id,
                    'event_slug' => $event->slug,
                    'tickets' => json_encode($request->tickets),
                    'user_id' => Auth::id(),
                    'coupon_code' => $request->coupon_code ?? '',
                    'discount_amount' => (string) $discountAmount,
                    'original_amount' => (string) $totalAmount,
                    'participants' => json_encode($request->input('participants', [])),
                    'reservation_type' => $reservation['reservation_type'],
                    'company_name' => $reservation['company_name'] ?? '',
                    'company_details' => $reservation['company_details'] ?? '',
                    'contact_person' => $reservation['contact_person'] ?? '',
                    'attendee_count' => (string) $reservation['attendee_count'],
                    'attendees_later' => $request->boolean('attendees_later') ? '1' : '0',
                ],
            ]);

            return response()->json([
                'client_secret' => $intent->client_secret,
                'payment_intent_id' => $intent->id,
                'amount' => $finalAmount,
                'discount' => $discountAmount,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'event_slug' => 'required|string',
            'tickets' => 'nullable|array',
            'tickets.*.ticket_type_id' => 'required_with:tickets|distinct|exists:ticket_types,id,is_active,1',
            'tickets.*.quantity' => 'required_with:tickets|integer|min:1',
            'coupon_code' => 'nullable|string|max:50',
            'participants' => 'nullable|array',
            'reservation_type' => 'nullable|in:individual,company',
            'company_name' => 'nullable|string|max:255',
            'company_details' => 'nullable|string|max:2000',
            'contact_person' => 'nullable|string|max:255',
            'attendees_later' => 'nullable|boolean',
        ]);

        if (!config('services.stripe.secret')) {
            return response()->json(['error' => 'Stripe secret key is not configured.'], 500);
        }

        $event = Event::query()->where('slug', $request->event_slug)->firstOrFail();
        $user = Auth::user();

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $intent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($intent->status !== 'succeeded') {
                return response()->json(['error' => 'Payment has not been completed.'], 422);
            }

            // Confirmation can be retried by the browser, and may race Stripe's webhook.
            // A completed order for this intent is the successful, idempotent result.
            if (Order::query()->where('stripe_payment_intent_id', $intent->id)->exists()) {
                return response()->json(['success' => true, 'redirect' => route('tickets.index')]);
            }

            $discountAmount = (float) ($intent->metadata['discount_amount'] ?? 0);
            $couponCode = $intent->metadata['coupon_code'] ?? null;

            $coupon = null;
            $couponError = null;
            if ($couponCode) {
                $coupon = Coupon::query()->where('code', strtoupper($couponCode))->first();
                if ($coupon) {
                    $couponError = $coupon->validationError($event->id, $user->id);
                } else {
                    $couponError = 'Coupon code not found.';
                }
            }

            $tickets = $request->tickets ?? json_decode($intent->metadata['tickets'] ?? '[]', true);

            if (empty($tickets)) {
                return response()->json(['error' => 'No ticket data found.'], 422);
            }

            $participants = $request->input('participants', json_decode($intent->metadata['participants'] ?? '[]', true));
            $reservation = $this->validateReservation(
                $intent->metadata['reservation_type'] ?? 'individual',
                $intent->metadata['company_name'] ?? null,
                $intent->metadata['company_details'] ?? null,
                $intent->metadata['contact_person'] ?? null,
                ($intent->metadata['attendees_later'] ?? '0') === '1',
                $participants,
                (int) collect($tickets)->sum('quantity'),
            );

            $order = null;
            DB::transaction(function () use ($event, $request, $user, $discountAmount, $intent, $coupon, $couponError, $couponCode, $tickets, $participants, $reservation, &$order) {
                if ($couponError) {
                    throw new \Exception("Coupon no longer valid: {$couponError}");
                }

                $ticketTypes = $this->validateTicketSelectionForUser($event, $user, $tickets, true);

                $firstType = collect($ticketTypes)->first(fn ($type) => $type->payablePrice() > 0);
                $totalAmount = 0;

                foreach ($tickets as $entry) {
                    $tt = $ticketTypes[(int) $entry['ticket_type_id']];

                    $qty = (int) $entry['quantity'];
                    $remaining = $tt->quantity - $tt->sold_count;
                    if ($qty > $remaining) {
                        throw new \Exception("Only {$remaining} '{$tt->name}' tickets remaining. Your order required {$qty}.");
                    }

                    $totalAmount += $tt->payablePrice() * $qty;
                }

                $finalAmount = max(0, $totalAmount - $discountAmount);

                $order = Order::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'total_amount' => $finalAmount,
                    'discount_amount' => $discountAmount,
                    'currency' => $firstType?->checkoutCurrency() ?? 'USD',
                    'status' => 'completed',
                    'stripe_payment_intent_id' => $intent->id,
                    'coupon_id' => $coupon?->id,
                    ...$reservation,
                ]);

                $participantIdx = 0;
                foreach ($tickets as $entry) {
                    $tt = $ticketTypes[(int) $entry['ticket_type_id']];
                    $qty = (int) $entry['quantity'];
                    $lineTotal = $tt->payablePrice() * $qty;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'ticket_type_id' => $tt->id,
                        'quantity' => $qty,
                        'unit_price' => $tt->payablePrice(),
                        'total_price' => $lineTotal,
                    ]);

                    for ($i = 0; $i < $qty; $i++) {
                        $ticketCode = 'TKT-' . strtoupper($event->slug) . '-' . strtoupper(str()->random(6));
                        $ticket = Ticket::create([
                            'event_id' => $event->id,
                            'user_id' => $user->id,
                            'order_id' => $order->id,
                            'ticket_type_id' => $tt->id,
                            'qr_code' => $ticketCode,
                            'status' => 'active',
                        ]);

                        $p = $participants[$participantIdx] ?? [];
                        Attendee::create([
                            'event_id' => $event->id,
                            'user_id' => $user->id,
                            'ticket_id' => $ticket->id,
                            'participant_name' => $p['name'] ?? null,
                            'participant_email' => $p['email'] ?? null,
                            'status' => 'registered',
                        ]);

                        $participantIdx++;
                    }

                    $tt->increment('sold_count', $qty, []);
                }

                if ($coupon && $discountAmount > 0) {
                    $coupon->increment('used_count', 1, []);
                    CouponUsage::create([
                        'coupon_id' => $coupon->id,
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'discount_amount' => $discountAmount,
                    ]);
                }

                Invoice::create([
                    'order_id' => $order->id,
                    'invoice_number' => 'INV-' . date('Y') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                    'pdf_path' => 'invoices/inv_' . $order->id . '.pdf',
                    'issued_at' => now(),
                ]);

                $ticketNames = collect($tickets)->map(fn($t) => (TicketType::query()->find($t['ticket_type_id'])->name ?? '?') . ' x' . $t['quantity'])->implode(', ');

                ActivityLog::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'action' => 'ticket_purchased',
                    'model_type' => Order::class,
                    'model_id' => $order->id,
                    'metadata' => [
                        'tickets' => $ticketNames,
                        'total' => $finalAmount,
                        'discount' => $discountAmount,
                        'coupon' => $coupon?->code,
                        'payment_intent' => $intent->id,
                    ],
                    'ip_address' => $request->ip(),
                ]);
            });

            /** @var \App\Models\Order $order */
            try {
                Mail::to($user->email)->send(new TicketPurchaseMail($order));
            } catch (\Exception $e) {}

            try {
                Notification::send(
                    User::whereHas('organizations', fn($q) => $q->where('role', 'manager'))->get(),
                    new TicketPurchasedNotification($order)
                );
            } catch (\Exception $e) {}

            try {
                $notifyEmail = Organization::firstOrFail()->settings['notification_email'] ?? null;
                if ($notifyEmail) {
                    $event = $order->event;
                    $user = $order->user;
                    $ticketNames = $order->items->map(fn($i) => $i->ticketType->name . ' x' . $i->quantity)->implode(', ');
                    Mail::to($notifyEmail)->send(new AdminNotificationMail(
                        subjectText: "New Ticket Purchase - {$event->name}",
                        body: "{$user->name} ({$user->email}) purchased tickets for {$event->name}.\n\nTickets: {$ticketNames}\nTotal: " . number_format($order->total_amount, 2) . ' ' . $order->currency,
                        actionUrl: route('manage.events.show', $event->slug),
                        actionText: 'View Order',
                    ));
                }
            } catch (\Exception $e) {}

            return response()->json(['success' => true, 'redirect' => route('tickets.index')]);
        } catch (ValidationException $e) {
            return response()->json(['error' => collect($e->errors())->flatten()->first(), 'errors' => $e->errors()], 422);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Another request (usually the webhook) completed this payment intent first.
            if (Order::query()->where('stripe_payment_intent_id', $request->payment_intent_id)->exists()) {
                return response()->json(['success' => true, 'redirect' => route('tickets.index')]);
            }

            return response()->json(['error' => 'This payment has already been processed.'], 409);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function webhook(Request $request)
    {
        $endpointSecret = config('services.stripe.webhook_secret');

        if (!$endpointSecret) {
            Log::error('Stripe webhook called but STRIPE_WEBHOOK_SECRET is not configured.');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            Log::warning('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $this->handlePaymentIntentSucceeded($event->data->object);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handlePaymentIntentSucceeded(\Stripe\PaymentIntent $intent): void
    {
        // Idempotency: the confirm() endpoint already creates the order on success.
        // The webhook is a fallback for when the client never reaches confirm().
        if (Order::where('stripe_payment_intent_id', $intent->id)->exists()) {
            return;
        }

        $metadata = $intent->metadata;
        $userId = $metadata['user_id'] ?? null;
        $eventId = $metadata['event_id'] ?? null;
        $tickets = json_decode($metadata['tickets'] ?? '[]', true);
        $participants = json_decode($metadata['participants'] ?? '[]', true);
        $discountAmount = (float) ($metadata['discount_amount'] ?? 0);
        $couponCode = $metadata['coupon_code'] ?? null;

        if (!$userId || !$eventId || empty($tickets)) {
            Log::error("Stripe webhook: incomplete metadata for intent {$intent->id}", compact('userId', 'eventId'));
            return;
        }

        $user = User::find($userId);
        $event = Event::find($eventId);

        if (!$user || !$event) {
            Log::error("Stripe webhook: user or event not found for intent {$intent->id}");
            return;
        }

        $coupon = $couponCode ? Coupon::where('code', strtoupper($couponCode))->first() : null;

        try {
            $reservation = $this->validateReservation(
                $metadata['reservation_type'] ?? 'individual',
                $metadata['company_name'] ?? null,
                $metadata['company_details'] ?? null,
                $metadata['contact_person'] ?? null,
                ($metadata['attendees_later'] ?? '0') === '1',
                $participants,
                (int) collect($tickets)->sum('quantity'),
            );

            DB::transaction(function () use ($event, $user, $tickets, $participants, $discountAmount, $coupon, $intent, $reservation) {
                $ticketTypes = $this->validateTicketSelectionForUser($event, $user, $tickets, true);
                $totalAmount = 0;
                $firstType = null;

                foreach ($tickets as $entry) {
                    $tt = $ticketTypes[(int) $entry['ticket_type_id']];
                    if ($tt->payablePrice() > 0) { $firstType ??= $tt; }
                    $totalAmount += $tt->payablePrice() * (int) $entry['quantity'];
                }

                $finalAmount = max(0, $totalAmount - $discountAmount);

                $order = Order::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'total_amount' => $finalAmount,
                    'discount_amount' => $discountAmount,
                    'currency' => $firstType?->checkoutCurrency() ?? 'USD',
                    'status' => 'completed',
                    'stripe_payment_intent_id' => $intent->id,
                    'coupon_id' => $coupon?->id,
                    ...$reservation,
                ]);

                $participantIdx = 0;
                foreach ($tickets as $entry) {
                    $tt = $ticketTypes[(int) $entry['ticket_type_id']];

                    $qty = (int) $entry['quantity'];

                    OrderItem::create([
                        'order_id' => $order->id,
                        'ticket_type_id' => $tt->id,
                        'quantity' => $qty,
                        'unit_price' => $tt->payablePrice(),
                        'total_price' => $tt->payablePrice() * $qty,
                    ]);

                    for ($i = 0; $i < $qty; $i++) {
                        $ticketCode = 'TKT-' . strtoupper($event->slug) . '-' . strtoupper(str()->random(6));
                        $ticket = Ticket::create([
                            'event_id' => $event->id,
                            'user_id' => $user->id,
                            'order_id' => $order->id,
                            'ticket_type_id' => $tt->id,
                            'qr_code' => $ticketCode,
                            'status' => 'active',
                        ]);

                        $p = $participants[$participantIdx] ?? [];
                        Attendee::create([
                            'event_id' => $event->id,
                            'user_id' => $user->id,
                            'ticket_id' => $ticket->id,
                            'participant_name' => $p['name'] ?? null,
                            'participant_email' => $p['email'] ?? null,
                            'status' => 'registered',
                        ]);
                        $participantIdx++;
                    }

                    $tt->increment('sold_count', $qty);
                }

                if ($coupon && $discountAmount > 0) {
                    $coupon->increment('used_count');
                    CouponUsage::create([
                        'coupon_id' => $coupon->id,
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'discount_amount' => $discountAmount,
                    ]);
                }

                Invoice::create([
                    'order_id' => $order->id,
                    'invoice_number' => 'INV-' . date('Y') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                    'pdf_path' => 'invoices/inv_' . $order->id . '.pdf',
                    'issued_at' => now(),
                ]);

                ActivityLog::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'action' => 'ticket_purchased',
                    'model_type' => Order::class,
                    'model_id' => $order->id,
                    'metadata' => ['payment_intent' => $intent->id, 'source' => 'webhook'],
                    'ip_address' => null,
                ]);

                try {
                    Mail::to($user->email)->send(new TicketPurchaseMail($order));
                } catch (\Exception $e) {
                    Log::error("Webhook: failed to send confirmation email for order {$order->id}: " . $e->getMessage());
                }

                try {
                    $notifyEmail = Organization::firstOrFail()->settings['notification_email'] ?? null;
                    if ($notifyEmail) {
                        $ticketNames = collect($tickets)->map(fn($t) => (TicketType::find($t['ticket_type_id'])->name ?? '?') . ' x' . $t['quantity'])->implode(', ');
                        Mail::to($notifyEmail)->send(new AdminNotificationMail(
                            subjectText: "New Ticket Purchase (Webhook) - {$event->name}",
                            body: "{$user->name} ({$user->email}) purchased tickets for {$event->name}.\n\nTickets: {$ticketNames}\nTotal: " . number_format($finalAmount, 2),
                            actionUrl: route('manage.events.show', $event->slug),
                            actionText: 'View Order',
                        ));
                    }
                } catch (\Exception $e) {
                    Log::error("Webhook: failed to send admin notification for order {$order->id}: " . $e->getMessage());
                }
            });
        } catch (\Exception $e) {
            Log::error("Stripe webhook: failed to process intent {$intent->id}: " . $e->getMessage());
        }
    }

    /**
     * Ensure this event owns every ticket type and enforce its cumulative per-customer limit.
     * This is run inside issuance transactions as well as before creating a payment intent.
     *
     * @return array<int, TicketType>
     */
    private function validateTicketSelectionForUser(Event $event, User $user, array $tickets, bool $lockForUpdate = false): array
    {
        $ticketTypeIds = collect($tickets)->pluck('ticket_type_id')->map(fn ($id) => (int) $id)->all();

        if (count($ticketTypeIds) !== count(array_unique($ticketTypeIds))) {
            throw ValidationException::withMessages([
                'tickets' => 'Each ticket type may only be selected once per purchase.',
            ]);
        }

        $ticketTypes = [];

        foreach ($tickets as $entry) {
            $ticketTypeId = (int) $entry['ticket_type_id'];
            $query = TicketType::query()
                ->whereKey($ticketTypeId)
                ->where('event_id', $event->id);

            if ($lockForUpdate) {
                $query->lockForUpdate();
            }

            $ticketType = $query->first();

            if (!$ticketType || !$ticketType->is_active) {
                throw ValidationException::withMessages([
                    'tickets' => 'One or more selected ticket types are unavailable for this event.',
                ]);
            }

            if ($ticketTypes && $ticketType->payablePrice() > 0 && collect($ticketTypes)->contains(fn ($type) => $type->payablePrice() > 0 && $type->checkoutCurrency() !== $ticketType->checkoutCurrency())) {
                throw ValidationException::withMessages(['tickets' => 'Tickets with different payment currencies cannot be combined.']);
            }

            $existingTickets = Ticket::query()
                ->where('user_id', $user->id)
                ->where('ticket_type_id', $ticketType->id)
                ->where('status', '!=', 'cancelled');

            if ($lockForUpdate) {
                $existingTickets->lockForUpdate();
            }

            $existingQuantity = $existingTickets->count();

            $requestedQuantity = (int) $entry['quantity'];
            if ($ticketType->max_per_user && $existingQuantity + $requestedQuantity > $ticketType->max_per_user) {
                throw ValidationException::withMessages([
                    'quantity' => "You already have {$existingQuantity} '{$ticketType->name}' ticket(s). The maximum is {$ticketType->max_per_user} per customer.",
                ]);
            }

            $ticketTypes[$ticketType->id] = $ticketType;
        }

        return $ticketTypes;
    }

    /** @return array{reservation_type: string, company_name: ?string, company_details: ?string, contact_person: ?string, attendee_count: int} */
    private function validateReservation(string $reservationType, ?string $companyName, ?string $companyDetails, ?string $contactPerson, bool $attendeesLater, array $participants, int $attendeeCount): array
    {
        if ($reservationType === 'company' && blank($companyName)) {
            throw ValidationException::withMessages(['company_name' => 'Enter the company name for a company reservation.']);
        }

        if ($reservationType === 'company' && blank($contactPerson)) {
            throw ValidationException::withMessages(['contact_person' => 'Enter a contact person for a company reservation.']);
        }

        if ($reservationType !== 'company') {
            $attendeesLater = false;
        }

        if (!$attendeesLater) {
            if (count($participants) !== $attendeeCount) {
                throw ValidationException::withMessages(['participants' => 'Enter attendee details for every reserved ticket, or choose to add them later for a company reservation.']);
            }

            foreach ($participants as $participant) {
                if (blank($participant['name'] ?? null) || !filter_var($participant['email'] ?? null, FILTER_VALIDATE_EMAIL)) {
                    throw ValidationException::withMessages(['participants' => 'Every attendee needs a name and a valid email address.']);
                }
            }
        }

        return [
            'reservation_type' => $reservationType,
            'company_name' => $reservationType === 'company' ? trim($companyName) : null,
            'company_details' => $reservationType === 'company' ? (trim((string) $companyDetails) ?: null) : null,
            'contact_person' => $reservationType === 'company' ? trim((string) $contactPerson) : null,
            'attendee_count' => $attendeeCount,
        ];
    }
}
