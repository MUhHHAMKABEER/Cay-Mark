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
    </style>

    <div class="bg-gray-50 text-gray-800" x-data="{ viewMode: 'detail', isLoading: false, activeFilters: false }" x-init="// Check if user has a preference saved
    if (localStorage.getItem('auctionViewMode')) {
        viewMode = localStorage.getItem('auctionViewMode');
    }
    $watch('viewMode', value => {
        isLoading = true;
        localStorage.setItem('auctionViewMode', value);
        // Simulate loading delay for smooth transition
        setTimeout(() => { isLoading = false; }, 300);
    });">

        <main class="container mx-auto px-4 py-6">

            <!-- Page Header -->
            <div class="mb-6 animate-fade-in">
                <h2 class="text-2xl md:text-3xl font-bold text-secondary-800 mb-2">Repairable, Salvage and Wrecked Car
                    Auctions</h2>
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <p class="text-sm text-secondary-500 mb-2 md:mb-0">
                        Showing results {{ $auctions->firstItem() }} - {{ $auctions->lastItem() }} of
                        {{ $auctions->total() }}
                    </p>

                    <div class="flex items-center space-x-4">
                        <!-- View Toggle -->
                        <div class="flex items-center bg-white rounded-lg border border-gray-300 overflow-hidden shadow-sm">
                            <button @click="viewMode = 'grid'"
                                :class="viewMode === 'grid' ? 'active bg-primary-500 text-white' : 'text-gray-600'"
                                class="view-toggle-btn px-3 py-2 flex items-center transition-all duration-200">
                                <span class="material-icons text-sm mr-1">grid_view</span>
                                <span class="text-sm">Grid</span>
                            </button>
                            <button @click="viewMode = 'detail'"
                                :class="viewMode === 'detail' ? 'active bg-primary-500 text-white' : 'text-gray-600'"
                                class="view-toggle-btn px-3 py-2 flex items-center transition-all duration-200">
                                <span class="material-icons text-sm mr-1">view_list</span>
                                <span class="text-sm">List</span>
                            </button>
                        </div>

                        <!-- Active Filters -->
                        <div x-show="activeFilters" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            class="flex items-center bg-primary-100 rounded-lg px-3 py-1">
                            <span class="text-sm text-primary-700 mr-2">Filters Active</span>
                            <span
                                class="material-icons text-primary-500 text-sm cursor-pointer hover:text-primary-700">cancel</span>
                        </div>

                        <!-- Sort -->
                        <div class="flex items-center">
                            <label for="sort" class="text-sm text-secondary-600 mr-2">Sort by:</label>
                            <select id="sort"
                                class="border border-gray-300 rounded-lg py-1.5 px-3 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option>Recommended</option>
                                <option>Newest First</option>
                                <option>Price: Low to High</option>
                                <option>Price: High to Low</option>
                                <option>Ending Soonest</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <!-- Filters Sidebar -->
                <aside class="w-full lg:w-1/4">
                    <form method="GET" action="{{ route('Auction.index') }}">
                        <div
                            class="bg-white rounded-xl shadow-sm p-5 mb-6 sticky top-4 transition-all duration-300 hover:shadow-md">
                            <div class="flex justify-between items-center mb-5">
                                <h3 class="font-semibold text-secondary-800 text-lg">Filter Vehicles</h3>
                                <a href="{{ route('Auction.index') }}"
                                    class="text-sm text-primary-600 font-medium hover:text-primary-800 transition-colors">Clear
                                    All</a>
                            </div>

                            <div class="space-y-5 max-h-[70vh] overflow-y-auto filter-scroll pr-2">
                                <!-- Type -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="$event.target.nextElementSibling.classList.toggle('hidden'); $event.target.querySelector('span').textContent = $event.target.nextElementSibling.classList.contains('hidden') ? 'expand_more' : 'expand_less'">
                                        <span>Type</span>
                                        <span
                                            class="material-icons text-gray-500 text-lg transition-transform">expand_less</span>
                                    </h4>
                                    <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                        @foreach ($filterOptions['types'] as $type)
                                            <label class="flex items-center py-1 cursor-pointer group">
                                                <input type="checkbox" name="type[]" value="{{ $type }}"
                                                    @checked(in_array($type, request('type', [])))
                                                    class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                <span
                                                    class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ $type }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Makes -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="$event.target.nextElementSibling.classList.toggle('hidden'); $event.target.querySelector('span').textContent = $event.target.nextElementSibling.classList.contains('hidden') ? 'expand_more' : 'expand_less'">
                                        <span>Makes</span>
                                        <span
                                            class="material-icons text-gray-500 text-lg transition-transform">expand_less</span>
                                    </h4>
                                    <div>
                                        <input type="text" placeholder="Search Makes..."
                                            class="w-full border rounded-lg p-2 mb-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                        <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                            @foreach ($filterOptions['makes'] as $make)
                                                <label class="flex items-center py-1 cursor-pointer group">
                                                    <input type="checkbox" name="makes[]" value="{{ $make }}"
                                                        @checked(in_array($make, request('makes', [])))
                                                        class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                    <span
                                                        class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ $make }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Models -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="$event.target.nextElementSibling.classList.toggle('hidden'); $event.target.querySelector('span').textContent = $event.target.nextElementSibling.classList.contains('hidden') ? 'expand_more' : 'expand_less'">
                                        <span>Models</span>
                                        <span
                                            class="material-icons text-gray-500 text-lg transition-transform">expand_less</span>
                                    </h4>
                                    <div>
                                        <input type="text" placeholder="Search Models..."
                                            class="w-full border rounded-lg p-2 mb-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                        <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                            @foreach ($filterOptions['models'] as $model)
                                                <label class="flex items-center py-1 cursor-pointer group">
                                                    <input type="checkbox" name="models[]" value="{{ $model }}"
                                                        @checked(in_array($model, request('models', [])))
                                                        class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                    <span
                                                        class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ $model }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Locations -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="$event.target.nextElementSibling.classList.toggle('hidden'); $event.target.querySelector('span').textContent = $event.target.nextElementSibling.classList.contains('hidden') ? 'expand_more' : 'expand_less'">
                                        <span>Locations</span>
                                        <span
                                            class="material-icons text-gray-500 text-lg transition-transform">expand_less</span>
                                    </h4>
                                    <div>
                                        <input type="text" placeholder="Search Locations..."
                                            class="w-full border rounded-lg p-2 mb-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                        <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                            @foreach ($filterOptions['locations'] as $location)
                                                <label class="flex items-center py-1 cursor-pointer group">
                                                    <input type="checkbox" name="locations[]" value="{{ $location }}"
                                                        @checked(in_array($location, request('locations', [])))
                                                        class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                    <span
                                                        class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ $location }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <!-- Year -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2">Year</h4>
                                    <div class="flex items-center space-x-2">
                                        <input type="number" name="year_from" value="{{ request('year_from') }}"
                                            placeholder="From"
                                            class="w-1/2 border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                        <input type="number" name="year_to" value="{{ request('year_to') }}"
                                            placeholder="To"
                                            class="w-1/2 border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                    </div>
                                </div>

                                <!-- Odometer -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2">Odometer</h4>
                                    <div class="flex items-center space-x-2">
                                        <input type="number" name="odo_min" value="{{ request('odo_min') }}"
                                            placeholder="Min"
                                            class="w-1/2 border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                        <input type="number" name="odo_max" value="{{ request('odo_max') }}"
                                            placeholder="Max"
                                            class="w-1/2 border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                    </div>
                                </div>

                                <!-- Color -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="$event.target.nextElementSibling.classList.toggle('hidden'); $event.target.querySelector('span').textContent = $event.target.nextElementSibling.classList.contains('hidden') ? 'expand_more' : 'expand_less'">
                                        <span>Color</span>
                                        <span
                                            class="material-icons text-gray-500 text-lg transition-transform">expand_less</span>
                                    </h4>
                                    <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                        @foreach ($filterOptions['colors'] as $color)
                                            <label class="flex items-center py-1 cursor-pointer group">
                                                <input type="checkbox" name="colors[]" value="{{ $color }}"
                                                    @checked(in_array($color, request('colors', [])))
                                                    class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                <span
                                                    class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ $color }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Primary Damage -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="$event.target.nextElementSibling.classList.toggle('hidden'); $event.target.querySelector('span').textContent = $event.target.nextElementSibling.classList.contains('hidden') ? 'expand_more' : 'expand_less'">
                                        <span>Primary Damage</span>
                                        <span
                                            class="material-icons text-gray-500 text-lg transition-transform">expand_less</span>
                                    </h4>
                                    <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                        @foreach ($filterOptions['primary_damage'] as $pd)
                                            <label class="flex items-center py-1 cursor-pointer group">
                                                <input type="checkbox" name="primary_damage[]"
                                                    value="{{ $pd }}" @checked(in_array($pd, request('primary_damage', [])))
                                                    class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                <span
                                                    class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ $pd }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Secondary Damage -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="$event.target.nextElementSibling.classList.toggle('hidden'); $event.target.querySelector('span').textContent = $event.target.nextElementSibling.classList.contains('hidden') ? 'expand_more' : 'expand_less'">
                                        <span>Secondary Damage</span>
                                        <span
                                            class="material-icons text-gray-500 text-lg transition-transform">expand_less</span>
                                    </h4>
                                    <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                        @foreach ($filterOptions['secondary_damage'] as $sd)
                                            <label class="flex items-center py-1 cursor-pointer group">
                                                <input type="checkbox" name="secondary_damage[]"
                                                    value="{{ $sd }}" @checked(in_array($sd, request('secondary_damage', [])))
                                                    class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                <span
                                                    class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ $sd }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Transmission -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="$event.target.nextElementSibling.classList.toggle('hidden'); $event.target.querySelector('span').textContent = $event.target.nextElementSibling.classList.contains('hidden') ? 'expand_more' : 'expand_less'">
                                        <span>Transmission</span>
                                        <span
                                            class="material-icons text-gray-500 text-lg transition-transform">expand_less</span>
                                    </h4>
                                    <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                        @foreach ($filterOptions['transmission'] as $tr)
                                            <label class="flex items-center py-1 cursor-pointer group">
                                                <input type="checkbox" name="transmission[]" value="{{ $tr }}"
                                                    @checked(in_array($tr, request('transmission', [])))
                                                    class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                <span
                                                    class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ $tr }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Title Condition -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="$event.target.nextElementSibling.classList.toggle('hidden'); $event.target.querySelector('span').textContent = $event.target.nextElementSibling.classList.contains('hidden') ? 'expand_more' : 'expand_less'">
                                        <span>Title Condition</span>
                                        <span
                                            class="material-icons text-gray-500 text-lg transition-transform">expand_less</span>
                                    </h4>
                                    <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                        @foreach ($filterOptions['title_status'] as $ts)
                                            <label class="flex items-center py-1 cursor-pointer group">
                                                <input type="checkbox" name="title_condition[]"
                                                    value="{{ $ts }}" @checked(in_array($ts, request('title_condition', [])))
                                                    class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                <span
                                                    class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ $ts }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5">
                                <button type="submit"
                                    class="w-full bg-primary-600 text-white font-medium py-2.5 px-4 rounded-lg text-sm hover:bg-primary-700 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg">
                                    Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </aside>

                <!-- Vehicle Listings -->
                <!-- Vehicle Listings -->
