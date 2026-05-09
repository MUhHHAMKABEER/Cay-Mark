@extends('layouts.Buyer')
@section('title', 'Payment Successful – CayMark')
@section('content')

@php
    $listing = $invoice->listing;
    $pickupCode = $listing?->pickupCodeDisplay();
    $vehicleTitle = $invoice->item_name ?? ($listing ? trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '')) : 'Your vehicle');
@endphp

<div class="max-w-2xl mx-auto">
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mb-4">
            <span class="material-icons-round text-4xl">check_circle</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Successful</h1>
        <p class="text-gray-600">{{ $vehicleTitle }}</p>
    </div>

    @if($pickupCode)
        <div class="rounded-2xl border-2 border-amber-300 bg-gradient-to-br from-amber-50 to-yellow-50 p-6 mb-6 shadow-sm">
            <p class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-2">Your Pickup Code</p>
            <p class="text-3xl sm:text-4xl font-extrabold text-blue-800 tracking-wide font-mono mb-3">{{ $pickupCode }}</p>
            <p class="text-sm text-amber-950 leading-relaxed">Save this code to present to your seller after pickup.</p>
            <button type="button" onclick="navigator.clipboard.writeText('{{ $pickupCode }}').then(() => { this.textContent = 'Copied!'; setTimeout(() => { this.textContent = 'Copy code'; }, 2000); })"
                class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-amber-400 bg-white text-amber-900 text-sm font-semibold hover:bg-amber-50 transition">
                <span class="material-icons-round text-base">content_copy</span>
                <span>Copy code</span>
            </button>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">What&apos;s next</h2>
        <p class="text-gray-700 text-sm leading-relaxed mb-4">
            Please proceed to the <strong>Messaging Center</strong> to arrange pickup details with your seller.
        </p>
        <a href="{{ route('messaging.thread.show', $invoice->id) }}"
           class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-md hover:shadow-lg transition">
            <span class="material-icons-round text-lg">forum</span>
            Open Messaging Center
        </a>
        <a href="{{ route('buyer.purchase.show', $invoice) }}"
           class="mt-3 block text-center sm:inline-block sm:ml-3 text-sm font-semibold text-blue-600 hover:text-blue-800">
            View purchase details
        </a>
    </div>

    <p class="text-center text-sm text-gray-500">
        A confirmation email has been sent to your registered address.
    </p>
</div>

@endsection
