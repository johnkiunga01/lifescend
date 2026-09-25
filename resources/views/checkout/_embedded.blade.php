{{-- Embedded Checkout for Event Page Tab --}}
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-xl font-extrabold text-slate-900 mb-1">Get Your Tickets</h2>
    <p class="text-sm text-slate-500 mb-6">Select tickets and complete your registration.</p>

    {{-- Ticket Selection --}}
    <div class="space-y-4 mb-8">
        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Select Tickets</h3>
        @foreach($event->ticketTypes as $ticketType)
        @php
            $remaining = $ticketType->quantity - $ticketType->sold_count;
            $isSoldOut = $remaining <= 0;
            $isOnSale = (!$ticketType->sales_start || $ticketType->sales_start->isPast())
                && (!$ticketType->sales_end || $ticketType->sales_end->isFuture());
        @endphp
        <div class="rounded-xl border {{ $isSoldOut ? 'border-slate-200 opacity-60' : 'border-slate-200 hover:border-gold-300' }} bg-slate-50 p-4 transition-all duration-200"
            :class="{ 'ring-2 ring-gold-400 border-gold-400': tickets[{{ $ticketType->id }}] > 0 }">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h4 class="font-bold text-slate-900">{{ $ticketType->name }}</h4>
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
                    <p class="text-xs text-slate-500 mb-2">{{ $ticketType->description }}</p>
                    @endif
                    @if($ticketType->highlights)
                    <ul class="space-y-0.5 mb-2">
                        @foreach($ticketType->highlights as $highlight)
                        <li class="flex items-center gap-1.5 text-[11px] text-slate-600">
                            <svg class="h-3 w-3 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            {{ $highlight }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <div class="text-right shrink-0">
                    @if($ticketType->payablePrice() > 0)
                        <p class="text-lg font-extrabold text-slate-900">{{ number_format($ticketType->payablePrice(), 2) }} <span class="text-xs font-semibold text-slate-400">{{ $ticketType->checkoutCurrency() }}</span></p>
                        @if($ticketType->usdReferencePrice() !== null)
                        <p class="text-xs text-slate-500">{{ number_format($ticketType->usdReferencePrice(), 2) }} USD reference</p>
                        @endif
                    @else
                        <p class="text-lg font-extrabold text-emerald-600">Free</p>
                    @endif
                    @if(!$isSoldOut && $isOnSale)
                    <div class="mt-2 flex items-center gap-1.5 justify-end">
                        <button type="button" @click="decrementTicket({{ $ticketType->id }})"
                            class="h-8 w-8 rounded-lg border border-slate-300 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors"
                            :disabled="(tickets[{{ $ticketType->id }}] || 0) <= 0">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                        </button>
                        <span class="w-8 text-center font-bold text-slate-900 text-sm" x-text="tickets[{{ $ticketType->id }}] || 0"></span>
                        <button type="button" @click="incrementTicket({{ $ticketType->id }}, {{ $remaining }}, {{ $ticketType->max_per_user }})"
                            class="h-8 w-8 rounded-lg border border-slate-300 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors"
                            :disabled="(tickets[{{ $ticketType->id }}] || 0) >= {{ min($remaining, $ticketType->max_per_user) }}">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Attendee Details --}}
    <div x-show="totalQuantity > 0" class="mb-8">
        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-4">Attendee Details</h3>
        <div class="space-y-4">
            <template x-for="(slot, idx) in attendeeSlots" :key="idx">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center gap-2 mb-3 pb-2 border-b border-slate-200">
                        <div class="h-6 w-6 rounded-md bg-gold-100 flex items-center justify-center text-xs font-bold text-gold-700">
                            <span x-text="idx + 1"></span>
                        </div>
                        <span class="font-semibold text-slate-900 text-xs" x-text="'Ticket #' + (idx + 1) + ' - ' + slot.ticketName"></span>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" x-model="participants[idx].name" required maxlength="255"
                                placeholder="e.g. Juan Dela Cruz"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" x-model="participants[idx].email" required maxlength="255"
                                placeholder="attendee@example.com"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                        </div>
                    </div>
                    <input type="hidden" x-model="participants[idx].ticket_type_id">
                </div>
            </template>
        </div>
    </div>

    {{-- Order Summary --}}
    <div x-show="totalQuantity > 0" class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-6">
        <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide mb-3">Order Summary</h3>
        <div class="space-y-2 mb-3">
            <template x-for="(qty, ttId) in tickets" :key="ttId">
                <div x-show="qty > 0" class="flex items-center justify-between text-sm">
                    <span class="text-slate-600"><span x-text="ticketTypeNames[ttId]"></span> x<span x-text="qty"></span></span>
                    <span class="font-semibold text-slate-700" x-text="formatCurrency(ticketTypePrices[ttId] * qty)"></span>
                </div>
            </template>
        </div>
        <div class="border-t border-slate-200 pt-3 space-y-1.5">
            <div class="flex items-center justify-between text-sm">
                <span class="text-slate-500">Subtotal</span>
                <span class="font-semibold" x-text="formatCurrency(totalAmount)"></span>
            </div>
            <div x-show="coupon && coupon.valid" class="flex items-center justify-between text-sm">
                <span class="text-emerald-600">Discount</span>
                <span class="font-semibold text-emerald-600" x-text="coupon ? '-' + formatCurrency(coupon.discount) : ''"></span>
            </div>
            <div class="flex items-center justify-between text-base pt-2 border-t border-slate-200">
                <span class="font-bold text-slate-900">Total</span>
                <span class="font-extrabold text-slate-900" x-text="formatCurrency(totalAfterDiscount)"></span>
            </div>
        </div>
    </div>

    {{-- Coupon --}}
    <div x-show="totalQuantity > 0" class="mb-6">
        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wide mb-2">Coupon Code</label>
        <div class="flex gap-2">
            <input type="text" x-model="couponCode" @keyup.enter="applyCoupon()"
                placeholder="Enter code" maxlength="50"
                class="block flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none uppercase">
            <button type="button" @click="applyCoupon()"
                :disabled="couponLoading || !couponCode.trim()"
                class="rounded-lg bg-slate-800 px-4 py-2 text-xs font-bold text-white hover:bg-slate-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span x-show="!couponLoading">Apply</span>
                <span x-show="couponLoading" class="inline-flex items-center gap-1">
                    <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                </span>
            </button>
        </div>
        <div x-show="couponMessage" class="mt-2">
            <div x-show="coupon && coupon.valid" class="flex items-center gap-2 rounded-lg bg-emerald-50 border border-emerald-200 px-3 py-2 text-xs font-semibold text-emerald-700">
                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="couponCode.toUpperCase()"></span> applied!
                <button type="button" @click="removeCoupon()" class="ml-auto text-emerald-500 hover:text-emerald-700"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div x-show="couponMessage && (!coupon || !coupon.valid)" class="rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-xs font-semibold text-red-700" x-text="couponMessage"></div>
        </div>
    </div>

    {{-- Payment --}}
    <div x-show="totalQuantity > 0 && totalAfterDiscount > 0" class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">Payment Details</h3>
            <span class="flex items-center gap-1 text-[10px] font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-md px-2 py-1">
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                SSL Secure
            </span>
        </div>

        {{-- Card Preview --}}
        <div class="relative mb-4 rounded-lg bg-gradient-to-br from-slate-800 to-slate-900 p-4 overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-gold-500/10 rounded-full -translate-y-1/2 translate-x-1/4 blur-2xl"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[9px] font-semibold uppercase tracking-widest text-slate-400">Card</span>
                    <template x-if="cardBrand">
                        <span class="text-xs font-bold text-white" x-text="cardBrand === 'visa' ? 'VISA' : cardBrand === 'mastercard' ? 'Mastercard' : cardBrand === 'amex' ? 'AMEX' : ''"></span>
                    </template>
                    <template x-if="!cardBrand">
                        <svg class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </template>
                </div>
                <div x-text="cardDisplayNumber || '•••• •••• •••• ••••'" class="text-base tracking-wider font-bold text-white mb-3 font-mono"></div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[8px] uppercase tracking-widest text-slate-400 mb-0.5">Holder</p>
                        <p x-text="cardName || 'Your Name'" class="text-[11px] font-semibold text-white"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[8px] uppercase tracking-widest text-slate-400 mb-0.5">Expires</p>
                        <p x-text="cardExpiryDisplay || 'MM/YY'" class="text-[11px] font-semibold text-white"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Card Number <span class="text-red-500">*</span></label>
                <div id="card-number-element" class="stripe-card-embedded rounded-lg border border-slate-300 bg-white px-3 py-2.5 min-h-[40px] transition-all duration-200"></div>
                <div id="card-number-errors" class="mt-1 text-[10px] font-semibold text-red-600 min-h-[14px]"></div>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Name on Card <span class="text-red-500">*</span></label>
                <input type="text" x-model="cardName" placeholder="John Doe"
                    class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none transition-all duration-200">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Expiry <span class="text-red-500">*</span></label>
                <div id="card-expiry-element" class="stripe-card-embedded rounded-lg border border-slate-300 bg-white px-3 py-2.5 min-h-[40px] transition-all duration-200"></div>
                <div id="card-expiry-errors" class="mt-1 text-[10px] font-semibold text-red-600 min-h-[14px]"></div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">CVC <span class="text-red-500">*</span></label>
                <div id="card-cvc-element" class="stripe-card-embedded rounded-lg border border-slate-300 bg-white px-3 py-2.5 min-h-[40px] transition-all duration-200"></div>
                <div id="card-cvc-errors" class="mt-1 text-[10px] font-semibold text-red-600 min-h-[14px]"></div>
            </div>
        </div>

        {{-- Secured by --}}
        <div class="mt-4 rounded-lg bg-gradient-to-r from-slate-50 to-slate-100 border border-slate-200 p-3">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <div class="h-7 w-7 rounded-md bg-emerald-100 flex items-center justify-center shrink-0">
                        <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Secured by <span class="text-gold-600">Stripe</span></p>
                        <p class="text-[10px] text-slate-500">256-bit SSL encrypted</p>
                    </div>
                </div>
                <span class="px-2 py-1 rounded bg-white border border-slate-200 text-[9px] font-bold text-slate-500 uppercase">PCI DSS</span>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div x-show="totalQuantity > 0" class="flex items-center justify-between pt-4 border-t border-slate-200">
        <p x-show="!attendeesValid" class="text-xs text-amber-600 font-medium">Please fill in all attendee details.</p>
        <p x-show="attendeesValid && totalAfterDiscount <= 0" class="text-xs text-emerald-600 font-medium">Free registration — no payment needed.</p>
        <button type="button" @click="handlePayment()"
            :disabled="submitting || !attendeesValid"
            class="rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:from-gold-600 hover:to-gold-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed ml-auto">
            <span x-show="!submitting">
                <span x-show="totalAfterDiscount > 0" x-text="'Pay ' + formatCurrency(totalAfterDiscount)"></span>
                <span x-show="totalAfterDiscount <= 0">Complete Registration</span>
            </span>
            <span x-show="submitting" class="inline-flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                Processing...
            </span>
        </button>
    </div>

    <div x-show="paymentError" class="mt-4 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm font-semibold text-red-700" x-text="paymentError"></div>

    <div x-show="totalQuantity === 0" class="text-center py-8">
        <svg class="h-12 w-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
        <p class="text-sm font-semibold text-slate-400">Select your tickets above to get started</p>
    </div>
</div>

{{-- Hidden form for free orders --}}
<form x-ref="freeOrderForm" action="{{ route('events.purchase', $event->slug) }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="coupon_code" x-ref="freeCouponInput">
</form>
