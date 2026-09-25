@extends('layouts.dashboard')

@section('pageTitle', 'My Testimonial')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">My Testimonial</h1>
    <p class="text-sm text-slate-500 mt-1">Share your experience with our community.</p>
</div>

@if(session('success'))
<div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="grid gap-6 lg:grid-cols-2">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="h-10 w-10 rounded-xl bg-gold-100 flex items-center justify-center mb-4">
            <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">Submit Your Testimonial</h3>
        <p class="text-sm text-slate-500 mb-5">Tell us about your experience. Your testimonial will be reviewed before being published.</p>

        <form action="{{ route('customer.testimonials.store') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-2">Rating</label>
                <div class="flex items-center gap-1" x-data="{ rating: {{ old('rating', 5) }} }">
                    @for($i = 1; $i <= 5; $i++)
                    <button type="button" @@click="rating = {{ $i }}" class="cursor-pointer p-1 transition-colors">
                        <svg class="h-7 w-7" :class="rating >= {{ $i }} ? 'text-gold-500' : 'text-slate-300'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </button>
                    @endfor
                    <input type="hidden" name="rating" x-model="rating">
                </div>
            </div>
            <div class="mb-5">
                <label class="block text-sm font-medium text-slate-700 mb-1">Your Story</label>
                <textarea name="content" rows="4" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm resize-none" placeholder="Share your experience..." maxlength="2000">{{ old('content') }}</textarea>
                <p class="text-xs text-slate-400 mt-1">Max 2,000 characters.</p>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">Submit Testimonial</button>
        </form>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="h-10 w-10 rounded-xl bg-gold-100 flex items-center justify-center mb-4">
            <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </div>
        <h3 class="text-lg font-bold text-slate-900 mb-1">Your Submission</h3>
        <p class="text-sm text-slate-500 mb-5">Your submitted testimonial status.</p>

        @if($myTestimonial)
        <div class="rounded-xl bg-slate-50 border border-slate-200 p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $myTestimonial->is_approved ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $myTestimonial->is_approved ? 'Approved' : 'Pending Review' }}</span>
            </div>
            @if(!$myTestimonial->is_approved)
            <p class="text-sm text-slate-500">Your testimonial has been submitted and is awaiting review by our team. We'll notify you once it's approved.</p>
            @endif
            <div class="flex items-center gap-0.5 mt-3">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="h-4 w-4 {{ $i <= $myTestimonial->rating ? 'text-gold-500' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                @endfor
            </div>
            <p class="text-sm text-slate-600 mt-3">{{ $myTestimonial->content }}</p>
            <p class="text-xs text-slate-400 mt-2">{{ $myTestimonial->created_at->format('M d, Y') }}</p>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="h-12 w-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
            <p class="text-sm text-slate-500">You haven't submitted a testimonial yet. Fill out the form to share your experience!</p>
        </div>
        @endif
    </div>
</div>
@endsection