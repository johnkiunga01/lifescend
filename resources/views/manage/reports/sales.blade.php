@extends('layouts.dashboard')

@section('pageTitle', 'Sales Overview')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('manage.reports.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Reports</a>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Sales Overview</h1>
        </div>
        <div class="flex items-center gap-1 rounded-xl border border-slate-200 p-1 bg-slate-50">
            @foreach(['7d' => '7D', '30d' => '30D', '90d' => '90D', '12m' => '1Y'] as $val => $label)
            <a href="?period={{ $val }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-all {{ $period === $val ? 'bg-white text-slate-900 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-3 mb-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center"><svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Revenue</p>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">${{ number_format($totals['revenue'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center"><svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg></div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Completed Orders</p>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">{{ $totals['count'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded-lg bg-purple-100 flex items-center justify-center"><svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg></div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Avg Order Value</p>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">${{ number_format($totals['avg'], 2) }}</p>
        </div>
    </div>

    {{-- Chart --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm mb-8">
        <h2 class="text-lg font-bold text-slate-900 mb-1">Daily Revenue Trend</h2>
        <p class="text-xs text-slate-400 mb-4">Revenue per day over the selected period.</p>
        <div class="h-72">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-900">Recent Paid Orders</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase text-slate-500">Order</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase text-slate-500">Customer</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase text-slate-500">Event</th>
                        <th class="px-3 py-3.5 text-right text-xs font-semibold uppercase text-slate-500">Amount</th>
                        <th class="px-3 py-3.5 pr-6 text-right text-xs font-semibold uppercase text-slate-500">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 pl-6 pr-3 text-sm font-mono font-bold text-slate-900">{{ $order->invoice?->invoice_number ?? 'ORD-'.$order->id }}</td>
                        <td class="px-3 py-4 text-sm text-slate-600">{{ $order->user?->name ?? '—' }}</td>
                        <td class="px-3 py-4 text-sm text-slate-600 max-w-[180px] truncate">{{ $order->event?->name ?? '—' }}</td>
                        <td class="px-3 py-4 text-sm font-bold text-slate-900 text-right">${{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-3 py-4 pr-6 text-sm text-slate-500 text-right">{{ $order->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-sm text-slate-500">No orders in this period.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $orders->links() }}</div>@endif
    </div>
</div>
@endsection

@push('scripts')
<script id="daily-revenue-json" type="application/json">
    {
        !!json_encode($dailyRevenue) !!
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;
        const data = JSON.parse(document.getElementById('daily-revenue-json').textContent);
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(d => new Date(d.date).toLocaleDateString('en', {
                    month: 'short',
                    day: 'numeric'
                })),
                datasets: [{
                    label: 'Revenue ($)',
                    data: data.map(d => d.revenue),
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5,150,105,0.08)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                    pointBackgroundColor: '#059669',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#94a3b8',
                            callback: v => '$' + v
                        }
                    }
                }
            }
        });
    });
</script>
@endpush