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
        <div style="background:#FFFBEB;border:2px solid #C8A84B;border-radius:12px;padding:24px;margin-bottom:24px;text-align:center;">
            <p style="font-size:10px;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:.08em;margin:0 0 6px">Your Pickup Code</p>
            <p style="font-size:clamp(2rem,6vw,2.75rem);font-weight:800;color:#1e40af;font-family:'Courier New',monospace;letter-spacing:.06em;margin:0 0 10px;line-height:1.1">{{ $pickupCode }}</p>
            <p style="font-size:13px;color:#78350f;margin:0 0 14px;line-height:1.5">Save this code to present to your seller after pickup.</p>
            <button type="button"
                onclick="navigator.clipboard.writeText('{{ $pickupCode }}').then(() => { this.innerHTML='<span class=\'material-icons-round\' style=\'font-size:16px\'>check</span> Copied!'; setTimeout(() => { this.innerHTML='<span class=\'material-icons-round\' style=\'font-size:16px\'>content_copy</span> Copy code'; }, 2000); })"
                style="display:inline-flex;align-items:center;gap:6px;padding:8px 20px;border-radius:8px;border:1.5px solid #C8A84B;background:#fff;color:#92400e;font-size:13px;font-weight:600;cursor:pointer">
                <span class="material-icons-round" style="font-size:16px">content_copy</span> Copy code
            </button>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">What&apos;s next</h2>
        <p class="text-gray-700 text-sm leading-relaxed mb-4">
            Please proceed to the <strong>Messaging Center</strong> to arrange pickup details with your seller.
        </p>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('messaging.thread.show', $invoice->id) }}"
               class="inline-flex flex-1 items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-md hover:shadow-lg transition text-center">
                <span class="material-icons-round text-lg">forum</span>
                Go to Messaging Center
            </a>
            <a href="{{ route('buyer.purchase.show', $invoice) }}"
               class="inline-flex flex-1 items-center justify-center gap-2 px-5 py-3 rounded-xl border-2 border-gray-300 text-gray-800 font-semibold hover:border-blue-500 hover:text-blue-700 transition text-center">
                <span class="material-icons-round text-lg">description</span>
                View Invoice
            </a>
        </div>
    </div>

    <p class="text-center text-sm text-gray-500">
        A confirmation email has been sent to your registered address.
    </p>
</div>

@endsection
