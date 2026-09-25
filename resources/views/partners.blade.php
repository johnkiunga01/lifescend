@extends('layouts.app')

@section('pageTitle', 'Our Partners')

@section('content')
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">Our Partners</h1>
            <p class="mt-4 text-lg text-slate-600">Meet the organizations that support and collaborate with us.</p>
        </div>

        @if($partners->isEmpty())
            <div class="text-center py-12">
                <p class="text-slate-500">No partners to display at this time.</p>
            </div>
        @else
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($partners as $partner)
                <div class="group bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-lg transition-all duration-300">
                    <div class="aspect-w-16 aspect-h-9 bg-slate-50 flex items-center justify-center p-8">
                        @if($partner->image_url)
                            <img src="{{ $partner->image_url }}" alt="{{ $partner->name }}" class="max-h-20 max-w-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-300">
                        @else
                            <div class="h-20 w-20 rounded-xl bg-slate-200 flex items-center justify-center">
                                <svg class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-slate-900">{{ $partner->name }}</h3>
                        @if($partner->story)
                            <p class="mt-3 text-sm text-slate-600 line-clamp-3">{{ $partner->story }}</p>
                        @endif
                        @if($partner->website)
                            <a href="{{ $partner->website }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center text-sm font-semibold text-gold-600 hover:text-gold-700">
                                Visit Website
                                <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection