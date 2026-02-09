@extends('layouts.welcome')

@section('title', 'Tow Providers – CayMark')

@section('content')
<style>
    .tow-hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #1e40af 100%);
        position: relative;
        overflow: hidden;
    }
    .island-heading {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        color: #1e3a8a;
        border-bottom: 2px solid #2563eb;
        padding-bottom: 0.25rem;
    }
    .video-placeholder {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border: 2px dashed #9ca3af;
        border-radius: 1rem;
        min-height: 360px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }
</style>

{{-- 1. Header --}}
<section class="tow-hero py-16 md:py-20 px-4">
    <div class="container mx-auto max-w-4xl text-center text-white">
        <h1 class="text-4xl md:text-5xl font-extrabold font-heading mb-4">Tow Providers</h1>
        <p class="text-xl text-blue-100 max-w-2xl mx-auto">
            {{-- Intro paragraph: exact wording to be provided --}}
            Find approved third-party tow providers by island for vehicle pickup and delivery. Below is our directory of tow providers available across The Bahamas.
        </p>
    </div>
</section>

{{-- 2. Intro paragraph (directly under header – same block above; add more copy here if needed) --}}
<section class="py-8 px-4 bg-white">
    <div class="container mx-auto max-w-4xl">
        <p class="text-gray-700 text-lg leading-relaxed text-center">
            When you need a tow or transport for your vehicle after a CayMark purchase, you can use any provider listed below by island. We recommend contacting providers in advance to confirm availability and rates.
        </p>
    </div>
</section>

{{-- 3. Directory by island (alphabetical) --}}
<section class="py-12 px-4 bg-gray-50">
    <div class="container mx-auto max-w-4xl">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8 font-heading">Directory by Island</h2>
        <div class="space-y-10">
            @foreach($islands as $island => $providers)
                <div>
                    <h3 class="island-heading text-xl mb-3">{{ $island }}</h3>
                    <ul class="list-none space-y-2 pl-0">
                        @foreach($providers as $name)
                            <li class="text-gray-700 flex items-center">
                                <span class="w-2 h-2 bg-blue-500 rounded-full mr-3 flex-shrink-0"></span>
                                {{ $name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 4. Video section (placeholder for your video) --}}
<section class="py-12 px-4 bg-white">
    <div class="container mx-auto max-w-4xl">
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 font-heading">Learn More</h2>
        <div class="video-placeholder">
            <div class="text-center px-4">
                <span class="material-icons text-6xl text-gray-400 mb-2">videocam</span>
                <p class="font-medium">Video section</p>
                <p class="text-sm mt-1">Your video will be placed here.</p>
            </div>
        </div>
    </div>
</section>

{{-- 5. Signup CTA --}}
<section class="py-12 px-4 bg-gray-50">
    <div class="container mx-auto max-w-4xl text-center">
        <p class="text-lg text-gray-700 mb-4">
            Interested in becoming a third-party tow provider?
        </p>
        <a href="{{ route('tow-provider.signup') }}"
           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200">
            Sign up as a Tow Provider
        </a>
    </div>
</section>
@endsection
