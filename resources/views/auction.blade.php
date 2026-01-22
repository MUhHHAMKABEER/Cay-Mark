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

        <main class="w-full px-4 py-6">

            <!-- Page Header -->
            <div class="mb-6 animate-fade-in">
                <h2 class="text-2xl md:text-3xl font-bold text-secondary-800 mb-2">Repairable, Salvage and Wrecked Car
                    Auctions</h2>
                <div class="flex flex-col md:flex-row md:items-center justify-between">
                    <p class="text-sm text-secondary-500 mb-2 md:mb-0 results-count">
                        Showing results {{ $auctions->firstItem() ?? 0 }} - {{ $auctions->lastItem() ?? 0 }} of
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
                            <select id="sort" x-model="sortBy" @change="applyFilters()"
                                class="border border-gray-300 rounded-lg py-1.5 px-3 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                                <option value="newest">Newest First</option>
                                <option value="price_low">Price: Low to High</option>
                                <option value="price_high">Price: High to Low</option>
                                <option value="ending_soon">Ending Soonest</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6" x-data="filterData()" x-init="initFilters()">
                <!-- Filters Sidebar -->
                <aside class="w-full lg:w-1/4">
                    <div
                        class="bg-white rounded-xl shadow-sm p-5 mb-6 sticky top-4 transition-all duration-300 hover:shadow-md">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="font-semibold text-secondary-800 text-lg">Filter Vehicles</h3>
                            <button type="button" @click="clearAllFilters()"
                                class="text-sm text-primary-600 font-medium hover:text-primary-800 transition-colors">Clear
                                All</button>
                        </div>

                        <div class="space-y-5 max-h-[70vh] overflow-y-auto filter-scroll pr-2" id="filterForm">
                                <!-- 1. Location -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="toggleFilter('location')">
                                        <span>Location</span>
                                        <span class="material-icons text-gray-500 text-lg transition-transform" x-bind:class="filtersOpen.location ? 'rotate-180' : ''">expand_less</span>
                                    </h4>
                                    <div x-show="filtersOpen.location" class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                        @foreach ($filterOptions['locations'] as $location)
                                            <label class="flex items-center py-1 cursor-pointer group">
                                                <input type="checkbox" name="location[]" value="{{ $location }}"
                                                    x-model="selectedFilters.location"
                                                    @change="applyFilters()"
                                                    class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                <span
                                                    class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ $location }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- 2. Vehicle Type -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="toggleFilter('vehicle_type')">
                                        <span>Vehicle Type</span>
                                        <span class="material-icons text-gray-500 text-lg transition-transform" x-bind:class="filtersOpen.vehicle_type ? 'rotate-180' : ''">expand_less</span>
                                    </h4>
                                    <div x-show="filtersOpen.vehicle_type">
                                        <select name="vehicle_type" x-model="selectedFilters.vehicle_type" @change="applyFilters()"
                                            class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                            <option value="">All Types</option>
                                            @foreach ($filterOptions['vehicle_types'] as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- 3. Make -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="toggleFilter('make')">
                                        <span>Make</span>
                                        <span class="material-icons text-gray-500 text-lg transition-transform" x-bind:class="filtersOpen.make ? 'rotate-180' : ''">expand_less</span>
                                    </h4>
                                    <div x-show="filtersOpen.make">
                                        <input type="text" x-model="makeSearch" placeholder="Search Makes..."
                                            class="w-full border rounded-lg p-2 mb-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                        <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                            <template x-for="make in filteredMakes" :key="make">
                                                <label class="flex items-center py-1 cursor-pointer group">
                                                    <input type="checkbox" :value="make"
                                                        x-model="selectedFilters.makes"
                                                        @change="applyFilters()"
                                                        class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                    <span class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors" x-text="make"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. Model -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="toggleFilter('model')">
                                        <span>Model</span>
                                        <span class="material-icons text-gray-500 text-lg transition-transform" x-bind:class="filtersOpen.model ? 'rotate-180' : ''">expand_less</span>
                                    </h4>
                                    <div x-show="filtersOpen.model">
                                        <input type="text" x-model="modelSearch" placeholder="Search Models..."
                                            class="w-full border rounded-lg p-2 mb-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                        <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                            <template x-for="model in filteredModels" :key="model">
                                                <label class="flex items-center py-1 cursor-pointer group">
                                                    <input type="checkbox" :value="model"
                                                        x-model="selectedFilters.models"
                                                        @change="applyFilters()"
                                                        class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                    <span class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors" x-text="model"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. Year (Range Slider) -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2">Year</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center space-x-2">
                                            <input type="number" x-model.number="yearFrom" @input.debounce.500ms="applyFilters()"
                                                :min="1900" :max="yearTo || {{ date('Y') + 1 }}"
                                                placeholder="From"
                                                class="w-1/2 border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                            <input type="number" x-model.number="yearTo" @input.debounce.500ms="applyFilters()"
                                                :min="yearFrom || 1900" :max="{{ date('Y') + 1 }}"
                                                placeholder="To"
                                                class="w-1/2 border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                        </div>
                                        <div class="px-2">
                                            <input type="range" x-model.number="yearFrom" @input.debounce.500ms="applyFilters()"
                                                :min="1900" :max="yearTo || {{ date('Y') + 1 }}"
                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                            <input type="range" x-model.number="yearTo" @input.debounce.500ms="applyFilters()"
                                                :min="yearFrom || 1900" :max="{{ date('Y') + 1 }}"
                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer mt-2">
                                        </div>
                                    </div>
                                </div>

                                <!-- 6. Odometer (Range Slider) -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2">Odometer</h4>
                                    <div class="space-y-3">
                                        <div class="flex items-center space-x-2">
                                            <input type="number" x-model.number="odometerMin" @input.debounce.500ms="applyFilters()"
                                                :min="0" :max="odometerMax || {{ $filterOptions['odometer_max'] ?? 250000 }}"
                                                placeholder="Min"
                                                class="w-1/2 border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                            <input type="number" x-model.number="odometerMax" @input.debounce.500ms="applyFilters()"
                                                :min="odometerMin || 0" :max="{{ $filterOptions['odometer_max'] ?? 250000 }}"
                                                placeholder="Max"
                                                class="w-1/2 border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                        </div>
                                        <div class="px-2">
                                            <input type="range" x-model.number="odometerMin" @input.debounce.500ms="applyFilters()"
                                                :min="0" :max="odometerMax || {{ $filterOptions['odometer_max'] ?? 250000 }}"
                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                                            <input type="range" x-model.number="odometerMax" @input.debounce.500ms="applyFilters()"
                                                :min="odometerMin || 0" :max="{{ $filterOptions['odometer_max'] ?? 250000 }}"
                                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer mt-2">
                                        </div>
                                        <p class="text-xs text-gray-500 text-center" x-text="`${odometerMin || 0} mi - ${odometerMax || {{ $filterOptions['odometer_max'] ?? 250000 }}} mi`"></p>
                                    </div>
                                </div>

                                <!-- 7. Damage Type -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="toggleFilter('damage_type')">
                                        <span>Damage Type</span>
                                        <span class="material-icons text-gray-500 text-lg transition-transform" x-bind:class="filtersOpen.damage_type ? 'rotate-180' : ''">expand_less</span>
                                    </h4>
                                    <div x-show="filtersOpen.damage_type" class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                        @foreach ($filterOptions['damage_types'] as $damage)
                                            <label class="flex items-center py-1 cursor-pointer group">
                                                <input type="checkbox" name="damage_type[]" value="{{ $damage }}"
                                                    x-model="selectedFilters.damage_type"
                                                    @change="applyFilters()"
                                                    class="rounded text-primary-600 focus:ring-primary-500 mr-2 transition-all group-hover:scale-110">
                                                <span
                                                    class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">{{ $damage }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- 8. Body Style -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="toggleFilter('body_style')">
                                        <span>Body Style</span>
                                        <span class="material-icons text-gray-500 text-lg transition-transform" x-bind:class="filtersOpen.body_style ? 'rotate-180' : ''">expand_less</span>
                                    </h4>
                                    <div x-show="filtersOpen.body_style">
                                        <select name="body_style" x-model="selectedFilters.body_style" @change="applyFilters()"
                                            class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                            <option value="">All Body Styles</option>
                                            @foreach ($filterOptions['body_styles'] as $style)
                                                <option value="{{ $style }}">{{ $style }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- 9. Engine Type -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="toggleFilter('engine_type')">
                                        <span>Engine Type</span>
                                        <span class="material-icons text-gray-500 text-lg transition-transform" x-bind:class="filtersOpen.engine_type ? 'rotate-180' : ''">expand_less</span>
                                    </h4>
                                    <div x-show="filtersOpen.engine_type">
                                        <select name="engine_type" x-model="selectedFilters.engine_type" @change="applyFilters()"
                                            class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                            <option value="">All Engine Types</option>
                                            @foreach ($filterOptions['engine_types'] as $engine)
                                                <option value="{{ $engine }}">{{ $engine }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- 10. Cylinders -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="toggleFilter('cylinders')">
                                        <span>Cylinders</span>
                                        <span class="material-icons text-gray-500 text-lg transition-transform" x-bind:class="filtersOpen.cylinders ? 'rotate-180' : ''">expand_less</span>
                                    </h4>
                                    <div x-show="filtersOpen.cylinders">
                                        <select name="cylinders" x-model="selectedFilters.cylinders" @change="applyFilters()"
                                            class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                            <option value="">All</option>
                                            @foreach ($filterOptions['cylinders'] as $cyl)
                                                <option value="{{ $cyl }}">{{ $cyl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- 11. Transmission -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="toggleFilter('transmission')">
                                        <span>Transmission</span>
                                        <span class="material-icons text-gray-500 text-lg transition-transform" x-bind:class="filtersOpen.transmission ? 'rotate-180' : ''">expand_less</span>
                                    </h4>
                                    <div x-show="filtersOpen.transmission" class="space-y-2">
                                        <label class="flex items-center py-2 cursor-pointer group">
                                            <input type="radio" name="transmission" value="Automatic"
                                                x-model="selectedFilters.transmission"
                                                @change="applyFilters()"
                                                class="text-primary-600 focus:ring-primary-500 mr-2">
                                            <span class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">Automatic</span>
                                        </label>
                                        <label class="flex items-center py-2 cursor-pointer group">
                                            <input type="radio" name="transmission" value="Manual"
                                                x-model="selectedFilters.transmission"
                                                @change="applyFilters()"
                                                class="text-primary-600 focus:ring-primary-500 mr-2">
                                            <span class="text-sm text-secondary-600 group-hover:text-secondary-800 transition-colors">Manual</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- 12. Drive Train -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="toggleFilter('drive_train')">
                                        <span>Drive Train</span>
                                        <span class="material-icons text-gray-500 text-lg transition-transform" x-bind:class="filtersOpen.drive_train ? 'rotate-180' : ''">expand_less</span>
                                    </h4>
                                    <div x-show="filtersOpen.drive_train">
                                        <select name="drive_train" x-model="selectedFilters.drive_train" @change="applyFilters()"
                                            class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                            <option value="">All Drive Trains</option>
                                            @foreach ($filterOptions['drive_trains'] as $dt)
                                                <option value="{{ $dt }}">{{ $dt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- 13. Fuel Type -->
                                <div class="filter-section">
                                    <h4 class="text-sm font-medium text-secondary-800 mb-2 flex justify-between items-center cursor-pointer"
                                        @click="toggleFilter('fuel_type')">
                                        <span>Fuel Type</span>
                                        <span class="material-icons text-gray-500 text-lg transition-transform" x-bind:class="filtersOpen.fuel_type ? 'rotate-180' : ''">expand_less</span>
                                    </h4>
                                    <div x-show="filtersOpen.fuel_type">
                                        <select name="fuel_type" x-model="selectedFilters.fuel_type" @change="applyFilters()"
                                            class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                            <option value="">All Fuel Types</option>
                                            @foreach ($filterOptions['fuel_types'] as $fuel)
                                                <option value="{{ $fuel }}">{{ $fuel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
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
                                            @if (Auth::check())
                                                <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
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
                    <div x-show="viewMode === 'detail'" class="detail-view grid grid-cols-1 gap-5" id="auctionListings" x-cloak>
                        @include('partials.auction-listings', ['auctions' => $auctions])
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center animate-fade-in" id="auctionPagination">
                        @include('partials.auction-pagination', ['auctions' => $auctions])
                    </div>
            </div>
            </section>

            <!-- Include Alpine.js if not already -->
            <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


            <script>
                function filterData() {
                    return {
                        isLoading: false,
                        filtersOpen: {
                            location: true,
                            vehicle_type: false,
                            make: false,
                            model: false,
                            damage_type: false,
                            body_style: false,
                            engine_type: false,
                            cylinders: false,
                            transmission: false,
                            drive_train: false,
                            fuel_type: false,
                        },
                        selectedFilters: {
                            location: @json(request('location', [])),
                            vehicle_type: '{{ request('vehicle_type', '') }}',
                            makes: @json(request('makes', [])),
                            models: @json(request('models', [])),
                            damage_type: @json(request('damage_type', [])),
                            body_style: '{{ request('body_style', '') }}',
                            engine_type: '{{ request('engine_type', '') }}',
                            cylinders: '{{ request('cylinders', '') }}',
                            transmission: '{{ request('transmission', '') }}',
                            drive_train: '{{ request('drive_train', '') }}',
                            fuel_type: '{{ request('fuel_type', '') }}',
                        },
                        yearFrom: {{ request('year_from', 1900) }},
                        yearTo: {{ request('year_to', date('Y') + 1) }},
                        odometerMin: {{ request('odometer_min', 0) }},
                        odometerMax: {{ request('odometer_max', $filterOptions['odometer_max'] ?? 250000) }},
                        sortBy: '{{ request('sort', 'newest') }}',
                        makeSearch: '',
                        modelSearch: '',
                        allMakes: @json($filterOptions['makes']),
                        allModels: @json($filterOptions['models']),
                        
                        get filteredMakes() {
                            if (!this.makeSearch) return this.allMakes;
                            return this.allMakes.filter(make => 
                                make.toLowerCase().includes(this.makeSearch.toLowerCase())
                            );
                        },
                        
                        get filteredModels() {
                            if (!this.modelSearch) return this.allModels;
                            return this.allModels.filter(model => 
                                model.toLowerCase().includes(this.modelSearch.toLowerCase())
                            );
                        },
                        
                        toggleFilter(filterName) {
                            this.filtersOpen[filterName] = !this.filtersOpen[filterName];
                        },
                        
                        initFilters() {
                            // Initialize from URL params if present
                        },
                        
                        clearAllFilters() {
                            this.selectedFilters = {
                                location: [],
                                vehicle_type: '',
                                makes: [],
                                models: [],
                                damage_type: [],
                                body_style: '',
                                engine_type: '',
                                cylinders: '',
                                transmission: '',
                                drive_train: '',
                                fuel_type: '',
                            };
                            this.yearFrom = 1900;
                            this.yearTo = {{ date('Y') + 1 }};
                            this.odometerMin = 0;
                            this.odometerMax = {{ $filterOptions['odometer_max'] ?? 250000 }};
                            this.sortBy = 'newest';
                            this.applyFilters();
                        },
                        
                        applyFilters() {
                            this.isLoading = true;
                            
                            const params = new URLSearchParams();
                            
                            // Add array filters
                            if (this.selectedFilters.location.length > 0) {
                                this.selectedFilters.location.forEach(loc => params.append('location[]', loc));
                            }
                            if (this.selectedFilters.makes.length > 0) {
                                this.selectedFilters.makes.forEach(make => params.append('makes[]', make));
                            }
                            if (this.selectedFilters.models.length > 0) {
                                this.selectedFilters.models.forEach(model => params.append('models[]', model));
                            }
                            if (this.selectedFilters.damage_type.length > 0) {
                                this.selectedFilters.damage_type.forEach(damage => params.append('damage_type[]', damage));
                            }
                            
                            // Add single value filters
                            if (this.selectedFilters.vehicle_type) params.append('vehicle_type', this.selectedFilters.vehicle_type);
                            if (this.selectedFilters.body_style) params.append('body_style', this.selectedFilters.body_style);
                            if (this.selectedFilters.engine_type) params.append('engine_type', this.selectedFilters.engine_type);
                            if (this.selectedFilters.cylinders) params.append('cylinders', this.selectedFilters.cylinders);
                            if (this.selectedFilters.transmission) params.append('transmission', this.selectedFilters.transmission);
                            if (this.selectedFilters.drive_train) params.append('drive_train', this.selectedFilters.drive_train);
                            if (this.selectedFilters.fuel_type) params.append('fuel_type', this.selectedFilters.fuel_type);
                            
                            // Add range filters
                            if (this.yearFrom && this.yearFrom > 1900) params.append('year_from', this.yearFrom);
                            if (this.yearTo && this.yearTo < {{ date('Y') + 1 }}) params.append('year_to', this.yearTo);
                            if (this.odometerMin && this.odometerMin > 0) params.append('odometer_min', this.odometerMin);
                            if (this.odometerMax && this.odometerMax < {{ $filterOptions['odometer_max'] ?? 250000 }}) params.append('odometer_max', this.odometerMax);
                            
                            // Add sort
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
                                    
                                    // Update results count
                                    const resultsText = document.querySelector('.results-count');
                                    if (resultsText) {
                                        resultsText.textContent = `Showing results 1 - ${data.count} of ${data.count}`;
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
