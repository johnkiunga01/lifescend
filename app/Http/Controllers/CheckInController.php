<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\CheckIn;
use App\Models\Attendee;
use App\Models\ActivityLog;

class CheckInController extends Controller
{
    public function index()
    {
        $step = session('checkin.step', 1);
        $eventId = session('checkin.event_id');
        $ticketId = session('checkin.ticket_id');

        $events = null;
        $event = null;
        $ticket = null;
        $success = session('checkin.success');

        if ($step === 1) {
            $events = Event::where('status', 'published')
                ->orderBy('start_date', 'desc')
                ->get();
        }

        if ($step >= 2 && $eventId) {
            $event = Event::find($eventId);
            if (!$event) {
                session(['checkin.step' => 1, 'checkin.event_id' => null, 'checkin.ticket_id' => null]);
                $step = 1;
            }
        }

        if ($step === 3 && $ticketId) {
            $ticket = Ticket::with(['user', 'ticketType', 'event'])->find($ticketId);
            if (!$ticket) {
                session(['checkin.step' => 1, 'checkin.event_id' => null, 'checkin.ticket_id' => null]);
                $step = 1;
            }
        }

        $recentCheckIns = CheckIn::with(['ticket', 'user', 'event', 'checkedInBy'])
            ->latest()
            ->take(20)
            ->get();

        return view('checkin.index', compact(
            'step', 'events', 'event', 'ticket', 'recentCheckIns', 'success',
        ));
    }

    public function selectEvent(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $event = Event::findOrFail($data['event_id']);

        session([
            'checkin.step' => 2,
            'checkin.event_id' => $event->id,
            'checkin.ticket_id' => null,
            'checkin.success' => null,
        ]);

        return redirect()->route('checkin.index');
    }

    public function verify(Request $request)
    {
        $eventId = session('checkin.event_id');

        if (!$eventId) {
            return redirect()->route('checkin.index');
        }

        $data = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $ticket = Ticket::with(['user', 'ticketType', 'event'])
            ->where('qr_code', $data['qr_code'])
            ->where('event_id', $eventId)
            ->first();

        if (!$ticket) {
            return redirect()->route('checkin.index')
                ->with('checkin.error', 'Ticket code not found for this event.');
        }

        if ($ticket->status === 'used' || $ticket->checked_in_at) {
            return redirect()->route('checkin.index')
                ->with('checkin.error', 'This ticket has already been used for check-in.');
        }

        if ($ticket->status === 'cancelled') {
            return redirect()->route('checkin.index')
                ->with('checkin.error', 'This ticket has been cancelled.');
        }

        session([
            'checkin.step' => 3,
            'checkin.ticket_id' => $ticket->id,
        ]);

        return redirect()->route('checkin.index');
    }

    public function confirm(Request $request)
    {
        $eventId = session('checkin.event_id');
        $ticketId = session('checkin.ticket_id');

        if (!$eventId || !$ticketId) {
            return redirect()->route('checkin.index');
        }

        $ticket = Ticket::with('user')->findOrFail($ticketId);

        if ($ticket->status === 'used' || $ticket->checked_in_at) {
            return redirect()->route('checkin.index')
                ->with('checkin.error', 'This ticket has already been used for check-in.');
        }

        if ($ticket->status === 'cancelled') {
            return redirect()->route('checkin.index')
                ->with('checkin.error', 'This ticket has been cancelled.');
        }

        $ticket->update([
            'checked_in_at' => now(),
            'status' => 'used',
        ]);

        Attendee::where('ticket_id', $ticket->id)->update([
            'status' => 'checked-in',
        ]);

        $checkIn = CheckIn::create([
            'event_id' => $ticket->event_id,
            'ticket_id' => $ticket->id,
            'user_id' => $ticket->user_id,
            'checked_in_by' => Auth::id(),
            'device_info' => [
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ],
            'checked_in_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'event_id' => $ticket->event_id,
            'action' => 'attendee_checked_in',
            'model_type' => CheckIn::class,
            'model_id' => $checkIn->id,
            'metadata' => [
                'ticket_code' => $ticket->qr_code,
                'user_name' => $ticket->user->name,
            ],
            'ip_address' => $request->ip(),
        ]);

        session([
            'checkin.step' => 4,
            'checkin.success' => "{$ticket->user->name} has been checked in successfully!",
        ]);

        return redirect()->route('checkin.index');
    }

    public function reset()
    {
        session([
            'checkin.step' => 1,
            'checkin.event_id' => null,
            'checkin.ticket_id' => null,
            'checkin.success' => null,
        ]);

        return redirect()->route('checkin.index');
    }
}
