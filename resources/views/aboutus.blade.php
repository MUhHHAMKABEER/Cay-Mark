@extends('layouts.welcome')

@section('content')
<style>
    .about-hero-gradient {
        background: linear-gradient(135deg, #0a2258 0%, #1e3a8a 30%, #312e81 70%, #1e40af 100%);
    }
    
    .floating-shapes {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    
    .shape-circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .shape-circle-1 {
        width: 300px;
        height: 300px;
        top: -100px;
        right: -50px;
        animation: float 20s infinite ease-in-out;
    }
    
    .shape-circle-2 {
        width: 200px;
        height: 200px;
        bottom: -50px;
        left: 10%;
        animation: float 15s infinite ease-in-out reverse;
    }
    
    .shape-circle-3 {
        width: 150px;
        height: 150px;
        top: 50%;
        right: 20%;
        animation: float 25s infinite ease-in-out;
    }
    
    @keyframes float {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
        }
        33% {
            transform: translate(30px, -30px) rotate(120deg);
        }
        66% {
            transform: translate(-20px, 20px) rotate(240deg);
        }
    }
    
    .feature-icon {
        width: 80px;
        height: 80px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .feature-card:hover .feature-icon {
        transform: scale(1.1) rotate(5deg);
        border-color: rgba(255, 255, 255, 0.5);
        background: rgba(255, 255, 255, 0.1);
    }
    
    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-5px);
    }
    
    .client-logo {
        filter: brightness(0) invert(1);
        opacity: 0.7;
        transition: all 0.3s ease;
    }
    
    .client-logo:hover {
        opacity: 1;
        transform: scale(1.1);
    }
    
    .cta-gradient {
        background: linear-gradient(135deg, #1e3a8a 0%, #312e81 50%, #1e40af 100%);
    }
    
    .wave-pattern {
        background-image: 
            radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
    }
</style>

<!-- Hero Header Section -->
<section class="relative min-h-[500px] flex items-center about-hero-gradient overflow-hidden">
    <div class="floating-shapes">
        <div class="shape-circle shape-circle-1"></div>
        <div class="shape-circle shape-circle-2"></div>
        <div class="shape-circle shape-circle-3"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left: Title and Tagline -->
            <div>
                <h1 class="text-5xl md:text-6xl font-extrabold text-white mb-6 font-heading leading-tight">
                    About Us
                </h1>
                <p class="text-2xl md:text-3xl text-blue-200 font-semibold mb-8">
                    We believe that technology can change the world.
                </p>
            </div>
            
            <!-- Right: Description -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-white/20">
                <p class="text-white text-lg leading-relaxed">
                    CayMark is The Bahamas' first digital auction platform designed to bring confidence, transparency, and simplicity to the way our islands buy and sell vehicles. Built with the needs of Bahamian buyers and sellers in mind, CayMark blends modern technology with a straightforward, user-friendly experience that makes online auctions accessible to everyone from first-time buyers to seasoned dealerships.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Features/Services Section -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Expertise -->
            <div class="feature-card text-center group">
                <div class="feature-icon mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Expertise</h3>
                <p class="text-gray-600 leading-relaxed">
                    Our team of experienced experts have the knowledge and expertise to deliver innovative IT solutions that transform how Bahamians buy and sell vehicles.
                </p>
            </div>
            
            <!-- Technology -->
            <div class="feature-card text-center group">
                <div class="feature-icon mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Technology</h3>
                <p class="text-gray-600 leading-relaxed">
                    We stay up to date with the latest trends and technologies in the digital marketplace, so you can get the most advanced auction solutions available.
                </p>
            </div>
            
            <!-- Solutions -->
            <div class="feature-card text-center group">
                <div class="feature-icon mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Solutions</h3>
                <p class="text-gray-600 leading-relaxed">
                    We take a personalized approach to every project, working closely with you to understand your business and create solutions that fit your needs.
                </p>
            </div>
            
            <!-- Results -->
            <div class="feature-card text-center group">
                <div class="feature-icon mx-auto mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Results</h3>
                <p class="text-gray-600 leading-relaxed">
                    We've helped buyers and sellers of all sizes across The Bahamas achieve their goals with our innovative auction platform and trusted marketplace.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-16 bg-gradient-to-br from-blue-900 via-indigo-900 to-purple-900 relative overflow-hidden">
    <div class="wave-pattern absolute inset-0"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="bg-white/10 backdrop-blur-md rounded-3xl p-12 border border-white/20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="stat-card rounded-2xl p-8">
                    <div class="text-5xl md:text-6xl font-extrabold text-white mb-3">500+</div>
                    <div class="text-white/90 text-lg font-semibold">Successful Auctions</div>
                </div>
                <div class="stat-card rounded-2xl p-8">
                    <div class="text-5xl md:text-6xl font-extrabold text-white mb-3">98%</div>
                    <div class="text-white/90 text-lg font-semibold">Satisfied Users</div>
                </div>
                <div class="stat-card rounded-2xl p-8">
                    <div class="text-5xl md:text-6xl font-extrabold text-white mb-3">35+</div>
                    <div class="text-white/90 text-lg font-semibold">Bahamas Islands</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 font-heading">Our Mission</h2>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-600 to-indigo-600 mx-auto mb-8"></div>
            </div>
            
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed space-y-6">
                <p class="text-xl">
                    Our mission is simple: create a safer, smarter, and more reliable marketplace where you can discover quality vehicles, place real-time bids, and secure great deals without stress or uncertainty. Every feature on CayMark is built with purpose. We focus on clarity, fairness, and trust—the essentials of a marketplace that puts users first.
                </p>
                
                <p class="text-xl">
                    We believe in giving buyers the information they need to make confident decisions, and giving sellers the tools to reach serious, ready-to-act customers across the islands. Whether you're upgrading your vehicle, searching for something specific, or simply exploring what's available, CayMark is here to make the process seamless from start to finish.
                </p>
                
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 border-l-4 border-blue-600 mt-8">
                    <p class="text-xl font-semibold text-gray-900">
                        Driven by innovation and shaped by local insight, CayMark is more than a platform—it's the evolution of how Bahamians exchange vehicles. Real auctions. Real people. Real opportunities.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team/Community Section -->
<section class="py-20 bg-gradient-to-b from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 font-heading">Built for The Bahamas</h2>
                <div class="w-24 h-1 bg-gradient-to-r from-blue-600 to-indigo-600 mb-8"></div>
                <p class="text-lg text-gray-700 leading-relaxed mb-6">
                    CayMark is the Bahamas' first digital auction platform designed to bring confidence, transparency, and simplicity to the way our islands buy and sell vehicles. Built with the needs of Bahamian buyers and sellers in mind, CayMark blends modern technology with a straightforward, user-friendly experience.
                </p>
                <p class="text-lg text-gray-700 leading-relaxed">
                    Our platform makes online auctions accessible to everyone from first-time buyers to seasoned dealerships, creating a community-driven marketplace that connects people across every island.
                </p>
            </div>
            <div class="relative">
                <div class="rounded-2xl overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop" 
                         alt="CayMark Team" 
                         class="w-full h-auto object-cover">
                </div>
                <div class="absolute -bottom-6 -right-6 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl p-6 shadow-xl">
                    <div class="text-white">
                        <div class="text-3xl font-bold mb-1">Trusted</div>
                        <div class="text-lg">by Bahamians</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Client/Partner Logos Section -->
<section class="py-16 bg-white border-t border-gray-200">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Trusted Platform</h3>
            <p class="text-gray-600">Used by buyers and sellers across The Bahamas</p>
        </div>
        <div class="flex flex-wrap justify-center items-center gap-12 opacity-60">
            <!-- Placeholder for partner/client logos -->
            <div class="text-2xl font-bold text-gray-400">BAHAMAS</div>
            <div class="text-2xl font-bold text-gray-400">AUTOMOTIVE</div>
            <div class="text-2xl font-bold text-gray-400">MARINE</div>
            <div class="text-2xl font-bold text-gray-400">DEALERS</div>
            <div class="text-2xl font-bold text-gray-400">INDIVIDUALS</div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-20 cta-gradient relative overflow-hidden">
    <div class="wave-pattern absolute inset-0"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-block w-32 h-32 bg-white/10 backdrop-blur-md rounded-full mb-8 flex items-center justify-center border border-white/20">
                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6 font-heading">
                Ready to Get Started?
            </h2>
            <p class="text-xl text-blue-100 mb-10 leading-relaxed">
                Welcome to the future of buying and selling in The Bahamas.<br>
                Welcome to CayMark.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center justify-center bg-white text-blue-600 font-bold py-4 px-8 rounded-xl hover:bg-blue-50 transition-all transform hover:scale-105 shadow-xl">
                    Get Started Free
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
                <a href="{{ route('Auction.index') }}" 
                   class="inline-flex items-center justify-center bg-white/10 backdrop-blur-md text-white font-bold py-4 px-8 rounded-xl border border-white/30 hover:bg-white/20 transition-all transform hover:scale-105">
                    Browse Auctions
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
