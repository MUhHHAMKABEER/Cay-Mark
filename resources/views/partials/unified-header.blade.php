@php
    $user = Auth::user();
    $isGuest = !$user;
    $isBuyer = $user && $user->role === 'buyer';
    $isSeller = $user && $user->role === 'seller';
    $registrationComplete = $user && $user->isRegistrationComplete();
    $buyerHeaderStats = null;
    if ($isBuyer && $user) {
        $wallet = \App\Models\UserWallet::getOrCreateForUser($user->id);
        $activeBids = $user->bids()->where('status', 'active')->count();
        // Match dashboard: won = auctions ended where user has highest bid (paid or pending)
        $wonCount = $user->getWonAuctions()->count();
        $watchlistCount = $user->watchlist()->count();
        $buyerHeaderStats = [
            'buying_power' => (float) $wallet->available_balance,
            'active_bids' => $activeBids,
            'won_count' => $wonCount,
            'watchlist_count' => $watchlistCount,
        ];
    }
    $sellerHeaderStats = null;
    if ($isSeller && $user) {
        $activeListings = \App\Models\Listing::where('seller_id', $user->id)->where('listing_state', 'active')->where('status', 'approved')->count();
        $recentlyFinished = \App\Models\Listing::where('seller_id', $user->id)->where('listing_state', 'ended')->where('updated_at', '>=', now()->subDays(30))->count();
        $sellerHeaderStats = ['active_auctions' => $activeListings, 'recently_finished' => $recentlyFinished];
    }
    // Notification bar: for registered users only (buyers, sellers, or users who haven't selected role yet)
    $showNotificationBar = !$isGuest;
    $unreadNotificationCount = $user ? $user->unreadNotifications()->count() : 0;
@endphp

<!-- Top Header Bar (white for all users, same as normal dashboard) -->
<div class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <!-- First Header Row: Logo, Search (centered), Actions -->
    <div class="bg-white">
        <div class="container mx-auto px-4 py-3 relative">
            <div class="flex items-center justify-between gap-4">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0">
                    <a href="{{ route('welcome') }}" class="flex items-center min-h-[2.5rem] md:min-h-[3rem]">
                        <img src="{{ asset(config('logos.header', 'Logos/Caymark Logo.png')) }}" alt="CayMark" class="h-16 md:h-20 w-auto max-w-[280px] object-contain" width="200" height="80" loading="eager" />
                    </a>
                </div>

                <!-- Search Bar (centered; on small screens hidden so Register/Login stay visible) -->
                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xl px-4 pointer-events-none hidden sm:block">
                    <div class="pointer-events-auto">
                        <form action="{{ route('Auction.index') }}" method="GET" class="relative buyer-search-form" id="buyer-header-search">
                            <input 
                                type="text" 
                                name="search"
                                placeholder="Search for vehicles by make, model, island..." 
                                class="w-full px-4 py-2.5 pl-4 pr-24 rounded-full border border-gray-300 bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                value="{{ request('search') }}"
                                id="buyer-header-search-input"
                            >
                            <button type="button" class="absolute right-12 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1 clear-search-btn" aria-label="Clear search" style="display: none;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors" aria-label="Search">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Actions (always visible for guests: z-index and min-width so they are never hidden) -->
                <div class="flex items-center space-x-2 sm:space-x-3 flex-shrink-0 relative z-10 min-w-0">
                    @if($isGuest)
                        <!-- Guest Mode: Register & Login (ensure always visible on all screen sizes) -->
                        <a href="{{ route('register') }}" class="guest-btn px-3 py-2 sm:px-4 sm:py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm sm:text-base font-semibold rounded-lg transition-colors whitespace-nowrap">
                            REGISTER
                        </a>
                        <a href="{{ route('login') }}" class="guest-btn px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm sm:text-base font-semibold rounded-lg transition-colors whitespace-nowrap">
                            LOGIN
                        </a>
                    @elseif($isBuyer)
                        <!-- Buyer Mode: Buying Power, Bid Status, Watchlist + Profile -->
                        <div class="hidden lg:flex items-center gap-4">
                            <a href="{{ route('buyer.deposit-withdrawal') }}" class="buyer-header-stat flex flex-col items-center rounded-lg bg-gray-100 hover:bg-gray-200 px-3 py-2 min-w-[100px] transition-colors border border-gray-200">
                                <span class="flex items-center gap-1 text-gray-600 text-xs">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Buying Power
                                </span>
                                <span class="text-gray-900 font-semibold text-sm">${{ number_format($buyerHeaderStats['buying_power'], 0) }}</span>
                            </a>
                            <a href="{{ route('dashboard.buyer', ['tab' => 'auctions']) }}" class="buyer-header-stat flex flex-col items-center rounded-lg bg-gray-100 hover:bg-gray-200 px-3 py-2 min-w-[90px] transition-colors border border-gray-200">
                                <span class="flex items-center gap-1 text-gray-600 text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                                    Bid Status
                                </span>
                                <span class="text-gray-900 font-semibold text-sm">{{ $buyerHeaderStats['active_bids'] }}|{{ $buyerHeaderStats['won_count'] }}</span>
                            </a>
                            <a href="{{ route('buyer.watchlist') }}" class="buyer-header-stat flex flex-col items-center rounded-lg bg-gray-100 hover:bg-gray-200 px-3 py-2 min-w-[80px] transition-colors border border-gray-200">
                                <span class="flex items-center gap-1 text-gray-600 text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                    Watchlist
                                </span>
                                <span class="text-gray-900 font-semibold text-sm">{{ $buyerHeaderStats['watchlist_count'] }}</span>
                            </a>
                        </div>
                        <!-- Profile -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="w-10 h-10 rounded-full bg-blue-500 hover:bg-blue-600 flex items-center justify-center text-white font-semibold text-lg transition-colors flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50">
                                <a href="{{ route('dashboard.buyer') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Buyer Dashboard</a>
                                <a href="{{ route('buyer.messaging-center') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Messages / Notifications</a>
                                <a href="{{ route('buyer.deposit-withdrawal') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Wallet / Deposits</a>
                                <a href="{{ route('dashboard.buyer') }}?tab=user" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Account Settings</a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    @elseif($isSeller)
                        <!-- Seller Mode: Active/Recent counts + Quick links + Profile -->
                        <div class="hidden lg:flex items-center gap-3">
                            <a href="{{ route('dashboard.seller') }}" class="flex flex-col items-center rounded-lg bg-gray-100 hover:bg-gray-200 px-3 py-2 min-w-[80px] transition-colors text-sm">
                                <span class="text-gray-600">Active</span>
                                <span class="font-semibold text-gray-900">{{ $sellerHeaderStats['active_auctions'] }}</span>
                            </a>
                            <a href="{{ route('dashboard.seller', ['tab' => 'auctions']) }}" class="flex flex-col items-center rounded-lg bg-gray-100 hover:bg-gray-200 px-3 py-2 min-w-[80px] transition-colors text-sm">
                                <span class="text-gray-600">Recently finished</span>
                                <span class="font-semibold text-gray-900">{{ $sellerHeaderStats['recently_finished'] }}</span>
                            </a>
                        </div>
                        <a href="{{ route('seller.listings.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                            Submit listing
                        </a>
                        <a href="{{ route('dashboard.seller') }}" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors" title="Notifications">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($user->unreadNotifications()->count() > 0)
                                <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">{{ $user->unreadNotifications()->count() }}</span>
                            @endif
                        </a>
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                <a href="{{ route('dashboard.seller') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                <a href="{{ route('Auction.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Auctions</a>
                                <a href="{{ route('seller.listings.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Submit listing</a>
                                <a href="{{ route('seller.payouts') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payouts</a>
                                <a href="{{ route('dashboard.seller', ['tab' => 'auctions']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My listings</a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account settings</a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Logged in but registration incomplete: user icon + dropdown (Complete registration, Logout) -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="w-10 h-10 rounded-full bg-blue-500 hover:bg-blue-600 flex items-center justify-center text-white font-semibold text-lg transition-colors flex-shrink-0" title="Account">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl border border-gray-200 py-2 z-50">
                                <a href="{{ route('finish.registration') }}" class="block px-4 py-2 text-sm text-blue-600 font-medium hover:bg-blue-50">Complete registration</a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Second Header Row: Main Menu -->
    <div class="bg-gray-50 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <nav class="flex items-center justify-between">
                <!-- Main Menu: Home, Auction, Getting Started, Services & Support, Contact Us (no Help) -->
                <div class="flex items-center space-x-6 lg:space-x-8 flex-wrap">
                    <a href="{{ route('welcome') }}" class="py-3 px-2 font-medium transition-colors text-gray-700 hover:text-blue-600 {{ request()->routeIs('welcome') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Home</a>
                    <a href="{{ route('Auction.index') }}" class="py-3 px-2 font-medium transition-colors text-gray-700 hover:text-blue-600 {{ request()->routeIs('Auction.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Auction</a>
                    <a href="{{ route('buyer-guide') }}" class="py-3 px-2 font-medium transition-colors text-gray-700 hover:text-blue-600 {{ request()->routeIs('buyer-guide') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Getting Started</a>
                    <!-- Services & Support Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" @click.away="open = false" class="py-3 px-2 font-medium transition-colors flex items-center text-gray-700 hover:text-blue-600">
                            Services & Support
                            <svg class="w-4 h-4 ml-1 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute top-full left-0 mt-1 w-72 bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden z-50">
                            <a href="{{ route('fee-calculator') }}" class="block px-5 py-3.5 text-sm font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors {{ request()->routeIs('fee-calculator') ? 'bg-blue-100' : '' }}">
                                Fee Calculator
                            </a>
                            <a href="{{ route('help-center') }}" class="block px-5 py-3.5 text-sm text-gray-800 hover:bg-gray-50 transition-colors">
                                Help Center
                            </a>
                            <a href="{{ route('video-guide') }}" class="block px-5 py-3.5 text-sm text-gray-800 hover:bg-gray-50 transition-colors">
                                Video guides
                            </a>
                            <a href="{{ route('policy') }}" class="block px-5 py-3.5 text-sm text-gray-800 hover:bg-gray-50 transition-colors">
                                Policy
                            </a>
                            <a href="{{ route('tow-provider.index') }}" class="block px-5 py-3.5 text-sm text-gray-800 hover:bg-gray-50 transition-colors">
                                Tow Provider Support
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('contact') }}" class="py-3 px-2 font-medium transition-colors text-gray-700 hover:text-blue-600 {{ request()->routeIs('contact') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">Contact Us</a>
                </div>

            </nav>
        </div>
    </div>
    <!-- Notification bar: green, white text, registered users only (notification center) -->
    @if($showNotificationBar)
    <div class="border-t border-green-700 bg-green-600">
        <div class="container mx-auto px-4 py-2">
            <div class="flex items-center justify-center gap-2 text-sm text-white flex-wrap">
                <span class="w-2 h-2 bg-white rounded-full animate-pulse flex-shrink-0"></span>
                @if($isBuyer)
                    <a href="{{ route('buyer.messaging-center') }}" class="hover:underline font-medium">
                        @if($unreadNotificationCount > 0)
                            <span>{{ $unreadNotificationCount }} new notification{{ $unreadNotificationCount !== 1 ? 's' : '' }} — bid confirmations, outbid alerts, new bids on watched listings, payment required, new listings to checkout</span>
                        @else
                            <span>Notification center — bid confirmations &amp; updates, alerts on watched/owned listings, payment required, new listings to checkout</span>
                        @endif
                    </a>
                @elseif($isSeller)
                    <a href="{{ route('dashboard.seller') }}" class="hover:underline font-medium">
                        @if($unreadNotificationCount > 0)
                            <span>{{ $unreadNotificationCount }} new notification{{ $unreadNotificationCount !== 1 ? 's' : '' }} — new bids, listing activity, payment required, reminder to create listings</span>
                        @else
                            <span>Notification center — new bids on your listings, listing status updates, payment required, create new listing</span>
                        @endif
                    </a>
                @else
                    <span>Notification center — incomplete registration reminders, account activity, and updates</span>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    [x-cloak] { display: none !important; }
    .buyer-header-bar .buyer-search-form input { min-height: 44px; }
    @media (max-width: 1023px) {
        .buyer-header-stat { min-width: 70px; padding: 0.5rem 0.25rem; font-size: 0.75rem; }
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var inp = document.getElementById('buyer-header-search-input');
    var clearBtn = document.querySelector('.clear-search-btn');
    if (inp && clearBtn) {
        inp.addEventListener('input', function() { clearBtn.style.display = this.value ? 'block' : 'none'; });
        inp.addEventListener('keyup', function() { clearBtn.style.display = this.value ? 'block' : 'none'; });
        clearBtn.addEventListener('click', function() { inp.value = ''; clearBtn.style.display = 'none'; inp.focus(); });
        if (inp.value) clearBtn.style.display = 'block';
    }
});
</script>
