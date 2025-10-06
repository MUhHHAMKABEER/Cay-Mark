@extends('layouts.welcome')

@section('content')
    <!-- Hero Section with Carousel -->
    <section class="relative bg-cover bg-center h-96 md:h-screen max-h-[700px] overflow-hidden">
        <!-- Carousel -->
        <div class="hero-carousel">
            <div class="carousel-slide active"
                style="background-image: url('https://images.unsplash.com/photo-1601362840469-51e4d8d58785?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80')">
            </div>
            <div class="carousel-slide"
                style="background-image: url('https://images.unsplash.com/photo-1544829099-b9a0c07fad1a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1472&q=80')">
            </div>
            <div class="carousel-slide"
                style="background-image: url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80')">
            </div>
        </div>

        <div class="absolute inset-0 hero-gradient"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/40 to-blue-600/20"></div>

        <div class="container mx-auto px-4 h-full flex items-center relative z-10">
            <div class="text-white max-w-2xl animate__animated animate__fadeIn">
                <div class="inline-flex items-center bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 mb-4 border border-white/20">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm font-medium">LIVE AUCTIONS HAPPENING NOW</span>
                </div>

                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight font-heading drop-shadow-lg">Premium Online Vehicle Auctions</h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100 drop-shadow-md">Discover unbeatable deals on 300,000+ vehicles. Join our 150+ weekly live auctions open to the public.</p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#"
                        class="group bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-8 rounded-full transition-all transform hover:scale-105 shadow-lg hover:shadow-xl text-center relative overflow-hidden">
                        <span class="relative z-10 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Join Live Auction Now
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-[100%] transition-transform duration-700"></div>
                    </a>
                    <a href="#"
                        class="group bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-bold py-4 px-8 rounded-full transition-all transform hover:scale-105 shadow-lg border border-white/20 hover:border-white/30 text-center relative overflow-hidden">
                        <span class="relative z-10 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            How It Works
                        </span>
                    </a>
                </div>

                <div class="flex items-center mt-8 text-blue-100">
                    <div class="flex items-center mr-6">
                        <svg class="w-5 h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>300,000+ Vehicles</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>150+ Weekly Auctions</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auction Finder Form (moved into flow, overlapping hero with negative margin and proper z-index) -->
        <div class="relative -mt-20 md:-mt-36 z-50">
            <div class="container mx-auto px-4 z-50">

            </div>
        </div>
    </section>

    <!-- Upcoming Auctions Section -->
    @php
    // Get only 4 upcoming auctions
    $auctionListings = \App\Models\Listing::with('images')
        ->where('listing_method', 'auction')
        ->where('listing_state','active')->where('status','approved')
        ->take(4)
        ->get();
    @endphp

    <!-- add top padding so the overlapping finder doesn't cover content -->
    <section class="py-20 bg-gradient-to-b from-gray-50 to-white mt-16 pt-24">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center mb-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2 font-heading">Upcoming Vehicle Auctions</h2>
                    <p class="text-gray-600">Don't miss out on these exclusive auction opportunities</p>
                </div>
                <a href="{{ route('Auction.index') }}" class="mt-4 md:mt-0 inline-flex items-center text-blue-600 hover:text-blue-800 font-medium transition-colors duration-300 group">
                    View All Upcoming Auctions
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($auctionListings as $auction)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover border border-gray-100 group">
                        <div class="relative overflow-hidden">
                            <div class="absolute top-4 left-4 z-10">
                                <span class="bg-gradient-to-r from-red-600 to-red-700 text-white text-xs font-bold px-3 py-2 rounded-full shadow-lg flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    LEAVE's SOON
                                </span>
                            </div>
                            @php
    $img = $auction->images[0] ?? null;
    $imgUrl = $img
        ? (str_contains($img->image_path, '/')
            ? asset($img->image_path)
            : asset('uploads/listings/' . $img->image_path))
        : asset('images/placeholder-car.png');
@endphp

<div class="h-48 overflow-hidden relative group">
    <img src="{{ $imgUrl }}"
         alt="listing image"
         class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">

    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
</div>

                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-3">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm font-medium text-blue-600">Auction</span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-700 transition-colors">{{ $auction->title ?? $auction->make . ' ' . $auction->model }}</h3>
                            <p class="text-gray-600 mb-4 line-clamp-2">{{ $auction->description ?? 'Virtual Auction Lane, multiple vehicles available' }}</p>
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Online
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Starts in 2 days
                                </div>
                            </div>
                            <a href="{{ route('auction.show', $auction->id) }}"
                                class="group w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-xl text-center transition-all transform hover:scale-105 duration-300 flex items-center justify-center shadow-md">
                                View Auction Details
                                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-12">
                        <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-600 mb-2">No Upcoming Auctions</h3>
                            <p class="text-gray-500">Check back later for new auction listings.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- What is CayMark Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center">
                <div class="lg:w-1/2 mb-10 lg:mb-0 lg:pr-10">
                    <div class="inline-flex items-center bg-blue-100 text-blue-800 rounded-full px-4 py-2 mb-4">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        About CayMark
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 font-heading">What is CayMark?</h2>
                    <p class="text-gray-600 text-lg mb-6 leading-relaxed">
                        CayMark is a premier online vehicle auction platform that connects buyers and sellers from
                        around the world. We provide a transparent, efficient, and secure marketplace for purchasing vehicles of all
                        types.
                    </p>
                    <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                        With our innovative technology and customer-focused approach, we've revolutionized the way
                        people buy and sell vehicles online, making the process simpler, faster, and more accessible to everyone.
                    </p>
                    <div class="flex flex-wrap gap-4 mb-8">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Secure Transactions</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">Global Marketplace</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">24/7 Support</span>
                        </div>
                    </div>
                    <a href="#"
                        class="inline-flex items-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-8 rounded-xl transition-all transform hover:scale-105 duration-300 shadow-md">
                        Learn More About Us
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
                <div class="lg:w-1/2">
                    <div class="relative">
                        <div class="rounded-2xl shadow-2xl overflow-hidden card-hover">
                            <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1073&q=80"
                                alt="CayMark Platform" class="w-full h-full object-cover">
                        </div>
                        <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-blue-600 rounded-2xl shadow-lg flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why CayMark Section -->
    <section class="py-20 gradient-bg text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 font-heading">Why Choose CayMark?</h2>
                <p class="text-blue-100 max-w-2xl mx-auto">Discover the advantages of using our platform for all your vehicle auction needs</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center group">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 h-full border border-white/20 transition-all duration-300 group-hover:bg-white/15 group-hover:scale-105">
                        <div class="bg-gradient-to-br from-white to-blue-100 text-blue-600 rounded-2xl h-20 w-20 flex items-center justify-center mx-auto mb-6 shadow-lg transition-transform duration-300 group-hover:scale-110">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 font-heading">Trusted Platform</h3>
                        <p class="text-blue-100 leading-relaxed">Secure transactions and verified sellers ensure peace of mind for all your purchases.</p>
                    </div>
                </div>

                <div class="text-center group">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 h-full border border-white/20 transition-all duration-300 group-hover:bg-white/15 group-hover:scale-105">
                        <div class="bg-gradient-to-br from-white to-blue-100 text-blue-600 rounded-2xl h-20 w-20 flex items-center justify-center mx-auto mb-6 shadow-lg transition-transform duration-300 group-hover:scale-110">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 font-heading">Wide Selection</h3>
                        <p class="text-blue-100 leading-relaxed">Access thousands of vehicles from various makes, models, and price ranges.</p>
                    </div>
                </div>

                <div class="text-center group">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 h-full border border-white/20 transition-all duration-300 group-hover:bg-white/15 group-hover:scale-105">
                        <div class="bg-gradient-to-br from-white to-blue-100 text-blue-600 rounded-2xl h-20 w-20 flex items-center justify-center mx-auto mb-6 shadow-lg transition-transform duration-300 group-hover:scale-110">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 font-heading">Competitive Pricing</h3>
                        <p class="text-blue-100 leading-relaxed">Our auction format ensures you get the best possible price for your desired vehicle.</p>
                    </div>
                </div>

                <div class="text-center group">
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 h-full border border-white/20 transition-all duration-300 group-hover:bg-white/15 group-hover:scale-105">
                        <div class="bg-gradient-to-br from-white to-blue-100 text-blue-600 rounded-2xl h-20 w-20 flex items-center justify-center mx-auto mb-6 shadow-lg transition-transform duration-300 group-hover:scale-110">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 font-heading">Expert Support</h3>
                        <p class="text-blue-100 leading-relaxed">Our team of professionals is available to assist you throughout the entire process.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Explore: Popular Vehicles Section -->
    @php
    use App\Models\Listing;

    // Get only 4 listings that have a buy_now_price
    $buyNowItems = Listing::with('images')->where('listing_method', 'buy_now')->where('listing_state','active')->where('status','approved')->take(4)->get();
    @endphp

    <section class="py-16 bg-gradient-to-b from-white to-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2 font-heading">Explore: Popular Vehicles</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Discover our most sought-after vehicles available for immediate purchase
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                @forelse($buyNowItems as $listing)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden card-hover border border-gray-100 group">
                        <div class="relative overflow-hidden">
                            <div class="absolute top-4 right-4 z-10">
                                <span class="bg-gradient-to-r from-green-600 to-green-700 text-white text-xs font-bold px-3 py-2 rounded-full shadow-lg flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    BUY NOW
                                </span>
                            </div>
                             @php
                                    $img = $listing->images->first();
                                    $imgUrl = $img
                                        ? (str_contains($img->image_path, '/')
                                            ? asset($img->image_path)
                                            : asset('uploads/listings/' . $img->image_path))
                                        : asset('images/placeholder-car.png');
                                @endphp
                                <img src="{{ $imgUrl }}" alt="listing image" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">

                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-700 transition-colors">{{ $listing->make }} {{ $listing->model }}</h3>
                            <p class="text-gray-600 mb-4 line-clamp-2">{{ $listing->description ?? 'Well-maintained vehicle ready for immediate purchase' }}</p>
                            <div class="flex justify-between items-center mb-6">
                                <span class="text-2xl font-bold text-blue-600">${{ number_format($listing->price, 2) }}</span>
                                <span class="text-sm text-gray-500 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $listing->odometer ?? 'N/A' }} miles
                                </span>
                            </div>
                            <a href="{{ route('listing.show', $listing->id) }}"
                               class="group w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-xl text-center transition-all transform hover:scale-105 duration-300 flex items-center justify-center shadow-md">
                                View Listing
                                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-12">
                        <div class="bg-white rounded-2xl shadow-lg p-8 max-w-md mx-auto">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-600 mb-2">No Vehicles Available</h3>
                            <p class="text-gray-500">Check back soon for new vehicle listings.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="text-center">
                <a href="{{ route('marketplace.index') }}"
                   class="inline-flex items-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-8 rounded-xl transition-all transform hover:scale-105 duration-300 shadow-md">
                    View All Vehicles
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- How to Get Started Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4 font-heading">How to Get Started</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Join thousands of satisfied customers in just three simple steps</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-16">
                <div class="text-center group">
                    <div class="relative mb-6">
                        <div
                            class="bg-gradient-to-br from-blue-100 to-blue-50 text-blue-600 rounded-2xl h-24 w-24 flex items-center justify-center font-bold text-4xl mx-auto shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl">
                            1
                        </div>
                        <div class="absolute -right-6 top-1/2 transform -translate-y-1/2 hidden md:block">
                            <svg class="w-12 h-12 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 font-heading">Choose Your Role</h3>
                    <p class="text-gray-600 leading-relaxed">Select whether you want to join as a buyer or seller on our platform.</p>
                </div>

                <div class="text-center group">
                    <div class="relative mb-6">
                        <div
                            class="bg-gradient-to-br from-blue-100 to-blue-50 text-blue-600 rounded-2xl h-24 w-24 flex items-center justify-center font-bold text-4xl mx-auto shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl">
                            2
                        </div>
                        <div class="absolute -right-6 top-1/2 transform -translate-y-1/2 hidden md:block">
                            <svg class="w-12 h-12 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 font-heading">Pick a Membership</h3>
                    <p class="text-gray-600 leading-relaxed">Select the membership level that best fits your needs and budget.</p>
                </div>

                <div class="text-center group">
                    <div class="mb-6">
                        <div
                            class="bg-gradient-to-br from-blue-100 to-blue-50 text-blue-600 rounded-2xl h-24 w-24 flex items-center justify-center font-bold text-4xl mx-auto shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:shadow-xl">
                            3
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 font-heading">Start Buying or Selling</h3>
                    <p class="text-gray-600 leading-relaxed">Begin browsing auctions or listing your vehicles immediately.</p>
                </div>
            </div>

            <div class="text-center">
                @if(!Auth::check())
                    <button id=""
                        class="group bg-gradient-to-r from-blue-600 to-blue-800 hover:from-blue-700 hover:to-blue-900 text-white font-bold py-4 px-12 rounded-xl transition-all transform hover:scale-105 shadow-lg hover:shadow-xl text-center text-xl relative overflow-hidden">
                        <a href="{{ route('register') }}">
                        <span class="relative z-10 flex items-center justify-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Register Now
                        </span></a>
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent transform -skew-x-12 -translate-x-full group-hover:translate-x-[100%] transition-transform duration-700"></div>
                    </button>
                @endif
            </div>
        </div>
    </section>
@endsection
