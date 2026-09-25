@extends('layouts.dashboard')

@section('pageTitle', 'Check-In')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-extrabold text-slate-900">Attendee Check-In</h1>
    <p class="mt-1 text-sm text-slate-500">Select an event, validate tickets, and check in attendees.</p>
</div>

@php
    $error = session('checkin.error');
    $success = session('checkin.success');
@endphp

@if($success)
    <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        {{ $success }}
    </div>
@endif

@if($error)
    <div class="mb-6 flex items-center gap-3 rounded-xl bg-red-50 border border-red-200 px-5 py-4 text-sm text-red-700">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        {{ $error }}
    </div>
@endif

{{-- Step Indicator --}}
<div class="mb-8">
    <div class="flex items-center gap-0">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold {{ $step >= 1 ? 'bg-gold-500 text-white' : 'bg-slate-200 text-slate-400' }}">1</span>
            <span class="text-sm font-semibold {{ $step >= 1 ? 'text-slate-900' : 'text-slate-400' }}">Event</span>
        </div>
        <div class="h-px w-12 mx-2 {{ $step >= 2 ? 'bg-gold-500' : 'bg-slate-200' }}"></div>
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold {{ $step >= 2 ? 'bg-gold-500 text-white' : 'bg-slate-200 text-slate-400' }}">2</span>
            <span class="text-sm font-semibold {{ $step >= 2 ? 'text-slate-900' : 'text-slate-400' }}">Scan</span>
        </div>
        <div class="h-px w-12 mx-2 {{ $step >= 3 ? 'bg-gold-500' : 'bg-slate-200' }}"></div>
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold {{ $step >= 3 ? 'bg-gold-500 text-white' : 'bg-slate-200 text-slate-400' }}">3</span>
            <span class="text-sm font-semibold {{ $step >= 3 ? 'text-slate-900' : 'text-slate-400' }}">Verify</span>
        </div>
        <div class="h-px w-12 mx-2 {{ $step >= 4 ? 'bg-gold-500' : 'bg-slate-200' }}"></div>
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-bold {{ $step >= 4 ? 'bg-gold-500 text-white' : 'bg-slate-200 text-slate-400' }}">4</span>
            <span class="text-sm font-semibold {{ $step >= 4 ? 'text-slate-900' : 'text-slate-400' }}">Done</span>
        </div>
    </div>
</div>

