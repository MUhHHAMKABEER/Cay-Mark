@extends('layouts.welcome')

@section('content')
@php
    $listing = $auctionListing;
    $highestBid = $listing->bids()->where('status', 'active')->orderByDesc('amount')->first();
    $currentBid = $highestBid ? (float) $highestBid->amount : (float) ($listing->starting_price ?? $listing->price ?? 0);
    
    // Calculate time remaining
    $endDate = $listing->auction_end_time 
        ? \Carbon\Carbon::parse($listing->auction_end_time)
        : \Carbon\Carbon::parse($listing->auction_start_time ?? $listing->created_at)->addDays($listing->auction_duration ?? 7);
    
    $isExpired = $endDate->isPast();
    $timeRemaining = !$isExpired ? now()->diff($endDate) : null;
    
    // Get increment service
    $incrementService = new \App\Services\BiddingIncrementService();
    $nextValidBid = $incrementService->calculateMinimumNextBid($currentBid);
    $incrementAmount = $incrementService->getIncrementForBid($currentBid);
    
    // Images
    $images = collect($listing->images ?? [])->map(function($img) {
        $path = is_object($img) ? ($img->image_path ?? $img->path ?? null) : $img;
        if (!$path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        return asset('uploads/listings/' . ltrim($path, '/'));
    })->filter()->values();
    
    $mainImage = $images->first() ?? asset('images/placeholder.png');
    
    // Check if in watchlist
    $inWatchlist = Auth::check() && Auth::user()->watchlist()->where('listing_id', $listing->id)->exists();
    
    // User's highest bid
    $userHighestBid = Auth::check() ? $listing->bids()->where('user_id', Auth::id())->where('status', 'active')->max('amount') : null;
    $isWinning = Auth::check() && $highestBid && $highestBid->user_id === Auth::id();
    $isOutbid = Auth::check() && $userHighestBid && $highestBid && $highestBid->amount > $userHighestBid;
    
    // Sale status
    $saleStatus = 'Active';
    if ($isExpired) {
        $saleStatus = 'Ended';
    } elseif ($listing->status === 'pending') {
        $saleStatus = 'On Approval';
    } elseif ($listing->reserve_price && $currentBid < $listing->reserve_price) {
        $saleStatus = 'On Minimum Bid';
    }
    
    // Reserve status
    $reserveMet = !$listing->reserve_price || $currentBid >= $listing->reserve_price;
@endphp

<style>
    .auction-detail-page {
        background: #f5f7fa;
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }
    
    .section-title {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e2e8f0;
        letter-spacing: -0.5px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .info-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
    }
    
    .countdown-box {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .countdown-display {
        font-size: 32px;
        font-weight: 700;
        font-variant-numeric: tabular-nums;
        margin: 8px 0;
    }
    
    .countdown-label {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 4px;
    }
    
    .current-bid-display {
        font-size: 42px;
        font-weight: 700;
        color: #1e293b;
        text-align: center;
        margin: 16px 0;
    }
    
    .bid-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        margin: 8px 0;
    }
    
    .bid-status-badge.winning {
        background: #dcfce7;
        color: #166534;
    }
    
    .bid-status-badge.outbid {
        background: #fef3c7;
        color: #92400e;
    }
    
    .bid-status-badge.none {
        background: #f1f5f9;
        color: #475569;
    }
    
    .sale-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
    }
    
    .sale-status-badge.active {
        background: #dcfce7;
        color: #166534;
    }
    
    .sale-status-badge.approval {
        background: #fef3c7;
        color: #92400e;
    }
    
    .sale-status-badge.minimum {
        background: #fef3c7;
        color: #92400e;
    }
    
    .sale-status-badge.ended {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .reserve-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
    }
    
    .reserve-badge.met {
        background: #dcfce7;
        color: #166534;
    }
    
    .reserve-badge.not-met {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .bid-input-wrapper {
        display: flex;
        gap: 8px;
        margin: 16px 0;
    }
    
    .bid-input {
        flex: 1;
        padding: 14px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 18px;
        font-weight: 600;
        text-align: center;
    }
    
    .bid-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .bid-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        padding: 14px 32px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s;
        width: 100%;
    }
    
    .bid-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }
    
    .bid-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .photo-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 16px;
        max-width: 100%;
    }
    
    .photo-item {
        position: relative;
        border-radius: 6px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s;
        width: 70px;
        height: 52px;
        flex-shrink: 0;
    }
    
    .photo-item:hover {
        border-color: #3b82f6;
        transform: scale(1.02);
    }
    
    .photo-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .photo-item.active {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    
    .main-photo-container {
        position: relative;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .main-photo {
        width: 100%;
        height: 650px;
        object-fit: cover;
        display: block;
    }
    
    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 16px;
    }
    
    .action-btn {
        flex: 1;
        padding: 12px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .action-btn:hover {
        border-color: #3b82f6;
        color: #3b82f6;
        background: #eff6ff;
    }
    
    .action-btn.active {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
    }
    
    .final-price-btn {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 12px;
    }
    
    .final-price-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }
    
    .damage-section {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 16px;
        border-radius: 8px;
        margin: 12px 0;
    }
    
    .damage-label {
        font-size: 12px;
        color: #92400e;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    
    .damage-value {
        font-size: 16px;
        color: #78350f;
        font-weight: 600;
    }
    
    .notes-section {
        background: #f8fafc;
        border-left: 4px solid #64748b;
        padding: 16px;
        border-radius: 8px;
        margin: 12px 0;
    }
    
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #3b82f6;
        font-weight: 600;
        margin-bottom: 20px;
        text-decoration: none;
    }
    
    .back-link:hover {
        text-decoration: underline;
    }
</style>

<div class="auction-detail-page">
    <div class="container mx-auto px-4" style="max-width: 90%;">
        <!-- Back Link -->
        <a href="{{ route('buyer.auctions') }}" class="back-link">
            <span class="material-icons">arrow_back</span>
            Back to results
        </a>

        <!-- Main Title -->
        <h1 class="text-4xl font-bold mb-6 text-gray-900">
            {{ $listing->year ?? '' }} {{ $listing->make ?? '' }} {{ $listing->model ?? '' }} {{ $listing->trim ?? '' }}
        </h1>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Left Column: Sections 4 & 5 (Bigger for images) -->
            <div class="lg:col-span-3 space-y-6">
                <!-- PHOTOS -->
                <div class="section-card">
                    <h2 class="section-title">
                        PHOTOS
                    </h2>
                    
                    @if($images->count() > 0)
                    <!-- Main Photo -->
                    <div class="main-photo-container">
                        <img src="{{ $mainImage }}" alt="Main photo" class="main-photo" id="mainPhoto">
                    </div>

                    <!-- Photo Gallery Grid -->
                    <div class="photo-gallery">
                        @foreach($images as $index => $img)
                        <div class="photo-item {{ $index === 0 ? 'active' : '' }}" 
                             onclick="changeMainPhoto('{{ $img }}', {{ $index }})"
                             data-index="{{ $index }}">
                            <img src="{{ $img }}" alt="Photo {{ $index + 1 }}">
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <span class="material-icons text-gray-400" style="font-size: 64px;">image_not_supported</span>
                        <p class="text-gray-500 mt-4">No photos available</p>
                    </div>
                    @endif
                </div>

                <!-- LOT INFORMATION -->
                <div class="section-card">
                    <h2 class="section-title">
                        LOT INFORMATION
                    </h2>
                    
                    <div class="info-grid mb-6">
                        <div class="info-item">
                            <span class="info-label">Location</span>
                            <span class="info-value">{{ $listing->island ?? 'N/A' }}</span>
                        </div>
                        @if($listing->item_number)
                        <div class="info-item">
                            <span class="info-label">Item Number</span>
                            <span class="info-value">{{ $listing->item_number }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="action-buttons">
                        <button onclick="toggleWatchlist()" class="action-btn {{ $inWatchlist ? 'active' : '' }}" id="watchlistBtn">
                            <span class="material-icons">{{ $inWatchlist ? 'favorite' : 'favorite_border' }}</span>
                            <span>Add to Watchlist</span>
                        </button>
                        <button onclick="shareListing()" class="action-btn">
                            <span class="material-icons">share</span>
                            <span>Share Listing</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sections 1, 2 & 3 -->
            <div class="lg:col-span-2 space-y-6">
                <!-- VEHICLE INFORMATION -->
                <div class="section-card">
                    <h2 class="section-title">
                        VEHICLE INFORMATION
                    </h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Vehicle Type</span>
                            <span class="info-value">
                                @if($listing->vehicle_type && stripos($listing->vehicle_type, 'INCOMPLETE') === false)
                                    {{ $listing->vehicle_type }}
                                @elseif($listing->major_category)
                                    {{ $listing->major_category }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Year</span>
                            <span class="info-value">{{ $listing->year ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Make</span>
                            <span class="info-value">{{ $listing->make ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Model</span>
                            <span class="info-value">{{ $listing->model ?? 'N/A' }}</span>
                        </div>
                        @if($listing->trim)
                        <div class="info-item">
                            <span class="info-label">Trim</span>
                            <span class="info-value">{{ $listing->trim }}</span>
                        </div>
                        @endif
                        <div class="info-item">
                            <span class="info-label">Body Style</span>
                            <span class="info-value">{{ $listing->body_style ?? $listing->subcategory ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Color</span>
                            <span class="info-value">{{ $listing->color ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Engine Size</span>
                            <span class="info-value">{{ $listing->engine_type ?? 'N/A' }}</span>
                        </div>
                        @if($listing->cylinders)
                        <div class="info-item">
                            <span class="info-label">Cylinders</span>
                            <span class="info-value">{{ $listing->cylinders }}</span>
                        </div>
                        @endif
                        <div class="info-item">
                            <span class="info-label">Transmission</span>
                            <span class="info-value">
                                @if($listing->transmission)
                                    {{ ucfirst($listing->transmission) }}
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                        @if($listing->drive_type)
                        <div class="info-item">
                            <span class="info-label">Drive Type</span>
                            <span class="info-value">{{ $listing->drive_type }}</span>
                        </div>
                        @endif
                        <div class="info-item">
                            <span class="info-label">Fuel Type</span>
                            <span class="info-value">{{ $listing->fuel_type ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Odometer</span>
                            <span class="info-value">{{ $listing->odometer ? number_format($listing->odometer) . ' mi' : 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Has Key</span>
                            <span class="info-value">{{ $listing->keys_available ? 'Yes' : 'No' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">VIN</span>
                            <span class="info-value">{{ $listing->vin ? substr($listing->vin, 0, 8) . '******' : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- BID INFORMATION -->
                <div class="section-card">
                    <h2 class="section-title">
                        BID INFORMATION
                    </h2>
            
                    <!-- Auction Countdown -->
                    <div class="countdown-box" id="countdownBox">
                        <div class="countdown-label">Auction Countdown</div>
                        <div class="countdown-display" id="countdownDisplay">
                            @if($timeRemaining && !$isExpired)
                                @if($timeRemaining->days > 0)
                                    {{ $timeRemaining->days }}d : {{ $timeRemaining->h }}h : {{ $timeRemaining->i }}m : {{ $timeRemaining->s }}s
                                @elseif($timeRemaining->h > 0)
                                    {{ $timeRemaining->h }}h : {{ $timeRemaining->i }}m : {{ $timeRemaining->s }}s
                                @else
                                    {{ $timeRemaining->i }}m : {{ $timeRemaining->s }}s
                                @endif
                            @else
                                Auction Ended
                            @endif
                        </div>
                    </div>

                    <!-- Sale Ends -->
                    <div class="info-item mb-4">
                        <span class="info-label">Sale Ends</span>
                        <span class="info-value">{{ $endDate->format('F d, Y') }} at {{ $endDate->format('g:i A') }}</span>
                    </div>

                    <!-- Current Bid -->
                    <div class="text-center mb-6">
                        <div class="info-label mb-2">Current Bid</div>
                        <div class="current-bid-display">${{ number_format($currentBid, 0) }}</div>
                    </div>

                    <!-- Your Bid Status -->
                    <div class="mb-4">
                        <span class="info-label">Your Bid Status</span>
                        <div class="mt-2">
                            @if(Auth::check())
                                @if($isWinning)
                                    <div class="bid-status-badge winning">
                                        <span class="material-icons">check_circle</span>
                                        You're the Highest Bidder
                                    </div>
                                @elseif($isOutbid)
                                    <div class="bid-status-badge outbid">
                                        <span class="material-icons">trending_down</span>
                                        Outbid
                                    </div>
                                @elseif($userHighestBid)
                                    <div class="bid-status-badge outbid">
                                        <span class="material-icons">trending_down</span>
                                        Outbid
                                    </div>
                                @else
                                    <div class="bid-status-badge none">
                                        <span class="material-icons">info</span>
                                        You Haven't Bid
                                    </div>
                                @endif
                            @else
                                <div class="bid-status-badge none">
                                    <span class="material-icons">info</span>
                                    You Haven't Bid
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Sale Status -->
                    <div class="mb-4">
                        <span class="info-label">Sale Status</span>
                        <div class="mt-2">
                            @if($saleStatus === 'Active')
                                <span class="sale-status-badge active">
                                    <span class="material-icons" style="font-size: 16px;">check_circle</span>
                                    Active
                                </span>
                            @elseif($saleStatus === 'On Approval')
                                <span class="sale-status-badge approval">
                                    <span class="material-icons" style="font-size: 16px;">schedule</span>
                                    On Approval
                                </span>
                            @elseif($saleStatus === 'On Minimum Bid')
                                <span class="sale-status-badge minimum">
                                    <span class="material-icons" style="font-size: 16px;">warning</span>
                                    On Minimum Bid
                                </span>
                            @else
                                <span class="sale-status-badge ended">
                                    <span class="material-icons" style="font-size: 16px;">cancel</span>
                                    Ended
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Seller's Reserve -->
                    @if($listing->reserve_price)
                    <div class="mb-4">
                        <span class="info-label">Seller's Reserve</span>
                        <div class="mt-2">
                            @if($reserveMet)
                                <span class="reserve-badge met">
                                    <span class="material-icons" style="font-size: 16px;">check_circle</span>
                                    Met
                                </span>
                            @else
                                <span class="reserve-badge not-met">
                                    <span class="material-icons" style="font-size: 16px;">cancel</span>
                                    Not Met
                                </span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Bid Increment -->
                    <div class="mb-4">
                        <span class="info-label">Bid Increment</span>
                        <span class="info-value">${{ number_format($incrementAmount, 0) }}</span>
                    </div>

                    <!-- Place Your Bid -->
                    @if(!$isExpired && Auth::check() && Auth::user()->role === 'buyer')
                    <div class="mb-4">
                        <span class="info-label">Place Your Bid</span>
                        <form action="{{ route('auction.bid.store', $listing->getSlugOrGenerate()) }}" method="POST" id="bidForm">
                            @csrf
                            <div class="bid-input-wrapper">
                                <button type="button" onclick="adjustBid(-{{ $incrementAmount }})" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-l-lg font-bold">-</button>
                                <input type="number" 
                                       name="amount" 
                                       id="bidAmount"
                                       class="bid-input"
                                       value="{{ $nextValidBid }}"
                                       min="{{ $nextValidBid }}"
                                       step="{{ $incrementAmount }}">
                                <button type="button" onclick="adjustBid({{ $incrementAmount }})" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-r-lg font-bold">+</button>
                            </div>
                            <button type="submit" class="bid-btn" id="bidSubmitBtn">
                                Bid now
                            </button>
                        </form>
                    </div>
                    @elseif(!Auth::check())
                    <div class="mb-4">
                        <span class="info-label">Place Your Bid</span>
                        <div class="text-center py-4">
                            <p class="text-gray-600 mb-4">Please login to place a bid</p>
                            <a href="{{ route('login') }}" class="bid-btn inline-block w-auto px-8">Login</a>
                        </div>
                    </div>
                    @elseif($isExpired)
                    <div class="mb-4">
                        <span class="info-label">Place Your Bid</span>
                        <div class="text-center py-4">
                            <p class="text-red-600 font-semibold">This auction has ended</p>
                        </div>
                    </div>
                    @endif

                    <!-- Final Price Calculator -->
                    <button onclick="window.open('{{ route('fee-calculator') }}', '_blank')" class="final-price-btn">
                        <span class="material-icons" style="font-size: 18px; vertical-align: middle;">calculate</span>
                        Final Price Calculator
                    </button>

                    <p class="text-xs text-gray-500 mt-4 text-center italic">
                        ALL SALES ARE FINAL. SOLD "AS IS, WHERE IS."
                    </p>
                </div>

                <!-- DAMAGE INFORMATION -->
                <div class="section-card">
                    <h2 class="section-title">
                        DAMAGE INFORMATION
                    </h2>
                    
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

                    @if($listing->notes || $listing->description)
                    <div class="notes-section">
                        <div class="damage-label">Additional Notes (Optional)</div>
                        <div class="damage-value" style="color: #475569;">
                            {{ $listing->notes ?? $listing->description ?? 'No additional notes provided.' }}
                        </div>
                    </div>
                    @else
                    <div class="notes-section">
                        <div class="damage-label">Additional Notes (Optional)</div>
                        <div class="damage-value" style="color: #64748b;">
                            No additional notes provided.
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const images = @json($images->toArray());
    let currentImageIndex = 0;

    function changeMainPhoto(src, index) {
        currentImageIndex = index;
        document.getElementById('mainPhoto').src = src;
        
        // Update active thumbnail
        document.querySelectorAll('.photo-item').forEach((item, i) => {
            item.classList.toggle('active', i === index);
        });
    }

    function adjustBid(amount) {
        const input = document.getElementById('bidAmount');
        const current = parseFloat(input.value) || {{ $nextValidBid }};
        const min = {{ $nextValidBid }};
        const newAmount = Math.max(min, current + amount);
        input.value = newAmount.toFixed(2);
    }

    function toggleWatchlist() {
        @if(Auth::check())
        fetch('{{ route("listing.watchlist", $listing->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            const btn = document.getElementById('watchlistBtn');
            if (data.in_watchlist) {
                btn.classList.add('active');
                btn.querySelector('span.material-icons').textContent = 'favorite';
            } else {
                btn.classList.remove('active');
                btn.querySelector('span.material-icons').textContent = 'favorite_border';
            }
        });
        @else
        window.location.href = '{{ route("login") }}';
        @endif
    }

    function shareListing() {
        if (navigator.share) {
            navigator.share({
                title: '{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}',
                text: 'Check out this auction listing!',
                url: window.location.href
            });
        } else {
            // Fallback: Copy to clipboard
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link copied to clipboard!');
            });
        }
    }

    // Countdown Timer - Real-time calculation from database end time
    @if($timeRemaining && !$isExpired)
    const countdownEl = document.getElementById('countdownDisplay');
    const countdownBox = document.getElementById('countdownBox');
    const endTime = new Date('{{ $endDate->toIso8601String() }}');
    
    function updateCountdown() {
        const now = new Date();
        const diff = Math.max(0, Math.floor((endTime - now) / 1000));
        
        if (diff <= 0) {
            countdownEl.textContent = 'Auction Ended';
            countdownBox.style.background = 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)';
            return;
        }
        
        const days = Math.floor(diff / 86400);
        const hours = Math.floor((diff % 86400) / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        const seconds = diff % 60;
        
        if (days > 0) {
            countdownEl.textContent = `${days}d : ${hours}h : ${minutes}m : ${seconds}s`;
        } else if (hours > 0) {
            countdownEl.textContent = `${hours}h : ${minutes}m : ${seconds}s`;
        } else {
            countdownEl.textContent = `${minutes}m : ${seconds}s`;
        }
    }
    
    updateCountdown();
    setInterval(updateCountdown, 1000); // Update every second
    @endif

    // Form Submission
    document.getElementById('bidForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const submitBtn = document.getElementById('bidSubmitBtn');
        const amount = parseFloat(document.getElementById('bidAmount').value);
        const minBid = {{ $nextValidBid }};
        
        if (amount < minBid) {
            alert('Your bid must be at least $' + minBid.toLocaleString());
            return false;
        }
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Placing Bid...';
        
        // Submit via AJAX
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                amount: amount
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Bid placed successfully!');
                location.reload();
            } else {
                alert(data.message || 'Failed to place bid');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Bid now';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Bid now';
        });
    });
</script>
@endsection
