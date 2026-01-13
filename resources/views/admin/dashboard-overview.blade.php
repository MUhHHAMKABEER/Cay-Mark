@extends('layouts.admin')

@section('title', 'Admin Dashboard - CayMark')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600 mt-2">Welcome back, {{ auth()->user()->name }}</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Active Listings -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Listings</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_active_listings'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-list text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Listings Awaiting Approval -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Approval</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['listings_awaiting_approval'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_users'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $stats['total_buyers'] ?? 0 }} buyers, {{ $stats['total_sellers'] ?? 0 }} sellers
                    </p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Auctions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Auctions</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['active_auctions'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-gavel text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Payments Pending -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Payments Pending</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['payments_pending'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-credit-card text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Payouts Pending -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Payouts Pending</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['payouts_pending'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-full">
                    <i class="fas fa-money-bill-wave text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Open Disputes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Open Disputes</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['open_disputes'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-100">Quick Actions</p>
                    <p class="text-lg font-semibold mt-2">Manage Platform</p>
                </div>
                <div class="p-3 bg-white bg-opacity-20 rounded-full">
                    <i class="fas fa-cog text-white text-xl"></i>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <a href="{{ route('admin.listing-review') }}" class="block text-sm hover:underline">Review Listings</a>
                <a href="{{ route('admin.users') }}" class="block text-sm hover:underline">Manage Users</a>
                <a href="{{ route('admin.payments') }}" class="block text-sm hover:underline">View Payments</a>
            </div>
        </div>
    </div>

    <!-- Alerts Section -->
    @if(isset($alerts) && count($alerts) > 0)
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold text-gray-900">System Alerts</h2>
        </div>
        <div class="p-6 space-y-3">
            @foreach($alerts as $alert)
            <div class="flex items-center p-4 rounded-lg border-l-4 
                {{ $alert['type'] == 'danger' ? 'bg-red-50 border-red-500 text-red-700' : '' }}
                {{ $alert['type'] == 'warning' ? 'bg-yellow-50 border-yellow-500 text-yellow-700' : '' }}
                {{ $alert['type'] == 'info' ? 'bg-blue-50 border-blue-500 text-blue-700' : '' }}">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <span class="flex-1">{{ $alert['message'] }}</span>
                @if(isset($alert['link']))
                <a href="{{ $alert['link'] }}" class="ml-4 text-sm font-medium underline">View</a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-900">Recent Activity</h2>
            </div>
            <div class="p-6">
                @if(isset($recentActivities) && count($recentActivities) > 0)
                <div class="space-y-4">
                    @foreach($recentActivities as $activity)
                    <div class="flex items-start">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4 flex-shrink-0">
                            <i class="fas fa-{{ $activity['icon'] ?? 'circle' }} text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-800">{{ $activity['message'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">
                                @if(isset($activity['time']))
                                    {{ $activity['time']->diffForHumans() }}
                                @else
                                    {{ $activity['timestamp'] ?? 'Recently' }}
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>No recent activity</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Signups -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-900">Recent User Signups</h2>
            </div>
            <div class="p-6">
                @if(isset($recentSignups) && $recentSignups->count() > 0)
                <div class="space-y-4">
                    @foreach($recentSignups as $user)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                <span class="text-blue-600 font-semibold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-1 text-xs font-medium rounded
                                {{ $user->role == 'buyer' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $user->role == 'seller' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($user->role ?? 'guest') }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-users text-4xl mb-3"></i>
                    <p>No recent signups</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
