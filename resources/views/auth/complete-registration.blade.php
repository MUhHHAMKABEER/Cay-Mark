@extends('layouts.welcome')
@section('title', 'Complete Registration - CayMark')
@section('content')

@php
    $isBusinessSeller = $finishData['role'] === 'seller' && $package->price > 0;
    $paymentRequired = $finishData['role'] === 'buyer' || $isBusinessSeller;
@endphp

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Complete Your Registration</h1>
            <p class="text-gray-600 text-lg">Upload documents and complete payment</p>
        </div>

        <!-- Flash Messages -->
        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <ul class="list-disc list-inside text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('finish.registration.complete') }}" enctype="multipart/form-data" id="complete-registration-form">
            @csrf

            <!-- Package Summary -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Membership Summary</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Membership Type:</span>
                        <span class="text-gray-900 font-semibold">{{ ucfirst($finishData['role']) }} - {{ $package->title }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Price:</span>
                        <span class="text-2xl font-bold text-blue-600">${{ number_format($package->price, 2) }}</span>
                    </div>
                    @if(!$paymentRequired)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                            <p class="text-green-800 text-sm"><strong>No payment required at this time.</strong> You will pay $25 per listing when you submit items.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Document Upload Section -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Document Verification</h2>

                <!-- ID Document -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Government Issued ID Document *
                    </label>
                    <input type="file" name="id_document" accept=".jpg,.jpeg,.png,.pdf" required
                        class="block w-full text-sm text-gray-700 file:border file:border-gray-300 file:rounded-xl file:px-4 file:py-3 file:bg-white file:text-gray-700 file:cursor-pointer hover:file:bg-gray-50 transition duration-200">
                    <p class="text-sm text-gray-500 mt-1">JPG, PNG, or PDF (max 5MB)</p>
                    @error('id_document')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ID Type -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ID Type *
                    </label>
                    <select name="id_type" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">Select ID Type</option>
                        <option value="Passport">Passport</option>
                        <option value="Driver License">Driver's License</option>
                        <option value="National ID">National ID</option>
                    </select>
                    @error('id_type')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Business License (Business Seller only) -->
                @if($isBusinessSeller)
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Business License Document *
                        </label>
                        <input type="file" name="business_license" accept=".jpg,.jpeg,.png,.pdf" required
                            class="block w-full text-sm text-gray-700 file:border file:border-gray-300 file:rounded-xl file:px-4 file:py-3 file:bg-white file:text-gray-700 file:cursor-pointer hover:file:bg-gray-50 transition duration-200">
                        <p class="text-sm text-gray-500 mt-1">Must be current and not expired. JPG, PNG, or PDF (max 5MB)</p>
                        @error('business_license')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Relationship to Business -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Relationship to Business *
                        </label>
                        <select name="relationship_to_business" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="">Select Relationship</option>
                            <option value="Owner">Owner</option>
                            <option value="Founder">Founder</option>
                            <option value="Shareholder">Shareholder</option>
                            <option value="Employee">Employee</option>
                            <option value="Authorized Representative">Authorized Representative</option>
                            <option value="Manager">Manager</option>
                        </select>
                        @error('relationship_to_business')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- Payment Section (if required) -->
            @if($paymentRequired)
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Information</h2>

                    <div class="bg-gradient-to-br from-gray-50 to-blue-50 p-6 rounded-xl border border-gray-200 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-700 font-medium">Total Amount Due:</span>
                            <span class="text-3xl font-bold text-blue-600">${{ number_format($package->price, 2) }}</span>
                        </div>
                    </div>

                    <!-- Credit Card Form -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Card Number *
                            </label>
                            <input type="text" name="card_number" placeholder="1234 5678 9012 3456" required
                                maxlength="19" pattern="[0-9\s]{13,19}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('card_number')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Expiry Month *
                                </label>
                                <select name="expiry_month" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                    <option value="">MM</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endfor
                                </select>
                                @error('expiry_month')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Expiry Year *
                                </label>
                                <select name="expiry_year" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                    <option value="">YYYY</option>
                                    @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('expiry_year')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                CVC *
                            </label>
                            <input type="text" name="cvc" placeholder="123" required
                                maxlength="4" pattern="[0-9]{3,4}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('cvc')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- Terms Acknowledgment -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
                <div class="mb-6">
                    <label class="flex items-start space-x-4 p-4 rounded-xl border border-gray-200 hover:bg-gray-50 transition duration-200 cursor-pointer">
                        <input type="checkbox" name="agree_terms" value="1" required
                            class="mt-1 w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">
                            By completing registration, you agree to adhere to CayMark's Terms and Conditions and comply with all membership restrictions applicable to your selected account role.
                        </span>
                    </label>
                    @error('agree_terms')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <a href="{{ route('finish.registration') }}" 
                   class="inline-block px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition duration-200 mr-4">
                    Back
                </a>
                <button type="submit"
                    class="inline-block bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Complete Registration
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Format card number with spaces
    document.querySelector('input[name="card_number"]')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });
</script>
@endpush

@endsection

