@extends('layouts.dashboard')

@section('pageTitle', 'Attendance Report')

@section('content')
<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('manage.reports.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Reports</a>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-1">Attendance Report</h1>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-3 mb-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center"><svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg></div>
                <p class="text-xs font-semibold uppercase text-slate-500">Total Check-Ins</p>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center"><svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg></div>
                <p class="text-xs font-semibold uppercase text-slate-500">Today</p>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">{{ $stats['today'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center gap-3 mb-2">
                <div class="h-8 w-8 rounded-lg bg-purple-100 flex items-center justify-center"><svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg></div>
                <p class="text-xs font-semibold uppercase text-slate-500">Avg Per Event</p>
            </div>
            <p class="text-2xl font-extrabold text-slate-900">{{ number_format($stats['avg_per_event'], 1) }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-5 mb-8">
        <div class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">Check-In Timeline</h2>
            <p class="text-xs text-slate-400 mb-4">Number of check-ins per day.</p>
            <div class="h-72"><canvas id="timelineChart"></canvas></div>
        </div>
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-1">By Event</h2>
            <p class="text-xs text-slate-400 mb-4">Distribution across events.</p>
            <div class="h-72 flex items-center"><canvas id="byEventChart"></canvas></div>
        </div>
    </div>

    <div class="mb-6">
        <form method="GET" class="flex items-center gap-3">
            <select name="event_id" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-gold-500 focus:outline-none" onchange="this.form.submit()">
                <option value="">All Events</option>
                @foreach($events as $ev)<option value="{{ $ev->id }}" {{ $eventId == $ev->id ? 'selected' : '' }}>{{ $ev->name }}</option>@endforeach
            </select>
            @if($eventId)<a href="{{ route('manage.reports.attendance') }}" class="text-sm text-slate-500 hover:text-slate-700 underline">Clear</a>@endif
        </form>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-lg font-bold text-slate-900">Check-In Records</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase text-slate-500">Attendee</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase text-slate-500">Event</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase text-slate-500">Ticket</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase text-slate-500">Gate Agent</th>
                        <th class="px-3 py-3.5 pr-6 text-right text-xs font-semibold uppercase text-slate-500">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($checkIns as $ci)
                    <tr class="hover:bg-slate-50">
                        <td class="py-4 pl-6 pr-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $ci->user->name }}</p>
                            <p class="text-xs text-slate-500">{{ $ci->user->email }}</p>
                        </td>
                        <td class="px-3 py-4 text-sm text-slate-600 max-w-[150px] truncate">{{ $ci->event->name }}</td>
                        <td class="px-3 py-4 text-sm font-mono text-slate-600">{{ $ci->ticket->qr_code }}</td>
                        <td class="px-3 py-4 text-sm text-slate-600">{{ $ci->checkedInBy->name }}</td>
                        <td class="px-3 py-4 pr-6 text-sm text-slate-500 text-right whitespace-nowrap">{{ $ci->checked_in_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-sm text-slate-500">No check-ins recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($checkIns->hasPages())<div class="px-6 py-4 border-t border-slate-100">{{ $checkIns->links() }}</div>@endif
    </div>
</div>
@endsection

@push('scripts')
<script id="attendance-checkins-json" type="application/json">
    {
        !!json_encode($checkIns - > items()) !!
    }
</script>
<script id="attendance-events-json" type="application/json">
    {
        !!json_encode($events) !!
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkIns = JSON.parse(document.getElementById('attendance-checkins-json').textContent);
        const events = JSON.parse(document.getElementById('attendance-events-json').textContent);

        const timelineCtx = document.getElementById('timelineChart');
        if (timelineCtx && checkIns.length) {
            const byDate = {};
            checkIns.forEach(c => {
                const d = new Date(c.checked_in_at).toLocaleDateString('en', {
                    month: 'short',
                    day: 'numeric'
                });
                byDate[d] = (byDate[d] || 0) + 1;
            });
            const labels = Object.keys(byDate),
                values = Object.values(byDate);
            new Chart(timelineCtx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Check-Ins',
                        data: values,
                        borderColor: '#d97706',
                        backgroundColor: 'rgba(217,119,6,0.08)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#d97706',
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
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        const byEventCtx = document.getElementById('byEventChart');
        if (byEventCtx && events.length) {
            const counts = {};
            checkIns.forEach(c => {
                const n = c.event?.name || 'Unknown';
                counts[n] = (counts[n] || 0) + 1;
            });
            const sorted = Object.entries(counts).sort((a, b) => b[1] - a[1]);
            new Chart(byEventCtx, {
                type: 'doughnut',
                data: {
                    labels: sorted.map(e => e[0].length > 15 ? e[0].substring(0, 15) + '...' : e[0]),
                    datasets: [{
                        data: sorted.map(e => e[1]),
                        backgroundColor: ['#059669', '#2563eb', '#d97706', '#7c3aed', '#e11d48', '#0d9488', '#6366f1', '#0891b2'],
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
        }
    });
</script>
@endpush