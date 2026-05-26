@extends('layouts.welcome')
@section('title', 'Complete Registration — CayMark Island Exchange')
@section('content')

@php
    $isBusinessSeller = $finishData['role'] === 'seller' && $package->price > 0;
    $paymentRequired  = $finishData['role'] === 'buyer' || $isBusinessSeller;
    $idTypes          = ['Passport', 'NIB', 'Driver\'s License', 'Voter\'s Card', 'National ID'];
@endphp

<div class="bg-[#f8fafd] py-12 px-4">
    <div class="max-w-3xl mx-auto">

        {{-- ── Step progress ─────────────────────────────────────── --}}
        <div class="mb-10">
            <div class="flex items-center gap-0 max-w-lg mx-auto">
                @php
                $stepsC = [
                    ['n'=>'1','label'=>'Account','done'=>true,'active'=>false],
                    ['n'=>'2','label'=>'Role & Plan','done'=>true,'active'=>false],
                    ['n'=>'3','label'=>'Verify & Complete','done'=>false,'active'=>true],
                ];
                @endphp
                @foreach($stepsC as $i => $st)
                <div class="flex items-center {{ $i < count($stepsC)-1 ? 'flex-1' : '' }}">
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-8 h-8 flex items-center justify-center text-xs font-bold
                                    {{ $st['done'] ? 'bg-secondary-fixed-dim text-primary' : ($st['active'] ? 'bg-primary text-white' : 'bg-gray-200 text-gray-400') }}"
                             style="border-radius:0">
                            @if($st['done'])
                                <span class="material-symbols-outlined text-[14px]">check</span>
                            @else
                                {{ $st['n'] }}
                            @endif
                        </div>
                        <span class="text-xs font-bold uppercase tracking-widest hidden sm:block
                                     {{ $st['active'] ? 'text-primary' : ($st['done'] ? 'text-secondary-fixed-dim' : 'text-gray-400') }}">
                            {{ $st['label'] }}
                        </span>
                    </div>
                    @if($i < count($stepsC)-1)
                        <div class="flex-1 h-px mx-3 {{ $st['done'] ? 'bg-secondary-fixed-dim' : 'bg-gray-200' }}"></div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="text-center mb-10">
            <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-2">Step 3 of 3</p>
            <h1 class="text-3xl font-bold text-primary uppercase tracking-tight font-headline-lg">Verify &amp; Complete</h1>
            <p class="text-gray-400 text-sm mt-2">Upload your ID documents{{ $paymentRequired ? ' and complete payment' : '' }} to finish registration.</p>
        </div>

        {{-- Flash messages --}}
        @if (session('error'))
            <div class="border-l-4 border-error bg-red-50 px-5 py-4 flex items-start gap-3 text-sm text-red-800 mb-6" style="border-radius:0">
                <span class="material-symbols-outlined text-error text-[18px] flex-shrink-0 mt-0.5">error</span>
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="border-l-4 border-amber-400 bg-amber-50 px-5 py-4 text-sm text-amber-800 mb-6" style="border-radius:0">
                <p class="font-bold mb-1.5">Please fix the following:</p>
                <ul class="space-y-0.5 list-disc list-inside text-amber-700">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('finish.registration.complete') }}" enctype="multipart/form-data" id="complete-registration-form">
            @csrf

            {{-- ── Membership Summary ──────────────────────────────── --}}
            <div class="bg-white border-t-4 border-primary shadow-md mb-6" style="border-radius:0">
                <div class="px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">workspace_premium</span>
                    <h2 class="text-sm font-bold text-primary uppercase tracking-widest">Membership Summary</h2>
                </div>
                <div class="px-8 py-6 space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Membership Type</span>
                        <span class="text-sm font-bold text-primary">{{ ucfirst($finishData['role']) }} — {{ $package->title }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Price</span>
                        <span class="text-xl font-bold {{ $package->price > 0 ? 'text-primary' : 'text-green-600' }}">
                            {{ $package->price > 0 ? '$'.number_format($package->price, 2) : 'Free' }}
                        </span>
                    </div>
                    @if(!$paymentRequired)
                        <div class="border-l-4 border-green-400 bg-green-50 px-4 py-3 mt-2" style="border-radius:0">
                            <p class="text-xs text-green-700 leading-relaxed">
                                <strong>No payment required at registration.</strong>
                                A 4% commission (min $150) applies when each listing sells.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Document Upload ─────────────────────────────────── --}}
            <div class="bg-white border-t-4 border-secondary-fixed-dim shadow-md mb-6" style="border-radius:0">
                <div class="px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">badge</span>
                    <h2 class="text-sm font-bold text-primary uppercase tracking-widest">Identity Verification</h2>
                </div>
                <div class="px-8 py-6">
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">
                        Upload two government-issued ID documents. Accepted: Passport, NIB, Driver's License, Voter's Card, or National ID.
                    </p>

                    {{-- ID Doc 1 --}}
                    <div class="border-2 border-gray-200 p-6 mb-5 bg-[#f8fafd]" style="border-radius:0">
                        <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-5 h-5 bg-primary text-white flex items-center justify-center text-[10px] flex-shrink-0" style="border-radius:0">1</span>
                            Government ID — Document 1
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Document Type <span class="text-error">*</span></label>
                                <select name="id_type" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 focus:border-primary focus:outline-none text-sm text-gray-900 bg-white"
                                    style="border-radius:0">
                                    <option value="">Select type…</option>
                                    @foreach($idTypes as $opt)
                                        <option value="{{ $opt }}" {{ old('id_type') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                                @error('id_type') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Upload File <span class="text-error">*</span></label>
                                <input type="file" name="id_document" id="id_document" accept=".jpg,.jpeg,.png,.pdf" required
                                    class="block w-full text-sm text-gray-600 file:mr-3 file:px-4 file:py-2.5 file:border-2 file:border-primary file:bg-primary file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest file:cursor-pointer hover:file:bg-[#003377] file:transition-colors"
                                    style="border-radius:0"/>
                                <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, or PDF · max 5MB</p>
                                @error('id_document') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div id="id-doc-preview-wrap" class="hidden">
                            <p class="text-xs font-bold text-gray-500 mb-2">Selected: <span id="id-doc-filename" class="text-primary"></span></p>
                            <div id="id-doc-preview-card" class="doc-preview-card inline-block p-3 border-2 border-gray-200 bg-white cursor-pointer hover:border-primary transition-colors max-w-[180px]" data-doc-type="id" title="Click to preview" style="border-radius:0">
                                <div id="id-doc-thumb" class="w-28 h-28 border border-gray-100 overflow-hidden flex items-center justify-center text-gray-300 text-xs bg-gray-50" style="border-radius:0"></div>
                                <button type="button" class="id-doc-preview-btn mt-2 w-full py-1.5 px-3 bg-primary text-white text-[10px] font-bold uppercase tracking-widest hover:bg-[#003377] transition-colors" style="border-radius:0">Preview</button>
                            </div>
                        </div>
                    </div>

                    {{-- ID Doc 2 --}}
                    <div class="border-2 border-gray-200 p-6 mb-5 bg-[#f8fafd]" style="border-radius:0">
                        <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-5 h-5 bg-primary text-white flex items-center justify-center text-[10px] flex-shrink-0" style="border-radius:0">2</span>
                            Government ID — Document 2
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Document Type <span class="text-error">*</span></label>
                                <select name="id_type_2" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 focus:border-primary focus:outline-none text-sm text-gray-900 bg-white"
                                    style="border-radius:0">
                                    <option value="">Select type…</option>
                                    @foreach($idTypes as $opt)
                                        <option value="{{ $opt }}" {{ old('id_type_2') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                                @error('id_type_2') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Upload File <span class="text-error">*</span></label>
                                <input type="file" name="id_document_2" id="id_document_2" accept=".jpg,.jpeg,.png,.pdf" required
                                    class="block w-full text-sm text-gray-600 file:mr-3 file:px-4 file:py-2.5 file:border-2 file:border-primary file:bg-primary file:text-white file:text-xs file:font-bold file:uppercase file:tracking-widest file:cursor-pointer hover:file:bg-[#003377] file:transition-colors"
                                    style="border-radius:0"/>
                                <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, or PDF · max 5MB</p>
                                @error('id_document_2') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div id="id-doc-2-preview-wrap" class="hidden">
                            <p class="text-xs font-bold text-gray-500 mb-2">Selected: <span id="id-doc-2-filename" class="text-primary"></span></p>
                            <div id="id-doc-2-preview-card" class="doc-preview-card inline-block p-3 border-2 border-gray-200 bg-white cursor-pointer hover:border-primary transition-colors max-w-[180px]" data-doc-type="id_2" title="Click to preview" style="border-radius:0">
                                <div id="id-doc-2-thumb" class="w-28 h-28 border border-gray-100 overflow-hidden flex items-center justify-center text-gray-300 text-xs bg-gray-50" style="border-radius:0"></div>
                                <button type="button" class="id-doc-2-preview-btn mt-2 w-full py-1.5 px-3 bg-primary text-white text-[10px] font-bold uppercase tracking-widest hover:bg-[#003377] transition-colors" style="border-radius:0">Preview</button>
                            </div>
                        </div>
                    </div>

                    {{-- Business License (Business Seller only) --}}
                    @if($isBusinessSeller)
                        <div class="border-2 border-secondary-fixed-dim p-6 mb-5 bg-[#fdf8ee]" style="border-radius:0">
                            <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-secondary-fixed-dim text-[16px]">business</span>
                                Business License Document <span class="text-error ml-1">*</span>
                            </h3>
                            <input type="file" name="business_license" id="business_license" accept=".jpg,.jpeg,.png,.pdf" required
                                class="block w-full text-sm text-gray-600 file:mr-3 file:px-4 file:py-2.5 file:border-2 file:border-secondary-fixed-dim file:bg-secondary-fixed-dim file:text-primary file:text-xs file:font-bold file:uppercase file:tracking-widest file:cursor-pointer hover:file:opacity-90 file:transition-opacity mb-2"
                                style="border-radius:0"/>
                            <p class="text-[10px] text-gray-500">Must be current and not expired. JPG, PNG, or PDF · max 5MB</p>
                            @error('business_license') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror

                            <div id="license-preview-wrap" class="mt-3 hidden">
                                <p class="text-xs font-bold text-gray-500 mb-2">Selected: <span id="license-filename" class="text-primary"></span></p>
                                <div id="license-preview-card" class="doc-preview-card inline-block p-3 border-2 border-gray-200 bg-white cursor-pointer hover:border-primary transition-colors max-w-[180px]" data-doc-type="license" title="Click to preview" style="border-radius:0">
                                    <div id="license-thumb" class="w-28 h-28 border border-gray-100 overflow-hidden flex items-center justify-center text-gray-300 text-xs bg-gray-50" style="border-radius:0"></div>
                                    <button type="button" class="license-preview-btn mt-2 w-full py-1.5 px-3 bg-primary text-white text-[10px] font-bold uppercase tracking-widest hover:bg-[#003377] transition-colors" style="border-radius:0">Preview</button>
                                </div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">
                                Relationship to Business <span class="text-error">*</span>
                            </label>
                            <select name="relationship_to_business" required
                                class="w-full px-4 py-3 border-2 border-gray-200 focus:border-primary focus:outline-none text-sm text-gray-900 bg-white"
                                style="border-radius:0">
                                <option value="">Select relationship…</option>
                                @foreach(['Owner','Founder','Shareholder','Employee','Authorized Representative','Manager'] as $rel)
                                    <option value="{{ $rel }}" {{ old('relationship_to_business') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                @endforeach
                            </select>
                            @error('relationship_to_business') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Payment ─────────────────────────────────────────── --}}
            @if($paymentRequired)
                <div class="bg-white border-t-4 border-primary shadow-md mb-6" style="border-radius:0">
                    <div class="px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">credit_card</span>
                        <h2 class="text-sm font-bold text-primary uppercase tracking-widest">Payment Information</h2>
                    </div>
                    <div class="px-8 py-6">

                        {{-- Amount due --}}
                        <div class="border-2 border-gray-100 bg-[#f8fafd] p-5 mb-6 flex items-center justify-between" style="border-radius:0">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Due Now</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $package->title }}</p>
                            </div>
                            <span class="text-3xl font-bold text-primary">${{ number_format($package->price, 2) }}</span>
                        </div>

                        <div class="space-y-5">
                            {{-- Card number --}}
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Card Number <span class="text-error">*</span></label>
                                <div class="flex items-center border-2 border-gray-200 focus-within:border-primary transition-colors" style="border-radius:0">
                                    <span class="material-symbols-outlined text-gray-300 text-[20px] flex-shrink-0 ml-3">credit_card</span>
                                    <input type="text" name="card_number" placeholder="1234 5678 9012 3456" required
                                        maxlength="19" pattern="[0-9\s]{13,19}"
                                        class="flex-1 px-3 py-3.5 bg-transparent text-sm text-gray-900 placeholder-gray-300 focus:outline-none font-mono tracking-wider"
                                        style="border-radius:0"/>
                                </div>
                                @error('card_number') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Expiry + CVC --}}
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Month <span class="text-error">*</span></label>
                                    <select name="expiry_month" required
                                        class="w-full px-3 py-3 border-2 border-gray-200 focus:border-primary focus:outline-none text-sm text-gray-900 bg-white"
                                        style="border-radius:0">
                                        <option value="">MM</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                        @endfor
                                    </select>
                                    @error('expiry_month') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Year <span class="text-error">*</span></label>
                                    <select name="expiry_year" required
                                        class="w-full px-3 py-3 border-2 border-gray-200 focus:border-primary focus:outline-none text-sm text-gray-900 bg-white"
                                        style="border-radius:0">
                                        <option value="">YYYY</option>
                                        @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('expiry_year') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">CVC <span class="text-error">*</span></label>
                                    <div class="flex items-center border-2 border-gray-200 focus-within:border-primary transition-colors" style="border-radius:0">
                                        <span class="material-symbols-outlined text-gray-300 text-[18px] flex-shrink-0 ml-2">lock</span>
                                        <input type="text" name="cvc" placeholder="123" required maxlength="4" pattern="[0-9]{3,4}"
                                            class="flex-1 px-2 py-3 bg-transparent text-sm text-gray-900 placeholder-gray-300 focus:outline-none font-mono"
                                            style="border-radius:0"/>
                                    </div>
                                    @error('cvc') <p class="text-xs text-error mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Terms & Submit ──────────────────────────────────── --}}
            <div class="bg-white border-2 border-gray-200 p-6 mb-8" style="border-radius:0">
                <label class="flex items-start gap-4 cursor-pointer">
                    <input type="checkbox" name="agree_terms" value="1" required
                           class="mt-0.5 w-5 h-5 text-primary border-gray-300 focus:ring-primary/20 flex-shrink-0"
                           style="border-radius:0"/>
                    <span class="text-sm text-gray-600 leading-relaxed">
                        By completing registration, you agree to adhere to CayMark's Terms and Conditions and comply with all membership restrictions applicable to your selected account role.
                    </span>
                </label>
                @error('agree_terms') <p class="text-xs text-error mt-3">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('finish.registration') }}"
                   class="w-full sm:w-auto px-8 py-4 border-2 border-gray-200 text-gray-500 font-bold uppercase tracking-widest text-sm hover:border-gray-400 hover:text-gray-700 transition-colors flex items-center justify-center gap-2"
                   style="border-radius:0">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Back
                </a>
                <button type="submit"
                        class="w-full sm:w-auto px-10 py-4 bg-secondary-fixed-dim text-primary font-bold uppercase tracking-widest text-sm hover:bg-[#b8943b] transition-colors flex items-center justify-center gap-2"
                        style="border-radius:0">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Complete Registration
                </button>
            </div>

        </form>
    </div>
