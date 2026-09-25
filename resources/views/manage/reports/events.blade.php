@extends('layouts.dashboard')

@section('pageTitle', 'Event Performance')

@section('content')
<div>
    <div class="mb-6">
        <a href="{{ route('manage.reports.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Reports</a>
        <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Event Performance</h1>
    </div>

    {{-- Charts Row --}}
    <div class="grid gap-6 lg:grid-cols-2 mb-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Revenue by Event</h2>
            <p class="text-xs text-slate-400 mb-4">Total revenue per event from completed orders.</p>
            <div class="h-80">
                <canvas id="revenueBarChart"></canvas>
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Check-In Rate</h2>
            <p class="text-xs text-slate-400 mb-4">Percentage of tickets checked in per event.</p>
            <div class="h-80">
                <canvas id="checkinRateChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Events Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-900">All Events</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase text-slate-500">Event</th>
                        <th class="px-3 py-3.5 text-center text-xs font-semibold uppercase text-slate-500">Tickets Sold</th>
                        <th class="px-3 py-3.5 text-center text-xs font-semibold uppercase text-slate-500">Check-Ins</th>
                        <th class="px-3 py-3.5 text-center text-xs font-semibold uppercase text-slate-500">Rate</th>
                        <th class="px-3 py-3.5 text-right text-xs font-semibold uppercase text-slate-500">Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($events as $event)
                    @php $checkinRate = $event->tickets_count > 0 ? round(($event->check_ins_count / $event->tickets_count) * 100) : 0; @endphp
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 pl-6 pr-3">
                            <p class="text-sm font-bold text-slate-900">{{ $event->name }}</p>
                            <p class="text-xs text-slate-500">{{ $event->start_date->format('M d, Y') }} · {{ ucfirst($event->status) }}</p>
                        </td>
                        <td class="px-3 py-4 text-center text-sm font-semibold text-slate-900">{{ $event->tickets_count }}</td>
                        <td class="px-3 py-4 text-center text-sm font-semibold {{ $event->check_ins_count > 0 ? 'text-emerald-600' : 'text-slate-400' }}">{{ $event->check_ins_count }}</td>
                        <td class="px-3 py-4 text-center"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $checkinRate >= 80 ? 'bg-emerald-100 text-emerald-700' : ($checkinRate >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ $checkinRate }}%</span></td>
                        <td class="px-3 py-4 text-right text-sm font-bold text-emerald-600">${{ number_format($event->revenue ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-sm text-slate-500">No events found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script id="event-revenue-json" type="application/json">
    {
        !!json_encode($events) !!
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const events = JSON.parse(document.getElementById('event-revenue-json').textContent);

        const revCtx = document.getElementById('revenueBarChart');
        if (revCtx) {
            new Chart(revCtx, {
                type: 'bar',
                data: {
                    labels: events.map(e => e.name.length > 20 ? e.name.substring(0, 20) + '...' : e.name),
                    datasets: [{
                        label: 'Revenue',
                        data: events.map(e => e.revenue || 0),
                        backgroundColor: events.map((_, i) => ['#059669', '#2563eb', '#d97706', '#7c3aed', '#e11d48', '#0d9488', '#6366f1', '#0891b2', '#4f46e5', '#0284c7'][i % 10]),
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
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
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                color: '#475569'
                            }
                        }
                    }
                }
            });
        }

        const rateCtx = document.getElementById('checkinRateChart');
        if (rateCtx) {
            new Chart(rateCtx, {
                type: 'bar',
                data: {
                    labels: events.map(e => e.name.length > 20 ? e.name.substring(0, 20) + '...' : e.name),
                    datasets: [{
                        label: 'Tickets Sold',
                        data: events.map(e => e.tickets_count),
                        backgroundColor: 'rgba(37,99,235,0.6)',
                        borderRadius: 6
                    }, {
                        label: 'Checked In',
                        data: events.map(e => e.check_ins_count),
                        backgroundColor: 'rgba(5,150,105,0.6)',
                        borderRadius: 6
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
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
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
                                color: '#475569'
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush