@extends('layouts.dashboard')

@section('pageTitle', 'Manage Event Types')

@section('content')
<div x-data="bulkEditor()">
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Manage Event Types</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $types->count() }} types.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('manage.event-types.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg> Add Type</a>
        </div>
    </div>

    @if(session('success'))<div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>{{ session('success') }}</div>@endif
    @if($errors->any() && !session('success'))<div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
        <ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>@endif

    {{-- Bulk edit bar --}}
    <div x-show="selected.length > 0"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="mb-4 rounded-xl bg-gold-50 border border-gold-200 px-4 sm:px-5 py-3"
         style="display: none;">
        <form action="{{ route('manage.event-types.bulk-update') }}" method="POST">
            @csrf
            <template x-for="id in selected" hidden>
                <input type="hidden" name="ids[]" :value="id">
            </template>

            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm font-semibold text-gold-800 shrink-0" x-text="selected.length + ' selected'"></span>

                {{-- Action radio --}}
                <label class="flex items-center gap-1.5 text-xs sm:text-sm cursor-pointer">
                    <input type="radio" name="action" value="transfer_delete" x-model="bulkAction" class="text-gold-500 focus:ring-gold-500">
                    Transfer &amp; delete
                </label>
                <label class="flex items-center gap-1.5 text-xs sm:text-sm cursor-pointer">
                    <input type="radio" name="action" value="delete" x-model="bulkAction" class="text-gold-500 focus:ring-gold-500">
                    Delete
                </label>
            </div>

            {{-- Transfer select --}}
            <div x-show="bulkAction === 'transfer_delete'" class="mt-3 flex flex-wrap items-center gap-2">
                <select name="transfer_to" required class="rounded-lg border border-gold-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-gold-500 focus:ring-2 focus:ring-gold-500/20 outline-none">
                    <option value="" disabled selected>Transfer events to…</option>
                    @foreach($types as $other)
                    <option value="{{ $other->id }}">{{ $other->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-xs font-bold text-white hover:bg-amber-600 transition-all cursor-pointer" @@click="return confirm('Transfer events and delete selected type(s)? This cannot be undone.')">Transfer &amp; Delete</button>
            </div>

            {{-- Delete --}}
            <div x-show="bulkAction === 'delete'" class="mt-3 flex flex-wrap items-center gap-2" style="display: none;">
                <span class="text-xs text-slate-600">Types with events will be skipped.</span>
                <button type="submit" class="rounded-lg bg-red-500 px-4 py-2 text-xs font-bold text-white hover:bg-red-600 transition-all cursor-pointer" @@click="return confirm('Delete selected event type(s)? Types linked to events will be skipped.')">Delete</button>
            </div>

            <button type="button" @@click="clearAll()" class="mt-3 sm:mt-0 sm:ml-2 text-xs font-semibold text-slate-500 hover:text-slate-700 transition-colors cursor-pointer">Cancel</button>
        </form>
    </div>

    {{-- Desktop table --}}
    <div class="hidden sm:block rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3.5 pl-6 pr-2 text-left w-10">
                            <input type="checkbox" @@click="toggleAll($event.target.checked)"
                                   class="rounded border-slate-300 text-gold-500 focus:ring-gold-500 cursor-pointer"
                                   :checked="selected.length === {{ $types->count() }}">
                        </th>
                        <th class="py-3.5 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 w-12">Image</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Slug</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Mode</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Events</th>
                        <th class="px-3 py-3.5 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">System</th>
                        <th class="px-3 py-3.5 text-right pr-6 text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($types as $t)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 pl-6 pr-2">
                            @if(!$t->is_system)
                            <input type="checkbox" value="{{ $t->id }}" x-model="selected"
                                   class="rounded border-slate-300 text-gold-500 focus:ring-gold-500 cursor-pointer">
                            @endif
                        </td>
                        <td class="py-4 pr-3">
                            @if($t->image_url)<img src="{{ $t->image_url }}" alt="{{ $t->name }}" class="h-10 w-10 rounded-lg object-cover bg-slate-100">@else<div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>@endif
                        </td>
                        <td class="pr-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $t->name }}</p>
                        </td>
                        <td class="px-3 py-4 text-sm text-slate-500">{{ $t->slug }}</td>
                        <td class="px-3 py-4"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $t->mode === 'online' ? 'bg-blue-100 text-blue-700' : ($t->mode === 'hybrid' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700') }}">{{ ucfirst($t->mode) }}</span></td>
                        <td class="px-3 py-4 text-sm text-slate-500">{{ $t->events_count }}</td>
                        <td class="px-3 py-4 text-center">
                            @if($t->is_system)<span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500">System</span>@endif
                        </td>
                        <td class="px-3 py-4 text-right pr-6">
                            @if($t->is_system)
                                <span class="text-xs text-slate-400 italic">Protected</span>
                            @else
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('manage.event-types.edit', $t->id) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors">Edit</a>
                                <form action="{{ route('manage.event-types.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete event type?');">@csrf @method('DELETE')<button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button></form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-sm text-slate-500">No event types found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile cards --}}
    <div class="sm:hidden space-y-3">
        @forelse($types as $t)
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 p-4">
                @if(!$t->is_system)
                <input type="checkbox" value="{{ $t->id }}" x-model="selected"
                       class="rounded border-slate-300 text-gold-500 focus:ring-gold-500 cursor-pointer shrink-0">
                @endif
                <div class="w-10 shrink-0">
                    @if($t->image_url)<img src="{{ $t->image_url }}" alt="{{ $t->name }}" class="w-10 h-10 rounded-lg object-cover bg-slate-100">@else<div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>@endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                        <span class="text-sm font-semibold text-slate-900 truncate">{{ $t->name }}</span>
                        @if($t->is_system)<span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">System</span>@endif
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[11px] text-slate-500">{{ $t->slug }}</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $t->mode === 'online' ? 'bg-blue-100 text-blue-700' : ($t->mode === 'hybrid' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-700') }}">{{ ucfirst($t->mode) }}</span>
                        <span class="text-[11px] text-slate-400">{{ $t->events_count }} events</span>
                    </div>
                </div>
                @if(!$t->is_system)
                <div class="flex items-center gap-1 shrink-0">
                    <a href="{{ route('manage.event-types.edit', $t->id) }}" class="rounded-lg px-2 py-1.5 text-[11px] font-semibold text-gold-600 hover:bg-gold-50 transition-colors">Edit</a>
                    <form action="{{ route('manage.event-types.destroy', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete event type?');">@csrf @method('DELETE')<button type="submit" class="rounded-lg px-2 py-1.5 text-[11px] font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button></form>
                </div>
                @else
                <span class="text-[11px] text-slate-400 italic shrink-0">Protected</span>
                @endif
            </div>
        </div>
        @empty
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="py-12 text-center text-sm text-slate-500">No event types found.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
function bulkEditor() {
    return {
        selected: [],
        bulkAction: 'transfer_delete',
        toggleAll(checked) {
            if (checked) {
                this.selected = {{ $types->where('is_system', false)->pluck('id') }};
            } else {
                this.selected = [];
            }
        },
        clearAll() {
            this.selected = [];
            this.bulkAction = 'transfer_delete';
        }
    }
}
</script>
@endpush