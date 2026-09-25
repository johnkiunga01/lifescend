<?php

use App\Models\Ticket;
use App\Services\TicketHasher;

if (!function_exists('ticket_url')) {
    function ticket_url(Ticket $ticket): string
    {
        $hash = app(TicketHasher::class)->encode($ticket->id);
        return route('tickets.show', ['event' => $ticket->event->slug, 'hash' => $hash]);
    }
}
