<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketPurchasedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->order->event;
        $user = $this->order->user;

        return (new MailMessage)
            ->subject("New Ticket Purchase - {$event->name}")
            ->line("**{$user->name}** ({$user->email}) purchased tickets for *{$event->name}*.")
            ->line("Order total: **\$" . number_format($this->order->total_amount, 2) . '**')
            ->action('View Order', route('manage.events.show', $event->slug));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'event_id' => $this->order->event_id,
            'event_name' => $this->order->event->name,
            'user_name' => $this->order->user->name,
            'user_email' => $this->order->user->email,
            'total_amount' => $this->order->total_amount,
            'ticket_count' => $this->order->items->sum('quantity'),
        ];
    }
}
