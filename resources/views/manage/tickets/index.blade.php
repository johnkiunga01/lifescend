@extends('layouts.dashboard')

@section('pageTitle', 'Tickets')

@section('content')
@php $hasFilters = request()->hasAny(['search', 'status', 'event_id', 'checked_in', 'sort']); @endphp

<div x-data="{ modal: false, ticket: {}, complete: false, order: {}, openComplete(o) { this.order = { ...o }; this.complete = true; }, viewOpen: false, view: {}, openView(o) { this.view = { ...o }; this.viewOpen = true; }, payFromView() { this.order = { ...this.view }; this.viewOpen = false; this.complete = true; } }">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">{{ $view === 'reserved' ? 'Reserved Orders' : 'Tickets' }}</h1>
            <p class="text-sm text-slate-500 mt-1">
                @if($view === 'reserved')
                    {{ $reservedOrders->count() }} reservation{{ $reservedOrders->count() !== 1 ? 's' : '' }} awaiting payment
                @else
                    {{ $tickets->total() }} tickets{{ $hasFilters ? ' matching filters' : '' }}
                @endif
            </p>
        </div>
        @if($view === 'tickets')
        <div class="flex items-center gap-2">
            <select name="sort" onchange="window.location.href=this.value" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 focus:border-gold-500 focus:outline-none">
                <option value="{{ route('manage.tickets.index', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Sort: Newest</option>
                <option value="{{ route('manage.tickets.index', array_merge(request()->except('sort'), ['sort' => 'oldest'])) }}" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Sort: Oldest</option>
                <option value="{{ route('manage.tickets.index', array_merge(request()->except('sort'), ['sort' => 'user'])) }}" {{ request('sort') === 'user' ? 'selected' : '' }}>Sort: By User</option>
                <option value="{{ route('manage.tickets.index', array_merge(request()->except('sort'), ['sort' => 'event'])) }}" {{ request('sort') === 'event' ? 'selected' : '' }}>Sort: By Event</option>
            </select>
            @if($hasFilters)
            <a href="{{ route('manage.tickets.index') }}" class="text-xs text-red-500 hover:text-red-700 font-medium ml-2">Clear</a>
            @endif
        </div>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="mb-6 inline-flex rounded-xl border border-slate-200 bg-white p-1 shadow-sm">
        <a href="{{ route('manage.tickets.index') }}"
           class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ $view === 'tickets' ? 'bg-gold-500 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
            Tickets
        </a>
        <a href="{{ route('manage.tickets.index', ['view' => 'reserved']) }}"
           class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition-colors {{ $view === 'reserved' ? 'bg-gold-500 text-white' : 'text-slate-600 hover:bg-slate-50' }}">
            Reserved
            @if($reservedOrders->count() > 0)
            <span class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] font-bold {{ $view === 'reserved' ? 'bg-white/25 text-white' : 'bg-blue-100 text-blue-700' }}">{{ $reservedOrders->count() }}</span>
            @endif
        </a>
    </div>

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-3 text-sm text-emerald-700">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any() && !session('success'))
    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
        <ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    @if($view === 'reserved')
    {{-- Reserved Orders --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="py-3 pl-4 pr-2 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Order</th>
                        <th class="px-2 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Customer</th>
                        <th class="px-2 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 hidden md:table-cell">Event</th>
                        <th class="px-2 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 hidden sm:table-cell">Places</th>
                        <th class="px-2 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Amount</th>
                        <th class="px-2 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Method</th>
                        <th class="py-3 pl-2 pr-4 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reservedOrders as $order)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3 pl-4 pr-2">
                            <p class="text-xs font-mono font-bold text-slate-700">{{ $order->invoice?->invoice_number ?? 'ORD-'.$order->id }}</p>
                            <p class="text-[10px] text-slate-400">{{ $order->reserved_at?->format('M d, Y') ?? $order->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-2 py-3">
                            <p class="text-sm font-semibold text-slate-900 truncate max-w-[160px]">{{ $order->user?->name ?? 'Deleted User' }}</p>
                            <p class="text-xs text-slate-400 truncate max-w-[160px] hidden sm:block">{{ $order->user?->email ?? '—' }}</p>
                            @if($order->reservation_type === 'company' && $order->company_name)
                            <p class="text-[10px] text-slate-500 truncate max-w-[160px]">Company: {{ $order->company_name }}</p>
                            @if($order->contact_person)<p class="text-[10px] text-slate-500 truncate max-w-[160px]">Contact: {{ $order->contact_person }}</p>@endif
                            @endif
                        </td>
                        <td class="px-2 py-3 hidden md:table-cell max-w-[180px]">
                            <p class="text-sm text-slate-700 truncate">{{ $order->event?->name ?? '—' }}</p>
                        </td>
                        <td class="px-2 py-3 hidden sm:table-cell">
                            <div class="space-y-0.5">
                                @foreach($order->items as $item)
                                <p class="text-xs text-slate-600">{{ $item->ticketType?->name ?? 'Ticket' }} × {{ $item->quantity }}</p>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-2 py-3">
                            <p class="text-sm font-bold text-slate-900">{{ number_format($order->total_amount, 2) }}</p>
                            <p class="text-[10px] text-slate-400">{{ $order->currency }}</p>
                        </td>
                        <td class="px-2 py-3">
                            @if($order->payment_method)
                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">{{ $order->payment_method }}</span>
                            @else
                            <span class="text-xs text-slate-400">Not set</span>
                            @endif
                        </td>
                        <td class="py-3 pl-2 pr-4 text-right">
                            @php
                                $itemsText = $order->items->map(fn ($i) => ($i->ticketType?->name ?? 'Ticket') . ' × ' . $i->quantity)->implode(', ');
                                $orderPayload = [
                                    'id' => $order->id,
                                    'number' => $order->invoice?->invoice_number ?? 'ORD-' . $order->id,
                                    'customer' => $order->user?->name ?? 'Customer',
                                    'email' => $order->user?->email ?? '—',
                                    'event' => $order->event?->name ?? 'Event',
                                    'amount' => number_format($order->total_amount, 2) . ' ' . $order->currency,
                                    'method' => $order->payment_method ?? '',
                                    'items' => $itemsText,
                                    'places' => (int) $order->items->sum('quantity'),
                                    'reservedAt' => ($order->reserved_at ?? $order->created_at)->format('M d, Y H:i'),
                                    'reservationType' => $order->reservation_type,
                                    'companyName' => $order->company_name ?? '',
                                    'contactPerson' => $order->contact_person ?? '',
                                    'companyDetails' => $order->company_details ?? '',
                                    'itemsEmpty' => $order->items->isEmpty(),
                                    'ticketsPaid' => $order->items->sum('quantity') ?: ((int) $order->attendee_count ?: 1),
                                    'amountPaid' => number_format((float) $order->total_amount, 2, '.', ''),
                                    'eventTicketTypes' => $order->event?->ticketTypes->map(fn ($t) => ['id' => $t->id, 'name' => $t->name, 'price' => $t->currency . ' ' . number_format($t->price, 2)])->values() ?? [],
                                    'alloc' => (object) [],
                                ];
                            @endphp
                            <div class="flex items-center justify-end gap-1.5">
                                <button type="button" @@click="openView(@js($orderPayload))"
                                    class="rounded-lg px-3 py-1.5 text-[11px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer">View</button>
                                <button type="button" @@click="openComplete(@js($orderPayload))"
                                    class="rounded-lg px-3 py-1.5 text-[11px] font-bold text-white bg-gold-500 hover:bg-gold-600 transition-colors cursor-pointer">Mark as Paid</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-16 text-center">
                        <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <p class="text-sm text-slate-500">No reserved orders awaiting payment.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else
    {{-- Filters Bar --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm mb-6">
        <form method="GET" action="{{ route('manage.tickets.index') }}" class="p-4 sm:px-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by QR code, name, email, or event..."
                        class="block w-full rounded-xl border border-slate-300 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                </div>
                <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                    <select name="status" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-700 focus:border-gold-500 focus:outline-none">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Used</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <select name="event_id" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-700 focus:border-gold-500 focus:outline-none max-w-[150px]">
                        <option value="">All Events</option>
                        @foreach($events as $e)
                            <option value="{{ $e->id }}" {{ request('event_id') == $e->id ? 'selected' : '' }}>{{ \Str::limit($e->name, 18) }}</option>
                        @endforeach
                    </select>
                    <select name="checked_in" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-xs text-slate-700 focus:border-gold-500 focus:outline-none">
                        <option value="">All Check-In</option>
                        <option value="yes" {{ request('checked_in') === 'yes' ? 'selected' : '' }}>Checked In</option>
                        <option value="no" {{ request('checked_in') === 'no' ? 'selected' : '' }}>Not Checked</option>
                    </select>
                    <button type="submit" class="shrink-0 inline-flex items-center gap-1.5 rounded-xl bg-gold-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Tickets Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="py-3 pl-4 pr-2 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Ticket</th>
                        <th class="px-2 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Holder</th>
                        <th class="px-2 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 hidden md:table-cell">Event</th>
                        <th class="px-2 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 hidden sm:table-cell">Type</th>
                        <th class="px-2 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-2 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500">Date</th>
                        <th class="py-3 pl-2 pr-4 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $t)
                    @php
                        $dName = $t->attendee?->participant_name ?? $t->user?->name ?? '—';
                        $dEmail = $t->attendee?->participant_email ?? $t->user?->email ?? '—';
                        $eventName = $t->event->name ?? '—';
                        $eventDate = $t->event->start_date?->format('M d, Y') ?? '';
                        $venue = $t->event->venue?->name ?? 'Online';
                        $typeName = $t->ticketType?->name ?? '—';
                        $price = (float)($t->ticketType?->price ?? 0);
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="py-3 pl-4 pr-2">
                            <p class="text-xs font-mono font-bold text-slate-700 truncate max-w-[140px]">{{ $t->qr_code }}</p>
                        </td>
                        <td class="px-2 py-3">
                            <p class="text-sm font-semibold text-slate-900 truncate max-w-[160px]">{{ $dName }}</p>
                            <p class="text-xs text-slate-400 truncate max-w-[160px] hidden sm:block">{{ $dEmail }}</p>
                        </td>
                        <td class="px-2 py-3 hidden md:table-cell max-w-[180px]">
                            <p class="text-sm text-slate-700 truncate">{{ $eventName }}</p>
                        </td>
                        <td class="px-2 py-3 hidden sm:table-cell">
                            <span class="text-xs text-slate-600">{{ $typeName }}</span>
                        </td>
                        <td class="px-2 py-3">
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-bold
                                {{ $t->status === 'active' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $t->status === 'used' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                {{ $t->status === 'cancelled' ? 'bg-red-100 text-red-600' : '' }}
                                {{ $t->status === 'pending' ? 'bg-slate-100 text-slate-500' : '' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $t->status === 'active' ? 'bg-emerald-500' : '' }}{{ $t->status === 'used' ? 'bg-indigo-500' : '' }}{{ $t->status === 'cancelled' ? 'bg-red-500' : '' }}{{ $t->status === 'pending' ? 'bg-slate-400' : '' }}"></span>
                                {{ ucfirst($t->status) }}
                            </span>
                            @if($t->checked_in_at)<span class="ml-1.5 text-[10px] text-emerald-600 font-semibold">✓</span>@endif
                        </td>
                        <td class="px-2 py-3 text-right">
                            <span class="text-xs text-slate-500 whitespace-nowrap">{{ $t->created_at->format('M d') }}</span>
                        </td>
                        <td class="py-3 pl-2 pr-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button @@click="ticket = {
                                    qr: '{{ addslashes($t->qr_code) }}',
                                    holder: '{{ addslashes($dName) }}',
                                    email: '{{ addslashes($dEmail) }}',
                                    event: '{{ addslashes($eventName) }}',
                                    eventDate: '{{ addslashes($eventDate) }}',
                                    venue: '{{ addslashes($venue) }}',
                                    type: '{{ addslashes($typeName) }}',
                                    price: {{ $price }},
                                    status: '{{ $t->status }}',
                                    checkedIn: '{{ $t->checked_in_at?->format('M d, Y H:i') ?? '—' }}',
                                    date: '{{ $t->created_at->format('M d, Y') }}',
                                    cur: '{{ $t->ticketType?->currency ?? 'USD' }}'
                                }; modal = true" class="rounded-lg px-2.5 py-1.5 text-[11px] font-bold text-gold-600 hover:bg-gold-50 transition-colors cursor-pointer">View</button>
                                @if($t->status === 'active')
                                <form action="{{ route('manage.tickets.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this ticket?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg px-2.5 py-1.5 text-[11px] font-bold text-red-500 hover:bg-red-50 transition-colors cursor-pointer">Cancel</button>
                                </form>
                                @endif
                                @if($t->status === 'cancelled')
                                <form action="{{ route('manage.tickets.reactivate', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Reactivate this ticket?');">
                                    @csrf
                                    <button type="submit" class="rounded-lg px-2.5 py-1.5 text-[11px] font-bold text-emerald-600 hover:bg-emerald-50 transition-colors cursor-pointer">Reactivate</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-16 text-center">
                        <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                        <p class="text-sm text-slate-500">No tickets found{{ $hasFilters ? ' for the selected filters' : '' }}.</p>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($tickets->hasPages())
        <div class="border-t border-slate-200 px-6 py-3 bg-slate-50">{{ $tickets->links() }}</div>
        @endif
    </div>
    @endif

    {{-- Reservation Details Modal --}}
    <div x-show="viewOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="viewOpen" x-transition @@click="viewOpen = false" class="fixed inset-0 bg-black/60"></div>
            <div x-show="viewOpen" x-transition @@click.away="viewOpen = false" class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Reservation Details</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Order <span class="font-mono font-semibold" x-text="view.number"></span></p>
                    </div>
                    <button @@click="viewOpen = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>

                <div class="flex items-center gap-2 mb-4">
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-bold text-blue-700">Reserved</span>
                    <span class="text-xs text-slate-500" x-text="'Reserved ' + view.reservedAt"></span>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Customer</span>
                        <span class="font-semibold text-slate-900 text-right" x-text="view.customer"></span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Email</span>
                        <span class="text-slate-700 text-right break-all" x-text="view.email"></span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Event</span>
                        <span class="font-medium text-slate-800 text-right" x-text="view.event"></span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Places</span>
                        <span class="text-slate-700 text-right" x-text="view.items + ' (' + view.places + ')'"></span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Amount</span>
                        <span class="font-bold text-slate-900" x-text="view.amount"></span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Mode of Payment</span>
                        <span class="font-semibold text-slate-800" x-text="view.method || 'Not set'"></span>
                    </div>
                </div>

                <div x-show="view.reservationType === 'company'" x-cloak class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm space-y-2">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Company</p>
                    <p><span class="font-semibold text-slate-700">Name:</span> <span class="text-slate-600" x-text="view.companyName"></span></p>
                    <p><span class="font-semibold text-slate-700">Contact person:</span> <span class="text-slate-600" x-text="view.contactPerson"></span></p>
                    <template x-if="view.companyDetails">
                        <p><span class="font-semibold text-slate-700">Details:</span> <span class="text-slate-600 whitespace-pre-wrap" x-text="view.companyDetails"></span></p>
                    </template>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @@click="viewOpen = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Close</button>
                    <button type="button" @@click="payFromView()" class="rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">Mark as Paid</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Complete Reservation Modal --}}
    <div x-show="complete" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="complete" x-transition @@click="complete = false" class="fixed inset-0 bg-black/60"></div>
            <div x-show="complete" x-transition @@click.away="complete = false" class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Mark as Paid</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Order <span class="font-mono font-semibold" x-text="order.number"></span></p>
                    </div>
                    <button @@click="complete = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>

                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-[11px] font-bold text-blue-700">Reserved</span>
                    <span class="text-xs text-slate-500" x-text="'Reserved ' + order.reservedAt"></span>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 mb-5 space-y-2 text-sm">
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Customer</span><span class="font-semibold text-slate-900 text-right" x-text="order.customer"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Email</span><span class="text-slate-700 text-right break-all" x-text="order.email"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Event</span><span class="text-slate-700 text-right" x-text="order.event"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Places</span><span class="text-slate-700 text-right" x-text="order.items + ' (' + order.places + ')'"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Amount</span><span class="font-bold text-slate-900" x-text="order.amount"></span></div>
                    <div class="flex justify-between gap-4"><span class="text-slate-500">Current Mode</span><span class="font-semibold text-slate-800" x-text="order.method || 'Not set'"></span></div>
                    <div x-show="order.reservationType === 'company'" x-cloak class="pt-2 mt-1 border-t border-slate-200 space-y-1">
                        <p><span class="font-semibold text-slate-700">Company:</span> <span class="text-slate-600" x-text="order.companyName"></span></p>
                        <p><span class="font-semibold text-slate-700">Contact person:</span> <span class="text-slate-600" x-text="order.contactPerson"></span></p>
                        <template x-if="order.companyDetails">
                            <p><span class="font-semibold text-slate-700">Details:</span> <span class="text-slate-600 whitespace-pre-wrap" x-text="order.companyDetails"></span></p>
                        </template>
                    </div>
                </div>

                <form x-bind:action="`/manage/tickets/reservations/${order.id}/complete`" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-3 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tickets Paid <span class="text-red-500">*</span></label>
                            <input type="number" name="tickets_paid" min="1" max="1000" required x-model.number="order.ticketsPaid"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-gold-500 focus:outline-none">
                            <p class="text-[10px] text-slate-400 mt-1">Number of tickets to issue.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Amount Paid <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="amount_paid" required x-model="order.amountPaid"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-gold-500 focus:outline-none">
                            <p class="text-[10px] text-slate-400 mt-1">Amount actually received.</p>
                        </div>
                    </div>
                    <div x-show="order.itemsEmpty" x-cloak class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs font-bold text-amber-800 mb-2">No ticket line items found for this reservation. Allocate the tickets to issue:</p>
                        <div class="space-y-2">
                            <template x-for="tt in (order.eventTicketTypes || [])" :key="tt.id">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="text-sm text-slate-700"><span x-text="tt.name"></span> <span class="text-xs text-slate-400" x-text="tt.price"></span></span>
                                    <input type="number" min="0" max="100" x-model.number="order.alloc[tt.id]" :name="'new_tickets[' + tt.id + ']'"
                                        class="w-20 rounded-lg border border-slate-300 px-2 py-1 text-sm">
                                </div>
                            </template>
                            <p x-show="(order.eventTicketTypes || []).length === 0" class="text-xs text-amber-700">No ticket types available for this event.</p>
                        </div>
                    </div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mode of Payment <span class="text-red-500">*</span></label>
                    <select name="payment_method" x-model="order.method" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-gold-500 focus:outline-none">
                        <option value="">Select payment mode...</option>
                        <option value="Stripe">Stripe</option>
                        <option value="Cheque">Cheque</option>
                        @foreach($paymentTypes as $pt)
                        @if(!in_array(strtolower($pt->name), ['stripe', 'cheque'], true))
                        <option value="{{ $pt->name }}">{{ $pt->name }}{{ $pt->is_active ? '' : ' (inactive)' }}</option>
                        @endif
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-3">No online payment is taken here — this only records the mode of payment. Completing generates the order number, issues the tickets and emails the customer.</p>

                    <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @@click="complete = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" :disabled="!order.method" class="rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">Mark as Paid &amp; Issue Tickets</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Ticket Detail Modal --}}
    <div x-show="modal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="modal" x-transition @@click="modal = false" class="fixed inset-0 bg-black/60"></div>
            <div x-show="modal" x-transition @@click.away="modal = false" class="relative w-full max-w-xs rounded-2xl bg-white shadow-2xl overflow-hidden">
                {{-- Receipt header --}}
                <div class="text-center px-6 pt-8 pb-5 border-b-2 border-dashed border-slate-200">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2">Ticket Receipt</p>
                    <h2 class="text-base font-extrabold text-slate-900 leading-tight" x-text="ticket.event"></h2>
                    <p class="text-xs text-slate-500 mt-1" x-text="ticket.eventDate + ' · ' + ticket.venue"></p>
                </div>

                {{-- QR Code --}}
                <div class="flex flex-col items-center py-5 px-6 border-b border-dashed border-slate-200">
                    <p class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-3">Scan to Validate</p>
                    <div class="bg-white border-2 border-slate-300 p-2 rounded-xl">
                        <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(ticket.qr)" alt="QR Code" class="w-44 h-44">
                    </div>
                    <p class="mt-3 text-xs font-mono font-bold text-slate-700 tracking-wider select-all" x-text="ticket.qr"></p>
                </div>

                {{-- Details --}}
                <div class="px-6 pt-4 pb-2 space-y-3">
                    <div><p class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-2">Attendee</p>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-800 text-white text-xs font-bold" x-text="ticket.holder.substring(0, 2).toUpperCase()"></div>
                            <div><p class="text-sm font-bold text-slate-900" x-text="ticket.holder"></p><p class="text-xs text-slate-500" x-text="ticket.email"></p></div>
                        </div>
                    </div>
                    <div class="flex justify-between text-xs"><span class="text-slate-500">Ticket Type</span><span class="font-bold text-slate-900" x-text="ticket.type"></span></div>
                    <div class="flex justify-between text-xs"><span class="text-slate-500">Status</span><span class="font-bold" :class="{'text-emerald-700': ticket.status === 'active', 'text-indigo-700': ticket.status === 'used', 'text-red-600': ticket.status === 'cancelled'}" x-text="ticket.status.charAt(0).toUpperCase() + ticket.status.slice(1)"></span></div>
                    <div class="flex justify-between text-xs"><span class="text-slate-500">Checked In</span><span class="font-semibold text-slate-700" x-text="ticket.checkedIn"></span></div>
                    <div class="flex justify-between text-xs"><span class="text-slate-500">Purchased</span><span class="font-medium text-slate-600" x-text="ticket.date"></span></div>
                </div>

                {{-- Perforation --}}
                <div class="relative h-6 flex items-center justify-center">
                    <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 border-t-2 border-dashed border-slate-300"></div>
                    <div class="absolute -left-3 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-slate-100 border border-slate-300"></div>
                    <div class="absolute -right-3 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-slate-100 border border-slate-300"></div>
                </div>

                <div class="px-6 py-4 flex justify-end gap-2 bg-slate-50">
                    <button @@click="modal = false" class="rounded-lg px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection