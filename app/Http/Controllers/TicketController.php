<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Event;
use App\Models\Order;
use App\Models\PaymentType;
use App\Models\TicketType;
use App\Models\User;
use App\Services\OrderFulfillmentService;
use App\Services\TicketHasher;
use App\Mail\TicketPurchaseMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('event', 'ticketType', 'event.venue')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $reservations = Order::with('event', 'items.ticketType')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['reserved', 'pending'])
            ->whereDoesntHave('tickets')
            ->latest()
            ->get();

        return view('tickets.index', compact('tickets', 'reservations'));
    }

    public function manageIndex(Request $request)
    {
        $query = Ticket::with(['event', 'ticketType', 'user', 'attendee', 'order']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('qr_code', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('attendee', fn($a) => $a->where('participant_name', 'like', "%{$search}%")->orWhere('participant_email', 'like', "%{$search}%"))
                  ->orWhereHas('event', fn($e) => $e->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($eventId = $request->query('event_id')) {
            $query->where('event_id', $eventId);
        }

        if ($typeId = $request->query('ticket_type_id')) {
            $query->where('ticket_type_id', $typeId);
        }

        if ($request->query('checked_in') === 'yes') {
            $query->whereNotNull('checked_in_at');
        } elseif ($request->query('checked_in') === 'no') {
            $query->whereNull('checked_in_at');
        }

        $sort = $request->query('sort', 'newest');
        match ($sort) {
            'oldest' => $query->oldest(),
            'event' => $query->orderBy(
                Event::select('name')->whereColumn('events.id', 'tickets.event_id'),
            ),
            'user' => $query->orderBy(
                \App\Models\User::select('name')->whereColumn('users.id', 'tickets.user_id'),
            ),
            default => $query->latest(),
        };

        $tickets = $query->paginate(25)->appends($request->query());
        $events = Event::orderBy('name', 'asc')->get();

        $view = $request->query('view') === 'reserved' ? 'reserved' : 'tickets';

        $reservedOrders = Order::with(['user', 'event.ticketTypes', 'items.ticketType'])
            ->whereIn('status', ['reserved', 'pending'])
            ->whereDoesntHave('tickets')
            ->latest()
            ->get();

        $paymentTypes = PaymentType::orderBy('name')->get();

        return view('manage.tickets.index', compact('tickets', 'events', 'view', 'reservedOrders', 'paymentTypes'));
    }

    public function completeReservation(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_method' => 'required|string|max:255',
            'tickets_paid' => 'required|integer|min:1|max:1000',
            'amount_paid' => 'required|numeric|min:0',
            'new_tickets' => 'nullable|array',
            'new_tickets.*' => 'nullable|integer|min:0|max:1000',
        ]);

        $method = $data['payment_method'];
        $ticketsPaid = (int) $data['tickets_paid'];
        $amountPaid = round((float) $data['amount_paid'], 2);

        if ($order->status === 'completed' && $order->tickets()->exists()) {
            return back()->with('success', "Order #{$order->id} is already completed.");
        }

        $order->update([
            'payment_method' => $method,
            'reserved_at' => $order->reserved_at ?? now(),
            'paid_amount' => $amountPaid,
            'total_amount' => $amountPaid,
        ]);

        // Decide how the paid tickets are split across ticket types.
        $existingItems = $order->items()->get();

        if ($existingItems->isNotEmpty()) {
            $weights = [];
            foreach ($existingItems as $item) {
                $weights[$item->ticket_type_id] = max(1, (int) $item->quantity);
            }
        } else {
            $weights = [];
            foreach ($request->input('new_tickets', []) as $ticketTypeId => $qty) {
                $qty = (int) $qty;
                if ($qty > 0) {
                    $weights[(int) $ticketTypeId] = $qty;
                }
            }

            if (empty($weights) && $order->event && $order->event->ticketTypes->count() === 1) {
                $weights = [$order->event->ticketTypes->first()->id => 1];
            }

            if (empty($weights)) {
                return back()->withErrors(['new_tickets' => 'This reservation has no ticket line items. Select at least one ticket type to issue the paid tickets.']);
            }
        }

        // Rebuild order items so the ticket count equals the number of tickets paid.
        $order->items()->delete();
        foreach ($this->distributeTickets($weights, $ticketsPaid) as $ticketTypeId => $qty) {
            if ($qty < 1) {
                continue;
            }

            $ticketType = TicketType::find($ticketTypeId);
            if (! $ticketType) {
                continue;
            }

            $order->items()->create([
                'ticket_type_id' => $ticketType->id,
                'quantity' => $qty,
                'unit_price' => $ticketType->price,
                'total_price' => $ticketType->price * $qty,
            ]);
        }

        try {
            app(OrderFulfillmentService::class)->fulfill($order);
        } catch (\Throwable $e) {
            return back()->withErrors(['order' => 'Could not complete reservation: ' . $e->getMessage()]);
        }

        $order->refresh()->load('invoice');

        try {
            if ($order->user?->email) {
                Mail::to($order->user->email)->send(new TicketPurchaseMail($order));
            }
        } catch (\Throwable $e) {}

        $orderNumber = $order->invoice?->invoice_number ?? 'ORD-' . $order->id;

        return back()->with('success', "Reservation completed. {$ticketsPaid} ticket(s) issued under order {$orderNumber} and the customer has been emailed.");
    }

    /**
     * Split a total ticket count across ticket types proportionally to the given weights.
     *
     * @param  array<int, int>  $weights  ticket_type_id => weight
     * @return array<int, int>  ticket_type_id => quantity
     */
    private function distributeTickets(array $weights, int $total): array
    {
        $weights = array_filter($weights, fn ($w) => $w > 0);
        $sum = array_sum($weights);

        if ($sum <= 0 || $total < 1) {
            return [];
        }

        $result = [];
        $allocated = 0;
        $lastKey = array_key_last($weights);

        foreach ($weights as $id => $weight) {
            if ($id === $lastKey) {
                $qty = $total - $allocated;
            } else {
                $qty = (int) floor($total * $weight / $sum);
            }

            $result[$id] = max(0, $qty);
            $allocated += $result[$id];
        }

        return $result;
    }

    public function manageDestroy(Ticket $ticket)
    {
        $ticket->update(['status' => 'cancelled']);

        return back()->with('success', 'Ticket cancelled successfully.');
    }

    public function manageReactivate(Ticket $ticket)
    {
        if ($ticket->status === 'cancelled') {
            $ticket->update(['status' => 'active']);
        }

        return back()->with('success', 'Ticket reactivated successfully.');
    }

    public function updateAttendee(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === Auth::id(), 403);
        abort_if($ticket->status === 'cancelled' || $ticket->checked_in_at, 422, 'Attendee details can no longer be changed for this ticket.');

        $data = $request->validate([
            'participant_name' => 'required|string|max:255',
            'participant_email' => 'required|email|max:255',
        ]);

        $ticket->attendee()->updateOrCreate(
            ['ticket_id' => $ticket->id],
            [
                'event_id' => $ticket->event_id,
                'user_id' => $ticket->user_id,
                'participant_name' => $data['participant_name'],
                'participant_email' => $data['participant_email'],
                'status' => 'registered',
            ],
        );

        return back()->with('success', 'Attendee details saved.');
    }

    public function show(string $event, string $hash, TicketHasher $hasher)
    {
        $id = $hasher->decode($hash);

        if (!$id) {
            abort(404);
        }

        $ticket = Ticket::with([
            'event.venue',
            'ticketType',
            'order',
            'user',
            'attendee',
            'checkIns' => fn($q) => $q->latest(),
        ])->findOrFail($id);

        if ($ticket->event->slug !== $event) {
            abort(404);
        }

        if ($ticket->user_id !== Auth::id()) {
            $user = Auth::user();
            if (!$user || !($user instanceof User)) {
                abort(403);
            }
            $role = $user->organizations()->first()?->pivot?->role ?? 'customer';
            if (!in_array($role, ['admin', 'manager', 'staff'])) {
                abort(403);
            }
        }

        return view('tickets.show', compact('ticket'));
    }
}
