@extends('layouts.Buyer')
@section('title', 'Purchase Details – CayMark')
@section('content')

@php
    $listing = $invoice->listing;
    $pickupCode = $listing?->pickupCodeDisplay();
    $paid = ($invoice->payment_status ?? '') === 'paid';
    $vehicleTitle = $invoice->item_name ?? ($listing ? trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '')) : 'Purchase');
@endphp

<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 bg-clip-text text-transparent mb-1">Transaction details</h1>
        <p class="text-gray-600 text-sm">{{ $vehicleTitle }}</p>
    </div>

    @if($paid && $pickupCode && $listing && ! $listing->pickup_confirmed)
        <div class="rounded-2xl border-2 border-amber-300 bg-gradient-to-br from-amber-50 to-yellow-50 p-5 sm:p-6 mb-6 shadow-sm">
            <p class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-1">Pickup Code</p>
            <p class="text-2xl sm:text-3xl font-extrabold text-blue-800 tracking-wide font-mono mb-2">{{ $pickupCode }}</p>
            <p class="text-sm text-amber-950 leading-relaxed">Please present this code to your seller after vehicle pickup.</p>
            <button type="button" onclick="navigator.clipboard.writeText('{{ $pickupCode }}').then(() => { this.querySelector('span:last-child').textContent = 'Copied!'; setTimeout(() => { this.querySelector('span:last-child').textContent = 'Copy code'; }, 2000); })"
                class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-amber-400 bg-white text-amber-900 text-sm font-semibold hover:bg-amber-50 transition">
                <span class="material-icons-round text-base">content_copy</span>
                <span>Copy code</span>
            </button>
        </div>
    @elseif($paid && $listing && $listing->pickup_confirmed)
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 mb-6 text-emerald-900 text-sm">
            <span class="material-icons-round align-middle text-base mr-1">check_circle</span>
            Pickup confirmed. This sale is complete.
        </div>
    @elseif(! $paid)
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 mb-6 text-amber-900 text-sm">
            Payment is still pending. Complete payment to receive your pickup code and unlock messaging with the seller.
            <a href="{{ route('buyer.payment.checkout-single', $invoice->id) }}" class="block mt-2 font-semibold text-blue-700 hover:underline">Complete payment</a>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Order summary</h2>
        </div>
        <div class="p-5 sm:p-6 space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Invoice</span>
                <span class="font-mono font-semibold text-gray-900">#{{ $invoice->invoice_number ?? $invoice->id }}</span>
            </div>
            @if($listing)
                <div class="flex justify-between">
                    <span class="text-gray-600">Item</span>
                    <span class="font-medium text-gray-900 text-right">{{ $vehicleTitle }}</span>
                </div>
            @endif
            <div class="flex justify-between">
                <span class="text-gray-600">Payment status</span>
                <span class="font-semibold {{ $paid ? 'text-emerald-600' : 'text-amber-600' }}">{{ $paid ? 'Paid' : 'Pending' }}</span>
            </div>
            @if($paid)
                <div class="flex justify-between pt-2 border-t border-gray-100">
                    <span class="text-gray-600">Total paid</span>
                    <span class="text-lg font-bold text-gray-900">${{ number_format((float) $invoice->total_amount_due, 2) }}</span>
                </div>
            @endif
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('messaging.thread.show', $invoice->id) }}"
           class="inline-flex flex-1 items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-md hover:shadow-lg transition text-center">
            <span class="material-icons-round text-lg">forum</span>
            Messaging Center
        </a>
        @if($invoice->pdf_path)
            <a href="{{ route('buyer.invoice.download', $invoice->id) }}"
               class="inline-flex flex-1 items-center justify-center gap-2 px-5 py-3 rounded-xl border-2 border-gray-300 text-gray-800 font-semibold hover:border-blue-500 hover:text-blue-700 transition text-center">
                <span class="material-icons-round text-lg">description</span>
                Download invoice
            </a>
        @endif
    </div>

    <p class="mt-6 text-center">
        <a href="{{ route('buyer.auctions') }}?section=won" class="text-sm text-gray-500 hover:text-gray-800">← Back to My Auctions (Won)</a>
    </p>
</div>

@endsection
