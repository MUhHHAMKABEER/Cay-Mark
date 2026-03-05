@extends('layouts.Seller')

@section('content')
<div class="min-h-screen overflow-y-auto">

<div class="container mx-auto px-4 py-6">
    {{-- Breadcrumb --}}
    <nav class="text-sm mb-6 text-gray-600">
        <ol class="list-reset flex">
            <li><a href="{{ route('seller.dashboard') }}" class="text-primary-DEFAULT hover:underline transition-colors">Dashboard</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-gray-800 font-semibold">My Listings</li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800">My Listings</h1>
        <a href="{{ route('seller.listings.create') }}"
           class="mt-4 md:mt-0 px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg shadow-lg hover:bg-blue-700 hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300">
            + Add New Listing
        </a>
    </div>

    {{-- Stats Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-50 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Listings</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $counts['total'] ?? $listings->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-50 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $counts['active'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-50 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Sold</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $counts['sold'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filters --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-8">
        <form method="GET" action="{{ route('seller.listings.index') }}" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by make, model, year, or item number..."
                       class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select name="status" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="listing_method" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Types</option>
                <option value="auction" {{ request('listing_method') == 'auction' ? 'selected' : '' }}>Auction</option>
                <option value="buy_now" {{ request('listing_method') == 'buy_now' ? 'selected' : '' }}>Buy Now</option>
            </select>
            <button type="submit" class="w-full md:w-auto px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
                Search
            </button>
        </form>
    </div>

    {{-- Listings Grid --}}
    @if($listings->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        @foreach ($listings as $listing)
            @php
                $mainImage = $listing->images->first();
                $imgSrc = $mainImage ? asset('storage/' . $mainImage->image_path) : asset('images/placeholder-product.png');
                $title = trim(($listing->year ? $listing->year . ' ' : '') . ($listing->make ?? '') . ' ' . ($listing->model ?? ''));
                if (empty($title)) $title = 'Listing #' . ($listing->item_number ?? $listing->id);
                $statusConfig = [
                    'pending' => ['color' => 'yellow', 'text' => 'Pending'],
                    'active' => ['color' => 'green', 'text' => 'Active'],
                    'sold' => ['color' => 'blue', 'text' => 'Sold'],
                    'rejected' => ['color' => 'red', 'text' => 'Rejected'],
                ];
                $status = $statusConfig[$listing->status] ?? ['color' => 'gray', 'text' => ucfirst($listing->status ?? 'N/A')];
                $itemNumber = $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT);
                $price = $listing->listing_method === 'auction' ? ($listing->starting_price ?? $listing->price) : $listing->price ?? $listing->buy_now_price;
            @endphp

            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col h-full">
                <div class="relative">
                    <img src="{{ $imgSrc }}"
                         alt="{{ $title }}"
                         class="w-full h-52 object-cover">
                    <span class="absolute top-3 right-3 bg-{{ $status['color'] }}-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                        {{ $status['text'] }}
                    </span>
                    <span class="absolute top-3 left-3 bg-black/60 text-white text-xs font-medium px-2 py-1 rounded">
                        {{ $listing->listing_method === 'auction' ? 'Auction' : 'Buy Now' }}
                    </span>
                </div>

                <div class="p-5 flex-grow">
                    <h2 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">
                        {{ $title }}
                    </h2>
                    <p class="text-sm text-gray-600 mb-2">
                        <span class="font-medium">Item #:</span> {{ $itemNumber }}
                    </p>
                    <p class="text-sm text-gray-500 mb-3">
                        Added: {{ $listing->created_at->format('M d, Y') }}
                    </p>
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        @if($listing->listing_method === 'auction')
                            <div class="text-sm font-medium text-gray-600">Starting price</div>
                            <div class="text-lg font-bold text-blue-600">${{ number_format($price, 2) }}</div>
                        @else
                            <div class="text-sm font-medium text-gray-600">Price</div>
                            <div class="text-lg font-bold text-gray-800">${{ number_format($price ?? 0, 2) }}</div>
                        @endif
                    </div>
                </div>

                <div class="px-5 pb-5 mt-auto">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('seller.listings.show', $listing) }}" class="flex-1 px-4 py-2.5 bg-blue-600 text-white text-center font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            Preview & Manage
                        </a>
                        @if($listing->listing_method === 'auction' && $listing->slug)
                            <a href="{{ route('auction.show', $listing) }}" target="_blank" class="px-4 py-2.5 border border-gray-300 text-gray-700 text-center font-medium rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                View Public Page
                            </a>
                        @else
                            <a href="{{ route('listing.show', $listing) }}" target="_blank" class="px-4 py-2.5 border border-gray-300 text-gray-700 text-center font-medium rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                View Public Page
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $listings->withQueryString()->links() }}
    </div>

    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
        <h3 class="mt-5 text-xl font-medium text-gray-900">No listings found</h3>
        <p class="mt-2 text-gray-500">You haven't submitted any vehicle listings yet, or no listings match your filters.</p>
        <div class="mt-6">
            <a href="{{ route('seller.listings.create') }}" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Your First Listing
            </a>
        </div>
    </div>
    @endif
</div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
