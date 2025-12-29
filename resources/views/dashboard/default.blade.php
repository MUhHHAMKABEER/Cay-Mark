@extends('layouts.welcome')
@section('title', 'Complete Your Registration - CayMark')
@section('content')

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Your Registration is Not Complete</h1>
                <p class="text-gray-600 text-lg">Please finish your registration to access all CayMark features</p>
            </div>

            <!-- Notification Panel -->
            <div class="bg-amber-50 border-l-4 border-amber-400 p-6 rounded-xl mb-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-amber-900 mb-2">Complete Your Registration</h3>
                        <p class="text-amber-800 mb-4">
                            To start using CayMark, you need to:
                        </p>
                        <ul class="list-disc list-inside text-amber-800 space-y-2 mb-4">
                            <li>Select your membership type (Buyer or Seller)</li>
                            <li>Upload your identification documents</li>
                            <li>Complete payment (if required for your membership)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Primary Action Button -->
            <div class="text-center">
                <a href="{{ route('finish.registration') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200 text-lg">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Finish Registration — Select Your Membership
                </a>
            </div>
        </div>

        <!-- What You Can Do Now -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">What You Can Do Now</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex items-start space-x-4 p-4 bg-green-50 rounded-xl border border-green-200">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-green-900 mb-1">Browse Listings</h3>
                        <p class="text-green-800 text-sm">View all available vehicles and auction listings</p>
                    </div>
                </div>

                <div class="flex items-start space-x-4 p-4 bg-green-50 rounded-xl border border-green-200">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-green-900 mb-1">View Details</h3>
                        <p class="text-green-800 text-sm">See full listing details, photos, and descriptions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- What's Restricted -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">What's Restricted Until Registration Complete</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Bid on Auctions</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Use Buy Now</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Submit Listings</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Access Seller Tools</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Add/Use Deposits</span>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span class="text-red-800 font-medium">Access Buyer/Seller Dashboards</span>
                </div>
            </div>
        </div>

        <!-- Profile Section -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100 mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Your Profile Information</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Name:</span>
                    <span class="text-gray-900 font-semibold">{{ Auth::user()->name }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-200">
                    <span class="text-gray-600 font-medium">Email:</span>
                    <span class="text-gray-900 font-semibold">{{ Auth::user()->email }}</span>
                </div>
                <div class="flex justify-between items-center py-3">
                    <span class="text-gray-600 font-medium">Account Status:</span>
                    <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm font-semibold">Registration Incomplete</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

