<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Outfit, Arial, sans-serif; background: #f8fafc; padding: 32px;">
    <div style="max-width: 560px; margin: 0 auto; background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <div style="text-align: center; margin-bottom: 24px;">
            <span style="display: inline-block; background: #D4AF37; color: white; width: 40px; height: 40px; border-radius: 10px; line-height: 40px; font-size: 20px; font-weight: bold;">L</span>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <div style="display: inline-block; background: #d1fae5; width: 56px; height: 56px; border-radius: 50%; line-height: 56px; font-size: 28px;">&#10003;</div>
        </div>

        <h1 style="font-size: 20px; color: #0f172a; margin: 0 0 8px; text-align: center;">Tickets Confirmed!</h1>
        <p style="color: #64748b; font-size: 14px; margin: 0 0 20px; text-align: center;">Dear <strong>{{ $order->user->name }}</strong>,</p>

        <p style="color: #334155; font-size: 14px; line-height: 1.6; margin: 0 0 16px;">
            Your tickets for <strong>{{ $order->event->name }}</strong> have been confirmed.
        </p>

        <div style="background: #f1f5f9; border-radius: 12px; padding: 20px; margin: 0 0 20px;">
            <p style="color: #475569; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 12px;">Order Summary</p>
            <table style="width: 100%; font-size: 13px; color: #334155;">
                @foreach($order->items as $item)
                <tr>
                    <td style="padding: 6px 0; color: #64748b;">{{ $item->ticketType->name ?? 'Ticket' }} x{{ $item->quantity }}</td>
                    <td style="padding: 6px 0; font-weight: 600; text-align: right;">${{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
                @if($order->discount_amount > 0)
                <tr>
                    <td style="padding: 6px 0; color: #64748b;">Discount</td>
                    <td style="padding: 6px 0; font-weight: 600; text-align: right; color: #059669;">-${{ number_format($order->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 0 0; border-top: 1px solid #e2e8f0; font-weight: 600;">Total</td>
                    <td style="padding: 8px 0 0; border-top: 1px solid #e2e8f0; font-weight: 700; text-align: right; font-size: 16px;">${{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <div style="background: #fef3c7; border-radius: 12px; padding: 20px; margin: 0 0 20px; border-left: 4px solid #D4AF37;">
            <p style="color: #92400e; font-size: 13px; line-height: 1.6; margin: 0;">
                <strong>Event:</strong> {{ $order->event->name }}<br>
                <strong>Date:</strong> {{ $order->event->start_date->format('F d, Y') }}@if($order->event->end_date && $order->event->start_date->format('Y-m-d') !== $order->event->end_date->format('Y-m-d')) - {{ $order->event->end_date->format('F d, Y') }}@endif<br>
                <strong>Location:</strong> {{ $order->event->venue->name ?? 'Online' }}<br>
                <strong>Order #:</strong> {{ $order->invoice->invoice_number ?? $order->id }}
            </p>
        </div>

        <p style="color: #64748b; font-size: 13px; margin: 0 0 12px;">You can view your tickets anytime from your dashboard.</p>

        <div style="text-align: center; margin: 24px 0;">
            <a href="{{ route('tickets.index') }}"
               style="display: inline-block; background: #D4AF37; color: white; padding: 12px 28px; border-radius: 12px; text-decoration: none; font-size: 14px; font-weight: 700;">
                View My Tickets
            </a>
        </div>

        <p style="color: #64748b; font-size: 13px; margin: 0 0 4px;">Best regards,</p>
        <p style="color: #0f172a; font-size: 14px; font-weight: 600; margin: 0;">Lifescend Team</p>

        <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 24px 0 16px;">
        <p style="color: #94a3b8; font-size: 11px; margin: 0; text-align: center;">This email confirms your ticket purchase. Keep this for your records.</p>
    </div>
</body>
</html>
