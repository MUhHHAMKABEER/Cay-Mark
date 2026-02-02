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

    <div class="bg-gray-50 text-gray-800" x-data="filterData()" x-init="initFilters()">

        <main class="w-full px-4 py-6">

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
                                    <span x-text="sortBy === 'newest' ? 'Newest First' : sortBy === 'price_low' ? 'Price: Low to High' : sortBy === 'price_high' ? 'Price: High to Low' : 'Ending Soonest'">Newest First</span>
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
                                        <span>Newest First</span>
                                        <span x-show="sortBy === 'newest'" class="material-icons text-lg text-blue-600">check</span>
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
                                    <button type="button"
                                        @click="sortBy = 'ending_soon'; applyFilters(); sortOpen = false"
                                        :class="sortBy === 'ending_soon' ? 'bg-blue-50 text-blue-700' : 'text-gray-800 hover:bg-gray-50'"
                                        class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm font-medium transition">
                                        <span>Ending Soonest</span>
                                        <span x-show="sortBy === 'ending_soon'" class="material-icons text-lg text-blue-600">check</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col xl:flex-row gap-6">
                <!-- Vehicle Finder Panel - Enhanced & Wider -->
                <aside class="w-full xl:w-96 flex-shrink-0">
                    <div class="vehicle-finder bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
                        <div class="vehicle-finder-header">
                            <div class="flex items-center justify-between">
                                <span>Vehicle Finder</span>
                                <span class="material-icons text-white/80 text-xl">tune</span>
                            </div>
                        </div>
                        <div class="vehicle-finder-body">
                            <!-- Condition -->
                            <div class="vehicle-finder-row">
                                <label>Condition</label>
                                <div class="input-wrap">
                                    <div class="segmented-control">
                                        <button type="button" :class="{ 'active': condition === '' }" @click="condition = ''; applyFilters()">All</button>
                                        <button type="button" :class="{ 'active': condition === 'used' }" @click="condition = 'used'; applyFilters()">Used</button>
                                        <button type="button" :class="{ 'active': condition === 'salvaged' }" @click="condition = 'salvaged'; applyFilters()">Salvage</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Types -->
                            <div class="vehicle-finder-row">
                                <label>Vehicle Type</label>
                                <div class="input-wrap">
                                    <select x-model="selectedFilters.vehicle_type" @change="applyFilters()">
                                        <option value="">All Types</option>
                                        @foreach ($filterOptions['vehicle_types'] as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Odometer -->
                            <div class="vehicle-finder-row">
                                <label>Odometer Range</label>
                                <div class="input-wrap">
                                    <div class="flex justify-between text-xs text-gray-500 mb-2 font-medium">
                                        <span>0 mi</span>
                                        <span>250,000+ mi</span>
                                    </div>
                                    <input type="range" class="odometer-range w-full" min="0" max="{{ $filterOptions['odometer_max'] ?? 250000 }}"
                                        x-model.number="odometerMax" @input.debounce.300ms="applyFilters()">
                                    <p class="text-xs text-blue-600 mt-2 font-semibold" x-text="`Up to ${odometerMax ? odometerMax.toLocaleString() : 0} miles`"></p>
                                </div>
                            </div>
                            
                            <!-- Year -->
                            <div class="vehicle-finder-row">
                                <label>Year Range</label>
                                <div class="input-wrap">
                                    <div class="year-selects">
                                        <select x-model.number="yearFrom" @change="applyFilters()">
                                            @for($y = date('Y') + 1; $y >= 1990; $y--)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                        <span class="separator">to</span>
                                        <select x-model.number="yearTo" @change="applyFilters()">
                                            @for($y = date('Y') + 1; $y >= 1990; $y--)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Damage type -->
                            <div class="vehicle-finder-row">
                                <label>Damage Type</label>
                                <div class="input-wrap">
                                    <select x-model="damageTypeSingle" @change="applyFilters()">
                                        <option value="">All Damage Types</option>
                                        @foreach ($filterOptions['damage_types'] as $damage)
                                            <option value="{{ $damage }}">{{ $damage }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Make -->
                            <div class="vehicle-finder-row">
                                <label>Make</label>
                                <div class="input-wrap">
                                    <select x-model="makeSingle" @change="applyFilters()">
                                        <option value="">All Makes</option>
                                        @foreach ($filterOptions['makes'] as $make)
                                            <option value="{{ $make }}">{{ $make }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Model -->
                            <div class="vehicle-finder-row">
                                <label>Model</label>
                                <div class="input-wrap">
                                    <select x-model="modelSingle" @change="applyFilters()">
                                        <option value="">All Models</option>
                                        @foreach ($filterOptions['models'] as $model)
                                            <option value="{{ $model }}">{{ $model }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Location -->
                            <div class="vehicle-finder-row">
                                <label>Location</label>
                                <div class="input-wrap">
                                    <div class="location-radio">
                                        <label><input type="radio" name="location_mode" value="location" checked> Location</label>
                                        <label><input type="radio" name="location_mode" value="state"> State/Province</label>
                                        <label><input type="radio" name="location_mode" value="zip"> Zip Code</label>
                                    </div>
                                    <select x-model="locationSingle" @change="applyFilters()">
                                        <option value="">All Locations</option>
                                        @foreach ($filterOptions['locations'] as $location)
                                            <option value="{{ $location }}">{{ $location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <!-- OR Divider -->
                            <div class="or-divider"><span>OR</span></div>
                            
                            <!-- VIN/Lot # -->
                            <div class="vehicle-finder-row">
                                <label>VIN / Lot Number</label>
                                <div class="input-wrap">
                                    <input type="text" x-model="vinLot" placeholder="Enter VIN or lot number"
                                        @input.debounce.400ms="applyFilters()">
                                </div>
                            </div>
                            
                            <!-- Search button -->
                            <button type="button" class="btn-search-vehicle mt-3" @click="applyFilters()">
                                <span class="flex items-center justify-center gap-2">
                                    <span class="material-icons text-lg">search</span>
                                    <span>Search Vehicles</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Vehicle Listings -->
                <section class="flex-1">
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

                    <!-- Grid View -->
                    <div x-show="viewMode === 'grid'"
                        class="grid-view grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" x-cloak>
                        @forelse($auctions as $listing)
                            <div
                                class="vehicle-card bg-white rounded-xl shadow-sm overflow-hidden flex flex-col h-full animate-bounce-in">
                                <!-- Image -->
                                <div class="image-container relative">
                                    @php
                                        $img = $listing->images->first();
                                        $imgUrl = $img
                                            ? (str_contains($img->image_path, '/')
                                                ? asset($img->image_path)
                                                : asset('uploads/listings/' . $img->image_path))
                                            : asset('images/placeholder-car.png');
                                    @endphp
                                    <img alt="{{ $listing->title ?? $listing->make . ' ' . $listing->model }}"
                                        src="{{ $imgUrl }}"
                                        class="w-full h-48 object-cover cursor-pointer transition-transform duration-300 hover:scale-105"
                                        onclick="openImageModal('{{ $imgUrl }}')" />

                                    <div class="image-overlay">
                                        <button
                                            class="view-details-btn bg-white text-primary-600 font-medium py-2 px-4 rounded-lg text-sm hover:bg-primary-50 transition-colors"
                                            onclick="openImageModal('{{ $imgUrl }}')">
                                            View Image
                                        </button>
                                    </div>

                                    <div class="absolute top-3 left-3 flex flex-col space-y-1">
                                        @if ($listing->featured)
                                            <span
                                                class="badge bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Featured</span>
                                        @endif
                                        @php
                                            // Calculate end date from database
                                            if ($listing->auction_end_time) {
                                                $endDate = \Carbon\Carbon::parse($listing->auction_end_time);
                                            } elseif ($listing->auction_start_time) {
                                                $endDate = \Carbon\Carbon::parse($listing->auction_start_time)->addDays($listing->auction_duration ?? 7);
                                            } else {
                                                $endDate = \Carbon\Carbon::parse($listing->created_at)->addDays($listing->auction_duration ?? 7);
                                            }
                                            $isExpired = \Carbon\Carbon::now()->greaterThanOrEqualTo($endDate);
                                        @endphp
                                        @if(!$isExpired)
                                        <div class="bg-gradient-to-br from-blue-600/95 to-indigo-700/95 backdrop-blur-md text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-xl border border-white/20" 
                                             id="countdown-grid-{{ $listing->id }}" 
                                             data-end-time="{{ $endDate->toIso8601String() }}">
                                            <div class="flex items-center space-x-1">
                                                <span class="bg-white/20 backdrop-blur-sm px-1.5 py-0.5 rounded font-mono text-xs" id="days-grid-{{ $listing->id }}">00</span>
                                                <span class="text-white/70 text-xs">:</span>
                                                <span class="bg-white/20 backdrop-blur-sm px-1.5 py-0.5 rounded font-mono text-xs" id="hours-grid-{{ $listing->id }}">00</span>
                                                <span class="text-white/70 text-xs">:</span>
                                                <span class="bg-white/20 backdrop-blur-sm px-1.5 py-0.5 rounded font-mono text-xs" id="minutes-grid-{{ $listing->id }}">00</span>
                                            </div>
                                        </div>
                                        @else
                                        <span class="badge bg-gray-500/90 backdrop-blur-sm text-white px-2 py-1 rounded-full text-xs font-semibold border border-gray-400/30">Ended</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-4 flex-1 flex flex-col">
                                    <h3 class="text-lg font-semibold text-secondary-800 mb-2 line-clamp-1">
                                        {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                    </h3>

                                    <div class="grid grid-cols-2 gap-2 mb-4">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="material-icons text-gray-400 text-sm mr-1">speed</span>
                                            <span>{{ $listing->odometer ? number_format($listing->odometer) . ' mi' : 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="material-icons text-gray-400 text-sm mr-1">location_on</span>
                                            <span class="truncate">{{ $listing->island ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="material-icons text-gray-400 text-sm mr-1">receipt</span>
                                            <span class="truncate">{{ $listing->title_status ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <span class="material-icons text-gray-400 text-sm mr-1">event</span>
                                            <span>{!! $listing->sale_date ? \Carbon\Carbon::parse($listing->sale_date)->format('M d') : 'N/A' !!}</span>
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

                    <!-- Detailed View -->
                    <div x-show="viewMode === 'detail'" class="detail-view grid grid-cols-1 gap-5" id="auctionListings" x-cloak>
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
                        condition: '{{ request('condition', '') }}',
                        makeSingle: @json(is_array(request('makes')) ? (request('makes')[0] ?? '') : (request('makes') ?? '')),
                        modelSingle: @json(is_array(request('models')) ? (request('models')[0] ?? '') : (request('models') ?? '')),
                        locationSingle: @json(is_array(request('location')) ? (request('location')[0] ?? '') : (request('location') ?? '')),
                        damageTypeSingle: @json(is_array(request('damage_type')) ? (request('damage_type')[0] ?? '') : (request('damage_type') ?? '')),
                        vinLot: '{{ request('search', '') }}',
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
                        },
                        
                        clearAllFilters() {
                            this.selectedFilters.vehicle_type = '';
                            this.condition = '';
                            this.makeSingle = '';
                            this.modelSingle = '';
                            this.locationSingle = '';
                            this.damageTypeSingle = '';
                            this.vinLot = '';
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
                            
                            if (this.condition) params.append('condition', this.condition);
                            if (this.selectedFilters.vehicle_type) params.append('vehicle_type', this.selectedFilters.vehicle_type);
                            if (this.makeSingle) params.append('makes[]', this.makeSingle);
                            if (this.modelSingle) params.append('models[]', this.modelSingle);
                            if (this.locationSingle) params.append('location[]', this.locationSingle);
                            if (this.damageTypeSingle) params.append('damage_type[]', this.damageTypeSingle);
                            if (this.vinLot && this.vinLot.trim()) params.append('search', this.vinLot.trim());
                            
                            if (this.yearFrom && this.yearFrom > 1990) params.append('year_from', this.yearFrom);
                            if (this.yearTo && this.yearTo < {{ date('Y') + 1 }}) params.append('year_to', this.yearTo);
                            if (this.odometerMax && this.odometerMax < {{ $filterOptions['odometer_max'] ?? 250000 }}) params.append('odometer_max', this.odometerMax);
                            if (this.sortBy) params.append('sort', this.sortBy);
                            
                            // Update URL without reload
                            const newUrl = '{{ route('Auction.index') }}' + (params.toString() ? '?' + params.toString() : '');
                            window.history.pushState({}, '', newUrl);
                            
                            // Make AJAX request
                            fetch(newUrl, {
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
                                    
                                    // Update countdown timers after AJAX load
                                    setTimeout(updateCountdownTimers, 100);
                                }
                                this.isLoading = false;
                            })
                            .catch(error => {
                                console.error('Filter error:', error);
                                this.isLoading = false;
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
                });

                // Close modal on ESC key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeImageModal();
                    }
                });
                
                // Countdown Timer Function
                function updateCountdownTimers() {
                    // Update grid view timers
                    document.querySelectorAll('[id^="countdown-grid-"]').forEach(element => {
                        const listingId = element.id.replace('countdown-grid-', '');
                        const endTime = new Date(element.getAttribute('data-end-time'));
                        const now = new Date();
                        const diff = Math.max(0, Math.floor((endTime - now) / 1000));
                        
                        if (diff <= 0) {
                            element.innerHTML = '<span class="badge bg-gray-500/90 backdrop-blur-sm text-white px-2 py-1 rounded-full text-xs font-semibold border border-gray-400/30">Ended</span>';
                            return;
                        }
                        
                        const days = Math.floor(diff / 86400);
                        const hours = Math.floor((diff % 86400) / 3600);
                        const minutes = Math.floor((diff % 3600) / 60);
                        
                        const daysEl = document.getElementById('days-grid-' + listingId);
                        const hoursEl = document.getElementById('hours-grid-' + listingId);
                        const minutesEl = document.getElementById('minutes-grid-' + listingId);
                        
                        if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
                        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
                        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
                    });
                    
                    // Update detail view timers
                    document.querySelectorAll('[id^="countdown-detail-"]').forEach(element => {
                        const listingId = element.id.replace('countdown-detail-', '');
                        const endTime = new Date(element.getAttribute('data-end-time'));
                        const now = new Date();
                        const diff = Math.max(0, Math.floor((endTime - now) / 1000));
                        
                        if (diff <= 0) {
                            element.innerHTML = '<div class="bg-gray-500/90 backdrop-blur-md text-white px-4 py-2.5 rounded-xl text-sm font-bold shadow-xl border border-gray-400/30">Auction Ended</div>';
                            return;
                        }
                        
                        const days = Math.floor(diff / 86400);
                        const hours = Math.floor((diff % 86400) / 3600);
                        const minutes = Math.floor((diff % 3600) / 60);
                        const seconds = diff % 60;
                        
                        const daysEl = document.getElementById('days-detail-' + listingId);
                        const hoursEl = document.getElementById('hours-detail-' + listingId);
                        const minutesEl = document.getElementById('minutes-detail-' + listingId);
                        const secondsEl = document.getElementById('seconds-detail-' + listingId);
                        
                        if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
                        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
                        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
                        if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
                    });
                }
                
                // Update countdown timers every second
                setInterval(updateCountdownTimers, 1000);
                updateCountdownTimers(); // Initial call
            </script>
        @endsection
