{{-- resources/views/listing/show.blade.php --}}
@extends('layouts.Buyer')

@section('content')
<div class="container mx-auto px-4 py-6">

    {{-- Breadcrumb --}}
    <nav class="text-sm mb-6 text-gray-500">
        <a href="" class="hover:underline">Home</a> /
        <a href="{{ route('marketplace.index') }}" class="hover:underline">Marketplace</a> /
        <span class="text-gray-700">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</span>
    </nav>

    {{-- Title --}}
    <h1 class="text-2xl font-bold mb-4">
        {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Left: Main Image --}}
        <div class="md:col-span-1">
            @php
                $images = collect($listing->images)->map(fn($img) => asset('uploads/listings/' . $img->image_path));
                $mainImage = $images->first() ?? asset('images/placeholder.png');
            @endphp

            <img id="mainImage"
                 src="{{ $mainImage }}"
                 alt="{{ $listing->make }} {{ $listing->model }}"
                 class="w-full rounded-lg shadow">

            {{-- Gallery if multiple images --}}
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

        {{-- Middle: Vehicle Details --}}
        <div class="md:col-span-2 bg-white rounded-lg shadow p-5">
            <h2 class="text-xl font-semibold mb-3">Vehicle Details</h2>

            <p class="mb-1"><strong>Make:</strong> {{ $listing->make }}</p>
            <p class="mb-1"><strong>Model:</strong> {{ $listing->model }}</p>
            <p class="mb-1"><strong>Year:</strong> {{ $listing->year }}</p>
            <p class="mb-1"><strong>Mileage:</strong> {{ $listing->mileage ?? 'N/A' }}</p>
            <p class="mb-1"><strong>Price:</strong>
                <span class="text-primary-DEFAULT font-bold">${{ number_format($listing->price, 2) }}</span>
            </p>
            <p class="mb-4"><strong>Status:</strong>
                <span class="font-semibold {{ $listing->status === 'available' ? 'text-green-600' : 'text-red-600' }}">
                    {{ ucfirst($listing->status) }}
                </span>
            </p>

          {{-- Buy Now Button --}}
@php
    // Auction status logic
    $endDate = \Carbon\Carbon::parse($listing->created_at)->addDays($listing->auction_duration);
    $isExpired = $endDate->isPast();

    if ($listing->bought) {
        $status = 'Bought';
        $statusClass = 'text-blue-600';
    } elseif ($isExpired) {
        $status = 'Expired';
        $statusClass = 'text-red-600';
    } else {
        $status = 'Active';
        $statusClass = 'text-green-600';
    }

    // Update logic: mark as bought if not already bought/expired
    if (!$listing->bought && !$isExpired && request('buy') == $listing->id) {
        $listing->bought = true;
        $listing->save();
        $status = 'Bought';
        $statusClass = 'text-blue-600';
    }
@endphp

{{-- Show Buy Now only if auction is active and not bought --}}
@if(!$listing->bought )
    <a href="{{ url()->current() . '?buy=' . $listing->id }}"
       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
        Buy Now
    </a>
@endif

        </div>
    </div>

    {{-- Description Section --}}
    <div class="mt-8 bg-white rounded-lg shadow p-5">
        <h2 class="text-xl font-semibold mb-3">Description</h2>
        <p class="text-gray-700 leading-relaxed">
            {{ $listing->description ?? 'No description available for this vehicle.' }}
        </p>
    </div>
</div>
@endsection
