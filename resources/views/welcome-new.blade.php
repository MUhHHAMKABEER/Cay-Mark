@extends('layouts.welcome')

@section('content')
@php
    // Get top 8 popular auctions (by views/clicks or default to latest)
    $popularAuctions = \App\Models\Listing::with('images')
        ->withCount(['watchlistedBy as likes_count'])
        ->where('listing_method', 'auction')
        ->where('listing_state', 'active')
        ->where('status', 'approved')
        ->orderByDesc('likes_count')
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get();
    
    // Get marketplace items for vehicle finder
    $marketplaceItems = \App\Models\Listing::with('images')
        ->where('listing_method', 'buy_now')
        ->where('listing_state', 'active')
        ->where('status', 'approved')
        ->take(12)
        ->get();

    $likedListingIds = Auth::check() ? Auth::user()->watchlist()->pluck('listing_id') : collect();
@endphp

<!-- Banner Header with Rotating Images -->
<!-- Dimensions: 1920x600px -->
<section class="relative h-[600px] overflow-hidden" x-data="{ currentSlide: 0, slides: 3 }">
    <div class="absolute inset-0">
        <!-- Slide 1 -->
        <div x-show="currentSlide === 0" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/banner-1.jpg') }}');">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/30"></div>
        </div>
        <!-- Slide 2 -->
        <div x-show="currentSlide === 1" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/banner-2.jpg') }}');">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/30"></div>
        </div>
        <!-- Slide 3 -->
        <div x-show="currentSlide === 2" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/banner-3.jpg') }}');">
            <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-black/30"></div>
        </div>
    </div>
    
    <!-- Banner Content -->
    <div class="relative z-10 h-full flex items-center">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl text-white">
                <div class="inline-flex items-center bg-white/20 backdrop-blur-sm rounded-full px-4 py-2 mb-4 border border-white/30">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm font-medium">LIVE AUCTIONS HAPPENING NOW</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight font-heading drop-shadow-lg">INTRODUCING WHOLESALE AUCTIONS</h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100 drop-shadow-md">(Including Bank-Repo Vehicles) FLEET, FINANCE & COPART SELECT VEHICLES.</p>
                <a href="{{ route('Auction.index') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                    View Inventory
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Carousel Controls -->
    <button @click="currentSlide = (currentSlide - 1 + slides) % slides" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white p-3 rounded-full transition-all">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <button @click="currentSlide = (currentSlide + 1) % slides" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white p-3 rounded-full transition-all">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    
    <!-- Auto-rotate script -->
    <script>
        setInterval(() => {
            if (document.querySelector('[x-data*="currentSlide"]')) {
                const component = Alpine.$data(document.querySelector('[x-data*="currentSlide"]'));
                if (component) component.currentSlide = (component.currentSlide + 1) % component.slides;
            }
        }, 5000);
    </script>
</section>

<!-- Popular Car Auctions Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 font-heading">Popular Car Auctions</h2>
            <a href="{{ route('Auction.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">View All →</a>
        </div>
        
        <!-- Carousel Container (shows 4 at a time, can scroll to see 4 more) -->
        <div class="relative" x-data="{ currentIndex: 0, itemsPerView: 4, totalItems: {{ $popularAuctions->count() }} }">
            <div class="overflow-hidden">
                <div class="flex transition-transform duration-500 ease-in-out" :style="`transform: translateX(-${currentIndex * (100 / itemsPerView)}%)`">
                    @foreach($popularAuctions as $auction)
                        <div class="w-full md:w-1/2 lg:w-1/4 flex-shrink-0 px-3">
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-all">
                                <div class="relative">
                                    @php
                                        $img = $auction->images->first();
                                        $imgUrl = $img ? (str_contains($img->image_path, '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path)) : asset('images/placeholder-car.png');
                                    @endphp
                                    <img src="{{ $imgUrl }}" alt="{{ $auction->make }} {{ $auction->model }}" class="w-full h-48 object-cover">
                                    <!-- Countdown Timer -->
                                    <div class="absolute bottom-2 left-2 bg-black/70 text-white px-3 py-1.5 rounded-lg text-sm font-semibold">
                                        @php
                                            $endDate = $auction->auction_end_time ? \Carbon\Carbon::parse($auction->auction_end_time) : \Carbon\Carbon::parse($auction->created_at)->addDays($auction->auction_duration ?? 7);
                                            $now = \Carbon\Carbon::now();
                                            $diff = $now->diff($endDate);
                                        @endphp
                                        <span x-data="{ 
                                            days: {{ $diff->days }}, 
                                            hours: {{ $diff->h }}, 
                                            minutes: {{ $diff->i }}, 
                                            seconds: {{ $diff->s }},
                                            init() {
                                                setInterval(() => {
                                                    this.seconds--;
                                                    if (this.seconds < 0) { this.seconds = 59; this.minutes--; }
                                                    if (this.minutes < 0) { this.minutes = 59; this.hours--; }
                                                    if (this.hours < 0) { this.hours = 23; this.days--; }
                                                }, 1000);
                                            }
                                        }">
                                            <span x-text="String(days).padStart(2, '0')"></span>d: 
                                            <span x-text="String(hours).padStart(2, '0')"></span>h: 
                                            <span x-text="String(minutes).padStart(2, '0')"></span>m: 
                                            <span x-text="String(seconds).padStart(2, '0')"></span>s
                                        </span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <h3 class="font-bold text-lg text-gray-900 mb-1">{{ $auction->year }} {{ $auction->make }} {{ $auction->model }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">Lot #{{ $auction->item_number ?? $auction->id }}</p>
                                    @php
                                        $highestBid = $auction->bids()->where('status', 'active')->orderByDesc('amount')->first();
                                        $currentBid = $highestBid ? (float)$highestBid->amount : (float)($auction->starting_price ?? 0);
                                    @endphp
                                    <p class="text-green-600 font-semibold mb-3">
                                        Current bid: {{ $currentBid > 0 ? '$' . number_format($currentBid, 2) : 'Start the bidding' }}
                                    </p>
                                    <p class="text-sm text-gray-500 mb-3">Location: {{ strtoupper($auction->island ?? 'N/A') }}</p>
                                    <div class="flex gap-2">
                                        <a href="{{ route('auction.show', $auction->id) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg font-semibold transition-colors">
                                            View details
                                        </a>
                                        @php
                                            $liked = $likedListingIds->contains($auction->id);
                                            $likesCount = $auction->likes_count ?? 0;
                                        @endphp
                                        <form action="{{ route('listing.watchlist', $auction->id) }}" method="POST">
                                            @csrf
                                            <button
                                                type="submit"
                                                class="js-like-toggle flex items-center gap-1.5 p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors {{ $liked ? 'text-red-500' : 'text-gray-600' }}"
                                                data-url="{{ route('listing.watchlist', $auction->id) }}"
                                                data-liked="{{ $liked ? '1' : '0' }}"
                                                data-auth="{{ Auth::check() ? '1' : '0' }}"
                                                data-unliked-class="text-gray-600"
                                                aria-label="Like listing">
                                                <span class="material-icons text-base">{{ $liked ? 'favorite' : 'favorite_border' }}</span>
                                                <span class="text-xs js-like-count">{{ $likesCount }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Navigation Arrows (only show if more than 4 items) -->
            @if($popularAuctions->count() > 4)
            <button @click="if (currentIndex > 0) currentIndex--" :disabled="currentIndex === 0" :class="currentIndex === 0 ? 'opacity-50 cursor-not-allowed' : ''" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 bg-white shadow-lg rounded-full p-3 hover:bg-gray-50 transition-all z-10">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="if (currentIndex < Math.floor((totalItems - itemsPerView) / 1)) currentIndex++" :disabled="currentIndex >= Math.floor((totalItems - itemsPerView) / 1)" :class="currentIndex >= Math.floor((totalItems - itemsPerView) / 1) ? 'opacity-50 cursor-not-allowed' : ''" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 bg-white shadow-lg rounded-full p-3 hover:bg-gray-50 transition-all z-10">
                <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            @endif
        </div>
    </div>
</section>

<!-- Vehicle Finder Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-8 font-heading text-center">Auction Car Finder</h2>
        
        <!-- Search Filters -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <form action="{{ route('Auction.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select name="vehicle_type" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Cars</option>
                    <option value="truck">Trucks</option>
                    <option value="suv">SUVs</option>
                    <option value="motorcycle">Motorcycles</option>
                </select>
                <select name="make" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Makes</option>
                    <!-- Populate with makes from database -->
                </select>
                <select name="year_from" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="1900">1900</option>
                    @for($year = date('Y'); $year >= 1900; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
                <select name="year_to" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}</option>
                    @for($year = date('Y'); $year >= 1900; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
                <div class="md:col-span-4 flex justify-center">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-12 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                        Search
                    </button>
                    <a href="{{ route('Auction.index') }}" class="ml-4 text-blue-600 hover:text-blue-800 font-semibold flex items-center">
                        Advanced Search →
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Results Grid (Marketplace + Auctions) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($marketplaceItems->take(8) as $item)
                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-xl transition-all">
                    @php
                        $img = $item->images->first();
                        $imgUrl = $img ? (str_contains($img->image_path, '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path)) : asset('images/placeholder-car.png');
                    @endphp
                    <img src="{{ $imgUrl }}" alt="{{ $item->make }} {{ $item->model }}" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-gray-900 mb-1">{{ $item->year }} {{ $item->make }} {{ $item->model }}</h3>
                        <p class="text-blue-600 font-semibold text-xl mb-2">${{ number_format($item->price ?? $item->buy_now_price ?? 0, 2) }}</p>
                        <a href="{{ route('listing.show', $item->id) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg font-semibold transition-colors">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-600">No vehicles found. Check back soon!</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Mini Register Form Section -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-2xl p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2 font-heading text-center">Register a New Account for FREE!</h2>
            <p class="text-gray-600 text-center mb-6">Create your account in seconds and start bidding today</p>
            
            <form action="{{ route('register') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="first_name" placeholder="First Name" required class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                    <input type="text" name="last_name" placeholder="Last Name" required class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                </div>
                <input type="tel" name="phone" placeholder="Phone Number" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                <input type="email" name="email" placeholder="Email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                <div class="flex items-center">
                    <input type="checkbox" id="terms" required class="mr-2">
                    <label for="terms" class="text-sm text-gray-700">I agree to the Terms and Conditions</label>
                </div>
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                    REGISTER NOW
                </button>
            </form>
        </div>
    </div>
</section>

<!-- 4 Photo Highlights Section (Why Use CayMark) -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-12 font-heading text-center">Why Use CayMark?</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Highlight 1 -->
            <div class="text-center">
                <div class="mb-4">
                    <img src="{{ asset('images/highlight-1.jpg') }}" alt="Secure Platform" class="w-full h-48 object-cover rounded-lg shadow-lg">
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Secure Platform</h3>
                <p class="text-gray-600">Your transactions are protected with industry-leading security</p>
            </div>
            <!-- Highlight 2 -->
            <div class="text-center">
                <div class="mb-4">
                    <img src="{{ asset('images/highlight-2.jpg') }}" alt="Wide Selection" class="w-full h-48 object-cover rounded-lg shadow-lg">
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Wide Selection</h3>
                <p class="text-gray-600">Browse thousands of vehicles from across The Bahamas</p>
            </div>
            <!-- Highlight 3 -->
            <div class="text-center">
                <div class="mb-4">
                    <img src="{{ asset('images/highlight-3.jpg') }}" alt="Easy Process" class="w-full h-48 object-cover rounded-lg shadow-lg">
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Easy Process</h3>
                <p class="text-gray-600">Simple bidding and buying process from start to finish</p>
            </div>
            <!-- Highlight 4 -->
            <div class="text-center">
                <div class="mb-4">
                    <img src="{{ asset('images/highlight-4.jpg') }}" alt="Island-Wide" class="w-full h-48 object-cover rounded-lg shadow-lg">
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Island-Wide</h3>
                <p class="text-gray-600">Connect with sellers and buyers across every island</p>
            </div>
        </div>
    </div>
</section>

<!-- What is CayMark Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 font-heading text-center">What is CayMark?</h2>
            <div class="bg-white rounded-lg shadow-lg p-8">
                <p class="text-lg text-gray-700 leading-relaxed mb-4">
                    <strong>CayMark Island Exchange & Auction House</strong> is The Bahamas' premier digital vehicle auction platform, dedicated to connecting buyers and sellers across every island. More than just an auction house, CayMark is redefining how The Bahamas buys, sells, and trades vehicles through a secure, transparent, and fully online marketplace.
                </p>
                <p class="text-lg text-gray-700 leading-relaxed mb-4">
                    Browse an extensive selection of cars, trucks, boats, and heavy equipment from sellers throughout the islands. Participate in real-time auctions or purchase instantly using our Buy Now option — all from one powerful digital platform.
                </p>
                <p class="text-lg text-gray-700 leading-relaxed">
                    <strong>Sign up today and experience the future of island trade.</strong>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Signup Section -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold mb-4 font-heading">Sign up for our newsletter</h2>
                    <p class="text-blue-100 text-lg mb-6">Get fresh updates on the newest listings and auctions straight to your inbox.</p>
                </div>
                <div class="bg-white rounded-lg shadow-2xl p-6">
                    <form action="#" method="POST" class="space-y-4">
                        @csrf
                        <input type="email" name="email" placeholder="Enter your email address" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg">
                            Subscribe Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
