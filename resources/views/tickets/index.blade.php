@extends('layouts.dashboard')

@section('pageTitle', 'My Tickets')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">My Tickets</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $tickets->count() }} ticket{{ $tickets->count() !== 1 ? 's' : '' }}</p>
</div>

@if(session('success'))
<div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-3 text-sm text-emerald-700 shadow-sm">
    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

@if(isset($reservations) && $reservations->isNotEmpty())
<div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm">
    <div class="flex items-center gap-2 mb-3">
        <svg class="h-4 w-4 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <h2 class="text-sm font-bold text-blue-800">Pending reservations ({{ $reservations->count() }})</h2>
    </div>
    <div class="space-y-3">
        @foreach($reservations as $order)
        <div class="rounded-xl bg-white border border-blue-100 p-4">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ $order->event->name ?? 'Event' }}</p>
                    <p class="text-xs text-slate-500 mt-1">{{ $order->items->sum('quantity') }} place(s) · {{ number_format($order->total_amount, 2) }} {{ $order->currency }}</p>
                    @if($order->payment_method)
                    <p class="text-xs text-slate-500 mt-1">Payment method: <span class="font-semibold">{{ $order->payment_method }}</span></p>
                    @endif
                </div>
                <span class="shrink-0 inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-bold text-blue-700">{{ ucfirst($order->status) }}</span>
            </div>
            <p class="text-xs text-blue-700 mt-2">Your place is held. Tickets appear here once payment is recorded.</p>
        </div>
        @endforeach
    </div>
</div>
@endif

@if($tickets->isEmpty())
<div class="rounded-2xl border border-slate-200 bg-white p-12 shadow-sm text-center">
    <svg class="h-16 w-16 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
    <h2 class="text-lg font-bold text-slate-900 mb-2">No tickets yet</h2>
    <p class="text-sm text-slate-500 mb-6">Browse events and grab your first ticket today.</p>
    <a href="{{ route('events') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:from-gold-600 hover:to-gold-700 transition-all">Browse Events</a>
</div>
@else
<div class="space-y-4">
    @foreach($tickets as $ticket)
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4 min-w-0 flex-1">
                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-bold text-lg shrink-0">
                    {{ strtoupper(substr($ticket->event->name, 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-bold text-slate-900 text-sm truncate">{{ $ticket->event->name }}</h3>
                        @php
                            $statusColors = ['active' => 'bg-emerald-100 text-emerald-700', 'used' => 'bg-amber-100 text-amber-700', 'cancelled' => 'bg-red-100 text-red-600'];
                            $statusClass = $statusColors[$ticket->status] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $statusClass }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $ticket->status === 'active' ? 'bg-emerald-500' : ($ticket->status === 'used' ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                        <span class="flex items-center gap-1">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $ticket->event->start_date->format('M d, Y') }}
                        </span>
                        <span>{{ $ticket->ticketType?->name ?? 'Standard' }}</span>
                        @if($ticket->event->venue)
                        <span class="flex items-center gap-1">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            {{ $ticket->event->venue->name }}
                        </span>
                        @endif
                    </p>
                </div>
            </div>
            <a href="{{ ticket_url($ticket) }}"
                class="shrink-0 inline-flex items-center gap-1.5 rounded-xl bg-gold-50 border border-gold-200 px-4 py-2 text-xs font-bold text-gold-700 hover:bg-gold-100 transition-all whitespace-nowrap">
                View Ticket
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection