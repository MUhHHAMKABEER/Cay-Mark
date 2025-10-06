@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
    <p class="text-gray-600">Welcome back, {{ auth()->user()->name }}</p>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <i class="fas fa-users text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Total Users</h3>
                {{-- <p class="text-2xl font-bold">{{ $stats['total_users'] }}</p> --}}
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <i class="fas fa-list text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Total Listings</h3>
                {{-- <p class="text-2xl font-bold">{{ $stats['total_listings'] }}</p> --}}
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <i class="fas fa-id-card text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Active Memberships</h3>
                {{-- <p class="text-2xl font-bold">{{ $stats['active_memberships'] }}</p> --}}
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-red-100 text-red-600">
                <i class="fas fa-exclamation-triangle text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Open Disputes</h3>
                {{-- <p class="text-2xl font-bold">{{ $stats['open_disputes'] }}</p> --}}
            </div>
        </div>
    </div>
</div>

<!-- Alerts Section -->
{{-- @if(count($alerts) > 0)
<div class="mb-8">
    <h2 class="text-xl font-semibold mb-4">System Alerts</h2>
    <div class="space-y-3">
        @foreach($alerts as $alert)
        <div class="flex items-center p-4 rounded-lg border-l-4 
            {{ $alert['type'] == 'danger' ? 'bg-red-50 border-red-500 text-red-700' : '' }}
            {{ $alert['type'] == 'warning' ? 'bg-yellow-50 border-yellow-500 text-yellow-700' : '' }}
            {{ $alert['type'] == 'info' ? 'bg-blue-50 border-blue-500 text-blue-700' : '' }}">
            <i class="fas fa-exclamation-circle mr-3"></i>
            <span class="flex-1">{{ $alert['message'] }}</span>
            <a href="{{ $alert['link'] }}" class="ml-4 text-sm font-medium underline">View</a>
        </div>
        @endforeach
    </div>
</div>
@endif --}}

<!-- Recent Activity -->
<div class="bg-white rounded-lg shadow">
    <div class="p-6 border-b">
        <h2 class="text-xl font-semibold">Recent Activity</h2>
    </div>
    <div class="divide-y">
        {{-- @forelse($recentActivities as $activity)
        <div class="p-4 flex items-center">
            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center mr-4">
                <i class="fas fa-{{ $activity['icon'] }} text-gray-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-gray-800">{{ $activity['message'] }}</p>
                <p class="text-sm text-gray-500">{{ $activity['time']->diffForHumans() }}</p>
            </div>
        </div>
        @empty
        <div class="p-6 text-center text-gray-500">
            No recent activity
        </div>
        @endforelse --}}
    </div>
</div>
@endsection