@extends('layouts.dashboard')

@section('pageTitle', 'Notifications')

@section('content')
@if(session('success'))
<div class="mb-4 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-3 text-sm text-emerald-700 shadow-sm">
    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Notifications</h1>
        <p class="text-sm text-slate-500 mt-1">{{ $notifications->total() }} total</p>
    </div>
    <form action="{{ route('notifications.markAllRead') }}" method="POST">
        @csrf
        <button type="submit" class="rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">Mark All Read</button>
    </form>
</div>

@if($notifications->isEmpty())
<div class="rounded-2xl border border-slate-200 bg-white p-12 shadow-sm text-center">
    <svg class="h-16 w-16 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
    <h2 class="text-lg font-bold text-slate-900 mb-2">No notifications</h2>
    <p class="text-sm text-slate-500">You're all caught up!</p>
</div>
@else
<div class="space-y-2">
    @foreach($notifications as $n)
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm flex items-start gap-4 {{ $n->read_at ? '' : 'border-gold-300 bg-gold-50/30' }}">
        <div class="mt-1 h-2.5 w-2.5 rounded-full shrink-0 {{ $n->read_at ? 'bg-slate-200' : 'bg-gold-500' }}"></div>
        <div class="flex-1 min-w-0">
            @php $data = $n->data; @endphp
            <p class="text-sm text-slate-800">
                @if(isset($data['action']))
                    {{ $data['description'] ?? $data['label'] }}
                @elseif(isset($data['event_name']))
                    <strong>{{ $data['user_name'] ?? 'Someone' }}</strong> purchased tickets for <strong>{{ $data['event_name'] }}</strong>
                @elseif(isset($data['ticket_code']))
                    <strong>{{ $data['user_name'] ?? 'Someone' }}</strong> checked in
                @elseif(isset($data['support_request_id']))
                    New account support request from <strong>{{ $data['email'] ?? 'Unknown' }}</strong>
                @else
                    Notification
                @endif
            </p>
            <div class="mt-1 flex items-center gap-3 text-xs text-slate-400">
                <span>{{ $n->created_at->diffForHumans() }}</span>
                @if(!$n->read_at)
                <form action="{{ route('notifications.markAsRead', $n->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="font-semibold text-gold-600 hover:text-gold-700 cursor-pointer">Mark read</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-6">
    {{ $notifications->links() }}
</div>
@endif
@endsection
