@extends('layouts.welcome')

@section('title', "Buyer's Guide — CayMark Island Exchange")

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════════════════ --}}
<section class="bg-primary text-white py-20 md:py-28 relative overflow-hidden">
    {{-- subtle grid texture --}}
    <div class="absolute inset-0 opacity-[0.04]"
         style="background-image:repeating-linear-gradient(0deg,#fff 0,#fff 1px,transparent 1px,transparent 40px),repeating-linear-gradient(90deg,#fff 0,#fff 1px,transparent 1px,transparent 40px)"></div>
    <div class="relative max-w-[1280px] mx-auto px-4 md:px-16 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 border border-white/20 bg-white/10 text-[11px] font-bold tracking-[0.2em] uppercase mb-8">
            <span class="material-symbols-outlined text-secondary-fixed-dim text-[16px]">menu_book</span>
            Getting Started
        </div>
        <h1 class="text-4xl md:text-6xl font-bold font-headline-lg uppercase tracking-tight mb-6">
            Buyer's<br/><span class="text-secondary-fixed-dim">Guide</span>
        </h1>
        <p class="text-white/70 text-lg max-w-2xl mx-auto font-body-lg mb-10">
            Everything you need to know to register, fund your account, bid on vehicles, and take delivery across the Bahamas.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('register') }}?role=buyer"
               class="px-8 py-4 bg-secondary-fixed-dim text-primary font-bold uppercase tracking-widest text-sm hover:bg-[#b8943b] transition-colors"
               style="border-radius:0">
                Register as Buyer
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
     HOW IT WORKS — 5 STEPS
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white border-b border-gray-100">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16">

        <div class="text-center mb-14">
            <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-3">The Process</p>
            <h2 class="text-3xl md:text-4xl font-bold text-primary uppercase tracking-tight font-headline-lg">How to Buy on CayMark</h2>
        </div>

        <div class="relative">
            {{-- connecting line --}}
            <div class="hidden lg:block absolute top-[52px] left-[calc(10%+28px)] right-[calc(10%+28px)] h-px bg-gray-200 z-0"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 relative z-10">

                @php
                $steps = [
                    ['num'=>'01','icon'=>'person_add','title'=>'Create Account','body'=>'Register with your email, select the Buyer role, and complete your profile verification.'],
                    ['num'=>'02','icon'=>'account_balance_wallet','title'=>'Fund Your Wallet','body'=>'Deposit funds into your CayMark wallet. Your available balance determines your bidding power.'],
                    ['num'=>'03','icon'=>'search','title'=>'Find a Vehicle','body'=>'Browse live auctions by make, model, island, or category. Add items to your watchlist to track them.'],
                    ['num'=>'04','icon'=>'gavel','title'=>'Place Your Bid','body'=>'Enter your maximum bid. CayMark auto-bids up to your limit. You\'ll be notified if outbid.'],
                    ['num'=>'05','icon'=>'local_shipping','title'=>'Win & Collect','body'=>'If you win, an invoice is generated. Complete payment and coordinate pickup directly with the seller.'],
                ];
                @endphp

                @foreach($steps as $step)
                <div class="flex flex-col items-center text-center group">
                    <div class="w-14 h-14 bg-primary flex items-center justify-center mb-5 flex-shrink-0 group-hover:bg-secondary-fixed-dim transition-colors duration-300"
                         style="border-radius:0">
                        <span class="material-symbols-outlined text-white text-[24px]">{{ $step['icon'] }}</span>
                    </div>
                    <div class="text-[10px] font-bold text-secondary-fixed-dim tracking-[0.25em] mb-1">{{ $step['num'] }}</div>
                    <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-3">{{ $step['title'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $step['body'] }}</p>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     MEMBERSHIP — SINGLE PLAN
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-[#f8fafd]">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16">

        <div class="text-center mb-14">
            <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-3">Membership</p>
            <h2 class="text-3xl md:text-4xl font-bold text-primary uppercase tracking-tight font-headline-lg">Buyer Access Plan</h2>
            <p class="text-gray-500 mt-3 max-w-lg mx-auto text-sm">One simple plan. Full access to every auction and marketplace listing across the Bahamas.</p>
        </div>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white border-t-4 border-secondary-fixed-dim shadow-xl" style="border-radius:0">
                <div class="bg-primary px-10 py-10 text-center">
                    <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.25em] mb-3">Annual Membership</p>
                    <h3 class="text-3xl font-bold text-white font-headline-md mb-1">Buyer Membership</h3>
                    <div class="flex items-end justify-center gap-1 mt-4">
                        <span class="text-5xl font-bold text-white">$64</span>
                        <span class="text-2xl font-bold text-white">.99</span>
                        <span class="text-white/50 text-sm mb-1 ml-1">/ year</span>
                    </div>
                </div>
                <div class="px-10 py-10">
                    <div class="grid sm:grid-cols-2 gap-4 mb-8">
                        @php
                        $features = [
                            ['icon'=>'gavel',           'text'=>'Full bidding access to all auctions'],
                            ['icon'=>'store',           'text'=>'Marketplace access & Buy Now feature'],
                            ['icon'=>'notifications',   'text'=>'Real-time outbid notifications'],
                            ['icon'=>'favorite',        'text'=>'Unlimited watchlist items'],
                            ['icon'=>'receipt_long',    'text'=>'Automatic invoice generation on win'],
                            ['icon'=>'forum',           'text'=>'Direct seller messaging after winning'],
                            ['icon'=>'dashboard',       'text'=>'Full buyer dashboard & bid history'],
                            ['icon'=>'account_balance_wallet','text'=>'Secure wallet & deposit system'],
                        ];
                        @endphp
                        @foreach($features as $f)
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px] flex-shrink-0 mt-0.5">{{ $f['icon'] }}</span>
                            <span class="text-sm text-gray-700 leading-snug">{{ $f['text'] }}</span>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('register') }}?role=buyer"
                       class="flex items-center justify-center gap-2 w-full py-4 bg-primary text-white font-bold uppercase tracking-widest text-sm hover:bg-[#003377] transition-colors"
                       style="border-radius:0">
                        <span class="material-symbols-outlined text-[18px]">person_add</span>
                        Get Started — Register Now
                    </a>
                    <p class="text-center text-xs text-gray-400 mt-4">Annual subscription. Renews each year. Cancel anytime.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     HOW BIDDING WORKS
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white border-t border-gray-100">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16">

        <div class="grid lg:grid-cols-2 gap-16 items-start">

            {{-- Left: explanation --}}
            <div>
                <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-3">Auction Rules</p>
                <h2 class="text-3xl font-bold text-primary uppercase tracking-tight font-headline-lg mb-6">How Bidding Works</h2>
                <p class="text-gray-600 text-sm leading-relaxed mb-8">
                    CayMark runs timed, live auctions. When you place a bid, the system competes up to your maximum limit automatically — you don't need to stay online.
                </p>

                <div class="space-y-5">
                    @php
                    $rules = [
                        ['icon'=>'timer','title'=>'Timed Auctions','body'=>'Every auction has a set end date and time. The highest bid when the clock runs out wins.'],
                        ['icon'=>'trending_up','title'=>'Auto-Bidding','body'=>'Set your maximum and CayMark bids incrementally on your behalf, keeping you in the lead up to your limit.'],
                        ['icon'=>'notifications_active','title'=>'Outbid Alerts','body'=>'You receive instant notifications when someone exceeds your bid so you can decide whether to raise.'],
                        ['icon'=>'account_balance_wallet','title'=>'Wallet-Backed Bids','body'=>'Your wallet balance must cover your bid amount. Funds are held while you\'re the leading bidder.'],
                        ['icon'=>'gavel','title'=>'Winning the Auction','body'=>'The highest bid at closing wins. A formal invoice is generated immediately and emailed to you.'],
                    ];
                    @endphp
                    @foreach($rules as $r)
                    <div class="flex gap-4">
                        <div class="w-10 h-10 bg-[#f0f4fb] flex items-center justify-center flex-shrink-0" style="border-radius:0">
                            <span class="material-symbols-outlined text-primary text-[20px]">{{ $r['icon'] }}</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-primary uppercase tracking-wider mb-1">{{ $r['title'] }}</h4>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ $r['body'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: payment & pickup --}}
            <div>
                <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-3">After You Win</p>
                <h2 class="text-3xl font-bold text-primary uppercase tracking-tight font-headline-lg mb-6">Payment &amp; Pickup</h2>

                <div class="space-y-4 mb-8">
                    @php
                    $pickup = [
                        ['step'=>'01','title'=>'Invoice Generated','body'=>'An invoice is automatically created the moment the auction closes. Check your dashboard and email.'],
                        ['step'=>'02','title'=>'Complete Payment','body'=>'Pay directly via your CayMark wallet. Payment must be completed within the specified window to secure the vehicle.'],
                        ['step'=>'03','title'=>'Messaging Unlocked','body'=>'Once payment clears, a secure messaging thread opens between you and the seller to arrange handover.'],
                        ['step'=>'04','title'=>'Vehicle Handover','body'=>'Coordinate pickup location and time. Confirm receipt through the platform to close the transaction.'],
                    ];
                    @endphp
                    @foreach($pickup as $p)
                    <div class="border border-gray-100 p-5 flex gap-4 hover:border-primary hover:bg-[#f8fafd] transition-colors" style="border-radius:0">
                        <span class="text-[11px] font-bold text-secondary-fixed-dim tracking-widest w-8 flex-shrink-0 mt-0.5">{{ $p['step'] }}</span>
                        <div>
                            <h4 class="text-sm font-bold text-primary uppercase tracking-wider mb-1">{{ $p['title'] }}</h4>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ $p['body'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="bg-[#fff8e7] border-l-4 border-secondary-fixed-dim p-5" style="border-radius:0">
                    <div class="flex gap-3">
                        <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px] flex-shrink-0 mt-0.5">info</span>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <strong class="text-primary">Buyer's Note:</strong> The seller retains a 4% commission (minimum $150) which is deducted from the sale proceeds — not from the buyer. You pay only the winning bid amount.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     FAQ
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-[#f8fafd] border-t border-gray-100">
    <div class="max-w-[900px] mx-auto px-4 md:px-16">

        <div class="text-center mb-14">
            <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-3">FAQ</p>
            <h2 class="text-3xl font-bold text-primary uppercase tracking-tight font-headline-lg">Common Questions</h2>
        </div>

        @php
        $faqs = [
            ['q'=>'What do I need to register as a buyer?','a'=>'A valid email address and basic personal details. After registration you will be asked to verify your identity before placing bids.'],
            ['q'=>'How does the wallet work?','a'=>'You deposit funds via supported payment methods into your CayMark wallet. When you bid, the corresponding amount is held and released if you\'re outbid.'],
            ['q'=>'Can I bid on multiple auctions at once?','a'=>'Yes. With an active Buyer Membership you can hold bids on as many open auctions as your wallet balance supports.'],
            ['q'=>'What happens if I win but don\'t pay?','a'=>'Failure to complete payment within the specified window may result in forfeiture of the item and suspension of your bidding privileges.'],
            ['q'=>'Is there a buyer\'s premium on top of my winning bid?','a'=>'No buyer\'s premium is added. You pay exactly the amount of your winning bid. The 4% commission is charged to the seller.'],
            ['q'=>'Can I inspect the vehicle before bidding?','a'=>'Vehicle photos, videos, condition reports, and island location are listed on each auction page. Physical inspections depend on seller and island availability.'],
        ];
        @endphp

        <div class="space-y-3" id="faq-list">
            @foreach($faqs as $i => $faq)
            <div class="bg-white border border-gray-200 overflow-hidden" style="border-radius:0">
                <button type="button"
                    class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none faq-btn"
                    data-target="faq-{{ $i }}">
                    <span class="text-sm font-bold text-primary uppercase tracking-wide pr-4">{{ $faq['q'] }}</span>
                    <span class="material-symbols-outlined text-gray-400 text-[20px] flex-shrink-0 faq-icon transition-transform duration-200">expand_more</span>
                </button>
                <div id="faq-{{ $i }}" class="hidden px-6 pb-5 border-t border-gray-100">
                    <p class="text-sm text-gray-600 leading-relaxed pt-4">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     BOTTOM CTA
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-primary text-white">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16 text-center">
        <h2 class="text-3xl md:text-4xl font-bold uppercase tracking-tight font-headline-lg mb-4">
            Ready to Start Bidding?
        </h2>
        <p class="text-white/60 max-w-xl mx-auto text-sm mb-10">
            Join CayMark today and access hundreds of vehicles, fleet assets, and marine listings across the Bahamian archipelago.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('register') }}?role=buyer"
               class="px-10 py-4 bg-secondary-fixed-dim text-primary font-bold uppercase tracking-widest text-sm hover:bg-[#b8943b] transition-colors"
               style="border-radius:0">
                Create Buyer Account
            </a>
            <a href="{{ route('Auction.index') }}"
               class="px-10 py-4 border border-white/30 text-white font-bold uppercase tracking-widest text-sm hover:bg-white/10 transition-colors"
               style="border-radius:0">
                Browse Live Auctions
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.querySelectorAll('.faq-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var targetId = this.getAttribute('data-target');
        var panel    = document.getElementById(targetId);
        var icon     = this.querySelector('.faq-icon');
        var isOpen   = !panel.classList.contains('hidden');
        // Close all
        document.querySelectorAll('.faq-btn').forEach(function(b) {
            var t = document.getElementById(b.getAttribute('data-target'));
            var i = b.querySelector('.faq-icon');
            if (t) t.classList.add('hidden');
            if (i) i.style.transform = '';
        });
        // Open clicked if it was closed
        if (!isOpen) {
            panel.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        }
    });
});
</script>
@endpush

@endsection
