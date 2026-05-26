@extends('layouts.welcome')

@section('title', "Seller's Guide — CayMark Island Exchange")

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
            <span class="material-symbols-outlined text-secondary-fixed-dim text-[16px]">sell</span>
            Getting Started
        </div>
        <h1 class="text-4xl md:text-6xl font-bold font-headline-lg uppercase tracking-tight mb-6">
            Seller's<br/><span class="text-secondary-fixed-dim">Guide</span>
        </h1>
        <p class="text-white/70 text-lg max-w-2xl mx-auto font-body-lg mb-10">
            Everything you need to know to list your vehicles, reach verified buyers, and earn across the Bahamas — whether you're an individual or a business.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('register') }}?role=seller"
               class="px-8 py-4 bg-secondary-fixed-dim text-primary font-bold uppercase tracking-widest text-sm hover:bg-[#b8943b] transition-colors"
               style="border-radius:0">
                Register as Seller
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
     HOW IT WORKS — 3 STEPS
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white border-b border-gray-100">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16">

        <div class="text-center mb-14">
            <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-3">The Process</p>
            <h2 class="text-3xl md:text-4xl font-bold text-primary uppercase tracking-tight font-headline-lg">How to Sell on CayMark</h2>
        </div>

        <div class="relative">
            {{-- connecting line --}}
            <div class="hidden lg:block absolute top-[52px] left-[calc(16.66%+28px)] right-[calc(16.66%+28px)] h-px bg-gray-200 z-0"></div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-10 relative z-10 max-w-4xl mx-auto">

                @php
                $steps = [
                    ['num'=>'01','icon'=>'how_to_reg','title'=>'Register & Choose Plan','body'=>'Create your account, select the Seller role, and choose between the Individual or Business plan. Verification is reviewed by our team.'],
                    ['num'=>'02','icon'=>'add_photo_alternate','title'=>'Submit Your Listing','body'=>'Access your seller dashboard, provide vehicle details, upload required photos and video, and attach ownership documentation.'],
                    ['num'=>'03','icon'=>'payments','title'=>'Get Approved & Earn','body'=>'Our admin team reviews your listing within 24 hours. Once approved it goes live. When sold, your proceeds are processed minus the 4% commission.'],
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
     SELLER PLANS — INDIVIDUAL vs BUSINESS
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-[#f8fafd]">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16">

        <div class="text-center mb-14">
            <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-3">Seller Plans</p>
            <h2 class="text-3xl md:text-4xl font-bold text-primary uppercase tracking-tight font-headline-lg">Choose Your Plan</h2>
            <p class="text-gray-500 mt-3 max-w-lg mx-auto text-sm">Both plans include full access to CayMark's auction and marketplace platform. A 4% commission (minimum $150) applies on each successful sale.</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 max-w-4xl mx-auto">

            {{-- ── Individual Seller ──────────────────────────── --}}
            <div class="bg-white border-t-4 border-outline shadow-md" style="border-radius:0">
                <div class="bg-[#1b3a6b] px-8 py-8 text-center">
                    <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.25em] mb-3">For Private Sellers</p>
                    <h3 class="text-2xl font-bold text-white font-headline-md mb-1">Individual Seller</h3>
                    <div class="flex items-end justify-center gap-1 mt-4">
                        <span class="text-5xl font-bold text-white">Free</span>
                    </div>
                    <p class="text-white/50 text-xs mt-2">No subscription required</p>
                </div>
                <div class="px-8 py-8">
                    @php
                    $indFeatures = [
                        ['icon'=>'check_circle','text'=>'Free registration — no annual fee','ok'=>true],
                        ['icon'=>'check_circle','text'=>'Unlimited listing submissions','ok'=>true],
                        ['icon'=>'check_circle','text'=>'Set Buy Now, Reserve, or Starting Bid price','ok'=>true],
                        ['icon'=>'check_circle','text'=>'Full seller dashboard access','ok'=>true],
                        ['icon'=>'check_circle','text'=>'4% commission (min $150) on each sale','ok'=>true],
                        ['icon'=>'cancel',      'text'=>'No free relisting after expiry','ok'=>false],
                        ['icon'=>'cancel',      'text'=>'No dedicated account manager','ok'=>false],
                        ['icon'=>'cancel',      'text'=>'No advanced listing tools','ok'=>false],
                    ];
                    @endphp
                    <div class="space-y-3 mb-8">
                        @foreach($indFeatures as $f)
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[20px] flex-shrink-0 mt-0.5 {{ $f['ok'] ? 'text-green-500' : 'text-gray-300' }}">{{ $f['icon'] }}</span>
                            <span class="text-sm {{ $f['ok'] ? 'text-gray-700' : 'text-gray-400' }} leading-snug">{{ $f['text'] }}</span>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('register') }}?role=seller&plan=individual"
                       class="flex items-center justify-center gap-2 w-full py-4 bg-[#1b3a6b] text-white font-bold uppercase tracking-widest text-sm hover:bg-primary transition-colors"
                       style="border-radius:0">
                        <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                        Get Started — Free
                    </a>
                    <p class="text-center text-xs text-gray-400 mt-4">No credit card required to register.</p>
                </div>
            </div>

            {{-- ── Business Seller ────────────────────────────── --}}
            <div class="bg-white border-t-4 border-secondary-fixed-dim shadow-xl relative" style="border-radius:0">
                {{-- Most Popular badge --}}
                <div class="absolute -top-4 left-1/2 -translate-x-1/2 z-10">
                    <div class="px-5 py-1.5 bg-secondary-fixed-dim text-primary text-[10px] font-bold uppercase tracking-widest" style="border-radius:0">
                        Best for Dealers
                    </div>
                </div>
                <div class="bg-primary px-8 py-8 text-center">
                    <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.25em] mb-3">For Dealers & Businesses</p>
                    <h3 class="text-2xl font-bold text-white font-headline-md mb-1">Business Seller</h3>
                    <div class="flex items-end justify-center gap-1 mt-4">
                        <span class="text-5xl font-bold text-white">$599</span>
                        <span class="text-2xl font-bold text-white">.99</span>
                        <span class="text-white/50 text-sm mb-1 ml-1">/ year</span>
                    </div>
                    <p class="text-white/50 text-xs mt-2">Annual subscription</p>
                </div>
                <div class="px-8 py-8">
                    @php
                    $bizFeatures = [
                        ['icon'=>'check_circle','text'=>'Unlimited listing submissions','ok'=>true],
                        ['icon'=>'check_circle','text'=>'Free relisting within 48 hours of expiry','ok'=>true],
                        ['icon'=>'check_circle','text'=>'Set Buy Now, Reserve, or Starting Bid price','ok'=>true],
                        ['icon'=>'check_circle','text'=>'Dedicated account manager','ok'=>true],
                        ['icon'=>'check_circle','text'=>'Advanced listing & management tools','ok'=>true],
                        ['icon'=>'check_circle','text'=>'Full seller dashboard access','ok'=>true],
                        ['icon'=>'check_circle','text'=>'Priority listing review','ok'=>true],
                        ['icon'=>'check_circle','text'=>'4% commission (min $150) on each sale','ok'=>true],
                    ];
                    @endphp
                    <div class="space-y-3 mb-8">
                        @foreach($bizFeatures as $f)
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[20px] flex-shrink-0 mt-0.5 {{ $f['ok'] ? 'text-green-500' : 'text-gray-300' }}">{{ $f['icon'] }}</span>
                            <span class="text-sm {{ $f['ok'] ? 'text-gray-700' : 'text-gray-400' }} leading-snug">{{ $f['text'] }}</span>
                        </div>
                        @endforeach
                    </div>
                    <a href="{{ route('register') }}?role=seller&plan=business"
                       class="flex items-center justify-center gap-2 w-full py-4 bg-primary text-white font-bold uppercase tracking-widest text-sm hover:bg-[#003377] transition-colors"
                       style="border-radius:0">
                        <span class="material-symbols-outlined text-[18px]">business</span>
                        Register Business Account
                    </a>
                    <p class="text-center text-xs text-gray-400 mt-4">Annual subscription. Renews each year. Cancel anytime.</p>
                </div>
            </div>

        </div>

        {{-- Commission note --}}
        <div class="max-w-4xl mx-auto mt-8">
            <div class="bg-[#fff8e7] border-l-4 border-secondary-fixed-dim p-5" style="border-radius:0">
                <div class="flex gap-3">
                    <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px] flex-shrink-0 mt-0.5">info</span>
                    <p class="text-sm text-gray-700 leading-relaxed">
                        <strong class="text-primary">Commission Note:</strong> A 4% platform commission with a minimum of $150 is applied to each successfully sold listing. This is deducted from the seller's proceeds — buyers pay only the winning bid amount.
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════
     LISTING REQUIREMENTS
══════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-white border-t border-gray-100">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16">

        <div class="grid lg:grid-cols-2 gap-16 items-start">

            {{-- Left: required vehicle info --}}
            <div>
                <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-3">Listing Requirements</p>
                <h2 class="text-3xl font-bold text-primary uppercase tracking-tight font-headline-lg mb-6">Required Information</h2>
                <p class="text-gray-600 text-sm leading-relaxed mb-8">
                    Every listing must include the following details so buyers can make an informed decision. Incomplete submissions will be returned for correction.
                </p>

                <div class="space-y-4">
                    @php
                    $info = [
                        ['icon'=>'title',          'title'=>'Vehicle Title / Name',      'body'=>'The full name or title of the vehicle as it appears on ownership documents.'],
                        ['icon'=>'directions_car', 'title'=>'Make, Model & Year',         'body'=>'Manufacturer, model name, and year of production.'],
                        ['icon'=>'palette',        'title'=>'Color & Condition',           'body'=>'Exterior color and overall vehicle condition (Excellent, Good, Fair, Poor, etc.).'],
                        ['icon'=>'location_on',    'title'=>'Island Location',             'body'=>'Which island the vehicle is currently located on — critical for buyer logistics.'],
                        ['icon'=>'build',          'title'=>'Damage Type (if any)',        'body'=>'Any known mechanical, structural, or cosmetic damage must be disclosed.'],
                        ['icon'=>'category',       'title'=>'Vehicle Category',            'body'=>'Car, truck, SUV, motorcycle, marine craft, or other applicable category.'],
                        ['icon'=>'description',    'title'=>'Full Description',            'body'=>'A detailed description covering key features, history, and any additional notes.'],
                        ['icon'=>'sell',           'title'=>'Pricing Type',                'body'=>'Choose a Starting Bid, Reserve Price, or Buy Now amount — or a combination.'],
                    ];
                    @endphp
                    @foreach($info as $item)
                    <div class="flex gap-4">
                        <div class="w-10 h-10 bg-[#f0f4fb] flex items-center justify-center flex-shrink-0" style="border-radius:0">
                            <span class="material-symbols-outlined text-primary text-[20px]">{{ $item['icon'] }}</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-primary uppercase tracking-wider mb-1">{{ $item['title'] }}</h4>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ $item['body'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: media & docs + after approval --}}
            <div>
                <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-3">Media & Documentation</p>
                <h2 class="text-3xl font-bold text-primary uppercase tracking-tight font-headline-lg mb-6">Required Uploads</h2>

                <div class="space-y-4 mb-8">
                    @php
                    $media = [
                        ['step'=>'01','title'=>'5 Vehicle Photos','body'=>'Front, Back, Left Side, Right Side, and Under the Hood. Photos must be clear and taken in good lighting.'],
                        ['step'=>'02','title'=>'Video Walkthrough','body'=>'A short video showing the vehicle running or, if non-running, a startup attempt so buyers can assess condition.'],
                        ['step'=>'03','title'=>'Proof of Ownership','body'=>'A clear copy of the vehicle title or equivalent ownership document issued in the Bahamas.'],
                        ['step'=>'04','title'=>'Seller Identification','body'=>'Two valid forms of ID (e.g. passport, NIB card, business license). Business sellers must include company registration documents.'],
                    ];
                    @endphp
                    @foreach($media as $m)
                    <div class="border border-gray-100 p-5 flex gap-4 hover:border-primary hover:bg-[#f8fafd] transition-colors" style="border-radius:0">
                        <span class="text-[11px] font-bold text-secondary-fixed-dim tracking-widest w-8 flex-shrink-0 mt-0.5">{{ $m['step'] }}</span>
                        <div>
                            <h4 class="text-sm font-bold text-primary uppercase tracking-wider mb-1">{{ $m['title'] }}</h4>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ $m['body'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="bg-[#fff8e7] border-l-4 border-secondary-fixed-dim p-5" style="border-radius:0">
                    <div class="flex gap-3">
                        <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px] flex-shrink-0 mt-0.5">info</span>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <strong class="text-primary">Review Timeline:</strong> Listings are reviewed by our admin team within 24 hours of submission. You will be notified by email once your listing is approved or if additional information is needed.
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
            ['q'=>'Do I need to pay anything to register as an Individual Seller?','a'=>'No. Individual Seller registration is completely free. You only pay when a listing sells — a 4% commission (minimum $150) is deducted from your proceeds.'],
            ['q'=>'What is the difference between a Reserve Price and a Starting Bid?','a'=>'A Starting Bid is the minimum price at which bidding begins. A Reserve Price is a hidden minimum — if bidding doesn\'t reach your reserve, the vehicle is not sold. You can set both on the same listing.'],
            ['q'=>'How does the free relisting for Business Sellers work?','a'=>'If your listing expires without a sale, Business Sellers can relist the same vehicle at no additional charge within 48 hours of expiry. After 48 hours, a new submission is required.'],
            ['q'=>'When do I receive my payment after a sale?','a'=>'Once the buyer completes payment via their CayMark wallet, your proceeds (minus the 4% commission) are processed and credited to your account. Exact processing times are shown in your seller dashboard.'],
            ['q'=>'Can I sell in both the Auction and the Marketplace?','a'=>'Yes. Both Individual and Business Sellers can list vehicles for timed auction or as fixed-price marketplace listings with a Buy Now option, subject to admin approval.'],
            ['q'=>'What happens if my listing is rejected?','a'=>'If your submission is incomplete or doesn\'t meet CayMark\'s listing standards, you\'ll receive a notification explaining what needs to be corrected. You can resubmit after making the required changes.'],
        ];
        @endphp

        <div class="space-y-3" id="faq-list">
            @foreach($faqs as $i => $faq)
            <div class="bg-white border border-gray-200 overflow-hidden" style="border-radius:0">
                <button type="button"
                    class="w-full flex items-center justify-between px-6 py-5 text-left focus:outline-none faq-btn"
                    data-target="sg-faq-{{ $i }}">
                    <span class="text-sm font-bold text-primary uppercase tracking-wide pr-4">{{ $faq['q'] }}</span>
                    <span class="material-symbols-outlined text-gray-400 text-[20px] flex-shrink-0 faq-icon transition-transform duration-200">expand_more</span>
                </button>
                <div id="sg-faq-{{ $i }}" class="hidden px-6 pb-5 border-t border-gray-100">
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
            Ready to List Your Vehicle?
        </h2>
        <p class="text-white/60 max-w-xl mx-auto text-sm mb-10">
            Join CayMark and reach verified buyers across the Bahamian archipelago. Individual registration is free — start selling today.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('register') }}?role=seller&plan=individual"
               class="px-10 py-4 bg-secondary-fixed-dim text-primary font-bold uppercase tracking-widest text-sm hover:bg-[#b8943b] transition-colors"
               style="border-radius:0">
                Register — Free Individual
            </a>
            <a href="{{ route('register') }}?role=seller&plan=business"
               class="px-10 py-4 border border-white/30 text-white font-bold uppercase tracking-widest text-sm hover:bg-white/10 transition-colors"
               style="border-radius:0">
                Register — Business Plan
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
