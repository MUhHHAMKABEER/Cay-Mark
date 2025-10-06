@extends('layouts.Buyer')

@section('content')
<h1 class="text-3xl font-bold mb-6">My Watchlist</h1>

@if($watchlistItems->isEmpty())
    <p class="text-gray-500">You haven’t added any listings to your watchlist yet.</p>
@else
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($watchlistItems as $item)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <img src="{{ $item->images->first()?->url ?? '/placeholder.png' }}" alt="Listing Image" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-lg font-semibold">{{ $item->make }} {{ $item->model }}</h2>
                    <p class="text-gray-600">${{ number_format($item->price, 2) }}</p>
                    <div class="mt-2 flex gap-2">
                        <form method="POST" action="{{ route('listing.watchlist', $item->id) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1 text-sm text-red-500 border border-red-300 rounded hover:bg-red-50 transition">
                                Remove
                            </button>
                        </form>
                        <a href="{{ route('listing.show', $item->id) }}" class="px-3 py-1 text-sm text-blue-500 border border-blue-300 rounded hover:bg-blue-50 transition">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
