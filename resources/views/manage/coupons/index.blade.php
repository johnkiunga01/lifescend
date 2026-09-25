@extends('layouts.dashboard')

@section('pageTitle', 'Coupons')

@section('content')
<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Coupons</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $coupons->count() }} coupon{{ $coupons->count() !== 1 ? 's' : '' }}.</p>
        </div>
        <a href="{{ route('manage.coupons.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Add Coupon</a>
    </div>

    @if(session('success'))<div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
    @if($errors->any() && !session('success'))<div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Code</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Discount</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Event</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Usage</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Valid</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-3 py-3.5 text-right pr-6 text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($coupons as $c)
                    @php
                        $now = now();
                        $expired = $c->expires_at && $now->gt($c->expires_at);
                        $notStarted = $c->starts_at && $now->lt($c->starts_at);
                        $exhausted = $c->max_uses && $c->usages_count >= $c->max_uses;
                        $isUsable = $c->is_active && !$expired && !$exhausted && !$notStarted;
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 pl-6 pr-3">
                            <p class="text-sm font-bold font-mono text-slate-900">{{ $c->code }}</p>
                            @if($c->description)<p class="text-xs text-slate-500 mt-0.5 max-w-[200px] truncate">{{ $c->description }}</p>@endif
                        </td>
                        <td class="px-3 py-4">
                            <span class="text-sm font-semibold text-slate-900">
                                @if($c->type === 'percentage'){{ $c->value }}% @else ${{ number_format($c->value, 2) }} @endif
                            </span>
                            @if($c->min_order_amount)<p class="text-[10px] text-slate-400 mt-0.5">Min: ${{ number_format($c->min_order_amount, 2) }}</p>@endif
                        </td>
                        <td class="px-3 py-4 text-sm text-slate-600">{{ $c->event?->name ?? 'All Events' }}</td>
                        <td class="px-3 py-4 text-sm text-slate-600">{{ $c->usages_count }}{{ $c->max_uses ? ' / ' . $c->max_uses : '' }}</td>
                        <td class="px-3 py-4 text-sm text-slate-600">
                            @if($notStarted)
                                <span class="text-xs text-amber-600">From {{ $c->starts_at->format('M j') }}</span>
                            @elseif($expired)
                                <span class="text-xs text-red-600">Expired {{ $c->expires_at->diffForHumans() }}</span>
                            @elseif($c->expires_at)
                                <span class="text-xs text-emerald-600">{{ $c->expires_at->diffForHumans() }} left</span>
                            @else
                                <span class="text-xs text-slate-400">No expiry</span>
                            @endif
                        </td>
                        <td class="px-3 py-4">
                            @if(!$c->is_active)
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500">Inactive</span>
                            @elseif($expired)
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-600">Expired</span>
                            @elseif($exhausted)
                                <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-600">Exhausted</span>
                            @elseif($notStarted)
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-600">Scheduled</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-600">Active</span>
                            @endif
                        </td>
                        <td class="px-3 py-4 text-right pr-6">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('manage.coupons.edit', $c->id) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors">Edit</a>
                                <form action="{{ route('manage.coupons.destroy', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this coupon?');">@csrf @method('DELETE')<button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-sm text-slate-500">No coupons yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
