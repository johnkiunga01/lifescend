<?php

namespace App\Http\Controllers;

use App\Models\Event;

class CheckoutController extends Controller
{
    public function show(string $slug)
    {
        $event = Event::with(['ticketTypes' => function ($q) {
            $q->where('is_active', true)->orderBy('price');
        }, 'venue'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        if ($event->ticketTypes->isEmpty()) {
            return redirect()->route('events.show', $slug)
                ->with('error', 'No tickets are currently available for this event.');
        }

        $stripeKey = config('services.stripe.key');

        $intent = request()->query('intent') === 'reserve' ? 'reserve' : null;

        return view('checkout.show', compact('event', 'stripeKey', 'intent'));
    }
}
