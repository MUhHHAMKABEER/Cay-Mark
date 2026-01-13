@extends('layouts.admin')

@section('title', 'Membership Management - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Membership Management</h1>
        <p class="text-gray-600 mt-2">Manage all user subscriptions and memberships</p>
    </div>

    <!-- Membership Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $membershipStats['total'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-id-card text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $membershipStats['active'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Expired</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $membershipStats['expired'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $membershipStats['pending_renewal'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Expiring Soon</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $membershipStats['expiring_soon'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Memberships Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">All Memberships</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($memberships as $membership)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($membership->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $membership->user->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $membership->user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $membership->package->title ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">${{ number_format($membership->package->price ?? 0, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $membership->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $membership->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $membership->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $membership->status === 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($membership->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $membership->starts_at ? $membership->starts_at->format('M j, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($membership->ends_at)
                                {{ $membership->ends_at->format('M j, Y') }}
                                @if($membership->ends_at->isPast())
                                    <span class="text-red-600 text-xs block">Expired</span>
                                @elseif($membership->ends_at->diffInDays(now()) <= 7)
                                    <span class="text-orange-600 text-xs block">Expiring Soon</span>
                                @endif
                            @else
                                <span class="text-gray-400">No expiry</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $membership->created_at->format('M j, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-id-card text-4xl mb-3 text-gray-300"></i>
                            <p>No memberships found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($memberships->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $memberships->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
