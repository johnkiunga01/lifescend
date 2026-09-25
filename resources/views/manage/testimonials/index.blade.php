@extends('layouts.dashboard')

@section('pageTitle', 'Manage Testimonials')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Manage Testimonials</h1>
    <p class="text-sm text-slate-500 mt-1">{{ $testimonials->count() }} testimonials.</p>
</div>

@if(session('success'))<div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50"><tr>
                <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Customer</th>
                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Testimonial</th>
                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Rating</th>
                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                <th class="px-3 py-3.5 text-right pr-6 text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($testimonials as $t)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-4 pl-6 pr-3">
                        <p class="text-sm font-semibold text-slate-900">{{ $t->user->name }}</p>
                        <p class="text-xs text-slate-500">{{ $t->user->email }}</p>
                    </td>
                    <td class="px-3 py-4 text-sm text-slate-600 max-w-xs">
                        <p class="line-clamp-2">{{ $t->content }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ $t->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="px-3 py-4">
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="h-4 w-4 {{ $i <= $t->rating ? 'text-gold-500' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </td>
                    <td class="px-3 py-4"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $t->is_approved ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $t->is_approved ? 'Approved' : 'Pending' }}</span></td>
                    <td class="px-3 py-4 text-right pr-6">
                        <div class="flex items-center justify-end gap-2">
                            @if(!$t->is_approved)
                            <form action="{{ route('manage.testimonials.approve', $t->id) }}" method="POST" class="inline">@csrf<button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-emerald-600 hover:bg-emerald-50 transition-colors cursor-pointer">Approve</button></form>
                            @else
                            <form action="{{ route('manage.testimonials.reject', $t->id) }}" method="POST" class="inline">@csrf<button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-amber-600 hover:bg-amber-50 transition-colors cursor-pointer">Reject</button></form>
                            @endif
                            <form action="{{ route('manage.testimonials.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete testimonial?');">@csrf @method('DELETE')<button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-12 text-center text-sm text-slate-500">No testimonials yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection