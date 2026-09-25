@extends('layouts.dashboard')

@section('pageTitle', 'Revenue Breakdown')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('manage.reports.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Reports</a>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Revenue Breakdown</h1>
        </div>
        <div class="flex items-center gap-1 rounded-xl border border-slate-200 p-1 bg-slate-50">
            @foreach(['7d' => '7D', '30d' => '30D', '90d' => '90D', '12m' => '1Y'] as $val => $label)
            <a href="?period={{ $val }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold transition-all {{ $period === $val ? 'bg-white text-slate-900 shadow-sm border border-slate-200' : 'text-slate-500 hover:text-slate-700' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    {{-- Chart Row --}}
    <div class="grid gap-6 lg:grid-cols-3 mb-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">By Payment Method</h2>
            <p class="text-xs text-slate-400 mb-4">Revenue share.</p>
            <div class="h-64 flex items-center"><canvas id="methodChart"></canvas></div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">By Event Type</h2>
            <p class="text-xs text-slate-400 mb-4">Revenue share.</p>
            <div class="h-64 flex items-center"><canvas id="eventTypeChart"></canvas></div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">By Ticket Tier</h2>
            <p class="text-xs text-slate-400 mb-4">Revenue share.</p>
            <div class="h-64 flex items-center"><canvas id="ticketTypeChart"></canvas></div>
        </div>
    </div>

    {{-- By Payment Method Table --}}
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">By Payment Method</h2>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase text-slate-500">Method</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold uppercase text-slate-500">Orders</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold uppercase text-slate-500">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($byMethod as $m)<tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 font-semibold text-slate-900">{{ $m->method }}</td>
                        <td class="py-3 px-4 text-right text-slate-600">{{ $m->count }}</td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-600">${{ number_format($m->revenue, 2) }}</td>
                    </tr>@empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-sm text-slate-500">No data.</td>
                    </tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">By Event Type</h2>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase text-slate-500">Type</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold uppercase text-slate-500">Orders</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold uppercase text-slate-500">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($byEventType as $et)<tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 font-semibold text-slate-900 capitalize">{{ $et->event_type }}</td>
                        <td class="py-3 px-4 text-right text-slate-600">{{ $et->count }}</td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-600">${{ number_format($et->revenue, 2) }}</td>
                    </tr>@empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-sm text-slate-500">No data.</td>
                    </tr>@endforelse
                </tbody>
            </table>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">By Ticket Tier</h2>
            </div>
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-semibold uppercase text-slate-500">Tier</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold uppercase text-slate-500">Sold</th>
                        <th class="py-3 px-4 text-right text-xs font-semibold uppercase text-slate-500">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($byTicketType as $tt)<tr class="hover:bg-slate-50">
                        <td class="py-3 px-4 font-semibold text-slate-900">{{ $tt->name }}</td>
                        <td class="py-3 px-4 text-right text-slate-600">{{ $tt->sold }}</td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-600">${{ number_format($tt->revenue, 2) }}</td>
                    </tr>@empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-sm text-slate-500">No data.</td>
                    </tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script id="revenue-by-method-json" type="application/json">
    {
        !!json_encode($byMethod) !!
    }
</script>
<script id="revenue-by-event-type-json" type="application/json">
    {
        !!json_encode($byEventType) !!
    }
</script>
<script id="revenue-by-ticket-type-json" type="application/json">
    {
        !!json_encode($byTicketType) !!
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const byMethod = JSON.parse(document.getElementById('revenue-by-method-json').textContent);
        const byEventType = JSON.parse(document.getElementById('revenue-by-event-type-json').textContent);
        const byTicketType = JSON.parse(document.getElementById('revenue-by-ticket-type-json').textContent);
        const pieColors = ['#059669', '#2563eb', '#d97706', '#7c3aed', '#e11d48', '#0d9488', '#6366f1', '#0891b2'];

        [{
                id: 'methodChart',
                data: byMethod,
                labelKey: 'method'
            },
            {
                id: 'eventTypeChart',
                data: byEventType,
                labelKey: 'event_type'
            },
            {
                id: 'ticketTypeChart',
                data: byTicketType,
                labelKey: 'name'
            }
        ].forEach(cfg => {
            const ctx = document.getElementById(cfg.id);
            if (!ctx || !cfg.data.length) return;
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: cfg.data.map(d => (d[cfg.labelKey] || '').length > 18 ? (d[cfg.labelKey] || '').substring(0, 18) + '...' : d[cfg.labelKey]),
                    datasets: [{
                        data: cfg.data.map(d => d.revenue),
                        backgroundColor: pieColors,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 14,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    });
</script>
@endpush