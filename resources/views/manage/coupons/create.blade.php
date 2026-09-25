@extends('layouts.dashboard')

@section('pageTitle', 'Create Coupon')

@section('content')
<div class="mb-6">
    <a href="{{ route('manage.coupons.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Back to Coupons</a>
    <h1 class="text-2xl font-extrabold text-slate-900 mt-2">Create Coupon</h1>
    <p class="text-sm text-slate-500 mt-1">Create a discount coupon for events.</p>
</div>

<form action="{{ route('manage.coupons.store') }}" method="POST" class="max-w-2xl" x-data="{
    generatedCode: '{{ old('code') }}',
    generateCode() {
        const prefixes = ['WELCOME', 'SAVE', 'DEAL', 'OFFER', 'VIP', 'EARLY', 'FLASH', 'BONUS', 'EXTRA', 'SPECIAL'];
        const suffix = Math.random().toString(36).substring(2, 6).toUpperCase();
        this.generatedCode = prefixes[Math.floor(Math.random() * prefixes.length)] + suffix;
        $refs.codeInput.value = this.generatedCode;
    }
}">
    @csrf
    @if($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm space-y-6">
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Coupon Code <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <input x-ref="codeInput" name="code" type="text" required value="{{ old('code') }}" placeholder="e.g. SUMMER2026" maxlength="50"
                        class="block flex-1 min-w-0 rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm uppercase">
                    <button type="button" @@click="generateCode()"
                        class="shrink-0 rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:border-gold-400 transition-all cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </button>
                </div>
                <p class="text-xs text-slate-400 mt-1">Enter manually or click generate.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Event (Optional)</label>
                <select name="event_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                    <option value="">All Events (Global)</option>
                    @foreach($events as $ev)
                    <option value="{{ $ev->id }}" {{ old('event_id') == $ev->id ? 'selected' : '' }}>{{ $ev->name }} ({{ $ev->start_date?->format('M j, Y') ?: 'TBD' }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Discount Type <span class="text-red-500">*</span></label>
                <select name="type" id="discount-type" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                    <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="amount" {{ old('type') === 'amount' ? 'selected' : '' }}>Fixed Amount ($)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Discount Value <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold" id="value-prefix">%</span>
                    <input name="value" type="number" step="0.01" required value="{{ old('value') }}" placeholder="10" min="0.01" max="100"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pl-8 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Description (Internal)</label>
            <textarea name="description" rows="2" placeholder="e.g. Early bird discount for Q2 events" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">{{ old('description') }}</textarea>
        </div>

        <div class="grid gap-6 sm:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Min Order Amount</label>
                <input name="min_order_amount" type="number" step="0.01" value="{{ old('min_order_amount') }}" placeholder="0.00" min="0"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Max Uses (Total)</label>
                <input name="max_uses" type="number" value="{{ old('max_uses') }}" placeholder="Unlimited" min="1"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Max Per User</label>
                    <input name="max_uses_per_user" type="number" value="{{ old('max_uses_per_user') }}" placeholder="Unlimited" min="1"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
                <input name="starts_at" type="datetime-local" value="{{ old('starts_at') }}"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Expiry Date</label>
                <input name="expires_at" type="datetime-local" value="{{ old('expires_at') }}"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
            </div>
        </div>

        <div class="flex items-center gap-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                class="h-4 w-4 rounded border-slate-300 text-gold-500 focus:ring-gold-500">
            <label for="is_active" class="text-sm font-medium text-slate-700">Active on creation</label>
        </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">Create Coupon</button>
        <a href="{{ route('manage.coupons.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
    </div>
</form>
@endsection
