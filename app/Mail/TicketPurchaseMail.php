<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketPurchaseMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
    ) {}

    public function envelope(): Envelope
    {
        $eventName = $this->order->event->name;
        return new Envelope(
            subject: "Tickets Confirmed - {$eventName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'mail.ticket-purchase',
        );
    }
}
