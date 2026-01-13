@extends('layouts.dashboard')

@section('title', 'Auctions - Buyer Dashboard')

@section('content')
<div class="bg-gray-50 min-h-screen p-6">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">My Auctions</h1>
        <p class="text-gray-600 mt-2">Track your current, won, and lost auctions</p>
    </div>

    <!-- Sub-tabs for CURRENT, WON, LOST -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="showAuctionSection('current')" 
                        id="auction-current" 
                        class="auction-tab-button active px-6 py-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600 whitespace-nowrap">
                    CURRENT
                </button>
                <button onclick="showAuctionSection('won')" 
                        id="auction-won" 
                        class="auction-tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent whitespace-nowrap">
                    WON
                </button>
                <button onclick="showAuctionSection('lost')" 
                        id="auction-lost" 
                        class="auction-tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent whitespace-nowrap">
                    LOST
                </button>
            </nav>
        </div>
    </div>

    <!-- CURRENT AUCTIONS -->
    <div id="auction-section-current" class="auction-section">
        @if(isset($currentAuctions) && $currentAuctions->count() > 0)
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

                            @php
                                $pendingInvoice = $listing->getPendingInvoiceForUser($user->id);
                            @endphp

                            @if($pendingInvoice)
                                <!-- WIN PENDING PAYMENT STATE -->
                                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded mb-3">
                                    <p class="font-semibold text-amber-900 mb-2">PAYMENT REQUIRED</p>
                                    <a href="{{ route('buyer.payment.checkout-single', $pendingInvoice->id) }}" 
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
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-gray-500 text-lg font-medium mb-2">No current auctions with active bids.</p>
                    <p class="text-gray-400 text-sm">Start bidding on listings to see them here.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- WON AUCTIONS -->
    <div id="auction-section-won" class="auction-section hidden">
        @if(isset($wonAuctions) && $wonAuctions->count() > 0)
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
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <p class="text-gray-500 text-lg font-medium mb-2">No won auctions yet.</p>
                    <p class="text-gray-400 text-sm">Auctions you win and pay for will appear here.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- LOST AUCTIONS -->
    <div id="auction-section-lost" class="auction-section hidden">
        @if(isset($lostAuctions) && $lostAuctions->count() > 0)
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
                                Final Winning Price: ${{ number_format($listing->final_price ?? $listing->getHighestBidAmount() ?? 0, 2) }}
                            </p>
                            <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-semibold">
                                LOST
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-500 text-lg font-medium mb-2">No lost auctions.</p>
                    <p class="text-gray-400 text-sm">Auctions where you were outbid will appear here.</p>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function showAuctionSection(section) {
    // Hide all sections
    document.querySelectorAll('.auction-section').forEach(sec => {
        sec.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.auction-tab-button').forEach(btn => {
        btn.classList.remove('active', 'text-blue-600', 'border-blue-600');
        btn.classList.add('text-gray-500', 'border-transparent');
    });
    
    // Show selected section
    const sectionElement = document.getElementById('auction-section-' + section);
    if (sectionElement) {
        sectionElement.classList.remove('hidden');
    }
    
    // Add active class to selected tab
    const activeButton = document.getElementById('auction-' + section);
    if (activeButton) {
        activeButton.classList.add('active', 'text-blue-600', 'border-blue-600');
        activeButton.classList.remove('text-gray-500', 'border-transparent');
    }
    
    // Update URL without page reload
    const url = new URL(window.location);
    url.searchParams.set('section', section);
    window.history.pushState({}, '', url);
}

// Show section on page load based on URL parameter
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const section = urlParams.get('section') || 'current';
    showAuctionSection(section);
    
    // Update countdowns
    updateCountdowns();
});

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
</script>
@endsection
