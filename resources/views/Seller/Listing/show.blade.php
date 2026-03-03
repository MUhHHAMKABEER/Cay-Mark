@extends('layouts.Seller')

@section('title', 'Listing Preview – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? ''))

@section('content')
@php
    $images = collect($listing->images ?? [])->map(function($img) {
        $path = is_object($img) ? ($img->image_path ?? $img->path ?? null) : $img;
        if (!$path) return null;
        if (str_starts_with($path, 'http')) return $path;
        if (str_starts_with($path, 'storage/') || str_starts_with($path, 'listings/')) {
            return asset($path);
        }
        return asset('storage/' . ltrim($path, '/'));
    })->filter()->values();
    $mainImage = $images->first() ?? asset('images/placeholder-product.png');
@endphp

<style>
    .seller-preview-page { background: #f5f7fa; min-height: 100vh; padding: 1.5rem 0; }
    .section-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 24px; }
    .section-title { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e2e8f0; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }
    .info-item { display: flex; flex-direction: column; gap: 4px; }
    .info-label { font-size: 12px; color: #64748b; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
    .info-value { font-size: 16px; font-weight: 600; color: #1e293b; }
    .analytics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; }
    .analytics-card { background: white; border-radius: 12px; padding: 1.25rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-align: center; border: 1px solid #e2e8f0; }
    .analytics-value { font-size: 1.75rem; font-weight: 700; color: #1e293b; }
    .analytics-label { font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }
    .damage-section { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 1rem; border-radius: 8px; margin: 0.5rem 0; }
    .damage-label { font-size: 12px; color: #92400e; font-weight: 600; }
    .damage-value { font-size: 16px; color: #78350f; font-weight: 600; }
    .notes-section { background: #f8fafc; border-left: 4px solid #64748b; padding: 1rem; border-radius: 8px; margin: 0.5rem 0; }
    .main-photo { width: 100%; max-height: 500px; object-fit: cover; border-radius: 12px; }
    .photo-gallery { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
    .photo-item { width: 70px; height: 52px; border-radius: 6px; overflow: hidden; cursor: pointer; border: 2px solid transparent; }
    .photo-item:hover, .photo-item.active { border-color: #2563eb; }
    .photo-item img { width: 100%; height: 100%; object-fit: cover; }
</style>

<div class="seller-preview-page">
    <div class="container mx-auto px-4" style="max-width: 1200px;">
        {{-- Breadcrumb --}}
        <nav class="text-sm mb-4 text-gray-600">
            <ol class="list-reset flex flex-wrap items-center gap-x-2">
                <li><a href="{{ route('seller.dashboard') }}" class="text-blue-600 hover:underline">Dashboard</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('seller.listings.index') }}" class="text-blue-600 hover:underline">My Listings</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-800 font-semibold truncate">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</li>
            </ol>
        </nav>

        {{-- Actions + Title --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">
                {{ $listing->year ?? '' }} {{ $listing->make ?? '' }} {{ $listing->model ?? '' }} {{ $listing->trim ?? '' }}
            </h1>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('seller.listings.edit', $listing) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-700 text-white font-medium rounded-lg hover:bg-gray-800 shadow-sm">
                    <span class="material-icons text-lg">edit</span>
                    Edit Listing
                </a>
                @if($listing->listing_method === 'auction' && $listing->slug)
                    <a href="{{ route('auction.show', $listing) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm">
                        <span class="material-icons text-lg">visibility</span>
                        View Public Page
                    </a>
                @else
                    <a href="{{ route('listing.show', $listing) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm">
                        <span class="material-icons text-lg">visibility</span>
                        View Public Page
                    </a>
                @endif
            </div>
        </div>

        {{-- Analytics --}}
        <div class="analytics-grid mb-8">
            <div class="analytics-card">
                <div class="analytics-value">{{ number_format($viewCount) }}</div>
                <div class="analytics-label">Views / Clicks</div>
            </div>
            <div class="analytics-card">
                <div class="analytics-value">{{ number_format($totalBids) }}</div>
                <div class="analytics-label">Total Bids Received</div>
            </div>
            <div class="analytics-card">
                <div class="analytics-value">
                    @if($timeRemaining && !$isExpired)
                        @if($timeRemaining->days > 0)
                            {{ $timeRemaining->days }}d {{ $timeRemaining->h }}h
                        @elseif($timeRemaining->h > 0)
                            {{ $timeRemaining->h }}h {{ $timeRemaining->i }}m
                        @else
                            {{ $timeRemaining->i }}m {{ $timeRemaining->s }}s
                        @endif
                    @else
                        <span class="text-red-600">Ended</span>
                    @endif
                </div>
                <div class="analytics-label">Time Remaining</div>
            </div>
            @if($listing->listing_method === 'auction')
            <div class="analytics-card">
                <div class="analytics-value text-green-600">${{ number_format($currentBid, 0) }}</div>
                <div class="analytics-label">Current Bid</div>
            </div>
            @endif
        </div>

        {{-- Full preview layout (same as front-end) --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- Left: Photos + Lot --}}
            <div class="lg:col-span-3 space-y-6">
                <div class="section-card">
                    <h2 class="section-title">Photos</h2>
                    @if($images->count() > 0)
                        <img src="{{ $mainImage }}" alt="Main" class="main-photo" id="mainPhoto">
                        <div class="photo-gallery">
                            @foreach($images as $index => $img)
                                <div class="photo-item {{ $index === 0 ? 'active' : '' }}" onclick="changePhoto('{{ $img }}', {{ $index }})" data-index="{{ $index }}">
                                    <img src="{{ $img }}" alt="Photo {{ $index + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <span class="material-icons text-gray-400" style="font-size: 48px;">image_not_supported</span>
                            <p class="text-gray-500 mt-2">No photos</p>
                        </div>
                    @endif
                </div>

                <div class="section-card">
                    <h2 class="section-title">Lot Information</h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Location (Island)</span>
                            <span class="info-value">{{ $listing->island ?? 'N/A' }}</span>
                        </div>
                        @if($listing->item_number)
                        <div class="info-item">
                            <span class="info-label">Item Number</span>
                            <span class="info-value">{{ $listing->item_number }}</span>
                        </div>
                        @endif
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="info-value">{{ ucfirst($listing->status ?? 'N/A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Vehicle info + Damage --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="section-card">
                    <h2 class="section-title">Vehicle Information</h2>
                    <div class="info-grid">
                        <div class="info-item"><span class="info-label">Vehicle Type</span><span class="info-value">{{ $listing->vehicle_type ?? $listing->major_category ?? 'N/A' }}</span></div>
                        <div class="info-item"><span class="info-label">Year</span><span class="info-value">{{ $listing->year ?? 'N/A' }}</span></div>
                        <div class="info-item"><span class="info-label">Make</span><span class="info-value">{{ $listing->make ?? 'N/A' }}</span></div>
                        <div class="info-item"><span class="info-label">Model</span><span class="info-value">{{ $listing->model ?? 'N/A' }}</span></div>
                        @if($listing->trim)<div class="info-item"><span class="info-label">Trim</span><span class="info-value">{{ $listing->trim }}</span></div>@endif
                        <div class="info-item"><span class="info-label">Body Style</span><span class="info-value">{{ $listing->body_style ?? $listing->subcategory ?? 'N/A' }}</span></div>
                        <div class="info-item"><span class="info-label">Color</span><span class="info-value">{{ $listing->color ?? 'N/A' }}</span></div>
                        <div class="info-item"><span class="info-label">Engine</span><span class="info-value">{{ $listing->engine_type ?? 'N/A' }}</span></div>
                        @if($listing->cylinders)<div class="info-item"><span class="info-label">Cylinders</span><span class="info-value">{{ $listing->cylinders }}</span></div>@endif
                        <div class="info-item"><span class="info-label">Transmission</span><span class="info-value">{{ $listing->transmission ? ucfirst($listing->transmission) : 'N/A' }}</span></div>
                        @if($listing->drive_type)<div class="info-item"><span class="info-label">Drive Type</span><span class="info-value">{{ $listing->drive_type }}</span></div>@endif
                        <div class="info-item"><span class="info-label">Fuel Type</span><span class="info-value">{{ $listing->fuel_type ?? 'N/A' }}</span></div>
                        <div class="info-item">
                            <span class="info-label">Odometer</span>
                            <span class="info-value">
                                @if($listing->odometer)
                                    {{ number_format($listing->odometer) }} mi
                                    @if($listing->odometer_estimated ?? false)<span class="text-amber-600 font-medium">(Estimated)</span>@endif
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <div class="info-item"><span class="info-label">Has Key</span><span class="info-value">{{ $listing->keys_available ? 'Yes' : 'No' }}</span></div>
                        <div class="info-item">
                            <span class="info-label">Title Status</span>
                            <span class="info-value">
                                @if(($listing->title_status ?? '') === 'CLEAN') Yes
                                @elseif(($listing->title_status ?? '') === 'SALVAGE') No
                                @else {{ $listing->title_status ?? 'N/A' }}
                                @endif
                            </span>
                        </div>
                        <div class="info-item"><span class="info-label">VIN</span><span class="info-value font-mono">{{ $listing->vin ?? 'N/A' }}</span></div>
                    </div>
                </div>

                <div class="section-card">
                    <h2 class="section-title">Damage Information</h2>
                    @if($listing->primary_damage)
                        <div class="damage-section">
                            <div class="damage-label">Primary Damage</div>
                            <div class="damage-value">{{ $listing->primary_damage }}</div>
                        </div>
                    @endif
                    @if($listing->secondary_damage)
                        <div class="damage-section">
                            <div class="damage-label">Secondary Damage</div>
                            <div class="damage-value">{{ $listing->secondary_damage }}</div>
                        </div>
                    @endif
                    <div class="notes-section">
                        <div class="damage-label">Additional Notes</div>
                        <div class="damage-value" style="color: #475569;">
                            {{ $listing->rejection_notes ?? $listing->additional_notes ?? 'None' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($images->count() > 0)
<script>
function changePhoto(src, index) {
    document.getElementById('mainPhoto').src = src;
    document.querySelectorAll('.photo-item').forEach(function(el, i) {
        el.classList.toggle('active', i === index);
    });
}
</script>
@endif
@endsection
