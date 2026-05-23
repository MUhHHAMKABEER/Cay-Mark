@php
    $sellerPackage = $user->activeSubscription?->package ?? null;
    $isIndividualSeller = $isIndividualSeller
        ?? (empty($user->business_license_path)
            && ! ($sellerPackage && (float) ($sellerPackage->price ?? 0) === 0.0));
    $dur = old('auction_duration', $listing->auction_duration ?? 7);
@endphp

<style>
/* ── Card input icons ── */
.payment-card-wrap { position: relative; }
.payment-card-wrap .card-icon {
    position: absolute;
    left: 0.9rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1.1rem;
    pointer-events: none;
}
.payment-card-wrap .form-input { padding-left: 2.6rem; }
.payment-card-wrap .lock-icon {
    position: absolute;
    right: 0.9rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.95rem;
    pointer-events: none;
}

/* ── Card type badge ── */
#card-type-badge {
    display: none;
    position: absolute;
    right: 2.4rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

/* ── Terms box ── */
.terms-box {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    transition: border-color 0.2s ease;
}
.terms-box:focus-within { border-color: #063466; }
.terms-box a { color: #063466; font-weight: 600; text-decoration: underline; }
.terms-box a:hover { color: #1e3a8a; }

/* ── Payment secure badge ── */
.secure-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 0.3rem 0.7rem;
    font-weight: 600;
}

/* ── Business skip panel ── */
.business-skip-panel {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1.5px solid #bae6fd;
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.business-skip-icon {
    width: 44px;
    height: 44px;
    background: #0ea5e9;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>

<div id="section3" class="form-section" style="display: @if(session('error_section') === 'section3') block; border: 2px solid #ef4444; border-radius: 12px; @else none; @endif">

    {{-- ── Section header ── --}}
    <div class="section-header">
        <div class="section-icon">3</div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Auction Settings + Payment</h2>
            <p class="text-sm text-gray-500">Set your auction duration, pricing, and complete checkout</p>
        </div>
    </div>

    {{-- ══════════════════════════
         AUCTION DURATION
    ══════════════════════════ --}}
    <div class="mb-6">
        <label class="form-label">Auction Duration <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-2">
            @foreach([3, 5, 7, 14, 21, 28] as $days)
                <label class="duration-option border rounded-lg p-3 text-center cursor-pointer transition-all
                    {{ (string)$dur === (string)$days ? 'border-blue-600 bg-blue-50 shadow-sm' : 'border-gray-200 hover:border-gray-300' }}">
                    <input type="radio" name="auction_duration" value="{{ $days }}" required class="hidden"
                           {{ (string)$dur === (string)$days ? 'checked' : '' }}>
                    <div class="font-bold text-lg leading-tight">{{ $days }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Days</div>
                </label>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════════════
         PRICING
    ══════════════════════════ --}}
    <div class="mb-6">
        <h3 class="font-semibold text-gray-900 mb-1" style="font-size:0.9375rem">Pricing</h3>
        <p class="text-sm text-gray-500 mb-4">Set your opening bid. Reserve and Buy Now prices are optional.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="form-label">Starting Bid <span class="text-red-500">*</span></label>
                <div class="payment-card-wrap">
                    <span class="card-icon" style="font-size:0.95rem; font-weight:700; color:#475569">$</span>
                    <input type="number" name="starting_price" step="0.01" min="0.01" required
                           class="form-input" placeholder="0.00"
                           value="{{ old('starting_price', $listing->starting_price ?? '') }}">
                </div>
            </div>
            <div>
                <label class="form-label">Reserve Price <span class="text-gray-400 font-normal">(optional)</span></label>
                <div class="payment-card-wrap">
                    <span class="card-icon" style="font-size:0.95rem; font-weight:700; color:#94a3b8">$</span>
                    <input type="number" name="reserve_price" step="0.01" min="0"
                           class="form-input" placeholder="0.00"
                           value="{{ old('reserve_price', $listing->reserve_price ?? '') }}">
                </div>
            </div>
            <div>
                <label class="form-label">Buy Now Price <span class="text-gray-400 font-normal">(optional)</span></label>
                <div class="payment-card-wrap">
                    <span class="card-icon" style="font-size:0.95rem; font-weight:700; color:#94a3b8">$</span>
                    <input type="number" name="buy_now_price" step="0.01" min="0"
                           class="form-input" placeholder="0.00"
                           value="{{ old('buy_now_price', $listing->buy_now_price ?? '') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════
         PAYMENT SECTION
         Casual seller → full card form
         Business seller → skip panel
    ══════════════════════════ --}}
    @if($isIndividualSeller)

    {{-- ── Casual Seller: Card Payment Form ── --}}
    <div class="mb-6" id="payment-section">
        <div class="flex items-center justify-between mb-1 flex-wrap gap-2">
            <div>
                <h3 class="font-semibold text-gray-900" style="font-size:0.9375rem">Payment</h3>
                <p class="text-sm text-gray-500">A one-time <strong class="text-gray-700">$25 listing fee</strong> is charged per submission.</p>
            </div>
            <span class="secure-badge">
                <i class="fas fa-lock" style="font-size:0.7rem"></i>
                SSL Secured
            </span>
        </div>

        <div class="mt-4 bg-white border-2 border-gray-200 rounded-xl p-5" style="transition: border-color 0.2s;">

            {{-- Cardholder Name --}}
            <div class="mb-4">
                <label class="form-label">Cardholder Name <span class="text-red-500">*</span></label>
                <div class="payment-card-wrap">
                    <span class="material-icons-round card-icon" style="font-size:1.1rem">person_outline</span>
                    <input type="text" name="cardholder_name"
                           class="form-input"
                           placeholder="Name as it appears on card"
                           value="{{ old('cardholder_name') }}"
                           autocomplete="cc-name">
                </div>
            </div>

            {{-- Card Number --}}
            <div class="mb-4">
                <label class="form-label">Card Number <span class="text-red-500">*</span></label>
                <div class="payment-card-wrap">
                    <span class="material-icons-round card-icon">credit_card</span>
                    <input type="text" name="card_number" id="card_number_input"
                           class="form-input font-mono"
                           inputmode="numeric"
                           maxlength="19"
                           placeholder="1234 5678 9012 3456"
                           value="{{ old('card_number') }}"
                           autocomplete="cc-number">
                    <span id="card-type-badge"></span>
                    <span class="material-icons-round lock-icon" style="font-size:0.9rem">lock</span>
                </div>
            </div>

            {{-- Expiry + CVV ── side by side --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Expiration Date <span class="text-red-500">*</span></label>
                    <div class="payment-card-wrap">
                        <span class="material-icons-round card-icon" style="font-size:1rem">calendar_today</span>
                        <input type="text" name="card_expiry" id="card_expiry_input"
                               class="form-input"
                               placeholder="MM / YY"
                               maxlength="7"
                               value="{{ old('card_expiry') }}"
                               autocomplete="cc-exp"
                               inputmode="numeric">
                    </div>
                </div>
                <div>
                    <label class="form-label">
                        CVV <span class="text-red-500">*</span>
                        <span class="relative group ml-1 cursor-help" style="display:inline-block; vertical-align:middle;">
                            <span class="material-icons-round" style="font-size:0.95rem; color:#94a3b8; vertical-align:middle">help_outline</span>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 bg-gray-900 text-white text-xs rounded-lg px-3 py-2 opacity-0 group-hover:opacity-100 transition-opacity z-20 pointer-events-none leading-relaxed">
                                3-digit code on the back of your card. Amex cards use a 4-digit code on the front.
                            </span>
                        </span>
                    </label>
                    <div class="payment-card-wrap">
                        <span class="material-icons-round card-icon" style="font-size:1rem">security</span>
                        <input type="text" name="card_cvc" id="card_cvc_input"
                               class="form-input"
                               inputmode="numeric"
                               maxlength="4"
                               placeholder="CVV"
                               value="{{ old('card_cvc') }}"
                               autocomplete="cc-csc">
                        <span class="material-icons-round lock-icon" style="font-size:0.9rem">lock</span>
                    </div>
                </div>
            </div>

            {{-- Accepted cards strip --}}
            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                <span class="text-xs text-gray-400 font-medium">We accept:</span>
                @foreach(['VISA','MC','AMEX','DISC'] as $brand)
                <span style="font-size:0.65rem; font-weight:800; letter-spacing:0.04em; padding:0.2rem 0.5rem; border:1.5px solid #e2e8f0; border-radius:5px; color:#475569; background:#f8fafc;">{{ $brand }}</span>
                @endforeach
                <span class="ml-auto secure-badge">
                    <i class="fas fa-shield-alt" style="font-size:0.65rem"></i>
                    Encrypted &amp; Secure
                </span>
            </div>
        </div>
    </div>

    @else

    {{-- ── Business Seller: No fee, skip payment ── --}}
    <div class="business-skip-panel mb-6">
        <div class="business-skip-icon">
            <span class="material-icons-round text-white" style="font-size:1.3rem">workspace_premium</span>
        </div>
        <div>
            <p class="font-semibold text-sky-900" style="font-size:0.9375rem">No listing fee for your Business Seller account</p>
            <p class="text-sm text-sky-700 mt-0.5">Your subscription covers all listing submissions. Review your details and submit when ready.</p>
        </div>
    </div>

    @endif

    {{-- ══════════════════════════
         TERMS & CONFIRMATION
    ══════════════════════════ --}}
    <div class="terms-box mb-6">
        <div class="flex items-start gap-3">
            <input type="checkbox" name="terms_accepted" value="1" required id="terms_checkbox"
                   class="mt-1 flex-shrink-0"
                   style="width:18px; height:18px; accent-color:#063466; cursor:pointer;"
                   {{ old('terms_accepted') ? 'checked' : '' }}>
            <label for="terms_checkbox" class="text-sm text-gray-700 leading-relaxed cursor-pointer">
                I have carefully reviewed this submission and confirm that all information provided is accurate and complete.
                I agree to CayMark's
                <a href="{{ route('terms.of.service') }}" target="_blank" rel="noopener">Terms &amp; Conditions</a>
                and have read the
                <a href="{{ route('sellers-guide') }}" target="_blank" rel="noopener">Seller's Guide</a>
                and
                <a href="{{ route('help-center') }}" target="_blank" rel="noopener">seller resources</a>
                before submitting.
            </label>
        </div>
        <p class="text-xs text-gray-400 mt-3 ml-7">
            By submitting, you authorise CayMark to list your vehicle and process any applicable fees in accordance with our policies.
        </p>
    </div>

    {{-- ══════════════════════════
         NAVIGATION BUTTONS
    ══════════════════════════ --}}
    <div class="flex flex-col sm:flex-row gap-4 justify-between">
        <button type="button" onclick="showSection(2)" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </button>
        @php $listingBlocked = !($isEdit ?? false) && count($missingRequirements ?? []) > 0; @endphp
        <button type="submit" class="btn-success" id="submit-listing-btn"
            @if($listingBlocked) disabled title="Complete your profile before submitting a listing" @endif
            style="{{ $listingBlocked ? 'opacity:0.45;cursor:not-allowed;' : '' }}">
            @if($isEdit)
                <i class="fas fa-save mr-2"></i> Save Changes
            @elseif($listingBlocked)
                <i class="fas fa-lock mr-2"></i> Complete Profile to Submit
            @else
                <i class="fas fa-check-circle mr-2"></i> Complete Submission
            @endif
        </button>
    </div>
</div>

{{-- ── Payment input JS: card number spacing, expiry slash, CVV digits-only ── --}}
<script>
(function () {
    /* Card number — insert spaces every 4 digits, strip non-digits */
    var cardInput = document.getElementById('card_number_input');
    var cardBadge = document.getElementById('card-type-badge');
    if (cardInput) {
        cardInput.addEventListener('input', function () {
            var raw = this.value.replace(/\D/g, '').slice(0, 16);
            this.value = raw.replace(/(.{4})/g, '$1 ').trim();
            /* Basic card-type detection */
            if (cardBadge) {
                var first = raw.charAt(0);
                var first2 = raw.slice(0, 2);
                var first4 = raw.slice(0, 4);
                var label = '', color = '';
                if (first === '4') { label = 'Visa'; color = '#1a1f71'; }
                else if (['51','52','53','54','55'].some(function(p){ return first2 === p; }) || (parseInt(first4) >= 2221 && parseInt(first4) <= 2720)) { label = 'MC'; color = '#eb001b'; }
                else if (first2 === '34' || first2 === '37') { label = 'Amex'; color = '#007bc1'; }
                else if (first2 === '60' || first2 === '65' || first4 === '6011') { label = 'Disc'; color = '#ff6600'; }
                if (label && raw.length >= 1) {
                    cardBadge.textContent = label;
                    cardBadge.style.display = 'inline-block';
                    cardBadge.style.background = color;
                    cardBadge.style.color = '#fff';
                } else {
                    cardBadge.style.display = 'none';
                }
            }
        });
        /* On keydown: allow backspace to remove space + digit */
        cardInput.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && this.value.endsWith(' ')) {
                e.preventDefault();
                this.value = this.value.slice(0, -2);
            }
        });
    }

    /* Expiry — auto-insert " / " after 2 digits */
    var expiryInput = document.getElementById('card_expiry_input');
    if (expiryInput) {
        expiryInput.addEventListener('input', function (e) {
            var raw = this.value.replace(/\D/g, '').slice(0, 4);
            if (raw.length > 2) {
                this.value = raw.slice(0, 2) + ' / ' + raw.slice(2);
            } else {
                this.value = raw;
            }
        });
        expiryInput.addEventListener('keydown', function (e) {
            if (e.key === 'Backspace' && (this.value.endsWith(' / ') || this.value.endsWith('/'))) {
                e.preventDefault();
                this.value = this.value.replace(/\s*\/\s*$/, '').slice(0, -1);
            }
        });
    }

    /* CVV — digits only, 3–4 chars */
    var cvcInput = document.getElementById('card_cvc_input');
    if (cvcInput) {
        cvcInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 4);
        });
    }

    /* Duration option highlight on click */
    document.querySelectorAll('.duration-option').forEach(function (label) {
        label.addEventListener('click', function () {
            document.querySelectorAll('.duration-option').forEach(function (l) {
                l.classList.remove('border-blue-600', 'bg-blue-50', 'shadow-sm');
                l.classList.add('border-gray-200');
            });
            label.classList.add('border-blue-600', 'bg-blue-50', 'shadow-sm');
            label.classList.remove('border-gray-200');
        });
    });
})();
</script>
