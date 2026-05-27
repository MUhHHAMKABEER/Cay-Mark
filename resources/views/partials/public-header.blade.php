@php
    /* ── Auth context ───────────────────────────── */
    $phUser     = Auth::user();
    $phIsGuest  = !$phUser;
    $phIsBuyer  = $phUser && $phUser->role === 'buyer';
    $phIsSeller = $phUser && $phUser->role === 'seller';

    /* ── Buyer stats (wallet, bids, watchlist) ─── */
    $phBuyingPower   = null;
    $phActiveBids    = 0;
    $phWatchlistCount = 0;
    if ($phIsBuyer) {
        try {
            $phWallet        = \App\Models\UserWallet::getOrCreateForUser($phUser->id);
            $phBuyingPower   = (float) $phWallet->available_balance;
            $phActiveBids    = $phUser->bids()->where('status', 'active')->count();
            $phWatchlistCount = $phUser->watchlist()->count();
        } catch (\Throwable $e) { /* suppress on pages that don't need it */ }
    }

    /* ── Seller stats (active listings) ─────────── */
    $phActiveListings = 0;
    if ($phIsSeller) {
        try {
            $phActiveListings = \App\Models\Listing::where('seller_id', $phUser->id)
                ->where('listing_state', 'active')->where('status', 'approved')->count();
        } catch (\Throwable $e) {}
    }

    /* ── Notifications ───────────────────────────── */
    $phUnreadCount = 0;
    $phNotifsUrl   = '#';
    if ($phUser && method_exists($phUser, 'unreadNotifications')) {
        try {
            $phUnreadCount = $phUser->unreadNotifications()->count();
        } catch (\Throwable $e) {}
    }
    if ($phIsBuyer)  $phNotifsUrl = route('buyer.notifications');
    if ($phIsSeller) $phNotifsUrl = route('seller.notifications');

    /* ── Active nav detection ────────────────────── */
    $phHomeActive           = request()->routeIs('welcome');
    $phAuctionActive        = request()->routeIs('Auction.index', 'auction.show', 'auction.dashboard', 'listing.show');
    $phGettingStartedActive = request()->routeIs('buyer-guide', 'sellers-guide', 'video-guide', 'about*');
    $phContactActive        = request()->routeIs('contact');

    /* ── User avatar initial ─────────────────────── */
    $phInitial = $phUser ? strtoupper(substr($phUser->name ?? 'U', 0, 1)) : '';
@endphp

