@extends('layouts.dashboard')

@section('pageTitle', $event->name)

@section('content')
<div>
    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('manage.events.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Back to Events</a>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $event->name }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $event->start_date->format('M d, Y') }} @if($event->venue)· {{ $event->venue->name }}@endif</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('manage.events.edit', $event->slug) }}" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">Edit Event</a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Tickets</p>
            <p class="text-2xl font-extrabold text-slate-900 mt-1">{{ $stats['total_tickets'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Checked In</p>
            <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['checked_in'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending</p>
            <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Revenue</p>
            <p class="text-2xl font-extrabold text-slate-900 mt-1">${{ number_format($stats['revenue'], 2) }}</p>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">

        {{-- Left: Attendance & QR Scanner --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Quick QR Check-In --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Quick Check-In</h2>
                    <span class="text-xs text-slate-400">Scan or enter ticket code</span>
                </div>
                <form action="{{ route('manage.events.checkin', $event->slug) }}" method="POST" class="flex gap-3">
                    @csrf
                    <input type="text" name="qr_code" placeholder="Scan QR or enter ticket code..." required autofocus
                        class="block flex-1 min-w-0 rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm font-mono">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:bg-emerald-600 transition-all shrink-0">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Check In
                    </button>
                </form>
            </div>

            {{-- Attendees / Tickets Table --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900">Attendees</h2>
                    <span class="text-xs text-slate-500">{{ $event->attendees->count() }} total</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Attendee</th>
                                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Ticket Type</th>
                                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Code</th>
                                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Check-In</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($event->attendees as $attendee)
                            @php
                                $isCheckedIn = $attendee->status === 'checked-in';
                                $displayName = $attendee->participant_name ?? $attendee->user?->name ?? 'Deleted User';
                                $displayEmail = $attendee->participant_email ?? $attendee->user?->email ?? '—';
                                $isParticipant = !empty($attendee->participant_name);
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 pl-6 pr-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-gold-100 flex items-center justify-center text-xs font-bold text-gold-700 shrink-0">
                                            {{ strtoupper(substr($displayName, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $displayName }}</p>
                                                @if($isParticipant)
                                                <span class="inline-flex items-center rounded-full bg-indigo-50 border border-indigo-200 px-2 py-0.5 text-[10px] font-semibold text-indigo-600">Attendee</span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-slate-500 truncate">{{ $displayEmail }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-sm text-slate-600">{{ $attendee->ticket?->ticketType?->name ?? '—' }}</td>
                                <td class="px-3 py-4">
                                    <span class="text-sm font-mono font-semibold text-slate-700">{{ $attendee->ticket?->qr_code ?? '—' }}</span>
                                </td>
                                <td class="px-3 py-4">
                                    @if($isCheckedIn)
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Checked In</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-600">Registered</span>
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-sm text-slate-500">
                                    @if($isCheckedIn)
                                        {{ $attendee->ticket?->checkIns->first()?->checked_in_at?->diffForHumans() ?? 'Just now' }}
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-sm text-slate-500">No attendees yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Check-Ins --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-900">Recent Check-Ins</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Attendee</th>
                                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Ticket</th>
                                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Agent</th>
                                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($event->checkIns as $ci)
                            @php
                                $ciName = $ci->ticket?->attendee?->participant_name ?? $ci->user->name;
                                $ciEmail = $ci->ticket?->attendee?->participant_email ?? $ci->user->email;
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-4 pl-6 pr-3">
                                    <p class="text-sm font-semibold text-slate-900">{{ $ciName }}</p>
                                    <p class="text-xs text-slate-500">{{ $ciEmail }}</p>
                                </td>
                                <td class="px-3 py-4 text-sm font-mono text-slate-600">{{ $ci->ticket->qr_code }}</td>
                                <td class="px-3 py-4 text-sm text-slate-600">{{ $ci->checkedInBy->name }}</td>
                                <td class="px-3 py-4 text-sm text-slate-500">{{ $ci->checked_in_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-sm text-slate-500">No check-ins yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right: Event Info --}}
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="h-4 w-4 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Event Details
                </h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Status</dt>
                        <dd><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $event->status === 'published' ? 'bg-emerald-100 text-emerald-700' : ($event->status === 'draft' ? 'bg-slate-100 text-slate-600' : 'bg-amber-100 text-amber-700') }}">{{ ucfirst($event->status) }}</span></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Type</dt>
                        <dd class="font-semibold text-slate-900">{{ ucfirst($event->event_type) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Start</dt>
                        <dd class="font-semibold text-slate-900">{{ $event->start_date->format('M d, Y h:i A') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">End</dt>
                        <dd class="font-semibold text-slate-900">{{ $event->end_date->format('M d, Y h:i A') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Timezone</dt>
                        <dd class="font-semibold text-slate-900">{{ $event->timezone }}</dd>
                    </div>
                    @if($event->venue)
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Venue</dt>
                        <dd class="font-semibold text-slate-900 text-right max-w-[60%]">{{ $event->venue->name }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Capacity</dt>
                        <dd class="font-semibold text-slate-900">{{ $event->capacity ?? 'Unlimited' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Tickets Sold</dt>
                        <dd class="font-semibold text-slate-900">{{ $stats['total_tickets'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Revenue</dt>
                        <dd class="font-semibold text-emerald-600">${{ number_format($stats['revenue'], 2) }}</dd>
                    </div>
                </dl>
            </div>

            @if($event->description)
            {{-- Description --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="h-4 w-4 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                    Description
                </h3>
                <div class="prose prose-sm prose-slate max-w-none prose-headings:font-bold prose-headings:text-slate-900">{!! $event->description_html !!}</div>
            </div>
            @endif

            {{-- Ticket Types Summary --}}
            @if($event->ticketTypes->isNotEmpty())
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <svg class="h-4 w-4 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                    Ticket Tiers
                </h3>
                <div class="space-y-3">
                    @foreach($event->ticketTypes as $tt)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $tt->name }}</p>
                            <p class="text-xs text-slate-500">
                                {{ number_format($tt->payablePrice(), 2) }} {{ $tt->checkoutCurrency() }}
                                @if($tt->usdReferencePrice() !== null)
                                    · {{ number_format($tt->usdReferencePrice(), 2) }} USD reference
                                @endif
                                · {{ $tt->sold_count }}/{{ $tt->quantity }} sold
                            </p>
                        </div>
                        <span class="text-xs font-semibold {{ ($tt->quantity - $tt->sold_count) <= 0 ? 'text-red-500' : 'text-emerald-600' }}">
                            {{ $tt->quantity - $tt->sold_count }} left
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Public Link --}}
            <a href="{{ route('events.show', $event->slug) }}" target="_blank"
                class="flex items-center justify-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:border-gold-300 hover:text-gold-600 transition-all shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                View Public Event Page
            </a>
        </div>
    </div>
</div>
@endsection
