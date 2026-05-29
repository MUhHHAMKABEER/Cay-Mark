@extends('layouts.welcome')
@section('title', 'Complete Registration — CayMark Island Exchange')
@section('content')

@php
    $isBusinessSeller = $finishData['role'] === 'seller' && $package->price > 0;
    $paymentRequired  = $finishData['role'] === 'buyer' || $isBusinessSeller;
    $idTypes          = ['Passport', 'NIB', 'Driver\'s License', 'Voter\'s Card', 'National ID'];
@endphp

@push('styles')
<style>
    .cm-sec-title { font-size:13px; font-weight:700; color:#1A1A1A; display:flex; align-items:center; gap:8px; margin-bottom:14px; }
    .cm-sec-title .material-symbols-outlined { font-size:18px; color:#1B3A6B; }
    .cm-card { background:#fff; border:1.5px solid #E2E5E9; border-radius:12px; padding:20px 22px; }

    .cm-drop {
        display:flex; flex-direction:column; align-items:center; justify-content:center; gap:4px;
        border:1.5px dashed #CBD5E1; border-radius:10px; padding:22px 16px; text-align:center;
        cursor:pointer; background:#F8FAFC; transition:border-color .2s, background .2s;
    }
    .cm-drop:hover, .cm-drop.is-drag { border-color:#1B3A6B; background:#EEF2F9; }
    .cm-drop.is-filled { border-color:#16A34A; background:#F0FDF4; border-style:solid; }
    .cm-drop-ico { font-size:30px; color:#1B3A6B; }
    .cm-drop.is-filled .cm-drop-ico { color:#16A34A; }
    .cm-drop-title { font-size:13px; font-weight:600; color:#374151; word-break:break-all; }
    .cm-drop-sub { font-size:11.5px; color:#9CA3AF; }

    .cm-doc-num { width:22px; height:22px; border-radius:50%; background:#1B3A6B; color:#fff;
                  font-size:11px; font-weight:700; display:inline-flex; align-items:center; justify-content:center; }
    .cm-remove { font-size:12px; font-weight:600; color:#DC2626; background:none; border:none; cursor:pointer; }
    .cm-remove:hover { text-decoration:underline; }
    .cm-summary { background:#F5F6F7; border:1px solid #E2E5E9; border-radius:12px; padding:16px 18px; }
    .cm-summary-row { display:flex; justify-content:space-between; align-items:center; font-size:13.5px; }
</style>
@endpush

<x-auth.split maxw="600px">

    {{-- Stepper --}}
    <div class="mb-8"><x-auth.stepper :current="3" /></div>

    {{-- Heading --}}
    <div class="mb-7">
        <h1 class="text-[28px] font-bold leading-tight" style="color:#1A1A1A">Verify Your Identity</h1>
        <p class="text-sm mt-1" style="color:#6B7280">Upload your documents{{ $paymentRequired ? ' and complete payment' : '' }} to complete registration.</p>
    </div>

    {{-- Alerts --}}
    @if (session('error') || $errors->any())
        <div class="space-y-3 mb-6">
            @if (session('error'))
                <div class="flex items-start gap-2.5 px-4 py-3 rounded-lg text-sm" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
                    <span class="material-symbols-outlined" style="font-size:18px;color:#DC2626">error</span>{{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="px-4 py-3 rounded-lg text-sm" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
                    <p class="font-semibold mb-1">Please fix the following:</p>
                    <ul class="space-y-0.5 list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('finish.registration.complete') }}" enctype="multipart/form-data" id="complete-registration-form" class="space-y-6">
        @csrf

        {{-- ── Membership summary ── --}}
        <div class="cm-summary">
            <div class="cm-summary-row pb-2 mb-2" style="border-bottom:1px solid #E2E5E9">
                <span style="color:#6B7280">Membership</span>
                <span class="font-semibold" style="color:#1A1A1A">{{ ucfirst($finishData['role']) }} — {{ $package->title }}</span>
            </div>
            <div class="cm-summary-row">
                <span style="color:#6B7280">Price</span>
                <span class="font-bold" style="color:{{ $package->price > 0 ? '#1B3A6B' : '#16A34A' }}">
                    {{ $package->price > 0 ? '$' . number_format($package->price, 2) : 'Free' }}
                </span>
            </div>
        </div>

        {{-- ── Identity verification ── --}}
        <div class="cm-card">
            <p class="cm-sec-title"><span class="material-symbols-outlined">badge</span>Government-Issued ID</p>
            <p class="text-[13px] mb-5" style="color:#6B7280">Upload two government-issued IDs. Accepted: Passport, NIB, Driver's License, Voter's Card, or National ID.</p>

            {{-- ID 1 --}}
            <div class="mb-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="cm-doc-num">1</span>
                    <span class="text-[13px] font-semibold" style="color:#1A1A1A">Document 1</span>
                </div>
                <div class="cm-field-wrap mb-3">
                    <label class="cm-auth-label">Document Type</label>
                    <div class="cm-auth-fieldwrap">
                        <select name="id_type" required class="cm-auth-input cm-auth-select">
                            <option value="">Select type…</option>
                            @foreach ($idTypes as $opt)
                                <option value="{{ $opt }}" @selected(old('id_type') === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined cm-auth-chevron">expand_more</span>
                    </div>
                    @error('id_type')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
                </div>
                <label class="cm-drop" data-dropzone data-input="id_document">
                    <span class="material-symbols-outlined cm-drop-ico">cloud_upload</span>
                    <span class="cm-drop-title">Click to upload or drag and drop</span>
                    <span class="cm-drop-sub">JPG, PNG or PDF — max 5MB</span>
                    <input type="file" name="id_document" id="id_document" accept=".jpg,.jpeg,.png,.pdf" required class="sr-only">
                </label>
                @error('id_document')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
                <div id="id-doc-preview-wrap" class="hidden mt-3 flex items-center gap-3">
                    <div id="id-doc-preview-card" class="doc-preview-card cursor-pointer" data-doc-type="id" title="Click to preview">
                        <div id="id-doc-thumb" class="w-16 h-16 rounded-lg border border-gray-200 overflow-hidden flex items-center justify-center text-gray-300 text-xs bg-gray-50"></div>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[12px]" style="color:#6B7280">Selected: <span id="id-doc-filename" class="font-semibold" style="color:#1A1A1A"></span></p>
                        <div class="flex items-center gap-3 mt-1">
                            <button type="button" class="id-doc-preview-btn text-[12px] font-semibold" style="color:#1B3A6B">Preview</button>
                            <button type="button" class="cm-remove" data-remove-input="id_document" data-remove-wrap="id-doc-preview-wrap">Remove</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ID 2 --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="cm-doc-num">2</span>
                    <span class="text-[13px] font-semibold" style="color:#1A1A1A">Document 2</span>
                </div>
                <div class="cm-field-wrap mb-3">
                    <label class="cm-auth-label">Document Type</label>
                    <div class="cm-auth-fieldwrap">
                        <select name="id_type_2" required class="cm-auth-input cm-auth-select">
                            <option value="">Select type…</option>
                            @foreach ($idTypes as $opt)
                                <option value="{{ $opt }}" @selected(old('id_type_2') === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined cm-auth-chevron">expand_more</span>
                    </div>
                    @error('id_type_2')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
                </div>
                <label class="cm-drop" data-dropzone data-input="id_document_2">
                    <span class="material-symbols-outlined cm-drop-ico">cloud_upload</span>
                    <span class="cm-drop-title">Click to upload or drag and drop</span>
                    <span class="cm-drop-sub">JPG, PNG or PDF — max 5MB</span>
                    <input type="file" name="id_document_2" id="id_document_2" accept=".jpg,.jpeg,.png,.pdf" required class="sr-only">
                </label>
                @error('id_document_2')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
                <div id="id-doc-2-preview-wrap" class="hidden mt-3 flex items-center gap-3">
                    <div id="id-doc-2-preview-card" class="doc-preview-card cursor-pointer" data-doc-type="id_2" title="Click to preview">
                        <div id="id-doc-2-thumb" class="w-16 h-16 rounded-lg border border-gray-200 overflow-hidden flex items-center justify-center text-gray-300 text-xs bg-gray-50"></div>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[12px]" style="color:#6B7280">Selected: <span id="id-doc-2-filename" class="font-semibold" style="color:#1A1A1A"></span></p>
                        <div class="flex items-center gap-3 mt-1">
                            <button type="button" class="id-doc-2-preview-btn text-[12px] font-semibold" style="color:#1B3A6B">Preview</button>
                            <button type="button" class="cm-remove" data-remove-input="id_document_2" data-remove-wrap="id-doc-2-preview-wrap">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Business license (business seller only) ── --}}
        @if ($isBusinessSeller)
            <div class="cm-card">
                <p class="cm-sec-title"><span class="material-symbols-outlined">business</span>Business Details</p>

                <label class="cm-drop" data-dropzone data-input="business_license">
                    <span class="material-symbols-outlined cm-drop-ico">cloud_upload</span>
                    <span class="cm-drop-title">Upload your business license</span>
                    <span class="cm-drop-sub">JPG, PNG or PDF — max 5MB</span>
                    <input type="file" name="business_license" id="business_license" accept=".jpg,.jpeg,.png,.pdf" required class="sr-only">
                </label>
                @error('business_license')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
                <div id="license-preview-wrap" class="hidden mt-3 flex items-center gap-3">
                    <div id="license-preview-card" class="doc-preview-card cursor-pointer" data-doc-type="license" title="Click to preview">
                        <div id="license-thumb" class="w-16 h-16 rounded-lg border border-gray-200 overflow-hidden flex items-center justify-center text-gray-300 text-xs bg-gray-50"></div>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[12px]" style="color:#6B7280">Selected: <span id="license-filename" class="font-semibold" style="color:#1A1A1A"></span></p>
                        <div class="flex items-center gap-3 mt-1">
                            <button type="button" class="license-preview-btn text-[12px] font-semibold" style="color:#1B3A6B">Preview</button>
                            <button type="button" class="cm-remove" data-remove-input="business_license" data-remove-wrap="license-preview-wrap">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="cm-field-wrap mt-4">
                    <label class="cm-auth-label">Relationship to Business</label>
                    <div class="cm-auth-fieldwrap">
                        <select name="relationship_to_business" required class="cm-auth-input cm-auth-select">
                            <option value="">Select relationship…</option>
                            @foreach (['Owner','Founder','Shareholder','Employee','Authorized Representative','Manager'] as $rel)
                                <option value="{{ $rel }}" @selected(old('relationship_to_business') === $rel)>{{ $rel }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined cm-auth-chevron">expand_more</span>
                    </div>
                    @error('relationship_to_business')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
                </div>
            </div>
        @endif

        {{-- ── Payment (buyer + business seller) ── --}}
        @if ($paymentRequired)
            <div class="cm-card">
                <p class="cm-sec-title"><span class="material-symbols-outlined">credit_card</span>Payment Details</p>

                {{-- Cardholder (UX only — not persisted) --}}
                <div class="cm-field-wrap mb-4">
                    <label for="cardholder_name" class="cm-auth-label">Cardholder Name</label>
                    <div class="cm-auth-fieldwrap">
                        <span class="material-symbols-outlined cm-auth-licon">person</span>
                        <input type="text" id="cardholder_name" name="cardholder_name" value="{{ old('cardholder_name') }}"
                               placeholder="Name on card" class="cm-auth-input has-licon">
                    </div>
                </div>

                {{-- Card number --}}
                <div class="cm-field-wrap mb-4">
                    <label for="card_number" class="cm-auth-label">Card Number</label>
                    <div class="cm-auth-fieldwrap">
                        <span class="material-symbols-outlined cm-auth-licon">credit_card</span>
                        <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required
                               maxlength="19" pattern="[0-9\s]{13,19}" inputmode="numeric"
                               class="cm-auth-input has-licon {{ $errors->has('card_number') ? 'is-error' : '' }}" style="font-family:monospace;letter-spacing:.04em">
                    </div>
                    @error('card_number')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
                </div>

                {{-- Expiry + CVC --}}
                <div class="grid grid-cols-3 gap-3">
                    <div class="cm-field-wrap">
                        <label class="cm-auth-label">Month</label>
                        <div class="cm-auth-fieldwrap">
                            <select name="expiry_month" required class="cm-auth-input cm-auth-select">
                                <option value="">MM</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" @selected(old('expiry_month') == str_pad($i,2,'0',STR_PAD_LEFT))>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                            <span class="material-symbols-outlined cm-auth-chevron">expand_more</span>
                        </div>
                        @error('expiry_month')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
                    </div>
                    <div class="cm-field-wrap">
                        <label class="cm-auth-label">Year</label>
                        <div class="cm-auth-fieldwrap">
                            <select name="expiry_year" required class="cm-auth-input cm-auth-select">
                                <option value="">YYYY</option>
                                @for ($i = (int) date('Y'); $i <= (int) date('Y') + 10; $i++)
                                    <option value="{{ $i }}" @selected(old('expiry_year') == $i)>{{ $i }}</option>
                                @endfor
                            </select>
                            <span class="material-symbols-outlined cm-auth-chevron">expand_more</span>
                        </div>
                        @error('expiry_year')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
                    </div>
                    <div class="cm-field-wrap">
                        <label class="cm-auth-label">CVV</label>
                        <div class="cm-auth-fieldwrap">
                            <span class="material-symbols-outlined cm-auth-licon">lock</span>
                            <input type="text" name="cvc" placeholder="123" required maxlength="4" pattern="[0-9]{3,4}" inputmode="numeric"
                                   class="cm-auth-input has-licon {{ $errors->has('cvc') ? 'is-error' : '' }}" style="font-family:monospace">
                        </div>
                        @error('cvc')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Order summary --}}
                <div class="cm-summary mt-5">
                    <div class="cm-summary-row"><span style="color:#6B7280">Plan</span><span class="font-semibold" style="color:#1A1A1A">{{ $package->title }}</span></div>
                    <div class="cm-summary-row mt-1.5"><span style="color:#6B7280">Amount</span><span class="font-semibold" style="color:#1A1A1A">${{ number_format($package->price, 2) }}</span></div>
                    <div class="cm-summary-row mt-2 pt-2" style="border-top:1px solid #E2E5E9">
                        <span class="font-bold" style="color:#1A1A1A">Total</span>
                        <span class="font-extrabold text-lg" style="color:#1B3A6B">${{ number_format($package->price, 2) }}</span>
                    </div>
                    <p class="flex items-center gap-1.5 text-[11.5px] mt-2" style="color:#9CA3AF">
                        <span class="material-symbols-outlined" style="font-size:14px">lock</span> Secure payment
                    </p>
                </div>
            </div>
        @else
            {{-- Individual seller — no payment --}}
            <div class="flex items-start gap-3 px-4 py-4 rounded-xl" style="background:#F0FDF4;border:1px solid #bbf7d0">
                <span class="material-symbols-outlined" style="color:#16A34A">check_circle</span>
                <div>
                    <p class="text-[13.5px] font-semibold" style="color:#065f46">No payment required at this time.</p>
                    <p class="text-[12.5px] mt-0.5" style="color:#15803d">A 4% commission (min $150) applies when each listing sells.</p>
                </div>
            </div>
        @endif

        {{-- ── Terms ── --}}
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="agree_terms" value="1" required class="mt-0.5 w-5 h-5 rounded flex-shrink-0" style="accent-color:#1B3A6B">
            <span class="text-[13px]" style="color:#374151">
                By completing registration you agree to CayMark's Terms and Conditions and confirm all submitted information is accurate.
            </span>
        </label>
        @error('agree_terms')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror

        {{-- ── Actions ── --}}
        <div class="flex flex-col-reverse sm:flex-row gap-3 pt-1">
            <a href="{{ route('finish.registration') }}" class="cm-auth-btn cm-auth-btn--ghost" style="text-decoration:none">
                <span class="material-symbols-outlined" style="font-size:18px">arrow_back</span> Back
            </a>
            <button type="submit" class="cm-auth-btn">
                <span class="material-symbols-outlined" style="font-size:18px">check_circle</span>
                {{ $paymentRequired ? 'Complete Registration & Pay' : 'Complete Registration' }}
            </button>
        </div>
    </form>

</x-auth.split>

{{-- Document Preview Modal --}}
<div id="doc-preview-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4" style="display:none;">
    <div class="bg-white shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col rounded-xl" id="doc-modal-content" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100" style="background:#1B3A6B">
            <h3 class="text-sm font-bold text-white" id="doc-modal-title">Document Preview</h3>
            <button type="button" id="doc-modal-close" class="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors" aria-label="Close">
                <span class="material-symbols-outlined" style="font-size:18px">close</span>
            </button>
        </div>
        <div class="p-4 overflow-auto flex-1 flex items-center justify-center min-h-[300px] bg-gray-100">
            <img id="doc-modal-img" class="max-w-full max-h-[70vh] object-contain hidden" alt="Document preview">
            <iframe id="doc-modal-pdf" class="w-full min-h-[70vh] border-0 hidden" title="PDF preview"></iframe>
            <p id="doc-modal-placeholder" class="text-gray-400 text-sm hidden">No preview available.</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Format card number
    document.querySelector('input[name="card_number"]')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '');
        let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formatted;
    });

    // Document preview
    (function() {
        var docPreviews = {};

        function isPdf(file) { return file && (file.type === 'application/pdf' || (file.name && file.name.toLowerCase().endsWith('.pdf'))); }
        function isImage(file) { return file && /^image\/(jpeg|jpg|png|gif|webp)/.test(file.type); }

        function showPreview(inputId, wrapId, nameId, thumbId, cardId, title) {
            var input = document.getElementById(inputId);
            if (!input || !input.files || !input.files[0]) return;
            var file = input.files[0];
            var wrap = document.getElementById(wrapId);
            var nameEl = document.getElementById(nameId);
            var thumbEl = document.getElementById(thumbId);
            if (!wrap || !nameEl || !thumbEl) return;

            if (docPreviews[inputId] && docPreviews[inputId].url) URL.revokeObjectURL(docPreviews[inputId].url);
            wrap.classList.remove('hidden');
            nameEl.textContent = file.name;

            var url = URL.createObjectURL(file);
            docPreviews[inputId] = { url: url, name: file.name, isPdf: isPdf(file) };

            if (isImage(file)) {
                thumbEl.innerHTML = '';
                var img = document.createElement('img');
                img.src = url; img.className = 'w-full h-full object-contain';
                thumbEl.appendChild(img);
            } else if (isPdf(file)) {
                thumbEl.innerHTML = '<div class="flex flex-col items-center justify-center h-full p-2"><span class="material-symbols-outlined text-error text-[26px] block">picture_as_pdf</span></div>';
            } else {
                thumbEl.innerHTML = '<span class="text-xs text-gray-300">Preview</span>';
            }

            var card = document.getElementById(cardId);
            if (card) {
                card.onclick = function(e) {
                    if (e.target && e.target.tagName === 'BUTTON') return;
                    e.preventDefault();
                    openModal(inputId, title);
                };
            }
        }

        function openModal(inputId, title) {
            var data = docPreviews[inputId];
            if (!data) return;
            var modal = document.getElementById('doc-preview-modal');
            var imgEl = document.getElementById('doc-modal-img');
            var pdfEl = document.getElementById('doc-modal-pdf');
            var ph    = document.getElementById('doc-modal-placeholder');
            var ttl   = document.getElementById('doc-modal-title');

            ttl.textContent = title || 'Document Preview';
            imgEl.classList.add('hidden'); pdfEl.classList.add('hidden'); ph.classList.add('hidden');

            if (data.isPdf) { pdfEl.src = data.url; pdfEl.classList.remove('hidden'); }
            else if (data.url) { imgEl.src = data.url; imgEl.classList.remove('hidden'); }
            else { ph.classList.remove('hidden'); }

            modal.style.display = 'flex'; modal.classList.remove('hidden');
        }

        function closeModal() {
            var modal = document.getElementById('doc-preview-modal');
            var imgEl = document.getElementById('doc-modal-img');
            var pdfEl = document.getElementById('doc-modal-pdf');
            modal.style.display = 'none'; modal.classList.add('hidden');
            imgEl.src = ''; pdfEl.src = '';
        }

        document.getElementById('id_document').addEventListener('change', function() {
            showPreview('id_document', 'id-doc-preview-wrap', 'id-doc-filename', 'id-doc-thumb', 'id-doc-preview-card', 'ID Document 1');
        });
        document.getElementById('id_document_2').addEventListener('change', function() {
            showPreview('id_document_2', 'id-doc-2-preview-wrap', 'id-doc-2-filename', 'id-doc-2-thumb', 'id-doc-2-preview-card', 'ID Document 2');
        });

        @if($isBusinessSeller)
        document.getElementById('business_license').addEventListener('change', function() {
            showPreview('business_license', 'license-preview-wrap', 'license-filename', 'license-thumb', 'license-preview-card', 'Business License');
        });
        @endif

        document.getElementById('doc-modal-close').addEventListener('click', closeModal);
        document.getElementById('doc-preview-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        document.querySelectorAll('.id-doc-preview-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) { e.preventDefault(); openModal('id_document', 'ID Document 1'); });
        });
        document.querySelectorAll('.id-doc-2-preview-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) { e.preventDefault(); openModal('id_document_2', 'ID Document 2'); });
        });
        document.querySelectorAll('.license-preview-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) { e.preventDefault(); openModal('business_license', 'Business License'); });
        });
    })();

    // Dropzone state (filename + check) + drag-and-drop + remove
    (function() {
        document.querySelectorAll('[data-dropzone]').forEach(function(zone) {
            var input = document.getElementById(zone.dataset.input);
            if (!input) return;
            var titleEl = zone.querySelector('.cm-drop-title');
            var icoEl   = zone.querySelector('.cm-drop-ico');
            var defTitle = titleEl ? titleEl.textContent : '';

            function reflect() {
                if (input.files && input.files.length) {
                    zone.classList.add('is-filled');
                    if (icoEl) icoEl.textContent = 'check_circle';
                    if (titleEl) titleEl.textContent = input.files[0].name;
                } else {
                    zone.classList.remove('is-filled');
                    if (icoEl) icoEl.textContent = 'cloud_upload';
                    if (titleEl) titleEl.textContent = defTitle;
                }
            }
            input.addEventListener('change', reflect);

            ['dragenter', 'dragover'].forEach(function(ev) {
                zone.addEventListener(ev, function(e) { e.preventDefault(); zone.classList.add('is-drag'); });
            });
            ['dragleave', 'dragend'].forEach(function(ev) {
                zone.addEventListener(ev, function(e) { e.preventDefault(); zone.classList.remove('is-drag'); });
            });
            zone.addEventListener('drop', function(e) {
                e.preventDefault(); zone.classList.remove('is-drag');
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                    try {
                        var dt = new DataTransfer();
                        dt.items.add(e.dataTransfer.files[0]);
                        input.files = dt.files;
                    } catch (_) { input.files = e.dataTransfer.files; }
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
            reflect();
        });

        document.querySelectorAll('[data-remove-input]').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                var input = document.getElementById(btn.dataset.removeInput);
                if (input) { input.value = ''; input.dispatchEvent(new Event('change', { bubbles: true })); }
                var wrap = document.getElementById(btn.dataset.removeWrap);
                if (wrap) wrap.classList.add('hidden');
            });
        });
    })();
</script>
@endpush

@endsection
