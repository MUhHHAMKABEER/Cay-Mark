@extends('layouts.welcome')
@section('title', 'Upgrade to Business Seller - CayMark')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 py-12 px-4 relative">

    <!-- Background pattern -->
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <div class="absolute inset-0" style="background-image:radial-gradient(circle at 2px 2px,#063466 1px,transparent 0);background-size:40px 40px;"></div>
    </div>

    <div class="container mx-auto max-w-4xl relative z-10">

        <!-- Header -->
        <div class="text-center mb-10">
            <div class="inline-block mb-5">
                <div class="bg-white rounded-2xl px-10 py-7 shadow-xl border border-gray-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#063466] via-[#1e3a8a] to-[#2563eb] mb-2">
                        Upgrade to Business Seller
                    </h1>
                    <p class="text-gray-500 text-base font-medium">Choose the plan that fits your business — annual membership with full benefits.</p>
                </div>
            </div>
        </div>

        <!-- Flash / Errors -->
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 rounded-xl px-5 py-4 mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-red-800 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-xl px-5 py-4 mb-6">
                <p class="font-semibold text-yellow-800 mb-2">Please fix the following:</p>
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="text-yellow-700 text-sm flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full flex-shrink-0"></span>{{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Plan Selection Form -->
        <form method="POST" action="{{ route('upgrade.membership.store') }}">
            @csrf
            <input type="hidden" name="role" value="seller">

            <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-[#063466] to-[#1e3a8a] px-8 py-5">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></span>
                        <span class="text-white font-semibold text-lg">Select Your Business Seller Plan</span>
                    </div>
                </div>

                <div class="p-8">
                    @if($packages->isEmpty())
                        <div class="text-center py-16 text-gray-400">
                            <svg class="w-14 h-14 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="font-semibold text-lg">No business seller plans available right now.</p>
                            <p class="text-sm mt-1">Please contact support for assistance.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-{{ $packages->count() === 1 ? '1' : '2' }} gap-6">
                            @foreach($packages as $i => $pkg)
                                @php
                                    $features = is_array($pkg->features)
                                        ? $pkg->features
                                        : (is_string($pkg->features) ? json_decode($pkg->features, true) : []);
                                    $features = is_array($features) ? $features : [];
                                    $isFirst  = $i === 0;
                                @endphp

                                <label class="block cursor-pointer group">
                                    <input type="radio" name="package_id" value="{{ $pkg->id }}" class="sr-only peer" {{ $isFirst ? 'checked' : '' }} required>
                                    <div class="relative p-7 border-2 rounded-2xl transition-all duration-300 cursor-pointer
                                        border-gray-200 hover:border-[#063466] hover:shadow-xl
                                        peer-checked:border-[#063466] peer-checked:shadow-2xl peer-checked:bg-gradient-to-br peer-checked:from-blue-50 peer-checked:to-indigo-50
                                        {{ $isFirst ? 'border-[#063466] shadow-xl bg-gradient-to-br from-blue-50 to-indigo-50' : '' }}">

                                        <!-- Selected indicator -->
                                        <div class="package-radio-indicator absolute top-4 right-4 w-6 h-6 border-2 border-gray-300 rounded-full peer-checked:border-[#063466] peer-checked:bg-[#063466] transition-all duration-200
                                            {{ $isFirst ? 'border-[#063466] bg-[#063466]' : '' }} flex items-center justify-center">
                                            <svg class="package-radio-check w-3.5 h-3.5 text-white {{ $isFirst ? '' : 'opacity-0' }} transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>

                                        <!-- Plan badge -->
                                        <div class="inline-flex items-center gap-1.5 bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full mb-4">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            Business Seller
                                        </div>

                                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $pkg->title }}</h3>

                                        <div class="flex items-baseline gap-1 mb-4">
                                            <span class="text-3xl font-extrabold text-[#063466]">${{ number_format($pkg->price, 2) }}</span>
                                            @if($pkg->duration_days)
                                                <span class="text-gray-400 text-sm font-medium">/ {{ $pkg->duration_days }} days</span>
                                            @else
                                                <span class="text-gray-400 text-sm font-medium">/ year</span>
                                            @endif
                                        </div>

                                        @if(!empty($features))
                                            <ul class="space-y-2">
                                                @foreach($features as $feat)
                                                    <li class="flex items-start gap-2 text-sm text-gray-700">
                                                        <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        {{ $feat }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        @if($pkg->max_listings)
                                            <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-3 text-xs text-gray-500">
                                                <div>
                                                    <span class="font-semibold text-gray-700">{{ $pkg->max_listings }}</span> total listings
                                                </div>
                                                @if($pkg->max_listings_per_month)
                                                <div>
                                                    <span class="font-semibold text-gray-700">{{ $pkg->max_listings_per_month }}</span>/month
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @error('package_id')
                        <p class="text-red-600 mt-4 text-sm font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Trust strip -->
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-800">Secure Payment</p>
                    <p class="text-xs text-gray-400 mt-0.5">Bank-level encryption</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-800">Verified Platform</p>
                    <p class="text-xs text-gray-400 mt-0.5">Trusted marketplace</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-gray-800">24/7 Support</p>
                    <p class="text-xs text-gray-400 mt-0.5">Always here to help</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('seller.dashboard') }}"
                   class="w-full sm:w-auto px-8 py-3.5 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-bold text-base hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Dashboard
                </a>
                @unless($packages->isEmpty())
                <button type="submit"
                        class="w-full sm:w-auto px-10 py-3.5 bg-gradient-to-r from-[#063466] to-[#1e3a8a] text-white rounded-xl font-bold text-base shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-200 flex items-center justify-center gap-2">
                    Continue to Payment
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                @endunless
            </div>

        </form>
    </div>
</div>

@push('styles')
<style>
    label:has(input[name="package_id"]:checked) .package-radio-indicator {
        border-color: #063466 !important;
        background-color: #063466 !important;
    }
    label:has(input[name="package_id"]:checked) .package-radio-check {
        opacity: 1 !important;
    }
</style>
@endpush
@endsection
