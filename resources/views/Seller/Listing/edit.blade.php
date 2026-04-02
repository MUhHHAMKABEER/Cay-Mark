@extends('layouts.Seller')

@section('title', 'Edit Listing')

@section('content')
<div class="min-h-screen overflow-y-auto">
    <div class="container mx-auto px-4 py-6">
        <nav class="text-sm mb-6 text-gray-600">
            <ol class="list-reset flex flex-wrap items-center gap-x-2">
                <li><a href="{{ route('seller.dashboard') }}" class="text-blue-600 hover:underline">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('seller.auctions') }}" class="text-blue-600 hover:underline">My Listings</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('seller.listings.show', $listing) }}" class="text-blue-600 hover:underline">Preview</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-800 font-semibold">Edit</li>
            </ol>
        </nav>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 max-w-2xl mx-auto text-center">
            <span class="material-icons text-gray-300 text-6xl">edit_note</span>
            <h1 class="text-2xl font-bold text-gray-900 mt-4">Edit Listing</h1>
            <p class="text-gray-600 mt-2">
                {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
            </p>
            <p class="text-gray-500 text-sm mt-4">
                Full edit form (pre-fill from existing listing) can be added here. For now you can update your listing by creating a new one or contact support.
            </p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('seller.listings.show', $listing) }}" class="px-5 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                    Back to Preview
                </a>
                <a href="{{ route('seller.auctions') }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">
                    My Listings
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
