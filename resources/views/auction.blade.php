@extends('layouts.welcome')

@section('content')
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a'
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-down': 'slideDown 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        },
                        slideDown: {
                            '0%': {
                                transform: 'translateY(-10px)',
                                opacity: '0'
                            },
                            '100%': {
                                transform: 'translateY(0)',
                                opacity: '1'
                            }
                        },
                        bounceIn: {
                            '0%': {
                                transform: 'scale(0.9)',
                                opacity: '0'
                            },
                            '50%': {
                                transform: 'scale(1.02)'
                            },
                            '100%': {
                                transform: 'scale(1)',
                                opacity: '1'
                            }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .filter-section {
            transition: all 0.3s ease;
        }

        .vehicle-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease, opacity 0.3s ease;
        }

        .vehicle-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .badge {
            font-size: 0.7rem;
        }

        .view-toggle-btn {
            transition: all 0.2s ease;
        }

        .view-toggle-btn.active {
            background-color: #3b82f6;
            color: white;
        }

        .grid-view .vehicle-card {
            opacity: 0;
            animation: fadeIn 0.5s ease forwards;
        }

        .grid-view .vehicle-card:nth-child(odd) {
            animation-delay: 0.05s;
        }

        .grid-view .vehicle-card:nth-child(even) {
            animation-delay: 0.1s;
        }

        .detail-view .vehicle-card {
            animation: slideDown 0.4s ease forwards;
        }

        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Custom scrollbar for filter sections */
        .filter-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .filter-scroll::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .filter-scroll::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .filter-scroll::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Price tag animation */
        .price-tag {
            position: relative;
            overflow: hidden;
        }

        .price-tag::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s;
        }

        .price-tag:hover::before {
            left: 100%;
        }

        /* Image overlay effect */
        .image-container {
            position: relative;
            overflow: hidden;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-container:hover .image-overlay {
            opacity: 1;
        }

        .view-details-btn {
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .image-container:hover .view-details-btn {
            transform: translateY(0);
        }
        
        /* Countdown Timer Styles - Modern Glassmorphism */
        .countdown-badge {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.95) 0%, rgba(79, 70, 229, 0.95) 100%);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        /* Vehicle finder panel - Enhanced & Sticky */
        .vehicle-finder {
            position: sticky;
            top: 1rem;
            max-height: calc(100vh - 2rem);
            display: flex;
            flex-direction: column;
        }
        .vehicle-finder-header {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 50%, #1d4ed8 100%);
            color: white;
            font-weight: 700;
            font-size: 1.125rem;
            padding: 1.25rem 1.5rem;
            border-radius: 16px 16px 0 0;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            letter-spacing: 0.025em;
        }
        .vehicle-finder-body { 
            padding: 1.5rem 1.5rem 1.75rem; 
            overflow-y: auto;
            flex: 1;
        }
        .vehicle-finder-row { 
            display: flex; 
            flex-direction: column;
            margin-bottom: 1.25rem;
            gap: 0.5rem;
        }
        .vehicle-finder-row label { 
            font-size: 0.875rem; 
            font-weight: 600; 
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        .vehicle-finder-row .input-wrap { 
            width: 100%;
        }
        .segmented-control { 
            display: inline-flex; 
            border: 1px solid #d1d5db; 
            border-radius: 10px; 
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .segmented-control button { 
            padding: 0.625rem 1.25rem; 
            font-size: 0.875rem; 
            font-weight: 600; 
            border: none; 
            background: #f9fafb; 
            color: #6b7280; 
            cursor: pointer; 
            transition: all 0.2s ease;
            flex: 1;
        }
        .segmented-control button:not(:last-child) { 
            border-right: 1px solid #d1d5db; 
        }
        .segmented-control button.active { 
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); 
            color: white;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .segmented-control button:hover:not(.active) { 
            background: #f3f4f6;
            color: #374151;
        }
        .vehicle-finder select, 
        .vehicle-finder input[type="text"] { 
            width: 100%; 
            padding: 0.625rem 0.875rem; 
            border: 1px solid #d1d5db; 
            border-radius: 10px; 
            font-size: 0.875rem;
            background: white;
            transition: all 0.2s ease;
            color: #1f2937;
        }
        .vehicle-finder select:focus,
        .vehicle-finder input[type="text"]:focus {
            outline: none;
            border-color: #3b82f6;
            ring: 2px;
            ring-color: rgba(59, 130, 246, 0.2);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .vehicle-finder select:hover,
        .vehicle-finder input[type="text"]:hover {
            border-color: #9ca3af;
        }
        .vehicle-finder .or-divider { 
            display: flex; 
            align-items: center; 
            margin: 1.5rem 0; 
        }
        .vehicle-finder .or-divider::before, 
        .vehicle-finder .or-divider::after { 
            content: ''; 
            flex: 1; 
            height: 1px; 
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
        }
        .vehicle-finder .or-divider span { 
            padding: 0 1.25rem; 
            font-size: 0.75rem; 
            font-weight: 700; 
            color: #9ca3af;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .btn-search-vehicle { 
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); 
            color: white; 
            font-weight: 700; 
            font-size: 0.9375rem;
            padding: 0.875rem 1.5rem; 
            border-radius: 10px; 
            border: none; 
            width: 100%; 
            cursor: pointer; 
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(22, 163, 74, 0.3);
            letter-spacing: 0.025em;
        }
        .btn-search-vehicle:hover { 
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(22, 163, 74, 0.4);
        }
        .btn-search-vehicle:active {
            transform: translateY(0);
        }
        input[type="range"].odometer-range { 
            appearance: none; 
            -webkit-appearance: none; 
            height: 8px; 
            background: linear-gradient(to right, #dbeafe, #3b82f6);
            border-radius: 4px;
            outline: none;
            transition: opacity 0.2s;
        }
        input[type="range"].odometer-range:hover {
            opacity: 0.9;
        }
        input[type="range"].odometer-range::-webkit-slider-thumb { 
            -webkit-appearance: none; 
            width: 20px; 
            height: 20px; 
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); 
            border-radius: 50%; 
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.4);
            transition: all 0.2s ease;
        }
        input[type="range"].odometer-range::-webkit-slider-thumb:hover {
            transform: scale(1.1);
            box-shadow: 0 3px 6px rgba(37, 99, 235, 0.5);
        }
        input[type="range"].odometer-range::-moz-range-thumb {
            width: 20px; 
            height: 20px; 
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); 
            border-radius: 50%; 
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.4);
            border: none;
        }
        .location-radio { 
            display: flex; 
            flex-wrap: wrap;
            gap: 0.75rem; 
            margin-bottom: 0.625rem; 
        }
        .location-radio label { 
            display: flex; 
            align-items: center; 
            gap: 0.4rem; 
            font-size: 0.8125rem; 
            color: #4b5563;
            font-weight: 500;
            cursor: pointer; 
            min-width: auto;
            transition: color 0.2s ease;
        }
        .location-radio label:hover {
            color: #1f2937;
        }
        .location-radio input[type="radio"] {
            accent-color: #2563eb;
            cursor: pointer;
        }
        .year-selects {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        .year-selects select {
            flex: 1;
        }
        .year-selects .separator {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 500;
        }

        .results-typing-cursor {
            display: inline-block;
            animation: typing-cursor-blink 0.8s step-end infinite;
            color: #3b82f6;
            margin-left: 1px;
        }
        @keyframes typing-cursor-blink {
            50% { opacity: 0; }
        }

        /* Page header typography */
        .auction-page-title {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 35%, #4f46e5 70%, #1d4ed8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .results-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
            color: #1e40af;
            box-shadow: 0 1px 3px rgba(59, 130, 246, 0.08);
        }
    </style>

    <div class="bg-gray-50 text-gray-800 w-full cm-pull-refresh-root" id="cm-auction-pull-root" x-data="filterData()" x-init="initFilters()" @cm-pull-refresh.window="applyFilters()">

        <main class="w-full px-4 sm:px-6 lg:px-10 xl:px-12 2xl:px-16 py-6">

            <!-- Page Header -->
            <div class="mb-6 animate-fade-in">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-4">
                    <h2 class="auction-page-title text-2xl md:text-3xl lg:text-4xl font-bold tracking-tight">
                        Repairable, Salvage &amp; Wrecked Car Auctions
                    </h2>
                    <div class="results-count-badge shrink-0">
                        <span class="material-icons text-lg text-blue-600" aria-hidden="true">analytics</span>
                        <span class="results-count" data-full-text="Showing results {{ $auctions->firstItem() ?? 0 }} - {{ $auctions->lastItem() ?? 0 }} of {{ $auctions->total() }}">
                            <span id="results-count-typed"></span><span class="results-typing-cursor" id="results-count-cursor" aria-hidden="true">|</span>
                        </span>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

                    <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                        <!-- View Toggle: Segmented control -->
                        <div class="inline-flex rounded-xl bg-gray-100 p-1 shadow-inner" role="group" aria-label="View layout">
                            <button type="button"
                                @click="viewMode = 'grid'"
                                :class="viewMode === 'grid'
                                    ? 'bg-white text-blue-600 shadow-md ring-1 ring-gray-200/80'
                                    : 'text-gray-500 hover:text-gray-700'"
                                class="view-toggle-btn inline-flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-medium transition-all duration-200">
                                <span class="material-icons text-lg">grid_view</span>
                                <span>Grid</span>
                            </button>
                            <button type="button"
                                @click="viewMode = 'detail'"
                                :class="viewMode === 'detail'
                                    ? 'bg-white text-blue-600 shadow-md ring-1 ring-gray-200/80'
                                    : 'text-gray-500 hover:text-gray-700'"
                                class="view-toggle-btn inline-flex items-center gap-1.5 rounded-lg px-4 py-2.5 text-sm font-medium transition-all duration-200">
                                <span class="material-icons text-lg">view_list</span>
                                <span>List</span>
                            </button>
                        </div>

                        <!-- Active Filters pill -->
                        <div x-show="activeFilters"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-sm text-blue-700 ring-1 ring-blue-200/60">
                            <span>Filters active</span>
                            <span class="material-icons text-base cursor-pointer opacity-80 hover:opacity-100">cancel</span>
                        </div>

                        <!-- Sort by: Custom dropdown -->
                        <div class="inline-flex items-center gap-2" x-data="{ sortOpen: false }">
                            <span class="text-sm font-medium text-gray-600 whitespace-nowrap">Sort by</span>
                            <div class="relative" @click.outside="sortOpen = false">
                                <button type="button"
                                    @click="sortOpen = !sortOpen"
                                    class="inline-flex items-center justify-between gap-2 rounded-xl border border-gray-200 bg-white py-2.5 pl-4 pr-10 text-sm font-medium text-gray-800 shadow-sm transition hover:border-gray-300 hover:bg-gray-50 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none cursor-pointer min-w-[180px]">
                                    <span x-text="sortBy === 'newest' ? 'Newest to Oldest' : sortBy === 'oldest' ? 'Oldest to Newest' : sortBy === 'price_low' ? 'Price: Low to High' : 'Price: High to Low'">Newest to Oldest</span>
                                    <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 transition transform" :class="sortOpen && 'rotate-180'">
                                        <span class="material-icons text-xl">expand_more</span>
                                    </span>
                                </button>
                                <div x-show="sortOpen"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 scale-100"
                                    x-transition:leave-end="opacity-0 scale-95"
                                    class="absolute right-0 left-0 top-full z-50 mt-1.5 rounded-xl border border-gray-200 bg-white py-1 shadow-lg ring-1 ring-black/5"
                                    style="display: none;">
                                    <button type="button"
                                        @click="sortBy = 'newest'; applyFilters(); sortOpen = false"
                                        :class="sortBy === 'newest' ? 'bg-blue-50 text-blue-700' : 'text-gray-800 hover:bg-gray-50'"
                                        class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm font-medium transition">
                                        <span>Newest to Oldest</span>
                                        <span x-show="sortBy === 'newest'" class="material-icons text-lg text-blue-600">check</span>
                                    </button>
                                    <button type="button"
                                        @click="sortBy = 'oldest'; applyFilters(); sortOpen = false"
                                        :class="sortBy === 'oldest' ? 'bg-blue-50 text-blue-700' : 'text-gray-800 hover:bg-gray-50'"
                                        class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm font-medium transition">
                                        <span>Oldest to Newest</span>
                                        <span x-show="sortBy === 'oldest'" class="material-icons text-lg text-blue-600">check</span>
                                    </button>
                                    <button type="button"
                                        @click="sortBy = 'price_low'; applyFilters(); sortOpen = false"
                                        :class="sortBy === 'price_low' ? 'bg-blue-50 text-blue-700' : 'text-gray-800 hover:bg-gray-50'"
                                        class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm font-medium transition">
                                        <span>Price: Low to High</span>
                                        <span x-show="sortBy === 'price_low'" class="material-icons text-lg text-blue-600">check</span>
                                    </button>
                                    <button type="button"
                                        @click="sortBy = 'price_high'; applyFilters(); sortOpen = false"
                                        :class="sortBy === 'price_high' ? 'bg-blue-50 text-blue-700' : 'text-gray-800 hover:bg-gray-50'"
                                        class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm font-medium transition">
                                        <span>Price: High to Low</span>
                                        <span x-show="sortBy === 'price_high'" class="material-icons text-lg text-blue-600">check</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile: floating Filters button + bottom sheet (desktop sidebar unchanged at xl+) --}}
            <button
                type="button"
                class="cm-mobile-filters-btn xl:hidden"
                aria-controls="cm-auction-filter-sheet"
                aria-expanded="false"
                data-cm-open-sheet="cm-auction-filter-sheet"
            >
                <span class="material-icons" aria-hidden="true">tune</span>
                <span>Filters</span>
            </button>

            <x-ui.filter-bottom-sheet id="cm-auction-filter-sheet" title="Vehicle Finder">
                <div class="vehicle-finder vehicle-finder--sheet">
                    <div class="vehicle-finder-body filter-scroll">
                        @include('partials.auction-vehicle-finder-fields')
                    </div>
                </div>
            </x-ui.filter-bottom-sheet>

            <div class="flex flex-col xl:flex-row xl:gap-8 2xl:gap-10">
                <!-- Vehicle Finder Panel (desktop) -->
                <aside class="hidden xl:block w-full xl:w-80 flex-shrink-0">
                    <div class="vehicle-finder bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                        <div class="vehicle-finder-header">
                            <div class="flex items-center justify-between">
                                <span>Vehicle Finder</span>
                                <span class="material-icons text-white/80 text-xl">tune</span>
                            </div>
                        </div>
                        <div class="vehicle-finder-body filter-scroll">
                            @include('partials.auction-vehicle-finder-fields')
                        </div>
                    </div>
                </aside>

                <!-- Vehicle Listings -->
                <section class="flex-1 min-w-0">
                    <!-- Loading Skeleton -->
                    <template x-if="isLoading">
                        <div class="grid grid-cols-1 gap-5">
                            <div class="bg-white rounded-xl shadow-sm p-5 animate-pulse">
                                <div class="flex flex-col md:flex-row">
                                    <div class="md:w-2/5 h-48 skeleton rounded-lg mb-4 md:mb-0 md:mr-5"></div>
                                    <div class="flex-1">
                                        <div class="h-6 skeleton rounded w-3/4 mb-2"></div>
                                        <div class="grid grid-cols-2 gap-4 mt-4">
                                            <div class="h-4 skeleton rounded w-full"></div>
                                            <div class="h-4 skeleton rounded w-full"></div>
                                            <div class="h-4 skeleton rounded w-full"></div>
                                            <div class="h-4 skeleton rounded w-full"></div>
                                        </div>
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <div class="h-8 skeleton rounded w-1/3 mb-2"></div>
                                            <div class="h-10 skeleton rounded w-1/2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Grid View: less cramped – fewer columns, larger gap -->
                    <div x-show="viewMode === 'grid'"
                        class="grid-view grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 lg:gap-8" x-cloak>
                        @forelse($auctions as $listing)
                            <div
                                class="vehicle-card bg-white rounded-xl shadow-sm overflow-hidden flex flex-col h-full animate-bounce-in">
                                <!-- Image: consistent URL (uploads/listings or storage/listings) -->
                                <div class="image-container relative min-h-[12rem] bg-gray-100">
                                    @php
                                        $img = $listing->images->first();
                                        if (!$img) {
                                            $imgUrl = asset('images/placeholder-car.png');
                                        } else {
                                            $p = $img->image_path ?? '';
                                            if (str_starts_with($p, 'http')) {
                                                $imgUrl = $p;
                                            } elseif (str_contains($p, '/')) {
                                                $imgUrl = asset(ltrim($p, '/'));
                                            } elseif (str_starts_with($p, 'listings/')) {
                                                $imgUrl = asset('storage/' . $p);
                                            } else {
                                                $imgUrl = asset('uploads/listings/' . $p);
                                            }
                                        }
                                    @endphp
                                    <img alt="{{ $listing->title ?? $listing->make . ' ' . $listing->model }}"
                                        src="{{ $imgUrl }}"
                                        class="w-full h-48 object-cover cursor-pointer transition-transform duration-300 hover:scale-105"
                                        loading="lazy"
                                        onerror="this.onerror=null; this.src='{{ asset('images/placeholder-car.png') }}';"
                                        onclick="openImageModal('{{ $imgUrl }}')" />

                                    <div class="image-overlay">
                                        <button
                                            class="view-details-btn bg-white text-primary-600 font-medium py-2 px-4 rounded-lg text-sm hover:bg-primary-50 transition-colors"
                                            onclick="openImageModal('{{ $imgUrl }}')">
                                            View Image
                                        </button>
                                    </div>

                                    <div class="absolute top-3 right-3 z-10">
                                        <x-ui.watchlist-heart
                                            :listing="$listing"
                                            :in-watchlist="$likedListingIds->contains($listing->id)"
                                            :likes-count="$listing->likes_count ?? 0"
                                        />
                                    </div>

                                    <div class="absolute top-3 left-3 flex flex-col space-y-1 z-10">
                                        @if ($listing->featured)
                                            <span
                                                class="badge bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Featured</span>
                                        @endif
                                        @php
                                            $endDate = $listing->getAuctionEndDate();
                                        @endphp
                                        <x-ui.ending-soon-badge :end="$endDate" />
                                        <x-ui.countdown :end="$endDate" :listing-id="$listing->id" variant="grid" />
                                    </div>
                                </div>

                                <!-- Content: spacing and formatting for readability -->
                                <div class="p-4 flex-1 flex flex-col min-w-0">
                                    <h3 class="text-lg font-semibold text-secondary-800 mb-3 line-clamp-2 break-words">
                                        {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                    </h3>

                                    <div class="grid grid-cols-2 gap-x-3 gap-y-3 mb-4">
                                        <div class="flex items-start text-sm text-gray-600 min-w-0">
                                            <span class="material-icons text-gray-400 text-sm mr-1.5 shrink-0">speed</span>
                                            <span class="truncate">
                                            @if($listing->odometer)
                                                {{ number_format($listing->odometer) }} km
                                                @if($listing->odometer_estimated)
                                                    <span class="text-amber-600 font-medium">(Estimated)</span>
                                                    <span class="material-icons text-gray-400 cursor-help align-middle ml-0.5" title="This is an estimated odometer reading and may be subject to change." style="font-size: 14px;">info</span>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                        </div>
                                        <div class="flex items-start text-sm text-gray-600 min-w-0">
                                            <span class="material-icons text-gray-400 text-sm mr-1.5 shrink-0">location_on</span>
                                            <span class="truncate">{{ $listing->island ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-start text-sm text-gray-600 min-w-0">
                                            <span class="material-icons text-gray-400 text-sm mr-1.5 shrink-0">receipt</span>
                                            <span class="truncate">{{ $listing->title_status_display }}</span>
                                        </div>
                                        <div class="flex items-start text-sm text-gray-600 min-w-0">
                                            <span class="material-icons text-gray-400 text-sm mr-1.5 shrink-0">event</span>
                                            <span class="truncate" title="{{ $listing->sale_date ? '' : 'Sale date not set for this listing.' }}">{{ $listing->sale_date ? \Carbon\Carbon::parse($listing->sale_date)->format('M d, Y') : 'N/A' }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-auto pt-3 border-t border-gray-200">
                                        <p class="text-xs text-gray-500">Current Bid</p>
                                        <div class="flex justify-between items-center">
                                            <p class="text-xl font-bold text-green-600 price-tag">
                                                ${{ number_format($listing->current_bid ?? 0) }}
                                            </p>
                                            <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                                                class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 text-sm transform hover:-translate-y-0.5 hover:shadow-md">
                                                Bid Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-10">
                                <span class="material-icons text-gray-400 text-6xl mb-4">search_off</span>
                                <h3 class="text-xl font-semibold text-gray-600 mb-2">No listings found</h3>
                                <p class="text-gray-500">Try adjusting your filters to find more results.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Detailed View: more spacing between cards -->
                    <div x-show="viewMode === 'detail'" class="detail-view grid grid-cols-1 gap-8" id="auctionListings" x-cloak>
                        @include('partials.auction-listings', ['auctions' => $auctions])
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center animate-fade-in" id="auctionPagination">
                        @include('partials.auction-pagination', ['auctions' => $auctions])
                    </div>
                </section>
            </div>

            <!-- Include Alpine.js if not already -->
            <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


            <script>
                function filterData() {
                    return {
                        isLoading: false,
                        viewMode: 'detail',
                        activeFilters: false,
                        selectedFilters: {
                            vehicle_type: '{{ request('vehicle_type', '') }}',
                        },
                        titleCondition: '{{ request('title_condition') ? (is_array(request('title_condition')) ? request('title_condition')[0] : request('title_condition')) : '' }}',
                        condition: '{{ request('condition', '') }}',
                        makeSingle: @json(is_array(request('makes')) ? (request('makes')[0] ?? '') : (request('makes') ?? '')),
                        modelSingle: @json(is_array(request('models')) ? (request('models')[0] ?? '') : (request('models') ?? '')),
                        locationSingle: @json(is_array(request('location')) ? (request('location')[0] ?? '') : (request('location') ?? '')),
                        fuelTypeMulti: @json(request('fuel_type', [])),
                        colorMulti: @json(request('colors', [])),
                        damageTypeMulti: @json(request('damage_type', [])),
                        yearFrom: {{ request('year_from', 1990) }},
                        yearTo: {{ request('year_to', date('Y') + 1) }},
                        odometerMin: 0,
                        odometerMax: {{ request('odometer_max', $filterOptions['odometer_max'] ?? 250000) }},
                        sortBy: '{{ request('sort', 'newest') }}',
                        allMakes: @json($filterOptions['makes']),
                        allModels: @json($filterOptions['models']),
                        
                        initFilters() {
                            if (typeof localStorage !== 'undefined' && localStorage.getItem('auctionViewMode')) {
                                this.viewMode = localStorage.getItem('auctionViewMode');
                            }
                            this.$watch('viewMode', value => {
                                this.isLoading = true;
                                if (typeof localStorage !== 'undefined') localStorage.setItem('auctionViewMode', value);
                                setTimeout(() => { this.isLoading = false; }, 300);
                            });
                            window.__auctionRefresh = () => this.applyFilters();
                        },
                        
                        clearAllFilters() {
                            this.selectedFilters.vehicle_type = '';
                            this.titleCondition = '';
                            this.condition = '';
                            this.makeSingle = '';
                            this.modelSingle = '';
                            this.locationSingle = '';
                            this.fuelTypeMulti = [];
                            this.colorMulti = [];
                            this.damageTypeMulti = [];
                            this.yearFrom = 1990;
                            this.yearTo = {{ date('Y') + 1 }};
                            this.odometerMin = 0;
                            this.odometerMax = {{ $filterOptions['odometer_max'] ?? 250000 }};
                            this.sortBy = 'newest';
                            this.applyFilters();
                        },
                        
                        applyFilters() {
                            this.isLoading = true;
                            const params = new URLSearchParams();
                            const self = this;
                            
                            if (this.titleCondition) params.append('title_condition[]', this.titleCondition);
                            if (this.condition) params.append('condition', this.condition);
                            if (this.selectedFilters.vehicle_type) params.append('vehicle_type', this.selectedFilters.vehicle_type);
                            if (this.makeSingle && this.makeSingle.trim()) params.append('makes[]', this.makeSingle.trim());
                            if (this.modelSingle && this.modelSingle.trim()) params.append('models[]', this.modelSingle.trim());
                            if (this.locationSingle) params.append('location[]', this.locationSingle);
                            (this.fuelTypeMulti || []).forEach(v => { if (v) params.append('fuel_type[]', v); });
                            (this.colorMulti || []).forEach(v => { if (v) params.append('colors[]', v); });
                            (this.damageTypeMulti || []).forEach(v => { if (v) params.append('damage_type[]', v); });
                            
                            if (this.yearFrom && this.yearFrom > 1990) params.append('year_from', this.yearFrom);
                            if (this.yearTo && this.yearTo < {{ date('Y') + 1 }}) params.append('year_to', this.yearTo);
                            if (this.odometerMax && this.odometerMax < {{ $filterOptions['odometer_max'] ?? 250000 }}) params.append('odometer_max', this.odometerMax);
                            if (this.sortBy) params.append('sort', this.sortBy);
                            
                            // Update URL without reload
                            const newUrl = '{{ route('Auction.index') }}' + (params.toString() ? '?' + params.toString() : '');
                            window.history.pushState({}, '', newUrl);
                            
                            // Make AJAX request
                            return fetch(newUrl, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    document.getElementById('auctionListings').innerHTML = data.html;
                                    document.getElementById('auctionPagination').innerHTML = data.pagination;
                                    
                                    // Update results count with typing effect
                                    const resultsEl = document.querySelector('.results-count');
                                    if (resultsEl) {
                                        resultsEl.setAttribute('data-full-text', `Showing results 1 - ${data.count} of ${data.count}`);
                                        typeResultsCount();
                                    }
                                    
                                    if (window.CaymarkUI && CaymarkUI.auction) {
                                        CaymarkUI.auction.initCountdowns(document.getElementById('auctionListings'));
                                        CaymarkUI.auction.initWatchlistHearts(document.getElementById('auctionListings'));
                                    }
                                }
                                self.isLoading = false;
                            })
                            .catch(error => {
                                console.error('Filter error:', error);
                                self.isLoading = false;
                            });
                        }
                    }
                }
                
                function openImageModal(url) {
                    document.getElementById('modalImage').src = url;
                    document.getElementById('imageModal').classList.remove('hidden');
                    document.getElementById('imageModal').classList.add('flex');
                    setTimeout(() => {
                        document.getElementById('imageModal').classList.add('opacity-100');
                    }, 10);
                }

                function closeImageModal() {
                    document.getElementById('imageModal').classList.remove('opacity-100');
                    setTimeout(() => {
                        document.getElementById('imageModal').classList.add('hidden');
                        document.getElementById('imageModal').classList.remove('flex');
                    }, 300);
                }

                function typeResultsCount() {
                    const el = document.querySelector('.results-count');
                    const typed = document.getElementById('results-count-typed');
                    const cursor = document.getElementById('results-count-cursor');
                    if (!el || !typed || !cursor) return;
                    const text = el.getAttribute('data-full-text') || '';
                    typed.textContent = '';
                    cursor.style.visibility = 'visible';
                    let i = 0;
                    const speed = 45;
                    function tick() {
                        if (i < text.length) {
                            typed.textContent += text[i];
                            i++;
                            setTimeout(tick, speed);
                        } else {
                            cursor.style.visibility = 'hidden';
                        }
                    }
                    tick();
                }

                document.addEventListener('DOMContentLoaded', function() {
                    typeResultsCount();
                    if (window.CaymarkUI && CaymarkUI.auction) {
                        CaymarkUI.auction.initCountdowns();
                        CaymarkUI.auction.initWatchlistHearts();
                    }
                    if (window.CaymarkUI && CaymarkUI.mobile) {
                        CaymarkUI.mobile.initPullToRefresh(
                            document.getElementById('cm-auction-pull-root'),
                            function () {
                                if (typeof window.__auctionRefresh === 'function') {
                                    return window.__auctionRefresh();
                                }
                                window.dispatchEvent(new CustomEvent('cm:pull-refresh'));
                            }
                        );
                    }
                });

                // Close modal on ESC key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeImageModal();
                    }
                });
                
            </script>
        @endsection
