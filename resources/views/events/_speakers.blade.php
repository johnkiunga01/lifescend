@php $speakerList = $speakers ?? collect(); @endphp
@if($speakerList->isNotEmpty())
<div class="not-prose my-7 space-y-5">
    @foreach($speakerList as $speaker)
    <article class="relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 px-6 py-5 shadow-sm sm:px-7">
        <span class="absolute inset-y-0 left-0 w-1.5 bg-gold-500"></span>
        <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
            <img src="{{ $speaker->photo_url }}" alt="{{ $speaker->name }}" class="h-20 w-20 shrink-0 rounded-full border-2 border-teal-400 bg-white p-1 object-cover shadow-sm sm:h-[86px] sm:w-[86px]">
            <div class="min-w-0 flex-1">
                <h3 class="text-xl font-extrabold leading-tight text-slate-900 sm:text-2xl">{{ $speaker->name }}</h3>
                <p class="mt-1 text-base font-bold text-gold-700">{{ $speaker->company ?: 'lifescend.events' }}</p>
                <p class="mt-1 text-sm font-extrabold uppercase tracking-wide text-slate-500 sm:text-base">{{ $speaker->job_title ?: 'Trainer' }}</p>
            </div>
        </div>
        @if($speaker->bio)
        <p class="mt-5 text-base leading-6 text-slate-600 sm:text-lg sm:leading-7">{{ $speaker->bio }}</p>
        @endif
    </article>
    @endforeach
</div>
@endif
