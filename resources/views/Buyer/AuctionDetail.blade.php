@extends('layouts.Buyer')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Breadcrumb --}}
    <nav class="text-sm mb-6 text-gray-500">
        <a href="{{ url('/') }}" class="hover:underline">Home</a> /
        <a href="{{ route('marketplace.index') }}" class="hover:underline">Auctions</a> /
        <span class="text-gray-700">{{ $auctionListing->year }} {{ $auctionListing->make }} {{ $auctionListing->model }}</span>
    </nav>

    {{-- Title --}}
    <h1 class="text-2xl font-bold mb-4">
        {{ $auctionListing->year }} {{ $auctionListing->make }} {{ $auctionListing->model }}
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Left: Main Image --}}
        <div class="md:col-span-1">
            <img id="mainImage" src="{{ $mainImage }}"
                 alt="{{ $auctionListing->make }} {{ $auctionListing->model }}"
                 class="w-full rounded-lg shadow h-25">

            {{-- Gallery --}}
            @if($images->count() > 1)
                <div class="flex mt-3 space-x-2 overflow-x-auto">
                    @foreach($images as $img)
                        <img src="{{ $img }}"
                             class="w-20 h-16 object-cover rounded border hover:ring-2 hover:ring-primary-DEFAULT cursor-pointer"
                             onclick="document.querySelector('#mainImage').src='{{ $img }}'">
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Middle: Auction Details --}}
        <div class="md:col-span-2 bg-white rounded-lg shadow p-5">
            <h2 class="text-xl font-semibold mb-3">Auction Details</h2>

            <p class="mb-1"><strong>Status:</strong>
                @if($isExpired)
                    <span class="text-red-600">Expired</span>
                @else
                    <span class="text-green-600">Active</span>
                @endif
            </p>

<p class="mb-1"><strong>Ends:</strong>
    {{ $endDate ? $endDate->format('M d, Y h:i A') : 'N/A' }}
</p>
            <p class="mb-1"><strong>Current Bid:</strong>
                <span class="text-primary-DEFAULT font-bold">${{ number_format($currentBid, 2) }}</span>
            </p>
            <p class="mb-4"><strong>Total Bids:</strong> {{ $auctionListing->bids->count() }}</p>

            {{-- Place a Bid --}}
            @if(!$isExpired && Auth::check() && Auth::user()->role === 'buyer')
                <form action="" method="POST" class="flex items-center space-x-3">
                    @csrf
                    <input type="number" name="bid_amount"
                           placeholder="Enter your bid"
                           class="border rounded p-2 w-1/2"
                           min="{{ $currentBid + 1 }}" required>
                    <button type="submit"
                            class="bg-primary-DEFAULT hover:bg-primary-dark text-white px-4 py-2 rounded">
                        Bid Now
                    </button>
                </form>
            @elseif(!Auth::check())
                <p class="text-sm text-gray-500 mt-3">Please <a href="{{ route('login') }}" class="text-blue-600 hover:underline">login</a> to place a bid.</p>
            @endif
        </div>
    </div>

    {{-- Bid History --}}
    <div class="mt-8 bg-white rounded-lg shadow p-5">
        <h2 class="text-xl font-semibold mb-3">Bid History</h2>
        <table class="w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Bidder</th>
                    <th class="px-4 py-2 text-left">Amount</th>
                    <th class="px-4 py-2 text-left">Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auctionListing->bids as $bid)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $bid->user->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-2">${{ number_format($bid->amount, 2) }}</td>
                        <td class="px-4 py-2">{{ $bid->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-gray-500">No bids yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
