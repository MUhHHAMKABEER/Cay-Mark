@extends('layouts.admin')

@section('title', 'Listing Review - Admin')

@section('content')
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
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
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
@endsection
