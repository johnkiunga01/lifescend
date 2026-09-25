@extends('layouts.app')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
.attendee-scroll::-webkit-scrollbar { width: 4px; }
.attendee-scroll::-webkit-scrollbar-track { background: transparent; }
.attendee-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
.attendee-scroll::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endpush

@section('content')
<div x-data="checkout()" x-init="init()" class="min-h-screen bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('events.show', $event->slug) }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Event
            </a>
        </div>

        <div class="grid gap-8 lg:grid-cols-[1fr_400px] lg:items-start">
            {{-- Left: Steps --}}
            <div class="space-y-6">
                {{-- Progress Steps --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <nav class="flex items-center gap-2 text-sm font-semibold" aria-label="Progress">
                        <template x-for="(label, s) in ['Select Tickets', 'Attendee Details', 'Payment']" :key="s">
                            <button type="button"
                                @click="goToStep(s + 1)"
                                :disabled="s + 1 > maxStep"
                                :class="{
                                    'bg-gold-500 text-white shadow-md shadow-gold-500/20': step === s + 1,
                                    'bg-emerald-50 text-emerald-700 border border-emerald-200 cursor-pointer': step > s + 1,
                                    'bg-slate-100 text-slate-400 cursor-not-allowed': step < s + 1 && s + 1 > maxStep,
                                    'bg-slate-100 text-slate-500 cursor-pointer': step < s + 1 && s + 1 <= maxStep
                                }"
                                class="flex-1 rounded-xl px-4 py-3 text-center transition-all duration-200">
                                <span class="text-xs opacity-60 block mb-0.5">Step <span x-text="s + 1"></span></span>
                                <span x-text="label"></span>
                            </button>
                        </template>
                    </nav>
                </div>

                {{-- Step 1: Ticket Selection --}}
                <div x-show="step === 1" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-extrabold text-slate-900 mb-1">Select Your Tickets</h2>
                    <p class="text-sm text-slate-500 mb-6">{{ $event->name }}</p>


                    <div class="space-y-4">
                        @foreach($event->ticketTypes as $ticketType)
                        @php
                            $remaining = $ticketType->quantity - $ticketType->sold_count;
                            $isSoldOut = $remaining <= 0;
                            $isOnSale = (!$ticketType->sales_start || $ticketType->sales_start->isPast())
                                && (!$ticketType->sales_end || $ticketType->sales_end->isFuture());
                        @endphp
                        <div class="rounded-xl border {{ $isSoldOut ? 'border-slate-200 opacity-60' : 'border-slate-200 hover:border-gold-300' }} bg-white p-5 transition-all duration-200"
                            :class="{ 'ring-2 ring-gold-400 border-gold-400': tickets[{{ $ticketType->id }}] > 0 }">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-bold text-slate-900 text-lg">{{ $ticketType->name }}</h3>
                                        @if(!$isOnSale && $ticketType->sales_start && $ticketType->sales_start->isFuture())
                                        <span class="rounded-full bg-amber-50 border border-amber-200 px-2 py-0.5 text-[10px] font-bold text-amber-700">Coming Soon</span>
                                        @endif
                                        @if($isSoldOut)
                                        <span class="rounded-full bg-red-50 border border-red-200 px-2 py-0.5 text-[10px] font-bold text-red-600">Sold Out</span>
                                        @elseif($remaining < 10 && $remaining > 0)
                                        <span class="rounded-full bg-amber-50 border border-amber-200 px-2 py-0.5 text-[10px] font-bold text-amber-700">Only {{ $remaining }} left</span>
                                        @endif
                                    </div>
                                    @if($ticketType->description)
                                    <p class="text-sm text-slate-500 mb-3">{{ $ticketType->description }}</p>
                                    @endif
                                    @if($ticketType->highlights)
                                    <ul class="space-y-1 mb-3">
                                        @foreach($ticketType->highlights as $highlight)
                                        <li class="flex items-center gap-2 text-xs text-slate-600">
                                            <svg class="h-3.5 w-3.5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            {{ $highlight }}
                                        </li>
                                        @endforeach
                                    </ul>
                                    @endif
                                </div>

                                <div class="text-right shrink-0">
                                    @if($ticketType->payablePrice() > 0)
                                    @if($ticketType->usdReferencePrice() !== null)
                                    <div class="flex items-center justify-end gap-2">
                                        <p class="text-2xl font-extrabold text-slate-900" x-text="displayCurrency === 'EUR' ? '{{ number_format($ticketType->payablePrice(), 2) }} EUR' : '{{ number_format($ticketType->usdReferencePrice(), 2) }} USD'">{{ number_format($ticketType->payablePrice(), 2) }} EUR</p>
                                        <div class="inline-flex rounded-lg border border-slate-200 bg-white p-0.5" role="group" aria-label="Displayed currency">
                                            <button type="button" title="Show EUR price" aria-label="Show EUR price" :aria-pressed="displayCurrency === 'EUR'" @click="displayCurrency = 'EUR'" :class="displayCurrency === 'EUR' ? 'bg-gold-500 text-white' : 'text-slate-500 hover:bg-slate-100'" class="h-8 w-8 rounded-md text-sm font-bold transition-colors">€</button>
                                            <button type="button" title="Show USD price" aria-label="Show USD price" :aria-pressed="displayCurrency === 'USD'" @click="displayCurrency = 'USD'" :class="displayCurrency === 'USD' ? 'bg-gold-500 text-white' : 'text-slate-500 hover:bg-slate-100'" class="h-8 w-8 rounded-md text-sm font-bold transition-colors">$</button>
                                        </div>
                                    </div>
                                    <p x-show="displayCurrency === 'USD'" class="text-xs text-slate-500">Payment is charged in EUR</p>
                                    @else
                                    <p class="text-2xl font-extrabold text-slate-900">{{ number_format($ticketType->payablePrice(), 2) }} <span class="text-sm font-semibold text-slate-400">{{ $ticketType->checkoutCurrency() }}</span></p>
                                    @endif
                                    @else
                                    <p class="text-2xl font-extrabold text-emerald-600">Free</p>
                                    @endif

                                    @if(!$isSoldOut && $isOnSale)
                                    <div class="mt-3 flex items-center gap-2 justify-end">
                                        <button type="button" @click="decrementTicket({{ $ticketType->id }})"
                                            class="h-9 w-9 rounded-lg border border-slate-300 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors"
                                            :disabled="(tickets[{{ $ticketType->id }}] || 0) <= 0">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                        </button>
                                        <span class="w-10 text-center font-bold text-slate-900" x-text="tickets[{{ $ticketType->id }}] || 0"></span>
                                        <button type="button" @click="incrementTicket({{ $ticketType->id }}, {{ $remaining }}, {{ $ticketType->max_per_user }})"
                                            class="h-9 w-9 rounded-lg border border-slate-300 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors"
                                            :disabled="(tickets[{{ $ticketType->id }}] || 0) >= {{ min($remaining, $ticketType->max_per_user) }}">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex justify-end">
                        <button type="button" @click="goToStep(2)"
                            :disabled="totalQuantity === 0"
                            :class="totalQuantity === 0 ? 'bg-slate-200 text-slate-400 cursor-not-allowed' : 'bg-gradient-to-r from-gold-500 to-gold-600 text-white shadow-lg shadow-gold-500/25 hover:from-gold-600 hover:to-gold-700'"
                            class="rounded-xl px-8 py-3 text-sm font-bold transition-all duration-200">
                            Continue to Attendees
                            <svg class="h-4 w-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Step 2: Attendee Details --}}
                <div x-show="step === 2" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    {{-- Header --}}
                    <div class="px-6 pt-6 pb-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-gold-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-extrabold text-slate-900 leading-tight">Attendee Details</h2>
                                    <p class="text-xs text-slate-500 mt-0.5">Fill in details for each ticket holder</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold text-slate-500 bg-slate-100 rounded-full px-3 py-1"
                                    x-text="attendeeSlots.length + ' ' + (attendeeSlots.length === 1 ? 'ticket' : 'tickets')"></span>
                                <span class="text-xs font-semibold rounded-full px-3 py-1"
                                    :class="attendeesValid ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-amber-700 bg-amber-50 border border-amber-200'"
                                    x-text="attendeesValid ? 'All filled' : (participants.filter(p => p && p.name && p.email).length + '/' + attendeeSlots.length + ' filled')">
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        {{-- Section 1: Reservation for --}}
                        <section class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                            <div class="grid grid-cols-2 gap-3">
                            <button type="button" @click="reservationType = 'individual'; attendeesLater = false"
                                :class="reservationType === 'individual' ? 'border-gold-400 ring-2 ring-gold-300 bg-gold-50 text-gold-800' : 'border-slate-200 bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                class="rounded-xl border p-3 text-left text-sm font-semibold transition-colors">Individual</button>
                            <button type="button" @click="reservationType = 'company'"
                                :class="reservationType === 'company' ? 'border-gold-400 ring-2 ring-gold-300 bg-gold-50 text-gold-800' : 'border-blue-200 bg-blue-100 text-blue-800 hover:bg-blue-200'"
                                class="rounded-xl border p-3 text-left text-sm font-semibold transition-colors">Company</button>
                            </div>
                        </section>

                        {{-- Section 2: Company information --}}
                        <section x-show="reservationType === 'company'" x-cloak class="rounded-2xl border border-gold-200 bg-gold-50/40 p-5">
                            <div class="flex items-start gap-3 mb-4">
                                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gold-500 text-white">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-4h6v4M9 9h.01M15 9h.01M9 13h.01M15 13h.01"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Company information</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Billing details and the person we should contact.</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-1.5">Company name <span class="text-red-400">*</span></label>
                                    <input x-model="companyName" type="text" maxlength="255" placeholder="Company name"
                                        class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-gold-400 focus:ring-2 focus:ring-gold-100 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-1.5">Company details</label>
                                    <textarea x-model="companyDetails" rows="3" maxlength="2000" placeholder="Address, billing info, notes..."
                                        class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-gold-400 focus:ring-2 focus:ring-gold-100 focus:outline-none resize-none"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wide text-slate-600 mb-1.5">Contact person <span class="text-red-400">*</span></label>
                                    <input x-model="contactPerson" type="text" maxlength="255" placeholder="Full name of the contact person"
                                        class="block w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-gold-400 focus:ring-2 focus:ring-gold-100 focus:outline-none">
                                </div>
                                <div class="rounded-xl bg-blue-50 border border-blue-100 px-3 py-2.5 text-xs text-blue-800">
                                    This reservation holds <strong x-text="totalQuantity"></strong> attendee place(s).
                                </div>
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-700">
                                    <input x-model="attendeesLater" type="checkbox" class="mt-0.5 rounded border-slate-300 text-gold-600 focus:ring-gold-500">
                                    <span><strong>Add attendee details later</strong><br><span class="text-xs text-slate-500">You can assign a name and email to each ticket from My Tickets after reserving.</span></span>
                                </label>
                            </div>
                        </section>

                        {{-- Section 3: Attendee details --}}
                        <section x-show="reservationType !== 'company' || !attendeesLater" class="rounded-2xl border border-slate-200 p-5">
                            <div class="flex items-start gap-3 mb-4">
                                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gold-100 text-gold-600">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Attendee details</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Fill in details for each ticket holder.</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="attendee-scroll space-y-3 overflow-y-auto pr-1"
                             style="max-height: 420px; scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent;">
                            <template x-for="(slot, idx) in attendeeSlots" :key="idx">
                                <div class="rounded-xl border bg-white transition-all duration-200"
                                     :class="participants[idx] && participants[idx].name && participants[idx].email
                                         ? 'border-emerald-200 shadow-sm shadow-emerald-50'
                                         : 'border-slate-200 hover:border-slate-300'">
                                    {{-- Card Header --}}
                                    <div class="flex items-center gap-3 px-4 py-2.5 border-b border-slate-100 bg-slate-50/60 rounded-t-xl">
                                        {{-- Number / checkmark badge --}}
                                        <div class="h-7 w-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0"
                                             :class="participants[idx] && participants[idx].name && participants[idx].email
                                                 ? 'bg-emerald-100 text-emerald-700'
                                                 : 'bg-gold-100 text-gold-700'">
                                            <template x-if="participants[idx] && participants[idx].name && participants[idx].email">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </template>
                                            <template x-if="!(participants[idx] && participants[idx].name && participants[idx].email)">
                                                <span x-text="idx + 1"></span>
                                            </template>
                                        </div>
                                        {{-- Attendee label --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 leading-none" x-text="'Attendee ' + (idx + 1)"></p>
                                        </div>
                                        {{-- Ticket package badge --}}
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-gold-200 bg-gold-50 px-2.5 py-1">
                                                <svg class="h-3 w-3 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                                <span class="text-xs font-semibold text-gold-700" x-text="slot.ticketName"></span>
                                            </span>
                                            <span class="text-xs font-bold text-slate-600"
                                                  x-text="ticketTypePriceLabels[slot.ticket_type_id]"></span>
                                        </div>
                                        {{-- Remove ticket button (hidden when only 1 ticket left) --}}
                                        <button type="button"
                                            x-show="totalQuantity > 1"
                                            @click="decrementTicket(slot.ticket_type_id); if (totalQuantity === 0) step = 1;"
                                            title="Remove this ticket"
                                            class="ml-1 h-6 w-6 rounded-md flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors shrink-0">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                    {{-- Fields --}}
                                    <div class="grid gap-3 sm:grid-cols-2 p-4">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                                Full Name <span class="text-red-400">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                </div>
                                                <input type="text"
                                                    x-model="participants[idx].name"
                                                    :name="'participants[' + idx + '][name]'"
                                                    required maxlength="255"
                                                    placeholder="Full name"
                                                    class="block w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 py-2.5 text-slate-900 placeholder-slate-400 focus:border-gold-400 focus:ring-2 focus:ring-gold-100 focus:outline-none text-sm transition-all">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wide">
                                                Email Address <span class="text-red-400">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                </div>
                                                <input type="email"
                                                    x-model="participants[idx].email"
                                                    :name="'participants[' + idx + '][email]'"
                                                    required maxlength="255"
                                                    placeholder="email@example.com"
                                                    class="block w-full rounded-lg border border-slate-200 bg-white pl-9 pr-3 py-2.5 text-slate-900 placeholder-slate-400 focus:border-gold-400 focus:ring-2 focus:ring-gold-100 focus:outline-none text-sm transition-all">
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden"
                                        x-model="participants[idx].ticket_type_id"
                                        :name="'participants[' + idx + '][ticket_type_id]'">
                                </div>
                            </template>
                        </div>
                        {{-- Scroll hint when more than 3 --}}
                        <p class="text-center text-xs text-slate-400 -mt-1 flex items-center justify-center gap-1"
                           x-show="attendeeSlots.length > 3">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4"/></svg>
                            Scroll to see all attendees
                        </p>

                        {{-- Add More Tickets --}}
                        <div class="rounded-xl border border-dashed border-slate-300 overflow-hidden transition-all duration-200"
                             :class="showAddTicket ? 'border-gold-300 bg-gold-50/30' : 'hover:border-slate-400'">
                            <button type="button"
                                @click="showAddTicket = !showAddTicket"
                                class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left transition-colors hover:bg-slate-50/60">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-7 w-7 rounded-full flex items-center justify-center transition-colors"
                                         :class="showAddTicket ? 'bg-gold-500 text-white' : 'bg-slate-200 text-slate-500'">
                                        <svg class="h-3.5 w-3.5 transition-transform duration-200"
                                             :class="showAddTicket ? 'rotate-45' : ''"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-semibold"
                                          :class="showAddTicket ? 'text-gold-700' : 'text-slate-600'"
                                          x-text="showAddTicket ? 'Hide ticket options' : 'Add more tickets'"></span>
                                </div>
                                <svg class="h-4 w-4 text-slate-400 transition-transform duration-200"
                                     :class="showAddTicket ? 'rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Ticket type picker --}}
                            <div x-show="showAddTicket"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 -translate-y-1"
                                 class="border-t border-dashed border-slate-300 px-4 py-3 space-y-2.5">
                                @foreach($event->ticketTypes as $tt)
                                @php
                                    $rem = $tt->quantity - $tt->sold_count;
                                    $isSoldOut = $rem <= 0;
                                    $isOnSale = (!$tt->sales_start || $tt->sales_start->isPast())
                                        && (!$tt->sales_end || $tt->sales_end->isFuture());
                                @endphp
                                <div class="flex items-center gap-3 rounded-lg border bg-white p-3 transition-all"
                                     :class="(tickets[{{ $tt->id }}] || 0) > 0 ? 'border-gold-300 shadow-sm' : 'border-slate-200'">
                                    {{-- Package icon + info --}}
                                    <div class="h-9 w-9 rounded-lg shrink-0 flex items-center justify-center"
                                         :class="(tickets[{{ $tt->id }}] || 0) > 0 ? 'bg-gold-100' : 'bg-slate-100'">
                                        <svg class="h-4 w-4"
                                             :class="(tickets[{{ $tt->id }}] || 0) > 0 ? 'text-gold-600' : 'text-slate-400'"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-slate-800 leading-tight">{{ $tt->name }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            @if($tt->payablePrice() > 0)
                                                {{ number_format($tt->payablePrice(), 2) }} {{ $tt->checkoutCurrency() }}
                                            @else
                                                Free
                                            @endif
                                            @if(!$isSoldOut && $rem < 10)
                                                &middot; <span class="text-amber-600 font-medium">{{ $rem }} left</span>
                                            @endif
                                        </p>
                                    </div>
                                    {{-- Controls --}}
                                    @if($isSoldOut || !$isOnSale)
                                    <span class="text-xs font-semibold text-slate-400 bg-slate-100 rounded-full px-2.5 py-1">
                                        {{ $isSoldOut ? 'Sold out' : 'Unavailable' }}
                                    </span>
                                    @else
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button type="button"
                                            @click="decrementTicket({{ $tt->id }})"
                                            :disabled="(tickets[{{ $tt->id }}] || 0) <= 0"
                                            :class="(tickets[{{ $tt->id }}] || 0) <= 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100'"
                                            class="h-7 w-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                                        </button>
                                        <span class="w-6 text-center text-sm font-bold text-slate-800"
                                              x-text="tickets[{{ $tt->id }}] || 0"></span>
                                        <button type="button"
                                            @click="incrementTicket({{ $tt->id }}, {{ $rem }}, {{ $tt->max_per_user }})"
                                            :disabled="(tickets[{{ $tt->id }}] || 0) >= {{ min($rem, $tt->max_per_user) }} || totalQuantity >= 10"
                                            :class="((tickets[{{ $tt->id }}] || 0) >= {{ min($rem, $tt->max_per_user) }} || totalQuantity >= 10) ? 'opacity-30 cursor-not-allowed' : 'hover:bg-gold-50 hover:border-gold-300'"
                                            class="h-7 w-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 transition-colors">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                                <p class="text-xs text-slate-400 text-center pt-1"
                                   x-show="totalQuantity >= 10">Maximum 10 tickets per order reached.</p>
                            </div>
                        </div>
                            </div>
                        </section>
                    </div>

                    <div x-show="reservationType === 'company' && attendeesLater" x-cloak class="mx-6 my-4 rounded-xl border border-dashed border-blue-300 bg-blue-50 p-4 text-sm text-blue-800">
                        Attendee details are optional for this company reservation. Empty attendee records will be created for the <span x-text="totalQuantity"></span> reserved place(s), ready for you to complete later.
                    </div>

                    {{-- Footer --}}
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <button type="button" @click="step = 1"
                            class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-800 transition-colors border border-transparent hover:border-slate-200">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Back
                        </button>
                        <button type="button" @click="goToStep(3)"
                            :disabled="!attendeesValid"
                            :class="attendeesValid ? 'bg-gradient-to-r from-gold-500 to-gold-600 text-white shadow-md shadow-gold-500/20 hover:from-gold-600 hover:to-gold-700 hover:shadow-lg hover:shadow-gold-500/30' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                            class="inline-flex items-center gap-2 rounded-xl px-7 py-2.5 text-sm font-bold transition-all duration-200">
                            Continue to Payment
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Step 3: Payment --}}
                <div x-show="step === 3" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">Payment Details</h2>
                            <p class="text-sm text-slate-500 mt-0.5">Complete your purchase securely</p>
                        </div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            256-bit SSL Secure
                        </div>
                    </div>

                    {{-- Coupon Section --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-6">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Have a coupon code?</label>
                        <div class="flex gap-3">
                            <input type="text"
                                x-model="couponCode"
                                @keyup.enter="applyCoupon()"
                                placeholder="Enter coupon code"
                                maxlength="50"
                                class="block flex-1 rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm uppercase">
                            <button type="button" @click="applyCoupon()"
                                :disabled="couponLoading || !couponCode.trim()"
                                class="rounded-xl bg-slate-800 px-6 py-3 text-sm font-bold text-white hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!couponLoading">Apply</span>
                                <span x-show="couponLoading" class="inline-flex items-center gap-1">
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Checking...
                                </span>
                        </button>
                        </div>
                    </div>
                        <div x-show="couponMessage" class="mt-3">
                            <div x-show="coupon && coupon.valid"
                                class="flex items-center gap-2 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-2.5 text-sm font-semibold text-emerald-700">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Coupon <span class="font-extrabold" x-text="couponCode.toUpperCase()"></span> applied! You save <span class="font-extrabold" x-text="coupon ? formatCurrency(coupon.discount) : ''"></span>.
                                <button type="button" @click="removeCoupon()" class="ml-auto text-emerald-500 hover:text-emerald-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <div x-show="couponMessage && (!coupon || !coupon.valid)"
                                class="rounded-lg bg-red-50 border border-red-200 px-4 py-2.5 text-sm font-semibold text-red-700"
                                x-text="couponMessage">
                            </div>
                        </div>

                    {{-- Card Preview --}}
                    <div x-show="paymentMode === 'pay' || totalAfterDiscount <= 0" class="relative mb-6 rounded-xl bg-gradient-to-br from-slate-800 to-slate-900 p-5 overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gold-500/10 rounded-full -translate-y-1/2 translate-x-1/4 blur-2xl"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-gold-500/5 rounded-full translate-y-1/2 -translate-x-1/4 blur-xl"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-6">
                                <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Credit / Debit Card</span>
                                <div class="flex items-center gap-1">
                                    <template x-if="cardBrand === 'visa'">
                                        <span class="text-sm font-bold text-white" style="font-family: serif; font-style: italic;">VISA</span>
                                    </template>
                                    <template x-if="cardBrand === 'mastercard'">
                                        <span class="text-sm font-bold text-white" style="font-family: serif;">Mastercard</span>
                                    </template>
                                    <template x-if="cardBrand === 'amex'">
                                        <span class="text-sm font-bold text-white" style="font-family: serif;">AMEX</span>
                                    </template>
                                    <template x-if="!cardBrand">
                                        <svg class="h-6 w-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    </template>
                                </div>
                            </div>
                            <div x-text="cardDisplayNumber || '•••• •••• •••• ••••'" class="text-lg md:text-xl tracking-wider font-bold text-white mb-4 font-mono"></div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[9px] uppercase tracking-widest text-slate-400 mb-0.5">Card Holder</p>
                                    <p x-text="cardName || 'Your Name'" class="text-xs font-semibold text-white"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] uppercase tracking-widest text-slate-400 mb-0.5">Expires</p>
                                    <p x-text="cardExpiryDisplay || 'MM/YY'" class="text-xs font-semibold text-white"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Stripe Individual Card Fields + Address --}}
                    <div x-show="paymentMode === 'pay' || totalAfterDiscount <= 0" class="grid gap-5 sm:grid-cols-2">
                        {{-- Card Number --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                    Card Number <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <div id="card-number-element" class="stripe-card rounded-xl border border-slate-300 bg-white px-4 py-3.5 min-h-[48px] transition-all duration-200"></div>
                            <div id="card-number-errors" class="mt-1 text-xs font-semibold text-red-600 min-h-[18px]"></div>
                        </div>

                        {{-- Name on Card --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Name on Card <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <input type="text" x-model="cardName" placeholder="John Doe"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm transition-all duration-200">
                        </div>

                        {{-- Expiry --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Expiry Date <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <div id="card-expiry-element" class="stripe-card rounded-xl border border-slate-300 bg-white px-4 py-3.5 min-h-[48px] transition-all duration-200"></div>
                            <div id="card-expiry-errors" class="mt-1 text-xs font-semibold text-red-600 min-h-[18px]"></div>
                        </div>

                        {{-- CVC --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    CVC <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <div id="card-cvc-element" class="stripe-card rounded-xl border border-slate-300 bg-white px-4 py-3.5 min-h-[48px] transition-all duration-200"></div>
                            <div id="card-cvc-errors" class="mt-1 text-xs font-semibold text-red-600 min-h-[18px]"></div>
                        </div>

                        {{-- Divider --}}
                        <div class="sm:col-span-2 my-1">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 border-t border-slate-200"></div>
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Billing Address</span>
                                <div class="flex-1 border-t border-slate-200"></div>
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Street Address <span class="text-red-500">*</span>
                                </span>
                            </label>
                            <input type="text" x-model="billingAddress" placeholder="123 Main Street"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm transition-all duration-200">
                        </div>

                        {{-- City --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">City <span class="text-red-500">*</span></label>
                            <input type="text" x-model="billingCity" placeholder="New York"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm transition-all duration-200">
                        </div>

                        {{-- State --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">State <span class="text-red-500">*</span></label>
                            <input type="text" x-model="billingState" placeholder="NY"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm transition-all duration-200">
                        </div>

                        {{-- ZIP --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">ZIP Code <span class="text-red-500">*</span></label>
                            <input type="text" x-model="billingZip" placeholder="10001"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm transition-all duration-200">
                        </div>

                        {{-- Country --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Country <span class="text-red-500">*</span></label>
                            <select x-model="billingCountry"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm transition-all duration-200">
                                <option value="">Select country</option>
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="GB">United Kingdom</option>
                                <option value="AU">Australia</option>
                                <option value="PH">Philippines</option>
                                <option value="SG">Singapore</option>
                                <option value="MY">Malaysia</option>
                                <option value="IN">India</option>
                                <option value="DE">Germany</option>
                                <option value="FR">France</option>
                                <option value="JP">Japan</option>
                                <option value="AE">United Arab Emirates</option>
                            </select>
                        </div>
                    </div>

                    {{-- Secured by Badge --}}
                    <div x-show="paymentMode === 'pay' || totalAfterDiscount <= 0" class="mt-6 rounded-xl bg-gradient-to-r from-slate-50 to-slate-100 border border-slate-200 p-4">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800">Secured by <span class="text-gold-600">Stripe</span></p>
                                    <p class="text-xs text-slate-500">Your payment information is encrypted with 256-bit SSL technology.</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-md bg-white border border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">PCI DSS</span>
                                <span class="px-2.5 py-1 rounded-md bg-white border border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wider">3D Secure</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 mt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between">
                            <button type="button" @click="step = 2"
                                class="rounded-xl px-6 py-3.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                                <svg class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                                Back
                            </button>
                            <div class="flex items-center gap-3">
                                <p x-show="!attendeesValid && totalQuantity > 0" class="text-xs text-amber-600 font-medium">Please fill in all attendee details.</p>

                                {{-- Free order: complete registration --}}
                                <button x-show="totalAfterDiscount <= 0" type="button" @click="handlePayment()"
                                :disabled="submitting || !attendeesValid"
                                class="rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-8 py-3.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:from-gold-600 hover:to-gold-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!submitting" class="flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Complete Registration
                                    </span>
                                    <span x-show="submitting" class="inline-flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        Processing...
                                    </span>
                                </button>

                                {{-- Paid order: Pay now --}}
                                <button x-show="totalAfterDiscount > 0 && paymentMode === 'pay'" type="button" @click="handlePayment()"
                                :disabled="submitting || !attendeesValid"
                                class="rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-10 py-3.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:from-gold-600 hover:to-gold-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span x-show="!submitting" class="flex items-center gap-2">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span x-text="'Pay ' + formatCurrency(totalAfterDiscount)"></span>
                                    </span>
                                    <span x-show="submitting" class="inline-flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        Processing...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="paymentError" class="mt-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm font-semibold text-red-700" x-text="paymentError"></div>
                </div>
            </div>

            {{-- Right: Order Summary --}}
            <div class="lg:sticky lg:top-24">
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="bg-slate-900 px-6 py-5">
                        <h3 class="text-sm font-semibold text-white">Order Summary</h3>
                        <p class="text-xs text-slate-400 mt-1">{{ $event->name }}</p>
                    </div>

                    <div class="p-6">
                        <template x-if="totalQuantity === 0">
                            <div class="text-center py-6">
                                <svg class="h-12 w-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                                <p class="text-sm font-semibold text-slate-400">No tickets selected yet</p>
                                <p class="text-xs text-slate-400 mt-1">Select your tickets to continue</p>
                            </div>
                        </template>

                        <template x-if="totalQuantity > 0">
                            <div>
                                {{-- Line Items --}}
                                <div class="space-y-3 mb-4">
                                    <template x-for="(qty, ttId) in tickets" :key="ttId">
                                        <div x-show="qty > 0" class="flex items-center justify-between text-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-slate-700" x-text="ticketTypeNames[ttId]"></span>
                                                <span class="text-slate-400" x-text="'x' + qty"></span>
                                            </div>
                                            <span class="font-semibold text-slate-900" x-text="formatCurrency(ticketTypePrices[ttId] * qty)"></span>
                                        </div>
                                    </template>
                                </div>

                                <div class="border-t border-slate-200 pt-4 space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-500">Subtotal</span>
                                        <span class="font-semibold text-slate-700" x-text="formatCurrency(totalAmount)"></span>
                                    </div>

                                    <div x-show="coupon && coupon.valid" class="flex items-center justify-between text-sm">
                                        <span class="text-emerald-600 flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                            Discount (<span x-text="couponCode.toUpperCase()"></span>)
                                        </span>
                                        <span class="font-semibold text-emerald-600" x-text="coupon ? '-' + formatCurrency(coupon.discount) : ''"></span>
                                    </div>

                                    <div class="flex items-center justify-between pt-3 border-t border-slate-200">
                                        <span class="text-base font-bold text-slate-900">Total</span>
                                        <span class="text-base font-extrabold text-slate-900" x-text="formatCurrency(totalAfterDiscount)"></span>
                                    </div>
                                </div>

                                <div class="mt-4 rounded-lg bg-gradient-to-r from-emerald-50 to-emerald-50/50 border border-emerald-200 p-3">
                                    <div class="flex items-center gap-2">
                                        <div class="h-7 w-7 rounded-md bg-emerald-100 flex items-center justify-center shrink-0">
                                            <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-emerald-800">Secured by <span class="text-gold-600">Stripe</span></p>
                                            <p class="text-[10px] text-emerald-600">256-bit SSL encrypted</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Event Quick Info --}}
                <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center gap-3">
                        @if($event->logo)
                        <img src="{{ $event->logo_url }}" alt="{{ $event->name }}" class="h-12 w-12 rounded-xl object-cover shrink-0">
                        @else
                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-white font-bold text-lg shrink-0">{{ strtoupper(substr($event->name, 0, 1)) }}</div>
                        @endif
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 text-sm truncate">{{ $event->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $event->start_date->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form x-ref="freeOrderForm" action="{{ route('events.purchase', $event->slug) }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="coupon_code" x-ref="freeCouponInput">
    </form>

    <form x-ref="reserveForm" action="{{ route('events.reserve', $event->slug) }}" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="coupon_code" x-ref="reserveCouponInput">
    </form>
</div>
@endsection

@push('styles')
<style>
.stripe-card, .stripe-card-embedded {
    box-sizing: border-box;
    width: 100%;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.stripe-card.StripeElement--focus, .stripe-card-embedded.StripeElement--focus {
    border-color: #D4AF37;
    box-shadow: 0 0 0 1px #D4AF37;
}
.stripe-card.StripeElement--invalid, .stripe-card-embedded.StripeElement--invalid {
    border-color: #ef4444;
}
.stripe-card.StripeElement--complete, .stripe-card-embedded.StripeElement--complete {
    border-color: #22c55e;
}
.stripe-card iframe, .stripe-card-embedded iframe {
    margin: -1px 0;
}
</style>
@endpush

@push('head')
@php
    $checkoutData = [
        'currency' => $event->ticketTypes->first(fn ($type) => $type->payablePrice() > 0)?->checkoutCurrency() ?? 'USD',
        'ticketTypes' => $event->ticketTypes->mapWithKeys(fn($tt) => [$tt->id => [
            'name' => $tt->name,
            'price' => $tt->payablePrice(),
            'max_per_user' => (int) $tt->max_per_user,
            'remaining' => max(0, $tt->quantity - $tt->sold_count),
        ]])->toArray(),
        'eventId' => $event->id,
        'eventSlug' => $event->slug,
        'stripeKey' => $stripeKey,
        'routes' => [
            'coupons.validate' => route('coupons.validate'),
            'stripe.intent' => route('events.stripe.intent', $event->slug),
            'stripe.confirm' => route('stripe.confirm'),
        ],
    ];
@endphp
<script id="checkout-data" type="application/json">@json($checkoutData)</script>
<script>
const checkoutData = JSON.parse(document.getElementById('checkout-data').textContent);
const { currency, ticketTypes, eventId, eventSlug } = checkoutData;

function checkout() {

    return {
        step: 1,
        maxStep: 1,
        tickets: {},
        participants: [],
        reservationType: 'individual',
        companyName: '',
        companyDetails: '',
        contactPerson: '',
        attendeesLater: false,
        couponCode: '',
        couponLoading: false,
        coupon: null,
        couponMessage: '',
        submitting: false,
        reserving: false,
        paymentMode: 'pay',
        displayCurrency: 'EUR',
        paymentError: '',
        stripe: null,
        clientSecret: null,
        stripeElements: null,
        cardNumber: null,
        cardExpiry: null,
        cardCvc: null,
        cardBrand: '',
        cardDisplayNumber: '',
        cardExpiryDisplay: '',
        cardName: '',
        billingAddress: '',
        billingCity: '',
        billingState: '',
        billingZip: '',
        billingCountry: '',
        showAddTicket: false,

        init() {
            this.stripe = Stripe(checkoutData.stripeKey);

            // Restore persisted checkout state for this event
            const _storageKey = 'checkout_' + eventSlug;
            try {
                const _saved = sessionStorage.getItem(_storageKey);
                if (_saved) {
                    const _s = JSON.parse(_saved);
                    if (_s.tickets && Object.keys(_s.tickets).length > 0) {
                        this.tickets = Object.fromEntries(
                            Object.entries(_s.tickets).filter(([id]) => id in ticketTypes)
                        );
                    }
                    if (_s.participants && _s.participants.length > 0) this.participants = _s.participants;
                    if (['individual', 'company'].includes(_s.reservationType)) this.reservationType = _s.reservationType;
                    if (_s.companyName) this.companyName = _s.companyName;
                    if (_s.companyDetails) this.companyDetails = _s.companyDetails;
                    if (_s.contactPerson) this.contactPerson = _s.contactPerson;
                    if (_s.attendeesLater) this.attendeesLater = true;
                    if (_s.maxStep) this.maxStep = _s.maxStep;
                    if (_s.step && _s.step >= 1 && _s.step <= (_s.maxStep || 1)) this.step = _s.step;
                    if (_s.couponCode) this.couponCode = _s.couponCode;
                }
            } catch (_e) {}

            // Mount Stripe if restored directly to payment step
            if (this.step === 3) {
                this.$nextTick(() => { if (!this.cardNumber) this.mountCardElements(); });
            }

            // Persist state on every meaningful change
            const _save = () => {
                try {
                    sessionStorage.setItem(_storageKey, JSON.stringify({
                        step: this.step,
                        maxStep: this.maxStep,
                        tickets: this.tickets,
                        participants: this.participants,
                        reservationType: this.reservationType,
                        companyName: this.companyName,
                        companyDetails: this.companyDetails,
                        contactPerson: this.contactPerson,
                        attendeesLater: this.attendeesLater,
                        couponCode: this.couponCode,
                    }));
                } catch (_e) {}
            };
            this.$watch('step', _save);
            this.$watch('maxStep', _save);
            this.$watch('tickets', _save);
            this.$watch('couponCode', _save);
            this.$watch(() => JSON.stringify(this.participants), _save);
            this.$watch('reservationType', _save);
            this.$watch('companyName', _save);
            this.$watch('companyDetails', _save);
            this.$watch('contactPerson', _save);
            this.$watch('attendeesLater', _save);

            this.$watch('step', (value) => {
                if (value === 3) {
                    this.$nextTick(() => {
                        if (!this.cardNumber) this.mountCardElements();
                    });
                } else {
                    this.unmountCardElements();
                }
            });
        },

        get totalQuantity() {
            return Object.values(this.tickets).reduce((sum, q) => sum + (q || 0), 0);
        },

        get totalAmount() {
            let total = 0;
            for (const [ttId, qty] of Object.entries(this.tickets)) {
                if (qty > 0 && ticketTypes[ttId]) {
                    total += ticketTypes[ttId].price * qty;
                }
            }
            return total;
        },

        get discount() {
            return this.coupon && this.coupon.valid ? this.coupon.discount : 0;
        },

        get totalAfterDiscount() {
            return Math.max(0, this.totalAmount - this.discount);
        },

        get ticketTypeNames() {
            const names = {};
            for (const [id, tt] of Object.entries(ticketTypes)) names[id] = tt.name;
            return names;
        },

        get ticketTypePrices() {
            const prices = {};
            for (const [id, tt] of Object.entries(ticketTypes)) prices[id] = tt.price;
            return prices;
        },

        get ticketTypePriceLabels() {
            const labels = {};
            for (const [id, tt] of Object.entries(ticketTypes)) {
                labels[id] = tt.price > 0 ? tt.price.toFixed(2) + ' ' + currency : 'Free';
            }
            return labels;
        },

        get attendeeSlots() {
            const slots = [];
            for (const [ttId, qty] of Object.entries(this.tickets)) {
                if (qty > 0 && ticketTypes[ttId]) {
                    for (let i = 0; i < qty; i++) {
                        slots.push({ ticket_type_id: parseInt(ttId), ticketName: ticketTypes[ttId].name });
                    }
                }
            }
            this.syncParticipants(slots);
            return slots;
        },

        syncParticipants(slots) {
            const targetLen = slots.length;
            if (this.participants.length === 0 && targetLen > 0) {
                this.participants = slots.map(s => ({ name: '', email: '', ticket_type_id: s.ticket_type_id }));
                return;
            }
            while (this.participants.length < targetLen) {
                const s = slots[this.participants.length];
                this.participants.push({ name: '', email: '', ticket_type_id: s ? s.ticket_type_id : slots[slots.length - 1].ticket_type_id });
            }
            if (this.participants.length > targetLen) {
                this.participants = this.participants.slice(0, targetLen);
            }
        },

        get attendeesValid() {
            if (this.totalQuantity === 0) return false;
            if (this.reservationType === 'company') {
                if (!this.companyName.trim() || !this.contactPerson.trim()) return false;
                if (this.attendeesLater) return true;
            }
            for (const p of this.participants) {
                if (!p.name.trim() || !p.email.trim()) return false;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(p.email.trim())) return false;
            }
            return true;
        },

        get billingValid() {
            return this.cardName.trim()
                && this.billingAddress.trim()
                && this.billingCity.trim()
                && this.billingState.trim()
                && this.billingZip.trim()
                && this.billingCountry;
        },

        goToStep(n) {
            if (n === 2 && this.totalQuantity === 0) return;
            if (n === 3 && !this.attendeesValid) return;
            this.step = n;
            if (n > this.maxStep) this.maxStep = n;
        },

        incrementTicket(ttId, remaining, maxPerUser) {
            if (this.totalQuantity >= 10) return;
            const current = this.tickets[ttId] || 0;
            const max = Math.min(remaining, maxPerUser || 10);
            if (current >= max) return;
            this.tickets = { ...this.tickets, [ttId]: current + 1 };
            this.resetCoupon();
        },

        decrementTicket(ttId) {
            const current = this.tickets[ttId] || 0;
            if (current <= 0) return;
            if (current === 1) {
                const next = { ...this.tickets };
                delete next[ttId];
                this.tickets = next;
            } else {
                this.tickets = { ...this.tickets, [ttId]: current - 1 };
            }
            this.resetCoupon();
        },

        mountCardElements() {
            if (this.cardNumber) return;

            this.stripeElements = this.stripe.elements();

            const baseStyle = {
                base: {
                    fontFamily: '"Outfit", sans-serif',
                    fontSize: '16px',
                    color: '#1e293b',
                    '::placeholder': { color: '#94a3b8' },
                },
                invalid: { color: '#ef4444', iconColor: '#ef4444' },
            };

            this.cardNumber = this.stripeElements.create('cardNumber', { style: baseStyle });
            this.cardExpiry = this.stripeElements.create('cardExpiry', { style: baseStyle });
            this.cardCvc = this.stripeElements.create('cardCvc', { style: baseStyle });

            this.cardNumber.mount('#card-number-element');
            this.cardExpiry.mount('#card-expiry-element');
            this.cardCvc.mount('#card-cvc-element');

            this.cardNumber.on('change', (event) => {
                const el = document.getElementById('card-number-errors');
                el.textContent = event.error ? event.error.message : '';
                this.cardBrand = event.brand && event.brand !== 'unknown' ? event.brand : '';
                if (event.complete) {
                    this.stripe._card && this.stripe._card.getToken().then(t => {
                        if (t && t.card) this.cardDisplayNumber = '•••• ' + t.card.last4;
                    }).catch(() => {});
                }
            });

            this.cardExpiry.on('change', (event) => {
                const el = document.getElementById('card-expiry-errors');
                el.textContent = event.error ? event.error.message : '';
                if (event.complete && event.value) {
                    this.cardExpiryDisplay = event.value.month.padStart(2, '0') + '/' + event.value.year;
                }
            });

            this.cardCvc.on('change', (event) => {
                const el = document.getElementById('card-cvc-errors');
                el.textContent = event.error ? event.error.message : '';
            });
        },

        unmountCardElements() {
            if (this.cardNumber) { this.cardNumber.unmount(); this.cardNumber = null; }
            if (this.cardExpiry) { this.cardExpiry.unmount(); this.cardExpiry = null; }
            if (this.cardCvc) { this.cardCvc.unmount(); this.cardCvc = null; }
            if (this.stripeElements) { this.stripeElements = null; }
        },

        async applyCoupon() {
            if (!this.couponCode.trim()) return;
            if (this.totalQuantity === 0) return;

            this.couponLoading = true;
            this.couponMessage = '';

            try {
                const ticketsPayload = Object.entries(this.tickets)
                    .filter(([, qty]) => qty > 0)
                    .map(([ttId, qty]) => ({ ticket_type_id: parseInt(ttId), quantity: qty }));

                const res = await fetch(checkoutData.routes['coupons.validate'], {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        code: this.couponCode.trim(),
                        event_id: eventId,
                        tickets: ticketsPayload,
                    }),
                });

                const data = await res.json();

                if (res.ok && data.valid) {
                    this.coupon = data;
                    this.couponMessage = '';
                } else {
                    this.coupon = null;
                    this.couponMessage = data.message || 'Invalid coupon code.';
                }
            } catch (e) {
                this.coupon = null;
                this.couponMessage = 'Failed to validate coupon. Please try again.';
            } finally {
                this.couponLoading = false;
            }
        },

        removeCoupon() {
            this.couponCode = '';
            this.coupon = null;
            this.couponMessage = '';
        },

        resetCoupon() {
            if (this.coupon) {
                this.coupon = null;
                this.couponMessage = '';
            }
        },

        async handlePayment() {
            if (!this.attendeesValid || this.totalQuantity < 1 || this.submitting) return;

            this.submitting = true;
            this.paymentError = '';

            try {
                const ticketsPayload = Object.entries(this.tickets)
                    .filter(([, qty]) => qty > 0)
                    .map(([ttId, qty]) => ({ ticket_type_id: parseInt(ttId), quantity: qty }));

                if (this.totalAfterDiscount <= 0) {
                    await this.submitFreeOrder(ticketsPayload);
                    return;
                }

                const intentRes = await fetch(checkoutData.routes['stripe.intent'], {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        tickets: ticketsPayload,
                        coupon_code: this.coupon && this.coupon.valid ? this.couponCode.trim() : '',
                        participants: this.participants,
                        reservation_type: this.reservationType,
                        company_name: this.companyName.trim(),
                        company_details: this.companyDetails.trim(),
                        contact_person: this.contactPerson.trim(),
                        attendees_later: this.attendeesLater,
                    }),
                });

                const intentData = await intentRes.json();

                if (!intentRes.ok) {
                    const errMsg = intentData.error
                        || (intentData.errors && Object.values(intentData.errors).flat().join(' '))
                        || intentData.message
                        || 'Failed to create payment. Please try again.';
                    throw new Error(errMsg);
                }

                this.clientSecret = intentData.client_secret;

                const { paymentIntent, error } = await this.stripe.confirmCardPayment(this.clientSecret, {
                    payment_method: {
                        card: this.cardNumber,
                        billing_details: {
                            name: this.cardName.trim(),
                            address: {
                                line1: this.billingAddress.trim(),
                                city: this.billingCity.trim(),
                                state: this.billingState.trim(),
                                postal_code: this.billingZip.trim(),
                                country: this.billingCountry,
                            },
                        },
                    },
                });

                if (error) {
                    throw new Error(error.message || 'Payment failed. Please try again.');
                }

                if (paymentIntent.status === 'succeeded') {
                    const confirmRes = await fetch(checkoutData.routes['stripe.confirm'], {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            payment_intent_id: paymentIntent.id,
                            event_slug: eventSlug,
                            tickets: ticketsPayload,
                            coupon_code: this.coupon && this.coupon.valid ? this.couponCode.trim() : '',
                            participants: this.participants,
                            reservation_type: this.reservationType,
                            company_name: this.companyName.trim(),
                            company_details: this.companyDetails.trim(),
                            contact_person: this.contactPerson.trim(),
                            attendees_later: this.attendeesLater,
                        }),
                    });

                    const confirmData = await confirmRes.json();

                    if (!confirmRes.ok) {
                        const errMsg = confirmData.error
                            || (confirmData.errors && Object.values(confirmData.errors).flat().join(' '))
                            || confirmData.message
                            || 'Failed to finalize order. Please contact support.';
                        throw new Error(errMsg);
                    }

                    if (confirmData.redirect) {
                        sessionStorage.removeItem('checkout_' + eventSlug);
                        window.location.href = confirmData.redirect;
                    }
                } else {
                    this.submitting = false;
                    this.paymentError = 'Payment was not completed. Status: ' + paymentIntent.status;
                }
            } catch (e) {
                this.submitting = false;
                this.paymentError = e.message || 'An unexpected error occurred.';
            }
        },

        async submitFreeOrder(ticketsPayload) {
            const form = this.$refs.freeOrderForm;
            const couponInput = this.$refs.freeCouponInput;

            form.querySelectorAll('.dynamic-field').forEach(el => el.remove());

            ticketsPayload.forEach((ticket, tIdx) => {
                const ttIdEl = document.createElement('input');
                ttIdEl.type = 'hidden';
                ttIdEl.name = `tickets[${tIdx}][ticket_type_id]`;
                ttIdEl.value = ticket.ticket_type_id;
                ttIdEl.className = 'dynamic-field';
                form.appendChild(ttIdEl);

                const qtyEl = document.createElement('input');
                qtyEl.type = 'hidden';
                qtyEl.name = `tickets[${tIdx}][quantity]`;
                qtyEl.value = ticket.quantity;
                qtyEl.className = 'dynamic-field';
                form.appendChild(qtyEl);
            });

            couponInput.value = this.coupon && this.coupon.valid ? this.couponCode.trim() : '';
            [['reservation_type', this.reservationType], ['company_name', this.companyName.trim()], ['company_details', this.companyDetails.trim()], ['contact_person', this.contactPerson.trim()], ['attendees_later', this.attendeesLater ? '1' : '0']].forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                input.className = 'dynamic-field';
                form.appendChild(input);
            });
            sessionStorage.removeItem('checkout_' + eventSlug);

            this.participants.forEach((p, idx) => {
                const nameEl = document.createElement('input');
                nameEl.type = 'hidden';
                nameEl.name = `participants[${idx}][name]`;
                nameEl.value = p.name;
                nameEl.className = 'dynamic-field';
                form.appendChild(nameEl);

                const emailEl = document.createElement('input');
                emailEl.type = 'hidden';
                emailEl.name = `participants[${idx}][email]`;
                emailEl.value = p.email;
                emailEl.className = 'dynamic-field';
                form.appendChild(emailEl);
            });

            form.submit();
        },

        submitReservation() {
            if (!this.attendeesValid || this.totalQuantity < 1 || this.reserving || this.submitting) return;
            if (!confirm('Reserve now and pay later? No tickets are issued until payment is recorded. We will contact you to arrange payment.')) return;

            this.reserving = true;
            this.paymentError = '';

            const form = this.$refs.reserveForm;
            const couponInput = this.$refs.reserveCouponInput;

            form.querySelectorAll('.dynamic-field').forEach(el => el.remove());

            Object.entries(this.tickets)
                .filter(([, qty]) => qty > 0)
                .forEach(([ttId, qty], tIdx) => {
                    [['ticket_type_id', parseInt(ttId)], ['quantity', qty]].forEach(([key, value]) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `tickets[${tIdx}][${key}]`;
                        input.value = value;
                        input.className = 'dynamic-field';
                        form.appendChild(input);
                    });
                });

            couponInput.value = this.coupon && this.coupon.valid ? this.couponCode.trim() : '';

            [['reservation_type', this.reservationType], ['company_name', this.companyName.trim()], ['company_details', this.companyDetails.trim()], ['contact_person', this.contactPerson.trim()], ['attendees_later', this.attendeesLater ? '1' : '0']].forEach(([name, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                input.className = 'dynamic-field';
                form.appendChild(input);
            });

            this.participants.forEach((p, idx) => {
                [['name', p.name], ['email', p.email]].forEach(([key, value]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `participants[${idx}][${key}]`;
                    input.value = value;
                    input.className = 'dynamic-field';
                    form.appendChild(input);
                });
            });

            sessionStorage.removeItem('checkout_' + eventSlug);
            form.submit();
        },

        formatCurrency(amount) {
            return amount.toLocaleString('en-US', { style: 'currency', currency: currency });
        },
    };
}
</script>
@endpush
