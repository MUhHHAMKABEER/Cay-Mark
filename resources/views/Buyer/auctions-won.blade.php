@extends('layouts.Buyer')
@section('title', 'Auctions Won - CayMark')
@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Auctions Won</h1>
        <p class="text-gray-600">View your winning auctions and download invoices</p>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded">
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    @if($invoices->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Won Auctions Yet</h3>
            <p class="text-gray-600">You haven't won any auctions yet. Start bidding to see your wins here!</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($invoices as $invoice)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-start space-x-4">
                                @if($invoice->listing->images->first())
                                    <img src="{{ asset('uploads/listings/' . $invoice->listing->images->first()->image_path) }}" 
                                         alt="{{ $invoice->item_name }}"
                                         class="w-24 h-24 object-cover rounded-lg">
                                @else
                                    <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $invoice->item_name }}</h3>
                                    <p class="text-sm text-gray-600 mb-2">Item ID: {{ $invoice->item_id }}</p>
                                    <p class="text-sm text-gray-600">Sale Date: {{ $invoice->sale_date->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <div class="text-right">
                                @if($invoice->payment_status === 'paid')
                                    <span class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                        Paid
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm font-semibold">
                                        Payment Pending
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Winning Bid Amount</p>
                                <p class="text-lg font-bold text-gray-900">${{ number_format($invoice->winning_bid_amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Buyer Fees</p>
                                <p class="text-lg font-bold text-blue-600">${{ number_format($invoice->buyer_commission, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Amount Due</p>
                                <p class="text-2xl font-bold text-gray-900">${{ number_format($invoice->total_amount_due, 2) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            @if($invoice->pdf_path)
                                <a href="{{ route('buyer.invoice.download', $invoice->id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download Invoice
                                </a>
                            @endif

                            @if($invoice->payment_status === 'paid')
                                <a href="{{ route('post-auction.thread', $invoice->id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    Coordinate Pickup
                                </a>
                            @else
                                <a href="{{ route('buyer.payment.checkout-single', $invoice->id) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Make Payment
                                </a>
                            @endif
                        </div>
                        
                        @if($invoice->payment_status === 'paid' && $invoice->listing->pickup_pin)
                            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                <p class="text-sm font-semibold text-blue-900 mb-1">Your Pickup PIN:</p>
                                <p class="text-2xl font-bold text-blue-600 font-mono">{{ $invoice->listing->pickup_pin }}</p>
                                <p class="text-xs text-blue-700 mt-2">Share this PIN with the seller at pickup. Keep it secure.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection

