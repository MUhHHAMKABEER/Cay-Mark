@extends('layouts.public')

@section('title', 'CayMark Island Exchange | Premium Vehicle Auctions')

@section('content')
@php
    /* ── Image URL helper ──────────────────────────────────────── */
    $homeImgUrl = function ($path) {
        $path = trim((string) ($path ?? ''));
        if ($path === '' || str_starts_with($path, 'http')) return $path ?: asset('images/placeholder-product.png');
        $p = ltrim(str_replace('\\', '/', $path), '/');
        return str_starts_with($p, 'uploads/') ? asset($p) : asset('uploads/listings/' . $p);
    };

    /* ── Dynamic auction data ──────────────────────────────────── */
    $now = now()->format('Y-m-d H:i:s');
    $activeAuctionBase = \App\Models\Listing::with(['images', 'bids'])
        ->where('listing_method', 'auction')
        ->where('status', 'approved')
        ->whereRaw(
            "COALESCE(auction_end_time, DATE_ADD(COALESCE(auction_start_time, created_at), INTERVAL COALESCE(auction_duration, 7) DAY)) > ?",
            [$now]
        );

    // 4 featured auctions (most viewed / most recent) — bids eager-loaded to avoid N+1
    $featuredAuctions = (clone $activeAuctionBase)
        ->orderByDesc('view_count')
        ->orderByDesc('created_at')
        ->take(4)
        ->get();

    /* ── Auction Finder dropdowns ──────────────────────────────── */
    $finderMakes = \App\Models\Listing::where('status', 'approved')
        ->whereNotNull('make')
        ->distinct()
        ->orderBy('make')
        ->pluck('make');

    $finderYears = range(date('Y'), 2000);   // newest first

    $finderCategories = [
        'car'       => 'Passenger Vehicles',
        'marine'    => 'Marine / Boats',
        'truck'     => 'Trucks & SUVs',
        'equipment' => 'Heavy Equipment',
    ];

    /* ── Corporate stats (dynamic) ─────────────────────────────── */
    $statActiveAuctions = (clone $activeAuctionBase)->count();
    $statTotalListings  = \App\Models\Listing::where('status', 'approved')->count();
    $statVerifiedUsers  = \App\Models\User::whereNotNull('role')->where('role', '!=', 'admin')->count();

    /* ── Auth context for CTA section ──────────────────────────── */
    $homeUser     = Auth::user();
    $homeIsGuest  = !$homeUser;
    $homeIsBuyer  = $homeUser && $homeUser->role === 'buyer';
    $homeIsSeller = $homeUser && $homeUser->role === 'seller';
@endphp

