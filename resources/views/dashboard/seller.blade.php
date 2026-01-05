@extends('layouts.Seller')

@section('title', 'Seller Dashboard - CayMark')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="bg-white rounded-lg shadow mb-6 p-6">
            <h1 class="text-3xl font-bold text-gray-900">Seller Dashboard</h1>
            <p class="text-gray-600 mt-2">Manage your listings, auctions, and sales</p>
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
                    <button onclick="showTab('submission')" 
                            id="tab-submission" 
                            class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent whitespace-nowrap">
                        SUBMISSION
                    </button>
                    <button onclick="showTab('auctions')" 
                            id="tab-auctions" 
                            class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent whitespace-nowrap">
                        AUCTIONS
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

                <!-- Full Name / Business Name -->
                @if($user->business_license_path)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Name</label>
                        <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                            <span class="text-gray-900">{{ $user->name }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Cannot be changed</p>
                    </div>
                @else
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                            <span class="text-gray-900">{{ $user->name }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Cannot be changed</p>
                    </div>
                @endif

                <!-- Email Address -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registered Email Address</label>
                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                        <span class="text-gray-900">{{ $user->email }}</span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">Visible only; cannot be edited</p>
                </div>

                <!-- Account Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                        <span class="text-gray-900 font-semibold">
                            {{ $user->business_license_path ? 'Business Seller' : 'Individual Seller' }}
                        </span>
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

                <!-- Verification Documents -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Verification Documents</label>
                    @if($documents->count() > 0)
                        <div class="space-y-4">
                            @foreach($documents as $document)
                                <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                @if($document->doc_type === 'business_license')
                                                    Business License
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $document->doc_type)) }}
                                                @endif
                                            </p>
                                            @if($user->relationship_to_business && $document->doc_type === 'business_license')
                                                <p class="text-sm text-gray-600 mt-1">
                                                    Relationship: {{ ucfirst(str_replace('_', ' ', $user->relationship_to_business)) }}
                                                </p>
                                            @endif
                                        </div>
                                        @if($document->path)
                                            <a href="{{ asset('storage/' . $document->path) }}" 
                                               target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                View
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-500 mt-4">Documents are view-only.</p>
                    @else
                        <div class="bg-gray-50 border border-gray-300 rounded-lg p-6 text-center">
                            <p class="text-gray-500">No documents uploaded yet.</p>
                        </div>
                    @endif
                </div>

                <!-- Payout Settings -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Payout Settings</label>
                    @if($payoutMethod)
                        <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 mb-4">
                            <div class="space-y-2">
                                <p><span class="font-medium">Bank Name:</span> {{ $payoutMethod->bank_name }}</p>
                                <p><span class="font-medium">Account Holder:</span> {{ $payoutMethod->account_holder_name }}</p>
                                <p><span class="font-medium">Account Number:</span> ****{{ substr($payoutMethod->account_number, -4) }}</p>
                                @if($payoutMethod->routing_number)
                                    <p><span class="font-medium">Routing Number:</span> ****{{ substr($payoutMethod->routing_number, -4) }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                    <button onclick="showPayoutModal()" 
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                        {{ $payoutMethod ? 'Update Payout Settings' : 'Add Payout Settings' }}
                    </button>
                    <p class="text-sm text-gray-500 mt-2">Payout details must be entered before any earnings can be released.</p>
                </div>
            </div>

            <!-- SUBMISSION TAB -->
            <div id="content-submission" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Submit New Listing</h2>
                
                @if(!$payoutMethod)
                    <div class="bg-amber-50 border-l-4 border-amber-400 p-6 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold text-amber-900 mb-2">Payout Settings Required</h3>
                        <p class="text-amber-800 mb-4">You must add payout settings before submitting a listing.</p>
                        <a href="#content-user" onclick="showTab('user')" class="inline-block bg-amber-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-amber-700 transition duration-200">
                            Add Payout Settings
                        </a>
                    </div>
                @else
                    <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Ready to List Your Vehicle?</h3>
                        <p class="text-gray-600 mb-6">Submit a new listing through our three-step submission process.</p>
                        <a href="{{ route('listings.create') }}" 
                           class="inline-block bg-blue-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 text-lg">
                            SUBMIT NEW LISTING
                        </a>
                        <p class="text-sm text-gray-500 mt-4">Three-step process: Vehicle Information → Photo Upload → Auction Settings & Payment</p>
                    </div>
                @endif
            </div>

            <!-- AUCTIONS TAB -->
            <div id="content-auctions" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">My Auctions</h2>

                <!-- Summary Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-blue-900 mb-1">CURRENT SUMMARY</h3>
                        <p class="text-2xl font-bold text-blue-600">{{ $auctionSummary['current_count'] }}</p>
                        <p class="text-sm text-blue-700">Active Auctions</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-green-900 mb-1">PAST SUMMARY</h3>
                        <p class="text-2xl font-bold text-green-600">{{ $auctionSummary['total_items_sold'] }}</p>
                        <p class="text-sm text-green-700">Total Items Sold</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-green-900 mb-1">PAST SUMMARY</h3>
                        <p class="text-2xl font-bold text-green-600">${{ number_format($auctionSummary['total_sales_revenue'], 2) }}</p>
                        <p class="text-sm text-green-700">Total Sales Revenue</p>
                    </div>
                </div>

                <!-- Sub-tabs for CURRENT, PAST, REJECTED -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="flex -mb-px">
                        <button onclick="showAuctionSection('current')" 
                                id="auction-current" 
                                class="auction-tab-button active px-6 py-3 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                            CURRENT
                        </button>
                        <button onclick="showAuctionSection('past')" 
                                id="auction-past" 
                                class="auction-tab-button px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                            PAST
                        </button>
                        <button onclick="showAuctionSection('rejected')" 
                                id="auction-rejected" 
                                class="auction-tab-button px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent">
                            REJECTED
                        </button>
                    </nav>
                </div>

                <!-- CURRENT AUCTIONS -->
                <div id="auction-section-current" class="auction-section">
                    @if($currentAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($currentAuctions as $listing)
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

                                        @if($listing->awaiting_pin)
                                            <!-- Awaiting PIN Confirmation -->
                                            <p class="text-lg font-bold text-green-600 mb-3">
                                                Final Sale Price: ${{ number_format($listing->current_bid, 2) }}
                                            </p>
                                            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded mb-3">
                                                <p class="font-semibold text-amber-900 mb-2">Awaiting Pickup Confirmation</p>
                                                <form method="POST" action="{{ route('seller-dashboard.confirm-pickup', $listing->id) }}">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">ENTER PICKUP PIN</label>
                                                        <input type="text" 
                                                               name="pickup_pin" 
                                                               maxlength="4"
                                                               pattern="[0-9]{4}"
                                                               required
                                                               class="w-full border border-gray-300 rounded-lg px-4 py-2 text-center text-2xl font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                               placeholder="____">
                                                    </div>
                                                    <button type="submit" 
                                                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                                        CONFIRM PICKUP
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <!-- Active Auction -->
                                            <p class="text-lg font-bold text-blue-600 mb-3">
                                                Current Bid: ${{ number_format($listing->current_bid, 2) }}
                                            </p>
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
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <p class="text-gray-500 text-lg">No current auctions.</p>
                        </div>
                    @endif
                </div>

                <!-- PAST AUCTIONS -->
                <div id="auction-section-past" class="auction-section hidden">
                    @if($pastAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($pastAuctions as $listing)
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
                                        <p class="text-lg font-bold text-green-600 mb-2">
                                            Final Sale Price: ${{ number_format($listing->final_price, 2) }}
                                        </p>
                                        <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            ENDED
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <p class="text-gray-500 text-lg">No past auctions yet.</p>
                        </div>
                    @endif
                </div>

                <!-- REJECTED LISTINGS -->
                <div id="auction-section-rejected" class="auction-section hidden">
                    @if($rejectedListings->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($rejectedListings as $listing)
                                <div class="bg-white border border-red-200 rounded-lg overflow-hidden shadow-sm">
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
                                        
                                        @if($listing->rejection_reason || $listing->rejection_notes)
                                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                                                @if($listing->rejection_reason)
                                                    <p class="text-sm text-red-800 mb-1">
                                                        <span class="font-medium">Rejection Reason:</span> {{ $listing->rejection_reason }}
                                                    </p>
                                                @endif
                                                @if($listing->rejection_notes)
                                                    <p class="text-sm text-red-800">
                                                        <span class="font-medium">Rejection Notes:</span> {{ $listing->rejection_notes }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif

                                        @if($listing->can_edit)
                                            <div class="mb-3">
                                                <p class="text-sm text-amber-600 font-medium mb-1">
                                                    Time Remaining to Edit: <span id="rejection-timer-{{ $listing->id }}" 
                                                                                  data-deadline="{{ $listing->edit_deadline->toIso8601String() }}">
                                                        {{ $listing->time_remaining }}
                                                    </span>
                                                </p>
                                            </div>
                                            <a href="{{ route('listings.create') }}?edit={{ $listing->id }}" 
                                               class="block w-full bg-blue-600 text-white text-center px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                                EDIT LISTING
                                            </a>
                                        @else
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3">
                                                <p class="text-sm text-gray-600">Editing is permanently locked. Submit a new listing to relist.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <p class="text-gray-500 text-lg">No rejected listings.</p>
                        </div>
                    @endif
                </div>
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
                <p class="text-gray-600 mb-4">Post-payment pickup coordination threads with buyers.</p>

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
                                        <p class="text-sm text-gray-600">Buyer: {{ $thread->buyer->name }}</p>
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
                    <form method="POST" action="{{ route('seller.support.submit') }}">
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

                <!-- Ticket History -->
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
            <form method="POST" action="{{ route('seller-dashboard.change-password') }}">
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

<!-- Payout Settings Modal -->
<div id="payoutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white m-4">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payout Settings</h3>
            <form method="POST" action="{{ route('seller-dashboard.update-payout') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name *</label>
                        <input type="text" 
                               name="bank_name" 
                               value="{{ $payoutMethod->bank_name ?? '' }}"
                               required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Holder Name *</label>
                        <input type="text" 
                               name="account_holder_name" 
                               value="{{ $payoutMethod->account_holder_name ?? '' }}"
                               required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Number *</label>
                        <input type="text" 
                               name="account_number" 
                               value="{{ $payoutMethod ? '****' . substr($payoutMethod->account_number, -4) : '' }}"
                               required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Routing / Transfer Number</label>
                        <input type="text" 
                               name="routing_number" 
                               value="{{ $payoutMethod && $payoutMethod->routing_number ? '****' . substr($payoutMethod->routing_number, -4) : '' }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SWIFT Number</label>
                        <input type="text" 
                               name="swift_number" 
                               value="{{ $payoutMethod && $payoutMethod->swift_number ? '****' . substr($payoutMethod->swift_number, -4) : '' }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Country / Payout Region *</label>
                        <input type="text" 
                               name="country" 
                               value="Bahamas"
                               required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="hidePayoutModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        Save Payout Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tab Navigation
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'text-blue-600', 'border-blue-600');
        button.classList.add('text-gray-500', 'border-transparent');
    });
    
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'text-blue-600', 'border-blue-600');
    activeButton.classList.remove('text-gray-500', 'border-transparent');
}

// Auction Section Navigation
function showAuctionSection(section) {
    document.querySelectorAll('.auction-section').forEach(section => {
        section.classList.add('hidden');
    });
    
    document.querySelectorAll('.auction-tab-button').forEach(button => {
        button.classList.remove('active', 'text-blue-600', 'border-blue-600');
        button.classList.add('text-gray-500', 'border-transparent');
    });
    
    document.getElementById('auction-section-' + section).classList.remove('hidden');
    
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

// Payout Modal
function showPayoutModal() {
    document.getElementById('payoutModal').classList.remove('hidden');
}

function hidePayoutModal() {
    document.getElementById('payoutModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const passwordModal = document.getElementById('passwordModal');
    const payoutModal = document.getElementById('payoutModal');
    if (event.target == passwordModal) {
        hidePasswordModal();
    }
    if (event.target == payoutModal) {
        hidePayoutModal();
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

// Rejection Timer
function updateRejectionTimers() {
    document.querySelectorAll('[id^="rejection-timer-"]').forEach(element => {
        const deadline = new Date(element.getAttribute('data-deadline'));
        const now = new Date();
        const diff = deadline - now;

        if (diff <= 0) {
            element.textContent = 'Time Expired';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

        let timeString = '';
        if (days > 0) {
            timeString = `${days} days, ${hours} hours`;
        } else if (hours > 0) {
            timeString = `${hours} hours, ${minutes} minutes`;
        } else {
            timeString = `${minutes} minutes`;
        }

        element.textContent = timeString;
    });
}

// Update timers every second
setInterval(() => {
    updateCountdowns();
    updateRejectionTimers();
}, 1000);
updateCountdowns();
updateRejectionTimers();
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
