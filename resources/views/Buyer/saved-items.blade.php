@extends('layouts.dashboard')

@section('title', 'Saved Items - Buyer Dashboard')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Saved Items</h1>
        <p class="text-gray-600 mt-2">Your bookmarked listings</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
        {{ session('success') }}
    </div>
    @endif

    <!-- Saved Items Grid -->
    <div class="bg-white rounded-lg shadow p-6">
        @if($savedItems->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($savedItems as $listing)
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
                                Current Bid: ${{ number_format($listing->highest_bid ?? 0, 2) }}
                            </p>

                            <!-- Countdown Timer (if auction is active) -->
                            @if($listing->status === 'active' && $listing->auction_end_time)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-1">Time Remaining:</p>
                                    <p class="text-lg font-bold text-red-600" 
                                       id="countdown-{{ $listing->id }}"
                                       data-end-time="{{ $listing->auction_end_time }}">
                                        Calculating...
                                    </p>
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex flex-col space-y-2">
                                <form method="POST" action="{{ route('listing.watchlist', $listing->id) }}" class="w-full">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition duration-200">
                                        REMOVE FROM SAVED
                                    </button>
                                </form>
                                
                                <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}" 
                                   class="block w-full bg-blue-600 text-white text-center px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                    PLACE BID
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <p class="text-gray-500 text-lg">No saved items yet.</p>
                <p class="text-gray-400 text-sm mt-2">Browse listings and save items you're interested in.</p>
            </div>
        @endif
    </div>
</div>

@if($savedItems->count() > 0)
<script>
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
@endif
@endsection