<section class="w-full lg:w-3/4">
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
                                        <span
                                            class="badge bg-primary-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Bid
                                            Now</span>
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
                                            <span class="truncate">{{ $listing->location ?? 'N/A' }}</span>
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
                                            @if (Auth::check())
                                                <a href="{{ route('auction.show', $listing->id) }}"
                                                    class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 text-sm transform hover:-translate-y-0.5 hover:shadow-md">
                                                    Bid Now
                                                </a>
                                            @else
                                                <button @click="openModal = true"
                                                    class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 text-sm transform hover:-translate-y-0.5 hover:shadow-md">
                                                    Bid Now
                                                </button>
                                            @endif
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
                    <div x-show="viewMode === 'detail'" class="detail-view grid grid-cols-1 gap-5" x-cloak>
                        @forelse($auctions as $listing)
                            <div
                                class="vehicle-card bg-white rounded-xl shadow-sm overflow-hidden flex flex-col md:flex-row animate-slide-down">
                                <!-- Image -->
                                <div class="md:w-2/5 relative image-container">
                                    @php
                                        $img = $listing->images->first();
                                        $imgUrl = $img
                                            ? (str_contains($img->image_path, '/')
                                                ? asset($img->image_path)
                                                : asset('uploads/listings/' . $img->image_path))
                                            : asset('images/placeholder-car.png');
                                    @endphp
                                    <img alt="{{ $listing->title ?? $listing->make . ' ' . $listing->model }}"
                                        style="height:250px" src="{{ $imgUrl }}"
                                        class="w-full h-full object-cover rounded-lg cursor-pointer transition-transform duration-300 hover:scale-105"
                                        onclick="openImageModal('{{ $imgUrl }}')" />
                                    <div class="image-overlay">
                                        <button
                                            class="view-details-btn bg-white text-primary-600 font-medium py-2 px-4 rounded-lg text-sm hover:bg-primary-50 transition-colors"
                                            onclick="openImageModal('{{ $imgUrl }}')">
                                            View Image
                                        </button>
                                    </div>
                                </div>

                                <!-- Info -->
                                <div class="p-5 flex-1 flex flex-col">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-lg font-semibold text-secondary-800">
                                            {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                        </h3>
                                        <div class="flex space-x-2">
                                            <span
                                                class="material-icons text-gray-400 hover:text-red-500 cursor-pointer transition-colors">favorite_border</span>
                                            <span
                                                class="material-icons text-gray-400 hover:text-primary-500 cursor-pointer transition-colors">share</span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                                        <div class="flex items-center">
                                            <span class="material-icons text-gray-400 text-sm mr-2">speed</span>
                                            <div>
                                                <p class="text-xs text-gray-500">Odometer</p>
                                                <p class="text-sm font-medium">
                                                    {{ $listing->odometer ? number_format($listing->odometer) . ' miles' : 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="material-icons text-gray-400 text-sm mr-2">receipt</span>
                                            <div>
                                                <p class="text-xs text-gray-500">Title Code</p>
                                                <p class="text-sm font-medium">{{ $listing->title_status ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="material-icons text-gray-400 text-sm mr-2">location_on</span>
                                            <div>
                                                <p class="text-xs text-gray-500">Location</p>
                                                <p class="text-sm font-medium">{{ $listing->location ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="material-icons text-gray-400 text-sm mr-2">event</span>
                                            <div>
                                                <p class="text-xs text-gray-500">Sale Date</p>
                                                <p class="text-sm font-medium">
                                                    {{ $listing->sale_date ? \Carbon\Carbon::parse($listing->sale_date)->format('M d, Y') : 'N/A' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Additional details -->
                                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-600">
                                        @if ($listing->primary_damage)
                                            <div class="flex"><span class="font-medium mr-1">Primary Damage:</span>
                                                <span>{{ $listing->primary_damage }}</span>
                                            </div>
                                        @endif
                                        @if ($listing->secondary_damage)
                                            <div class="flex"><span class="font-medium mr-1">Secondary Damage:</span>
                                                <span>{{ $listing->secondary_damage }}</span>
                                            </div>
                                        @endif
                                        @if ($listing->transmission)
                                            <div class="flex"><span class="font-medium mr-1">Transmission:</span>
                                                <span>{{ $listing->transmission }}</span>
                                            </div>
                                        @endif
                                        @if ($listing->color)
                                            <div class="flex"><span class="font-medium mr-1">Color:</span>
                                                <span>{{ $listing->color }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div
                                        class="mt-4 pt-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between">
                                        <div>
                                            <p class="text-xs text-gray-500">Current Bid</p>
                                            <p class="text-2xl font-bold text-green-600 price-tag">
                                                ${{ number_format($listing->current_bid ?? 0) }}
                                            </p>
                                        </div>
                                        <div class="mt-3 sm:mt-0 flex space-x-2">
                                            @if (Auth::check())
                                                <a href="{{ route('auction.show', $listing->id) }}"
                                                    class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-6 rounded-lg transition-all duration-300 text-sm transform hover:-translate-y-0.5 hover:shadow-md flex-1 sm:flex-none text-center">
                                                    Bid Now
                                                </a>
                                            @else
                                                <button @click="openModal = true"
                                                    class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2.5 px-6 rounded-lg transition-all duration-300 text-sm transform hover:-translate-y-0.5 hover:shadow-md flex-1 sm:flex-none">
                                                    Bid Now
                                                </button>
                                            @endif
                                            <a href="{{ route('auction.show', $listing->id) }}"
                                                class="border border-gray-300 text-secondary-700 font-medium py-2.5 px-3 rounded-lg hover:bg-gray-50 transition-all duration-300 text-sm transform hover:-translate-y-0.5 hover:shadow-sm">
                                                <span class="material-icons text-lg">visibility</span>
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

                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center animate-fade-in">
                        {{ $auctions->links('pagination::tailwind') }}
                    </div>
            </div>
            </section>

            <!-- Include Alpine.js if not already -->
            <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


            <script>
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

                // Close modal on ESC key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        closeImageModal();
                    }
                });

                // Filter section toggle functionality
                document.querySelectorAll('.filter-section h4').forEach(header => {
                    header.addEventListener('click', () => {
                        const content = header.nextElementSibling;
                        const icon = header.querySelector('span.material-icons');

                        if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                            content.style.maxHeight = '0';
                            content.style.opacity = '0';
                            icon.textContent = 'expand_more';
                            icon.style.transform = 'rotate(0deg)';
                        } else {
                            content.style.maxHeight = content.scrollHeight + 'px';
                            content.style.opacity = '1';
                            icon.textContent = 'expand_less';
                            icon.style.transform = 'rotate(180deg)';
                        }
                    });
                });

                // Initialize filter sections
                document.querySelectorAll('.filter-section > div').forEach(section => {
                    section.style.maxHeight = section.scrollHeight + 'px';
                    section.style.opacity = '1';
                    section.style.transition = 'max-height 0.3s ease, opacity 0.3s ease';
                });
            </script>
        @endsection
