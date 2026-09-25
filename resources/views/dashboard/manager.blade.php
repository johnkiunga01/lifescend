@extends('layouts.dashboard')

@section('pageTitle', 'Manager Dashboard')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Manager Overview</h1>
    <p class="text-sm text-slate-500 mt-1">Event performance and team oversight.</p>
</div>

{{-- Metrics --}}
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Revenue</p>
        <p class="mt-2 text-3xl font-extrabold text-slate-900">${{ number_format($totalRevenue, 2) }}</p>
    </div>
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

{{-- Events --}}
<div class="mb-8">
    <h2 class="text-lg font-bold text-slate-900 mb-4">Events</h2>
    <div class="grid gap-5 sm:grid-cols-2">
        @foreach($events as $event)
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex justify-between items-start mb-3">
                <span class="inline-flex items-center rounded-full bg-gold-100 px-2.5 py-0.5 text-xs font-medium text-gold-700">{{ ucfirst($event->event_type) }}</span>
                <span class="text-xs text-slate-400">{{ $event->start_date->format('M d, Y') }}</span>
            </div>
            <h3 class="font-bold text-slate-900">{{ $event->name }}</h3>
            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($event->description), 140) }}</p>
            <div class="mt-3 flex items-center justify-between">
                <span class="text-xs text-slate-400">{{ $event->venue->name ?? 'Online' }}</span>
                <a href="{{ route('events.show', $event->slug) }}" class="text-xs font-semibold text-gold-600 hover:text-gold-700">View &rarr;</a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-2">
    {{-- Training Requests --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Training Requests</h2>
        @if($trainingRequests->isEmpty())
        <p class="text-sm text-slate-500 text-center py-4">No requests yet.</p>
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

    {{-- Recent Orders --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-4">Recent Transactions</h2>
        @if($recentOrders->isEmpty())
        <p class="text-sm text-slate-500 text-center py-4">No transactions yet.</p>
        @else
        <div class="space-y-3">
            @foreach($recentOrders as $order)
            <div class="flex justify-between items-center border-b border-slate-100 pb-3 last:border-0">
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ $order->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $order->event->name }}</p>
                </div>
                <span class="text-sm font-bold text-slate-900">${{ number_format($order->total_amount, 2) }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection