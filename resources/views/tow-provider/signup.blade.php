@extends('layouts.welcome')

@section('title', 'Sign Up as Tow Provider – CayMark')

@section('content')
<style>
    .form-input {
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.625rem 0.875rem;
        width: 100%;
        transition: all 0.2s ease;
    }
    .form-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .section-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }
    .toc-box {
        max-height: 240px;
        overflow-y: auto;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1rem;
        background: #f9fafb;
        font-size: 0.875rem;
        color: #374151;
    }
</style>

<section class="py-12 px-4 bg-gray-50 min-h-screen">
    <div class="container mx-auto max-w-2xl">
        <div class="mb-8">
            <a href="{{ route('tow-provider.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1">
                <span class="material-icons text-lg">arrow_back</span> Back to Tow Providers
            </a>
        </div>

        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 font-heading mb-2">Sign Up as a Tow Provider</h1>
        <p class="text-gray-600 mb-8">Complete the form below to apply. You will need a current business license and payment for the signup fee.</p>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4 shadow-sm">
                <ul class="text-red-800 text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tow-provider.signup.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            {{-- Contact & business info --}}
            <div class="section-card p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 font-heading">Your Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1.5">First name <span class="text-red-500">*</span></label>
                        <input type="text" id="first_name" name="first_name" required
                               value="{{ old('first_name') }}" class="form-input" autocomplete="given-name">
                    </div>
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1.5">Last name <span class="text-red-500">*</span></label>
                        <input type="text" id="last_name" name="last_name" required
                               value="{{ old('last_name') }}" class="form-input" autocomplete="family-name">
                    </div>
                </div>
                <div class="mt-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">Phone number <span class="text-red-500">*</span></label>
                    <input type="tel" id="phone" name="phone" required
                           value="{{ old('phone') }}" class="form-input" autocomplete="tel">
                </div>
                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email address <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" required
                           value="{{ old('email') }}" class="form-input" autocomplete="email">
                </div>
                <div class="mt-4">
                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1.5">Business name <span class="text-red-500">*</span></label>
                    <input type="text" id="business_name" name="business_name" required
                           value="{{ old('business_name') }}" class="form-input">
                </div>
            </div>

            {{-- Business license upload --}}
            <div class="section-card p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 font-heading">Business License</h2>
                <p class="text-gray-600 text-sm mb-4">Upload a photo or scan of your current, up-to-date business license. Accepted: JPG, PNG, or PDF (max 10MB).</p>
                <input type="file" id="business_license" name="business_license" required
                       accept=".jpg,.jpeg,.png,.pdf"
                       class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            {{-- Terms & Conditions --}}
            <div class="section-card p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 font-heading">Terms &amp; Conditions</h2>
                <p class="text-gray-600 text-sm mb-3">Please read the following terms. You must accept them to continue.</p>
                <div class="toc-box mb-4">
                    {{-- Placeholder content – replace with your actual T&C text --}}
                    <p class="font-semibold text-gray-900 mb-2">Tow Provider Agreement</p>
                    <p>By signing up as a third-party tow provider with CayMark, you agree to provide tow and transport services in accordance with our policies and the laws of The Bahamas. You represent that you hold a valid, current business license and appropriate insurance.</p>
                    <p class="mt-2">CayMark does not employ tow providers directly. You operate as an independent business. CayMark may list your business in our directory and refer buyers to you; we do not guarantee any volume of referrals. You are responsible for your own pricing, scheduling, and customer communications.</p>
                    <p class="mt-2">You must maintain a current business license and keep your listing information up to date. CayMark reserves the right to remove providers from the directory at any time. The signup fee is non-refundable.</p>
                    <p class="mt-2">[Full Terms &amp; Conditions content to be provided.]</p>
                </div>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="terms_accepted" value="1" required
                           {{ old('terms_accepted') ? 'checked' : '' }}
                           class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-gray-700 text-sm">I have read and accept the Terms &amp; Conditions <span class="text-red-500">*</span></span>
                </label>
            </div>

            {{-- Payment --}}
            <div class="section-card p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4 font-heading">Payment</h2>
                <p class="text-gray-600 text-sm mb-4">Signup fee: <strong class="text-gray-900">${{ $signupFeeDollars }}</strong></p>
                <div class="space-y-4">
                    <div>
                        <label for="cardholder_name" class="block text-sm font-medium text-gray-700 mb-1.5">Name on card <span class="text-red-500">*</span></label>
                        <input type="text" id="cardholder_name" name="cardholder_name" required
                               value="{{ old('cardholder_name') }}" class="form-input" placeholder="John Doe" autocomplete="cc-name">
                    </div>
                    <div>
                        <label for="card_number" class="block text-sm font-medium text-gray-700 mb-1.5">Card number <span class="text-red-500">*</span></label>
                        <input type="text" id="card_number" name="card_number" required
                               value="{{ old('card_number') }}" class="form-input font-mono" placeholder="4242 4242 4242 4242" autocomplete="cc-number" maxlength="19">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="card_expiry" class="block text-sm font-medium text-gray-700 mb-1.5">Expiry (MM/YY) <span class="text-red-500">*</span></label>
                            <input type="text" id="card_expiry" name="card_expiry" required
                                   value="{{ old('card_expiry') }}" class="form-input" placeholder="12/26" autocomplete="cc-exp">
                        </div>
                        <div>
                            <label for="card_cvv" class="block text-sm font-medium text-gray-700 mb-1.5">CVC <span class="text-red-500">*</span></label>
                            <input type="text" id="card_cvv" name="card_cvv" required
                                   value="{{ old('card_cvv') }}" class="form-input" placeholder="123" autocomplete="cc-csc">
                        </div>
                    </div>
                </div>
                @if(config('services.payment.sandbox', true))
                    <p class="mt-4 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                        <strong>Sandbox:</strong> Use test card <code class="bg-amber-100 px-1 rounded font-mono">4242 4242 4242 4242</code>. Any expiry (e.g. 12/26) and CVC (e.g. 123) work.
                    </p>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                <button type="submit"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    Submit &amp; Pay ${{ $signupFeeDollars }}
                </button>
                <a href="{{ route('tow-provider.index') }}" class="text-sm text-gray-500 hover:text-gray-900">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection
