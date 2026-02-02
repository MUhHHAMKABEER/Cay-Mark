@extends('layouts.Buyer')
@section('title', 'Checkout – CayMark')
@section('content')

<div class="max-w-6xl mx-auto">
    {{-- Page header – match dashboard gradient title --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 bg-clip-text text-transparent mb-1">Checkout</h1>
        <p class="text-gray-600 text-sm">Complete payment for your auction win</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4 shadow-sm">
            <ul class="text-red-800 text-sm list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Horizontal layout: summary left, payment right --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
        {{-- Order summary (left) – sticky on large screens --}}
        <div class="lg:col-span-5">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden lg:sticky lg:top-6">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Order summary</h2>
                </div>
                <div class="p-5">
                    <div class="flex gap-4">
                        @if($invoice->listing && $invoice->listing->images && $invoice->listing->images->first())
                            <img src="{{ asset('uploads/listings/' . $invoice->listing->images->first()->image_path) }}"
                                 alt="{{ $invoice->item_name }}"
                                 class="w-28 h-28 object-cover rounded-lg flex-shrink-0 border border-gray-100">
                        @else
                            <div class="w-28 h-28 rounded-lg flex-shrink-0 bg-gray-100 flex items-center justify-center border border-gray-100">
                                <span class="material-icons-round text-gray-400 text-4xl">directions_car</span>
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ $invoice->item_name }}</h3>
                            <p class="text-xs text-gray-500 mt-1">Item #{{ $invoice->item_id ?? $invoice->listing->item_number ?? '—' }}</p>
                            <p class="text-xs text-gray-500">Invoice #{{ $invoice->invoice_number ?? $invoice->id }}</p>
                        </div>
                    </div>
                    <div class="mt-5 pt-4 border-t border-gray-100 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Winning bid</span>
                            <span class="font-semibold text-gray-900">${{ number_format($invoice->winning_bid_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Buyer fees</span>
                            <span class="font-semibold text-blue-600">${{ number_format($invoice->buyer_commission, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-baseline pt-3 border-t border-gray-200">
                            <span class="text-sm font-medium text-gray-700">Total due</span>
                            <span class="text-2xl font-bold text-gray-900">${{ number_format($invoice->total_amount_due, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment form (right) --}}
        <div class="lg:col-span-7">
            <form action="{{ route('buyer.payment.process') }}" method="POST" class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                @csrf
                <input type="hidden" name="invoice_ids[]" value="{{ $invoice->id }}">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Payment details</h2>
                </div>
                <div class="p-5 lg:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label for="cardholder_name" class="block text-sm font-medium text-gray-700 mb-1.5">Name on card</label>
                            <input type="text" id="cardholder_name" name="cardholder_name" required
                                   value="{{ old('cardholder_name') }}"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="John Doe" autocomplete="cc-name">
                        </div>
                        <div class="sm:col-span-2">
                            <label for="card_number" class="block text-sm font-medium text-gray-700 mb-1.5">Card number</label>
                            <input type="text" id="card_number" name="card_number" required
                                   value="{{ old('card_number') }}"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 font-mono placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="4242 4242 4242 4242" autocomplete="cc-number" maxlength="19">
                        </div>
                        <div>
                            <label for="card_expiry" class="block text-sm font-medium text-gray-700 mb-1.5">Expiry (MM/YY)</label>
                            <input type="text" id="card_expiry" name="card_expiry" required
                                   value="{{ old('card_expiry') }}"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="12/26" autocomplete="cc-exp">
                        </div>
                        <div>
                            <label for="card_cvv" class="block text-sm font-medium text-gray-700 mb-1.5">CVC</label>
                            <input type="text" id="card_cvv" name="card_cvv" required
                                   value="{{ old('card_cvv') }}"
                                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="123" autocomplete="cc-csc">
                        </div>
                    </div>

                    @if(config('services.payment.sandbox', true))
                        <p class="mt-4 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                            <strong>Sandbox:</strong> Use test card <code class="bg-amber-100 px-1 rounded font-mono">4242 4242 4242 4242</code> for success. Any expiry (e.g. 12/26) and CVC (e.g. 123) work.
                        </p>
                    @endif

                    <div class="mt-6 pt-5 border-t border-gray-100 flex flex-wrap items-center gap-4">
                        <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            Pay ${{ number_format($invoice->total_amount_due, 2) }}
                        </button>
                        <a href="{{ route('buyer.auctions-won') }}" class="text-sm text-gray-500 hover:text-gray-900 transition">Cancel and return to Auctions Won</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
