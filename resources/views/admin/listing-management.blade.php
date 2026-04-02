@extends('layouts.admin')

@section('title', 'Active Auctions Management - Admin')

@section('content')
@php
    $listingImageUrl = function ($path) {
        $path = trim((string) ($path ?? ''));
        if ($path === '' || str_starts_with($path, 'http')) return $path ?: asset('images/placeholder-product.png');
        $p = ltrim(str_replace('\\', '/', $path), '/');
        return str_starts_with($p, 'uploads/') ? asset($p) : asset('uploads/listings/' . $p);
    };
@endphp
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Active Auctions Management</h1>
        <p class="text-gray-600 mt-2">Manage all approved and active auctions</p>
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

    <!-- Search and Filters (JS client-side) -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <form class="js-admin-filter-form flex flex-wrap gap-4" data-admin-filter-target="#listings-tbody">
            <div class="flex-1 min-w-[250px]">
                <input type="text" name="search" placeholder="Search by item number, make, or model..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
            <div>
                <a href="{{ route('admin.active-listings') }}" data-admin-filter-clear class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">Clear</a>
            </div>
        </form>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Active Auctions</h2>
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
                <tbody id="listings-tbody" class="bg-white divide-y divide-gray-200">
                    @forelse($activeListings as $listing)
                    <tr class="hover:bg-gray-50" data-filter-search="{{ strtolower(($listing->item_number ?? '') . ' ' . ($listing->id ?? '') . ' ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '') . ' ' . ($listing->subcategory ?? '') . ' ' . ($listing->seller->name ?? '') . ' ' . ($listing->seller->email ?? '')) }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $listing->item_number ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">#{{ $listing->id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($listing->images && $listing->images->count() > 0)
                                @php $mainImg = $listing->images->first(); $imgUrl = $listingImageUrl($mainImg->image_path); @endphp
                                <button type="button" class="admin-listing-thumb mr-3 js-active-listing-image" data-image="{{ $imgUrl }}" data-title="{{ ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '') }}">
                                    <img src="{{ $imgUrl }}" alt="{{ $listing->make ?? '' }} {{ $listing->model ?? '' }}" class="h-12 w-16 object-cover rounded">
                                </button>
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
                            @php
                                $endTime = $listing->auction_end_time;
                                if (!$endTime && $listing->auction_start_time && $listing->auction_duration) {
                                    $endTime = \Carbon\Carbon::parse($listing->auction_start_time)->addDays($listing->auction_duration);
                                }
                            @endphp
                            @if($endTime)
                                <div class="text-sm font-medium text-gray-900">{{ $endTime->format('M j, Y') }}</div>
                                <div class="text-xs {{ $endTime->isPast() ? 'text-red-600' : ($endTime->diffInHours(now()) <= 24 ? 'text-orange-600' : 'text-gray-500') }}">
                                    {{ $endTime->format('g:i A') }}
                                    @if($endTime->diffInHours(now()) <= 24 && !$endTime->isPast())
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
                                <form method="POST" action="{{ route('admin.listings.delete', $listing->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this listing?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete Listing">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="js-admin-empty-row">
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-list text-4xl mb-3 text-gray-300"></i>
                            <p>No active listings found</p>
                        </td>
                    </tr>
                    @endforelse
                    @if($activeListings->isNotEmpty())
                    <tr class="js-admin-empty-row" style="display: none;">
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500"><p>No matching listings</p></td>
                    </tr>
                    @endif
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

<!-- Image preview modal (click outside to close) -->
<div id="activeListingImageModal" class="admin-img-modal hidden" aria-hidden="true">
    <div class="admin-img-modal-backdrop" data-dismiss="activeListingImageModal"></div>
    <div class="admin-img-modal-card" onclick="event.stopPropagation()">
        <button type="button" class="admin-img-modal-close" onclick="closeActiveListingImageModal()" aria-label="Close"><i class="fas fa-times"></i></button>
        <img id="activeListingModalImg" src="" alt="Listing" class="admin-img-modal-img">
        <p id="activeListingModalTitle" class="admin-img-modal-title text-sm text-gray-600 mt-2"></p>
    </div>
</div>

<style>
.admin-listing-thumb { padding: 0; border: none; background: none; border-radius: 12px; cursor: pointer; overflow: hidden; display: block; }
.admin-listing-thumb:hover { opacity: 0.9; }
.admin-img-modal { position: fixed; inset: 0; z-index: 50; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.admin-img-modal.hidden { display: none !important; }
.admin-img-modal-backdrop { position: absolute; inset: 0; background: rgba(15,23,42,0.5); backdrop-filter: blur(4px); }
.admin-img-modal-card { position: relative; max-width: min(900px,95vw); background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); padding: 1rem; }
.admin-img-modal-close { position: absolute; top: 0.75rem; right: 0.75rem; width: 40px; height: 40px; border: none; border-radius: 10px; background: rgba(15,23,42,0.7); color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 2; }
.admin-img-modal-close:hover { background: rgba(15,23,42,0.9); }
.admin-img-modal-img { display: block; max-width: 100%; max-height: 80vh; object-fit: contain; }
.admin-img-modal-title { margin: 0; padding: 0 0.5rem; }
</style>

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

function openActiveListingImageModal(src, title) {
    document.getElementById('activeListingModalImg').src = src || '';
    document.getElementById('activeListingModalTitle').textContent = title || '';
    document.getElementById('activeListingImageModal').classList.remove('hidden');
    document.getElementById('activeListingImageModal').setAttribute('aria-hidden', 'false');
}
function closeActiveListingImageModal() {
    document.getElementById('activeListingImageModal').classList.add('hidden');
    document.getElementById('activeListingImageModal').setAttribute('aria-hidden', 'true');
    document.getElementById('activeListingModalImg').src = '';
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.js-active-listing-image').forEach(function(btn) {
        btn.addEventListener('click', function() {
            openActiveListingImageModal(btn.dataset.image, btn.dataset.title || '');
        });
    });
    document.querySelectorAll('[data-dismiss="activeListingImageModal"]').forEach(function(el) {
        el.addEventListener('click', closeActiveListingImageModal);
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeActiveListingImageModal();
    });
});

window.onclick = function(event) {
    const modal = document.getElementById('extendModal');
    if (event.target == modal) closeExtendModal();
};
</script>
@endsection
