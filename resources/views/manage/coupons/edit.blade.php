@extends('layouts.dashboard')

@section('pageTitle', 'Edit Coupon')

@section('content')
<div class="mb-6">
    <a href="{{ route('manage.coupons.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Back to Coupons</a>
    <h1 class="text-2xl font-extrabold text-slate-900 mt-2">Edit Coupon</h1>
    <p class="text-sm text-slate-500 mt-1">Update discount coupon details.</p>
</div>

<form action="{{ route('manage.coupons.update', $coupon->id) }}" method="POST" class="max-w-2xl" x-data="{
    generateCode() {
        const prefixes = ['WELCOME', 'SAVE', 'DEAL', 'OFFER', 'VIP', 'EARLY', 'FLASH', 'BONUS', 'EXTRA', 'SPECIAL'];
        const suffix = Math.random().toString(36).substring(2, 6).toUpperCase();
        $refs.codeInput.value = prefixes[Math.floor(Math.random() * prefixes.length)] + suffix;
    }
}">
    @csrf
    @method('PUT')
    @if($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm space-y-6">
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Coupon Code <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <input x-ref="codeInput" name="code" type="text" required value="{{ old('code', $coupon->code) }}" maxlength="50"
                        class="block flex-1 min-w-0 rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm uppercase">
                    <button type="button" @@click="generateCode()"
                        class="shrink-0 rounded-xl border border-slate-300 bg-white px-4 py-3 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:border-gold-400 transition-all cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Event (Optional)</label>
                <select name="event_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                    <option value="">All Events (Global)</option>
                    @foreach($events as $ev)
                    <option value="{{ $ev->id }}" {{ old('event_id', $coupon->event_id) == $ev->id ? 'selected' : '' }}>{{ $ev->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Discount Type <span class="text-red-500">*</span></label>
                <select name="type" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                    <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="amount" {{ old('type', $coupon->type) === 'amount' ? 'selected' : '' }}>Fixed Amount ($)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Discount Value <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold">%</span>
                    <input name="value" type="number" step="0.01" required value="{{ old('value', $coupon->value) }}" min="0.01" max="{{ $coupon->type === 'percentage' ? '100' : '999999' }}"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 pl-8 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Description (Internal)</label>
            <textarea name="description" rows="2" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">{{ old('description', $coupon->description) }}</textarea>
        </div>

        <div class="grid gap-6 sm:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Min Order Amount</label>
                <input name="min_order_amount" type="number" step="0.01" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" min="0"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Max Uses (Total)</label>
                <input name="max_uses" type="number" value="{{ old('max_uses', $coupon->max_uses) }}" min="1"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Max Per User</label>
                <input name="max_uses_per_user" type="number" value="{{ old('max_uses_per_user', $coupon->max_uses_per_user) }}" placeholder="Unlimited" min="1"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
                <input name="starts_at" type="datetime-local" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Expiry Date</label>
                <input name="expires_at" type="datetime-local" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}"
                    class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
            </div>
        </div>

        <div class="flex items-center gap-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                class="h-4 w-4 rounded border-slate-300 text-gold-500 focus:ring-gold-500">
            <label for="is_active" class="text-sm font-medium text-slate-700">Active</label>
        </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">Update Coupon</button>
        <a href="{{ route('manage.coupons.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
    </div>
</form>
@endsection
