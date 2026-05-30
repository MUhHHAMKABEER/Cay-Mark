@extends('layouts.Buyer')
@section('title', 'Multi-Item Checkout – CayMark')
@section('content')

<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 bg-clip-text text-transparent mb-1">Checkout</h1>
        <p class="text-gray-600 text-sm">Complete payment for {{ $invoices->count() }} {{ Str::plural('item', $invoices->count()) }}</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4 shadow-sm">
            <p class="font-semibold text-red-800 text-sm mb-1">Payment failed — no charges were made.</p>
            <ul class="text-red-700 text-sm list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

        {{-- ── Order summary (left, sticky) ── --}}
        <div class="lg:col-span-5">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden lg:sticky lg:top-6">
                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Items selected</h2>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 border border-blue-200 rounded-full px-2.5 py-0.5">
                        {{ $invoices->count() }}
                    </span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach($invoices as $invoice)
                    @php
                        $img = $invoice->listing?->images?->first();
                        $imgUrl = $img
                            ? (str_contains($img->image_path, '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path))
                            : null;
                    @endphp
                    <div class="p-4">
                        <div class="flex gap-3 mb-3">
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" alt="{{ $invoice->item_name }}"
                                     class="w-16 h-16 object-cover rounded-lg flex-shrink-0 border border-gray-100">
                            @else
                                <div class="w-16 h-16 rounded-lg flex-shrink-0 bg-gray-100 flex items-center justify-center border border-gray-100">
                                    <span class="material-icons-round text-gray-400 text-2xl">directions_car</span>
                                </div>
                            @endif
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-900 text-sm leading-tight line-clamp-2">{{ $invoice->item_name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $invoice->item_id
                                        ? strtoupper($invoice->item_id)
                                        : ($invoice->listing?->item_number
                                            ? 'CM'.str_pad($invoice->listing->item_number,6,'0',STR_PAD_LEFT)
                                            : '—') }}
                                </p>
                            </div>
                        </div>
                        <div class="space-y-1.5 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Winning bid</span>
                                <span class="font-semibold text-gray-900">${{ number_format($invoice->winning_bid_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Buyer fee (6%, min $100)</span>
                                <span class="font-semibold text-blue-600">${{ number_format($invoice->buyer_commission, 2) }}</span>
                            </div>
                            @if(($invoice->deposit_applied ?? 0) > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Deposit applied
                                </span>
                                <span class="font-semibold text-green-600">−${{ number_format($invoice->deposit_applied, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between pt-1.5 border-t border-gray-100">
                                <span class="font-semibold text-gray-700">Subtotal</span>
                                <span class="font-bold text-gray-900">${{ number_format($invoice->total_amount_due, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                {{-- Grand Total --}}
                <div class="px-5 py-4 bg-gray-50 border-t-2 border-gray-200 flex justify-between items-center">
                    <span class="font-bold text-gray-900 text-base">Grand Total</span>
                    <span class="text-2xl font-extrabold text-gray-900">${{ number_format($grandTotal, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- ── Payment form (right) ── --}}
        <div class="lg:col-span-7">
            <form action="{{ route('buyer.payment.process') }}" method="POST"
                  class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                @csrf
                @foreach($invoices as $inv)
                    <input type="hidden" name="invoice_ids[]" value="{{ $inv->id }}">
                @endforeach

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
                            <strong>Sandbox:</strong> Use test card <code class="bg-amber-100 px-1 rounded font-mono">4242 4242 4242 4242</code> — expiry 12/26, CVC 123.
                        </p>
                    @endif

                    <div class="mt-6 pt-5 border-t border-gray-100 flex flex-wrap items-center gap-4">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg hover:from-blue-700 hover:to-indigo-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                            <span class="material-icons-round text-lg">payment</span>
                            Pay ${{ number_format($grandTotal, 2) }} Now
                        </button>
                        <a href="{{ route('buyer.auctions') }}"
                           class="text-sm text-gray-500 hover:text-gray-900 transition">
                            Cancel
                        </a>
                    </div>

                    <p class="mt-4 text-xs text-gray-400 text-center">
                        All items are paid together. If the payment fails, no charges are made and all items remain outstanding.
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    /* Format card number with spaces */
    document.getElementById('card_number')?.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        e.target.value = v.match(/.{1,4}/g)?.join(' ') || v;
    });
</script>
@endsection
