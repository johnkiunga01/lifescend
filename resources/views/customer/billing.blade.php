@extends('layouts.dashboard')

@section('pageTitle', 'Billing History')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Billing History</h1>
    <p class="text-sm text-slate-500 mt-1">View your past orders, payments, and invoices.</p>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    @if($orders->isNotEmpty())
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Order</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Event</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Amount</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($orders as $order)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="text-sm font-mono font-bold text-slate-900">#{{ $order->id }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm font-semibold text-slate-900">{{ $order->event->name ?? 'N/A' }}</p>
                        @if($order->items->isNotEmpty())
                        <p class="text-xs text-slate-500">{{ $order->items->sum('quantity') }} ticket(s)</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-900">
                        {{ number_format($order->total_amount, 2) }} {{ $order->currency }}
                        @if($order->discount_amount > 0)
                        <span class="text-xs text-emerald-600 font-semibold block">-{{ number_format($order->discount_amount, 2) }} discount</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = ['completed' => 'bg-emerald-100 text-emerald-700', 'pending' => 'bg-amber-100 text-amber-700', 'failed' => 'bg-red-100 text-red-700', 'refunded' => 'bg-slate-100 text-slate-600'];
                            $colorClass = $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $colorClass }}">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ $order->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="text-center py-16">
            <svg class="h-12 w-12 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
            <h3 class="text-lg font-semibold text-slate-500 mb-1">No orders yet</h3>
            <p class="text-sm text-slate-400">When you purchase tickets, your billing history will appear here.</p>
        </div>
    @endif
</div>
@endsection