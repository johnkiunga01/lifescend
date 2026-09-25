@extends('layouts.dashboard')

@section('pageTitle', 'My Tickets')

@section('content')
<div x-data="{
    filter: 'all',
    tickets: {{ $tickets->map(fn($t) => [
        'id'          => $t->id,
        'upcoming'    => $t->event && $t->event->start_date >= now(),
    ])->values()->toJson() }}
}">

{{-- Header --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">My Tickets</h1>
        <p class="text-sm text-slate-500 mt-1">All your event passes in one place.</p>
    </div>

    {{-- Stats --}}
    <div class="flex items-center gap-3">
        @php
            $upcoming = $tickets->filter(fn($t) => $t->event && $t->event->start_date >= now())->count();
            $past     = $tickets->filter(fn($t) => $t->event && $t->event->start_date < now())->count();
        @endphp
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center shadow-sm">
            <p class="text-xl font-extrabold text-slate-900">{{ $tickets->count() }}</p>
            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400 mt-0.5">Total</p>
        </div>
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-center shadow-sm">
            <p class="text-xl font-extrabold text-emerald-700">{{ $upcoming }}</p>
            <p class="text-[10px] font-semibold uppercase tracking-wide text-emerald-500 mt-0.5">Upcoming</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-center shadow-sm">
            <p class="text-xl font-extrabold text-slate-500">{{ $past }}</p>
            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400 mt-0.5">Past</p>
        </div>
    </div>
</div>

{{-- Filter tabs --}}
<div class="mb-5 flex items-center gap-2">
    <button @click="filter = 'all'"
        :class="filter === 'all' ? 'bg-slate-900 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:border-slate-300'"
        class="rounded-lg px-4 py-2 text-sm font-semibold transition-all">
        All <span class="ml-1 opacity-60">{{ $tickets->count() }}</span>
    </button>
    <button @click="filter = 'upcoming'"
        :class="filter === 'upcoming' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:border-slate-300'"
        class="rounded-lg px-4 py-2 text-sm font-semibold transition-all">
        Upcoming <span class="ml-1 opacity-70">{{ $upcoming }}</span>
    </button>
    <button @click="filter = 'past'"
        :class="filter === 'past' ? 'bg-slate-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:border-slate-300'"
        class="rounded-lg px-4 py-2 text-sm font-semibold transition-all">
        Past <span class="ml-1 opacity-70">{{ $past }}</span>
    </button>
</div>

{{-- Ticket list --}}
@if($tickets->isEmpty())
<div class="rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center">
    <svg class="h-12 w-12 mx-auto mb-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
    </svg>
    <p class="text-slate-500 font-semibold">No tickets yet</p>
    <p class="text-slate-400 text-sm mt-1">Browse events and grab your first ticket.</p>
    <a href="{{ route('events.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white hover:bg-gold-600 transition-colors">
        Browse Events
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
</div>
@else
<div class="space-y-4">
    @foreach($tickets as $ticket)
    @php
        $isUpcoming = $ticket->event && $ticket->event->start_date >= now();
        $statusMap  = ['active' => ['bg-emerald-100 text-emerald-700', 'Active'], 'used' => ['bg-slate-100 text-slate-600', 'Used'], 'cancelled' => ['bg-red-100 text-red-600', 'Cancelled']];
        [$statusClass, $statusLabel] = $statusMap[$ticket->status] ?? ['bg-slate-100 text-slate-600', ucfirst($ticket->status)];
        $attendeeName = $ticket->attendee?->participant_name ?? auth()->user()->name;
    @endphp
    <div x-show="filter === 'all' || (filter === 'upcoming' && {{ $isUpcoming ? 'true' : 'false' }}) || (filter === 'past' && {{ !$isUpcoming ? 'true' : 'false' }})"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0">

        <div class="rounded-2xl border bg-white shadow-sm overflow-hidden transition-all hover:shadow-md
                    {{ $isUpcoming ? 'border-slate-200' : 'border-slate-200 opacity-80' }}">
            <div class="flex flex-col sm:flex-row">

                {{-- Left: event colour strip + logo --}}
                <div class="relative flex items-center justify-center sm:w-36 bg-gradient-to-br from-slate-800 to-slate-900 shrink-0 p-5">
                    @if($ticket->event?->logo)
                        <img src="{{ $ticket->event->logo_url }}" alt="{{ $ticket->event->name }}"
                             class="h-16 w-16 rounded-xl object-cover shadow-lg">
                    @else
                        <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-extrabold text-2xl shadow-lg">
                            {{ strtoupper(substr($ticket->event?->name ?? 'E', 0, 1)) }}
                        </div>
                    @endif
                    @if($isUpcoming)
                    <span class="absolute top-2 right-2 rounded-full bg-emerald-500 h-2.5 w-2.5 shadow-sm shadow-emerald-400"></span>
                    @endif
                </div>

                {{-- Middle: event info --}}
                <div class="flex-1 p-5 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        {{-- Package badge --}}
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-gold-50 border border-gold-200 px-2.5 py-0.5 text-xs font-semibold text-gold-700">
                            <svg class="h-3 w-3 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            {{ $ticket->ticketType?->name ?? 'General' }}
                        </span>
                        {{-- Status badge --}}
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusClass }}">
                            {{ $statusLabel }}
                        </span>
                        @if($isUpcoming)
                        <span class="inline-flex items-center rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Upcoming</span>
                        @endif
                    </div>

                    <h3 class="text-base font-bold text-slate-900 truncate">{{ $ticket->event?->name ?? 'Event' }}</h3>

                    <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $ticket->event?->start_date->format('D, M d, Y · g:i A') ?? '—' }}
                        </span>
                        @if($ticket->event?->venue)
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $ticket->event->venue->name }}
                        </span>
                        @endif
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $attendeeName }}
                        </span>
                    </div>
                </div>

                {{-- Right: QR + action --}}
                <div class="flex sm:flex-col items-center justify-between sm:justify-center gap-4 px-5 py-4 sm:py-5 border-t sm:border-t-0 sm:border-l border-slate-100 bg-slate-50/60 sm:w-40 shrink-0">
                    {{-- QR block --}}
                    <div class="flex flex-col items-center">
                        <div class="bg-white border border-slate-200 rounded-xl p-2 shadow-sm h-20 w-20 flex items-center justify-center">
                            <img src="{{ (new \chillerlan\QRCode\QRCode)->render($ticket->qr_code) }}"
                                 alt="QR Code" class="h-full w-full">
                        </div>
                        <p class="mt-1.5 text-[10px] font-mono font-bold text-slate-500 tracking-wide truncate max-w-[8rem]">{{ $ticket->qr_code }}</p>
                    </div>
                    {{-- View button --}}
                    <a href="{{ ticket_url($ticket) }}"
                       class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-gold-600 transition-colors whitespace-nowrap">
                        View Ticket
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Empty filter state --}}
<div x-show="filter !== 'all' && tickets.filter(t => filter === 'upcoming' ? t.upcoming : !t.upcoming).length === 0"
     class="mt-4 rounded-2xl border border-dashed border-slate-200 py-12 text-center text-slate-400 text-sm">
    No tickets in this category.
</div>
@endif

</div>
@endsection
