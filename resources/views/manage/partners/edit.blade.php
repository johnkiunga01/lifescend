@extends('layouts.dashboard')

@section('pageTitle', 'Edit Partner')

@section('content')
<div class="mb-6">
    <a href="{{ route('manage.partners.index') }}" class="text-sm text-gold-600 hover:text-gold-700 font-semibold">&larr; Back to Partners</a>
    <h1 class="text-2xl font-extrabold text-slate-900 mt-2">Edit Partner</h1>
</div>

<form action="{{ route('manage.partners.update', $partner) }}" method="POST" enctype="multipart/form-data" class="max-w-2xl" x-data="imageCropper({ name: 'image', ratio: 1, width: 200, height: 200, label: 'Partner Logo' })">
    @csrf @method('PUT')
    @if($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm space-y-5">
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Partner Name <span class="text-red-500">*</span></label><input name="name" type="text" required value="{{ old('name', $partner->name) }}" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Slug <span class="text-red-500">*</span></label><input name="slug" type="text" required value="{{ old('slug', $partner->slug) }}" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Website URL</label><input name="website" type="url" value="{{ old('website', $partner->website) }}" placeholder="https://example.com" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Partner Logo (200x200px)</label>@if($partner->image_url)<div class="mb-2"><img src="{{ $partner->image_url }}" alt="Current" class="h-14 w-14 rounded-lg object-cover"></div>@endif<input name="image" type="file" accept="image/jpeg,image/png,image/webp" @@change="onFileChange" x-ref="fileInput" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gold-50 file:text-gold-700 hover:file:bg-gold-100 cursor-pointer"></div>
        <div><label class="block text-sm font-medium text-slate-700 mb-1">Story / Biography</label><textarea name="story" rows="4" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm resize-none" placeholder="Tell the partner's story...">{{ old('story', $partner->story) }}</textarea></div>
        <div class="flex items-center gap-2"><input name="is_active" type="checkbox" value="1" {{ old('is_active', $partner->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-gold-600 focus:ring-gold-500"><label class="text-sm font-medium text-slate-700">Active</label></div>
    </div>
    <div class="mt-6 flex items-center gap-3">
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">Update Partner</button>
        <a href="{{ route('manage.partners.index') }}" class="text-sm font-semibold text-slate-600 hover:text-slate-800">Cancel</a>
    </div>
</form>

@include('manage.events._cropper-modal')
@endsection