@extends('layouts.welcome')
@section('content')

    @php
        // read current step from session (controller sets this)
        $currentStep = session('registration_step', 1);
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Modern Progress Bar -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
    <div class="flex justify-center">
        @php
            $steps = [
                1 => ['label' => 'Personal Info', 'icon' => '👤', 'color' => 'blue'],
                2 => ['label' => 'Package', 'icon' => '📦', 'color' => 'purple'],
                3 => ['label' => 'Payment', 'icon' => '💳', 'color' => 'orange'],
            ];
            $currentStep = session('registration_step', 1);
        @endphp

        @foreach ($steps as $stepNumber => $step)
            <div class="flex items-center">
                <!-- Step Circle -->
                <div class="flex flex-col items-center relative z-10">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-lg font-semibold transition-all duration-500 transform
                        {{ $stepNumber == $currentStep
                            ? "bg-{$step['color']}-500 text-white shadow-lg scale-110"
                            : ($stepNumber < $currentStep
                                ? "bg-{$step['color']}-400 text-white shadow-md scale-100"
                                : "bg-gray-100 text-gray-400 shadow-sm scale-100") }}">
                        {{ $step['icon'] }}
                    </div>
                    <span class="mt-3 text-sm font-semibold
                        {{ $stepNumber == $currentStep
                            ? "text-{$step['color']}-600"
                            : ($stepNumber < $currentStep
                                ? "text-{$step['color']}-500"
                                : "text-gray-400") }}">
                        {{ $step['label'] }}
                    </span>
                </div>

                <!-- Progress Bar -->
                @if ($stepNumber < count($steps))
                    <div class="flex items-center mx-4">
                        <div class="w-24 h-2 bg-gray-200 rounded-full relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-orange-400 to-orange-500 transition-all duration-1000 ease-out"
                                 style="width: {{ $stepNumber < $currentStep ? '100%' : '0%' }}"></div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>


            <!-- Enhanced Registration Form -->
            <div x-data="{
                step: {{ (int) $currentStep }},
                showModal: false,
                modalAgree: false,
                nextStep() { this.step++ },
                previousStep() { if (this.step > 1) this.step-- },
                showConfirmationModal() { this.showModal = true },
                confirmAndSubmit() {
                    if (!this.modalAgree) {
                        alert('Please agree to the terms and conditions to continue.');
                        return;
                    }
                    try {
                        if (this.$refs && this.$refs.step3Form) {
                            this.$refs.step3Form.submit();
                            return;
                        }
                    } catch (e) {
                        console.warn('Alpine ref submit failed:', e);
                    }

                    const f = document.getElementById('step3-form');
                    if (f) {
                        f.submit();
                    } else {
                        console.error('Could not find step3-form to submit');
                        alert('Submission failed: form not found. Please refresh the page and try again.');
                    }
                }
            }" class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">

                <!-- Enhanced Flash Messages -->
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

                <!-- Step 1: Basic Account Creation -->
                <div x-show="step === 1" x-cloak class="animate-fade-in">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Your Account</h2>
                        <p class="text-gray-600">Get started with just your basic information</p>
                    </div>

                    <form method="POST" action="{{ route('register.step1') }}" id="step1-form" novalidate>
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name Fields -->
                            <div class="space-y-2">
                                <label for="first_name" class="block text-sm font-semibold text-gray-700">First Name *</label>
                                <input type="text" id="first_name" name="first_name" required value="{{ old('first_name') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                                @error('first_name')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="last_name" class="block text-sm font-semibold text-gray-700">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" required value="{{ old('last_name') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                                @error('last_name')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Fields -->
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-semibold text-gray-700">Email *</label>
                                <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                                @error('email')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="email_confirmation" class="block text-sm font-semibold text-gray-700">Confirm Email *</label>
                                <input type="email" id="email_confirmation" name="email_confirmation" required value="{{ old('email_confirmation') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                                @error('email_confirmation')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Fields -->
                            <div class="space-y-2">
                                <label for="password" class="block text-sm font-semibold text-gray-700">Password *</label>
                                <input type="password" id="password" name="password" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                                @error('password')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-2">
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Confirm Password *</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                                @error('password_confirmation')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Terms Checkbox -->
                        <div class="mt-8">
                            <label class="flex items-start space-x-4 p-4 rounded-xl border border-gray-200 hover:bg-gray-50 transition duration-200 cursor-pointer">
                                <input type="checkbox" name="agree_terms" value="1" required
                                    class="mt-1 w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                                    {{ old('agree_terms') ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">
                                    I agree to CayMark's 
                                    <a href="#" target="_blank" class="font-semibold hover:underline text-blue-600">Terms and Privacy Policy</a>
                                </span>
                            </label>
                            @error('agree_terms')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-8 flex justify-end">
                            <button type="submit"
                                class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200">
                                Create Account
                                <svg class="w-5 h-5 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 2: Package Selection -->
                <div x-show="step === 2" x-cloak class="animate-fade-in">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Select Your Role & Package</h2>
                        <p class="text-gray-600">Choose how you want to use our platform</p>
                    </div>

                    <form method="POST" action="{{ route('register.step2') }}" id="step2-form" novalidate>
                        @csrf

                        <div class="mb-8">
                            <label class="block text-sm font-semibold text-gray-700 mb-4">I want to register as a:</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="role" value="buyer" class="sr-only peer" required
                                        {{ old('role') == 'buyer' ? 'checked' : '' }}>
                                    <div class="p-8 border-2 border-gray-300 rounded-2xl transition-all duration-300
                                        group-hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg">
                                        <div class="flex items-center mb-4">
                                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                                </svg>
                                            </div>
                                            <div class="font-bold text-xl text-gray-900">Buyer</div>
                                        </div>
                                        <p class="text-gray-600">Browse marketplace and participate in auctions</p>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="role" value="seller" class="sr-only peer"
                                        {{ old('role') == 'seller' ? 'checked' : '' }}>
                                    <div class="p-8 border-2 border-gray-300 rounded-2xl transition-all duration-300
                                        group-hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg">
                                        <div class="flex items-center mb-4">
                                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <div class="font-bold text-xl text-gray-900">Seller</div>
                                        </div>
                                        <p class="text-gray-600">List vehicles for sale or auction</p>
                                    </div>
                                </label>
                            </div>
                            @error('role')
                                <p class="text-sm text-red-600 mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div id="package-selection" class="space-y-4">
                            {{-- packages injected via JS --}}
                            @error('package_id')
                                <p class="text-sm text-red-600 mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" @click="previousStep()"
                                class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Back
                            </button>

                            <button type="submit"
                                class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200 flex items-center">
                                Continue to Payment
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 3: Payment -->
                <div x-show="step === 3" x-cloak class="animate-fade-in">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Payment Information</h2>
                        <p class="text-gray-600">Complete your registration</p>
                    </div>

                    <form method="POST" action="{{ route('register.step3') }}" id="step3-form" x-ref="step3Form" novalidate>
                        @csrf

                        <div class="bg-gradient-to-br from-gray-50 to-blue-50 p-8 rounded-2xl border border-gray-200 mb-8">
                            <h3 class="font-bold text-xl text-gray-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Order Summary
                            </h3>
                            <div id="payment-summary"></div>
                        </div>

                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-8 mb-8">
                            <h3 class="font-bold text-xl text-amber-900 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Bank Wire Payment
                            </h3>
                            <div class="space-y-4 text-sm">
                                <p class="text-amber-800">Please transfer the total amount to the following bank account:</p>
                                <div class="bg-white p-6 rounded-xl border border-amber-300 shadow-sm">
                                    <div class="space-y-2">
                                        <p><strong class="text-amber-900">Bank:</strong> Merchant Bank</p>
                                        <p><strong class="text-amber-900">Account Name:</strong> Bahamian Marketplace Ltd.</p>
                                        <p><strong class="text-amber-900">Account Number:</strong> 123456789</p>
                                        <p><strong class="text-amber-900">Routing Number:</strong> 021000021</p>
                                        <p><strong class="text-amber-900">Reference:</strong> <span id="payment-reference" class="font-mono bg-amber-100 px-2 py-1 rounded">REG-{{ time() }}</span></p>
                                    </div>
                                </div>
                                <p class="text-amber-700 font-semibold">Your account will be activated once we receive and confirm your payment.</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-start space-x-4 p-4 rounded-xl border border-gray-200 hover:bg-gray-50 transition duration-200 cursor-pointer">
                                <input type="checkbox" name="agree_terms" value="1" required
                                    class="mt-1 w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                                    {{ old('agree_terms') ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">I understand that I need to complete the bank wire transfer and my account will be pending until payment confirmation. I agree to the terms and conditions.</span>
                            </label>
                            @error('agree_terms')
                                <p class="text-sm text-red-600 mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="flex items-start space-x-4 p-4 rounded-xl border border-gray-200 hover:bg-gray-50 transition duration-200 cursor-pointer">
                                <input type="checkbox" name="marketing_opt_in" value="1"
                                    class="mt-1 w-5 h-5 text-blue-600 rounded focus:ring-blue-500"
                                    {{ old('marketing_opt_in') ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">I would like to receive promotional emails and SMS updates about new listings, auctions, and offers.</span>
                            </label>
                            @error('marketing_opt_in')
                                <p class="text-sm text-red-600 mt-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="mt-8 p-6 bg-blue-50 rounded-2xl border border-blue-200">
                            <p class="text-sm text-blue-800">By registering, you agree to the <a href="#" target="_blank" class="font-semibold hover:underline">Site Data & Privacy Policy</a>,
                                <a href="#" target="_blank" class="font-semibold hover:underline">Terms & Conditions</a>, and other applicable policies.
                            </p>
                        </div>

                        <div class="mt-8 flex justify-between">
                            <button type="button" @click="previousStep()"
                                class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Back
                            </button>

                            <button type="submit"
                                class="bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Complete Registration
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Enhanced Confirmation Modal -->
                <div x-show="showModal" x-cloak
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 animate-fade-in">
                    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-auto shadow-2xl transform transition-all duration-300 scale-100">
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Final Confirmation</h3>
                            <p class="text-gray-600">By registering and making payment, you agree to be subject to the Site Data & Privacy Policy and the Terms & Conditions.</p>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-start space-x-4 p-4 rounded-xl border border-gray-200 hover:bg-gray-50 transition duration-200 cursor-pointer">
                                <input type="checkbox" x-model="modalAgree" class="mt-1 w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                                <span class="text-sm text-gray-700 font-medium">I agree to the terms and conditions</span>
                            </label>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button @click="showModal = false"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition duration-200">
                                Cancel
                            </button>
                            <button @click="confirmAndSubmit()"
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition duration-200">
                                Confirm & Register
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Helpers
                const oldPackageId = @json(old('package_id'));
                const packagesContainer = document.getElementById('package-selection');
                const fileUploadContainer = document.getElementById('file-upload-container');

                // FIXED: use /api prefix for api.php routes
                const packagesUrl = (role) => "/api/packages/" + encodeURIComponent(role);

                // Load packages for role (GET /api/packages/{role})
                async function loadPackages(role) {
                    if (!packagesContainer) return;
                    packagesContainer.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div><p class="text-gray-500 mt-2">Loading packages...</p></div>';

                    try {
                        const res = await fetch(packagesUrl(role), {
                            credentials: 'same-origin'
                        });
                        if (!res.ok) throw new Error('Network response was not ok: ' + res.status);
                        const packages = await res.json();

                        if (!Array.isArray(packages) || packages.length === 0) {
                            packagesContainer.innerHTML = '<p class="text-gray-500 text-center py-8">No packages available for this role.</p>';
                            return;
                        }

                        packagesContainer.innerHTML = '';

                        packages.forEach(pkg => {
                            const label = document.createElement('label');
                            label.className = 'block border-2 border-gray-300 rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:border-blue-400 hover:shadow-lg';

                            const input = document.createElement('input');
                            input.type = 'radio';
                            input.name = 'package_id';
                            input.value = pkg.id;
                            input.className = 'sr-only';

                            if (oldPackageId !== null && oldPackageId != '' && String(oldPackageId) == String(pkg.id)) {
                                input.checked = true;
                                label.classList.add('border-blue-500', 'bg-blue-50', 'shadow-md');
                            }

                            input.addEventListener('change', () => {
                                packagesContainer.querySelectorAll('label').forEach(l => {
                                    l.classList.remove('border-blue-500', 'bg-blue-50', 'shadow-md');
                                });

                                if (input.checked) {
                                    label.classList.add('border-blue-500', 'bg-blue-50', 'shadow-md');
                                }
                            });

                            const info = document.createElement('div');
                            info.className = 'space-y-3';
                            info.innerHTML = `
                                <div class="flex justify-between items-start">
                                    <div class="font-bold text-xl text-gray-900">${escapeHtml(pkg.title ?? pkg.name ?? '')}</div>
                                    <div class="text-2xl font-bold text-blue-600">${typeof pkg.price !== 'undefined' ? '$' + Number(pkg.price).toFixed(2) : ''}</div>
                                </div>
                                <p class="text-gray-600 leading-relaxed">${escapeHtml(pkg.description ?? '')}</p>
                                ${pkg.features ? `<div class="space-y-2 mt-4">
                                    <div class="text-sm font-semibold text-gray-700">Features:</div>
                                    <div class="text-sm text-gray-600">${escapeHtml(pkg.features)}</div>
                                </div>` : ''}
                            `;

                            label.appendChild(input);
                            label.appendChild(info);
                            packagesContainer.appendChild(label);
                        });

                    } catch (err) {
                        console.error('Failed to load packages:', err);
                        packagesContainer.innerHTML = '<div class="text-center py-8 text-red-600 bg-red-50 rounded-2xl border border-red-200"><p>Failed to load packages. Please try again.</p></div>';
                    }
                }

                // Enhanced file upload inputs
                function createFileInputs() {
                    if (!fileUploadContainer) return;
                    if (fileUploadContainer.children.length > 0) return;

                    for (let i = 0; i < 2; i++) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'flex items-center space-x-4';

                        const input = document.createElement('input');
                        input.type = 'file';
                        input.name = 'id_documents[]';
                        input.accept = '.jpg,.jpeg,.png,.pdf';
                        input.required = true;
                        input.className = 'block w-full text-sm text-gray-700 file:border file:border-gray-300 file:rounded-xl file:px-4 file:py-3 file:bg-white file:text-gray-700 file:cursor-pointer hover:file:bg-gray-50 transition duration-200';

                        wrapper.appendChild(input);
                        fileUploadContainer.appendChild(wrapper);
                    }
                }

                // Wire role radio listeners
                function initRoleListeners() {
                    const roleInputs = document.querySelectorAll('input[name="role"]');
                    if (!roleInputs || roleInputs.length === 0) return;

                    roleInputs.forEach(input => {
                        input.addEventListener('change', function() {
                            if (this.checked) loadPackages(this.value);
                        });
                    });

                    const checked = Array.from(roleInputs).find(i => i.checked);
                    if (checked) loadPackages(checked.value);
                }

                function escapeHtml(str) {
                    if (str === null || typeof str === 'undefined') return '';
                    return String(str)
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#39;');
                }

                // Initialize
                initRoleListeners();
                createFileInputs();
            });
        </script>
    @endpush

    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            ring: 2px;
        }

        .file\:border:hover {
            border-color: #3b82f6;
        }
    </style>

@endsection
