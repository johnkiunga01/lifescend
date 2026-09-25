@extends('layouts.dashboard')

@section('pageTitle', 'Payments')

@section('content')
<div x-data="{ typeModal: false, editType: { id: null, name: '', description: '', is_active: true }, openEdit(t) { this.editType = { ...t }; this.typeModal = true; }, orderModal: false, orderId: null, orderCustomer: '', paymentMethod: '', openOrder(id, customer, method) { this.orderId = id; this.orderCustomer = customer; this.paymentMethod = method; this.orderModal = true; } }">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Payment History</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $stats['total'] }} orders · {{ $types->count() }} payment methods</p>
        </div>
        <button @@click="typeModal = true; editType = { id: null, name: '', description: '', is_active: true }" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-bold text-white hover:bg-slate-800 transition-all cursor-pointer"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>Payment Methods</button>
    </div>

    @if(session('success'))<div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
    @if($errors->any() && !session('success'))<div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    {{-- Stats --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 mb-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Revenue</p>
            <p class="text-xl font-extrabold text-slate-900 mt-1">${{ number_format($stats['revenue'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Today</p>
            <p class="text-xl font-extrabold text-emerald-600 mt-1">${{ number_format($stats['today'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Completed</p>
            <p class="text-xl font-extrabold text-emerald-600 mt-1">{{ $stats['completed'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Reserved</p>
            <p class="text-xl font-extrabold text-blue-600 mt-1">{{ $stats['reserved'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pending</p>
            <p class="text-xl font-extrabold text-amber-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Failed</p>
            <p class="text-xl font-extrabold text-red-600 mt-1">{{ $stats['failed'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Orders</p>
            <p class="text-xl font-extrabold text-slate-900 mt-1">{{ $stats['total'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 flex flex-wrap items-center gap-3">
        <form method="GET" class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative flex-1 min-w-[200px] max-w-md">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input name="search" type="text" placeholder="Search by customer, event, ref..." value="{{ request('search') }}" class="block w-full rounded-xl border border-slate-300 bg-white pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none">
            </div>
            <select name="status" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-gold-500 focus:outline-none" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="reserved" {{ request('status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition-all">Search</button>
            @if(request('search') || request('status'))
            <a href="{{ route('manage.payments.index') }}" class="text-sm text-slate-500 hover:text-slate-700 underline">Clear</a>
            @endif
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Order</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Customer</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Event</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Tickets</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Amount</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Method</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                        <th class="px-3 py-3.5 text-right pr-6 text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                    @php
                        $paymentMethod = $order->payment_method
                            ?: (str_starts_with($order->stripe_payment_intent_id ?? '', 'pi_simulated_') ? 'Direct' : 'Stripe');
                        $statusColors = ['completed' => 'bg-emerald-100 text-emerald-700', 'reserved' => 'bg-blue-100 text-blue-700', 'pending' => 'bg-amber-100 text-amber-700', 'failed' => 'bg-red-100 text-red-700'];
                        $origAmount = $order->items->sum('total_price');
                        $hasCoupon = $order->coupon_id && (float)$order->discount_amount > 0;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 pl-6 pr-3">
                            <p class="text-sm font-mono font-bold text-slate-900">{{ $order->invoice?->invoice_number ?? 'ORD-'.$order->id }}</p>
                            <p class="text-[10px] text-slate-400">{{ $order->stripe_payment_intent_id }}</p>
                        </td>
                        <td class="px-3 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-7 w-7 rounded-full bg-gold-100 flex items-center justify-center text-[10px] font-bold text-gold-700 shrink-0">{{ strtoupper(substr($order->user?->name ?? '?', 0, 2)) }}</div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $order->user?->name ?? 'Deleted User' }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ $order->user?->email ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <p class="text-sm font-semibold text-slate-900 truncate max-w-[180px]">{{ $order->event?->name ?? '—' }}</p>
                        </td>
                        <td class="px-3 py-4">
                            <div class="space-y-0.5">
                                @foreach($order->items as $item)
                                <p class="text-xs text-slate-600">{{ $item->ticketType?->name ?? 'Ticket' }} × {{ $item->quantity }}</p>
                                @endforeach
                                <p class="text-[10px] text-slate-400">{{ $order->tickets->count() }} ticket{{ $order->tickets->count() !== 1 ? 's' : '' }}</p>
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <div>
                                @if($hasCoupon)
                                <p class="text-xs text-slate-400 line-through">${{ number_format($origAmount, 2) }}</p>
                                @endif
                                <p class="text-sm font-bold text-slate-900">${{ number_format($order->total_amount, 2) }}</p>
                                @if($hasCoupon)
                                <p class="text-[10px] text-emerald-600">Save ${{ number_format($order->discount_amount, 2) }} ({{ $order->coupon?->code }})</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-3 py-4">
                            <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $paymentMethod === 'Stripe' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $paymentMethod }}
                            </span>
                            @if($order->currency !== 'USD')<span class="ml-1 text-[10px] text-slate-400">{{ $order->currency }}</span>@endif
                        </td>
                        <td class="px-3 py-4">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($order->status) }}</span>
                        </td>
                        <td class="px-3 py-4 text-sm text-slate-500 whitespace-nowrap">{{ $order->created_at->format('M d, Y') }}<br><span class="text-xs text-slate-400">{{ $order->created_at->format('h:i A') }}</span></td>
                        <td class="px-3 py-4 text-right pr-6">
                            @if($order->status === 'reserved' || $order->status === 'pending' || $order->tickets->isEmpty())
                            <button type="button" @@click="openOrder({{ $order->id }}, '{{ addslashes($order->user?->name ?? 'Customer') }}', '{{ addslashes($order->payment_method ?? '') }}')"
                                    class="rounded-lg px-3 py-1.5 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors cursor-pointer whitespace-nowrap">Manage</button>
                            @else
                            <span class="text-xs text-slate-300">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-sm text-slate-500">No payment records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

    {{-- Payment Types Modal --}}
    <div x-show="typeModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="typeModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="typeModal = false" class="fixed inset-0 bg-black/60"></div>
            <div x-show="typeModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" @@click.away="typeModal = false" class="relative w-full max-w-2xl max-h-[80vh] overflow-y-auto rounded-2xl bg-white p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div><h2 class="text-xl font-extrabold text-slate-900">Payment Methods</h2><p class="text-sm text-slate-500 mt-0.5">{{ $types->count() }} configured</p></div>
                    <button @@click="typeModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>

                {{-- Existing types --}}
                <div class="divide-y divide-slate-100">
                    @foreach($types as $t)
                    <div class="flex items-center justify-between py-3 px-2 hover:bg-slate-50 rounded-lg transition-colors">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $t->name }}</p>
                            <p class="text-xs text-slate-500">{{ $t->description ?? $t->slug }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $t->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $t->is_active ? 'Active' : 'Inactive' }}</span>
                            <button @@click="openEdit({id: {{ $t->id }}, name: '{{ $t->name }}', description: '{{ $t->description ?? '' }}', is_active: {{ $t->is_active ? 'true' : 'false' }}})" class="rounded-lg px-2 py-1 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors cursor-pointer">Edit</button>
                            <form action="{{ route('manage.payments.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete?');">@csrf @method('DELETE')<button class="rounded-lg px-2 py-1 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button></form>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Edit inline --}}
                <template x-if="editType.id">
                    <form x-bind:action="`/manage/payments/${editType.id}`" method="POST" class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200">
                        @csrf @method('PUT')
                        <p class="text-xs font-semibold text-amber-700 mb-3">Editing payment method</p>
                        <div class="grid gap-3 sm:grid-cols-3">
                            <input name="name" type="text" required x-model="editType.name" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-gold-500 focus:outline-none">
                            <input name="description" type="text" x-model="editType.description" placeholder="Description" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-gold-500 focus:outline-none">
                            <div class="flex items-center gap-2">
                                <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="is_active" value="1" x-model="editType.is_active" class="h-4 w-4 rounded border-slate-300 text-gold-500 focus:ring-gold-500"><span class="text-xs font-medium text-slate-700">Active</span></label>
                                <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-gold-500 px-3 py-2 text-xs font-bold text-white hover:bg-gold-600 transition-all cursor-pointer">Update</button>
                                <button type="button" @@click="editType.id = null" class="text-xs text-slate-500 hover:text-slate-700 cursor-pointer">Cancel</button>
                            </div>
                        </div>
                    </form>
                </template>
            </div>
        </div>
    </div>

    {{-- Manage Reservation / Payment Modal --}}
    <div x-show="orderModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="orderModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="orderModal = false" class="fixed inset-0 bg-black/60"></div>
            <div x-show="orderModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" @@click.away="orderModal = false" class="relative w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Manage Order</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Order #<span x-text="orderId"></span> · <span x-text="orderCustomer"></span></p>
                    </div>
                    <button @@click="orderModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                        <select x-model="paymentMethod" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-gold-500 focus:outline-none">
                            <option value="">Select a payment method...</option>
                            @foreach($types as $t)
                            <option value="{{ $t->name }}">{{ $t->name }}{{ $t->is_active ? '' : ' (inactive)' }}</option>
                            @endforeach
                        </select>
                        @if($types->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">No payment methods configured yet. Add one via the "Payment Methods" button above.</p>
                        @endif
                    </div>

                    <p class="text-xs text-slate-500">Marking as paid issues the reserved tickets and notifies the customer. Use this once the payment (e.g. cheque) has been received.</p>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-4 border-t border-slate-100">
                        <form x-bind:action="`/manage/payments/orders/${orderId}/reserve`" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" :value="paymentMethod">
                            <button type="submit" :disabled="!paymentMethod" class="w-full sm:w-auto rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">Mark as Reserved</button>
                        </form>
                        <form x-bind:action="`/manage/payments/orders/${orderId}/paid`" method="POST">
                            @csrf
                            <input type="hidden" name="payment_method" :value="paymentMethod">
                            <button type="submit" :disabled="!paymentMethod" class="w-full sm:w-auto rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">Mark as Paid &amp; Issue Tickets</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
