<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
use App\Notifications\TicketPurchasedNotification;

class EventController extends Controller
{
    public function show(string $slug)
    {
        $event = Event::with(['venue', 'speakers', 'topics', 'sessions', 'sessions.speakers', 'sessions.track', 'sessions.sessionType', 'sponsors', 'ticketTypes'])
            ->where('slug', $slug)
            ->firstOrFail();

        $isSaved = Auth::check() && $event->savedByUsers()->where('user_id', Auth::id())->exists();

        return view('events.show', compact('event', 'isSaved'));
    }

    public function purchase(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $request->validate([
            'tickets' => 'required|array|min:1',
            'tickets.*.ticket_type_id' => 'required|distinct|exists:ticket_types,id',
            'tickets.*.quantity' => 'required|integer|min:1|max:10',
            'coupon_code' => 'nullable|string|max:50',
            'participants' => 'nullable|array',
            'reservation_type' => 'nullable|in:individual,company',
            'company_name' => 'nullable|string|max:255',
            'company_details' => 'nullable|string|max:2000',
            'contact_person' => 'nullable|string|max:255',
            'attendees_later' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $ticketTypes = $this->validateTicketSelectionForUser($event, $user, $request->tickets);

        $totalAmount = 0;
        $currency = 'USD';

        foreach ($request->tickets as $entry) {
            $tt = $ticketTypes[$entry['ticket_type_id']];
            $qty = (int) $entry['quantity'];
            if (!isset($firstType)) { $firstType = $tt; $currency = $tt->currency; }

            $remaining = $tt->quantity - $tt->sold_count;
            if ($qty > $remaining) {
                return back()->withErrors(['quantity' => "Only {$remaining} '{$tt->name}' tickets available. You requested {$qty}."]);
            }

            if ($tt->max_per_user && $qty > $tt->max_per_user) {
                return back()->withErrors(['quantity' => "Maximum {$tt->max_per_user} '{$tt->name}' tickets per customer."]);
            }

            $totalAmount += $tt->price * $qty;
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
        $coupon = null;
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

            if (!$coupon) {
                return back()->withErrors(['coupon_code' => 'Coupon code not found.']);
            }

            $error = $coupon->validationError($event->id, $user->id);

            if ($error) {
                return back()->withErrors(['coupon_code' => $error]);
            }

            $discountAmount = $coupon->calculateDiscount($totalAmount);

            if ($discountAmount <= 0) {
                return back()->withErrors(['coupon_code' => 'Coupon does not apply to this order.']);
            }
        }

        $finalAmount = max(0, $totalAmount - $discountAmount);

        // Paid tickets must go through the Stripe checkout flow
        if ($finalAmount > 0) {
            return redirect()->route('checkout.show', $slug)
                ->with('info', 'Please complete your payment through the checkout.');
        }

        $order = null;

        DB::transaction(function () use ($event, $request, $user, $totalAmount, $discountAmount, $finalAmount, $coupon, $currency, $reservation, &$order) {
            $ticketTypes = $this->validateTicketSelectionForUser($event, $user, $request->tickets, true);

            $order = Order::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'total_amount' => $finalAmount,
                'discount_amount' => $discountAmount,
                'currency' => $currency,
                'status' => 'completed',
                'coupon_id' => $coupon?->id,
                ...$reservation,
            ]);

                $participantIdx = 0;
                foreach ($request->tickets as $entry) {
                    $tt = $ticketTypes[$entry['ticket_type_id']];
                    $qty = (int) $entry['quantity'];

                    $fresh = $tt->fresh();
                    $remaining = $fresh->quantity - $fresh->sold_count;
                    if ($qty > $remaining) {
                        throw new \Exception("Only {$remaining} '{$tt->name}' tickets remaining. Your order required {$qty}.");
                    }

                    $lineTotal = $tt->price * $qty;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'ticket_type_id' => $tt->id,
                        'quantity' => $qty,
                        'unit_price' => $tt->price,
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

                        $p = $request->input("participants.{$participantIdx}", []);
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
                'pdf_path' => 'invoices/simulated_' . $order->id . '.pdf',
                'issued_at' => now(),
            ]);

            $ticketNames = collect($request->tickets)->map(fn($t) => (TicketType::find($t['ticket_type_id'])?->name ?? '?') . ' x' . $t['quantity'])->implode(', ');

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
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        try {
            Mail::to($user->email)->send(new TicketPurchaseMail($order));
        } catch (\Exception $e) {}

        try {
            Notification::send(
                User::whereHas('organizations', fn($q) => $q->where('role', 'manager'))->get(),
                new TicketPurchasedNotification($order)
            );
        } catch (\Exception $e) {}

        return redirect()->route('tickets.index')->with('success', 'Ticket purchased successfully!');
    }

    /**
     * Place a reservation (pay later). Creates the order and records the intended
     * tickets, but issues no tickets and does not increment sold counts until a
     * manager records payment and marks the order paid.
     */
    public function reserve(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $request->validate([
            'tickets' => 'required|array|min:1',
            'tickets.*.ticket_type_id' => 'required|distinct|exists:ticket_types,id',
            'tickets.*.quantity' => 'required|integer|min:1|max:10',
            'coupon_code' => 'nullable|string|max:50',
            'participants' => 'nullable|array',
            'reservation_type' => 'nullable|in:individual,company',
            'company_name' => 'nullable|string|max:255',
            'company_details' => 'nullable|string|max:2000',
            'contact_person' => 'nullable|string|max:255',
            'attendees_later' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $ticketTypes = $this->validateTicketSelectionForUser($event, $user, $request->tickets);

        $totalAmount = 0;
        $currency = 'USD';

        foreach ($request->tickets as $entry) {
            $tt = $ticketTypes[$entry['ticket_type_id']];
            $qty = (int) $entry['quantity'];
            if (!isset($firstType)) { $firstType = $tt; $currency = $tt->currency; }

            $remaining = $tt->quantity - $tt->sold_count;
            if ($qty > $remaining) {
                return back()->withErrors(['quantity' => "Only {$remaining} '{$tt->name}' tickets available. You requested {$qty}."]);
            }

            if ($tt->max_per_user && $qty > $tt->max_per_user) {
                return back()->withErrors(['quantity' => "Maximum {$tt->max_per_user} '{$tt->name}' tickets per customer."]);
            }

            $totalAmount += $tt->price * $qty;
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
        $coupon = null;
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

            if (!$coupon) {
                return back()->withErrors(['coupon_code' => 'Coupon code not found.']);
            }

            $error = $coupon->validationError($event->id, $user->id);

            if ($error) {
                return back()->withErrors(['coupon_code' => $error]);
            }

            $discountAmount = $coupon->calculateDiscount($totalAmount);

            if ($discountAmount <= 0) {
                return back()->withErrors(['coupon_code' => 'Coupon does not apply to this order.']);
            }
        }

        $finalAmount = max(0, $totalAmount - $discountAmount);

        $order = null;

        DB::transaction(function () use ($event, $request, $user, $finalAmount, $discountAmount, $coupon, $currency, $reservation, &$order) {
            $ticketTypes = $this->validateTicketSelectionForUser($event, $user, $request->tickets, true);

            $order = Order::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'total_amount' => $finalAmount,
                'discount_amount' => $discountAmount,
                'currency' => $currency,
                'status' => 'reserved',
                'reserved_at' => now(),
                'coupon_id' => $coupon?->id,
                ...$reservation,
            ]);

            foreach ($request->tickets as $entry) {
                $tt = $ticketTypes[$entry['ticket_type_id']];
                $qty = (int) $entry['quantity'];

                $fresh = $tt->fresh();
                $remaining = $fresh->quantity - $fresh->sold_count;
                if ($qty > $remaining) {
                    throw new \Exception("Only {$remaining} '{$tt->name}' tickets remaining. Your reservation required {$qty}.");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_type_id' => $tt->id,
                    'quantity' => $qty,
                    'unit_price' => $tt->price,
                    'total_price' => $tt->price * $qty,
                ]);
            }

            ActivityLog::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'action' => 'reservation_placed',
                'model_type' => Order::class,
                'model_id' => $order->id,
                'metadata' => [
                    'attendees' => $reservation['attendee_count'],
                    'total' => $finalAmount,
                    'company' => $reservation['company_name'],
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('tickets.index')
            ->with('success', 'Reservation placed. We will contact you to arrange payment.');
    }

    /**
     * Validate a ticket selection against the event and the customer's existing tickets.
     *
     * The second check is intentionally repeated inside the transaction with row locks. This
     * prevents two simultaneous purchase requests from each passing validation and issuing
     * more tickets than a ticket type permits for one customer.
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

            if (!$ticketType) {
                throw ValidationException::withMessages([
                    'tickets' => 'One or more selected ticket types do not belong to this event.',
                ]);
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
