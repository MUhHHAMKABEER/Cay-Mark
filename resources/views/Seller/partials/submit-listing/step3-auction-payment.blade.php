@php
    $sellerPackage = $user->activeSubscription?->package ?? null;
    $isIndividualSeller = $isIndividualSeller ?? ($sellerPackage && (float) $sellerPackage->price === 25.00);
    $dur = old('auction_duration', $listing->auction_duration ?? 7);
@endphp
<div id="section3" class="form-section" style="display: @if(session('error_section') === 'section3') block; border: 2px solid #ef4444; border-radius: 12px; @else none; @endif">
    <div class="section-header">
        <div class="section-icon">3</div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Auction Settings + Payment</h2>
            <p class="text-sm text-gray-600">Duration, pricing, and checkout</p>
        </div>
    </div>

    <div class="mb-4 flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="form-label">Auction Duration <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
                @foreach([3, 5, 7, 14, 21, 28] as $days)
                    <label class="duration-option border rounded-lg p-3 text-center cursor-pointer {{ (string)$dur === (string)$days ? 'border-blue-600 bg-blue-50' : 'border-gray-200' }}">
                        <input type="radio" name="auction_duration" value="{{ $days }}" required class="hidden" {{ (string)$dur === (string)$days ? 'checked' : '' }}>
                        <div class="font-semibold text-lg">{{ $days }}</div>
                        <div class="text-xs text-gray-600">Days</div>
                    </label>
                @endforeach
            </div>
        </div>
        <button type="button" class="btn-secondary text-sm" title="Product behavior TBD" disabled>Auctions</button>
    </div>

    <div class="mb-4">
        <h3 class="text-base font-semibold mb-3">Pricing</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Starting Bid <span class="text-red-500">*</span></label>
                <input type="number" name="starting_price" step="0.01" min="0.01" required class="form-input"
                       value="{{ old('starting_price', $listing->starting_price ?? '') }}">
            </div>
            <div>
                <label class="form-label">Reserve Price (Optional)</label>
                <input type="number" name="reserve_price" step="0.01" min="0" class="form-input"
                       value="{{ old('reserve_price', $listing->reserve_price ?? '') }}">
            </div>
            <div>
                <label class="form-label">Buy Now Price (Optional)</label>
                <input type="number" name="buy_now_price" step="0.01" min="0" class="form-input"
                       value="{{ old('buy_now_price', $listing->buy_now_price ?? '') }}">
            </div>
        </div>
    </div>

    @if($isIndividualSeller)
    <div class="mb-4 p-4 border border-amber-200 bg-amber-50 rounded-xl">
        <h3 class="font-semibold text-amber-900 mb-2">Payment — $25 Listing Fee</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="form-label">Cardholder Name <span class="text-red-500">*</span></label>
                <input type="text" name="cardholder_name" class="form-input" value="{{ old('cardholder_name') }}" autocomplete="cc-name">
            </div>
            <div>
                <label class="form-label">Card Number <span class="text-red-500">*</span></label>
                <input type="text" name="card_number" class="form-input" inputmode="numeric" maxlength="19" value="{{ old('card_number') }}" autocomplete="cc-number">
            </div>
            <div>
                <label class="form-label">Expiration (MM/YY) <span class="text-red-500">*</span></label>
                <input type="text" name="card_expiry" class="form-input" placeholder="MM/YY" maxlength="5" value="{{ old('card_expiry') }}" autocomplete="cc-exp">
            </div>
            <div>
                <label class="form-label">CVV <span class="text-red-500">*</span></label>
                <input type="text" name="card_cvc" class="form-input" inputmode="numeric" maxlength="4" value="{{ old('card_cvc') }}" autocomplete="cc-csc">
            </div>
        </div>
    </div>
    @else
    <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
        No listing fee for your business account. Review and submit below.
    </div>
    @endif

    <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
        <label class="flex items-start gap-2 cursor-pointer">
            <input type="checkbox" name="terms_accepted" value="1" required class="mt-1" {{ old('terms_accepted') ? 'checked' : '' }}>
            <span class="text-sm text-gray-700">
                I have reviewed this submission carefully and agree to CayMark's
                <a href="#" class="text-blue-600 underline">terms and conditions</a> and seller resources.
            </span>
        </label>
    </div>

    <div class="flex flex-col sm:flex-row gap-4 justify-between">
        <button type="button" onclick="showSection(2)" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i> Back</button>
        <button type="submit" class="btn-success">
            @if($isEdit)<i class="fas fa-save mr-2"></i> Save changes @else<i class="fas fa-check-circle mr-2"></i> Complete Submission @endif
        </button>
    </div>
</div>
