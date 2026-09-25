<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\CheckIn;
use App\Models\ActivityLog;
use App\Models\TrainingRequest;
use App\Models\Venue;
use App\Models\EventType;
use App\Models\Sponsor;
use App\Models\Speaker;
use App\Models\Attendee;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userRole = $user->organizations()->first()?->pivot?->role ?? 'customer';

        view()->share('userRole', $userRole);

        return match ($userRole) {
            'admin'   => $this->adminDashboard(),
            'manager' => $this->managerDashboard(),
            'staff'   => $this->staffDashboard(),
            default   => $this->customerDashboard(),
        };
    }

    protected function adminDashboard()
    {
        return $this->staffDashboardData('dashboard.admin');
    }

    protected function managerDashboard()
    {
        return $this->staffDashboardData('dashboard.manager');
    }

    protected function staffDashboard()
    {
        return $this->staffDashboardData('dashboard.staff');
    }

    protected function staffDashboardData(string $view)
    {
        $totalEvents = Event::count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $totalTicketsSold = Ticket::count();
        $totalCheckedIn = CheckIn::count();

        $events = Event::with('venue')->latest()->get();
        $recentOrders = Order::with('user', 'event')->latest()->take(5)->get();
        $recentLogs = ActivityLog::with('user', 'event')->latest()->take(10)->get();
        $trainingRequests = TrainingRequest::with('user', 'event')->latest()->get();
        $eventTypes = EventType::orderBy('name')->get();
        $venues = Venue::orderBy('name')->get();
        $sponsors = Sponsor::orderBy('name')->get();
        $speakers = Speaker::orderBy('name')->get();

        $todayEvents = Event::with('venue')
            ->whereDate('start_date', today())
            ->orWhereDate('end_date', today())
            ->orWhere(function ($q) {
                $q->whereDate('start_date', '<=', today())
                  ->whereDate('end_date', '>=', today());
            })
            ->get()
            ->map(function ($event) {
                $event->ticketCount = Ticket::where('event_id', $event->id)->count();
                $event->checkedInCount = CheckIn::where('event_id', $event->id)->count();
                $event->checkInPercent = $event->ticketCount > 0
                    ? round(($event->checkedInCount / $event->ticketCount) * 100)
                    : 0;
                return $event;
            });

        return view($view, compact(
            'totalEvents', 'totalRevenue', 'totalTicketsSold', 'totalCheckedIn',
            'events', 'recentOrders', 'recentLogs', 'trainingRequests',
            'eventTypes', 'venues', 'sponsors', 'speakers', 'todayEvents'
        ));
    }

    protected function customerDashboard()
    {
        $user = Auth::user();

        $tickets = Ticket::with('event', 'ticketType', 'event.venue')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $trainingRequests = TrainingRequest::with('event')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $privateTrainings = TrainingRequest::with('event.venue')
            ->where('user_id', $user->id)
            ->where('visibility', 'private')
            ->whereNotNull('event_id')
            ->latest()
            ->get();

        $events = Event::where('status', 'published')->get();
        $eventTypes = EventType::orderBy('name')->get();
        $venues = Venue::orderBy('name')->get();
        $sponsors = Sponsor::orderBy('name')->get();
        $speakers = Speaker::orderBy('name')->get();

        return view('dashboard.customer', compact(
            'tickets', 'trainingRequests', 'privateTrainings',
            'events', 'eventTypes', 'venues', 'sponsors', 'speakers'
        ));
    }

    public function attendeeLookup(Request $request)
    {
        $query = $request->get('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $tickets = Ticket::with(['user', 'event.venue', 'ticketType'])
            ->where(function ($q) use ($query) {
                $q->whereHas('user', function ($u) use ($query) {
                    $u->where('name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
                })
                ->orWhere('qr_code', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'name' => $ticket->user?->name ?? 'N/A',
                    'email' => $ticket->user?->email ?? 'N/A',
                    'event' => $ticket->event?->name ?? 'N/A',
                    'event_date' => $ticket->event?->start_date?->format('M d, Y') ?? 'N/A',
                    'ticket_type' => $ticket->ticketType?->name ?? 'N/A',
                    'status' => $ticket->status,
                    'checked_in' => !is_null($ticket->checked_in_at),
                    'checked_in_at' => $ticket->checked_in_at?->format('M d, Y H:i') ?? null,
                ];
            });

        return response()->json($tickets);
    }
}
