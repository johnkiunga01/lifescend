<?php

namespace App\Services;

use App\Models\Attendee;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderFulfillmentService
{
    /**
     * Issue tickets, attendees, invoice and coupon usage for a paid/reserved order.
     *
     * Safe to call more than once: if tickets already exist for the order it does nothing.
     */
    public function fulfill(Order $order): Order
    {
        DB::transaction(function () use ($order) {
            $order = Order::with('items', 'event')->lockForUpdate()->findOrFail($order->id);

            if ($order->tickets()->exists()) {
                return;
            }

            foreach ($order->items as $item) {
                $ticketType = TicketType::lockForUpdate()->find($item->ticket_type_id);
                $qty = (int) $item->quantity;

                if (! $ticketType) {
                    throw new \RuntimeException('A ticket type in this order no longer exists.');
                }

                $remaining = $ticketType->quantity - $ticketType->sold_count;
                if ($qty > $remaining) {
                    throw new \RuntimeException("Only {$remaining} '{$ticketType->name}' ticket(s) remaining.");
                }

                for ($i = 0; $i < $qty; $i++) {
                    $ticket = Ticket::create([
                        'event_id' => $order->event_id,
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'ticket_type_id' => $ticketType->id,
                        'qr_code' => 'TKT-' . strtoupper($order->event->slug ?? 'EVT') . '-' . strtoupper(Str::random(6)),
                        'status' => 'active',
                    ]);

                    Attendee::create([
                        'event_id' => $order->event_id,
                        'user_id' => $order->user_id,
                        'ticket_id' => $ticket->id,
                        'participant_name' => null,
                        'participant_email' => null,
                        'status' => 'registered',
                    ]);
                }

                $ticketType->increment('sold_count', $qty);
            }

            if (! $order->invoice) {
                Invoice::create([
                    'order_id' => $order->id,
                    'invoice_number' => 'INV-' . date('Y') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                    'pdf_path' => 'invoices/simulated_' . $order->id . '.pdf',
                    'issued_at' => now(),
                ]);
            }

            if ($order->coupon_id && (float) $order->discount_amount > 0) {
                $coupon = Coupon::find($order->coupon_id);

                if ($coupon && ! CouponUsage::where('order_id', $order->id)->exists()) {
                    $coupon->increment('used_count');
                    CouponUsage::create([
                        'coupon_id' => $coupon->id,
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'discount_amount' => $order->discount_amount,
                    ]);
                }
            }

            $order->update(['status' => 'completed', 'paid_at' => now()]);
        });

        return $order->fresh(['items', 'tickets']);
    }
}
