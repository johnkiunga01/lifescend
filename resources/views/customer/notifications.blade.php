@extends('layouts.dashboard')

@section('pageTitle', 'Notifications')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Notifications</h1>
    <p class="text-sm text-slate-500 mt-1">Activity and status updates related to your account.</p>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    @if($logs->isNotEmpty())
        <div class="divide-y divide-slate-100">
            @foreach($logs as $log)
            <div class="p-5 hover:bg-slate-50 transition-colors flex items-start gap-4">
                @php
                    $iconMap = [
                        'training_requested' => ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'bg' => 'bg-gold-100 text-gold-600'],
                        'training_updated' => ['icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z', 'bg' => 'bg-blue-100 text-blue-600'],
                        'training_request_accepted' => ['icon' => 'M5 13l4 4L19 7', 'bg' => 'bg-emerald-100 text-emerald-600'],
                        'training_request_scheduled' => ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'bg' => 'bg-gold-100 text-gold-600'],
                        'order_created' => ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'bg' => 'bg-blue-100 text-blue-600'],
                    ];
                    $action = $iconMap[$log->action] ?? ['icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-slate-100 text-slate-600'];
                @endphp
                <div class="h-9 w-9 rounded-lg {{ $action['bg'] }} flex items-center justify-center shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $action['icon'] }}" /></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900">{{ ucwords(str_replace('_', ' ', $log->action)) }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if($log->event)
                            Related to <strong>{{ $log->event->name }}</strong>
                        @elseif(isset($log->metadata['title']))
                            Related to <strong>{{ $log->metadata['title'] }}</strong>
                        @endif
                    </p>
                    <p class="text-xs text-slate-400 mt-1">{{ $log->created_at->format('M d, Y \a\t g:i A') }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-5 py-4 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <svg class="h-12 w-12 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
            <h3 class="text-lg font-semibold text-slate-500 mb-1">No notifications yet</h3>
            <p class="text-sm text-slate-400">When there are updates about your activities, they will appear here.</p>
        </div>
    @endif
</div>
@endsection