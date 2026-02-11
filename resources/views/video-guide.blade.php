@extends('layouts.welcome')

@section('title', 'Video guides – CayMark')

@section('content')
<style>
    .video-guide-page { font-family: 'Roboto', sans-serif; }
    .video-guide-nav { background: #fff; border-bottom: 1px solid #e5e7eb; }
    .video-guide-nav a {
        color: #1e40af;
        font-weight: 700;
        padding: 1rem 0.75rem;
        margin: 0 0.25rem;
        text-decoration: none;
        position: relative;
        display: inline-block;
    }
    .video-guide-nav a:hover { color: #1d4ed8; }
    .video-guide-nav a.active::after {
        content: '';
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 0;
        width: 75%;
        height: 4px;
        background: #ea580c;
        border-radius: 2px;
    }
    .video-guide-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
    @media (max-width: 1024px) { .video-guide-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px)  { .video-guide-grid { grid-template-columns: 1fr; } }
    .video-guide-card {
        background: #fff;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .video-guide-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.12); }
    .video-guide-banner {
        background: linear-gradient(145deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%);
        min-height: 220px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    .video-guide-banner::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60%;
        background: linear-gradient(to top, rgba(0,0,0,0.15) 0%, transparent 100%);
        pointer-events: none;
    }
    .vehicle-silhouettes {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 45%;
        opacity: 0.12;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 120 40' fill='%23fff'%3E%3Cpath d='M10 28 L20 22 L30 26 L40 24 L50 28 L60 24 L70 26 L80 22 L90 28 L100 24 L110 26 L120 22' opacity='0.5'/%3E%3Cpath d='M15 32 L25 28 L35 30 L45 28 L55 32 L65 28 L75 30 L85 28 L95 32 L105 28 L115 30' opacity='0.4'/%3E%3C/svg%3E") bottom center repeat-x;
        background-size: 120px 40px;
    }
    .video-guide-banner-text { font-size: 1.125rem; line-height: 1.35; letter-spacing: 0.02em; }
    .video-thumb-wrap {
        position: relative;
        aspect-ratio: 16/10;
        background: linear-gradient(145deg, #0f172a 0%, #1e3a8a 60%, #1e40af 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .video-thumb-wrap img { width: 100%; height: 100%; object-fit: cover; opacity: 0.85; }
    .play-btn {
        position: absolute;
        width: 72px;
        height: 72px;
        background: #dc2626;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        box-shadow: 0 4px 20px rgba(220,38,38,0.5);
        cursor: pointer;
        z-index: 2;
        transition: transform 0.2s, background 0.2s;
    }
    .play-btn:hover { transform: scale(1.08); background: #b91c1c; }
    .play-btn .material-icons { font-size: 40px; margin-left: 4px; }
    .video-overlay-content {
        position: absolute;
        inset: 0;
        padding: 1rem 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        z-index: 1;
        background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, transparent 35%, rgba(0,0,0,0.4) 100%);
    }
    .pill-badge {
        display: inline-block;
        background: #ea580c;
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 0.35rem 0.65rem;
        border-radius: 9999px;
        align-self: flex-start;
    }
    .video-overlay-title { font-size: 1.25rem; font-weight: 800; letter-spacing: 0.03em; line-height: 1.2; }
    .video-overlay-sub { font-size: 0.875rem; opacity: 0.95; margin-top: 0.25rem; }
    .video-card-caption { padding: 0.75rem 0 0; font-size: 0.9375rem; color: #1f2937; font-weight: 500; }
    .video-card-caption a { color: #1e40af; text-decoration: none; }
    .video-card-caption a:hover { text-decoration: underline; }
    .brand-logo-small { width: 56px; height: auto; opacity: 0.9; }
</style>

<div class="video-guide-page bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-6 max-w-6xl">
        {{-- Category navigation --}}
        <nav class="video-guide-nav flex flex-wrap items-center justify-center gap-1 -mx-2 mb-8">
            <a href="{{ route('video-guide') }}?cat=all" class="{{ !request('cat') || request('cat') === 'all' ? 'active' : '' }}">View all</a>
            <a href="{{ route('video-guide') }}?cat=bid-buy" class="{{ request('cat') === 'bid-buy' ? 'active' : '' }}">Bid & buy</a>
            <a href="{{ route('video-guide') }}?cat=pay-pickup" class="{{ request('cat') === 'pay-pickup' ? 'active' : '' }}">Pay & pick up</a>
            <a href="{{ route('video-guide') }}?cat=testimonials" class="{{ request('cat') === 'testimonials' ? 'active' : '' }}">Testimonials & reviews</a>
            <a href="{{ route('video-guide') }}?cat=getting-started" class="{{ request('cat') === 'getting-started' ? 'active' : '' }}">Getting started</a>
            <a href="{{ route('video-guide') }}?cat=seller" class="{{ request('cat') === 'seller' ? 'active' : '' }}">Seller guides</a>
        </nav>

        {{-- Grid: intro banner + video cards --}}
        <div class="video-guide-grid">
            {{-- Introductory banner card (top-left) --}}
            <div class="video-guide-card">
                <div class="video-guide-banner">
                    <div class="vehicle-silhouettes" aria-hidden="true"></div>
                    <p class="video-guide-banner-text text-white font-bold uppercase tracking-wide relative z-10">A trusted marketplace for 100% online vehicle auctions</p>
                    <div class="relative z-10 flex justify-end">
                        <img src="{{ asset(config('logos.header', 'Logos/1.png')) }}" alt="CayMark" class="brand-logo-small brightness-0 invert" />
                    </div>
                </div>
                <p class="video-card-caption pt-3">Welcome to CayMark</p>
            </div>

            {{-- Video tutorial cards (placeholder content – replace with real videos/links) --}}
            @foreach ([
                ['title' => 'Registration', 'sub' => 'Register and sign in', 'caption' => 'Step 1 of 3 | How to register and sign up for used vehicles', 'cat' => 'getting-started', 'thumb' => null],
                ['title' => 'Membership', 'sub' => 'Buyer and seller accounts', 'caption' => 'Step 2 of 3 | How to become a CayMark member and buy vehicles', 'cat' => 'getting-started', 'thumb' => null],
                ['title' => 'Licenses & documents', 'sub' => 'Upload and verify', 'caption' => 'Step 3 of 3 | How to upload licenses and documents', 'cat' => 'getting-started', 'thumb' => null],
                ['title' => 'Bidding', 'sub' => 'Place and manage bids', 'caption' => 'How to bid on vehicles and manage your bids', 'cat' => 'bid-buy', 'thumb' => null],
                ['title' => 'Payment & pickup', 'sub' => 'Pay and arrange collection', 'caption' => 'How to pay and arrange vehicle pickup', 'cat' => 'pay-pickup', 'thumb' => null],
            ] as $i => $video)
                @if(!request('cat') || request('cat') === 'all' || request('cat') === $video['cat'])
                <div class="video-guide-card">
                    <div class="video-thumb-wrap">
                        @if($video['thumb'])
                            <img src="{{ asset($video['thumb']) }}" alt="">
                        @endif
                        <div class="video-overlay-content">
                            <div class="flex items-start justify-between gap-2">
                                <img src="{{ asset(config('logos.header', 'Logos/1.png')) }}" alt="" class="w-8 h-8 object-contain brightness-0 invert opacity-90" />
                                <span class="pill-badge">CayMark tutorials</span>
                            </div>
                            <div>
                                <p class="video-overlay-title text-white">{{ $video['title'] }}</p>
                                <p class="video-overlay-sub text-white">/ {{ $video['sub'] }}</p>
                            </div>
                            <div class="flex justify-end">
                                <img src="{{ asset(config('logos.header', 'Logos/1.png')) }}" alt="CayMark" class="brand-logo-small brightness-0 invert" />
                            </div>
                        </div>
                        <a href="#" class="play-btn" aria-label="Play: {{ $video['caption'] }}">
                            <span class="material-icons">play_arrow</span>
                        </a>
                    </div>
                    <p class="video-card-caption">{{ $video['caption'] }}</p>
                </div>
                @endif
            @endforeach
        </div>

        @if(request('cat') && request('cat') !== 'all')
            <p class="text-center text-gray-500 mt-8">
                <a href="{{ route('video-guide') }}" class="text-blue-600 hover:underline">View all video guides</a>
            </p>
        @endif
    </div>
</div>
@endsection