{{-- ══════════════════════════════════════════════════════════════
     MAIN HEADER STRIP: Logo · Search · Auth Actions
══════════════════════════════════════════════════════════════ --}}
<header class="bg-white sticky top-0 z-50 border-b border-slate-100 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">
    <div class="w-full max-w-[1280px] mx-auto px-4 md:px-10 py-3">
        <div class="flex items-center gap-4">

            {{-- ── Logo ── --}}
            <div class="flex-shrink-0">
                <a href="{{ route('welcome') }}">
                    <img src="{{ asset(config('logos.header', 'Logos/Caymark Logo.png')) }}"
                         alt="CayMark Island Exchange"
                         class="h-[56px] w-auto object-contain"
                         width="180" height="56" loading="eager"/>
                </a>
            </div>

            {{-- ── Desktop search bar — centered, borderless ── --}}
            <div class="flex-1 hidden md:flex justify-center"
                 x-data="cmSearch('{{ Route::has('auction.suggest') ? route('auction.suggest') : url('/auction-suggest') }}', '{{ route('Auction.index') }}')"
                 @click.outside="close()"
                 @keydown.escape.window="close()">

                <form method="GET" action="{{ route('Auction.index') }}" :action="auctionUrl" class="relative w-full max-w-xl" @submit.prevent="submit()">
                    {{-- Pill wrapper — no border, soft background only --}}
                    <div class="flex items-center rounded-full bg-slate-100 focus-within:bg-white focus-within:shadow-[0_0_0_2px_theme('colors.primary')] transition-all duration-150">
                        <input
                            type="text"
                            name="search"
                            x-model="query"
                            @focus="open()"
                            @input.debounce.250ms="fetch()"
                            @keydown.arrow-down.prevent="moveDown()"
                            @keydown.arrow-up.prevent="moveUp()"
                            @keydown.enter.prevent="selectActive()"
                            placeholder="Search vehicle auctions by make, model…"
                            autocomplete="off"
                            class="flex-1 bg-transparent py-2.5 pl-4 pr-2 text-[13.5px] text-slate-800 placeholder:text-slate-400 focus:outline-none min-w-0"
                        />
                        {{-- Clear button --}}
                        <button type="button" x-show="query" x-cloak
                            @click="query=''; $el.closest('form').querySelector('input').focus(); fetch()"
                            class="flex-shrink-0 flex items-center justify-center w-5 h-5 rounded-full bg-slate-300 hover:bg-slate-400 transition-colors mr-1">
                            <svg width="8" height="8" viewBox="0 0 12 12" fill="currentColor" class="text-slate-600">
                                <path d="M10.5 1.5 6 6m0 0L1.5 10.5M6 6 10.5 10.5M6 6 1.5 1.5"/>
                            </svg>
                        </button>
                        {{-- Single search submit button --}}
                        <button type="submit"
                            class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white hover:bg-[#003377] transition-colors mr-1 my-1">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Dropdown panel --}}
                    <div x-show="isOpen" x-cloak
                         class="absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-2xl shadow-xl z-[9999] overflow-hidden"
                         style="display:none">

                        {{-- Suggested (when typing) --}}
                        <template x-if="suggested.length > 0">
                            <div>
                                <p class="px-4 pt-3 pb-1 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Suggested</p>
                                <template x-for="(item, i) in suggested" :key="i">
                                    <button type="button"
                                        @click="pick(item.label)"
                                        :class="activeIdx === i ? 'bg-[#f0f4fb]' : 'hover:bg-gray-50'"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-colors">
                                        <span class="material-symbols-outlined text-primary flex-shrink-0" style="font-size:17px">search</span>
                                        <span class="text-sm font-semibold text-gray-800" x-text="item.label"></span>
                                    </button>
                                </template>
                            </div>
                        </template>

                        {{-- Popular Searches --}}
                        <template x-if="popular.length > 0">
                            <div :class="suggested.length ? 'border-t border-gray-100' : ''">
                                <p class="px-4 pt-3 pb-1 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Popular Searches</p>
                                <template x-for="(make, j) in popular" :key="j">
                                    <button type="button"
                                        @click="pick(make)"
                                        :class="activeIdx === suggested.length + j ? 'bg-[#f0f4fb]' : 'hover:bg-gray-50'"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-left transition-colors">
                                        <span class="material-symbols-outlined text-gray-400 flex-shrink-0" style="font-size:17px">trending_up</span>
                                        <span class="text-sm text-gray-700" x-text="make"></span>
                                    </button>
                                </template>
                                <div class="h-2"></div>
                            </div>
                        </template>

                        {{-- Loading state --}}
                        <template x-if="loading">
                            <div class="px-4 py-4 flex items-center gap-2 text-sm text-gray-400">
                                <svg class="animate-spin w-4 h-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                Searching…
                            </div>
                        </template>
                    </div>
                </form>
            </div>

            {{-- ── Right side: Guest → Login/Register | Auth → Stats + Bell + Avatar ── --}}
            <div class="flex items-center gap-2.5 flex-shrink-0">

                @if($phIsGuest)
                    {{-- GUEST --}}
                    <a href="{{ route('login') }}"
                        class="hidden md:inline-flex items-center px-4 py-2 rounded-full border border-slate-300 text-slate-700 hover:border-primary hover:text-primary text-[12.5px] font-semibold transition-colors">
                        Log In
                    </a>
                    <a href="{{ route('register') }}"
                        class="hidden md:inline-flex items-center px-4 py-2 rounded-full bg-primary text-white hover:bg-[#003377] text-[12.5px] font-semibold transition-colors">
                        Register
                    </a>

                @elseif($phIsBuyer)
                    {{-- Wallet balance --}}
                    @if($phBuyingPower !== null)
                    <a href="{{ route('buyer.deposit-withdrawal') }}"
                        class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-colors"
                        title="Available buying power">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#002452" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        <span class="text-[11.5px] font-bold text-primary whitespace-nowrap">
                            ${{ number_format($phBuyingPower, 0) }}
                        </span>
                    </a>
                    @endif

                    {{-- Active bids badge --}}
                    @if($phActiveBids > 0)
                    <a href="{{ route('buyer.auctions') }}"
                        class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-100 transition-colors"
                        title="Active bids">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0"></span>
                        <span class="text-[11.5px] font-bold whitespace-nowrap">{{ $phActiveBids }} Bid{{ $phActiveBids !== 1 ? 's' : '' }}</span>
                    </a>
                    @endif

                    {{-- Notification bell --}}
                    <a href="{{ $phNotifsUrl }}"
                        class="relative flex items-center justify-center w-9 h-9 rounded-full text-slate-500 hover:text-primary hover:bg-slate-100 transition-colors"
                        title="Notifications">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        @if($phUnreadCount > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                        @endif
                    </a>
                    {{-- User avatar + dropdown --}}
                    <div class="relative" id="phMenuWrap">
                        <button id="phMenuBtn" type="button"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-full border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-colors focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {{ $phInitial }}
                            </div>
                            <span class="hidden md:block text-[12.5px] font-semibold text-slate-700 max-w-[100px] truncate">{{ explode(' ', $phUser->name)[0] }}</span>
                            <svg id="phMenuChevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400 transition-transform duration-200 flex-shrink-0">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>
                        <div id="phMenuDropdown"
                            class="absolute right-0 top-full mt-2 w-60 bg-white border border-slate-200 rounded-2xl shadow-xl z-[9999] hidden overflow-hidden">
                            {{-- User info --}}
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                                <p class="text-[10px] font-bold text-primary uppercase tracking-widest mb-0.5">Buyer Account</p>
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $phUser->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $phUser->email }}</p>
                            </div>
                            <a href="{{ route('buyer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-50">
                                <span class="material-symbols-outlined text-primary text-[18px]">dashboard</span> Dashboard
                            </a>
                            <a href="{{ route('buyer.auctions') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-50">
                                <span class="material-symbols-outlined text-primary text-[18px]">gavel</span> My Bids
                            </a>
                            <a href="{{ route('buyer.watchlist') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-50">
                                <span class="material-symbols-outlined text-primary text-[18px]">favorite</span> Watchlist
                            </a>
                            <a href="{{ route('buyer.deposit-withdrawal') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-50">
                                <span class="material-symbols-outlined text-primary text-[18px]">account_balance_wallet</span> Wallet
                            </a>
                            <a href="{{ route('buyer.user') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                                <span class="material-symbols-outlined text-primary text-[18px]">manage_accounts</span> Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                                    <span class="material-symbols-outlined text-[18px]">logout</span> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>

                @elseif($phIsSeller)
                    {{-- SELLER: Active listings stat --}}
                    <a href="{{ route('seller.dashboard') }}"
                        class="hidden lg:flex items-center gap-2 px-3 py-2 bg-[#f0f4fb] border border-[#d7e2ff] hover:bg-[#e4ecf8] transition-colors"
                        style="border-radius:0" title="Active listings">
                        <span class="material-symbols-outlined text-primary text-[16px]">inventory_2</span>
                        <span class="text-[11px] font-bold text-primary uppercase tracking-widest whitespace-nowrap">
                            {{ $phActiveListings }} Live
                        </span>
                    </a>
                    {{-- Submit listing CTA --}}
                    <a href="{{ route('seller.listings.create') }}"
                        class="hidden md:inline-flex items-center gap-1.5 px-4 py-2.5 bg-secondary-fixed-dim text-primary hover:bg-[#b8943b] transition-colors text-[11px] font-bold uppercase tracking-widest"
                        style="border-radius:0">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                        List Vehicle
                    </a>
                    {{-- Notification bell --}}
                    <a href="{{ $phNotifsUrl }}"
                        class="relative flex items-center justify-center w-10 h-10 text-gray-500 hover:text-primary transition-colors"
                        title="Notifications">
                        <span class="material-symbols-outlined text-[22px]">notifications</span>
                        @if($phUnreadCount > 0)
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center">
                            {{ $phUnreadCount > 9 ? '9+' : $phUnreadCount }}
                        </span>
                        @endif
                    </a>
                    {{-- Seller avatar + dropdown --}}
                    <div class="relative" id="phMenuWrap">
                        <button id="phMenuBtn" type="button"
                            class="flex items-center gap-2 pl-1 pr-2.5 py-1 rounded-full border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-colors focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {{ $phInitial }}
                            </div>
                            <span class="hidden md:block text-[12.5px] font-semibold text-slate-700 max-w-[100px] truncate">{{ explode(' ', $phUser->name)[0] }}</span>
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400 transition-transform duration-200 flex-shrink-0">
                                <polyline points="6 9 12 15 18 9"/>
                            </svg>
                        </button>
                        <div id="phMenuDropdown"
                            class="absolute right-0 top-full mt-2 w-60 bg-white border border-slate-200 rounded-2xl shadow-xl z-[9999] hidden overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                                <p class="text-[10px] font-bold text-primary uppercase tracking-widest mb-0.5">Seller Account</p>
                                <p class="text-sm font-bold text-slate-900 truncate">{{ $phUser->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $phUser->email }}</p>
                            </div>
                            <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-50">
                                <span class="material-symbols-outlined text-primary text-[18px]">dashboard</span> Dashboard
                            </a>
                            <a href="{{ route('seller.auctions') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-50">
                                <span class="material-symbols-outlined text-primary text-[18px]">inventory_2</span> My Listings
                            </a>
                            <a href="{{ route('seller.listings.create') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                                <span class="material-symbols-outlined text-primary text-[18px]">add_circle</span> New Listing
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors text-left">
                                    <span class="material-symbols-outlined text-[18px]">logout</span> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>

                @else
                    {{-- INCOMPLETE REGISTRATION: show limited nav --}}
                    <a href="{{ route('finish.registration') }}"
                        class="px-5 py-2.5 bg-secondary-fixed-dim text-primary font-bold text-sm hidden md:inline-block hover:bg-[#b8943b] transition-colors"
                        style="border-radius:0">
                        Complete Registration
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-semibold transition-colors" style="border-radius:0">
                            Sign Out
                        </button>
                    </form>
                @endif

                {{-- Mobile hamburger (always shown) --}}
                <button id="phMobileBtn" type="button" class="md:hidden flex items-center justify-center w-10 h-10 text-gray-700 hover:bg-gray-100 transition-colors" style="border-radius:0">
                    <span class="material-symbols-outlined" id="phMobileIcon">menu</span>
                </button>
            </div>
        </div>
    </div>