<div class="grid gap-8 lg:grid-cols-3 mb-10">
    <div class="lg:col-span-1">
        {{-- STEP 1: Select Event --}}
        @if($step === 1)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Select Event</h2>
            <p class="text-sm text-slate-500 mb-5">Choose the event you are checking attendees into.</p>

            <form action="{{ route('checkin.select-event') }}" method="POST" x-data="{ search: '' }">
                @csrf
                <div class="relative mb-4">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" x-model="search" placeholder="Search events..." class="block w-full rounded-xl border border-slate-300 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                </div>
                <div class="space-y-3 max-h-[400px] overflow-y-auto overscroll-contain pr-1 scrollbar-thin scrollbar-thumb-slate-300 scrollbar-track-transparent">
                    @forelse($events as $ev)
                    <label x-show="$el.querySelector('input').checked || search === '' || '{{ strtolower(addslashes($ev->name)) }}'.includes(search.toLowerCase())" class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 cursor-pointer hover:border-gold-300 hover:bg-gold-50/30 transition-all has-[:checked]:border-gold-500 has-[:checked]:bg-gold-50 has-[:checked]:ring-1 has-[:checked]:ring-gold-500">
                        <input type="radio" name="event_id" value="{{ $ev->id }}" class="mt-1 h-4 w-4 shrink-0 accent-gold-500" required>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-slate-900">{{ $ev->name }}</p>
                                @if($ev->start_date && $ev->start_date->isFuture())
                                    @php $daysUntil = (int) now()->diffInDays($ev->start_date, false); @endphp
                                    @if($ev->start_date->isToday())
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 shrink-0">Today</span>
                                    @elseif($daysUntil <= 7)
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700 shrink-0">Coming Soon</span>
                                    @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700 shrink-0">Coming Soon in {{ $daysUntil }} days</span>
                                    @endif
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $ev->start_date?->format('M j, Y') ?: 'Date TBD' }}</p>
                            @if($ev->venue)<p class="text-xs text-slate-400 mt-0.5">{{ $ev->venue->name }}</p>@endif
                        </div>
                    </label>
                    @empty
                    <p class="text-sm text-slate-500 text-center py-4">No published events available.</p>
                    @endforelse
                </div>

                <button type="submit" class="mt-5 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gold-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all duration-200">
                    Continue
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
            </form>
        </div>
        @endif

        {{-- STEP 2: Enter QR Code --}}
        @if($step === 2)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900">Scan Ticket</h2>
                <form action="{{ route('checkin.reset') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-slate-700 underline">Change</button>
                </form>
            </div>

            <div class="mb-4 rounded-xl bg-slate-50 border border-slate-200 p-3 text-sm">
                <p class="font-semibold text-slate-900">{{ $event?->name }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $event?->start_date?->format('M j, Y') }}</p>
            </div>

            <form action="{{ route('checkin.verify') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="qr_code" class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">QR / Ticket Code</label>
                        <input id="qr_code" name="qr_code" type="text" placeholder="e.g. TKT-AISUMMIT-XYZ" required autofocus
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                    </div>
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gold-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        Find Ticket
                    </button>
                </div>
            </form>
        </div>
        @endif

        {{-- STEP 3: Verify & Confirm --}}
        @if($step === 3 && $ticket)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900">Verify Attendee</h2>
                <form action="{{ route('checkin.reset') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-slate-500 hover:text-slate-700 underline">Cancel</button>
                </form>
            </div>

            <div class="space-y-4">
                <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gold-100 text-gold-600 text-lg font-bold">
                        {{ strtoupper(substr($ticket->user->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900">{{ $ticket->user->name }}</p>
                        <p class="text-xs text-slate-500">{{ $ticket->user->email }}</p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 divide-y divide-slate-100">
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs font-medium text-slate-500">Ticket Code</span>
                        <span class="text-sm font-mono font-semibold text-slate-900">{{ $ticket->qr_code }}</span>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs font-medium text-slate-500">Ticket Type</span>
                        <span class="text-sm font-semibold text-slate-900">{{ $ticket->ticketType?->name ?? 'Standard' }}</span>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs font-medium text-slate-500">Event</span>
                        <span class="text-sm font-semibold text-slate-900 text-right">{{ $ticket->event->name }}</span>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3">
                        <span class="text-xs font-medium text-slate-500">Status</span>
                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Active</span>
                    </div>
                </div>

                <form action="{{ route('checkin.confirm') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:bg-emerald-600 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Confirm Check-In
                    </button>
                </form>

                <form action="{{ route('checkin.reset') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-center text-sm font-semibold text-slate-500 hover:text-slate-700 py-2">This is not the right person</button>
                </form>
            </div>
        </div>
        @endif

        {{-- STEP 4: Success --}}
        @if($step === 4)
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 mb-4">
                <svg class="h-8 w-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <h2 class="text-lg font-bold text-slate-900 mb-1">Check-In Complete</h2>
            <p class="text-sm text-slate-500 mb-6">{{ session('checkin.success') }}</p>

            <form action="{{ route('checkin.reset') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gold-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Check In Next Attendee
                </button>
            </form>
        </div>
        @endif
    </div>

    <div class="lg:col-span-2">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900">Recent Check-Ins</h2>
                    @if($step >= 2 && $event)
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-slate-500">Filtering:</span>
                        <span class="inline-flex items-center rounded-full bg-gold-100 px-2.5 py-0.5 text-xs font-semibold text-gold-700">{{ $event->name }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="overflow-x-auto max-h-[400px] overflow-y-auto overscroll-contain scrollbar-thin scrollbar-thumb-slate-300 scrollbar-track-transparent">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50 sticky top-0 z-10">
                        <tr>
                            <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Attendee</th>
                            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Event</th>
                            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Ticket Code</th>
                            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Gate Agent</th>
                            <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentCheckIns as $checkIn)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm">
                                    <p class="font-semibold text-slate-900">{{ $checkIn->user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $checkIn->user->email }}</p>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-600">{{ $checkIn->event->name }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-600 font-mono">{{ $checkIn->ticket->qr_code }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-600">{{ $checkIn->checkedInBy->name }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">{{ $checkIn->checked_in_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-sm text-slate-500">No check-ins recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
