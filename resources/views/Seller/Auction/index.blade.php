@extends('layouts.Seller')

@section('content')
<div class="min-h-screen overflow-y-auto">
<div class="container mx-auto px-4 py-6">
    {{-- Breadcrumb --}}
    <nav class="text-sm mb-6 text-gray-600">
        <ol class="list-reset flex">
            <li><a href="" class="text-primary-DEFAULT hover:underline transition-colors">Dashboard</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-gray-800 font-semibold">My Auction Listings</li>
        </ol>
    </nav>

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800">My Auction Listings</h1>
        <a href="#" class="mt-4 md:mt-0 px-5 py-2.5 bg-primary-DEFAULT text-white font-medium rounded-lg hover:bg-primary-dark transition-colors shadow-md hover:shadow-lg">
            + Create New Auction
        </a>
    </div>

    {{-- Stats Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-50 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Listings</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $listings->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-50 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Auctions</p>
                    <p class="text-2xl font-bold text-gray-800">
                        @php
                            $activeCount = 0;
                            foreach ($listings as $listing) {
                                $endDate = \Carbon\Carbon::parse($listing->created_at)->addDays($listing->auction_duration);
                                if (!$endDate->isPast()) $activeCount++;
                            }
                        @endphp
                        {{ $activeCount }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-50 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Bids</p>
                    <p class="text-2xl font-bold text-gray-800">
                        @php
                            $totalBids = 0;
                            foreach ($listings as $listing) {
                                $totalBids += $listing->bids->count();
                            }
                        @endphp
                        {{ $totalBids }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Filters --}}
    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-8">
        <form method="GET" action="" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by make, model, or year..."
                       class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-DEFAULT focus:border-transparent">
            </div>
            <button type="submit"
                    class="w-full md:w-auto px-5 py-2.5 bg-primary-DEFAULT text-white font-medium rounded-lg hover:bg-primary-dark transition-colors flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
                Search
            </button>
        </form>
    </div>

    {{-- Auction Listings Grid --}}
    @if($listings->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
        @foreach ($listings as $listing)
            @php
                // Normalize main image
                $mainImage = optional($listing->images->first())->image_path
                    ? asset('uploads/listings/' . $listing->images->first()->image_path)
                    : asset('images/placeholder.png');

                // Calculate auction end date
                $endDate = \Carbon\Carbon::parse($listing->created_at)->addDays($listing->auction_duration);
                $isExpired = $endDate->isPast();

                // Highest bid
                $highestBid = $listing->bids->max('amount') ?? 0;

                // Time remaining for active auctions
                $timeRemaining = null;
                if (!$isExpired) {
                    $timeRemaining = $endDate->diffForHumans();
                }
            @endphp

            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 flex flex-col h-full">
                {{-- Auction image with status badge --}}
                <div class="relative">
                    <img src="{{ $mainImage }}"
                         alt="{{ $listing->make }} {{ $listing->model }}"
                         class="w-full h-52 object-cover">

                    @if($isExpired)
                    <span class="absolute top-3 right-3 bg-red-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                        Expired
                    </span>
                    @else
                    <span class="absolute top-3 right-3 bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md">
                        Active
                    </span>
                    @endif

                    {{-- Time remaining progress bar --}}
                    @if(!$isExpired)
                    <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-40 text-white p-2 text-xs">
                        <div class="flex justify-between mb-1">
                            <span>Ends: {{ $endDate->format('M d, Y') }}</span>
                            <span>{{ $timeRemaining }}</span>
                        </div>
                        @php
                            $startDate = \Carbon\Carbon::parse($listing->created_at);
                            $totalHours = $startDate->diffInHours($endDate);
                            $elapsedHours = $startDate->diffInHours(now());
                            $percentage = min(100, ($elapsedHours / $totalHours) * 100);
                        @endphp
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="p-5 flex-grow">
                    {{-- Title --}}
                    <h2 class="text-xl font-bold text-gray-800 mb-2 line-clamp-1">
                        {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                    </h2>

                    {{-- Vehicle details --}}
                    <div class="flex items-center text-sm text-gray-600 mb-3">
                        <svg class="w-4 h-4 mr-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1v3m5-3v3m5-3v3M1 7h18M5 11h10M2 3h16a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/>
                        </svg>
                        <span>Listed: {{ $listing->created_at->format('M d, Y') }}</span>
                    </div>

                    {{-- Bid information --}}
                    <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-600">Current Bid</span>
                            <span class="text-lg font-bold text-primary-DEFAULT">${{ number_format($highestBid, 2) }}</span>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $listing->bids->count() }} {{ Str::plural('bid', $listing->bids->count()) }} placed
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="px-5 pb-5 mt-auto">
                    <div class="flex space-x-3">
                        <a href=""
                           class="flex-1 px-4 py-2.5 bg-primary-DEFAULT text-white text-center font-medium rounded-lg hover:bg-primary-dark transition-colors">
                            View Details
                        </a>
                        <a href=""
                           class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    {{-- Empty state --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
        </svg>
        <h3 class="mt-5 text-xl font-medium text-gray-900">No auctions found</h3>
        <p class="mt-2 text-gray-500">You don't have any auction listings yet.</p>
        <div class="mt-6">
            <a href="#" class="px-5 py-2.5 bg-primary-DEFAULT text-white font-medium rounded-lg hover:bg-primary-dark transition-colors inline-flex items-center">
                <svg class="w-4 h-4 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Create your first auction
            </a>
        </div>
    </div>
    @endif

    {{-- Pagination --}}
    @if($listings->hasPages())
    <div class="bg-white px-5 py-3 rounded-xl shadow-sm border border-gray-100">
        {{ $listings->links() }}
    </div>
    @endif
</div>
</div>

<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .pagination {
        display: flex;
        justify-content: center;
        list-style-type: none;
        padding: 0;
    }

    .pagination li {
        margin: 0 4px;
    }

    .pagination li a,
    .pagination li span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        color: #4b5563;
        font-weight: 500;
        transition: all 0.2s;
    }

    .pagination li a:hover {
        background-color: #f3f4f6;
        color: #1f2937;
    }

    .pagination li.active span {
        background-color: #0066ff;
        color: white;
        border-color: #0066ff;
    }

    .pagination li.disabled span {
        color: #9ca3af;
        cursor: not-allowed;
    }
</style>
@endsection
