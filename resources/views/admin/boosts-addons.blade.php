@extends('layouts.admin')

@section('title', 'Boosts & Add-ons Management - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Boosts & Add-ons Management</h1>
        <p class="text-gray-600 mt-2">Manage listing boosts and add-on features</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Boosts</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_boosts'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-rocket text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Boosts</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active_boosts'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Add-ons</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['total_addons'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-plus-circle text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Add-ons</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['active_addons'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-check text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Coming Soon Notice -->
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <i class="fas fa-rocket text-6xl text-gray-300 mb-4"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Boosts & Add-ons Feature</h2>
            <p class="text-gray-600 mb-6">
                The boosts and add-ons management feature is currently under development. 
                This page will allow you to manage listing boosts and additional features for sellers.
            </p>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-left">
                <h3 class="font-semibold text-blue-900 mb-3">Planned Features:</h3>
                <ul class="space-y-2 text-blue-800">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Manage listing placement tiers (Basic, Boosted, Premium)</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Configure boost pricing and duration</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Track active boosts and add-ons</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Manage add-on features (highlighted listings, featured placement, etc.)</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
