@extends('layouts.welcome')

@section('title', 'Fee Calculator — CayMark Island Exchange')

@section('content')

@php
    $depositThreshold = 2000;  // bids at or above this require a security deposit
    $depositPercent   = 10;    // % of bid held as security deposit
    $buyerFeeRate     = 6;     // % buyer fee on top of vehicle price
    $buyerFeeMin      = 100;   // minimum buyer fee
@endphp

{{-- ══════════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════════ --}}
<section class="bg-primary text-white py-20 md:py-28 relative overflow-hidden">
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image:repeating-linear-gradient(0deg,#fff 0,#fff 1px,transparent 1px,transparent 40px),repeating-linear-gradient(90deg,#fff 0,#fff 1px,transparent 1px,transparent 40px)"></div>
    <div class="relative max-w-[1280px] mx-auto px-4 md:px-16 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 border border-white/20 bg-white/10 text-[11px] font-bold tracking-[0.2em] uppercase mb-8">
            <span class="material-symbols-outlined text-secondary-fixed-dim text-[16px]">calculate</span>
            Services &amp; Support
        </div>
        <h1 class="text-4xl md:text-6xl font-bold font-headline-lg uppercase tracking-tight mb-6">
            Fee<br/><span class="text-secondary-fixed-dim">Calculator</span>
        </h1>
        <p class="text-white/70 text-lg max-w-2xl mx-auto font-body-lg mb-10">
            Know exactly what you need in your wallet before bidding — no surprises at checkout.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('buyer-guide') }}"
               class="px-8 py-4 bg-secondary-fixed-dim text-primary font-bold uppercase tracking-widest text-sm hover:bg-[#b8943b] transition-colors"
               style="border-radius:0">
                Buyer's Guide
            </a>
            <a href="{{ route('Auction.index') }}"
               class="px-8 py-4 border-2 border-white/40 text-white font-bold uppercase tracking-widest text-sm hover:bg-white/10 transition-colors"
               style="border-radius:0">
                Browse Auctions
            </a>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     INTERACTIVE CALCULATOR
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white" x-data="feeCalc()">
    <div class="max-w-[860px] mx-auto px-4 md:px-8">

        {{-- ── BUYER CALCULATOR ─────────────────────────────────── --}}
        <div>
            <div class="mb-8">
                <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-2">Buyer Estimate</p>
                <h2 class="text-2xl font-bold text-primary uppercase tracking-tight font-headline-md mb-2">Fee Calculator</h2>
                <p class="text-gray-500 text-sm">Enter the amount you plan to bid (or the vehicle price) to see your security deposit, the total you pay for the item, and CayMark's fees.</p>
            </div>

            {{-- Input --}}
            <div class="mb-8">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Amount ($)</label>
                <div class="flex items-stretch border-2 border-gray-200 focus-within:border-primary transition-colors duration-200" style="border-radius:0">
                    <span class="flex items-center px-5 text-xl font-bold text-gray-300 border-r-2 border-gray-200 select-none bg-gray-50">$</span>
                    <input type="number" min="0" step="100" placeholder="0"
                           x-model.number="bidAmount"
                           @input="bidAmount = $event.target.value ? parseFloat($event.target.value) : 0"
                           class="flex-1 px-5 py-4 text-2xl font-bold text-primary bg-transparent focus:outline-none placeholder-gray-200"
                           style="border-radius:0"/>
                    <span class="flex items-center px-4 text-xs font-bold text-gray-300 uppercase tracking-wider select-none bg-gray-50">USD</span>
                </div>
            </div>

            {{-- Results --}}
            <div x-show="bidAmount > 0" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                <div class="border border-gray-100 overflow-hidden mb-6" style="border-radius:0">

                    {{-- 1. Security deposit --}}
                    <div class="flex justify-between items-start px-6 py-5 border-b border-gray-100"
                         :class="bidAmount >= {{ $depositThreshold }} ? 'bg-amber-50' : ''">
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[11px] font-bold mt-0.5"
                                  :class="bidAmount >= {{ $depositThreshold }} ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-500'">1</span>
                            <div>
                                <span class="text-sm font-semibold text-gray-800">Security deposit <span class="font-normal text-gray-400">(on your account)</span></span>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="bidAmount >= {{ $depositThreshold }}
                                    ? '{{ $depositPercent }}% of your bid — held in your wallet before bidding. Applied to your payment if you win.'
                                    : 'No deposit required for bids under ${{ number_format($depositThreshold) }}.'">
                                </p>
                            </div>
                        </div>
                        <span class="ml-8 flex-shrink-0 text-base font-bold"
                              :class="bidAmount >= {{ $depositThreshold }} ? 'text-amber-600' : 'text-green-500'"
                              x-text="bidAmount >= {{ $depositThreshold }} ? fmt(deposit) : 'None'"></span>
                    </div>

                    {{-- 2. Vehicle price --}}
                    <div class="flex justify-between items-start px-6 py-5 border-b border-gray-100">
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[11px] font-bold mt-0.5">2</span>
                            <div>
                                <span class="text-sm font-semibold text-gray-800">What you pay for the vehicle</span>
                                <p class="text-xs text-gray-400 mt-0.5">Your winning bid amount — paid directly at close.</p>
                            </div>
                        </div>
                        <span class="ml-8 flex-shrink-0 text-base font-bold text-primary" x-text="fmt(bidAmount)"></span>
                    </div>

                    {{-- 3. CayMark buyer fee --}}
                    <div class="flex justify-between items-start px-6 py-5 border-b border-gray-100 bg-gray-50">
                        <div class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-[11px] font-bold mt-0.5">3</span>
                            <div>
                                <span class="text-sm font-semibold text-gray-800">CayMark fees <span class="font-normal text-gray-400">(on top of vehicle price)</span></span>
                                <p class="text-xs text-gray-400 mt-0.5"
                                   x-text="'{{ $buyerFeeRate }}% of the sale price' + (rawBuyerFee < {{ $buyerFeeMin }} ? ' — minimum ${{ number_format($buyerFeeMin) }} applies.' : '.')"></p>
                            </div>
                        </div>
                        <span class="ml-8 flex-shrink-0 text-base font-bold text-primary" x-text="fmt(buyerFee)"></span>
                    </div>

                    {{-- Total bar --}}
                    <div class="flex justify-between items-center px-6 py-5 bg-primary">
                        <div>
                            <span class="text-sm font-bold text-white uppercase tracking-wider">Total due at checkout</span>
                            <p class="text-xs text-white/50 mt-0.5">Vehicle price + CayMark fees</p>
                        </div>
                        <span class="text-2xl font-bold text-secondary-fixed-dim ml-8 flex-shrink-0" x-text="fmt(total)"></span>
                    </div>

                </div>

                {{-- How it works --}}
                <div class="border border-gray-100 p-5" style="border-radius:0">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-3">How it works</p>
                    <ul class="space-y-2.5">
                        <li class="flex items-start gap-2.5 text-xs text-gray-600">
                            <span class="material-symbols-outlined text-secondary-fixed-dim flex-shrink-0" style="font-size:15px;margin-top:1px">check_circle</span>
                            Bids of ${{ number_format($depositThreshold) }}+ require a {{ $depositPercent }}% security deposit in your account.
                        </li>
                        <li class="flex items-start gap-2.5 text-xs text-gray-600">
                            <span class="material-symbols-outlined text-secondary-fixed-dim flex-shrink-0" style="font-size:15px;margin-top:1px">check_circle</span>
                            CayMark's buyer fee is {{ $buyerFeeRate }}% of the sale price (minimum ${{ number_format($buyerFeeMin) }}).
                        </li>
                        <li class="flex items-start gap-2.5 text-xs text-gray-600">
                            <span class="material-symbols-outlined text-secondary-fixed-dim flex-shrink-0" style="font-size:15px;margin-top:1px">check_circle</span>
                            Deposit is applied toward your final payment if you win.
                        </li>
                    </ul>
                </div>
            </div>

            {{-- empty state --}}
            <div x-show="bidAmount <= 0" class="border border-dashed border-gray-200 p-10 text-center text-gray-300" style="border-radius:0">
                <span class="material-symbols-outlined text-[40px] block mb-3">calculate</span>
                <p class="text-sm">Enter a bid amount above to see your estimate.</p>
            </div>

        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     BOTTOM CTA
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-primary text-white">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16 text-center">
        <h2 class="text-3xl md:text-4xl font-bold uppercase tracking-tight font-headline-lg mb-4">
            Ready to Get Started?
        </h2>
        <p class="text-white/60 max-w-xl mx-auto text-sm mb-10">
            No buyer's premium, no hidden fees. Register today and start bidding with full confidence.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('register') }}?role=buyer"
               class="px-10 py-4 bg-secondary-fixed-dim text-primary font-bold uppercase tracking-widest text-sm hover:bg-[#b8943b] transition-colors"
               style="border-radius:0">
                Register as Buyer
            </a>
            <a href="{{ route('Auction.index') }}"
               class="px-10 py-4 border border-white/30 text-white font-bold uppercase tracking-widest text-sm hover:bg-white/10 transition-colors"
               style="border-radius:0">
                Browse Auctions
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
function feeCalc() {
    return {
        bidAmount: 0,

        get deposit() {
            if (this.bidAmount >= {{ $depositThreshold }}) {
                return Math.round(this.bidAmount * ({{ $depositPercent }} / 100) * 100) / 100;
            }
            return 0;
        },

        get rawBuyerFee() {
            return Math.round(this.bidAmount * ({{ $buyerFeeRate }} / 100) * 100) / 100;
        },

        get buyerFee() {
            return Math.max(this.rawBuyerFee, {{ $buyerFeeMin }});
        },

        get total() {
            return Math.round((this.bidAmount + this.buyerFee) * 100) / 100;
        },

        fmt(n) {
            return '$' + Number(n).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    };
}
</script>
@endpush

@endsection