</div>

{{-- Document Preview Modal --}}
<div id="doc-preview-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4" style="display:none;">
    <div class="bg-white shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col" style="border-radius:0" id="doc-modal-content" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-primary">
            <h3 class="text-sm font-bold text-white uppercase tracking-widest" id="doc-modal-title">Document Preview</h3>
            <button type="button" id="doc-modal-close"
                    class="w-8 h-8 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white transition-colors focus:outline-none"
                    style="border-radius:0" aria-label="Close">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <div class="p-4 overflow-auto flex-1 flex items-center justify-center min-h-[300px] bg-gray-100">
            <img id="doc-modal-img" class="max-w-full max-h-[70vh] object-contain hidden" alt="Document preview" style="border-radius:0">
            <iframe id="doc-modal-pdf" class="w-full min-h-[70vh] border-0 hidden" title="PDF preview" style="border-radius:0"></iframe>
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
                thumbEl.innerHTML = '<div class="flex flex-col items-center justify-center h-full p-2"><span class="material-symbols-outlined text-error text-[36px] block mb-1">picture_as_pdf</span><span class="text-[10px] text-gray-400">PDF</span></div>';
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

        var hoverTimer;
        document.querySelectorAll('.doc-preview-card').forEach(function(card) {
            card.addEventListener('mouseenter', function() {
                var self = this;
                hoverTimer = setTimeout(function() {
                    var docType = self.getAttribute('data-doc-type');
                    var inputId = docType === 'license' ? 'business_license' : (docType === 'id_2' ? 'id_document_2' : 'id_document');
                    var title   = docType === 'license' ? 'Business License' : (docType === 'id_2' ? 'ID Document 2' : 'ID Document 1');
                    if (docPreviews[inputId]) openModal(inputId, title);
                }, 350);
            });
            card.addEventListener('mouseleave', function() { clearTimeout(hoverTimer); });
        });
    })();
</script>
@endpush

@endsection
