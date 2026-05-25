@extends('layouts.dashboard')
@section('title', 'Upgrade to Business Seller - CayMark')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-6">
    <div class="max-w-3xl mx-auto">

        <!-- Page header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-xl flex items-center justify-center shadow">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Upgrade to Business Seller</h1>
                    <p class="text-sm text-gray-500">Select a plan to unlock business features, higher listing limits, and analytics.</p>
                </div>
            </div>
        </div>

        <!-- Progress indicator -->
        <div class="flex items-center gap-3 mb-8">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-[#063466] flex items-center justify-center text-white text-xs font-bold">1</div>
                <span class="text-sm font-semibold text-[#063466]">Select Plan</span>
            </div>
            <div class="flex-1 h-0.5 bg-gray-200 rounded-full"></div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 text-xs font-bold">2</div>
                <span class="text-sm font-medium text-gray-400">Documents & Payment</span>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 mb-5 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-red-800 text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-4 mb-5">
                <p class="font-semibold text-yellow-800 text-sm mb-2">Please fix the following:</p>
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="text-yellow-700 text-sm flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full flex-shrink-0"></span>{{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Plan selection form -->
        <form method="POST" action="{{ route('upgrade.membership.store') }}">
            @csrf
            <input type="hidden" name="role" value="seller">

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-[#063466] to-[#1e3a8a] px-6 py-4 flex items-center gap-3">
                    <span class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-white font-semibold">Choose Your Business Seller Plan</span>
                </div>

                <div class="p-6">
                    @if($packages->isEmpty())
                        <div class="text-center py-14 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="font-semibold">No business seller plans available right now.</p>
                            <p class="text-sm mt-1">Please contact support.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 {{ $packages->count() > 1 ? 'md:grid-cols-2' : '' }} gap-5">
                            @foreach($packages as $i => $pkg)
                                @php
                                    $features = is_array($pkg->features)
                                        ? $pkg->features
                                        : (is_string($pkg->features) ? json_decode($pkg->features, true) : []);
                                    $features = is_array($features) ? $features : [];
                                @endphp
                                <label class="block cursor-pointer">
                                    <input type="radio" name="package_id" value="{{ $pkg->id }}"
                                           class="sr-only peer" {{ $i === 0 ? 'checked' : '' }} required>
                                    <div class="relative p-6 border-2 rounded-2xl transition-all duration-200 cursor-pointer
                                        border-gray-200 hover:border-[#063466] hover:shadow-lg
                                        peer-checked:border-[#063466] peer-checked:bg-blue-50 peer-checked:shadow-lg
                                        {{ $i === 0 ? 'border-[#063466] bg-blue-50 shadow-lg' : '' }}">

                                        <!-- Check dot -->
                                        <div class="absolute top-4 right-4 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all duration-200
                                            package-radio-indicator
                                            {{ $i === 0 ? 'border-[#063466] bg-[#063466]' : 'border-gray-300' }}">
                                            <svg class="package-radio-check w-3 h-3 text-white transition-opacity duration-200 {{ $i === 0 ? '' : 'opacity-0' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>

                                        <!-- Badge -->
                                        <span class="inline-flex items-center gap-1 bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full mb-3">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            Business Seller
                                        </span>

                                        <h3 class="text-lg font-bold text-gray-900 mb-1 pr-8">{{ $pkg->title }}</h3>

                                        <div class="flex items-baseline gap-1 mb-4">
                                            <span class="text-2xl font-extrabold text-[#063466]">${{ number_format($pkg->price, 2) }}</span>
                                            <span class="text-gray-400 text-sm">
                                                @if($pkg->duration_days)
                                                    / {{ $pkg->duration_days }} days
                                                @else
                                                    / year
                                                @endif
                                            </span>
                                        </div>

                                        @if(!empty($features))
                                            <ul class="space-y-1.5">
                                                @foreach($features as $feat)
                                                    <li class="flex items-start gap-2 text-sm text-gray-600">
                                                        <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        {{ $feat }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        @if($pkg->max_listings)
                                            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-4 text-xs text-gray-500">
                                                <span><strong class="text-gray-700">{{ $pkg->max_listings }}</strong> total listings</span>
                                                @if($pkg->max_listings_per_month)
                                                    <span><strong class="text-gray-700">{{ $pkg->max_listings_per_month }}</strong>/month</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @endif

                    @error('package_id')
                        <p class="text-red-600 mt-4 text-sm font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('seller.dashboard') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Dashboard
                </a>
                @unless($packages->isEmpty())
                <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-2.5 bg-gradient-to-r from-[#063466] to-[#1e3a8a] text-white text-sm font-bold rounded-xl shadow hover:shadow-lg hover:scale-105 transition-all duration-200">
                    Continue to Documents & Payment
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
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
