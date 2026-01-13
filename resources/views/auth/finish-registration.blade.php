@extends('layouts.welcome')
@section('title', 'Select Your Membership - CayMark')
@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 py-12 px-4 relative">
    <!-- Professional Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, #063466 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>

    <div class="container mx-auto max-w-6xl relative z-10">
        <!-- Professional Header Section -->
        <div class="text-center mb-12 animate-fade-in">
            <div class="inline-block mb-6">
                <div class="bg-white rounded-2xl px-10 py-6 shadow-xl border border-gray-100">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-xl flex items-center justify-center shadow-lg transform rotate-3 hover:rotate-0 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#063466] via-[#1e3a8a] to-[#2563eb] mb-3">
                        Select Your Membership
                    </h1>
                    <p class="text-gray-600 text-lg font-medium">Choose the perfect plan that fits your needs</p>
                </div>
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-center space-x-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-900">Step 1: Account Created</p>
                    </div>
                </div>
                <div class="w-16 h-1 bg-gray-200 rounded-full">
                    <div class="w-full h-full bg-gradient-to-r from-[#063466] to-[#1e3a8a] rounded-full"></div>
                </div>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-full flex items-center justify-center shadow-lg border-4 border-white">
                        <span class="text-white font-bold text-lg">2</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-900">Step 2: Select Membership</p>
                    </div>
                </div>
                <div class="w-16 h-1 bg-gray-200 rounded-full"></div>
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                        <span class="text-gray-500 font-bold text-lg">3</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-gray-500">Step 3: Verification</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-8 rounded-xl shadow-sm animate-fade-in">
                <div class="flex items-start">
                    <svg class="h-6 w-6 text-red-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-red-800 font-semibold">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-8 rounded-xl shadow-sm animate-fade-in">
                <div class="flex items-start">
                    <svg class="h-6 w-6 text-yellow-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <div>
                        <p class="text-yellow-800 font-semibold mb-2">Please fix the following errors:</p>
                        <ul class="text-yellow-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-center">
                                    <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-2"></span>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('finish.registration.membership') }}" id="membership-form">
            @csrf

            <!-- Role Selection Card -->
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden mb-8 animate-slide-up">
                <!-- Professional Top Bar -->
                <div class="bg-gradient-to-r from-[#063466] to-[#1e3a8a] px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-white font-semibold">I want to register as a:</span>
                        </div>
                    </div>
                </div>

                <div class="p-8 md:p-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Buyer Option -->
                        <label class="relative cursor-pointer group h-full flex">
                            <input type="radio" name="role" value="buyer" class="sr-only peer" required onchange="loadPackages('buyer')">
                            <div class="relative p-8 border-2 border-gray-300 rounded-2xl transition-all duration-300 group-hover:border-[#2563eb] group-hover:shadow-xl peer-checked:border-[#063466] peer-checked:bg-gradient-to-br peer-checked:from-blue-50 peer-checked:to-indigo-50 peer-checked:shadow-2xl transform peer-checked:scale-105 h-full w-full flex flex-col">
                                <div class="absolute top-4 right-4 w-6 h-6 border-2 border-gray-400 rounded-full peer-checked:border-[#063466] peer-checked:bg-[#063466] transition-all duration-300">
                                    <svg class="w-full h-full text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex flex-col items-center text-center flex-1 justify-center">
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Buyer</h3>
                                    <p class="text-gray-600 text-lg leading-relaxed">Browse marketplace and participate in exclusive auctions</p>
                                    <div class="mt-4 flex items-center text-[#063466]">
                                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-sm font-semibold">Best for Purchasing</span>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Seller Option -->
                        <label class="relative cursor-pointer group h-full flex">
                            <input type="radio" name="role" value="seller" class="sr-only peer" required onchange="loadPackages('seller')">
                            <div class="relative p-8 border-2 border-gray-300 rounded-2xl transition-all duration-300 group-hover:border-green-500 group-hover:shadow-xl peer-checked:border-[#063466] peer-checked:bg-gradient-to-br peer-checked:from-green-50 peer-checked:to-emerald-50 peer-checked:shadow-2xl transform peer-checked:scale-105 h-full w-full flex flex-col">
                                <div class="absolute top-4 right-4 w-6 h-6 border-2 border-gray-400 rounded-full peer-checked:border-[#063466] peer-checked:bg-[#063466] transition-all duration-300">
                                    <svg class="w-full h-full text-white opacity-0 peer-checked:opacity-100 transition-opacity duration-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex flex-col items-center text-center flex-1 justify-center">
                                    <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Seller</h3>
                                    <p class="text-gray-600 text-lg leading-relaxed">List vehicles for sale or auction to reach global buyers</p>
                                    <div class="mt-4 flex items-center text-[#063466]">
                                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-sm font-semibold">Best for Selling</span>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('role')
                        <p class="text-red-600 mt-4 text-center font-semibold flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Package Selection Card -->
            <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden mb-8 animate-slide-up">
                <!-- Professional Top Bar -->
                <div class="bg-gradient-to-r from-[#063466] to-[#1e3a8a] px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-white font-semibold">Choose Your Membership Package</span>
                        </div>
                    </div>
                </div>

                <div class="p-8 md:p-10">
                    <div id="package-selection" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2 text-center py-12">
                            <div class="inline-block bg-gray-50 rounded-2xl p-8 border border-gray-200">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-gray-600 text-lg font-semibold">Please select a role above to see available packages</p>
                            </div>
                        </div>
                    </div>
                    @error('package_id')
                        <p class="text-red-600 mt-4 text-center font-semibold flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
                <a href="{{ route('dashboard.default') }}" 
                   class="w-full sm:w-auto px-10 py-4 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-bold text-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel
                </a>
                <button type="submit" id="submit-btn" disabled
                    class="w-full sm:w-auto px-10 py-4 bg-gradient-to-r from-[#063466] to-[#1e3a8a] text-white rounded-xl font-bold text-lg shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    Continue to Verification
                    <svg class="w-6 h-6 ml-3 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>

            <!-- Trust Indicators -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-lg text-center transform hover:scale-105 transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-lg mb-2">Secure & Protected</h3>
                    <p class="text-gray-600 text-sm">Bank-level encryption</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-lg text-center transform hover:scale-105 transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-lg mb-2">Verified Platform</h3>
                    <p class="text-gray-600 text-sm">Trusted by thousands</p>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-lg text-center transform hover:scale-105 transition-all duration-300">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-gray-900 font-bold text-lg mb-2">24/7 Support</h3>
                    <p class="text-gray-600 text-sm">Always here to help</p>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    @keyframes fade-in {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    .animate-fade-in {
        animation: fade-in 0.6s ease-out forwards;
    }
    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-slide-up {
        animation: slide-up 0.8s ease-out forwards;
    }
</style>
@endpush

@push('scripts')
<script>
    let selectedRole = null;
    let selectedPackage = null;

    async function loadPackages(role) {
        selectedRole = role;
        const container = document.getElementById('package-selection');
        const submitBtn = document.getElementById('submit-btn');
        
        // Show loading state
        container.innerHTML = `
            <div class="col-span-2 text-center py-12">
                <div class="inline-block bg-gray-50 rounded-2xl p-8 border border-gray-200">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-300 border-t-[#063466] mx-auto mb-4"></div>
                    <p class="text-gray-600 text-lg font-semibold">Loading packages...</p>
                </div>
            </div>
        `;

        submitBtn.disabled = true;

        try {
            const response = await fetch(`/api/packages/${role}`);
            if (!response.ok) throw new Error('Failed to load packages');
            const packages = await response.json();

            if (!Array.isArray(packages) || packages.length === 0) {
                container.innerHTML = `
                    <div class="col-span-2 text-center py-12">
                        <div class="inline-block bg-gray-50 rounded-2xl p-8 border border-gray-200">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-gray-600 text-lg font-semibold">No packages available for this role.</p>
                        </div>
                    </div>
                `;
                return;
            }

            container.innerHTML = '';
            packages.forEach((pkg, index) => {
                const card = createPackageCard(pkg, role, index);
                container.appendChild(card);
            });
        } catch (error) {
            console.error('Failed to load packages:', error);
            container.innerHTML = `
                <div class="col-span-2 text-center py-12">
                    <div class="inline-block bg-red-50 rounded-2xl p-8 border border-red-200">
                        <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-red-600 text-lg font-semibold">Failed to load packages. Please try again.</p>
                    </div>
                </div>
            `;
        }
    }

    function createPackageCard(pkg, role, index) {
        const label = document.createElement('label');
        label.className = 'block cursor-pointer group h-full';

        const price = pkg.price > 0 ? `$${Number(pkg.price).toFixed(2)}` : 'Free';
        const isFree = pkg.price == 0;
        const isBusinessSeller = role === 'seller' && pkg.price > 0;
        const features = Array.isArray(pkg.features) ? pkg.features : (typeof pkg.features === 'string' ? JSON.parse(pkg.features) : []);

        const input = document.createElement('input');
        input.type = 'radio';
        input.name = 'package_id';
        input.value = pkg.id;
        input.className = 'sr-only';
        input.required = true;

        input.addEventListener('change', () => {
            document.querySelectorAll('#package-selection label').forEach(l => {
                l.querySelector('.package-card').classList.remove('ring-4', 'ring-[#063466]', 'bg-gradient-to-br', 'from-blue-50', 'to-indigo-50', 'scale-105');
            });
            if (input.checked) {
                label.querySelector('.package-card').classList.add('ring-4', 'ring-[#063466]', 'bg-gradient-to-br', 'from-blue-50', 'to-indigo-50', 'scale-105');
                selectedPackage = pkg.id;
                submitBtn.disabled = false;
            }
        });

        label.innerHTML = `
            <div class="package-card relative bg-white border-2 border-gray-300 rounded-2xl p-8 transition-all duration-500 hover:border-[#063466] hover:shadow-xl transform h-full flex flex-col">
                <div class="absolute top-6 right-6 w-8 h-8 border-2 border-gray-400 rounded-full transition-all duration-300 group-hover:border-[#063466]">
                    <svg class="w-full h-full text-[#063466] opacity-0 transition-opacity duration-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div class="flex-1">
                        <div class="flex items-center mb-3">
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 mr-3">${escapeHtml(pkg.title || 'Package')}</h3>
                            ${isFree ? '<span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold border border-green-300">FREE</span>' : ''}
                            ${isBusinessSeller ? '<span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-bold border border-purple-300">PREMIUM</span>' : ''}
                        </div>
                        ${pkg.description ? `<p class="text-gray-600 text-lg">${escapeHtml(pkg.description)}</p>` : ''}
                    </div>
                    <div class="mt-4 md:mt-0 md:ml-6">
                        <div class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-[#063466] to-[#1e3a8a]">
                            ${price}
                        </div>
                        ${pkg.duration_days ? `<p class="text-gray-500 text-sm text-center mt-2">per ${pkg.duration_days === 365 ? 'year' : pkg.duration_days + ' days'}</p>` : '<p class="text-gray-500 text-sm text-center mt-2">one-time</p>'}
                    </div>
                </div>

                ${features.length > 0 ? `
                    <div class="border-t border-gray-200 pt-6 flex-1 flex flex-col">
                        <h4 class="text-gray-900 font-semibold mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#063466]" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            Features Included
                        </h4>
                        <ul class="space-y-3 flex-1">
                            ${features.slice(0, 5).map(feature => `
                                <li class="flex items-start text-gray-700">
                                    <svg class="w-5 h-5 text-[#063466] mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>${escapeHtml(feature)}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                ` : ''}

                ${isFree ? `
                    <div class="mt-auto bg-green-50 border border-green-200 rounded-xl p-4">
                        <p class="text-green-800 text-sm font-semibold flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            No payment required at registration. You'll pay per listing when you submit items.
                        </p>
                    </div>
                ` : ''}
            </div>
        `;

        label.insertBefore(input, label.firstChild);
        return label;
    }

    function escapeHtml(str) {
        if (str === null || typeof str === 'undefined') return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    // Update submit button state when package is selected
    document.addEventListener('change', function(e) {
        if (e.target.name === 'package_id') {
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = false;
        }
    });
</script>
@endpush

@endsection
