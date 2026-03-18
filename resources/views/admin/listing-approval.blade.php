@extends('layouts.admin')

@section('title', 'Listing Review - Admin')

@section('content')
@php
    $resolveListingImageUrl = function ($imagePath) {
        $path = trim((string) $imagePath);
        if ($path === '') {
            return asset('images/placeholder-product.png');
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        $normalized = ltrim(str_replace('\\', '/', $path), '/');
        if (str_starts_with($normalized, 'storage/') || str_starts_with($normalized, 'uploads/')) {
            return asset($normalized);
        }
        return asset('uploads/listings/' . $normalized);
    };
@endphp

<style>
    .listing-review-thumb {
        position: relative;
        width: 68px;
        height: 52px;
        flex-shrink: 0;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #dbe4ee;
        background: #f8fafc;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
    }
    .listing-review-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .listing-review-thumb-button {
        display: block;
        width: 100%;
        height: 100%;
        padding: 0;
        border: 0;
        background: transparent;
        cursor: pointer;
    }
    .listing-review-thumb-badge {
        position: absolute;
        right: 6px;
        bottom: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 9999px;
        background: rgba(15, 23, 42, 0.72);
        color: #fff;
        font-size: 11px;
        pointer-events: none;
    }
    .listing-image-preview-modal {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(2px);
        z-index: 60;
    }
    .listing-image-preview-modal.is-visible {
        display: flex;
    }
    .listing-image-preview-card {
        width: min(720px, 100%);
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.25);
    }
    .listing-image-preview-frame {
        background: #0f172a;
        max-height: 70vh;
    }
    .listing-image-preview-frame img {
        width: 100%;
        max-height: 70vh;
        object-fit: contain;
        display: block;
    }
</style>

<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Listing Review</h1>
        <p class="text-gray-600 mt-2">Review and approve pending listings</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $pendingListings->total() ?? 0 }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pending</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ \App\Models\Listing::where('status', 'pending')->count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-list text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <form method="GET" action="{{ route('admin.listing-review') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[250px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by item number, make, model, or VIN..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>
            @if(request('search'))
            <div>
                <a href="{{ route('admin.listing-review') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Clear
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Pending Listings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Pending Listings for Approval</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Listing</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendingListings as $listing)
                    @php
                        $mainImage = $listing->images->first();
                        $imageUrl = $mainImage ? $resolveListingImageUrl($mainImage->image_path) : asset('images/placeholder-product.png');
                        $vehicleName = trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? ''));
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($listing->images && $listing->images->count() > 0)
                                <div class="listing-review-thumb mr-3">
                                    <button type="button"
                                        class="listing-review-thumb-button js-listing-image-trigger"
                                        data-image="{{ $imageUrl }}"
                                        data-title="{{ $vehicleName !== '' ? $vehicleName : 'Pending Listing' }}"
                                        data-listing="#{{ $listing->id }}"
                                        data-seller="{{ $listing->seller->name ?? 'N/A' }}">
                                        <img src="{{ $imageUrl }}"
                                            alt="{{ $listing->make ?? '' }} {{ $listing->model ?? '' }}">
                                        <span class="listing-review-thumb-badge">
                                            <i class="fas fa-search-plus"></i>
                                        </span>
                                    </button>
                                </div>
                                @else
                                <div class="h-12 w-16 bg-gray-200 rounded mr-3 flex items-center justify-center">
                                    <i class="fas fa-car text-gray-400"></i>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">#{{ $listing->id }}</div>
                                    <div class="text-xs text-gray-500">{{ $listing->subcategory ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $listing->seller->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $listing->seller->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                @if($listing->vin)
                                VIN: {{ $listing->vin }}
                                @else
                                No VIN
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $listing->listing_method === 'auction' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($listing->listing_method ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($listing->listing_method === 'auction')
                                <div class="text-sm font-medium text-gray-900">Starting: ${{ number_format($listing->starting_price ?? 0, 2) }}</div>
                                @if($listing->reserve_price)
                                <div class="text-xs text-gray-500">Reserve: ${{ number_format($listing->reserve_price, 2) }}</div>
                                @endif
                            @else
                                <div class="text-sm font-medium text-gray-900">${{ number_format($listing->buy_now_price ?? 0, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $listing->created_at->format('M j, Y') }}<br>
                            <span class="text-xs">{{ $listing->created_at->format('g:i A') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.listings.approval-detail', $listing->id) }}" 
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition">
                                    <i class="fas fa-eye mr-1"></i>Review
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-4xl text-green-300"></i>
                            </div>
                            <p class="text-lg font-medium mb-2">No pending listings</p>
                            <p class="text-sm text-gray-400">All listings have been reviewed. New listings will appear here when sellers submit them.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($pendingListings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pendingListings->links() }}
        </div>
        @endif
    </div>
</div>

<div id="listingImagePreviewModal" class="listing-image-preview-modal" aria-hidden="true">
    <div id="listingImagePreviewCard" class="listing-image-preview-card">
        <div class="listing-image-preview-frame">
            <img id="listingImagePreviewModalImg" src="" alt="Listing preview">
        </div>
        <div class="px-5 py-4 border-t border-gray-100">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 id="listingImagePreviewModalTitle" class="text-lg font-semibold text-gray-900">Listing Preview</h3>
                    <p id="listingImagePreviewModalMeta" class="text-sm text-gray-500 mt-1"></p>
                </div>
                <div class="text-xs text-gray-400 whitespace-nowrap">Click outside to close</div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('listingImagePreviewModal');
        var modalCard = document.getElementById('listingImagePreviewCard');
        var modalImg = document.getElementById('listingImagePreviewModalImg');
        var modalTitle = document.getElementById('listingImagePreviewModalTitle');
        var modalMeta = document.getElementById('listingImagePreviewModalMeta');

        if (!modal || !modalCard || !modalImg || !modalTitle || !modalMeta) return;

        function showModal(trigger) {
            modalImg.src = trigger.dataset.image || '';
            modalTitle.textContent = trigger.dataset.title || 'Listing Preview';
            modalMeta.textContent = (trigger.dataset.listing || '') + '  Seller: ' + (trigger.dataset.seller || 'N/A');
            modal.classList.add('is-visible');
            modal.setAttribute('aria-hidden', 'false');
        }

        function hideModal() {
            modal.classList.remove('is-visible');
            modal.setAttribute('aria-hidden', 'true');
            modalImg.src = '';
        }

        document.querySelectorAll('.js-listing-image-trigger').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                showModal(trigger);
            });
        });

        modal.addEventListener('click', function (event) {
            if (!modalCard.contains(event.target)) {
                hideModal();
            }
        });
    });
</script>
@endsection
