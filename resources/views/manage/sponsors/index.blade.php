@extends('layouts.dashboard')

@section('pageTitle', 'Manage Sponsors')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div><h1 class="text-2xl font-extrabold text-slate-900">Manage Sponsors</h1><p class="text-sm text-slate-500 mt-1">{{ $sponsors->count() }} sponsors.</p></div>
    <a href="{{ route('manage.sponsors.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Add Sponsor</a>
</div>

@if(session('success'))<div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50"><tr>
                <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 w-12">Logo</th>
                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Level</th>
                <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Website</th>
                <th class="px-3 py-3.5 text-right pr-6 text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($sponsors as $s)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-4 pl-6 pr-3">
                        @if($s->image_url)<img src="{{ $s->image_url }}" alt="{{ $s->name }}" class="h-10 w-10 rounded-lg object-cover bg-slate-100">@else<div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>@endif
                    </td>
                    <td class="pr-3"><p class="text-sm font-semibold text-slate-900">{{ $s->name }}</p></td>
                    <td class="px-3 py-4"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-gold-100 text-gold-700">{{ ucfirst($s->level) }}</span></td>
                    <td class="px-3 py-4 text-sm text-slate-500">@if($s->website)<a href="{{ $s->website }}" target="_blank" class="text-gold-600 hover:text-gold-700">{{ $s->website }}</a>@else — @endif</td>
                    <td class="px-3 py-4 text-right pr-6">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('manage.sponsors.edit', $s->id) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors">Edit</a>
                            <form action="{{ route('manage.sponsors.destroy', $s->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete sponsor?');">@csrf @method('DELETE')<button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-12 text-center text-sm text-slate-500">No sponsors found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
