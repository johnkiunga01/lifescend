@extends('layouts.dashboard')

@section('pageTitle', 'Edit Event Type')

@section('content')
<div class="mb-6">
    <a href="{{ route('manage.event-types.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Back to Event Types</a>
    <h1 class="text-2xl font-extrabold text-slate-900 mt-2">Edit Event Type</h1>
    <p class="text-sm text-slate-500 mt-1">Update event category.</p>
</div>

<form action="{{ route('manage.event-types.update', $type->id) }}" method="POST" enctype="multipart/form-data" class="max-w-lg">
    @csrf
    @method('PUT')
    @if($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm space-y-5">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Type Name <span class="text-red-500">*</span></label>
            <input name="name" type="text" required value="{{ old('name', $type->name) }}"
                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Mode <span class="text-red-500">*</span></label>
            <select name="mode" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                <option value="inhouse" {{ old('mode', $type->mode) === 'inhouse' ? 'selected' : '' }}>In-House</option>
                <option value="online" {{ old('mode', $type->mode) === 'online' ? 'selected' : '' }}>Online</option>
                <option value="hybrid" {{ old('mode', $type->mode) === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Image</label>
            @if($type->image_url)<div class="mb-2"><img src="{{ $type->image_url }}" alt="Current" class="h-14 w-14 rounded-lg object-cover"></div>@endif
            <input name="image" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gold-50 file:text-gold-700 hover:file:bg-gold-100 cursor-pointer">
        </div>
    </div>

    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            Update Event Type
        </button>
        <a href="{{ route('manage.event-types.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
    </div>
</form>
@endsection
