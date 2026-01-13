@extends('layouts.simple')

@section('title', 'Dashboard - CayMark')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-md animate-fade-in">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Header with CTA Button -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl shadow-2xl p-8 mb-8 overflow-hidden relative">
            <div class="absolute inset-0 bg-black opacity-5"></div>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur-sm">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-2">Welcome to CayMark</h1>
                            <p class="text-blue-100 text-lg">Complete your registration to unlock all features</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('finish.registration') }}" 
                   class="group relative bg-white text-blue-600 px-8 py-4 rounded-xl font-bold text-lg hover:bg-blue-50 transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 flex items-center gap-2">
                    <span>FINISH REGISTRATION</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-2xl shadow-lg mb-8 overflow-hidden">
            <div class="border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <nav class="flex -mb-px">
                    <button onclick="showTab('user')" 
                            id="tab-user" 
                            class="tab-button active flex-1 px-8 py-4 text-sm font-semibold text-blue-600 border-b-3 border-blue-600 bg-blue-50 bg-opacity-50 transition-all duration-200">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>ACCOUNT INFORMATION</span>
                        </div>
                    </button>
                    <button onclick="showTab('notifications')" 
                            id="tab-notifications" 
                            class="tab-button flex-1 px-8 py-4 text-sm font-semibold text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-3 border-transparent transition-all duration-200 hover:bg-gray-50">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span>NOTIFICATIONS</span>
                        </div>
                    </button>
                </nav>
            </div>

            <!-- USER TAB -->
            <div id="content-user" class="tab-content p-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Account Information</h2>
                    <p class="text-gray-600">Manage your account settings and personal information</p>
                </div>

                <div class="space-y-6">
                    <!-- Full Name -->
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Full Name
                        </label>
                        <div class="bg-gray-50 border-2 border-gray-200 rounded-lg px-4 py-3.5">
                            <span class="text-gray-900 font-medium text-lg">{{ $user->name }}</span>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Registered Email Address
                        </label>
                        <form method="POST" action="{{ route('basic-dashboard.update-email') }}" class="space-y-3">
                            @csrf
                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="flex-1">
                                    <input type="email" 
                                           name="email" 
                                           value="{{ $user->email }}" 
                                           class="w-full bg-white border-2 border-gray-300 rounded-lg px-4 py-3.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                           required>
                                </div>
                                <button type="submit" 
                                        class="bg-blue-600 text-white px-6 py-3.5 rounded-lg font-semibold hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    <span>Update Email</span>
                                </button>
                            </div>
                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Email can be changed. You will need to verify your new email address.
                            </p>
                        </form>
                    </div>

                    <!-- Password Management -->
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Password Management
                        </label>
                        <button onclick="showPasswordModal()" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold transition-all duration-200 border-2 border-gray-300 hover:border-gray-400 flex items-center gap-2 transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            <span>Change Password</span>
                        </button>
                    </div>

                    <!-- Document Section -->
                    <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                        <label class="block text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Documents
                        </label>
                        
                        @if($documents->count() > 0)
                            <div class="space-y-4">
                                @foreach($documents as $document)
                                    <div class="bg-white border-2 border-gray-200 rounded-lg p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="p-3 bg-blue-100 rounded-lg">
                                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $document->doc_type)) }}</p>
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        @if($document->filename)
                                                            {{ $document->filename }}
                                                        @else
                                                            Document uploaded
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            @if($document->path)
                                                <a href="{{ asset('storage/' . $document->path) }}" 
                                                   target="_blank" 
                                                   class="text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-2 hover:underline">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    View
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-sm text-gray-500 mt-4 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Documents are view-only and cannot be uploaded, deleted, or edited from the Basic Dashboard.
                            </p>
                        @else
                            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-gray-500 font-medium">No documents uploaded yet</p>
                                <p class="text-sm text-gray-400 mt-1">Documents will appear here once added during registration.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- NOTIFICATIONS TAB -->
            <div id="content-notifications" class="tab-content hidden p-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Notifications</h2>
                    <p class="text-gray-600">Stay updated with your account activities</p>
                </div>

                <!-- Default System Notification -->
                <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-500 p-6 rounded-xl mb-6 shadow-md">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-amber-100 rounded-full">
                                <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-amber-900 mb-2">YOUR REGISTRATION IS NOT COMPLETE</h3>
                            <p class="text-amber-800 mb-4">SELECT A MEMBERSHIP TO ACTIVATE YOUR ACCOUNT.</p>
                            <a href="{{ route('finish.registration') }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                FINISH REGISTRATION — SELECT YOUR MEMBERSHIP
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Additional Notifications -->
                @if($notifications->count() > 0)
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                            <div class="bg-white border-2 border-gray-200 rounded-xl p-5 hover:border-blue-300 hover:shadow-lg transition-all duration-200">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-4 flex-1">
                                        <div class="p-2 bg-blue-100 rounded-lg">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-gray-900 font-semibold mb-1">{{ $notification->data['message'] ?? ($notification->data['title'] ?? 'Notification') }}</p>
                                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    @if(!$notification->read_at)
                                        <span class="w-3 h-3 bg-blue-600 rounded-full flex-shrink-0 mt-2"></span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-12 text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-gray-500 font-medium text-lg">No additional notifications</p>
                        <p class="text-sm text-gray-400 mt-1">You're all caught up!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- What You Can Do -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    What You Can Do
                </h2>
                <p class="text-gray-600">Available features for your account</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $availableFeatures = [
                        ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'text' => 'View profile details'],
                        ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'text' => 'Change password'],
                        ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'text' => 'Receive notifications'],
                        ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'text' => 'Browse site listings (read-only)'],
                    ];
                @endphp
                @foreach($availableFeatures as $feature)
                <div class="flex items-center gap-3 p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-200 hover:border-green-300 hover:shadow-md transition-all duration-200">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-green-800 font-semibold text-sm">{{ $feature['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- What's Restricted -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    What's Restricted
                </h2>
                <p class="text-gray-600">Complete registration to unlock these features</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $restrictedFeatures = [
                        ['text' => 'Cannot bid'],
                        ['text' => 'Cannot buy'],
                        ['text' => 'Cannot list items'],
                        ['text' => 'Cannot access auctions'],
                        ['text' => 'Cannot access messaging'],
                        ['text' => 'Cannot access payouts'],
                        ['text' => 'Cannot submit documents'],
                        ['text' => 'Cannot interact with transaction features'],
                    ];
                @endphp
                @foreach($restrictedFeatures as $feature)
                <div class="flex items-center gap-3 p-4 bg-gradient-to-br from-red-50 to-rose-50 rounded-xl border-2 border-red-200 hover:border-red-300 hover:shadow-md transition-all duration-200">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    <span class="text-red-800 font-semibold text-sm">{{ $feature['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div id="passwordModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 transform transition-all">
        <div class="absolute top-4 right-4">
            <button onclick="hidePasswordModal()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="mb-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Change Password</h3>
            <p class="text-gray-600">Update your account password</p>
        </div>
        <form method="POST" action="{{ route('basic-dashboard.change-password') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                <input type="password" 
                       name="current_password" 
                       required
                       class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                <input type="password" 
                       name="password" 
                       required
                       minlength="8"
                       class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" 
                       name="password_confirmation" 
                       required
                       minlength="8"
                       class="w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
            </div>
            <div class="flex items-center justify-end gap-3 pt-4">
                <button type="button" 
                        onclick="hidePasswordModal()" 
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
.border-b-3 {
    border-bottom-width: 3px;
}
</style>

<script>
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'text-blue-600', 'border-blue-600', 'bg-blue-50', 'bg-opacity-50');
        button.classList.add('text-gray-500', 'border-transparent');
    });
    
    // Show selected tab content
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Add active class to selected tab
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.add('active', 'text-blue-600', 'border-blue-600', 'bg-blue-50', 'bg-opacity-50');
    activeButton.classList.remove('text-gray-500', 'border-transparent');
}

function showPasswordModal() {
    document.getElementById('passwordModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hidePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('passwordModal');
    if (event.target == modal) {
        hidePasswordModal();
    }
}
</script>

@if($errors->any())
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <script>
        setTimeout(() => {
            const el = document.querySelector('.fixed.top-4');
            if (el) el.remove();
        }, 5000);
    </script>
@endif

@endsection
