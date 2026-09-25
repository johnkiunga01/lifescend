@extends('layouts.dashboard')

@section('pageTitle', 'Training Requests')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Training Requests</h1>
            <p class="text-sm text-slate-500 mt-1">View and manage all training requests.</p>
        </div>
        <button @@click="trainingModal = true" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            New Request
        </button>
    </div>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    @if($trainingRequests->isNotEmpty())
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Request</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Attendees</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Preferred Date</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($trainingRequests as $tr)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-gold-100 flex items-center justify-center shrink-0">
                                <svg class="h-4 w-4 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900">{{ $tr->title }}</p>
                                <p class="text-xs text-slate-500">{{ $tr->user->name }} · {{ $tr->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($tr->training_type === 'online')
                            <span class="inline-flex items-center gap-1 rounded-full bg-gold-100 px-2.5 py-0.5 text-xs font-semibold text-gold-700">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                Online
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                In-House
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $tr->estimated_attendees ? number_format($tr->estimated_attendees) : '—' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-600">{{ $tr->preferred_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = ['pending' => 'bg-amber-100 text-amber-700', 'approved' => 'bg-emerald-100 text-emerald-700', 'rejected' => 'bg-red-100 text-red-700', 'completed' => 'bg-slate-100 text-slate-600'];
                            $colorClass = $statusColors[$tr->status] ?? $statusColors['pending'];
                        @endphp
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $colorClass }}">{{ ucfirst($tr->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if($tr->status === 'pending' && $tr->user_id === Auth::id())
                            <a href="{{ route('training.edit', $tr->slug) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                Edit
                            </a>
                            @endif
                            <a href="{{ route('training.show', $tr->slug) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                View
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="text-center py-16">
            <svg class="h-12 w-12 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            <h3 class="text-lg font-semibold text-slate-500 mb-1">No training requests</h3>
            <p class="text-sm text-slate-400 mb-6">No training requests have been submitted yet.</p>
            <button @@click="trainingModal = true" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Submit Your First Request
            </button>
        </div>
    @endif
</div>
@endsection