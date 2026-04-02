@extends('layouts.admin')

@section('title', 'Pending Payments - Admin')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="bg-white shadow-sm mb-6 rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-900">Pending Payments</h1>
        <p class="text-gray-600 mt-2">Buyers who owe payment after winning an auction</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Pending Invoices</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $invoices->total() }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">Total Amount Owed</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">${{ number_format($totalOwed, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buyer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Owed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Deadline</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</div>
                            <div class="text-xs text-gray-500">{{ $invoice->created_at->format('M j, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $invoice->buyer->name ?? 'Unknown' }}</div>
                            <div class="text-xs text-gray-500">{{ $invoice->buyer->email ?? '' }}</div>
                            @if($invoice->buyer->phone ?? false)
                            <div class="text-xs text-gray-500">{{ $invoice->buyer->phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $invoice->item_name ?? ($invoice->listing ? ($invoice->listing->year . ' ' . $invoice->listing->make . ' ' . $invoice->listing->model) : 'N/A') }}</div>
                            <div class="text-xs text-gray-500">{{ $invoice->listing->item_number ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-orange-600">${{ number_format($invoice->total_amount_due, 2) }}</div>
                            <div class="text-xs text-gray-500">Bid: ${{ number_format($invoice->winning_bid_amount, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($invoice->payment_deadline)
                                <div class="text-sm {{ $invoice->payment_deadline->isPast() ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                    {{ $invoice->payment_deadline->format('M j, Y g:i A') }}
                                </div>
                                @if($invoice->payment_deadline->isPast())
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-800">Overdue</span>
                                @else
                                    <div class="text-xs text-gray-500">{{ $invoice->payment_deadline->diffForHumans() }}</div>
                                @endif
                            @else
                                <span class="text-gray-400 text-sm">No deadline</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-check-circle text-4xl mb-3 text-green-300"></i>
                            <p>No pending payments</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