</header>

{{-- ══════════════════════════════════════════════════════════════
     SECONDARY NAV BAR: Page links + Language selector
══════════════════════════════════════════════════════════════ --}}
<div class="bg-white border-b border-slate-100 hidden md:block">
    <div class="relative w-full max-w-[1280px] mx-auto px-4 md:px-10 py-0 flex justify-center items-center min-h-[44px]">
        <nav class="flex items-center gap-6">

            {{-- Home --}}
            <a href="{{ route('welcome') }}"
                class="inline-flex items-center py-3 border-b-2 {{ $phHomeActive ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-primary hover:border-slate-300' }} transition-colors text-[11.5px] font-bold uppercase tracking-widest whitespace-nowrap">
                Home
            </a>

            {{-- Auction --}}
            <a href="{{ route('Auction.index') }}"
                class="inline-flex items-center py-3 border-b-2 {{ $phAuctionActive ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-primary hover:border-slate-300' }} transition-colors text-[11.5px] font-bold uppercase tracking-widest whitespace-nowrap">
                Auction
            </a>

            {{-- Getting Started --}}
            <a href="{{ route('buyer-guide') }}"
                class="inline-flex items-center py-3 border-b-2 {{ $phGettingStartedActive ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-primary hover:border-slate-300' }} transition-colors text-[11.5px] font-bold uppercase tracking-widest whitespace-nowrap">
                Getting Started
            </a>

            {{-- Services & Support (dropdown) --}}
            <div class="relative" id="phSupportWrap">
                <button type="button" id="phSupportBtn"
                    class="inline-flex items-center gap-1 py-3 border-b-2 border-transparent text-slate-500 hover:text-primary hover:border-slate-300 transition-colors text-[11.5px] font-bold uppercase tracking-widest whitespace-nowrap focus:outline-none"
                    aria-haspopup="true" aria-expanded="false">
                    Services &amp; Support
                    <svg id="phSupportChevron" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-200">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>

                {{-- Dropdown panel --}}
                <div id="phSupportDropdown"
                    class="absolute left-0 top-full mt-1 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl z-[9999] hidden overflow-hidden">

                    <a href="{{ route('help-center') }}"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100 font-semibold transition-colors">
                        <span class="material-symbols-outlined text-primary text-[17px]">help</span>
                        Help Center
                    </a>
                    <a href="{{ route('buyer-guide') }}"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100 font-semibold transition-colors">
                        <span class="material-symbols-outlined text-primary text-[17px]">person</span>
                        Buyer Guide
                    </a>
                    <a href="{{ route('sellers-guide') }}"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100 font-semibold transition-colors">
                        <span class="material-symbols-outlined text-primary text-[17px]">storefront</span>
                        Seller Guide
                    </a>
                    <a href="{{ route('fee-calculator') }}"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100 font-semibold transition-colors">
                        <span class="material-symbols-outlined text-primary text-[17px]">calculate</span>
                        Fee Calculator
                    </a>
                    <a href="{{ route('video-guide') }}"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 font-semibold transition-colors">
                        <span class="material-symbols-outlined text-primary text-[17px]">play_circle</span>
                        Video Guides
                    </a>
                </div>
            </div>

            {{-- Contact Us --}}
            <a href="{{ route('contact') }}"
                class="inline-flex items-center py-3 border-b-2 {{ $phContactActive ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-primary hover:border-slate-300' }} transition-colors text-[11.5px] font-bold uppercase tracking-widest whitespace-nowrap">
                Contact Us
            </a>

        </nav>

        {{-- Language / Region — absolutely pinned to the right --}}
        <div class="absolute right-4 md:right-10 flex items-center gap-1.5 cursor-pointer px-2.5 py-1 rounded-full bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-colors">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-500">
                <circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
            </svg>
            <span class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">EN</span>
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MOBILE MENU (shown on hamburger click)
══════════════════════════════════════════════════════════════ --}}
<div id="phMobileMenu" class="md:hidden hidden bg-white border-b border-gray-200 shadow-lg">
    <div class="px-4 py-4 space-y-2">
        {{-- Mobile search --}}
        <div class="relative mb-4"
             x-data="cmSearch('{{ Route::has('auction.suggest') ? route('auction.suggest') : url('/auction-suggest') }}', '{{ route('Auction.index') }}')"
             @click.outside="close()"
             @keydown.escape.window="close()">
            <form method="GET" action="{{ route('Auction.index') }}" :action="auctionUrl" @submit.prevent="submit()" class="relative flex">
                <input type="text" name="search" x-model="query"
                    @focus="open()"
                    @input.debounce.250ms="fetch()"
                    @keydown.arrow-down.prevent="moveDown()"
                    @keydown.arrow-up.prevent="moveUp()"
                    @keydown.enter.prevent="selectActive()"
                    placeholder="Search auctions…"
                    autocomplete="off"
                    class="w-full pl-4 pr-12 py-3 border border-gray-300 bg-white text-sm focus:outline-none focus:ring-1 focus:ring-primary"
                    style="border-radius:0"/>
                <button type="submit"
                    class="absolute right-0 top-0 bottom-0 px-4 bg-primary text-white flex items-center justify-center"
                    style="border-radius:0">
                    <span class="material-symbols-outlined text-[18px]">search</span>
                </button>
            </form>
            {{-- Mobile dropdown --}}
            <div x-show="isOpen" x-cloak
                 class="absolute left-0 right-0 top-full bg-white border border-gray-200 shadow-xl z-[9999]"
                 style="border-radius:0; display:none">
                <template x-if="suggested.length > 0">
                    <div>
                        <p class="px-4 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Suggested</p>
                        <template x-for="(item, i) in suggested" :key="i">
                            <button type="button" @click="pick(item.label)"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-left hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-primary flex-shrink-0" style="font-size:16px">search</span>
                                <span class="text-sm font-semibold text-gray-800" x-text="item.label"></span>
                            </button>
                        </template>
                    </div>
                </template>
                <template x-if="popular.length > 0">
                    <div :class="suggested.length ? 'border-t border-gray-100' : ''">
                        <p class="px-4 pt-3 pb-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Popular Searches</p>
                        <template x-for="(make, j) in popular" :key="j">
                            <button type="button" @click="pick(make)"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-left hover:bg-gray-50 transition-colors">
                                <span class="material-symbols-outlined text-gray-400 flex-shrink-0" style="font-size:16px">trending_up</span>
                                <span class="text-sm text-gray-700" x-text="make"></span>
                            </button>
                        </template>
                        <div class="h-2"></div>
                    </div>
                </template>
            </div>
        </div>
        {{-- Mobile nav links --}}
        <a href="{{ route('welcome') }}" class="flex items-center gap-3 py-3 border-b border-gray-100 text-sm font-bold text-gray-800">
            <span class="material-symbols-outlined text-primary text-[18px]">home</span> Home
        </a>
        <a href="{{ route('Auction.index') }}" class="flex items-center gap-3 py-3 border-b border-gray-100 text-sm font-bold text-gray-800">
            <span class="material-symbols-outlined text-primary text-[18px]">gavel</span> Auction
        </a>
        <a href="{{ route('buyer-guide') }}" class="flex items-center gap-3 py-3 border-b border-gray-100 text-sm font-bold text-gray-800">
            <span class="material-symbols-outlined text-primary text-[18px]">rocket_launch</span> Getting Started
        </a>

        {{-- Services & Support accordion --}}
        <div>
            <button type="button" id="phMobileSupportBtn"
                class="w-full flex items-center justify-between gap-3 py-3 border-b border-gray-100 text-sm font-bold text-gray-800 focus:outline-none">
                <span class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary text-[18px]">support_agent</span>
                    Services &amp; Support
                </span>
                <span class="material-symbols-outlined text-gray-400 text-[18px] transition-transform duration-200" id="phMobileSupportChevron">expand_more</span>
            </button>
            <div id="phMobileSupportPanel" class="hidden pl-9 pb-1 space-y-0">
                <a href="{{ route('help-center') }}" class="flex items-center gap-2 py-2.5 text-sm text-gray-600 hover:text-primary border-b border-gray-50 font-medium">
                    <span class="material-symbols-outlined text-[15px]">help</span> Help Center
                </a>
                <a href="{{ route('buyer-guide') }}" class="flex items-center gap-2 py-2.5 text-sm text-gray-600 hover:text-primary border-b border-gray-50 font-medium">
                    <span class="material-symbols-outlined text-[15px]">person</span> Buyer Guide
                </a>
                <a href="{{ route('sellers-guide') }}" class="flex items-center gap-2 py-2.5 text-sm text-gray-600 hover:text-primary border-b border-gray-50 font-medium">
                    <span class="material-symbols-outlined text-[15px]">storefront</span> Seller Guide
                </a>
                <a href="{{ route('fee-calculator') }}" class="flex items-center gap-2 py-2.5 text-sm text-gray-600 hover:text-primary border-b border-gray-50 font-medium">
                    <span class="material-symbols-outlined text-[15px]">calculate</span> Fee Calculator
                </a>
                <a href="{{ route('video-guide') }}" class="flex items-center gap-2 py-2.5 text-sm text-gray-600 hover:text-primary font-medium">
                    <span class="material-symbols-outlined text-[15px]">play_circle</span> Video Guides
                </a>
            </div>
        </div>

        <a href="{{ route('contact') }}" class="flex items-center gap-3 py-3 border-b border-gray-100 text-sm font-bold text-gray-800">
            <span class="material-symbols-outlined text-primary text-[18px]">mail</span> Contact Us
        </a>
        {{-- Auth actions --}}
        @if($phIsGuest)
        <div class="pt-3 flex gap-3">
            <a href="{{ route('login') }}"
                class="flex-1 py-3 text-center border-2 border-primary text-primary font-bold text-sm"
                style="border-radius:0">Login</a>
            <a href="{{ route('register') }}"
                class="flex-1 py-3 text-center bg-primary text-white font-bold text-sm hover:bg-[#003377] transition-colors"
                style="border-radius:0">Register</a>
        </div>
        @else
        <div class="pt-3 border-t border-gray-200 space-y-1">
            <div class="flex items-center gap-3 py-2 mb-2">
                <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center text-base font-bold flex-shrink-0">
                    {{ $phInitial }}
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">{{ $phUser->name }}</p>
                    <p class="text-xs text-gray-500">{{ ucfirst($phUser->role ?? 'user') }}</p>
                </div>
            </div>
            @if($phIsBuyer)
            <a href="{{ route('buyer.dashboard') }}" class="flex items-center gap-3 px-2 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                <span class="material-symbols-outlined text-primary text-[17px]">dashboard</span> Dashboard
            </a>
            <a href="{{ route('buyer.auctions') }}" class="flex items-center gap-3 px-2 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                <span class="material-symbols-outlined text-primary text-[17px]">gavel</span> My Bids
            </a>
            <a href="{{ route('buyer.watchlist') }}" class="flex items-center gap-3 px-2 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                <span class="material-symbols-outlined text-primary text-[17px]">favorite</span> Watchlist
            </a>
            @elseif($phIsSeller)
            <a href="{{ route('seller.dashboard') }}" class="flex items-center gap-3 px-2 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                <span class="material-symbols-outlined text-primary text-[17px]">dashboard</span> Dashboard
            </a>
            <a href="{{ route('seller.auctions') }}" class="flex items-center gap-3 px-2 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                <span class="material-symbols-outlined text-primary text-[17px]">inventory_2</span> My Listings
            </a>
            <a href="{{ route('seller.listings.create') }}" class="flex items-center gap-3 px-2 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                <span class="material-symbols-outlined text-primary text-[17px]">add_circle</span> New Listing
            </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-gray-100 mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-2 py-2.5 text-sm text-red-600 hover:bg-red-50">
                    <span class="material-symbols-outlined text-[17px]">logout</span> Sign Out
                </button>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- ── Inline header JS ───────────────────────────────────────── --}}
<script>
function cmSearch(suggestUrl, auctionUrl) {
    return {
        suggestUrl,
        auctionUrl,
        query: '{{ addslashes(request('search', '')) }}',
        isOpen: false,
        loading: false,
        suggested: [],
        popular: [],
        activeIdx: -1,
        _cache: {},
        _req: null,

        open() {
            this.isOpen = true;
            if (!this.query) this.fetch();
        },
        close() {
            this.isOpen = false;
            this.activeIdx = -1;
        },
        allItems() {
            return [...this.suggested, ...this.popular.map(m => ({ label: m }))];
        },
        moveDown() {
            const max = this.suggested.length + this.popular.length - 1;
            this.activeIdx = this.activeIdx < max ? this.activeIdx + 1 : 0;
        },
        moveUp() {
            const max = this.suggested.length + this.popular.length - 1;
            this.activeIdx = this.activeIdx > 0 ? this.activeIdx - 1 : max;
        },
        selectActive() {
            const items = this.allItems();
            if (this.activeIdx >= 0 && items[this.activeIdx]) {
                this.pick(items[this.activeIdx].label);
            } else {
                this.submit();
            }
        },
        pick(label) {
            this.query = label;
            this.close();
            this.$nextTick(() => this.submit());
        },
        submit() {
            window.location.href = this.auctionUrl + '?search=' + encodeURIComponent(this.query);
        },
        fetch() {
            this.isOpen = true;
            this.activeIdx = -1;
            const q = this.query.trim();
            if (this._cache[q]) {
                this.suggested = this._cache[q].suggested;
                this.popular   = this._cache[q].popular;
                return;
            }
            if (this._req) { this._req.abort && this._req.abort(); }
            this.loading = true;
            const ctrl = new AbortController();
            this._req = ctrl;
            fetch(this.suggestUrl + '?q=' + encodeURIComponent(q), { signal: ctrl.signal })
                .then(r => r.ok ? r.json() : Promise.reject())
                .then(data => {
                    this.suggested = data.suggested || [];
                    this.popular   = data.popular   || [];
                    this._cache[q] = { suggested: this.suggested, popular: this.popular };
                    this.loading = false;
                })
                .catch(() => { this.loading = false; this.isOpen = false; });
        },
    };
}

(function () {
    /* User avatar dropdown */
    var menuBtn  = document.getElementById('phMenuBtn');
    var menuDrop = document.getElementById('phMenuDropdown');
    if (menuBtn && menuDrop) {
        menuBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = !menuDrop.classList.contains('hidden');
            menuDrop.classList.toggle('hidden', open);
            var chevron = document.getElementById('phMenuChevron');
            if (chevron) chevron.style.transform = open ? '' : 'rotate(180deg)';
        });
        document.addEventListener('click', function (e) {
            if (!menuBtn.contains(e.target) && !menuDrop.contains(e.target)) {
                menuDrop.classList.add('hidden');
                var chevron = document.getElementById('phMenuChevron');
                if (chevron) chevron.style.transform = '';
            }
        });
    }

    /* Services & Support dropdown (desktop) */
    var suppBtn     = document.getElementById('phSupportBtn');
    var suppDrop    = document.getElementById('phSupportDropdown');
    var suppChevron = document.getElementById('phSupportChevron');
    var suppWrap    = document.getElementById('phSupportWrap');
    if (suppBtn && suppDrop) {
        suppBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = !suppDrop.classList.contains('hidden');
            suppDrop.classList.toggle('hidden', open);
            if (suppChevron) suppChevron.style.transform = open ? '' : 'rotate(180deg)';
            suppBtn.setAttribute('aria-expanded', open ? 'false' : 'true');
        });
        document.addEventListener('click', function (e) {
            if (suppWrap && !suppWrap.contains(e.target)) {
                suppDrop.classList.add('hidden');
                if (suppChevron) suppChevron.style.transform = '';
                suppBtn.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* Services & Support accordion (mobile) */
    var mobileSuppBtn     = document.getElementById('phMobileSupportBtn');
    var mobileSuppPanel   = document.getElementById('phMobileSupportPanel');
    var mobileSuppChevron = document.getElementById('phMobileSupportChevron');
    if (mobileSuppBtn && mobileSuppPanel) {
        mobileSuppBtn.addEventListener('click', function () {
            var open = mobileSuppPanel.classList.toggle('hidden');
            if (mobileSuppChevron) mobileSuppChevron.style.transform = open ? '' : 'rotate(180deg)';
        });
    }

    /* Mobile hamburger menu */
    var mobileBtn  = document.getElementById('phMobileBtn');
    var mobileMenu = document.getElementById('phMobileMenu');
    var mobileIcon = document.getElementById('phMobileIcon');
    if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', function () {
            var open = mobileMenu.classList.toggle('hidden');
            if (mobileIcon) mobileIcon.textContent = open ? 'menu' : 'close';
        });
    }
})();
</script>