{{-- ══════════════════════════════════════════════════════════════════
     HERO CAROUSEL SECTION
══════════════════════════════════════════════════════════════════ --}}
<section class="relative bg-primary overflow-hidden" style="height:clamp(600px,80vh,800px)">

    {{-- Sliding background images --}}
    <div class="absolute inset-0 z-0">
        <div class="hero-carousel-track h-full" id="hero-track">

            {{-- Slide 1: Premium Vehicles --}}
            <div class="hero-slide relative h-full">
                <div class="absolute inset-0 bg-cover bg-center"
                    style="background-image:url('https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1600&q=80')"></div>
                <div class="absolute inset-0" style="background:linear-gradient(to right,#002452 0%,rgba(0,36,82,0.75) 45%,rgba(0,36,82,0.15) 100%)"></div>
            </div>

            {{-- Slide 2: Marine & Yacht --}}
            <div class="hero-slide relative h-full">
                <div class="absolute inset-0 bg-cover bg-center"
                    style="background-image:url('https://images.unsplash.com/photo-1569263979104-865ab7cd8d13?w=1600&q=80')"></div>
                <div class="absolute inset-0" style="background:linear-gradient(to right,#002452 0%,rgba(0,36,82,0.75) 45%,rgba(0,36,82,0.15) 100%)"></div>
            </div>

            {{-- Slide 3: Heavy Fleet --}}
            <div class="hero-slide relative h-full">
                <div class="absolute inset-0 bg-cover bg-center"
                    style="background-image:url('https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1600&q=80')"></div>
                <div class="absolute inset-0" style="background:linear-gradient(to right,#002452 0%,rgba(0,36,82,0.75) 45%,rgba(0,36,82,0.15) 100%)"></div>
            </div>

        </div>
    </div>

    {{-- Content layer --}}
    <div class="relative z-10 h-full px-4 md:px-16 w-full max-w-[1280px] mx-auto flex flex-col lg:flex-row items-center justify-between gap-12 py-16">

        {{-- Left: Headlines + Controls --}}
        <div class="text-left w-full lg:w-1/2 max-w-2xl">

            {{-- Live badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-2 border border-white/30 text-white text-[11px] font-bold tracking-[0.2em] mb-8 bg-white/10" style="border-radius:0">
                <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                @if($statActiveAuctions > 0)
                    {{ $statActiveAuctions }} AUCTION{{ $statActiveAuctions !== 1 ? 'S' : '' }} ACTIVE NOW
                @else
                    REAL-TIME AUCTIONS ACTIVE
                @endif
            </div>

            {{-- Dynamic headline (changed by JS carousel) --}}
            <div id="hero-content">
                <h1 class="text-4xl md:text-[60px] leading-[1.05] font-bold text-white mb-6 font-display-lg uppercase tracking-tight" id="hero-title">
                    PREMIUM<br/>
                    <span class="text-secondary-fixed-dim">ISLAND ASSET</span><br/>
                    EXCHANGE
                </h1>
                <p class="text-white/90 text-lg mb-10 font-body-lg max-w-lg" id="hero-description">
                    The Bahamas' most trusted digital auction house for wholesale vehicles, fleet assets, and private listings.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-start gap-4 mb-12">
                <a href="{{ route('Auction.index') }}"
                    class="px-10 py-4 bg-white text-primary font-bold hover:bg-gray-100 transition-all text-label-md flex items-center gap-3 shadow-2xl uppercase tracking-wider"
                    style="border-radius:0">
                    Browse Catalog
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>
                @guest
                <a href="{{ route('register') }}"
                    class="px-10 py-4 border-2 border-white text-white font-bold hover:bg-white/10 transition-all text-label-md uppercase tracking-wider"
                    style="border-radius:0">
                    Create Account
                </a>
                @endguest
            </div>

        </div>

        {{-- Right: Auction Finder card --}}
        <div class="w-full lg:w-5/12">
            <div class="bg-white p-8 md:p-10 shadow-2xl border-t-4 border-secondary-fixed-dim" style="border-radius:0">
                <h2 class="text-2xl font-bold text-primary mb-1 font-headline-md uppercase tracking-wide">Auction Finder</h2>
                <p class="text-gray-400 mb-7 text-sm font-medium">Find your perfect island asset</p>
                <form method="GET" action="{{ route('Auction.index') }}" class="space-y-5 text-left">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-2 uppercase tracking-widest">Asset Category</label>
                            <select name="category"
                                class="w-full border-gray-300 text-gray-900 focus:ring-primary focus:border-primary text-sm py-3"
                                style="border-radius:0">
                                <option value="">All Categories</option>
                                @foreach($finderCategories as $val => $label)
                                <option value="{{ $val }}" {{ request('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-2 uppercase tracking-widest">Make / Brand</label>
                            <select name="make"
                                class="w-full border-gray-300 text-gray-900 focus:ring-primary focus:border-primary text-sm py-3"
                                style="border-radius:0">
                                <option value="">All Brands</option>
                                @foreach($finderMakes as $make)
                                <option value="{{ $make }}" {{ request('make') === $make ? 'selected' : '' }}>{{ $make }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-2 uppercase tracking-widest">Year From</label>
                            <select name="year_from"
                                class="w-full border-gray-300 text-gray-900 focus:ring-primary focus:border-primary text-sm py-3"
                                style="border-radius:0">
                                <option value="">Any Year</option>
                                @foreach(array_reverse($finderYears) as $yr)
                                <option value="{{ $yr }}" {{ request('year_from') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 mb-2 uppercase tracking-widest">Year To</label>
                            <select name="year_to"
                                class="w-full border-gray-300 text-gray-900 focus:ring-primary focus:border-primary text-sm py-3"
                                style="border-radius:0">
                                <option value="">Any Year</option>
                                @foreach($finderYears as $yr)
                                <option value="{{ $yr }}" {{ request('year_to') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit"
                        class="w-full py-4 bg-primary text-white font-bold hover:bg-[#003377] transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-sm shadow-lg"
                        style="border-radius:0">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                        Execute Search
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- ── Slide indicator dots — bottom-center of banner ── --}}
    <div class="absolute bottom-6 left-0 right-0 flex justify-center items-center gap-2 z-20" id="hero-dots">
        <div class="h-[3px] w-8 bg-white transition-all duration-300 slide-dot"></div>
        <div class="h-[3px] w-3 bg-white/30 transition-all duration-300 slide-dot"></div>
        <div class="h-[3px] w-3 bg-white/30 transition-all duration-300 slide-dot"></div>
    </div>

</section>

{{-- ══════════════════════════════════════════════════════════════════
     ACTIVE AUCTIONS SECTION
══════════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-surface">
    <div class="max-w-[1600px] mx-auto px-4 md:px-12">

        {{-- Section header --}}
        <div class="mb-12 flex flex-col sm:flex-row justify-between items-start sm:items-end border-b-2 border-gray-200 pb-6 gap-4">
            <div>
                <h2 class="text-4xl font-bold text-primary font-display-lg uppercase tracking-tight">
                    Active <span class="text-secondary-fixed-dim">Auctions</span>
                </h2>
                @if($statActiveAuctions > 4)
                <p class="text-sm text-gray-500 mt-2 font-medium">{{ $statActiveAuctions }} live auctions — showing top picks</p>
                @endif
            </div>
            <a href="{{ route('Auction.index') }}"
                class="group flex items-center gap-2 text-primary font-bold hover:text-[#003377] transition-colors text-sm uppercase tracking-widest">
                View Full Catalog
                <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>

        @if($featuredAuctions->isEmpty())
        {{-- Empty state --}}
        <div class="text-center py-20 border border-gray-200 bg-white">
            <span class="material-symbols-outlined text-[64px] text-gray-300 block mb-4">gavel</span>
            <h3 class="text-xl font-bold text-gray-500 uppercase tracking-wide mb-2">No Active Auctions</h3>
            <p class="text-gray-400 text-sm">New auctions are added regularly — check back soon.</p>
        </div>
        @else
        {{-- Auction cards grid / scrollable row --}}
        <div class="carousel-container pb-4">
            @foreach($featuredAuctions as $auction)
            @php
                $aImg    = $auction->images->first();
                $imgUrl  = $aImg ? $homeImgUrl($aImg->image_path) : null;
                $vehicle = trim(($auction->year ?? '') . ' ' . ($auction->make ?? '') . ' ' . ($auction->model ?? ''));

                // Compute end time
                $endTime = null;
                if ($auction->auction_end_time) {
                    $endTime = \Carbon\Carbon::parse($auction->auction_end_time);
                } elseif ($auction->auction_start_time && $auction->auction_duration) {
                    $endTime = \Carbon\Carbon::parse($auction->auction_start_time)->addDays((int)$auction->auction_duration);
                }
                $endTimeIso = $endTime ? $endTime->toIso8601String() : null;

                // Use eager-loaded bids collection (no extra query)
                $highestBid = $auction->bids->where('status', '!=', 'removed')->max('amount')
                              ?? $auction->starting_price
                              ?? 0;

                // Build auction detail URL
                $auctionUrl = $auction->slug
                    ? route('auction.show', ['listing' => $auction->slug])
                    : route('auction.dashboard', ['id' => $auction->id, 'slug' => $auction->id]);
            @endphp
            <div class="carousel-item w-full md:w-[calc(50%-12px)] lg:w-[calc(25%-18px)] bg-white border border-gray-200 hover:border-primary transition-all flex flex-col group" style="border-radius:0">

                {{-- Image --}}
                <div class="relative h-56 bg-gray-100 overflow-hidden flex-shrink-0">
                    @if($imgUrl)
                    <img src="{{ $imgUrl }}"
                         alt="{{ $vehicle }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out"/>
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-50">
                        <span class="material-symbols-outlined text-[64px] text-gray-300">directions_car</span>
                    </div>
                    @endif

                    {{-- Status badge --}}
                    <div class="absolute top-0 left-0 bg-primary text-white px-4 py-2 text-[10px] font-bold uppercase tracking-widest" style="border-radius:0">
                        Live Auction
                    </div>

                    {{-- Countdown timer --}}
                    @if($endTimeIso)
                    <div class="absolute bottom-3 left-3 bg-white/90 backdrop-blur text-primary px-3 py-2 font-mono text-sm font-bold shadow-sm flex items-center gap-2" style="border-radius:0">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse flex-shrink-0"></span>
                        <span class="js-countdown" data-end="{{ $endTimeIso }}">--:--:--</span>
                    </div>
                    @endif
                </div>

                {{-- Card body --}}
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="text-sm font-bold text-gray-900 mb-1.5 font-headline-sm uppercase leading-snug line-clamp-1">
                        {{ $vehicle ?: 'Vehicle #'.$auction->item_number }}
                    </h3>
                    <p class="text-xs text-gray-400 mb-5 flex items-center gap-1 uppercase tracking-wider font-semibold">
                        <span class="material-symbols-outlined text-[13px]">location_on</span>
                        Nassau, Bahamas
                    </p>
                    <div class="mt-auto flex justify-between items-end border-t border-gray-100 pt-4">
                        <div>
                            <p class="text-[10px] text-gray-400 mb-1 uppercase font-bold tracking-widest">Current Bid</p>
                            <p class="text-xl font-bold text-primary">${{ number_format($highestBid, 2) }}</p>
                        </div>
                        <a href="{{ $auctionUrl }}"
                            class="bg-primary text-white p-2.5 hover:bg-[#003377] transition-colors" style="border-radius:0"
                            title="Place bid on {{ $vehicle }}">
                            <span class="material-symbols-outlined text-[20px]">gavel</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     CTA / REGISTER SECTION (auth-aware)
══════════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-primary text-white">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16">
        <div class="flex flex-col md:flex-row items-center gap-16">

            {{-- Left: Benefits --}}
            <div class="flex-1 space-y-8">
                <div class="w-20 h-20 bg-secondary-fixed-dim/20 border border-secondary-fixed-dim flex items-center justify-center" style="border-radius:0">
                    <span class="material-symbols-outlined text-secondary-fixed-dim text-4xl">account_balance</span>
                </div>
                <div>
                    <h2 class="text-4xl md:text-5xl font-bold font-display-lg uppercase tracking-tight leading-tight mb-6">
                        Register a new<br/>account for free
                    </h2>
                    <p class="text-white/80 text-lg leading-relaxed max-w-lg font-body-lg">
                        Create your account in seconds and start bidding today. Join buyers and sellers across The Bahamas on CayMark.
                    </p>
                </div>
                <ul class="space-y-4">
                    <li class="flex items-center gap-3 text-white/90 font-bold uppercase tracking-widest text-xs">
                        <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">check_circle</span>
                        Verified Auction Participation
                    </li>
                    <li class="flex items-center gap-3 text-white/90 font-bold uppercase tracking-widest text-xs">
                        <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">check_circle</span>
                        Real-time Outbid Notifications
                    </li>
                    <li class="flex items-center gap-3 text-white/90 font-bold uppercase tracking-widest text-xs">
                        <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">check_circle</span>
                        Multi-island Logistics Coordination
                    </li>
                    <li class="flex items-center gap-3 text-white/90 font-bold uppercase tracking-widest text-xs">
                        <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">check_circle</span>
                        Secure Escrow Payment Gateway
                    </li>
                </ul>
            </div>

            {{-- Right: Auth-aware CTA panel --}}
            <div class="flex-1 w-full max-w-xl bg-white p-10 shadow-2xl" style="border-radius:0">
                @if($homeIsGuest)
                {{-- GUEST: Registration CTA --}}
                <h3 class="text-2xl font-bold text-primary mb-2 font-headline-md uppercase tracking-wide">Create Auction Account</h3>
                <p class="text-gray-400 text-sm mb-8">Select your role and get trading in minutes</p>
                <div class="space-y-4">
                    <a href="{{ route('register') }}?role=buyer"
                        class="w-full flex items-center justify-between p-5 border-2 border-gray-200 hover:border-primary hover:bg-gray-50 transition-all group" style="border-radius:0">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 flex items-center justify-center bg-primary/10" style="border-radius:0">
                                <span class="material-symbols-outlined text-primary text-[24px]">gavel</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 uppercase tracking-wide text-sm">Buyer Account</p>
                                <p class="text-xs text-gray-500 mt-0.5">Bid on vehicles & fleet assets</p>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-gray-400 group-hover:text-primary text-[20px]">arrow_forward</span>
                    </a>
                    <a href="{{ route('register') }}?role=seller"
                        class="w-full flex items-center justify-between p-5 border-2 border-gray-200 hover:border-secondary-fixed-dim hover:bg-gray-50 transition-all group" style="border-radius:0">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 flex items-center justify-center bg-secondary-fixed-dim/10" style="border-radius:0">
                                <span class="material-symbols-outlined text-secondary-fixed-dim text-[24px]">sell</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 uppercase tracking-wide text-sm">Seller Account</p>
                                <p class="text-xs text-gray-500 mt-0.5">List vehicles & reach island buyers</p>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-gray-400 group-hover:text-secondary-fixed-dim text-[20px]">arrow_forward</span>
                    </a>
                    <p class="text-center text-xs text-gray-500 pt-2">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Sign in here</a>
                    </p>
                </div>

                @elseif($homeIsBuyer)
                {{-- BUYER: Dashboard welcome --}}
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="w-14 h-14 rounded-full bg-primary text-white flex items-center justify-center text-2xl font-bold flex-shrink-0">
                        {{ strtoupper(substr($homeUser->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-0.5">Welcome back</p>
                        <p class="text-xl font-bold text-primary">{{ $homeUser->name }}</p>
                    </div>
                </div>
                <div class="space-y-3 mb-8">
                    <a href="{{ route('buyer.auctions') }}" class="flex items-center gap-4 p-4 border border-gray-100 hover:border-primary hover:bg-gray-50 transition-all" style="border-radius:0">
                        <span class="material-symbols-outlined text-primary text-[22px]">gavel</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">My Active Bids</p>
                            <p class="text-xs text-gray-500">Track your current bids</p>
                        </div>
                        <span class="material-symbols-outlined text-gray-300 ml-auto text-[18px]">chevron_right</span>
                    </a>
                    <a href="{{ route('buyer.watchlist') }}" class="flex items-center gap-4 p-4 border border-gray-100 hover:border-primary hover:bg-gray-50 transition-all" style="border-radius:0">
                        <span class="material-symbols-outlined text-primary text-[22px]">favorite</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Watchlist</p>
                            <p class="text-xs text-gray-500">Saved auctions you're watching</p>
                        </div>
                        <span class="material-symbols-outlined text-gray-300 ml-auto text-[18px]">chevron_right</span>
                    </a>
                    <a href="{{ route('buyer.deposit-withdrawal') }}" class="flex items-center gap-4 p-4 border border-gray-100 hover:border-primary hover:bg-gray-50 transition-all" style="border-radius:0">
                        <span class="material-symbols-outlined text-primary text-[22px]">account_balance_wallet</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Wallet & Deposits</p>
                            <p class="text-xs text-gray-500">Manage your buying power</p>
                        </div>
                        <span class="material-symbols-outlined text-gray-300 ml-auto text-[18px]">chevron_right</span>
                    </a>
                </div>
                <a href="{{ route('Auction.index') }}"
                    class="w-full py-4 bg-primary text-white font-bold hover:bg-[#003377] transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-sm shadow-lg" style="border-radius:0">
                    <span class="material-symbols-outlined text-[20px]">gavel</span>
                    Browse Live Auctions
                </a>

                @elseif($homeIsSeller)
                {{-- SELLER: Dashboard shortcuts --}}
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="w-14 h-14 rounded-full bg-primary text-white flex items-center justify-center text-2xl font-bold flex-shrink-0">
                        {{ strtoupper(substr($homeUser->name ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-0.5">Seller Account</p>
                        <p class="text-xl font-bold text-primary">{{ $homeUser->name }}</p>
                    </div>
                </div>
                <div class="space-y-3 mb-8">
                    <a href="{{ route('seller.listings.create') }}" class="flex items-center gap-4 p-4 border-2 border-secondary-fixed-dim hover:bg-secondary-fixed-dim/5 transition-all" style="border-radius:0">
                        <span class="material-symbols-outlined text-secondary-fixed-dim text-[22px]">add_circle</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">List a Vehicle</p>
                            <p class="text-xs text-gray-500">Submit a new auction listing</p>
                        </div>
                        <span class="material-symbols-outlined text-gray-300 ml-auto text-[18px]">chevron_right</span>
                    </a>
                    <a href="{{ route('seller.auctions') }}" class="flex items-center gap-4 p-4 border border-gray-100 hover:border-primary hover:bg-gray-50 transition-all" style="border-radius:0">
                        <span class="material-symbols-outlined text-primary text-[22px]">inventory_2</span>
                        <div>
                            <p class="text-sm font-bold text-gray-900">My Listings</p>
                            <p class="text-xs text-gray-500">Manage your auctions</p>
                        </div>
                        <span class="material-symbols-outlined text-gray-300 ml-auto text-[18px]">chevron_right</span>
                    </a>
                </div>
                <a href="{{ route('seller.dashboard') }}"
                    class="w-full py-4 bg-primary text-white font-bold hover:bg-[#003377] transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-sm shadow-lg" style="border-radius:0">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    Seller Dashboard
                </a>

                @else
                {{-- INCOMPLETE REGISTRATION --}}
                <h3 class="text-xl font-bold text-primary mb-4 font-headline-md uppercase">Complete Your Account</h3>
                <p class="text-gray-500 text-sm mb-6">Your registration isn't quite finished yet. Complete it to start bidding.</p>
                <a href="{{ route('finish.registration') }}"
                    class="w-full py-4 bg-primary text-white font-bold hover:bg-[#003377] transition-all flex items-center justify-center gap-3 uppercase tracking-widest text-sm" style="border-radius:0">
                    Finish Registration
                </a>
                @endif
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     CORPORATE OVERVIEW SECTION
══════════════════════════════════════════════════════════════════ --}}
<section class="py-24 bg-white border-b border-gray-200">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">

            {{-- Text --}}
            <div>
                <h2 class="text-4xl font-bold text-primary font-display-lg uppercase tracking-tight mb-8">
                    What is <span class="text-secondary-fixed-dim">CayMark?</span>
                </h2>
                <div class="space-y-6 text-gray-700 leading-loose font-body-lg">
                    <p>
                        <strong>CayMark Island Exchange &amp; Auction House</strong> is The Bahamas' premier digital vehicle auction platform, dedicated to connecting buyers and sellers across every island. More than just an auction house, CayMark is redefining how The Bahamas buys, sells, and trades vehicles through a secure, transparent, and fully online marketplace.
                    </p>
                    <p>
                        Browse an extensive selection of cars, trucks, boats, and heavy equipment from sellers throughout the islands. Participate in real-time auctions or purchase instantly using our Buy Now option — all from one powerful digital platform.
                    </p>
                    <p class="font-semibold text-primary">
                        Sign up today and experience the future of island trade.
                    </p>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-3 text-primary font-bold uppercase tracking-widest text-sm hover:underline">
                        Get Started
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </a>
                </div>
            </div>

            {{-- Dynamic stat tiles --}}
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-surface p-8 border border-gray-200 text-center" style="border-radius:0">
                    <p class="text-4xl font-bold text-primary mb-2">24/7</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Trading Access</p>
                </div>
                <div class="bg-surface p-8 border border-gray-200 text-center" style="border-radius:0">
                    <p class="text-4xl font-bold text-primary mb-2">
                        {{ $statActiveAuctions > 0 ? $statActiveAuctions.'+' : '100%' }}
                    </p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">
                        {{ $statActiveAuctions > 0 ? 'Live Now' : 'Verified Listings' }}
                    </p>
                </div>
                <div class="bg-surface p-8 border border-gray-200 text-center" style="border-radius:0">
                    <p class="text-4xl font-bold text-primary mb-2">ALL</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Island Coverage</p>
                </div>
                <div class="bg-surface p-8 border border-gray-200 text-center" style="border-radius:0">
                    <p class="text-4xl font-bold text-primary mb-2">SECURE</p>
                    <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Payment Gateway</p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     NEWSLETTER SECTION
══════════════════════════════════════════════════════════════════ --}}
<section class="py-20 bg-primary text-white border-t border-white/10">
    <div class="max-w-[1280px] mx-auto px-4 md:px-16">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-12">
            <div class="text-left">
                <h2 class="text-2xl font-bold font-headline-md uppercase tracking-widest mb-3">Market Intelligence Reports</h2>
                <p class="text-white/70 max-w-md font-body-md">Subscribe to receive weekly auction alerts and island market trend reports.</p>
            </div>
            <form class="w-full max-w-xl flex" action="#" method="POST">
                @csrf
                <input type="email" name="email" required placeholder="Business Email Address"
                    class="flex-grow bg-white/10 border border-white/20 text-white px-6 py-4 focus:ring-1 focus:ring-secondary-fixed-dim focus:border-secondary-fixed-dim placeholder:text-white/40 focus:outline-none"
                    style="border-radius:0"/>
                <button type="submit"
                    class="px-10 py-4 bg-secondary-fixed-dim text-primary font-bold hover:bg-secondary transition-colors uppercase tracking-[0.2em] text-xs flex-shrink-0"
                    style="border-radius:0">
                    Subscribe
                </button>
            </form>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     PAGE SCRIPTS
══════════════════════════════════════════════════════════════════ --}}
@push('scripts')
<script>
(function () {
    /* ── Hero Carousel ─────────────────────────────────────────── */
    var currentSlide = 0;
    var slides = [
        {
            title : "PREMIUM<br/><span style='color:#C8A84B'>ISLAND ASSET</span><br/>EXCHANGE",
            desc  : "The Bahamas' most trusted digital auction house for wholesale vehicles, fleet assets, and private listings."
        },
        {
            title : "ELITE<br/><span style='color:#C8A84B'>MARINE & YACHT</span><br/>TRADING",
            desc  : "Access exclusive marine vessel listings across the archipelago — from transport hulls to luxury day cruisers."
        },
        {
            title : "MAJOR<br/><span style='color:#C8A84B'>FLEET & HEAVY</span><br/>EQUIPMENT",
            desc  : "Industrial-grade liquidation for construction fleets, hospitality assets, and high-value institutional machinery."
        }
    ];
    var autoTimer = null;

    function heroUpdateUI() {
        var track = document.getElementById('hero-track');
        if (track) track.style.transform = 'translateX(-' + (currentSlide * 100) + '%)';

        var titleEl = document.getElementById('hero-title');
        var descEl  = document.getElementById('hero-description');
        if (titleEl) titleEl.innerHTML = slides[currentSlide].title;
        if (descEl)  descEl.textContent = slides[currentSlide].desc;

        var dots = document.querySelectorAll('.slide-dot');
        dots.forEach(function (d, i) {
            d.classList.toggle('bg-white',     i === currentSlide);
            d.classList.toggle('bg-white/30',  i !== currentSlide);
            d.classList.toggle('w-8',          i === currentSlide);
            d.classList.toggle('w-3',          i !== currentSlide);
        });
    }

    window.heroMoveSlide = function (dir) {
        currentSlide = (currentSlide + dir + slides.length) % slides.length;
        heroUpdateUI();
        clearInterval(autoTimer);
        autoTimer = setInterval(function () { heroMoveSlide(1); }, 8000);
    };

    autoTimer = setInterval(function () { heroMoveSlide(1); }, 8000);

    /* ── Countdown timers ──────────────────────────────────────── */
    function formatCountdown(ms) {
        if (ms <= 0) return 'Ended';
        var totalSecs = Math.floor(ms / 1000);
        var h = Math.floor(totalSecs / 3600);
        var m = Math.floor((totalSecs % 3600) / 60);
        var s = totalSecs % 60;
        var pad = function (n) { return n < 10 ? '0' + n : '' + n; };
        if (h >= 24) {
            var d = Math.floor(h / 24);
            return d + 'd ' + pad(h % 24) + 'h ' + pad(m) + 'm';
        }
        return pad(h) + ' : ' + pad(m) + ' : ' + pad(s);
    }

    var countdowns = document.querySelectorAll('.js-countdown');
    if (countdowns.length) {
        function tickCountdowns() {
            var now = Date.now();
            countdowns.forEach(function (el) {
                var end = new Date(el.dataset.end).getTime();
                el.textContent = formatCountdown(end - now);
            });
        }
        tickCountdowns();
        setInterval(tickCountdowns, 1000);
    }
})();
</script>
@endpush

@endsection
