@extends('layouts.dashboard')

@section('title', 'Buyer Dashboard - CayMark')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
            <h1 class="text-3xl font-bold text-gray-900">Buyer Dashboard</h1>
            <p class="text-gray-600 mt-2">Manage your auctions, bids, and purchases</p>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px overflow-x-auto">
                    <button onclick="showTab('user')" 
                            id="tab-user" 
                            class="tab-button active px-6 py-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600 whitespace-nowrap">
                        USER
                    </button>
                    <button onclick="showTab('auctions')" 
                            id="tab-auctions" 
                            class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent whitespace-nowrap">
                        AUCTIONS
                    </button>
                    <button onclick="showTab('saved')" 
                            id="tab-saved" 
                            class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent whitespace-nowrap">
                        SAVED ITEMS
                    </button>
                    <button onclick="showTab('notifications')" 
                            id="tab-notifications" 
                            class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent whitespace-nowrap">
                        NOTIFICATIONS
                    </button>
                    <button onclick="showTab('messaging')" 
                            id="tab-messaging" 
                            class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent whitespace-nowrap">
                        MESSAGING CENTER
                    </button>
                    <button onclick="showTab('support')" 
                            id="tab-support" 
                            class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent whitespace-nowrap">
                        CUSTOMER SUPPORT
                </button>
                </nav>
            </div>

            <!-- USER TAB -->
            <div id="content-user" class="tab-content p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Account Information</h2>

                <!-- Full Name -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                        <span class="text-gray-900">{{ $user->name }}</span>
                    </div>
                </div>

                <!-- Email Address -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registered Email Address</label>
                    <form method="POST" action="{{ route('buyer-dashboard.update-email') }}" class="flex items-center space-x-3">
                        @csrf
                        <div class="flex-1">
                            <input type="email" 
                                   name="email" 
                                   value="{{ $user->email }}" 
                                   class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-3 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                            Update Email
                    </button>
                    </form>
                    <p class="text-sm text-gray-500 mt-2">Email is visible only and can be edited.</p>
                </div>

                <!-- Account Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                        <span class="text-gray-900 font-semibold">Buyer</span>
                    </div>
                </div>

                <!-- ID -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">ID</label>
                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                        <span class="text-gray-900">{{ $user->id }}</span>
                    </div>
                </div>

                <!-- Password Management -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Management</label>
                    <button onclick="showPasswordModal()" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition duration-200">
                        Change Password
                    </button>
                    <p class="text-sm text-gray-500 mt-2">Password is not displayed.</p>
                </div>
            </div>

            <!-- AUCTIONS TAB -->
            <div id="content-auctions" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">My Auctions</h2>

                <!-- Sub-tabs for CURRENT, WON, LOST -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="flex -mb-px">
                        <button onclick="showAuctionSection('current')" 
                                id="auction-current" 
                                class="auction-tab-button active px-6 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                            CURRENT
                        </button>
                        <button onclick="showAuctionSection('won')" 
                                id="auction-won" 
                                class="auction-tab-button px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                            WON
                        </button>
                        <button onclick="showAuctionSection('lost')" 
                                id="auction-lost" 
                                class="auction-tab-button px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                            LOST
                        </button>
                    </nav>
                </div>

                <!-- CURRENT AUCTIONS -->
                <div id="auction-section-current" class="auction-section">
                    @if($currentAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($currentAuctions as $listing)
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition duration-200">
                                    <!-- Vehicle Thumbnail -->
                                    <div class="h-48 bg-gray-200 overflow-hidden">
                                        @if($listing->images->first())
                                            <img src="{{ asset('storage/' . $listing->images->first()->image_path) }}" 
                                                 alt="{{ $listing->make }} {{ $listing->model }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-4">
                                        <!-- Item Title -->
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                        </h3>

                                        <!-- ITEM NUMBER -->
                                        <p class="text-sm text-gray-600 mb-3">
                                            <span class="font-medium">ITEM NUMBER:</span> {{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                                        </p>

                                        <!-- Current Highest Bid -->
                                        <p class="text-lg font-bold text-blue-600 mb-3">
                                            Current Highest Bid: ${{ number_format($listing->highest_bid ?? $listing->starting_price ?? 0, 2) }}
                                        </p>

                                        @if($listing->pending_invoice)
                                            <!-- WIN PENDING PAYMENT STATE -->
                                            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded mb-3">
                                                <p class="font-semibold text-amber-900 mb-2">PAYMENT REQUIRED</p>
                                                <a href="{{ route('buyer.payment.checkout-single', $listing->pending_invoice->id) }}" 
                                                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                                    COMPLETE PAYMENT
                                                </a>
                                            </div>
                                        @else
                                            <!-- Countdown Timer -->
                                            @php
                                                $endTime = $listing->auction_end_time ?? ($listing->auction_start_time ? \Carbon\Carbon::parse($listing->auction_start_time)->addDays($listing->auction_duration) : null);
                                            @endphp
                                            @if($endTime && $endTime->isFuture())
                                                <div class="mb-3">
                                                    <p class="text-sm text-gray-600 mb-1">Time Remaining:</p>
                                                    <p class="text-lg font-bold text-red-600" id="countdown-{{ $listing->id }}" 
                                                       data-end-time="{{ $endTime->toIso8601String() }}">
                                                        Calculating...
                                                    </p>
                                                </div>
                                            @endif

                                            <!-- BID AGAIN Button -->
                                            <a href="{{ route('auction.show', $listing->id) }}" 
                                               class="block w-full bg-blue-600 text-white text-center px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                                BID AGAIN
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <p class="text-gray-500 text-lg">No current auctions with active bids.</p>
                        </div>
                    @endif
                </div>

                <!-- WON AUCTIONS -->
                <div id="auction-section-won" class="auction-section hidden">
                    @if($wonAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($wonAuctions as $invoice)
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                    <div class="h-48 bg-gray-200 overflow-hidden">
                                        @if($invoice->listing->images->first())
                                            <img src="{{ asset('storage/' . $invoice->listing->images->first()->image_path) }}" 
                                                 alt="{{ $invoice->listing->make }} {{ $invoice->listing->model }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                    </div>

                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $invoice->listing->year }} {{ $invoice->listing->make }} {{ $invoice->listing->model }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <span class="font-medium">ITEM NUMBER:</span> {{ $invoice->listing->item_number ?? 'CM' . str_pad($invoice->listing->id, 6, '0', STR_PAD_LEFT) }}
                                        </p>
                                        <p class="text-lg font-bold text-green-600 mb-2">
                                            Final Winning Price: ${{ number_format($invoice->final_price ?? $invoice->winning_bid_amount, 2) }}
                                        </p>
                                        <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            WON
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <p class="text-gray-500 text-lg">No won auctions yet.</p>
                        </div>
                    @endif
                </div>

                <!-- LOST AUCTIONS -->
                <div id="auction-section-lost" class="auction-section hidden">
                    @if($lostAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($lostAuctions as $listing)
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                    <div class="h-48 bg-gray-200 overflow-hidden">
                                        @if($listing->images->first())
                                            <img src="{{ asset('storage/' . $listing->images->first()->image_path) }}" 
                                                 alt="{{ $listing->make }} {{ $listing->model }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <span class="font-medium">ITEM NUMBER:</span> {{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                                        </p>
                                        <p class="text-lg font-bold text-gray-600 mb-2">
                                            Final Winning Price: ${{ number_format($listing->final_price, 2) }}
                                        </p>
                                        <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            LOST
                                        </span>
                            </div>
                                </div>
                            @endforeach
                            </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <p class="text-gray-500 text-lg">No lost auctions.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SAVED ITEMS TAB -->
            <div id="content-saved" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Saved Items</h2>

                @if($savedItems->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($savedItems as $listing)
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition duration-200">
                                <div class="h-48 bg-gray-200 overflow-hidden">
                                    @if($listing->images->first())
                                        <img src="{{ asset('storage/' . $listing->images->first()->image_path) }}" 
                                             alt="{{ $listing->make }} {{ $listing->model }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-2">
                                        <span class="font-medium">ITEM NUMBER:</span> {{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                                    </p>
                                    <p class="text-lg font-bold text-blue-600 mb-3">
                                        Current Highest Bid: ${{ number_format($listing->highest_bid, 2) }}
                                    </p>

                                    @php
                                        $endTime = $listing->auction_end_time ?? ($listing->auction_start_time ? \Carbon\Carbon::parse($listing->auction_start_time)->addDays($listing->auction_duration) : null);
                                    @endphp
                                    @if($endTime && $endTime->isFuture())
                                        <div class="mb-3">
                                            <p class="text-sm text-gray-600 mb-1">Time Remaining:</p>
                                            <p class="text-lg font-bold text-red-600" id="countdown-saved-{{ $listing->id }}" 
                                               data-end-time="{{ $endTime->toIso8601String() }}">
                                                Calculating...
                                            </p>
                                        </div>
                                    @endif

                                    <div class="flex space-x-2">
                                        <form method="POST" action="{{ route('listing.watchlist', $listing->id) }}" class="flex-1">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition duration-200">
                                                REMOVE FROM SAVED
                                            </button>
                                        </form>
                                        <a href="{{ route('auction.show', $listing->id) }}" 
                                           class="flex-1 bg-blue-600 text-white text-center px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                            PLACE BID
                                        </a>
                            </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 text-lg">No saved items yet.</p>
                    </div>
                @endif
                        </div>

            <!-- NOTIFICATIONS TAB -->
            <div id="content-notifications" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Notifications</h2>

                @if($notifications->count() > 0)
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-gray-900 font-medium">{{ $notification->data['message'] ?? ($notification->data['title'] ?? 'Notification') }}</p>
                                        <p class="text-sm text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(!$notification->read_at)
                                        <span class="ml-4 w-2 h-2 bg-blue-600 rounded-full"></span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 text-lg">No notifications at this time.</p>
                    </div>
                @endif
            </div>

            <!-- MESSAGING CENTER TAB -->
            <div id="content-messaging" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Messaging Center</h2>
                <p class="text-gray-600 mb-4">Post-payment messaging threads for pickup coordination.</p>

                @if($messagingThreads->count() > 0)
                    <div class="space-y-4">
                        @foreach($messagingThreads as $thread)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                                <div class="flex items-center space-x-4">
                                    <div class="h-20 w-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($thread->listing->images->first())
                                            <img src="{{ asset('storage/' . $thread->listing->images->first()->image_path) }}" 
                                                 alt="{{ $thread->listing->make }} {{ $thread->listing->model }}" 
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $thread->listing->year }} {{ $thread->listing->make }} {{ $thread->listing->model }}
                                        </h3>
                                        <p class="text-sm text-gray-600">Seller: {{ $thread->seller->name }}</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('post-auction.thread', $thread->invoice->id) }}" 
                                           class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                            View Thread
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 text-lg">No messaging threads available. Messaging Center unlocks after payment is completed.</p>
                    </div>
                @endif
            </div>

            <!-- CUSTOMER SUPPORT TAB -->
            <div id="content-support" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Customer Support</h2>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Submit Support Ticket</h3>
                    <form method="POST" action="{{ route('buyer.support.submit') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Title</label>
                            <input type="text" 
                                   name="title" 
                                   required
                                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea name="message" 
                                      rows="6" 
                                      required
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                            Submit Ticket
                        </button>
                    </form>
                </div>

                <!-- Ticket History (if implemented) -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket History</h3>
                    <div class="bg-gray-50 rounded-lg p-6 text-center">
                        <p class="text-gray-500">Ticket history will appear here once tickets are submitted.</p>
                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Password Change Modal -->
<div id="passwordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
            <form method="POST" action="{{ route('buyer-dashboard.change-password') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" 
                           name="current_password" 
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" 
                           name="password" 
                           required
                           minlength="8"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" 
                           name="password_confirmation" 
                           required
                           minlength="8"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="hidePasswordModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

        <script>
// Tab Navigation
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'text-blue-600', 'border-blue-600');
        button.classList.add('text-gray-500', 'border-transparent');
    });

    // Show selected tab content
    const contentElement = document.getElementById('content-' + tabName);
    if (contentElement) {
        contentElement.classList.remove('hidden');
    }
    
    // Add active class to selected tab
    const activeButton = document.getElementById('tab-' + tabName);
    if (activeButton) {
        activeButton.classList.add('active', 'text-blue-600', 'border-blue-600');
        activeButton.classList.remove('text-gray-500', 'border-transparent');
    }
    
    // Update URL without page reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
}

