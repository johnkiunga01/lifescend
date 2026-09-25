@extends('layouts.app')

@section('pageTitle', 'Testimonials')

@section('content')
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">What Our Community Says</h1>
            <p class="mt-4 text-lg text-slate-600">Hear from professionals who have experienced our events and training.</p>
        </div>

        @if($testimonials->isEmpty())
            <div class="text-center py-12">
                <p class="text-slate-500">No testimonials yet. Be the first to share your experience!</p>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($testimonials as $t)
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-0.5 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="h-5 w-5 {{ $i <= $t->rating ? 'text-gold-500' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed">&ldquo;{{ $t->content }}&rdquo;</p>
                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-gold-100 flex items-center justify-center text-xs font-bold text-gold-700">
                            {{ strtoupper(substr($t->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $t->user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $t->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection