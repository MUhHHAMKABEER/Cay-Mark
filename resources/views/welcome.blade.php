@extends('layouts.welcome')

@section('content')
@php
    // Get top 8 popular auctions (by views/clicks or default to latest)
    $popularAuctions = \App\Models\Listing::with('images')
        ->where('listing_method', 'auction')
        ->where('listing_state', 'active')
        ->where('status', 'approved')
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
@endphp

<style>
    /* Modern Homepage Styles with Character */
    .hero-gradient-overlay {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.75) 0%, rgba(30, 58, 138, 0.65) 50%, rgba(0, 0, 0, 0.5) 100%);
    }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    
    .auction-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(229, 231, 235, 0.8);
        position: relative;
    }
    
    .auction-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, transparent 50%);
        opacity: 0;
        transition: opacity 0.4s;
        z-index: 0;
        border-radius: 1rem;
    }
    
    .auction-card:hover::before {
        opacity: 1;
    }
    
    .auction-card:hover {
        transform: translateY(-12px) scale(1.03);
        box-shadow: 0 25px 50px rgba(59, 130, 246, 0.25);
        border-color: rgba(59, 130, 246, 0.5);
    }
    
    .countdown-badge {
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.9) 0%, rgba(30, 58, 138, 0.9) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .section-title {
        position: relative;
        display: inline-block;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -12px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 5px;
        background: linear-gradient(90deg, #3b82f6, #2563eb, #1e40af);
        border-radius: 3px;
        animation: title-underline 3s ease-in-out infinite;
    }
    
    @keyframes title-underline {
        0%, 100% { width: 80px; }
        50% { width: 120px; }
    }
    
    .highlight-card {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
    }
    
    .highlight-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.6s;
        z-index: 1;
    }
    
    .highlight-card:hover::before {
        left: 100%;
    }
    
    .highlight-card::after {
        content: '';
        position: absolute;
        inset: -2px;
        background: linear-gradient(135deg, #3b82f6, #2563eb, #1e40af);
        border-radius: 1rem;
        opacity: 0;
        transition: opacity 0.5s;
        z-index: -1;
    }
    
    .highlight-card:hover::after {
        opacity: 1;
    }
    
    .highlight-card:hover {
        transform: translateY(-16px) scale(1.02);
        box-shadow: 0 30px 60px rgba(59, 130, 246, 0.3);
        border-color: transparent;
    }
    
    .btn-modern {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .btn-modern::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .btn-modern:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }
    
    .search-box-modern {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.98) 100%);
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        border: 2px solid rgba(229, 231, 235, 0.5);
        position: relative;
        overflow: hidden;
    }
    
    .search-box-modern::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(59, 130, 246, 0.05), transparent);
        animation: search-shimmer 3s infinite;
    }
    
    @keyframes search-shimmer {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }
    
    .vehicle-card {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .vehicle-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(180deg, transparent 0%, rgba(59, 130, 246, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.4s;
        z-index: 1;
    }
    
    .vehicle-card:hover::after {
        opacity: 1;
    }
    
    .vehicle-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 25px 50px rgba(59, 130, 246, 0.2);
    }
    
    .register-form-modern {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.98) 100%);
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2);
        border: 2px solid rgba(229, 231, 235, 0.5);
        position: relative;
        overflow: hidden;
    }
    
    .register-form-modern::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(135deg, #3b82f6, #2563eb, #1e40af, #3b82f6);
        background-size: 300% 300%;
        border-radius: 1.5rem;
        opacity: 0;
        transition: opacity 0.5s;
        z-index: -1;
        animation: border-glow 3s ease infinite;
    }
    
    @keyframes border-glow {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .register-form-modern:hover::before {
        opacity: 0.3;
    }
    
    .newsletter-modern {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.98) 100%);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }
    
    .info-card-modern {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 250, 251, 0.98) 100%);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        border: 2px solid rgba(229, 231, 235, 0.5);
        position: relative;
    }
    
    .pulse-dot {
        animation: pulse-dot 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse-dot {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.7;
            transform: scale(1.2);
        }
    }
    
    .gradient-text {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1e40af 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: gradient-shift 3s ease infinite;
        background-size: 200% 200%;
    }
    
    @keyframes gradient-shift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .section-bg-pattern {
        background-image: 
            radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(37, 99, 235, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 50% 20%, rgba(30, 64, 175, 0.03) 0%, transparent 50%);
        position: relative;
    }
    
    /* Floating Elements */
    .floating-shape {
        position: absolute;
        border-radius: 50%;
        opacity: 0.1;
        animation: float 20s infinite ease-in-out;
    }
    
    @keyframes float {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
        }
        25% {
            transform: translate(30px, -30px) rotate(90deg);
        }
        50% {
            transform: translate(-20px, 20px) rotate(180deg);
        }
        75% {
            transform: translate(20px, 30px) rotate(270deg);
        }
    }
    
    .floating-shape-1 {
        width: 200px;
        height: 200px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        top: 10%;
        left: 5%;
        animation-delay: 0s;
    }
    
    .floating-shape-2 {
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, #2563eb, #1e40af);
        top: 60%;
        right: 10%;
        animation-delay: 2s;
    }
    
    .floating-shape-3 {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #1e40af, #3b82f6);
        bottom: 20%;
        left: 15%;
        animation-delay: 4s;
    }
    
    /* Animated Background Particles */
    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: rgba(59, 130, 246, 0.3);
        border-radius: 50%;
        animation: particle-float 15s infinite ease-in-out;
    }
    
    @keyframes particle-float {
        0%, 100% {
            transform: translateY(0) translateX(0);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-100vh) translateX(50px);
            opacity: 0;
        }
    }
    
    /* Text Animations */
    .fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .fade-in-delay-1 {
        animation: fadeInUp 0.8s ease-out 0.2s both;
    }
    
    .fade-in-delay-2 {
        animation: fadeInUp 0.8s ease-out 0.4s both;
    }
    
    .fade-in-delay-3 {
        animation: fadeInUp 0.8s ease-out 0.6s both;
    }
    
    /* Glow Effects */
    .glow-blue {
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
    }
    
    .glow-blue:hover {
        box-shadow: 0 0 30px rgba(59, 130, 246, 0.8);
    }
    
    /* Section Dividers */
    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.3), transparent);
        margin: 60px 0;
    }
    
    /* Enhanced Card Hover */
    .card-lift {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .card-lift:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
</style>

<!-- Enhanced Hero Banner Section -->
<section class="relative h-[600px] overflow-hidden" x-data="{ currentSlide: 0, slides: 3 }">
    <!-- Floating Shapes -->
    <div class="floating-shape floating-shape-1"></div>
    <div class="floating-shape floating-shape-2"></div>
    <div class="floating-shape floating-shape-3"></div>
    
    <!-- Animated Particles -->
    <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
    <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
    <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
    <div class="particle" style="left: 70%; animation-delay: 6s;"></div>
    <div class="particle" style="left: 90%; animation-delay: 8s;"></div>
    
    <div class="absolute inset-0">
        <!-- Slide 1 -->
        <div x-show="currentSlide === 0" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 scale-110" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-110" class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/banner-1.jpg') }}');">
            <div class="absolute inset-0 hero-gradient-overlay"></div>
        </div>
        <!-- Slide 2 -->
        <div x-show="currentSlide === 1" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 scale-110" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-110" class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/banner-2.jpg') }}');">
            <div class="absolute inset-0 hero-gradient-overlay"></div>
        </div>
        <!-- Slide 3 -->
        <div x-show="currentSlide === 2" x-transition:enter="transition ease-out duration-700" x-transition:enter-start="opacity-0 scale-110" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-700" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-110" class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/banner-3.jpg') }}');">
            <div class="absolute inset-0 hero-gradient-overlay"></div>
        </div>
    </div>
    
    <!-- Enhanced Banner Content -->
    <div class="relative z-10 h-full flex items-center">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl text-white fade-in-up">
                <div class="inline-flex items-center glass-card rounded-full px-5 py-2.5 mb-6 border border-white/20 fade-in-delay-1 glow-blue">
                    <span class="w-2.5 h-2.5 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full mr-3 pulse-dot"></span>
                    <span class="text-sm font-semibold text-gray-900">LIVE AUCTIONS HAPPENING NOW</span>
                </div>
                <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight font-heading drop-shadow-2xl fade-in-delay-1">
                    INTRODUCING<br>
                    <span class="text-blue-300 relative inline-block">
                        <span class="relative z-10">WHOLESALE AUCTIONS</span>
                        <span class="absolute bottom-0 left-0 right-0 h-3 bg-gradient-to-r from-blue-400/50 to-transparent blur-sm"></span>
                    </span>
                </h1>
                <p class="text-xl md:text-2xl mb-10 text-blue-100 drop-shadow-lg leading-relaxed font-medium fade-in-delay-2">
                    (Including Bank-Repo Vehicles)<br>
                    FLEET, FINANCE & COPART SELECT VEHICLES.
                </p>
                <div class="fade-in-delay-3">
                    <a href="{{ route('Auction.index') }}" class="inline-flex items-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-10 rounded-xl transition-all transform hover:scale-105 shadow-2xl btn-modern relative z-10 glow-blue">
                        <span class="relative z-10">View Inventory</span>
                        <svg class="w-5 h-5 ml-3 relative z-10 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Enhanced Carousel Controls -->
    <button @click="currentSlide = (currentSlide - 1 + slides) % slides" class="absolute left-6 top-1/2 -translate-y-1/2 z-20 bg-white/90 backdrop-blur-md text-gray-800 hover:text-blue-600 p-4 rounded-full transition-all hover:scale-110 shadow-2xl border-2 border-white/50 hover:border-blue-500">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>
    <button @click="currentSlide = (currentSlide + 1) % slides" class="absolute right-6 top-1/2 -translate-y-1/2 z-20 bg-white/90 backdrop-blur-md text-gray-800 hover:text-blue-600 p-4 rounded-full transition-all hover:scale-110 shadow-2xl border-2 border-white/50 hover:border-blue-500">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
        </svg>
    </button>
    
    <!-- Carousel Indicators -->
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex space-x-2">
        <button @click="currentSlide = 0" :class="currentSlide === 0 ? 'bg-white w-8' : 'bg-white/40 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
        <button @click="currentSlide = 1" :class="currentSlide === 1 ? 'bg-white w-8' : 'bg-white/40 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
        <button @click="currentSlide = 2" :class="currentSlide === 2 ? 'bg-white w-8' : 'bg-white/40 w-2'" class="h-2 rounded-full transition-all duration-300"></button>
    </div>
    
    <!-- Auto-rotate script -->
    <script>
        setInterval(() => {
            if (document.querySelector('[x-data*="currentSlide"]')) {
                const component = Alpine.$data(document.querySelector('[x-data*="currentSlide"]'));
                if (component) component.currentSlide = (component.currentSlide + 1) % component.slides;
            }
        }, 6000);
        
        // Countdown Timer Function for Homepage
        function updateHomepageCountdowns() {
            document.querySelectorAll('[id^="countdown-home-"]').forEach(element => {
                const listingId = element.id.replace('countdown-home-', '');
                const endTime = new Date(element.getAttribute('data-end-time'));
                const now = new Date();
                const diff = Math.max(0, Math.floor((endTime - now) / 1000));
                
                // Hide LIVE badge if expired
                const liveBadge = document.getElementById('live-badge-' + listingId);
                if (liveBadge) {
                    if (diff <= 0) {
                        liveBadge.style.display = 'none';
                    } else {
                        liveBadge.style.display = 'flex';
                    }
                }
                
                if (diff <= 0) {
                    element.innerHTML = '<div class="bg-gray-500/90 backdrop-blur-md text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-xl border border-gray-400/30">Auction Ended</div>';
                    return;
                }
                
                const days = Math.floor(diff / 86400);
                const hours = Math.floor((diff % 86400) / 3600);
                const minutes = Math.floor((diff % 3600) / 60);
                const seconds = diff % 60;
                
                const daysEl = document.getElementById('days-' + listingId);
                const hoursEl = document.getElementById('hours-' + listingId);
                const minutesEl = document.getElementById('minutes-' + listingId);
                const secondsEl = document.getElementById('seconds-' + listingId);
                
                if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
                if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
                if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
                if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
            });
        }
        
        // Update countdown timers every second
        setInterval(updateHomepageCountdowns, 1000);
        updateHomepageCountdowns(); // Initial call
    </script>
</section>

<!-- Enhanced Popular Car Auctions Section -->
<section class="py-20 bg-gradient-to-b from-white to-gray-50 section-bg-pattern relative overflow-hidden">
    <!-- Floating decorative elements -->
    <div class="absolute top-20 right-10 w-32 h-32 bg-blue-100 rounded-full opacity-20 blur-3xl"></div>
    <div class="absolute bottom-20 left-10 w-40 h-40 bg-indigo-100 rounded-full opacity-20 blur-3xl"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12 fade-in-up">
            <div>
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 font-heading section-title mb-4">
                    <span class="gradient-text">Popular Car Auctions</span>
                </h2>
                <p class="text-gray-600 text-lg mt-4">Discover the most sought-after vehicles</p>
            </div>
            <a href="{{ route('Auction.index') }}" class="mt-4 md:mt-0 inline-flex items-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-xl transition-all transform hover:scale-105 shadow-lg group glow-blue">
                View All
                <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
        
        <!-- Enhanced Carousel Container -->
        <div class="relative" x-data="{ currentIndex: 0, itemsPerView: 4, totalItems: {{ $popularAuctions->count() }} }">
            <div class="overflow-hidden rounded-2xl">
                <div class="flex transition-transform duration-700 ease-out" :style="`transform: translateX(-${currentIndex * (100 / itemsPerView)}%)`">
                    @foreach($popularAuctions as $auction)
                        <div class="w-full md:w-1/2 lg:w-1/4 flex-shrink-0 px-3">
                            <div class="auction-card bg-white rounded-2xl overflow-hidden shadow-lg">
                                <div class="relative group">
                                    @php
                                        $img = $auction->images->first();
                                        $imgUrl = $img ? (str_contains($img->image_path, '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path)) : asset('images/placeholder-car.png');
                                    @endphp
                                    <div class="overflow-hidden">
                                        <img src="{{ $imgUrl }}" alt="{{ $auction->make }} {{ $auction->model }}" class="w-full h-56 object-cover transform group-hover:scale-110 transition-transform duration-500">
                                    </div>
                                    <!-- Enhanced Countdown Timer -->
                                    @php
                                        // Calculate end date from database
                                        if ($auction->auction_end_time) {
                                            $endDate = \Carbon\Carbon::parse($auction->auction_end_time);
                                        } elseif ($auction->auction_start_time) {
                                            $endDate = \Carbon\Carbon::parse($auction->auction_start_time)->addDays($auction->auction_duration ?? 7);
                                        } else {
                                            $endDate = \Carbon\Carbon::parse($auction->created_at)->addDays($auction->auction_duration ?? 7);
                                        }
                                        
                                        $now = \Carbon\Carbon::now();
                                        $isExpired = $now->greaterThanOrEqualTo($endDate);
                                        $secondsRemaining = $isExpired ? 0 : $now->diffInSeconds($endDate, false);
                                    @endphp
                                    @if(!$isExpired)
                                    <!-- Live Badge - Only show if auction is active -->
                                    <div class="absolute top-3 right-3 bg-gradient-to-r from-red-500 to-red-600 text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-lg flex items-center" 
                                         id="live-badge-{{ $auction->id }}" 
                                         data-end-time="{{ $endDate->toIso8601String() }}">
                                        <span class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></span>
                                        LIVE
                                    </div>
                                    <!-- Modern Countdown Timer -->
                                    <div class="absolute bottom-3 left-3 bg-gradient-to-br from-blue-600/95 to-indigo-700/95 backdrop-blur-md text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-2xl border border-white/20" 
                                         id="countdown-home-{{ $auction->id }}" 
                                         data-end-time="{{ $endDate->toIso8601String() }}">
                                        <div class="flex items-center space-x-1.5">
                                            <span class="bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-lg font-mono text-xs" id="days-{{ $auction->id }}">00</span>
                                            <span class="text-white/70">:</span>
                                            <span class="bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-lg font-mono text-xs" id="hours-{{ $auction->id }}">00</span>
                                            <span class="text-white/70">:</span>
                                            <span class="bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-lg font-mono text-xs" id="minutes-{{ $auction->id }}">00</span>
                                            <span class="text-white/70">:</span>
                                            <span class="bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-lg font-mono text-xs" id="seconds-{{ $auction->id }}">00</span>
                                        </div>
                                    </div>
                                    @else
                                    <div class="absolute bottom-3 left-3 bg-gray-500/90 backdrop-blur-md text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-xl border border-gray-400/30">
                                        Auction Ended
                                    </div>
                                    @endif
                                </div>
                                <div class="p-5">
                                    <h3 class="font-bold text-xl text-gray-900 mb-2 line-clamp-1">{{ $auction->year }} {{ $auction->make }} {{ $auction->model }}</h3>
                                    <p class="text-sm text-gray-500 mb-3 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        Lot #{{ $auction->item_number ?? $auction->id }}
                                    </p>
                                    @php
                                        $highestBid = $auction->bids()->where('status', 'active')->orderByDesc('amount')->first();
                                        $currentBid = $highestBid ? (float)$highestBid->amount : (float)($auction->starting_price ?? 0);
                                    @endphp
                                    <div class="mb-4">
                                        <p class="text-xs text-gray-500 mb-1">Current Bid</p>
                                        <p class="text-2xl font-bold text-green-600">
                                            {{ $currentBid > 0 ? '$' . number_format($currentBid, 2) : 'Start Bidding' }}
                                        </p>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-4 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ strtoupper($auction->island ?? 'N/A') }}
                                    </p>
                                    <div class="flex gap-2">
                                        <a href="{{ route('auction.show', $auction->getSlugOrGenerate()) }}" class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-center py-3 px-4 rounded-xl font-bold transition-all transform hover:scale-105 shadow-lg">
                                            View Details
                                        </a>
                                        @auth
                                        <button class="p-3 border-2 border-gray-200 hover:border-blue-500 rounded-xl hover:bg-blue-50 transition-all group">
                                            <svg class="w-5 h-5 text-gray-600 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                        </button>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Enhanced Navigation Arrows -->
            @if($popularAuctions->count() > 4)
            <button @click="if (currentIndex > 0) currentIndex--" :disabled="currentIndex === 0" :class="currentIndex === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:scale-110 hover:text-blue-600'" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-6 bg-white shadow-2xl rounded-full p-4 hover:bg-blue-50 transition-all z-10 border-2 border-gray-200 hover:border-blue-500">
                <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="if (currentIndex < Math.floor((totalItems - itemsPerView) / 1)) currentIndex++" :disabled="currentIndex >= Math.floor((totalItems - itemsPerView) / 1)" :class="currentIndex >= Math.floor((totalItems - itemsPerView) / 1) ? 'opacity-30 cursor-not-allowed' : 'hover:scale-110 hover:text-blue-600'" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-6 bg-white shadow-2xl rounded-full p-4 hover:bg-blue-50 transition-all z-10 border-2 border-gray-200 hover:border-blue-500">
                <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            @endif
        </div>
    </div>
</section>

<!-- Enhanced Vehicle Finder Section -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white section-bg-pattern relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute top-10 left-20 w-24 h-24 bg-blue-200 rounded-full opacity-10 blur-2xl"></div>
    <div class="absolute bottom-10 right-20 w-32 h-32 bg-indigo-200 rounded-full opacity-10 blur-2xl"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-12 fade-in-up">
            <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 font-heading section-title mb-4 inline-block">
                <span class="gradient-text">Auction Car Finder</span>
            </h2>
            <p class="text-gray-600 text-lg mt-6">Find your perfect vehicle with our advanced search</p>
        </div>
        
        <!-- Enhanced Search Filters -->
        <div class="search-box-modern rounded-2xl p-8 mb-12">
            <form action="{{ route('Auction.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Vehicle Type</label>
                    <select name="vehicle_type" class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white">
                        <option value="">All Types</option>
                        <option value="car">Cars</option>
                        <option value="truck">Trucks</option>
                        <option value="suv">SUVs</option>
                        <option value="motorcycle">Motorcycles</option>
                    </select>
                </div>
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Make</label>
                    <select name="make" class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white">
                        <option value="">All Makes</option>
                    </select>
                </div>
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Year From</label>
                    <select name="year_from" class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white">
                        <option value="1900">1900</option>
                        @for($year = date('Y'); $year >= 1900; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Year To</label>
                    <select name="year_to" class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white">
                        <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}</option>
                        @for($year = date('Y'); $year >= 1900; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="md:col-span-4 flex flex-col sm:flex-row justify-center items-center gap-4 mt-4">
                    <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-12 rounded-xl transition-all transform hover:scale-105 shadow-xl btn-modern relative">
                        <span class="relative z-10 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Search Vehicles
                        </span>
                    </button>
                    <a href="{{ route('Auction.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center group">
                        Advanced Search
                        <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Enhanced Results Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($marketplaceItems->take(8) as $item)
                <div class="vehicle-card bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                    <div class="relative group overflow-hidden">
                        @php
                            $img = $item->images->first();
                            $imgUrl = $img ? (str_contains($img->image_path, '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path)) : asset('images/placeholder-car.png');
                        @endphp
                        <img src="{{ $imgUrl }}" alt="{{ $item->make }} {{ $item->model }}" class="w-full h-48 object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-bold text-gray-900 shadow-lg">
                            BUY NOW
                        </div>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-xl text-gray-900 mb-2 line-clamp-1">{{ $item->year }} {{ $item->make }} {{ $item->model }}</h3>
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-1">Price</p>
                            <p class="text-2xl font-bold gradient-text">${{ number_format($item->price ?? $item->buy_now_price ?? 0, 2) }}</p>
                        </div>
                        <a href="{{ route('listing.show', $item->id) }}" class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-center py-3 px-4 rounded-xl font-bold transition-all transform hover:scale-105 shadow-lg">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-600 text-lg">No vehicles found. Check back soon!</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Enhanced Mini Register Form Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
    <!-- Floating shapes -->
    <div class="absolute top-20 left-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-20 right-10 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-3xl mx-auto register-form-modern rounded-3xl p-10 fade-in-up">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl mb-4 shadow-lg glow-blue transform hover:scale-110 transition-transform">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <h2 class="text-4xl font-extrabold text-gray-900 font-heading mb-3">
                    <span class="gradient-text">Register a New Account for FREE!</span>
                </h2>
                <p class="text-gray-600 text-lg">Create your account in seconds and start bidding today</p>
            </div>
            
            <form action="{{ route('register') }}" method="GET" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" placeholder="Enter your first name" required class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" placeholder="Enter your last name" required class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" placeholder="Enter your phone number" required class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email" required class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900">
                </div>
                <div class="flex items-start">
                    <input type="checkbox" id="terms" required class="mt-1 mr-3 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="terms" class="text-sm text-gray-700">I agree to the <a href="#" class="text-blue-600 hover:underline font-semibold">Terms and Conditions</a></label>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-bold py-4 px-6 rounded-xl transition-all transform hover:scale-105 shadow-2xl btn-modern relative">
                    <span class="relative z-10 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        REGISTER NOW
                    </span>
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Enhanced 4 Photo Highlights Section -->
<section class="py-20 bg-white relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-full h-full">
        <div class="absolute top-20 left-10 w-64 h-64 bg-blue-50 rounded-full opacity-30 blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-72 h-72 bg-indigo-50 rounded-full opacity-30 blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center mb-16 fade-in-up">
            <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 font-heading section-title mb-4 inline-block">
                <span class="gradient-text">Why Use CayMark?</span>
            </h2>
            <p class="text-gray-600 text-lg mt-6">Experience the future of vehicle trading in The Bahamas</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Highlight 1 -->
            <div class="highlight-card bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="{{ asset('images/highlight-1.jpg') }}" alt="Secure Platform" class="w-full h-56 object-cover transform hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-white/90 backdrop-blur-sm rounded-xl mb-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Secure Platform</h3>
                    <p class="text-gray-600 leading-relaxed">Your transactions are protected with industry-leading security and encryption</p>
                </div>
            </div>
            <!-- Highlight 2 -->
            <div class="highlight-card bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="{{ asset('images/highlight-2.jpg') }}" alt="Wide Selection" class="w-full h-56 object-cover transform hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-white/90 backdrop-blur-sm rounded-xl mb-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Wide Selection</h3>
                    <p class="text-gray-600 leading-relaxed">Browse thousands of vehicles from across The Bahamas islands</p>
                </div>
            </div>
            <!-- Highlight 3 -->
            <div class="highlight-card bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="{{ asset('images/highlight-3.jpg') }}" alt="Easy Process" class="w-full h-56 object-cover transform hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-white/90 backdrop-blur-sm rounded-xl mb-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Easy Process</h3>
                    <p class="text-gray-600 leading-relaxed">Simple bidding and buying process from start to finish</p>
                </div>
            </div>
            <!-- Highlight 4 -->
            <div class="highlight-card bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                <div class="relative overflow-hidden">
                    <img src="{{ asset('images/highlight-4.jpg') }}" alt="Island-Wide" class="w-full h-56 object-cover transform hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-white/90 backdrop-blur-sm rounded-xl mb-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Island-Wide</h3>
                    <p class="text-gray-600 leading-relaxed">Connect with sellers and buyers across every island</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced What is CayMark Section -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white section-bg-pattern relative overflow-hidden">
    <!-- Decorative elements -->
    <div class="absolute top-10 right-20 w-40 h-40 bg-blue-100 rounded-full opacity-20 blur-3xl"></div>
    <div class="absolute bottom-10 left-20 w-36 h-36 bg-indigo-100 rounded-full opacity-20 blur-3xl"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12 fade-in-up">
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 font-heading section-title mb-4 inline-block">
                    <span class="gradient-text">What is CayMark?</span>
                </h2>
            </div>
            <div class="info-card-modern rounded-3xl p-10 fade-in-delay-1 card-lift">
                <div class="prose prose-lg max-w-none">
                    <p class="text-xl text-gray-700 leading-relaxed mb-6">
                        <strong class="text-2xl gradient-text">CayMark Island Exchange & Auction House</strong> is The Bahamas' premier digital vehicle auction platform, dedicated to connecting buyers and sellers across every island. More than just an auction house, CayMark is redefining how The Bahamas buys, sells, and trades vehicles through a secure, transparent, and fully online marketplace.
                    </p>
                    <p class="text-xl text-gray-700 leading-relaxed mb-6">
                        Browse an extensive selection of cars, trucks, boats, and heavy equipment from sellers throughout the islands. Participate in real-time auctions or purchase instantly using our Buy Now option — all from one powerful digital platform.
                    </p>
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border-l-4 border-blue-600">
                        <p class="text-xl text-gray-800 leading-relaxed font-semibold">
                            <span class="text-blue-600">Sign up today</span> and experience the future of island trade.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced Newsletter Signup Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 relative overflow-hidden">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-20"></div>
    <!-- Floating decorative shapes -->
    <div class="absolute top-10 left-10 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
    <div class="absolute bottom-10 right-10 w-56 h-56 bg-white/5 rounded-full blur-3xl"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-5xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                <div class="fade-in-up">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl mb-6 glow-blue transform hover:scale-110 transition-transform">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-extrabold mb-6 font-heading text-white drop-shadow-lg">Sign up for our newsletter</h2>
                    <p class="text-blue-100 text-xl mb-6 leading-relaxed drop-shadow-md">Get fresh updates on the newest listings and auctions straight to your inbox. Never miss a great deal!</p>
                    <div class="flex items-center space-x-4 text-white/80">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Weekly updates</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Exclusive deals</span>
                        </div>
                    </div>
                </div>
                <div class="newsletter-modern rounded-2xl p-8 fade-in-delay-1 card-lift">
                    <form action="#" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" placeholder="Enter your email address" required class="w-full px-4 py-3.5 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-900 hover:border-blue-300">
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-6 rounded-xl transition-all transform hover:scale-105 shadow-xl btn-modern relative glow-blue">
                            <span class="relative z-10 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Subscribe Now
                            </span>
                        </button>
                        <p class="text-xs text-gray-500 text-center">We respect your privacy. Unsubscribe at any time.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
