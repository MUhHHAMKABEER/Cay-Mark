@extends('layouts.admin')

@section('title', 'Payment Management - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Payment Management</h1>
        <p class="text-gray-600 mt-2">Manage all platform payments</p>
    </div>

    <!-- Payment Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">${{ number_format($paymentStats['total_revenue'] ?? 0, 2) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Release</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">${{ number_format($paymentStats['pending_release'] ?? 0, 2) }}</p>
                </div>
                <div class="p-3 bg-orange-100 rounded-full">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Held</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">${{ number_format($paymentStats['held'] ?? 0, 2) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-lock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ $paymentStats['completed'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Failed</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $paymentStats['failed'] ?? 0 }}</p>
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
                    <p class="text-2xl font-bold text-purple-600 mt-1">{{ $paymentStats['pending'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-hourglass-half text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <form method="GET" action="{{ route('admin.payments') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="buyer_id" value="{{ request('buyer_id') }}" placeholder="Buyer ID..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="pending_release" {{ request('status') == 'pending_release' ? 'selected' : '' }}>Pending Release</option>
                    <option value="held" {{ request('status') == 'held' ? 'selected' : '' }}>Held</option>
                </select>
            </div>
            <div>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
            @if(request('status') || request('buyer_id') || request('date_from') || request('date_to'))
            <div>
                <a href="{{ route('admin.payments') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Clear
                </a>
            </div>
            @endif
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">All Payments</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buyer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item/Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            #{{ $payment->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $payment->user->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $payment->user->email ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($payment->invoice)
                            <div class="text-sm font-medium text-gray-900">{{ $payment->invoice->item_name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">Invoice #{{ $payment->invoice->invoice_number ?? 'N/A' }}</div>
                            @elseif($payment->listing)
                            <div class="text-sm font-medium text-gray-900">{{ $payment->listing->item_number ?? 'Listing #' . $payment->listing_id }}</div>
                            <div class="text-xs text-gray-500">Listing</div>
                            @else
                            <div class="text-sm text-gray-500">Subscription Payment</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">${{ number_format($payment->amount ?? 0, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst(str_replace('_', ' ', $payment->method ?? 'N/A')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $payment->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $payment->status === 'pending_release' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $payment->status === 'held' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ !in_array($payment->status, ['completed', 'pending', 'failed', 'pending_release', 'held']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $payment->status ?? 'N/A')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $payment->created_at->format('M j, Y') }}<br>
                            <span class="text-xs">{{ $payment->created_at->format('g:i A') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="#" onclick="event.preventDefault(); showUpdateModal({{ $payment->id }}, '{{ $payment->status }}');" 
                                    class="text-blue-600 hover:text-blue-900" title="Update Status">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($payment->invoice)
                                <a href="{{ route('admin.invoice.download', $payment->invoice->id) }}" 
                                    class="text-green-600 hover:text-green-900" title="Download Invoice">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-credit-card text-4xl mb-3 text-gray-300"></i>
                            <p>No payments found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Update Status Modal -->
<div id="updateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <form id="updateForm" method="POST">
            @csrf
            @method('POST')
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Payment Status</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                    <option value="pending_release">Pending Release</option>
                    <option value="held">Held</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeUpdateModal()" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showUpdateModal(paymentId, currentStatus) {
    const form = document.getElementById('updateForm');
    form.action = "{{ route('admin.payments.update-status', ':id') }}".replace(':id', paymentId);
    form.querySelector('select[name="status"]').value = currentStatus;
    document.getElementById('updateModal').classList.remove('hidden');
}

function closeUpdateModal() {
    document.getElementById('updateModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('updateModal');
    if (event.target == modal) {
        closeUpdateModal();
    }
}
</script>
@endsection
