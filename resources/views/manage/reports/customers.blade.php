@extends('layouts.dashboard')

@section('pageTitle', 'Customer Insights')

@section('content')
<div>
    <div class="mb-6">
        <a href="{{ route('manage.reports.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Reports</a>
        <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Customer Insights</h1>
        <p class="text-sm text-slate-500 mt-1">Top {{ $topCustomers->count() }} customers by total spend.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3 mb-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Top Spenders</h2>
            <p class="text-xs text-slate-400 mb-4">Highest total spend per customer.</p>
            <div class="h-72"><canvas id="topSpendersChart"></canvas></div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Orders per Customer</h2>
            <p class="text-xs text-slate-400 mb-4">Number of orders placed.</p>
            <div class="h-72"><canvas id="ordersChart"></canvas></div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Tickets per Customer</h2>
            <p class="text-xs text-slate-400 mb-4">Total tickets purchased.</p>
            <div class="h-72"><canvas id="ticketsChart"></canvas></div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-900">Customer Leaderboard</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase text-slate-500 w-12">#</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase text-slate-500">Customer</th>
                        <th class="px-3 py-3.5 text-center text-xs font-semibold uppercase text-slate-500">Orders</th>
                        <th class="px-3 py-3.5 text-center text-xs font-semibold uppercase text-slate-500">Tickets</th>
                        <th class="px-3 py-3.5 text-right text-xs font-semibold uppercase text-slate-500">Total Spent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($topCustomers as $i => $user)
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 pl-6 pr-3 text-sm font-bold text-slate-400">{{ $i + 1 }}</td>
                        <td class="px-3 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-xs font-bold text-white shrink-0">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-4 text-center text-sm font-semibold text-slate-900">{{ $user->orders_count }}</td>
                        <td class="px-3 py-4 text-center text-sm font-semibold text-slate-900">{{ $user->tickets_count }}</td>
                        <td class="px-3 py-4 text-right text-sm font-bold text-emerald-600">${{ number_format($user->total_spent ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-sm text-slate-500">No customer data yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script id="top-customers-json" type="application/json">
    {
        !!json_encode($topCustomers) !!
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const customers = JSON.parse(document.getElementById('top-customers-json').textContent);
        const top10 = customers.slice(0, 10);
        const colors = ['#7c3aed', '#2563eb', '#059669', '#d97706', '#e11d48', '#0d9488', '#6366f1', '#0891b2', '#4f46e5', '#0284c7'];

        ['topSpendersChart', 'ordersChart', 'ticketsChart'].forEach((id, idx) => {
            const ctx = document.getElementById(id);
            if (!ctx || !top10.length) return;
            const datasets = idx === 0 ? [{
                    label: 'Spent',
                    data: top10.map(c => c.total_spent || 0),
                    backgroundColor: colors,
                    borderRadius: 6
                }] :
                idx === 1 ? [{
                    label: 'Orders',
                    data: top10.map(c => c.orders_count),
                    backgroundColor: 'rgba(37,99,235,0.6)',
                    borderRadius: 6
                }] :
                [{
                    label: 'Tickets',
                    data: top10.map(c => c.tickets_count),
                    backgroundColor: 'rgba(5,150,105,0.6)',
                    borderRadius: 6
                }];
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: top10.map(c => c.name.split(' ')[0]),
                    datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: idx === 0 ? 'y' : 'x',
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
                                    size: 10
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
                                    size: 10
                                },
                                color: '#94a3b8',
                                ...(idx === 2 ? {} : {
                                    callback: v => idx === 0 ? '$' + v : v
                                })
                            }
                        }
                    }
                }
            });
        });
    });
</script>
@endpush