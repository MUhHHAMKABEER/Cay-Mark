<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'CayMark Island Exchange & Auction House')</title>

    <!-- Tailwind (CDN for quick use; replace with Vite/build in production) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Alpine (optional) -->
    <script src="https://unpkg.com/alpinejs" defer></script>

    <!-- Page / project styles -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }

        h1,
        h2,
        h3,
        h4,
        .font-heading {
            font-family: 'Montserrat', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
        }

        .hero-gradient {
            background: linear-gradient(to right, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.3) 100%);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .countdown-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1)
            }

            50% {
                transform: scale(1.05)
            }

            100% {
                transform: scale(1)
            }
        }

        .vehicle-icon {
            transition: all 0.3s ease;
        }

        .vehicle-icon:hover {
            transform: scale(1.1);
        }

        .hero-carousel {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }

        .carousel-slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            background-size: cover;
            background-position: center;
        }

        .carousel-slide.active {
            opacity: 1;
        }

        .search-bar {
            border-radius: 50px;
            padding: 12px 20px;
        }

        .main-menu {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .footer-gradient {
            background: linear-gradient(135deg, #0a2258 0%, #1e3a8a 50%, #2563eb 100%);
        }

        .footer-link {
            transition: all 0.3s ease;
        }

        .footer-link:hover {
            color: #93c5fd;
            transform: translateX(5px);
        }

        .social-icon {
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            transform: translateY(-3px);
        }

        /* small helpers for layout */
        .container {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        /* Enhanced Header Styles */
        .header-nav {
            background-color: #063466;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .header-nav.scrolled {
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
        }

        .nav-link {
            position: relative;
            transition: all 0.3s ease;
            padding: 8px 0;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #3b82f6;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link.active::after {
            width: 100%;
        }

        .user-dropdown {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: none;
            transform: translateY(10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .user-dropdown.show {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        .search-container {
            position: relative;
            transition: all 0.3s ease;
        }

        .search-container:focus-within {
            transform: scale(1.02);
        }

        .search-container input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .mobile-menu {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced Modern Footer Styles */
        .footer-section {
            background: linear-gradient(135deg, #111827 0%, #1e3a8a 50%, #312e81 100%);
            position: relative;
            overflow: hidden;
        }

        .footer-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .footer-link:hover {
            color: #ffffff;
            transform: translateX(4px);
        }

        .footer-column {
            position: relative;
        }

        .footer-heading {
            position: relative;
        }

        .social-icon-modern {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .social-icon-modern::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }

        .social-icon-modern:hover::before {
            width: 100%;
            height: 100%;
        }

        @media (max-width: 768px) {
            .footer-column {
                margin-bottom: 30px;
            }
        }

        /* Dropdown Styles */
        .dropdown-menu {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: none;
            transform: translateY(10px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .dropdown-menu.show {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        /* Vehicle Finder Styles */
        .vehicle-finder {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50" x-data="{ mobileMenuOpen: false, howItWorksOpen: false, servicesOpen: false }">
    {{-- Unified Header Component --}}
    @include('partials.unified-header')
    
    {{-- Legacy Header (commented out - using unified header above) --}}
    {{-- <header class="header-nav sticky top-0 z-50" id="mainHeader">
        <div class="container px-4 py-3 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center transition-transform hover:scale-105 duration-300">
                    <img alt="Caymark logo" class="h-20" src="{{ asset('img/Caymark Logo-01.png') }}" />
                </a>
            </div>

            <!-- User Actions -->
            <div class="hidden md:flex items-center space-x-6">
                <div class="search-container relative max-w-md w-full">
                    <input type="text" placeholder="Search cars, boats, or equipment..."
                        class="w-full rounded-full border border-gray-300 bg-white px-4 py-2 pr-12 text-sm text-gray-700 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all duration-300">
                    <button
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-600 transition-colors duration-300">
                        <span class="material-icons">search</span>
                    </button>
                </div>

                @guest
                <!-- Guest: Show Login & Register -->
                <a href="{{ route('login') }}"
                    class="nav-link text-white hover:text-blue-300 transition-colors flex items-center">
                    <span class="material-icons mr-1">person_outline</span> Login
                </a>

                <a href="{{ route('register') }}"
                    class="bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold py-2 px-6 rounded-full hover:from-blue-700 hover:to-blue-900 transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                    Register
                </a>
                @else
                <!-- Authenticated Buyer: Show Dropdown -->
                <div class="relative">
                    <button id="userMenuBtn"
                        class="flex items-center bg-gradient-to-r from-blue-600 to-blue-800 text-white font-semibold px-5 py-2 rounded-full shadow-md hover:shadow-lg hover:from-blue-700 hover:to-blue-900 transition-all duration-300 transform hover:scale-105">
                        <span class="material-icons mr-2">account_circle</span>
                        {{ Auth::user()->name }}
                        <span class="material-icons ml-2 text-sm transition-transform duration-300">expand_more</span>
                    </button>

                    <!-- Dropdown -->
                    <div id="userMenuDropdown"
                        class="user-dropdown absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg overflow-hidden border border-gray-100 z-50">
                        <a href="{{ route('dashboard.buyer') }}"
                            class="block px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-all duration-300 flex items-center">
                            <span class="material-icons mr-2 text-sm">dashboard</span>
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left block px-4 py-3 text-gray-700 hover:bg-red-50 hover:text-red-600 transition-all duration-300 flex items-center">
                                <span class="material-icons mr-2 text-sm">logout</span>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
                @endguest
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-white transition-transform duration-300 hover:scale-110">
                <span class="material-icons text-3xl">menu</span>
            </button>
        </div>

        {{-- Main Menu (second header) --}}
 

        {{-- Mobile menu (alpine) --}}
        <div x-show="mobileMenuOpen" x-transition class="mobile-menu md:hidden bg-white py-4 px-4 shadow-lg">
            <div class="flex flex-col space-y-4">
                <a href="{{ url('/') }}"
                    class="nav-link text-gray-700 hover:text-blue-600 font-medium transition-colors flex items-center">
                    <span class="material-icons mr-2">home</span> Home
                </a>
                <a href="{{ route('Auction.index') }}"
                    class="nav-link text-gray-700 hover:text-blue-600 font-medium transition-colors flex items-center">
                    <span class="material-icons mr-2">gavel</span> Auctions
                </a>
                <a href="{{ route('marketplace.index') }}"
                    class="nav-link text-gray-700 hover:text-blue-600 font-medium transition-colors flex items-center">
                    <span class="material-icons mr-2">store</span> Marketplace
                </a>

                <!-- How It Works Mobile -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="font-medium text-gray-700 mb-2">How It Works</div>
                    <a href="#" class="block pl-4 py-2 text-gray-600 hover:text-blue-600 transition-colors">How to Buy</a>
                    <a href="#" class="block pl-4 py-2 text-gray-600 hover:text-blue-600 transition-colors">How to Sell</a>
                    <a href="#" class="block pl-4 py-2 text-gray-600 hover:text-blue-600 transition-colors">Bidding 101</a>
                    <a href="#" class="block pl-4 py-2 text-gray-600 hover:text-blue-600 transition-colors">Video Guides</a>
                </div>

                <!-- Services & Support Mobile -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="font-medium text-gray-700 mb-2">Services & Support</div>
                    <a href="#" class="block pl-4 py-2 text-gray-600 hover:text-blue-600 transition-colors">Fee Calculator</a>
                    <a href="#" class="block pl-4 py-2 text-gray-600 hover:text-blue-600 transition-colors">Help Center</a>
                    <a href="#" class="block pl-4 py-2 text-gray-600 hover:text-blue-600 transition-colors">Rules & Policies</a>
                    <a href="#" class="block pl-4 py-2 text-gray-600 hover:text-blue-600 transition-colors">Contact Us</a>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    @guest
                    <a href="{{ route('login') }}"
                        class="block mb-3 nav-link text-gray-700 hover:text-blue-600 transition-colors flex items-center">
                        <span class="material-icons mr-2">person_outline</span> Login
                    </a>
                    <a href="#" id="registerBtnMobile"
                        class="block bg-gradient-to-r from-blue-600 to-blue-800 text-white font-bold py-2 px-6 rounded-full hover:from-blue-700 hover:to-blue-900 transition-all duration-300 text-center transform hover:scale-105">
                        Register
                    </a>
                    @endguest
                </div>
            </div>
        </div>
    </header> --}}

    {{-- Main content area (each page will fill this) --}}
    <main>
        @yield('content')
    </main>

    {{-- Enhanced Modern Footer --}}
    <footer class="relative bg-gradient-to-br from-gray-900 via-blue-900 to-indigo-900 pt-20 pb-8 mt-20 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.03"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-40"></div>
        
        <!-- Top Border Gradient -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
        
        <div class="container relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-16">
                <!-- Company Info Column -->
                <div class="footer-column">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-xl">CM</span>
                        </div>
                        <h3 class="text-2xl font-extrabold text-white font-heading">CayMark</h3>
                    </div>
                    <p class="mb-6 leading-relaxed text-gray-300 text-base">
                        The Bahamas' premier digital trading center for vehicles and marine vessels. Transforming how the islands buy, sell, and trade.
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="group relative w-11 h-11 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg border border-white/10">
                            <svg class="w-5 h-5 text-white group-hover:text-blue-300 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.129 22 16.99 22 12z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="group relative w-11 h-11 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg border border-white/10">
                            <svg class="w-5 h-5 text-white group-hover:text-blue-300 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                        <a href="#" class="group relative w-11 h-11 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg border border-white/10">
                            <svg class="w-5 h-5 text-white group-hover:text-blue-300 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.904 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links Column -->
                <div class="footer-column">
                    <h3 class="footer-heading text-xl font-bold font-heading text-white mb-6 relative pb-3">
                        Quick Links
                        <span class="absolute bottom-0 left-0 w-12 h-0.5 bg-gradient-to-r from-blue-400 to-transparent"></span>
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="{{ url('/') }}" class="footer-link flex items-center py-2 text-gray-300 hover:text-white transition-all duration-300 group">
                                <svg class="w-4 h-4 mr-3 text-blue-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('Auction.index') }}" class="footer-link flex items-center py-2 text-gray-300 hover:text-white transition-all duration-300 group">
                                <svg class="w-4 h-4 mr-3 text-blue-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span>Auctions</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('marketplace.index') }}" class="footer-link flex items-center py-2 text-gray-300 hover:text-white transition-all duration-300 group">
                                <svg class="w-4 h-4 mr-3 text-blue-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span>Marketplace</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-link flex items-center py-2 text-gray-300 hover:text-white transition-all duration-300 group">
                                <svg class="w-4 h-4 mr-3 text-blue-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span>Contact Us</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Support Column -->
                <div class="footer-column">
                    <h3 class="footer-heading text-xl font-bold font-heading text-white mb-6 relative pb-3">
                        Support
                        <span class="absolute bottom-0 left-0 w-12 h-0.5 bg-gradient-to-r from-blue-400 to-transparent"></span>
                    </h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="#" class="footer-link flex items-center py-2 text-gray-300 hover:text-white transition-all duration-300 group">
                                <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Help Center</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-link flex items-center py-2 text-gray-300 hover:text-white transition-all duration-300 group">
                                <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-5m-3 5h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <span>Fee Calculator</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-link flex items-center py-2 text-gray-300 hover:text-white transition-all duration-300 group">
                                <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <span>Rules & Policies</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-link flex items-center py-2 text-gray-300 hover:text-white transition-all duration-300 group">
                                <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>FAQ</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact Information Column -->
                <div class="footer-column">
                    <h3 class="footer-heading text-xl font-bold font-heading text-white mb-6 relative pb-3">
                        Contact Information
                        <span class="absolute bottom-0 left-0 w-12 h-0.5 bg-gradient-to-r from-blue-400 to-transparent"></span>
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex items-start text-gray-300 hover:text-white transition-colors duration-300 group">
                            <svg class="w-5 h-5 mr-3 text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="group-hover:translate-x-1 transition-transform inline-block">Nassau, The Bahamas</span>
                        </li>
                        <li class="flex items-center text-gray-300 hover:text-white transition-colors duration-300 group">
                            <svg class="w-5 h-5 mr-3 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="group-hover:translate-x-1 transition-transform inline-block">+1 (242) 555-CARS</span>
                        </li>
                        <li class="flex items-center text-gray-300 hover:text-white transition-colors duration-300 group">
                            <svg class="w-5 h-5 mr-3 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="group-hover:translate-x-1 transition-transform inline-block">info@caymark.com</span>
                        </li>
                        <li class="flex items-center text-gray-300 hover:text-white transition-colors duration-300 group">
                            <svg class="w-5 h-5 mr-3 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="group-hover:translate-x-1 transition-transform inline-block">Mon-Fri: 9AM-6PM EST</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">
                    &copy; {{ date('Y') }} <span class="text-white font-semibold">CayMark Island Exchange & Auction House</span>. All rights reserved.
                </p>
                <div class="flex flex-wrap justify-center gap-6">
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors duration-300 relative group">
                        Privacy Policy
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-400 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors duration-300 relative group">
                        Terms of Service
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-400 group-hover:w-full transition-all duration-300"></span>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors duration-300 relative group">
                        Cookie Policy
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-400 group-hover:w-full transition-all duration-300"></span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Global registration modal (kept in layout so pages can reuse) --}}
    <div id="registerModal"
        class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur flex items-center justify-center hidden z-50 md:z-[9999]">
        <div
            class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all duration-300 scale-95 opacity-0">
            <button id="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-300">✕</button>
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-2xl font-bold text-gray-800 text-center">Create Your Account</h2>
                <p class="text-gray-500 text-sm text-center mt-1">Select your role to get started</p>
            </div>
            <div class="p-6 space-y-4">
                <button id="buyerBtn"
                    class="w-full px-6 py-4 flex items-center justify-between border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all duration-300">
                    <span>Buyer</span>
                    <span class="material-icons text-blue-500">arrow_forward</span>
                </button>
                <button id="sellerBtn"
                    class="w-full px-6 py-4 flex items-center justify-between border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all duration-300">
                    <span>Seller</span>
                    <span class="material-icons text-blue-500">arrow_forward</span>
                </button>
            </div>
            <div class="p-4 bg-gray-50 text-center border-t border-gray-100">
                <p class="text-sm text-gray-500">Already have an account? <a href="#"
                        class="text-blue-600 hover:underline transition-colors duration-300">Sign in</a></p>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.loginUrl = '{{ route('login') }}';

        document.addEventListener('DOMContentLoaded', () => {
            // Header scroll effect
            const header = document.getElementById('mainHeader');
            if (header) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 50) {
                        header.classList.add('scrolled');
                    } else {
                        header.classList.remove('scrolled');
                    }
                });
            }

            // User dropdown functionality
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userMenuDropdown = document.getElementById('userMenuDropdown');

            if (userMenuBtn && userMenuDropdown) {
                userMenuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    userMenuDropdown.classList.toggle('show');

                    // Rotate the dropdown icon
                    const icon = userMenuBtn.querySelector('.material-icons:last-child');
                    if (icon) {
                        icon.style.transform = userMenuDropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', (e) => {
                    if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                        userMenuDropdown.classList.remove('show');
                        const icon = userMenuBtn.querySelector('.material-icons:last-child');
                        if (icon) {
                            icon.style.transform = 'rotate(0deg)';
                        }
                    }
                });
            }

            // Modal wiring
            const registerBtn = document.getElementById('registerBtn');
            const registerBtnMobile = document.getElementById('registerBtnMobile');
            const registerBtnBottom = document.getElementById('registerBtnBottom');
            const registerModal = document.getElementById('registerModal');
            const closeModal = document.getElementById('closeModal');
            const buyerBtn = document.getElementById('buyerBtn');
            const sellerBtn = document.getElementById('sellerBtn');

            function openRegisterModal() {
                registerModal.classList.remove('hidden');
                const modalContent = registerModal.querySelector('div.bg-white');
                if (modalContent) {
                    setTimeout(() => {
                        modalContent.classList.remove('scale-95', 'opacity-0');
                        modalContent.classList.add('scale-100', 'opacity-100');
                    }, 10);
                }
            }

            function closeRegisterModal() {
                const modalContent = registerModal.querySelector('div.bg-white');
                if (modalContent) {
                    modalContent.classList.remove('scale-100', 'opacity-100');
                    modalContent.classList.add('scale-95', 'opacity-0');
                }
                setTimeout(() => registerModal.classList.add('hidden'), 300);
            }

            if (registerBtn) registerBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openRegisterModal();
            });
            if (registerBtnMobile) registerBtnMobile.addEventListener('click', (e) => {
                e.preventDefault();
                openRegisterModal();
            });
            if (registerBtnBottom) registerBtnBottom.addEventListener('click', (e) => {
                e.preventDefault();
                openRegisterModal();
            });
            if (closeModal) closeModal.addEventListener('click', closeRegisterModal);
            if (registerModal) registerModal.addEventListener('click', (e) => {
                if (e.target === registerModal) closeRegisterModal();
            });
            if (buyerBtn) buyerBtn.addEventListener('click', () => {
                alert('Open Buyer registration form here');
                closeRegisterModal();
            });
            if (sellerBtn) sellerBtn.addEventListener('click', () => {
                alert('Open Seller registration form here');
                closeRegisterModal();
            });

            // Add active class to current page in navigation
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.nav-link');

            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            // basic carousel helper (if a page uses it)
            const slides = document.querySelectorAll('.carousel-slide');
            if (slides.length) {
                let idx = 0;

                function show(n) {
                    slides.forEach(s => s.classList.remove('active'));
                    idx = (n + slides.length) % slides.length;
                    slides[idx].classList.add('active');
                }
                setInterval(() => show(idx + 1), 5000);
            }
        });

        document.addEventListener('click', (event) => {
            const btn = event.target.closest('.js-like-toggle');
            if (!btn) return;
            event.preventDefault();

            if (btn.dataset.auth !== '1') {
                window.location.href = window.loginUrl;
                return;
            }

            if (btn.dataset.loading === '1') return;
            btn.dataset.loading = '1';

            fetch(btn.dataset.url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => response.json())
            .then(data => {
                const liked = !!data.in_watchlist;
                const icon = btn.querySelector('.material-icons');
                const countEl = btn.querySelector('.js-like-count');
                const unlikedClass = btn.dataset.unlikedClass || 'text-gray-400';

                if (icon) {
                    icon.textContent = liked ? 'favorite' : 'favorite_border';
                }

                btn.classList.remove('text-red-500');
                btn.classList.remove(unlikedClass);
                btn.classList.add(liked ? 'text-red-500' : unlikedClass);

                if (countEl && typeof data.likes_count !== 'undefined') {
                    countEl.textContent = data.likes_count;
                }
            })
            .catch(() => {})
            .finally(() => {
                btn.dataset.loading = '0';
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
