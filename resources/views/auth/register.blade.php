@extends('layouts.welcome')
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#063466] via-[#1e3a8a] to-[#2563eb] mb-3">
                        Create Your Account
                    </h1>
                    <p class="text-gray-600 text-lg font-medium">Join CayMark - The Bahamas' Premier Digital Trading Platform</p>
                </div>
            </div>
        </div>

        <!-- Main Registration Card -->
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-200 overflow-hidden animate-slide-up">
            <!-- Professional Top Bar -->
            <div class="bg-gradient-to-r from-[#063466] to-[#1e3a8a] px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-white font-semibold text-sm">Secure Registration</span>
                    </div>
                    <div class="text-white/80 text-xs font-medium">
                        Step 1 of 1
                    </div>
                </div>
            </div>

            <div class="p-8 md:p-12">
                <!-- Flash Messages -->
                <div class="mb-8" role="status" aria-live="polite">
                    @if (session('error'))
                        <div class="rounded-xl bg-red-50 border-l-4 border-red-500 p-6 mb-4 shadow-sm animate-fade-in">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-semibold text-red-800">{{ session('error') }}</h3>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="rounded-xl bg-green-50 border-l-4 border-green-500 p-6 mb-4 shadow-sm animate-fade-in">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-semibold text-green-800">{{ session('success') }}</h3>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="rounded-xl bg-yellow-50 border-l-4 border-yellow-400 p-6 mb-4 shadow-sm animate-fade-in">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-semibold text-yellow-800 mb-2">Please fix the following errors:</h3>
                                    <ul class="text-sm text-yellow-700 space-y-1">
                                        @foreach ($errors->all() as $err)
                                            <li class="flex items-center">
                                                <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-2"></span>
                                                {{ $err }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Form Section -->
                <form method="POST" action="{{ route('register.step1') }}" id="step1-form" novalidate class="space-y-6">
                    @csrf
                    
                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label for="first_name" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-[#063466] transition-colors">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#063466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}"
                                    class="w-full pl-12 pr-4 py-4 rounded-xl border-2 border-gray-200 focus:border-[#063466] focus:ring-4 focus:ring-[#063466]/10 transition-all duration-300 text-gray-900 placeholder-gray-400 font-medium"
                                    placeholder="Enter your first name">
                                @error('first_name')
                                    <p class="text-sm text-red-600 mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="group">
                            <label for="last_name" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-[#063466] transition-colors">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#063466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="text" id="last_name" name="last_name" required value="{{ old('last_name') }}"
                                    class="w-full pl-12 pr-4 py-4 rounded-xl border-2 border-gray-200 focus:border-[#063466] focus:ring-4 focus:ring-[#063466]/10 transition-all duration-300 text-gray-900 placeholder-gray-400 font-medium"
                                    placeholder="Enter your last name">
                                @error('last_name')
                                    <p class="text-sm text-red-600 mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Email Field (Full Width) -->
                    <div class="group">
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-[#063466] transition-colors">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#063466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                class="w-full pl-12 pr-4 py-4 rounded-xl border-2 border-gray-200 focus:border-[#063466] focus:ring-4 focus:ring-[#063466]/10 transition-all duration-300 text-gray-900 placeholder-gray-400 font-medium"
                                placeholder="your.email@example.com">
                            @error('email')
                                <p class="text-sm text-red-600 mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Password Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label for="password" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-[#063466] transition-colors">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#063466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input type="password" id="password" name="password" required
                                    class="w-full pl-12 pr-4 py-4 rounded-xl border-2 border-gray-200 focus:border-[#063466] focus:ring-4 focus:ring-[#063466]/10 transition-all duration-300 text-gray-900 placeholder-gray-400 font-medium"
                                    placeholder="Minimum 8 characters">
                                @error('password')
                                    <p class="text-sm text-red-600 mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="group">
                            <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-[#063466] transition-colors">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#063466] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    class="w-full pl-12 pr-4 py-4 rounded-xl border-2 border-gray-200 focus:border-[#063466] focus:ring-4 focus:ring-[#063466]/10 transition-all duration-300 text-gray-900 placeholder-gray-400 font-medium"
                                    placeholder="Re-enter your password">
                                @error('password_confirmation')
                                    <p class="text-sm text-red-600 mt-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Terms Checkbox -->
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-2xl p-6 border-2 border-gray-200 hover:border-[#063466]/30 transition-all duration-300">
                        <label class="flex items-start space-x-4 cursor-pointer group">
                            <div class="flex-shrink-0 mt-1">
                                <input type="checkbox" name="agree_terms" value="1" required
                                    class="w-6 h-6 text-[#063466] rounded-lg focus:ring-4 focus:ring-[#063466]/20 border-2 border-gray-300 cursor-pointer transition-all duration-200"
                                    {{ old('agree_terms') ? 'checked' : '' }}>
                            </div>
                            <div class="flex-1">
                                <span class="text-gray-800 font-bold text-base block mb-1 group-hover:text-[#063466] transition-colors">
                                    I agree to CayMark's Terms and Privacy Policy
                                </span>
                                <span class="text-gray-600 text-sm">
                                    By creating an account, you agree to our 
                                    <a href="#" target="_blank" class="text-[#063466] hover:text-[#1e3a8a] font-semibold underline decoration-2 underline-offset-2 transition-colors">Terms of Service</a> 
                                    and 
                                    <a href="#" target="_blank" class="text-[#063466] hover:text-[#1e3a8a] font-semibold underline decoration-2 underline-offset-2 transition-colors">Privacy Policy</a>
                                </span>
                            </div>
                        </label>
                        @error('agree_terms')
                            <p class="text-sm text-red-600 mt-3 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-[#063466] to-[#1e3a8a] text-white px-8 py-5 rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                            <span class="relative z-10 flex items-center justify-center">
                                <span>Create My Account</span>
                                <svg class="w-6 h-6 ml-3 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-[#1e3a8a] to-[#2563eb] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </button>
                        <p class="text-center text-gray-500 text-sm mt-4">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="text-[#063466] hover:text-[#1e3a8a] font-semibold underline decoration-2 underline-offset-2 transition-colors">Sign in here</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Trust Indicators -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 text-center">
                <div class="w-12 h-12 bg-[#063466]/10 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-[#063466]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Secure & Protected</h3>
                <p class="text-sm text-gray-600">Your data is encrypted and secure</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 text-center">
                <div class="w-12 h-12 bg-[#063466]/10 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-[#063466]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">Quick Setup</h3>
                <p class="text-sm text-gray-600">Get started in under 2 minutes</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-lg border border-gray-200 text-center">
                <div class="w-12 h-12 bg-[#063466]/10 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-[#063466]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-gray-900 mb-1">24/7 Support</h3>
                <p class="text-sm text-gray-600">We're here to help anytime</p>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }

    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(40px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.8s ease-out;
    }

    .animate-slide-up {
        animation: slide-up 1s ease-out;
    }

    input:focus, select:focus, textarea:focus {
        outline: none;
    }
</style>

@endsection
