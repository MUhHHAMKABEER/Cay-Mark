@extends('layouts.welcome')
@section('content')

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Listing Details - AutoMarket</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: {
                                DEFAULT: '#2563eb', // keep single color as fallback
                                50: '#eff6ff',
                                100: '#dbeafe',
                                500: '#3b82f6',
                                600: '#2563eb',
                                700: '#1d4ed8',
                                800: '#1e40af',
                                900: '#1e3a8a',
                            },
                            secondary: '#1e40af', // preserved from first config
                            accent: {
                                DEFAULT: '#f59e0b', // fallback for accent
                                yellow: '#f59e0b',
                                green: '#10b981',
                                red: '#ef4444',
                            },
                            dark: '#1f2937',
                            light: '#f9fafb',
                        },
                        animation: {
                            'fade-in': 'fadeIn 0.5s ease-out',
                            'scale-in': 'scaleIn 0.3s ease-out',
                        },
                        keyframes: {
                            fadeIn: {
                                '0%': {
                                    opacity: '0'
                                },
                                '100%': {
                                    opacity: '1'
                                },
                            },
                            scaleIn: {
                                '0%': {
                                    transform: 'scale(0.9)',
                                    opacity: '0'
                                },
                                '100%': {
                                    transform: 'scale(1)',
                                    opacity: '1'
                                },
                            },
                        },
                    },
                },
            }
        </script>
        <style type="text/css">
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

            .plan-card {
                transition: all 0.3s ease;
            }

            .plan-card:hover {
                transform: translateY(-5px);
            }

            .feature-item {
                position: relative;
                padding-left: 1.75rem;
            }

            .feature-item:before {
                content: '';
                position: absolute;
                left: 0;
                top: 0.35rem;
                width: 1.25rem;
                height: 1.25rem;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='%2310b981'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z' /%3E%3C/svg%3E");
                background-repeat: no-repeat;
            }

            .negative-item:before {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke-width='1.5' stroke='%23ef4444'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z' /%3E%3C/svg%3E");
            }

            .popular-badge {
                position: absolute;
                top: -12px;
                right: 20px;
                background: linear-gradient(45deg, #3b82f6, #1d4ed8);
                color: white;
                font-size: 0.75rem;
                font-weight: 600;
                padding: 0.25rem 1rem;
                border-radius: 9999px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            /* Animation for modal entry */
            @keyframes modalEntry {
                0% {
                    opacity: 0;
                    transform: scale(0.95) translateY(20px);
                }

                100% {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }

            .animate-modal {
                animation: modalEntry 0.3s ease-out forwards;
            }

            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

            body {
                font-family: 'Inter', sans-serif;
                background-color: #f5f7fa;
            }

            .gradient-bg {
                background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            }

            .image-gallery {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 1rem;
            }

            .main-image {
                grid-column: span 2;
                grid-row: span 2;
            }

            .info-card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .info-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .sticky-details {
                position: sticky;
                top: 140px;
            }

            .feature-tag {
                display: inline-flex;
                align-items: center;
                background-color: #eff6ff;
                color: #2563eb;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.875rem;
                margin-right: 0.5rem;
                margin-bottom: 0.5rem;
            }

            .countdown-timer {
                background: linear-gradient(45deg, #f59e0b, #ef4444);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                font-weight: 700;
            }

            .related-card {
                transition: all 0.3s ease;
            }

            .related-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
        </style>
    </head>

    <body class="min-h-screen bg-gray-50">
        <!-- Header -->


        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 py-8">
            <!-- Breadcrumbs -->
            <div class="flex items-center text-sm text-gray-500 mb-6">
                <a href="#" class="hover:text-primary">Home</a>
                <span class="mx-2">/</span>
                <a href="#" class="hover:text-primary">Vehicles</a>
                <span class="mx-2">/</span>
                <a href="#" class="hover:text-primary">Cars</a>
                <span class="mx-2">/</span>
                <span class="text-gray-700">Listing Details</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ showModal: false, currentIndex: 0, images: {{ $listing->images->toJson() }} }"
                @keydown.window.escape="showModal = false"
                @keydown.window.arrow-left="currentIndex = (currentIndex - 1 + images.length) % images.length"
                @keydown.window.arrow-right="currentIndex = (currentIndex + 1) % images.length">

                <!-- Left Column - Images & Description -->
                <div class="lg:col-span-2">
                    <!-- Image Gallery -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                        @if ($listing->images->count())
                            <div class="image-gallery p-4 grid grid-cols-2 md:grid-cols-3 gap-4">
                                {{-- Main Image --}}
                                <div class="col-span-2 md:col-span-3 rounded-lg overflow-hidden">
                                    <img src="{{ asset('uploads/listings/' . $listing->images[0]->image_path) }}"
                                        alt="Main listing image" class="w-full h-80 object-cover rounded-lg cursor-pointer"
                                        @click="showModal = true; currentIndex = 0">
                                </div>

                                {{-- Thumbnails (next 4 images) --}}
                                @foreach ($listing->images->slice(1, 4) as $index => $image)
                                    <div class="rounded-lg overflow-hidden">
                                        <img src="{{ asset('uploads/listings/' . $image->image_path) }}" alt="Listing image"
                                            class="w-full h-40 object-cover rounded-lg cursor-pointer"
                                            @click="showModal = true; currentIndex = {{ $index + 1 }}">
                                    </div>
                                @endforeach
                            </div>

                            {{-- View all button --}}
                            @if ($listing->images->count() > 5)
                                <div class="px-4 pb-4 text-center">
                                    <button
                                        class="text-primary hover:text-secondary font-medium flex items-center justify-center mx-auto"
                                        @click="showModal = true; currentIndex = 0">
                                        <i class="fas fa-images mr-2"></i>
                                        View All {{ $listing->images->count() }} Photos
                                    </button>
                                </div>
                            @endif
                        @else
                            <div class="h-64 bg-gray-200 flex items-center justify-center rounded-lg">
                                <div class="text-center text-gray-500">
                                    <i class="fas fa-image text-4xl mb-2"></i>
                                    <p>No images available for this listing.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Modal -->
                    <div x-show="showModal" x-transition
                        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50"
                        @click.self="showModal = false">

                        <div class="relative w-full max-w-5xl mx-auto px-4">
                            <template x-for="(img, i) in images" :key="i">
                                <img x-show="currentIndex === i" :src="'/uploads/listings/' + img.image_path"
                                    class="w-full max-h-[80vh] object-contain rounded-lg">
                            </template>

                            <!-- Close Button -->
                            <button @click="showModal = false"
                                class="absolute top-3 right-3 bg-white rounded-full p-4 shadow">
                                ✕
                            </button>

                            <!-- Prev Button -->
                            <button @click="currentIndex = (currentIndex - 1 + images.length) % images.length"
                                class="absolute left-3 top-1/2 -translate-y-1/2 bg-white rounded-full p-3 shadow">
                                ‹
                            </button>

                            <!-- Next Button -->
                            <button @click="currentIndex = (currentIndex + 1) % images.length"
                                class="absolute right-3 top-1/2 -translate-y-1/2 bg-white rounded-full p-3 shadow">
                                ›
                            </button>
                        </div>
                    </div>

                    <!-- Description Card -->
                    <div class="bg-white rounded-xl shadow-md p-6 mb-6 info-card">
                        <h2 class="text-xl font-bold mb-4 text-dark flex items-center">
                            <i class="fas fa-file-alt mr-2 text-primary"></i>
                            Description
                        </h2>
                        <p class="text-gray-700 leading-relaxed">{{ $listing->description }}</p>
                    </div>

                    <!-- Specifications Card -->
                    <div class="bg-white rounded-xl shadow-md p-6 info-card">
                        <h2 class="text-xl font-bold mb-4 text-dark flex items-center">
                            <i class="fas fa-list-alt mr-2 text-primary"></i>
                            Technical Specifications
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-4">
                                <div>
                                    <h3 class="font-medium text-gray-500">Vehicle Information</h3>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Year</span>
                                            <span class="font-medium">{{ $listing->year }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Make & Model</span>
                                            <span class="font-medium">{{ $listing->make }} {{ $listing->model }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Trim</span>
                                            <span class="font-medium">{{ $listing->trim }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Color</span>
                                            <span class="font-medium">{{ $listing->color }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-medium text-gray-500">Mechanical</h3>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Fuel Type</span>
                                            <span class="font-medium">{{ $listing->fuel_type }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Transmission</span>
                                            <span class="font-medium">{{ $listing->transmission }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Engine Type</span>
                                            <span class="font-medium">{{ $listing->engine_type }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <h3 class="font-medium text-gray-500">Condition</h3>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Overall Condition</span>
                                            <span class="font-medium">{{ $listing->condition }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Title Status</span>
                                            <span class="font-medium">{{ $listing->title_status }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Primary Damage</span>
                                            <span class="font-medium">{{ $listing->primary_damage }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Keys Available</span>
                                            <span class="font-medium flex items-center">
                                                @if ($listing->keys_available == 1)
                                                    <i class="fas fa-check-circle text-green-600 mr-1"></i> Yes
                                                @else
                                                    <i class="fas fa-times-circle text-red-600 mr-1"></i> No
                                                @endif
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <div>
                                    <h3 class="font-medium text-gray-500">Listing Details</h3>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Listing Method</span>
                                            <span class="font-medium">{{ $listing->listing_method }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Category</span>
                                            <span class="font-medium">{{ $listing->major_category }} /
                                                {{ $listing->subcategory }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Location</span>
                                            <span class="font-medium">{{ $listing->location }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Action Panel -->
                <div class="lg:col-span-1">
                    <div class="sticky-details">
                        <!-- Price & Actions Card -->
                        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h2 class="text-3xl font-bold text-dark">${{ number_format($listing->price, 2) }}</h2>
                                    <p class="text-gray-500">Current Price</p>
                                </div>
                                @if ($listing->listing_method === 'auction')
                                    <div class="text-right">
                                        <div class="countdown-timer text-xl">2d 14h 33m</div>
                                        <p class="text-gray-500 text-sm">Auction ends</p>
                                    </div>
                                @endif
                            </div>

                            <div x-data="{ planModal: false }" class="space-y-3">
                                <!-- Buttons -->
                                @auth
                                    <!-- User is logged in -->
                                    {{-- <form method="POST" action="{{ route('listing.buy', $listing->id) }}" class="w-full"> --}}
                                    {{-- @csrf --}}
                                    <button type="button" id="buy-now-btn"
    class="block w-full bg-primary hover:bg-secondary text-white text-center py-3 px-4 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-md flex items-center justify-center">
    <i class="fas fa-shopping-cart mr-2"></i>
    Buy Now
</button>

<script>
document.getElementById('buy-now-btn').addEventListener('click', function () {
    fetch("{{ route('listing.buy', $listing->id) }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Accept": "application/json",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({})
    })
    .then(async res => {
        if (!res.ok) {
            let err = await res.text();
            throw new Error(err || "Server error");
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            alert("✅ " + data.message);
        } else {
            alert("⚠️ " + data.message);
        }
    })
    .catch(err => {
        console.error("Checkout failed:", err);
        alert("⚠️ Something went wrong while starting checkout. Check console for details.");
    });
});
</script>



                                    {{-- </form> --}}

                                    <!-- Add to Watchlist -->
                                    <form method="POST" action="{{ route('listing.watchlist', $listing->id) }}"
                                        class="w-full mt-2">
                                        @csrf
                                        <button type="submit"
                                            class="block w-full bg-white border border-gray-300 hover:border-primary text-dark hover:text-primary text-center py-3 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                                            <i
                                                class="fas fa-heart mr-2 {{ auth()->user()->watchlist()->where('listing_id', $listing->id)->exists() ? 'text-red-500' : '' }}"></i>
                                            {{ auth()->user()->watchlist()->where('listing_id', $listing->id)->exists() ? 'Remove from Watchlist' : 'Add to Watchlist' }}
                                        </button>
                                    </form>
                                @else
                                    <!-- User is not logged in -->
                                    <button @click="planModal = true"
                                        class="block w-full bg-primary hover:bg-secondary text-white text-center py-3 px-4 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-md flex items-center justify-center">
                                        <i class="fas fa-shopping-cart mr-2"></i>
                                        Buy Now
                                    </button>

                                    <button @click="planModal = true"
                                        class="block w-full bg-white border border-gray-300 hover:border-primary text-dark hover:text-primary text-center py-3 px-4 rounded-lg transition-all duration-300 flex items-center justify-center mt-2">
                                        <i class="fas fa-heart mr-2"></i>
                                        Add to Watchlist
                                    </button>
                                                                    <!-- Modal -->
<div x-data="{ showRegisterModal: false }" x-show="showRegisterModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-60 backdrop-blur-sm p-4"
     style="display: none;" @click.self="showRegisterModal = false">

    <div class="bg-white rounded-2xl shadow-2xl max-w-xl w-full p-6 md:p-8 relative animate-fade-in" @click.stop>

        <!-- Close Button -->
        <button @click="showRegisterModal = false"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Content -->
        <div class="text-center">
            <div class="flex items-center justify-center mb-4">
                <i class="fas fa-exclamation-circle text-primary-600 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Oops! You Need to Register First</h2>
            <p class="text-gray-500 mb-6">To place a bid or purchase items, please create an account. It's quick and easy!</p>
            <a href="{{ route('register') }}">
                <button class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg text-sm transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    Register Now
                </button>
            </a>
        </div>
    </div>
</div>
                                @endauth




                                <!-- Modal -->


<script>
    function openRegisterModal() {
        document.querySelector('[x-data]').__x.$data.showRegisterModal = true;
    }
</script>

                            </div>
                            <!-- Seller Info Card -->
                            {{-- <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                                <h3 class="font-bold text-lg text-dark mb-4">Seller Information</h3>
                                <div class="flex items-center mb-4">
                                    <div
                                        class="h-12 w-12 rounded-full bg-primary flex items-center justify-center text-white font-bold mr-3">
                                        AM
                                    </div>
                                    <div>
                                        <h4 class="font-medium">AutoMarket Pro</h4>
                                        <p class="text-sm text-gray-500">Member since 2018</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                                        <p class="text-xl font-bold text-primary">98%</p>
                                        <p class="text-xs text-gray-600">Positive Rating</p>
                                    </div>
                                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                                        <p class="text-xl font-bold text-primary">127</p>
                                        <p class="text-xs text-gray-600">Items Sold</p>
                                    </div>
                                </div>
                                <button
                                    class="w-full bg-gray-100 hover:bg-gray-200 text-dark text-center py-2 px-4 rounded-lg transition">
                                    <i class="fas fa-comment mr-2"></i>
                                    Contact Seller
                                </button>
                            </div> --}}
                        </div>
                    </div>
                </div>


                <!-- Related Listings Section -->


        </main>
        @php
            use App\Models\Listing;

            // Fetch similar listings directly in Blade
            $similarListings = Listing::with('images')
                ->where('major_category', $listing->major_category) // match category
                ->where('id', '!=', $listing->id)
                ->where('listing_state','active') // exclude current one
                ->take(6) // limit results
                ->get();
        @endphp

        <section class="mt-12">
            <div class="max-w-6xl mx-auto"> <!-- Center container with max width -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-dark">Similar Listings</h2>
                    <a href="#" class="text-primary hover:text-secondary font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($similarListings as $similar)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden related-card">
                            <!-- Image -->
                            <div class="h-48 w-full bg-gray-200 relative">
                                @if ($similar->images->count())
                                    <img src="{{ asset('uploads/listings/' . $similar->images[0]->image_path) }}"
                                        alt="{{ $similar->title }}" class="w-full h-48 object-cover">
                                @else
                                    <img src="https://source.unsplash.com/400x300/?car" alt="Default Car Image"
                                        class="w-full h-48 object-cover">
                                @endif
                            </div>




                            <!-- Info -->
                            <div class="p-4">
                                <h3 class="font-bold text-lg mb-1">{{ $similar->make }} {{ $similar->model }}</h3>
                                <p class="text-gray-500 text-sm mb-3">
                                    {{ $similar->body_type ?? 'N/A' }} |
                                    {{ number_format($similar->mileage ?? 0) }} miles
                                </p>

                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-xl">${{ number_format($similar->price, 0) }}</span>
                                    <button class="text-primary hover:text-secondary">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-3 text-gray-500">No similar listings found in this category.</p>
                    @endforelse
                </div>
            </div>
        </section>


        <!-- Footer -->


        <script>
            // Simple countdown timer for demonstration
            function updateCountdown() {
                const countdownEl = document.querySelector('.countdown-timer');
                if (countdownEl) {
                    // For demo purposes, we're just setting a static time
                    countdownEl.textContent = '2d 14h 33m';
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                updateCountdown();

                // Add to watchlist animation
                const watchlistButtons = document.querySelectorAll('a, button');
                watchlistButtons.forEach(button => {
                    if (button.textContent.includes('Watchlist') || button.innerHTML.includes('fa-heart')) {
                        button.addEventListener('click', function(e) {
                            if (this.innerHTML.includes('fa-heart')) {
                                this.innerHTML = this.innerHTML.replace('fa-heart', 'fa-check');
                                this.classList.add('text-green-500');
                                setTimeout(() => {
                                    this.innerHTML = this.innerHTML.replace('fa-check',
                                        'fa-heart');
                                    this.classList.remove('text-green-500');
                                }, 1500);
                            }
                        });
                    }
                });
            });

            function openModal() {
                document.querySelector('[x-data]').__x.$data.planModal = true;
            }

            // For demo purposes - open modal automatically
            setTimeout(openModal, 300);



</script>



    </body>


@endsection
