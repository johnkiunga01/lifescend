@extends('layouts.dashboard')

@section('pageTitle', 'Staff Dashboard')

@section('content')
<div x-data="{ lookupQuery: '', lookupResults: [], lookupLoading: false, lookupDone: false }">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900">Staff Overview</h1>
        <p class="text-sm text-slate-500 mt-1">Event operations and attendee management.</p>
    </div>

    {{-- Metrics --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Events</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalEvents }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tickets Sold</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalTicketsSold }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Checked In</p>
            <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $totalCheckedIn }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 mb-8">
        {{-- Quick Attendee Lookup --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Quick Attendee Lookup</h2>
            <p class="text-sm text-slate-500 mb-4">Search by name, email, or ticket reference.</p>
            <div class="relative">
                <input type="text" x-model="lookupQuery"
                    @@input.debounce.300ms="if (lookupQuery.length >= 2) { lookupLoading = true; lookupDone = false; fetch('{{ route('attendee.lookup') }}?q=' + encodeURIComponent(lookupQuery)).then(r => r.json()).then(d => { lookupResults = d; lookupLoading = false; lookupDone = true; }); } else { lookupResults = []; lookupDone = false; }"
                    placeholder="e.g. John, john@email.com, TKT-..."
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                <div x-show="lookupLoading" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <svg class="h-5 w-5 animate-spin text-gold-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </div>
            </div>

            <div x-show="lookupDone && lookupResults.length === 0" class="mt-4 text-sm text-slate-500 text-center py-4 border border-dashed border-slate-200 rounded-xl">
                No attendees found matching your search.
            </div>

            <template x-for="r in lookupResults" :key="r.id">
                <div class="mt-3 rounded-xl border border-slate-200 p-4 hover:border-gold-300 transition-colors">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <p class="text-sm font-bold text-slate-900" x-text="r.name"></p>
                            <p class="text-xs text-slate-500" x-text="r.email"></p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                              x-bind:class="r.checked_in ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'"
                              x-text="r.checked_in ? 'Checked In' : 'Not Checked In'"></span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-500">
                        <span x-text="r.event"></span>
                        <span x-text="r.ticket_type"></span>
                    </div>
                    <p x-show="r.checked_in_at" class="mt-1 text-[10px] text-slate-400" x-text="'Checked in at ' + r.checked_in_at"></p>
                </div>
            </template>
        </div>

        {{-- Quick Check-In --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Quick Check-In</h2>
            <p class="text-sm text-slate-500 mb-4">Validate attendee tickets at the gate.</p>
            <a href="{{ route('checkin.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Open Check-In Portal
            </a>
        </div>
    </div>

    {{-- Today's Events with Live Check-In --}}
    <div class="mb-8">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Today's Events</h2>
        @if($todayEvents->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-8 text-center">
                <svg class="h-10 w-10 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <p class="text-sm text-slate-500">No events happening today.</p>
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($todayEvents as $event)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-start justify-between mb-3">
                        <span class="inline-flex items-center rounded-full bg-gold-100 px-2.5 py-0.5 text-xs font-medium text-gold-700">{{ ucfirst($event->event_type) }}</span>
                        @php
                            $cap = $event->capacity;
                            $sold = $event->ticketCount;
                            $pct = $cap ? round(($sold / $cap) * 100) : null;
                        @endphp
                        @if($pct !== null)
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                {{ $pct >= 95 ? 'bg-red-100 text-red-700' : ($pct >= 80 ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700') }}">
                                {{ $pct }}% Full
                            </span>
                        @endif
                    </div>

                    <h3 class="font-bold text-slate-900 mb-1">{{ $event->name }}</h3>
                    <p class="text-xs text-slate-500 mb-4">{{ $event->venue->name ?? 'Online' }}</p>

                    <div class="mb-3">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                            <span>Check-In Progress</span>
                            <span class="font-semibold text-slate-700">{{ $event->checkedInCount }}/{{ $event->ticketCount }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500 bg-gold-500"
                                 :style="{ width: '{{ $event->checkInPercent }}%' }"></div>
                        </div>
                    </div>

                    <a href="{{ route('checkin.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gold-600 hover:text-gold-700 transition-colors">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Check-In Attendees
                    </a>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Training Requests --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Training Requests</h2>
            @if($trainingRequests->isEmpty())
                <p class="text-sm text-slate-500 text-center py-4">No training requests yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($trainingRequests->take(5) as $tr)
                        <a href="{{ route('training.show', $tr->slug) }}" class="flex justify-between items-start border-b border-slate-100 pb-3 last:border-0 hover:bg-slate-50 rounded-lg px-2 -mx-2 transition-colors">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $tr->title }}</p>
                                <p class="text-xs text-slate-500">{{ $tr->user->name }}{{ $tr->estimated_attendees ? ' · ' . number_format($tr->estimated_attendees) . ' attendees' : '' }}</p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $tr->status === 'pending' ? 'bg-amber-100 text-amber-700' : ($tr->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600') }}">
                                {{ ucfirst($tr->status) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Upcoming Events with Capacity --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Upcoming Events</h2>
            @forelse($events->take(5) as $event)
                @php
                    $cap = $event->capacity;
                    $sold = \App\Models\Ticket::where('event_id', $event->id)->count();
                    $pct = $cap ? round(($sold / $cap) * 100) : null;
                @endphp
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3 last:border-0 last:pb-0 last:mb-0">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-900 truncate">{{ $event->name }}</p>
                        <p class="text-xs text-slate-500">{{ $event->start_date->format('M d, Y') }} · {{ $event->venue->name ?? 'Online' }}</p>
                    </div>
                    <div class="flex items-center gap-2 ml-3">
                        @if($pct !== null)
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                {{ $pct >= 95 ? 'bg-red-100 text-red-700' : ($pct >= 80 ? 'bg-orange-100 text-orange-700' : 'bg-emerald-100 text-emerald-700') }}">
                                {{ $pct }}%
                            </span>
                        @endif
                        <span class="text-xs text-slate-400">{{ $sold }}/{{ $cap ?? '∞' }}</span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500 text-center py-4">No upcoming events.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection