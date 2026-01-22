@php
    $user = Auth::user();
    $isGuest = !$user;
    $isBuyer = $user && $user->role === 'buyer';
    $isSeller = $user && $user->role === 'seller';
@endphp

<!-- Top Header Bar -->
<div class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <!-- First Header Row: Logo, Search, Actions -->
    <div class="bg-white">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="flex items-center">
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-xl">CM</span>
                            </div>
                            <span class="text-2xl font-bold text-gray-900">CayMark</span>
                        </div>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl mx-8">
                    <form action="{{ route('Auction.index') }}" method="GET" class="relative">
                        <input 
                            type="text" 
                            name="search"
                            placeholder="Search for vehicles by make, model, VIN, lot" 
                            class="w-full px-4 py-2.5 pl-12 pr-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ request('search') }}"
                        >
                        <button type="submit" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </form>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center space-x-4">
                    @if($isGuest)
                        <!-- Guest Mode: Register & Login -->
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition-colors">
                            REGISTER
                        </a>
                        <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                            LOGIN
                        </a>
                    @elseif($isBuyer)
                        <!-- Buyer Mode: Notifications, Wallet, Profile -->
                        <a href="{{ route('buyer.notifications') }}" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors" title="Notifications">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if($user->unreadNotifications()->count() > 0)
                                <span class="absolute top-0 right-0 w-4 h-4 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">{{ $user->unreadNotifications()->count() }}</span>
                            @endif
                        </a>
                        <a href="{{ route('buyer.escrow') }}" class="flex items-center space-x-2 px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-sm font-semibold">Wallet</span>
                        </a>
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                <a href="{{ route('dashboard.buyer') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Buyer Dashboard</a>
                                <a href="{{ route('buyer.messaging-center') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Messages / Notifications</a>
                                <a href="{{ route('buyer.escrow') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Wallet / Deposits</a>
                                <a href="{{ route('dashboard.buyer') }}?tab=user" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Settings</a>
                                <hr class="my-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</button>
                                </form>
                            </div>
                        </div>
                    @elseif($isSeller)
                        <!-- Seller Mode: Submit Listing, Notifications, Profile -->
                        <a href="{{ route('seller.listings.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                            SUBMIT LISTING
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
                            <button @click="open = !open" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                <a href="{{ route('dashboard.seller') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Seller Dashboard</a>
                                <a href="{{ route('seller.listings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Listings</a>
                                <a href="{{ route('seller.chat') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Messages / Notifications</a>
                                <a href="{{ route('seller.payouts') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Payouts</a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Account Settings</a>
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
                <!-- Main Menu -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('welcome') }}" class="py-3 px-2 text-gray-700 hover:text-blue-600 font-medium transition-colors {{ request()->routeIs('welcome') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">HOME</a>
                    <a href="{{ route('Auction.index') }}" class="py-3 px-2 text-gray-700 hover:text-blue-600 font-medium transition-colors {{ request()->routeIs('Auction.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">AUCTIONS</a>
                    <a href="{{ route('buyer-guide') }}" class="py-3 px-2 text-gray-700 hover:text-blue-600 font-medium transition-colors">GETTING STARTED</a>
                    
                    <!-- Services & Support Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="py-3 px-2 text-gray-700 hover:text-blue-600 font-medium transition-colors flex items-center">
                            SERVICES & SUPPORT
                            <svg class="w-4 h-4 ml-1 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute top-full left-0 mt-1 w-72 bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden z-50" style="border-radius: 0.5rem 0.5rem 0.5rem 0.5rem;">
                            <a href="{{ route('fee-calculator') }}" class="block px-5 py-3.5 text-sm font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors {{ request()->routeIs('fee-calculator') ? 'bg-blue-100' : '' }}">
                                Fee Calculator
                            </a>
                            <a href="{{ route('help-center') }}" class="block px-5 py-3.5 text-sm text-gray-800 hover:bg-gray-50 transition-colors">
                                Help Center
                            </a>
                            <a href="{{ route('help-center') }}#registration-membership" class="block px-5 py-3.5 text-sm text-gray-800 hover:bg-gray-50 transition-colors">
                                Registration & Membership FAQ
                            </a>
                            <a href="{{ route('rules-policies') }}" class="block px-5 py-3.5 text-sm text-gray-800 hover:bg-gray-50 transition-colors">
                                Rules & Policies
                            </a>
                            <a href="{{ route('contact') }}" class="block px-5 py-3.5 text-sm text-gray-800 hover:bg-gray-50 transition-colors">
                                Contact Us
                            </a>
                        </div>
                    </div>
                    
                    <a href="{{ route('help-center') }}" class="py-3 px-2 text-gray-700 hover:text-blue-600 font-medium transition-colors">HELP</a>
                </div>

                <!-- Notification Bar (if needed) -->
                <div class="flex items-center">
                    @if($isBuyer || $isSeller)
                        <div class="text-sm text-gray-600">
                            <span class="inline-flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                Live Auctions Available
                            </span>
                        </div>
                    @endif
                </div>
            </nav>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
