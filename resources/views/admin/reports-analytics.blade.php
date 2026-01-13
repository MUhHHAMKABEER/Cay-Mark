@extends('layouts.admin')

@section('title', 'Reports & Analytics - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
        <p class="text-gray-600 mt-2">Comprehensive analytics and reporting dashboard</p>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users (30d)</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $userGrowth->sum('count') ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Sales (30d)</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">${{ number_format($salesData->sum('total') ?? 0, 2) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Orders (30d)</p>
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $salesData->sum('count') ?? 0 }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-shopping-cart text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Subscriptions</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $subscriptionData->where('status', 'active')->sum('count') ?? 0 }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-id-card text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- User Growth Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">User Growth (Last 30 Days)</h2>
            <div class="h-64 flex items-center justify-center">
                <canvas id="userGrowthChart"></canvas>
            </div>
            @if($userGrowth->isEmpty())
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-chart-line text-4xl mb-3 text-gray-300"></i>
                <p>No user growth data available</p>
            </div>
            @endif
        </div>

        <!-- Sales Data Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Sales Data (Last 30 Days)</h2>
            <div class="h-64 flex items-center justify-center">
                <canvas id="salesChart"></canvas>
            </div>
            @if($salesData->isEmpty())
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-chart-bar text-4xl mb-3 text-gray-300"></i>
                <p>No sales data available</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Subscription Analytics -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Subscription Analytics</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subscriptionData as $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            Package #{{ $data->package_id ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $data->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $data->status === 'expired' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $data->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ !in_array($data->status, ['active', 'expired', 'pending']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($data->status ?? 'N/A') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $data->count ?? 0 }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-id-card text-4xl mb-3 text-gray-300"></i>
                            <p>No subscription data available</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dispute Analytics (Placeholder) -->
    @if($disputeData && $disputeData->isNotEmpty())
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Dispute Analytics</h2>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-gavel text-4xl mb-3 text-gray-300"></i>
            <p>Dispute analytics will be available when the Dispute model is created</p>
        </div>
    </div>
    @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
// User Growth Chart
@if($userGrowth->isNotEmpty())
const userGrowthCtx = document.getElementById('userGrowthChart');
if (userGrowthCtx) {
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($userGrowth as $data)
                '{{ $data->date }}',
                @endforeach
            ],
            datasets: [{
                label: 'New Users',
                data: [
                    @foreach($userGrowth as $data)
                    {{ $data->count }},
                    @endforeach
                ],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
@endif

// Sales Chart
@if($salesData->isNotEmpty())
const salesCtx = document.getElementById('salesChart');
if (salesCtx) {
    new Chart(salesCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($salesData as $data)
                '{{ $data->date }}',
                @endforeach
            ],
            datasets: [{
                label: 'Sales Amount ($)',
                data: [
                    @foreach($salesData as $data)
                    {{ $data->total ?? 0 }},
                    @endforeach
                ],
                backgroundColor: 'rgba(34, 197, 94, 0.6)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}
@endif
</script>
@endsection
