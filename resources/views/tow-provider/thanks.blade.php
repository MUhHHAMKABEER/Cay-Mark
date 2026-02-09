@extends('layouts.welcome')

@section('title', 'Thank You – Tow Provider Signup')

@section('content')
<section class="py-16 px-4 bg-gray-50 min-h-screen flex items-center">
    <div class="container mx-auto max-w-xl text-center">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 md:p-12">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-icons text-4xl text-green-600">check_circle</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 font-heading mb-3">Thank You</h1>
            <p class="text-gray-600 mb-6">
                Your tow provider signup and payment have been received. We will review your application and business license and get in touch with you shortly.
            </p>
            <a href="{{ route('tow-provider.index') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                Back to Tow Providers
            </a>
        </div>
    </div>
</section>
@endsection
