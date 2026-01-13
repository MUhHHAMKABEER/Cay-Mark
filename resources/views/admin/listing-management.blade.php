@extends('layouts.admin')

@section('title', 'Active Listings Management - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Active Listings Management</h1>
        <p class="text-gray-600 mt-2">Manage all approved and active listings</p>
    </div>

    <!-- Listing Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Active</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $listingStats['total_active'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-list text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">With Bids</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $listingStats['with_bids'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-gavel text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ending Soon</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $listingStats['ending_soon'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Within 24 hours</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <form method="GET" action="{{ route('admin.active-listings') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[250px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by item number, make, or model..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>
            @if(request('search'))
            <div>
                <a href="{{ route('admin.active-listings') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Clear
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Active Listings</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Bid</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bids</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activeListings as $listing)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $listing->item_number ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">#{{ $listing->id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($listing->images && $listing->images->count() > 0)
                                <img src="{{ $listing->images->first()->image_path ?? 'https://via.placeholder.com/60' }}" 
                                    alt="{{ $listing->make ?? '' }} {{ $listing->model ?? '' }}"
                                    class="h-12 w-16 object-cover rounded mr-3">
                                @else
                                <div class="h-12 w-16 bg-gray-200 rounded mr-3 flex items-center justify-center">
                                    <i class="fas fa-car text-gray-400"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '') }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $listing->subcategory ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $listing->seller->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $listing->seller->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $listing->listing_method === 'auction' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($listing->listing_method ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($listing->listing_method === 'auction')
                                @php
                                    $highestBid = $listing->bids->max('amount') ?? $listing->starting_price ?? 0;
                                @endphp
                                <div class="text-sm font-medium text-gray-900">${{ number_format($highestBid, 2) }}</div>
                                <div class="text-xs text-gray-500">Starting: ${{ number_format($listing->starting_price ?? 0, 2) }}</div>
                            @else
                                <div class="text-sm font-medium text-gray-900">${{ number_format($listing->buy_now_price ?? 0, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($listing->auction_end_time)
                                <div class="text-sm font-medium text-gray-900">{{ $listing->auction_end_time->format('M j, Y') }}</div>
                                <div class="text-xs {{ $listing->auction_end_time->isPast() ? 'text-red-600' : ($listing->auction_end_time->diffInHours(now()) <= 24 ? 'text-orange-600' : 'text-gray-500') }}">
                                    {{ $listing->auction_end_time->format('g:i A') }}
                                    @if($listing->auction_end_time->diffInHours(now()) <= 24 && !$listing->auction_end_time->isPast())
                                        <span class="block">Ending Soon</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-400 text-sm">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $listing->bids->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $listing->bids->count() ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.listings.approval-detail', $listing->id) }}" 
                                    class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($listing->listing_method === 'auction')
                                <a href="#" onclick="event.preventDefault(); showExtendModal({{ $listing->id }});" 
                                    class="text-green-600 hover:text-green-900" title="Extend Auction">
                                    <i class="fas fa-clock"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-list text-4xl mb-3 text-gray-300"></i>
                            <p>No active listings found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($activeListings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $activeListings->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Extend Auction Modal -->
<div id="extendModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form id="extendForm" method="POST">
            @csrf
            @method('POST')
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Extend Auction Time</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Hours</label>
                <input type="number" name="hours" min="1" max="168" value="24" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Enter number of hours to extend</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeExtendModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Extend Auction
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showExtendModal(listingId) {
    const form = document.getElementById('extendForm');
    form.action = "{{ route('admin.listings.extend-auction', ':id') }}".replace(':id', listingId);
    document.getElementById('extendModal').classList.remove('hidden');
}

function closeExtendModal() {
    document.getElementById('extendModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('extendModal');
    if (event.target == modal) {
        closeExtendModal();
    }
}
</script>
@endsection
