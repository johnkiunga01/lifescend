@extends('layouts.dashboard')

@section('pageTitle', 'Coupon Analytics')

@section('content')
<div>
    <div class="mb-6">
        <a href="{{ route('manage.reports.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Reports</a>
        <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Coupon Analytics</h1>
    </div>

    <div class="grid gap-4 sm:grid-cols-3 mb-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded-lg bg-purple-100 flex items-center justify-center"><svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg></div>
                <p class="text-xs font-semibold uppercase text-slate-500">Total Redemptions</p>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">{{ $totalRedemptions }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded-lg bg-rose-100 flex items-center justify-center"><svg class="h-4 w-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <p class="text-xs font-semibold uppercase text-slate-500">Discount Given</p>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">${{ number_format($totalDiscount, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center"><svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <p class="text-xs font-semibold uppercase text-slate-500">Active Coupons</p>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">{{ $activeCoupons }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 mb-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Redemptions per Coupon</h2>
            <p class="text-xs text-slate-400 mb-4">Number of times each coupon was used.</p>
            <div class="h-72"><canvas id="redemptionsChart"></canvas></div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Discount by Coupon</h2>
            <p class="text-xs text-slate-400 mb-4">Total discount amount per coupon.</p>
            <div class="h-72"><canvas id="discountChart"></canvas></div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-900">All Coupons</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase text-slate-500">Code</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase text-slate-500">Type / Value</th>
                        <th class="px-3 py-3.5 text-center text-xs font-semibold uppercase text-slate-500">Redemptions</th>
                        <th class="px-3 py-3.5 text-right text-xs font-semibold uppercase text-slate-500">Discount</th>
                        <th class="px-3 py-3.5 pr-6 text-center text-xs font-semibold uppercase text-slate-500">Active</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($coupons as $coupon)
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 pl-6 pr-3 text-sm font-mono font-bold text-slate-900">{{ $coupon->code }}</td>
                        <td class="px-3 py-4 text-sm text-slate-600">{{ $coupon->type === 'percentage' ? $coupon->value.'%' : '$'.number_format($coupon->value, 2) }}</td>
                        <td class="px-3 py-4 text-center text-sm font-semibold text-slate-900">{{ $coupon->usages_count }}</td>
                        <td class="px-3 py-4 text-right text-sm font-bold text-rose-600">${{ number_format($coupon->usages_sum_discount_amount ?? 0, 2) }}</td>
                        <td class="px-3 py-4 pr-6 text-center"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $coupon->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $coupon->is_active ? 'Yes' : 'No' }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-sm text-slate-500">No coupons created.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script id="coupons-json" type="application/json">
    {
        !!json_encode($coupons) !!
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const coupons = JSON.parse(document.getElementById('coupons-json').textContent);
        const withUsage = coupons.filter(c => c.usages_count > 0);
        const labels = withUsage.map(c => c.code);
        const colors = ['#7c3aed', '#2563eb', '#059669', '#d97706', '#e11d48', '#0d9488', '#6366f1', '#0891b2', '#4f46e5', '#0284c7'];

        const redCtx = document.getElementById('redemptionsChart');
        if (redCtx && withUsage.length) {
            new Chart(redCtx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data: withUsage.map(c => c.usages_count),
                        backgroundColor: colors,
                        borderRadius: 8
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
                                    size: 10
                                }
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
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        const discCtx = document.getElementById('discountChart');
        if (discCtx && withUsage.length) {
            new Chart(discCtx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        data: withUsage.map(c => c.usages_sum_discount_amount || 0),
                        backgroundColor: colors,
                        borderRadius: 8
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
                                    size: 10
                                }
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
        }
    });
</script>
@endpush