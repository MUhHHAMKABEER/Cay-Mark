@extends('layouts.welcome')
@section('title', 'Complete Registration - CayMark')
@section('content')

@php
    $isBusinessSeller = $finishData['role'] === 'seller' && $package->price > 0;
    $paymentRequired = $finishData['role'] === 'buyer' || $isBusinessSeller;
@endphp

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Complete Your Registration</h1>
            <p class="text-gray-600 text-lg">Upload documents and complete payment</p>
        </div>

        <!-- Flash Messages -->
        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                <ul class="list-disc list-inside text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('finish.registration.complete') }}" enctype="multipart/form-data" id="complete-registration-form">
            @csrf

            <!-- Package Summary -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Membership Summary</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Membership Type:</span>
                        <span class="text-gray-900 font-semibold">{{ ucfirst($finishData['role']) }} - {{ $package->title }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Price:</span>
                        <span class="text-2xl font-bold text-blue-600">${{ number_format($package->price, 2) }}</span>
                    </div>
                    @if(!$paymentRequired)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                            <p class="text-green-800 text-sm"><strong>No payment required at this time.</strong> You will pay $25 per listing when you submit items.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Document Upload Section -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Document Verification</h2>

                <!-- ID Document -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Government Issued ID Document *
                    </label>
                    <input type="file" name="id_document" id="id_document" accept=".jpg,.jpeg,.png,.pdf" required
                        class="block w-full text-sm text-gray-700 file:border file:border-gray-300 file:rounded-xl file:px-4 file:py-3 file:bg-white file:text-gray-700 file:cursor-pointer hover:file:bg-gray-50 transition duration-200">
                    <p class="text-sm text-gray-500 mt-1">JPG, PNG, or PDF (max 5MB)</p>
                    @error('id_document')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                    <div id="id-doc-preview-wrap" class="mt-3 hidden">
                        <p class="text-sm font-semibold text-gray-700 mb-2">Added document: <span id="id-doc-filename" class="text-blue-600"></span></p>
                        <div id="id-doc-preview-card" class="doc-preview-card inline-block p-3 rounded-xl border-2 border-gray-200 bg-gray-50 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all max-w-[200px]" data-doc-type="id" title="Hover ya click karke full preview dekhen">
                            <div id="id-doc-thumb" class="w-32 h-32 rounded-lg bg-white border border-gray-200 overflow-hidden flex items-center justify-center text-gray-400 text-xs"></div>
                            <p id="id-doc-name" class="mt-2 text-sm font-medium text-gray-700 truncate"></p>
                            <button type="button" class="id-doc-preview-btn mt-2 w-full py-2 px-3 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-200 transition">Preview Document</button>
                            <p class="text-xs text-gray-500 mt-1">Ya is card par hover karen — modal mein preview dikhega</p>
                        </div>
                    </div>
                </div>

                <!-- ID Type -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ID Type *
                    </label>
                    <select name="id_type" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        <option value="">Select ID Type</option>
                        <option value="Passport">Passport</option>
                        <option value="Driver License">Driver's License</option>
                        <option value="National ID">National ID</option>
                    </select>
                    @error('id_type')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Business License (Business Seller only) -->
                @if($isBusinessSeller)
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Business License Document *
                        </label>
                        <input type="file" name="business_license" id="business_license" accept=".jpg,.jpeg,.png,.pdf" required
                            class="block w-full text-sm text-gray-700 file:border file:border-gray-300 file:rounded-xl file:px-4 file:py-3 file:bg-white file:text-gray-700 file:cursor-pointer hover:file:bg-gray-50 transition duration-200">
                        <p class="text-sm text-gray-500 mt-1">Must be current and not expired. JPG, PNG, or PDF (max 5MB)</p>
                        @error('business_license')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        <div id="license-preview-wrap" class="mt-3 hidden">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Added document: <span id="license-filename" class="text-blue-600"></span></p>
                            <div id="license-preview-card" class="doc-preview-card inline-block p-3 rounded-xl border-2 border-gray-200 bg-gray-50 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all max-w-[200px]" data-doc-type="license" title="Hover ya click karke full preview dekhen">
                                <div id="license-thumb" class="w-32 h-32 rounded-lg bg-white border border-gray-200 overflow-hidden flex items-center justify-center text-gray-400 text-xs"></div>
                                <p id="license-name" class="mt-2 text-sm font-medium text-gray-700 truncate"></p>
                                <button type="button" class="license-preview-btn mt-2 w-full py-2 px-3 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold hover:bg-blue-200 transition">Preview Document</button>
                                <p class="text-xs text-gray-500 mt-1">Ya is card par hover karen — modal mein preview dikhega</p>
                            </div>
                        </div>
                    </div>

                    <!-- Relationship to Business -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Relationship to Business *
                        </label>
                        <select name="relationship_to_business" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="">Select Relationship</option>
                            <option value="Owner">Owner</option>
                            <option value="Founder">Founder</option>
                            <option value="Shareholder">Shareholder</option>
                            <option value="Employee">Employee</option>
                            <option value="Authorized Representative">Authorized Representative</option>
                            <option value="Manager">Manager</option>
                        </select>
                        @error('relationship_to_business')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- Payment Section (if required) -->
            @if($paymentRequired)
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Information</h2>

                    <div class="bg-gradient-to-br from-gray-50 to-blue-50 p-6 rounded-xl border border-gray-200 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-700 font-medium">Total Amount Due:</span>
                            <span class="text-3xl font-bold text-blue-600">${{ number_format($package->price, 2) }}</span>
                        </div>
                    </div>

                    <!-- Credit Card Form -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Card Number *
                            </label>
                            <input type="text" name="card_number" placeholder="1234 5678 9012 3456" required
                                maxlength="19" pattern="[0-9\s]{13,19}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('card_number')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Expiry Month *
                                </label>
                                <select name="expiry_month" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                    <option value="">MM</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endfor
                                </select>
                                @error('expiry_month')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Expiry Year *
                                </label>
                                <select name="expiry_year" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                                    <option value="">YYYY</option>
                                    @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('expiry_year')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                CVC *
                            </label>
                            <input type="text" name="cvc" placeholder="123" required
                                maxlength="4" pattern="[0-9]{3,4}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('cvc')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- Terms Acknowledgment -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-gray-100">
                <div class="mb-6">
                    <label class="flex items-start space-x-4 p-4 rounded-xl border border-gray-200 hover:bg-gray-50 transition duration-200 cursor-pointer">
                        <input type="checkbox" name="agree_terms" value="1" required
                            class="mt-1 w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                        <span class="text-sm text-gray-700">
                            By completing registration, you agree to adhere to CayMark's Terms and Conditions and comply with all membership restrictions applicable to your selected account role.
                        </span>
                    </label>
                    @error('agree_terms')
                        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <a href="{{ route('finish.registration') }}" 
                   class="inline-block px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition duration-200 mr-4">
                    Back
                </a>
                <button type="submit"
                    class="inline-block bg-gradient-to-r from-green-500 to-green-600 text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Complete Registration
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Document Preview Modal (hover ya click par full preview) -->
<div id="doc-preview-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col" id="doc-modal-content" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-bold text-gray-900" id="doc-modal-title">Document Preview</h3>
            <button type="button" id="doc-modal-close" class="p-2 rounded-lg hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition" aria-label="Close">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4 overflow-auto flex-1 flex items-center justify-center min-h-[300px] bg-gray-100">
            <img id="doc-modal-img" class="max-w-full max-h-[70vh] object-contain rounded-lg hidden" alt="Document preview">
            <iframe id="doc-modal-pdf" class="w-full min-h-[70vh] rounded-lg border-0 hidden" title="PDF preview"></iframe>
            <p id="doc-modal-placeholder" class="text-gray-500 hidden">Preview yahan dikhega</p>
        </div>
        <p class="text-xs text-gray-500 text-center pb-3">Bahar click karke ya X dabayein to band ho jayega</p>
    </div>
</div>

@push('scripts')
<script>
    // Format card number with spaces
    document.querySelector('input[name="card_number"]')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });

    // Document preview: jo file select ki hai usko dikhana + hover/click par modal
    (function() {
        var docPreviews = {}; // id_document / business_license -> { url, name, isPdf }

        function isPdf(file) {
            return file && (file.type === 'application/pdf' || (file.name && file.name.toLowerCase().endsWith('.pdf')));
        }

        function isImage(file) {
            return file && /^image\/(jpeg|jpg|png|gif|webp)/.test(file.type);
        }

        function showPreview(inputId, wrapId, nameId, thumbId, cardId, title) {
            var input = document.getElementById(inputId);
            if (!input || !input.files || !input.files[0]) return;
            var file = input.files[0];
            var wrap = document.getElementById(wrapId);
            var nameEl = document.getElementById(nameId);
            var thumbEl = document.getElementById(thumbId);
            if (!wrap || !nameEl || !thumbEl) return;

            if (docPreviews[inputId] && docPreviews[inputId].url) {
                URL.revokeObjectURL(docPreviews[inputId].url);
            }

            wrap.classList.remove('hidden');
            nameEl.textContent = file.name;

            var url = URL.createObjectURL(file);
            docPreviews[inputId] = { url: url, name: file.name, isPdf: isPdf(file) };

            if (isImage(file)) {
                thumbEl.innerHTML = '';
                var img = document.createElement('img');
                img.src = url;
                img.className = 'w-full h-full object-contain';
                thumbEl.appendChild(img);
            } else if (isPdf(file)) {
                thumbEl.innerHTML = '<div class="flex flex-col items-center justify-center h-full p-2"><svg class="w-12 h-12 text-red-500 mb-1" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg><span>PDF</span></div>';
            } else {
                thumbEl.innerHTML = '<span>Preview</span>';
            }

            var card = document.getElementById(cardId);
            if (card) {
                card.onclick = function(e) {
                    if (e.target && e.target.classList && (e.target.classList.contains('id-doc-preview-btn') || e.target.classList.contains('license-preview-btn'))) return;
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
            var placeholder = document.getElementById('doc-modal-placeholder');
            var titleEl = document.getElementById('doc-modal-title');

            titleEl.textContent = title || 'Document Preview';
            imgEl.classList.add('hidden');
            pdfEl.classList.add('hidden');
            placeholder.classList.add('hidden');

            if (data.isPdf) {
                pdfEl.src = data.url;
                pdfEl.classList.remove('hidden');
            } else if (data.url) {
                imgEl.src = data.url;
                imgEl.classList.remove('hidden');
            } else {
                placeholder.classList.remove('hidden');
            }

            modal.style.display = 'flex';
            modal.classList.remove('hidden');
        }

        function closeModal() {
            var modal = document.getElementById('doc-preview-modal');
            var imgEl = document.getElementById('doc-modal-img');
            var pdfEl = document.getElementById('doc-modal-pdf');
            modal.style.display = 'none';
            modal.classList.add('hidden');
            imgEl.src = '';
            pdfEl.src = '';
        }

        document.getElementById('id_document').addEventListener('change', function() {
            showPreview('id_document', 'id-doc-preview-wrap', 'id-doc-filename', 'id-doc-thumb', 'id-doc-preview-card', 'ID Document Preview');
        });

        @if($isBusinessSeller)
        document.getElementById('business_license').addEventListener('change', function() {
            showPreview('business_license', 'license-preview-wrap', 'license-filename', 'license-thumb', 'license-preview-card', 'Business License Preview');
        });
        @endif

        document.getElementById('doc-modal-close').addEventListener('click', closeModal);
        document.getElementById('doc-preview-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        // "Preview Document" button se modal open
        document.querySelectorAll('.id-doc-preview-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) { e.preventDefault(); openModal('id_document', 'ID Document Preview'); });
        });
        document.querySelectorAll('.license-preview-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) { e.preventDefault(); openModal('business_license', 'Business License Preview'); });
        });

        // Hover: document card par hover karte hi modal mein proper preview dikhao (thodi delay se)
        var hoverTimer;
        document.querySelectorAll('.doc-preview-card').forEach(function(card) {
            card.addEventListener('mouseenter', function() {
                var self = this;
                hoverTimer = setTimeout(function() {
                    var docType = self.getAttribute('data-doc-type');
                    var inputId = docType === 'license' ? 'business_license' : 'id_document';
                    var title = docType === 'license' ? 'Business License Preview' : 'ID Document Preview';
                    if (docPreviews[inputId]) openModal(inputId, title);
                }, 350);
            });
            card.addEventListener('mouseleave', function() {
                clearTimeout(hoverTimer);
            });
        });
    })();
</script>
@endpush

@endsection

