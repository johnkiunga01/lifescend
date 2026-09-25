@extends('layouts.dashboard')

@section('pageTitle', 'Reports')

@section('content')
<div>
    <div class="mb-8">
        <h1 class="text-2xl font-extrabold text-slate-900">Reports</h1>
        <p class="text-sm text-slate-500 mt-1">Analytics and insights across your events and payments.</p>
    </div>

    {{-- Quick stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-5 text-white shadow-lg shadow-emerald-500/20">
            <div class="absolute right-0 top-0 -mt-3 -mr-3 h-20 w-20 rounded-full bg-white/10"></div>
            <svg class="h-5 w-5 mb-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <p class="text-xs font-semibold uppercase tracking-wider text-emerald-100">Today's Revenue</p>
            <p class="text-2xl font-extrabold mt-1">${{ number_format($quickStats['todayRevenue'], 2) }}</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-5 text-white shadow-lg shadow-blue-500/20">
            <div class="absolute right-0 top-0 -mt-3 -mr-3 h-20 w-20 rounded-full bg-white/10"></div>
            <svg class="h-5 w-5 mb-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            <p class="text-xs font-semibold uppercase tracking-wider text-blue-100">Today's Orders</p>
            <p class="text-2xl font-extrabold mt-1">{{ $quickStats['todayOrders'] }}</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-5 text-white shadow-lg shadow-amber-500/20">
            <div class="absolute right-0 top-0 -mt-3 -mr-3 h-20 w-20 rounded-full bg-white/10"></div>
            <svg class="h-5 w-5 mb-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <p class="text-xs font-semibold uppercase tracking-wider text-amber-100">Today's Check-Ins</p>
            <p class="text-2xl font-extrabold mt-1">{{ $quickStats['todayCheckIns'] }}</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 p-5 text-white shadow-lg shadow-rose-500/20">
            <div class="absolute right-0 top-0 -mt-3 -mr-3 h-20 w-20 rounded-full bg-white/10"></div>
            <svg class="h-5 w-5 mb-3 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <p class="text-xs font-semibold uppercase tracking-wider text-rose-100">Pending Orders</p>
            <p class="text-2xl font-extrabold mt-1">{{ $quickStats['pendingOrders'] }}</p>
        </div>
    </div>

    {{-- Report Cards --}}
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach([
            ['Sales Overview', 'Revenue trends, daily breakdown, and payment data.', 'manage.reports.sales', 'emerald'],
            ['Event Performance', 'Tickets sold, revenue, and check-in rates per event.', 'manage.reports.events', 'blue'],
            ['Attendance Report', 'Check-in records, no-shows, and daily attendance.', 'manage.reports.attendance', 'amber'],
            ['Customer Insights', 'Top customers, spending patterns, and demographics.', 'manage.reports.customers', 'purple'],
            ['Coupon Analytics', 'Redemption rates, discount impact, and coupon ROI.', 'manage.reports.coupons', 'rose'],
            ['Revenue Breakdown', 'Revenue by payment method, event type, and ticket tier.', 'manage.reports.revenue', 'teal'],
        ] as $idx => [$title, $desc, $route, $color])
        @php
            $gradients = [
                'emerald' => 'from-emerald-500 to-emerald-600 shadow-emerald-500/20 border-emerald-200',
                'blue' => 'from-blue-500 to-blue-600 shadow-blue-500/20 border-blue-200',
                'amber' => 'from-amber-500 to-amber-600 shadow-amber-500/20 border-amber-200',
                'purple' => 'from-purple-500 to-purple-600 shadow-purple-500/20 border-purple-200',
                'rose' => 'from-rose-500 to-rose-600 shadow-rose-500/20 border-rose-200',
                'teal' => 'from-teal-500 to-teal-600 shadow-teal-500/20 border-teal-200',
            ];
        @endphp
        <a href="{{ route($route) }}" class="group relative rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-xl hover:border-transparent transition-all duration-300 overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $gradients[$color] }}"></div>
            <div class="p-6 pt-7">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-{{ $color }}-600 transition-colors">{{ $title }}</h3>
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br {{ $gradients[$color] }} flex items-center justify-center text-white group-hover:scale-110 transition-transform shrink-0">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
                <p class="text-sm text-slate-500 leading-relaxed">{{ $desc }}</p>
                <span class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-{{ $color }}-600 group-hover:gap-2 transition-all">View Report <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg></span>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
