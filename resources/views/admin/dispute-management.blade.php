@extends('layouts.admin')

@section('title', 'Dispute Management - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Dispute Management</h1>
        <p class="text-gray-600 mt-2">Manage all platform disputes</p>
    </div>

    <!-- Dispute Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Disputes</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $disputeStats['total'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-gavel text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Open</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $disputeStats['open'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">In Progress</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $disputeStats['in_progress'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Resolved</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $disputeStats['resolved'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <form method="GET" action="{{ route('admin.disputes') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ID, buyer, or seller..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="escalated" {{ request('status') == 'escalated' ? 'selected' : '' }}>Escalated</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
            @if(request('search') || request('status'))
            <div>
                <a href="{{ route('admin.disputes') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Clear
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Disputes Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">All Disputes</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buyer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($disputes as $dispute)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            #{{ $dispute->id ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $dispute->buyer->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $dispute->buyer->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $dispute->seller->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $dispute->seller->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $dispute->listing->item_number ?? 'Listing #' . ($dispute->listing_id ?? 'N/A') }}</div>
                            <div class="text-xs text-gray-500">{{ $dispute->listing->make ?? '' }} {{ $dispute->listing->model ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($dispute->type ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ ($dispute->status ?? '') === 'open' ? 'bg-red-100 text-red-800' : '' }}
                                {{ ($dispute->status ?? '') === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ ($dispute->status ?? '') === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ ($dispute->status ?? '') === 'escalated' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ !in_array($dispute->status ?? '', ['open', 'in_progress', 'resolved', 'escalated']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $dispute->status ?? 'N/A')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $dispute->created_at->format('M j, Y') ?? 'N/A' }}<br>
                            <span class="text-xs">{{ $dispute->created_at->format('g:i A') ?? '' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.disputes.view', $dispute->id ?? 0) }}" 
                                    class="text-blue-600 hover:text-blue-900" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="mb-4">
                                <i class="fas fa-gavel text-4xl text-gray-300"></i>
                            </div>
                            <p class="text-lg font-medium mb-2">No disputes found</p>
                            <p class="text-sm text-gray-400">Disputes will appear here when users file complaints or issues.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if(isset($disputes) && method_exists($disputes, 'hasPages') && $disputes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $disputes->links() }}
        </div>
        @endif
    </div>

    <!-- Coming Soon Notice (if no disputes exist) -->
    @if(!isset($disputes) || $disputes->isEmpty())
    <div class="bg-white rounded-lg shadow p-12 text-center mt-6">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <i class="fas fa-gavel text-6xl text-gray-300 mb-4"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Dispute Management System</h2>
            <p class="text-gray-600 mb-6">
                The dispute management system is currently being developed. This page will allow you to manage disputes between buyers and sellers.
            </p>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-left">
                <h3 class="font-semibold text-blue-900 mb-3">Planned Features:</h3>
                <ul class="space-y-2 text-blue-800">
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>View all open disputes between buyers and sellers</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Review evidence and messages from both parties</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Update dispute status and resolution</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Add admin notes and final resolution</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check-circle text-blue-600 mr-2 mt-1"></i>
                        <span>Track dispute timeline and history</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
