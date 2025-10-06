@extends('layouts.welcome')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        /* Custom animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        .animate-slide-in { animation: slideIn 0.4s ease-out; }
        .animate-zoom-in { animation: zoomIn 0.3s ease-out; }

        /* Card hover effects */
        .vehicle-card {
            transition: all 0.3s ease;
            transform: translateY(0);
        }

        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        /* View toggle styles */
        .view-toggle-btn {
            transition: all 0.2s ease;
        }

        .view-toggle-btn.active {
            background-color: #4f46e5;
            color: white;
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

        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Filter improvements */
        .filter-section {
            transition: all 0.3s ease;
        }

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
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }

        .price-tag:hover::before {
            left: 100%;
        }

        /* Modal styles */
        .modal-enter {
            animation: modalEnter 0.4s ease-out forwards;
        }

        @keyframes modalEnter {
            0% { opacity: 0; transform: translateY(-20px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .carousel-enter {
            animation: carouselEnter 0.5s ease-out forwards;
        }

        @keyframes carouselEnter {
            0% { opacity: 0; transform: translateX(20px); }
            100% { opacity: 1; transform: translateX(0); }
        }

        .modal-container {
            transform: perspective(1000px) rotateX(5deg) translateZ(0);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset,
                0 10px 30px -10px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .carousel-image {
            transition: transform 0.3s ease;
        }

        .carousel-image:hover {
            transform: scale(1.02);
        }

        .thumbnail {
            transition: all 0.2s ease;
        }

        .thumbnail:hover,
        .thumbnail.active {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close-btn {
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            transform: rotate(90deg);
        }

        .carousel-btn {
            transition: all 0.3s ease;
            opacity: 0.7;
        }

        .carousel-btn:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        /* Grid view specific */
        .grid-view .vehicle-card {
            animation: fadeIn 0.5s ease forwards;
        }

        .grid-view .vehicle-card:nth-child(odd) {
            animation-delay: 0.05s;
        }

        .grid-view .vehicle-card:nth-child(even) {
            animation-delay: 0.1s;
        }

        /* Detail view specific */
        .detail-view .vehicle-card {
            animation: slideIn 0.4s ease forwards;
        }

        /* Line clamp utility */
        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
        }

        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
    </style>

    <div class="container mx-auto px-4 py-6 bg-gray-50"
         x-data="{ viewMode: 'grid', isLoading: false, activeFilters: false }"
         x-init="
            // Check if user has a preference saved
            if (localStorage.getItem('marketplaceViewMode')) {
                viewMode = localStorage.getItem('marketplaceViewMode');
            }
            $watch('viewMode', value => {
                isLoading = true;
                localStorage.setItem('marketplaceViewMode', value);
                // Simulate loading delay for smooth transition
                setTimeout(() => { isLoading = false; }, 300);
            });
         ">

        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8 animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Marketplace - Buy Now</h1>
                <p class="text-gray-600 mt-2">Find your perfect vehicle from our extensive inventory</p>
            </div>

            <!-- View Toggle -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center bg-white rounded-lg border border-gray-300 overflow-hidden shadow-sm">
                    <button
                        @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'active bg-indigo-600 text-white' : 'text-gray-600'"
                        class="view-toggle-btn px-4 py-2 flex items-center transition-all duration-200">
                        <i class="fas fa-th-large text-sm mr-2"></i>
                        <span class="text-sm font-medium">Grid</span>
                    </button>
                    <button
                        @click="viewMode = 'detail'"
                        :class="viewMode === 'detail' ? 'active bg-indigo-600 text-white' : 'text-gray-600'"
                        class="view-toggle-btn px-4 py-2 flex items-center transition-all duration-200">
                        <i class="fas fa-list text-sm mr-2"></i>
                        <span class="text-sm font-medium">List</span>
                    </button>
                </div>

                <!-- Results Count -->
                <div class="text-sm text-gray-500">
                    <span x-show="!isLoading">{{ $listings->total() }} results</span>
                    <span x-show="isLoading" class="flex items-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                    </span>
                </div>
            </div>
        </div>

        <div class="lg:flex lg:space-x-8">
            <!-- FILTERS SIDEBAR -->
            <aside class="w-full lg:w-1/4 mb-8 lg:mb-0">
                <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4 transition-all duration-300 hover:shadow-xl">
                    <div class="flex justify-between items-center mb-6">
                        <h5 class="text-xl font-bold text-gray-900">Vehicle Filters</h5>
                        <a href="{{ route('marketplace.index') }}" class="text-indigo-600 text-sm font-medium hover:underline transition-colors">Clear all</a>
                    </div>

                    <form id="filtersForm" method="GET" action="{{ route('marketplace.index') }}" class="space-y-6 max-h-[70vh] overflow-y-auto filter-scroll pr-2">
                        <!-- Type -->
                        <div class="filter-section">
                            <label class="block text-sm font-semibold mb-3 text-gray-800">Type</label>
                            <p class="text-xs text-gray-500 mb-3">Choose one or more</p>
                            <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                @foreach ($filterOptions['types'] as $type)
                                    <div class="flex items-center mb-2 group">
                                        <input class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-all group-hover:scale-110"
                                               type="checkbox" name="type[]" value="{{ $type }}"
                                               id="type_{{ \Illuminate\Support\Str::slug($type) }}"
                                               {{ in_array($type, (array) request('type', [])) ? 'checked' : '' }}>
                                        <label for="type_{{ \Illuminate\Support\Str::slug($type) }}"
                                               class="ml-2 text-gray-700 text-sm group-hover:text-gray-900 transition-colors cursor-pointer">
                                            {{ $type }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Makes (search + checkboxes) -->
                        <div class="filter-section">
                            <label class="block text-sm font-semibold mb-3 text-gray-800">Makes</label>
                            <input type="text" id="makesSearch"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-3 transition-all"
                                placeholder="Search makes...">
                            <div id="makesList" class="max-h-40 overflow-y-auto filter-scroll space-y-2">
                                @foreach ($filterOptions['makes'] as $make)
                                    <div class="flex items-center mb-2 group">
                                        <input class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-all group-hover:scale-110"
                                               type="checkbox" name="makes[]" value="{{ $make }}"
                                               id="make_{{ \Illuminate\Support\Str::slug($make) }}"
                                               {{ in_array($make, (array) request('makes', [])) ? 'checked' : '' }}>
                                        <label for="make_{{ \Illuminate\Support\Str::slug($make) }}"
                                               class="ml-2 text-gray-700 text-sm group-hover:text-gray-900 transition-colors cursor-pointer">
                                            {{ $make }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Models -->
                        <div class="filter-section">
                            <label class="block text-sm font-semibold mb-3 text-gray-800">Models</label>
                            <input type="text" id="modelsSearch"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-3 transition-all"
                                placeholder="Search models...">
                            <div id="modelsList" class="max-h-40 overflow-y-auto filter-scroll space-y-2">
                                @foreach ($filterOptions['models'] as $model)
                                    <div class="flex items-center mb-2 group">
                                        <input class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-all group-hover:scale-110"
                                               type="checkbox" name="models[]" value="{{ $model }}"
                                               id="model_{{ \Illuminate\Support\Str::slug($model) }}"
                                               {{ in_array($model, (array) request('models', [])) ? 'checked' : '' }}>
                                        <label for="model_{{ \Illuminate\Support\Str::slug($model) }}"
                                               class="ml-2 text-gray-700 text-sm group-hover:text-gray-900 transition-colors cursor-pointer">
                                            {{ $model }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Locations -->
                        <div class="filter-section">
                            <label class="block text-sm font-semibold mb-3 text-gray-800">Locations</label>
                            <input type="text" id="locationsSearch"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-3 transition-all"
                                placeholder="Search locations...">
                            <div id="locationsList" class="max-h-40 overflow-y-auto filter-scroll space-y-2">
                                @foreach ($filterOptions['locations'] as $loc)
                                    <div class="flex items-center mb-2 group">
                                        <input class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-all group-hover:scale-110"
                                               type="checkbox" name="locations[]" value="{{ $loc }}"
                                               id="loc_{{ \Illuminate\Support\Str::slug($loc) }}"
                                               {{ in_array($loc, (array) request('locations', [])) ? 'checked' : '' }}>
                                        <label for="loc_{{ \Illuminate\Support\Str::slug($loc) }}"
                                               class="ml-2 text-gray-700 text-sm group-hover:text-gray-900 transition-colors cursor-pointer">
                                            {{ $loc }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Year -->
                        <div class="filter-section">
                            <label class="block text-sm font-semibold mb-3 text-gray-800">Year</label>
                            <div class="flex space-x-3">
                                <input type="number" name="year_from"
                                    class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                    placeholder="From (YYYY)" value="{{ request('year_from') }}">
                                <input type="number" name="year_to"
                                    class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                    placeholder="To (YYYY)" value="{{ request('year_to') }}">
                            </div>
                        </div>

                        <!-- Odometer dual range -->
                        <div class="filter-section">
                            <label class="block text-sm font-semibold mb-3 text-gray-800">Odometer (km)</label>
                            <div class="flex space-x-3 mb-3">
                                <input type="number" id="odoMinInput" name="odo_min"
                                    class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                    placeholder="Min" value="{{ request('odo_min', 0) }}">
                                <input type="number" id="odoMaxInput" name="odo_max"
                                    class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                                    placeholder="Max" value="{{ request('odo_max', 200000) }}">
                            </div>
                            <div class="space-y-2">
                                <input type="range" id="odoMinRange" min="0" max="400000" step="100"
                                    class="w-full h-2 bg-indigo-100 rounded-lg appearance-none cursor-pointer"
                                    value="{{ request('odo_min', 0) }}">
                                <input type="range" id="odoMaxRange" min="0" max="400000" step="100"
                                    class="w-full h-2 bg-indigo-100 rounded-lg appearance-none cursor-pointer"
                                    value="{{ request('odo_max', 200000) }}">
                            </div>
                        </div>

                        <!-- Color -->
                        <div class="filter-section">
                            <label class="block text-sm font-semibold mb-3 text-gray-800">Color</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($filterOptions['colors'] as $color)
                                    <div class="flex items-center group">
                                        <input class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-all group-hover:scale-110"
                                               type="checkbox" name="colors[]" value="{{ $color }}"
                                               id="color_{{ \Illuminate\Support\Str::slug($color) }}"
                                               {{ in_array($color, (array) request('colors', [])) ? 'checked' : '' }}>
                                        <label for="color_{{ \Illuminate\Support\Str::slug($color) }}"
                                               class="ml-1 text-gray-700 text-sm group-hover:text-gray-900 transition-colors cursor-pointer">
                                            {{ $color }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Primary Damage -->
                        <div class="filter-section">
                            <label class="block text-sm font-semibold mb-3 text-gray-800">Primary Damage Type</label>
                            <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                @foreach ($filterOptions['primary_damage'] as $pd)
                                    <div class="flex items-center group">
                                        <input class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-all group-hover:scale-110"
                                               type="checkbox" name="primary_damage[]" value="{{ $pd }}"
                                               id="pd_{{ \Illuminate\Support\Str::slug($pd) }}"
                                               {{ in_array($pd, (array) request('primary_damage', [])) ? 'checked' : '' }}>
                                        <label for="pd_{{ \Illuminate\Support\Str::slug($pd) }}"
                                               class="ml-2 text-gray-700 text-sm group-hover:text-gray-900 transition-colors cursor-pointer">
                                            {{ $pd }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Secondary Damage -->
                        <div class="filter-section">
                            <label class="block text-sm font-semibold mb-3 text-gray-800">Secondary Damage Type</label>
                            <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                @foreach ($filterOptions['secondary_damage'] as $sd)
                                    <div class="flex items-center group">
                                        <input class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-all group-hover:scale-110"
                                               type="checkbox" name="secondary_damage[]" value="{{ $sd }}"
                                               id="sd_{{ \Illuminate\Support\Str::slug($sd) }}"
                                               {{ in_array($sd, (array) request('secondary_damage', [])) ? 'checked' : '' }}>
                                        <label for="sd_{{ \Illuminate\Support\Str::slug($sd) }}"
                                               class="ml-2 text-gray-700 text-sm group-hover:text-gray-900 transition-colors cursor-pointer">
                                            {{ $sd }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Transmission -->
                        <div class="filter-section">
                            <label class="block text-sm font-semibold mb-3 text-gray-800">Transmission</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($filterOptions['transmission'] as $t)
                                    <div class="flex items-center group">
                                        <input class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-all group-hover:scale-110"
                                               type="checkbox" name="transmission[]" value="{{ $t }}"
                                               id="trans_{{ \Illuminate\Support\Str::slug($t) }}"
                                               {{ in_array($t, (array) request('transmission', [])) ? 'checked' : '' }}>
                                        <label for="trans_{{ \Illuminate\Support\Str::slug($t) }}"
                                               class="ml-1 text-gray-700 text-sm group-hover:text-gray-900 transition-colors cursor-pointer">
                                            {{ $t }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Title Condition -->
                        <div class="filter-section">
                            <label class="block text-sm font-semibold mb-3 text-gray-800">Title Condition</label>
                            <div class="space-y-2 max-h-40 overflow-y-auto filter-scroll">
                                @foreach ($filterOptions['title_status'] as $ts)
                                    <div class="flex items-center group">
                                        <input class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition-all group-hover:scale-110"
                                               type="checkbox" name="title_condition[]" value="{{ $ts }}"
                                               id="ts_{{ \Illuminate\Support\Str::slug($ts) }}"
                                               {{ in_array($ts, (array) request('title_condition', [])) ? 'checked' : '' }}>
                                        <label for="ts_{{ \Illuminate\Support\Str::slug($ts) }}"
                                               class="ml-2 text-gray-700 text-sm group-hover:text-gray-900 transition-colors cursor-pointer">
                                            {{ $ts }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-3 pt-4">
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg">
                                Apply Filters
                            </button>
                            <a href="{{ route('marketplace.index') }}"
                                class="flex-1 px-4 py-3 border border-indigo-600 text-indigo-600 text-sm font-semibold rounded-lg hover:bg-indigo-50 text-center transition-all duration-300 transform hover:-translate-y-0.5">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- LISTINGS -->
            <main class="w-full lg:w-3/4" x-data="{ open: false, selected: {} }">
                <!-- Loading Skeleton (shown during view transition) -->
                <template x-if="isLoading">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template x-for="i in 6" :key="i">
                            <div class="bg-white rounded-xl shadow p-4 animate-pulse">
                                <div class="h-48 skeleton rounded-lg mb-4"></div>
                                <div class="h-4 skeleton rounded w-3/4 mb-2"></div>
                                <div class="h-3 skeleton rounded w-1/2 mb-3"></div>
                                <div class="h-8 skeleton rounded w-full"></div>
                            </div>
                        </template>
                    </div>
                </template>
                

                <!-- Grid View -->
                <div x-show="viewMode === 'grid' && !isLoading" class="grid-view grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" x-cloak>
                    @forelse($listings as $listing)
                        <div class="vehicle-card bg-white rounded-xl shadow-lg overflow-hidden flex flex-col h-full animate-zoom-in">
                            <div class="image-container relative">
                                @php
                                    $img = $listing->images->first();
                                    $imgUrl = $img
                                        ? (str_contains($img->image_path, '/')
                                            ? asset($img->image_path)
                                            : asset('uploads/listings/' . $img->image_path))
                                        : asset('images/placeholder-car.png');
                                @endphp
                                <img src="{{ $imgUrl }}" alt="listing image" class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">

                                <!-- Image Overlay -->
                                <div class="image-overlay">
                                    <a href="{{ route('listing.show', $listing->id) }}"
                                       class="view-details-btn bg-white text-indigo-600 font-medium py-2 px-4 rounded-lg text-sm hover:bg-indigo-50 transition-colors">
                                        View Details
                                    </a>
                                </div>

                                <!-- Badges -->
                                <div class="absolute top-3 left-3 flex flex-col space-y-1">
                                    @if($listing->featured)
                                        <span class="badge bg-red-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Featured</span>
                                    @endif
                                    <span class="badge bg-indigo-500 text-white px-2 py-1 rounded-full text-xs font-semibold">Buy Now</span>
                                </div>
                            </div>

                            <div class="p-4 flex-1 flex flex-col">
                                <h5 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1">{{ $listing->make }} {{ $listing->model }}</h5>
                                <p class="text-sm text-gray-500 mb-3">{{ $listing->year }} • {{ $listing->location ?? '—' }}</p>

                                <div class="grid grid-cols-2 gap-2 mb-4 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <i class="fas fa-tachometer-alt text-gray-400 text-xs mr-1"></i>
                                        <span>{{ $listing->odometer ? number_format($listing->odometer) . ' km' : 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-palette text-gray-400 text-xs mr-1"></i>
                                        <span class="truncate">{{ $listing->color ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-cog text-gray-400 text-xs mr-1"></i>
                                        <span class="truncate">{{ $listing->transmission ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-file-alt text-gray-400 text-xs mr-1"></i>
                                        <span class="truncate">{{ $listing->title_status ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                @if($listing->price)
                                    <div class="mt-auto pt-3 border-t border-gray-200">
                                        <p class="text-xs text-gray-500">Price</p>
                                        <p class="text-xl font-bold text-green-600 price-tag">
                                            ${{ number_format($listing->price) }}
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4 border-t border-gray-200">
                                <a href="{{ route('listing.show', $listing->id) }}"
                                   class="block text-center px-4 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-md">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 animate-fade-in">
                            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No listings found</h3>
                            <p class="text-gray-500">Try adjusting your filters to find more results.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Detailed View -->
                <div x-show="viewMode === 'detail' && !isLoading" class="detail-view grid grid-cols-1 gap-6" x-cloak>
                    @forelse($listings as $listing)
                        <div class="vehicle-card bg-white rounded-xl shadow-lg overflow-hidden flex flex-col md:flex-row animate-slide-in">
                            <div class="md:w-2/5 image-container">
                                @php
                                    $img = $listing->images->first();
                                    $imgUrl = $img
                                        ? (str_contains($img->image_path, '/')
                                            ? asset($img->image_path)
                                            : asset('uploads/listings/' . $img->image_path))
                                        : asset('images/placeholder-car.png');
                                @endphp
                                <img src="{{ $imgUrl }}" alt="listing image" class="w-full h-64 md:h-full object-cover transition-transform duration-300 hover:scale-105">

                                <!-- Image Overlay -->
                                <div class="image-overlay">
                                    <a href="{{ route('listing.show', $listing->id) }}"
                                       class="view-details-btn bg-white text-indigo-600 font-medium py-2 px-4 rounded-lg text-sm hover:bg-indigo-50 transition-colors">
                                        View Details
                                    </a>
                                </div>
                            </div>

                            <div class="p-6 flex-1 flex flex-col">
                                <div class="flex justify-between items-start mb-2">
                                    <h5 class="text-xl font-bold text-gray-900">{{ $listing->make }} {{ $listing->model }} {{ $listing->year }}</h5>
                                    <div class="flex space-x-2">
                                        <button class="text-gray-400 hover:text-red-500 transition-colors">
                                            <i class="far fa-heart"></i>
                                        </button>
                                        <button class="text-gray-400 hover:text-indigo-500 transition-colors">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                    </div>
                                </div>

                                <p class="text-gray-500 mb-4">{{ $listing->location ?? '—' }}</p>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-tachometer-alt text-gray-400 mr-2"></i>
                                        <div>
                                            <p class="text-xs text-gray-500">Odometer</p>
                                            <p class="text-sm font-medium">{{ $listing->odometer ? number_format($listing->odometer) . ' km' : 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-palette text-gray-400 mr-2"></i>
                                        <div>
                                            <p class="text-xs text-gray-500">Color</p>
                                            <p class="text-sm font-medium">{{ $listing->color ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-cog text-gray-400 mr-2"></i>
                                        <div>
                                            <p class="text-xs text-gray-500">Transmission</p>
                                            <p class="text-sm font-medium">{{ $listing->transmission ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-file-alt text-gray-400 mr-2"></i>
                                        <div>
                                            <p class="text-xs text-gray-500">Title</p>
                                            <p class="text-sm font-medium">{{ $listing->title_status ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional details for detailed view -->
                                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600">
                                    @if($listing->primary_damage)
                                        <div class="flex">
                                            <span class="font-medium mr-1">Primary Damage:</span>
                                            <span>{{ $listing->primary_damage }}</span>
                                        </div>
                                    @endif
                                    @if($listing->secondary_damage)
                                        <div class="flex">
                                            <span class="font-medium mr-1">Secondary Damage:</span>
                                            <span>{{ $listing->secondary_damage }}</span>
                                        </div>
                                    @endif
                                    @if($listing->fuel_type)
                                        <div class="flex">
                                            <span class="font-medium mr-1">Fuel Type:</span>
                                            <span>{{ $listing->fuel_type }}</span>
                                        </div>
                                    @endif
                                    @if($listing->engine)
                                        <div class="flex">
                                            <span class="font-medium mr-1">Engine:</span>
                                            <span>{{ $listing->engine }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-auto pt-4 border-t border-gray-200 flex flex-col md:flex-row md:items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-500">Price</p>
                                        <p class="text-2xl font-bold text-green-600 price-tag">
                                            ${{ number_format($listing->price ?? 0) }}
                                        </p>
                                    </div>
                                    <div class="mt-3 md:mt-0 flex space-x-3">
                                        <a href="{{ route('listing.show', $listing->id) }}"
                                           class="px-6 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-md">
                                            View Details
                                        </a>
                                        <button class="px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-300 transform hover:-translate-y-0.5">
                                            <i class="far fa-heart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 animate-fade-in">
                            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No listings found</h3>
                            <p class="text-gray-500">Try adjusting your filters to find more results.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($listings->hasPages())
                    <div class="mt-8 flex justify-center animate-fade-in">
                        {{ $listings->links('pagination::tailwind') }}
                    </div>
                @endif
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function wireSearch(inputId, listId) {
                const input = document.getElementById(inputId);
                const list = document.getElementById(listId);
                if (!input || !list) return;
                input.addEventListener('input', function() {
                    const q = input.value.trim().toLowerCase();
                    Array.from(list.querySelectorAll('.flex')).forEach(function(row) {
                        const label = row.querySelector('label').textContent.toLowerCase();
                        row.style.display = label.indexOf(q) === -1 ? 'none' : '';
                    });
                });
            }
            wireSearch('makesSearch', 'makesList');
            wireSearch('modelsSearch', 'modelsList');
            wireSearch('locationsSearch', 'locationsList');

            const minRange = document.getElementById('odoMinRange');
            const maxRange = document.getElementById('odoMaxRange');
            const minInput = document.getElementById('odoMinInput');
            const maxInput = document.getElementById('odoMaxInput');

            function clampRanges() {
                let minVal = parseInt(minRange.value);
                let maxVal = parseInt(maxRange.value);
                if (minVal > maxVal)[minVal, maxVal] = [maxVal, minVal];
                minRange.value = minVal;
                maxRange.value = maxVal;
                minInput.value = minVal;
                maxInput.value = maxVal;
            }

            if (minRange && maxRange) {
                minRange.addEventListener('input', clampRanges);
                maxRange.addEventListener('input', clampRanges);
            }
            if (minInput && maxInput) {
                minInput.addEventListener('change', () => {
                    minRange.value = minInput.value;
                    clampRanges();
                });
                maxInput.addEventListener('change', () => {
                    maxRange.value = maxInput.value;
                    clampRanges();
                });
            }
        });
    </script>
@endsection