// Show tab on page load based on URL parameter
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab) {
        showTab(tab);
    } else {
        // Default to 'user' tab
        showTab('user');
    }
});

// Auction Section Navigation
function showAuctionSection(section) {
    // Hide all auction sections
    document.querySelectorAll('.auction-section').forEach(section => {
        section.classList.add('hidden');
    });
    
    // Remove active class from all auction tabs
    document.querySelectorAll('.auction-tab-button').forEach(button => {
        button.classList.remove('active', 'text-blue-600', 'border-blue-600');
        button.classList.add('text-gray-500', 'border-transparent');
    });
    
    // Show selected section
    document.getElementById('auction-section-' + section).classList.remove('hidden');
    
    // Add active class to selected tab
    const activeButton = document.getElementById('auction-' + section);
    activeButton.classList.add('active', 'text-blue-600', 'border-blue-600');
    activeButton.classList.remove('text-gray-500', 'border-transparent');
}

// Password Modal
function showPasswordModal() {
    document.getElementById('passwordModal').classList.remove('hidden');
}

function hidePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
                }

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('passwordModal');
    if (event.target == modal) {
        hidePasswordModal();
    }
}

// Countdown Timer
function updateCountdowns() {
    document.querySelectorAll('[id^="countdown-"]').forEach(element => {
        const endTime = new Date(element.getAttribute('data-end-time'));
        const now = new Date();
        const diff = endTime - now;

        if (diff <= 0) {
            element.textContent = 'Auction Ended';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        let timeString = '';
        if (days > 0) {
            timeString = `${days}d ${hours}h ${minutes}m`;
        } else if (hours > 0) {
            timeString = `${hours}h ${minutes}m ${seconds}s`;
        } else {
            timeString = `${minutes}m ${seconds}s`;
        }

        element.textContent = timeString;
    });
}

// Update countdowns every second
setInterval(updateCountdowns, 1000);
updateCountdowns();
</script>

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            const el = document.querySelector('.fixed.top-4');
            if (el) el.remove();
        }, 3000);
    </script>
            @endif

@if($errors->any())
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <script>
        setTimeout(() => {
            const el = document.querySelector('.fixed.top-4');
            if (el) el.remove();
        }, 5000);
        </script>
@endif

@endsection
