<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Event;
use App\Models\EventEnquiry;
use App\Models\Order;
use App\Models\ActivityLog;
use App\Models\TrainingRequest;

class CustomerController extends Controller
{
    public function tickets()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tickets = $user->tickets()
            ->with('event.venue', 'ticketType', 'attendee')
            ->latest()
            ->get();

        return view('customer.tickets', compact('tickets'));
    }

    public function enquiries()
    {
        $user = Auth::user();
        $enquiries = EventEnquiry::with('event', 'attachments')
            ->where('contact', $user->email)
            ->latest()
            ->get();
        return view('customer.enquiries', compact('enquiries'));
    }

    public function billing()
    {
        $orders = Order::with('event', 'items.ticketType', 'invoice')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
        return view('customer.billing', compact('orders'));
    }

    public function savedEvents()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $events = $user->savedEvents()->with('venue')->latest()->get();
        return view('customer.saved-events', compact('events'));
    }

    public function toggleSave(Request $request)
    {
        $data = $request->validate(['event_id' => 'required|exists:events,id']);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $exists = $user->savedEvents()->where('event_id', $data['event_id'])->exists();

        if ($exists) {
            $user->savedEvents()->detach($data['event_id']);
            return response()->json(['saved' => false]);
        } else {
            $user->savedEvents()->attach($data['event_id']);
            return response()->json(['saved' => true]);
        }
    }

    public function calendar()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $tickets = $user->tickets()
            ->with('event.venue', 'ticketType')
            ->whereHas('event', fn($q) => $q->where('start_date', '>=', now()->subMonth()))
            ->latest('created_at')
            ->get();

        $trainingRequests = TrainingRequest::with('event')
            ->where('user_id', Auth::id())
            ->whereIn('visibility', ['public', 'private'])
            ->where('preferred_date', '>=', now()->subMonth())
            ->latest('created_at')
            ->get();

        $items = collect();

        foreach ($tickets as $ticket) {
            $items->push((object) [
                'type' => 'ticket',
                'model' => $ticket,
                'date' => $ticket->event->start_date,
                'month_key' => $ticket->event->start_date->format('F Y'),
            ]);
        }

        foreach ($trainingRequests as $tr) {
            $items->push((object) [
                'type' => 'training_request',
                'model' => $tr,
                'date' => $tr->preferred_date,
                'month_key' => $tr->preferred_date->format('F Y'),
            ]);
        }

        $eventsByMonth = $items->sortBy('date')->groupBy('month_key');

        return view('customer.calendar', compact('eventsByMonth'));
    }

    public function notifications()
    {
        $logs = ActivityLog::with('event')
            ->where('user_id', Auth::id())
            ->latest('created_at')
            ->paginate(20);

        return view('customer.notifications', compact('logs'));
    }

    public function settings()
    {
        return view('customer.settings');
    }

    public function updateSettings(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $data['name'];
        $emailChanged = $data['email'] !== $user->email;

        if ($emailChanged) {
            $user->email = $data['email'];
            $user->email_verified_at = null;
        }

        if (!empty($data['new_password'])) {
            if (!Hash::check($data['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->password = Hash::make($data['new_password']);
        }

        $user->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
        }

        return back()->with('success', 'Account settings updated successfully.');
    }

    public function help()
    {
        return view('customer.help');
    }
}
