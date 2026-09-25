@extends('layouts.dashboard')

@section('pageTitle', 'Edit Sponsor')

@section('content')
<div class="mb-6">
    <a href="{{ route('manage.sponsors.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Back to Sponsors</a>
    <h1 class="text-2xl font-extrabold text-slate-900 mt-2">Edit Sponsor</h1>
</div>

<form action="{{ route('manage.sponsors.update', $sponsor->id) }}" method="POST" enctype="multipart/form-data" class="max-w-2xl">
    @csrf @method('PUT')
    @if($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm space-y-5">
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Sponsor Name <span class="text-red-500">*</span></label><input name="name" type="text" required value="{{ old('name', $sponsor->name) }}" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
        <div class="grid gap-5 sm:grid-cols-2">
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Level <span class="text-red-500">*</span></label><select name="level" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm"><option value="platinum" {{ $sponsor->level === 'platinum' ? 'selected' : '' }}>Platinum</option><option value="gold" {{ $sponsor->level === 'gold' ? 'selected' : '' }}>Gold</option><option value="silver" {{ $sponsor->level === 'silver' ? 'selected' : '' }}>Silver</option><option value="bronze" {{ $sponsor->level === 'bronze' ? 'selected' : '' }}>Bronze</option></select></div>
            <div><label class="block text-sm font-medium text-slate-700 mb-1">Website</label><input name="website" type="text" value="{{ old('website', $sponsor->website) }}" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
        </div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Logo / Image</label>@if($sponsor->image_url)<div class="mb-2"><img src="{{ $sponsor->image_url }}" alt="Current" class="h-14 w-14 rounded-lg object-cover"></div>@endif<input name="image" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gold-50 file:text-gold-700 hover:file:bg-gold-100 cursor-pointer"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Description</label><textarea name="description" rows="3" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm resize-none">{{ old('description', $sponsor->description) }}</textarea></div>
    </div>
    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">Update Sponsor</button>
        <a href="{{ route('manage.sponsors.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
    </div>
</form>
@endsection
