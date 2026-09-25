@extends('layouts.dashboard')

@section('pageTitle', ($ticket->ticketType?->name ?? 'Ticket') . ' — ' . $ticket->event->name)

@section('content')
@php
    $displayName = $ticket->attendee?->participant_name ?? $ticket->user->name;
    $displayEmail = $ticket->attendee?->participant_email ?? $ticket->user->email;
    $cur = $ticket->ticketType?->currency ?? 'USD';
    $sym = ['USD' => '$', 'KES' => 'KSh', 'EUR' => '€', 'GBP' => '£'][$cur] ?? '$';
    $isCheckedIn = !is_null($ticket->checked_in_at);
@endphp

<div class="py-6 max-w-md mx-auto">
    <div class="mb-6 no-print">
        <a href="{{ route('dashboard') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Back to Dashboard</a>
    </div>

    {{-- Receipt --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-lg overflow-hidden">

        {{-- Header --}}
        <div class="text-center px-6 pt-8 pb-6 border-b-2 border-dashed border-slate-200">
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Digital Receipt</p>
            <h1 class="text-lg font-extrabold text-slate-900 leading-tight">{{ $ticket->event->name }}</h1>
            <p class="mt-1.5 inline-block rounded-full bg-gold-50 border border-gold-200 px-3 py-0.5 text-xs font-bold text-gold-700">{{ $ticket->ticketType?->name ?? 'Standard' }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $ticket->event->start_date->format('D, M d, Y') }}</p>
            @if($ticket->event->venue)
                <p class="text-xs text-slate-500">{{ $ticket->event->venue->name }}</p>
            @endif
        </div>

        {{-- QR Code --}}
        <div class="flex flex-col items-center py-6 px-6 border-b-2 border-dashed border-slate-200">
            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-4">Scan to Validate</p>
            <div class="bg-white border-2 border-slate-300 p-3 rounded-xl shadow-inner">
                <div class="w-52 h-52 flex items-center justify-center">
                    <img src="{{ (new \chillerlan\QRCode\QRCode)->render($ticket->qr_code) }}" alt="QR Code" class="w-full h-full">
                </div>
            </div>
            <p class="mt-3 text-xs font-mono font-bold text-slate-700 tracking-wider select-all">{{ $ticket->qr_code }}</p>
        </div>

        {{-- Attendee --}}
        <div class="px-6 pt-5 pb-4 border-b border-dashed border-slate-200">
            <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-3">Attendee</p>
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-900 text-white text-sm font-bold">
                    {{ strtoupper(substr($displayName, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">{{ $displayName }}</p>
                    <p class="text-xs text-slate-500">{{ $displayEmail }}</p>
                </div>
            </div>
        </div>

        @if($ticket->user_id === auth()->id() && !$isCheckedIn && $ticket->status !== 'cancelled')
        <form method="POST" action="{{ route('tickets.attendee.update', $ticket) }}" class="px-6 py-5 border-b border-dashed border-slate-200 bg-slate-50/50 space-y-3 no-print">
            @csrf
            @method('PUT')
            <div>
                <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400">Assign attendee</p>
                @if($ticket->order?->reservation_type === 'company' && !$ticket->attendee?->participant_name)
                    <p class="mt-1 text-xs text-blue-700">This place was reserved by {{ $ticket->order->company_name }}@if($ticket->order->contact_person) (contact: {{ $ticket->order->contact_person }})@endif. Add the attendee when ready.</p>
                @endif
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600" for="participant_name">Name</label>
                <input id="participant_name" name="participant_name" required maxlength="255" value="{{ old('participant_name', $ticket->attendee?->participant_name) }}" class="block w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-gold-500 focus:ring-gold-500">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600" for="participant_email">Email</label>
                <input id="participant_email" name="participant_email" type="email" required maxlength="255" value="{{ old('participant_email', $ticket->attendee?->participant_email) }}" class="block w-full rounded-lg border-slate-300 px-3 py-2 text-sm focus:border-gold-500 focus:ring-gold-500">
            </div>
            <button type="submit" class="rounded-lg bg-gold-500 px-3 py-2 text-xs font-bold text-white hover:bg-gold-600">Save attendee</button>
        </form>
        @endif

        {{-- Details --}}
        <div class="px-6 py-4 space-y-3">
            @if($ticket->order)
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500">Price</span>
                <span class="font-bold text-slate-900">
                    @if((float)$ticket->order->total_amount > 0)
                        {{ $sym }}{{ number_format((float)$ticket->ticketType?->price ?? 0, 2) }}
                    @else
                        <span class="text-emerald-600">Free</span>
                    @endif
                </span>
            </div>
            @endif
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500">Order #</span>
                <span class="font-mono font-bold text-slate-900">{{ $ticket->order?->invoice?->invoice_number ?? sprintf('#%04d', $ticket->order_id) }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500">Purchased</span>
                <span class="font-medium text-slate-700">{{ $ticket->created_at->format('M d, Y') }}</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500">Status</span>
                <span class="inline-flex items-center gap-1 font-bold
                    {{ $ticket->status === 'active' ? 'text-emerald-700' : '' }}
                    {{ $ticket->status === 'used' ? 'text-amber-700' : '' }}
                    {{ $ticket->status === 'cancelled' ? 'text-red-600' : '' }}">
                    <span class="h-1.5 w-1.5 rounded-full
                        {{ $ticket->status === 'active' ? 'bg-emerald-500' : '' }}
                        {{ $ticket->status === 'used' ? 'bg-amber-500' : '' }}
                        {{ $ticket->status === 'cancelled' ? 'bg-red-500' : '' }}"></span>
                    {{ ucfirst($ticket->status) }}
                </span>
            </div>
            @if($isCheckedIn)
            <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500">Checked In</span>
                <span class="font-medium text-emerald-700">{{ $ticket->checked_in_at->format('M d, Y H:i') }}</span>
            </div>
            @endif
        </div>

        {{-- Perforation --}}
        <div class="relative h-8 flex items-center justify-center">
            <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 border-t-2 border-dashed border-slate-300"></div>
            <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-100 border border-slate-300"></div>
            <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-slate-100 border border-slate-300"></div>
        </div>

        {{-- Footer actions --}}
        <div class="px-6 py-4 bg-slate-50 no-print flex items-center justify-between gap-3">
            <span class="text-[10px] text-slate-400">Keep this receipt for your records</span>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-all shadow-sm cursor-pointer">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Print
                </button>
            </div>
        </div>
    </div>

    {{-- Instructions --}}
    <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm no-print">
        <div class="flex items-start gap-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900">How to use this ticket</p>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">Present the QR code at the event entrance. Staff will scan it to verify your pass. You can also show the ticket code manually if needed.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    @media print {
        @page { margin: 0; size: 80mm auto; }
        body { background: white !important; font-size: 10px; color: black !important; }
        nav, footer, .no-print, a, button, svg, img[alt="Logo"] {
            display: none !important;
        }
        * { box-shadow: none !important; text-shadow: none !important; background: white !important; border-radius: 0 !important; }
        .max-w-md { max-width: 100% !important; margin: 0 !important; }
        .rounded-2xl, .rounded-xl, .rounded-lg, .rounded-full { border-radius: 0 !important; }
        .py-6 { padding: 0 !important; margin: 0 !important; }
        .text-slate-400, .text-slate-500, .text-slate-300 { color: #555 !important; }
        .text-slate-900, .text-slate-700 { color: black !important; }
        .text-emerald-700, .text-emerald-600 { color: black !important; }
        .bg-slate-50, .bg-slate-100 { background: white !important; }
        .border { border-color: #000 !important; }
        .shadow-lg { box-shadow: none !important; }
        h1 { font-size: 14px !important; }
        .w-52 { width: 160px !important; }
        .h-52 { height: 160px !important; }
        .border-dashed { border-style: dashed !important; }
        .hidden { display: block !important; }
    }
</style>
@endpush
