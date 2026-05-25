@extends('layouts.dashboard')
@section('title', 'Upgrade — Documents & Payment - CayMark')

@section('content')
@php
    $idTypes = ['Passport', 'NIB', "Driver's License", "Voter's Card", 'National ID'];
@endphp

<div class="min-h-screen bg-gray-50 py-8 px-6">
    <div class="max-w-3xl mx-auto">

        <!-- Page header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-gradient-to-br from-[#063466] to-[#1e3a8a] rounded-xl flex items-center justify-center shadow">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Upgrade to Business Seller</h1>
                    <p class="text-sm text-gray-500">Upload your documents and complete payment to activate your business account.</p>
                </div>
            </div>
        </div>

        <!-- Progress indicator -->
        <div class="flex items-center gap-3 mb-8">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-green-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <span class="text-sm font-semibold text-gray-500">Select Plan</span>
            </div>
            <div class="flex-1 h-0.5 bg-[#063466] rounded-full"></div>
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-[#063466] flex items-center justify-center text-white text-xs font-bold">2</div>
                <span class="text-sm font-semibold text-[#063466]">Documents & Payment</span>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 mb-5 flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-red-800 text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 mb-5">
                <p class="font-semibold text-red-800 text-sm mb-2">Please fix the following:</p>
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="text-red-700 text-sm flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full flex-shrink-0"></span>{{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('finish.registration.complete') }}" enctype="multipart/form-data" id="upgrade-complete-form">
            @csrf

            {{-- ─── Plan Summary ─── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
                <div class="bg-gradient-to-r from-[#063466] to-[#1e3a8a] px-6 py-4 flex items-center gap-3">
                    <span class="w-2.5 h-2.5 bg-green-400 rounded-full animate-pulse"></span>
                    <span class="text-white font-semibold">Plan Summary</span>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500 font-medium">Plan</span>
                        <span class="text-sm font-semibold text-gray-900">{{ $package->title }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500 font-medium">Account Type</span>
                        <span class="inline-flex items-center gap-1.5 text-sm font-bold text-indigo-700">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Business Seller
                        </span>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-sm text-gray-500 font-medium">Total Due</span>
                        <span class="text-2xl font-extrabold text-[#063466]">${{ number_format($package->price, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- ─── Government ID 1 ─── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <span class="material-icons-round text-[#063466]" style="font-size:18px">badge</span>
                    <h2 class="font-bold text-gray-900 text-sm">Government ID — Document 1 *</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Document Type *</label>
                        <select name="id_type" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm">
                            <option value="">Select type</option>
                            @foreach($idTypes as $opt)
                                <option value="{{ $opt }}" {{ old('id_type') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('id_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Upload Document *</label>
                        <input type="file" name="id_document" id="id_document" accept=".jpg,.jpeg,.png,.pdf" required
                            class="block w-full text-sm text-gray-600 file:mr-4 file:border file:border-gray-200 file:rounded-xl file:px-4 file:py-2.5 file:bg-white file:text-gray-700 file:text-sm file:cursor-pointer hover:file:bg-gray-50 transition">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, or PDF — max 5 MB</p>
                        @error('id_document')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        <div id="id-doc-preview-wrap" class="mt-3 hidden">
                            <div id="id-doc-preview-card" class="doc-preview-card inline-flex items-center gap-3 p-3 rounded-xl border-2 border-gray-200 bg-gray-50 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all" data-doc-type="id">
                                <div id="id-doc-thumb" class="w-14 h-14 rounded-lg bg-white border border-gray-200 overflow-hidden flex items-center justify-center text-gray-400 text-xs flex-shrink-0"></div>
                                <div>
                                    <p id="id-doc-name" class="text-sm font-medium text-gray-700 truncate max-w-[180px]"></p>
                                    <button type="button" class="id-doc-preview-btn mt-1 text-xs text-blue-600 font-semibold hover:underline">Preview</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── Government ID 2 ─── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                    <span class="material-icons-round text-[#063466]" style="font-size:18px">badge</span>
                    <h2 class="font-bold text-gray-900 text-sm">Government ID — Document 2 *</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Document Type *</label>
                        <select name="id_type_2" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm">
                            <option value="">Select type</option>
                            @foreach($idTypes as $opt)
                                <option value="{{ $opt }}" {{ old('id_type_2') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('id_type_2')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Upload Document *</label>
                        <input type="file" name="id_document_2" id="id_document_2" accept=".jpg,.jpeg,.png,.pdf" required
                            class="block w-full text-sm text-gray-600 file:mr-4 file:border file:border-gray-200 file:rounded-xl file:px-4 file:py-2.5 file:bg-white file:text-gray-700 file:text-sm file:cursor-pointer hover:file:bg-gray-50 transition">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, or PDF — max 5 MB</p>
                        @error('id_document_2')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        <div id="id-doc-2-preview-wrap" class="mt-3 hidden">
                            <div id="id-doc-2-preview-card" class="doc-preview-card inline-flex items-center gap-3 p-3 rounded-xl border-2 border-gray-200 bg-gray-50 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all" data-doc-type="id_2">
                                <div id="id-doc-2-thumb" class="w-14 h-14 rounded-lg bg-white border border-gray-200 overflow-hidden flex items-center justify-center text-gray-400 text-xs flex-shrink-0"></div>
                                <div>
                                    <p id="id-doc-2-name" class="text-sm font-medium text-gray-700 truncate max-w-[180px]"></p>
                                    <button type="button" class="id-doc-2-preview-btn mt-1 text-xs text-blue-600 font-semibold hover:underline">Preview</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── Business License (business-seller specific) ─── --}}
            <div class="bg-white rounded-2xl border border-indigo-100 shadow-sm overflow-hidden mb-5">
                <div class="px-6 py-4 border-b border-indigo-100 bg-indigo-50/40 flex items-center gap-2">
                    <span class="material-icons-round text-indigo-600" style="font-size:18px">business</span>
                    <h2 class="font-bold text-gray-900 text-sm">Business License *</h2>
                    <span class="ml-auto text-xs font-semibold text-indigo-600 bg-indigo-100 px-2.5 py-0.5 rounded-full">Business Seller Required</span>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Upload Business License *</label>
                        <input type="file" name="business_license" id="business_license" accept=".jpg,.jpeg,.png,.pdf" required
                            class="block w-full text-sm text-gray-600 file:mr-4 file:border file:border-gray-200 file:rounded-xl file:px-4 file:py-2.5 file:bg-white file:text-gray-700 file:text-sm file:cursor-pointer hover:file:bg-gray-50 transition">
                        <p class="text-xs text-gray-400 mt-1">Must be current and not expired — JPG, PNG, or PDF, max 5 MB</p>
                        @error('business_license')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        <div id="license-preview-wrap" class="mt-3 hidden">
                            <div id="license-preview-card" class="doc-preview-card inline-flex items-center gap-3 p-3 rounded-xl border-2 border-gray-200 bg-gray-50 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all" data-doc-type="license">
                                <div id="license-thumb" class="w-14 h-14 rounded-lg bg-white border border-gray-200 overflow-hidden flex items-center justify-center text-gray-400 text-xs flex-shrink-0"></div>
                                <div>
                                    <p id="license-name" class="text-sm font-medium text-gray-700 truncate max-w-[180px]"></p>
                                    <button type="button" class="license-preview-btn mt-1 text-xs text-blue-600 font-semibold hover:underline">Preview</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Relationship to Business (business-seller specific) --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Your Relationship to Business *</label>
                        <select name="relationship_to_business" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm">
                            <option value="">Select relationship</option>
                            <option value="Owner" {{ old('relationship_to_business') === 'Owner' ? 'selected' : '' }}>Owner</option>
                            <option value="Founder" {{ old('relationship_to_business') === 'Founder' ? 'selected' : '' }}>Founder</option>
                            <option value="Shareholder" {{ old('relationship_to_business') === 'Shareholder' ? 'selected' : '' }}>Shareholder</option>
                            <option value="Employee" {{ old('relationship_to_business') === 'Employee' ? 'selected' : '' }}>Employee</option>
                            <option value="Authorized Representative" {{ old('relationship_to_business') === 'Authorized Representative' ? 'selected' : '' }}>Authorized Representative</option>
                            <option value="Manager" {{ old('relationship_to_business') === 'Manager' ? 'selected' : '' }}>Manager</option>
                        </select>
                        @error('relationship_to_business')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- ─── Payment (business-seller specific) ─── --}}
            <div class="bg-white rounded-2xl border border-indigo-100 shadow-sm overflow-hidden mb-5">
                <div class="px-6 py-4 border-b border-indigo-100 bg-indigo-50/40 flex items-center gap-2">
                    <span class="material-icons-round text-indigo-600" style="font-size:18px">credit_card</span>
                    <h2 class="font-bold text-gray-900 text-sm">Payment Information *</h2>
                    <span class="ml-auto text-xs font-semibold text-indigo-600 bg-indigo-100 px-2.5 py-0.5 rounded-full">Business Seller Required</span>
                </div>
                <div class="p-6">
                    <!-- Amount pill -->
                    <div class="flex items-center justify-between bg-gradient-to-r from-[#063466]/5 to-indigo-50 border border-indigo-100 rounded-xl px-5 py-4 mb-5">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Due Today</p>
                            <p class="text-xs text-gray-400 mt-0.5">Annual Business Seller membership</p>
                        </div>
                        <span class="text-3xl font-extrabold text-[#063466]">${{ number_format($package->price, 2) }}</span>
                    </div>

                    <div class="space-y-4">
                        <!-- Card Number -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Card Number *</label>
                            <input type="text" name="card_number" id="upgrade_card_number"
                                   placeholder="1234 5678 9012 3456" required maxlength="19"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            @error('card_number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Expiry -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Expiry Month *</label>
                                <select name="expiry_month" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm">
                                    <option value="">MM</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                    @endfor
                                </select>
                                @error('expiry_month')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Expiry Year *</label>
                                <select name="expiry_year" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm">
                                    <option value="">YYYY</option>
                                    @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('expiry_year')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- CVC -->
                        <div class="max-w-xs">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">CVC *</label>
                            <input type="text" name="cvc" placeholder="123" required maxlength="4"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                            @error('cvc')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── Terms ─── --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="agree_terms" value="1" required
                           class="mt-0.5 w-4 h-4 rounded text-[#063466] border-gray-300 focus:ring-[#063466]">
                    <span class="text-sm text-gray-600 leading-relaxed">
                        By upgrading, I agree to CayMark's Terms and Conditions and confirm that the documents uploaded are genuine and current.
                    </span>
                </label>
                @error('agree_terms')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('upgrade.membership') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-2.5 bg-gradient-to-r from-[#063466] to-[#1e3a8a] text-white text-sm font-bold rounded-xl shadow hover:shadow-lg hover:scale-105 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Complete Upgrade
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Document Preview Modal -->
<div id="doc-preview-modal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/60 p-4" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-base font-bold text-gray-900" id="doc-modal-title">Document Preview</h3>
            <button type="button" id="doc-modal-close" class="p-1.5 rounded-lg hover:bg-gray-200 text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4 overflow-auto flex-1 flex items-center justify-center min-h-[280px] bg-gray-100">
            <img id="doc-modal-img" class="max-w-full max-h-[65vh] object-contain rounded-lg hidden" alt="Document preview">
            <iframe id="doc-modal-pdf" class="w-full min-h-[65vh] rounded-lg border-0 hidden" title="PDF preview"></iframe>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Card number formatting
    document.getElementById('upgrade_card_number')?.addEventListener('input', function(e) {
        var v = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
        e.target.value = v.match(/.{1,4}/g)?.join(' ') || v;
    });

    // Document preview system
    (function() {
        var docPreviews = {};

        function isPdf(file) { return file && (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')); }
        function isImage(file) { return file && /^image\/(jpeg|jpg|png|gif|webp)/.test(file.type); }

        function showPreview(inputId, wrapId, thumbId, nameId, cardId, title) {
            var input = document.getElementById(inputId);
            if (!input || !input.files || !input.files[0]) return;
            var file = input.files[0];
            var wrap = document.getElementById(wrapId);
            var thumbEl = document.getElementById(thumbId);
            var nameEl = document.getElementById(nameId);
            if (!wrap || !thumbEl || !nameEl) return;

            if (docPreviews[inputId]?.url) URL.revokeObjectURL(docPreviews[inputId].url);
            var url = URL.createObjectURL(file);
            docPreviews[inputId] = { url, name: file.name, isPdf: isPdf(file), title };

            nameEl.textContent = file.name;
            wrap.classList.remove('hidden');

            if (isImage(file)) {
                thumbEl.innerHTML = '';
                var img = document.createElement('img');
                img.src = url;
                img.className = 'w-full h-full object-contain';
                thumbEl.appendChild(img);
            } else if (isPdf(file)) {
                thumbEl.innerHTML = '<svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>';
            }
        }

        function openModal(inputId) {
            var data = docPreviews[inputId];
            if (!data) return;
            var modal = document.getElementById('doc-preview-modal');
            var imgEl = document.getElementById('doc-modal-img');
            var pdfEl = document.getElementById('doc-modal-pdf');
            document.getElementById('doc-modal-title').textContent = data.title || 'Document Preview';
            imgEl.classList.add('hidden'); pdfEl.classList.add('hidden');
            if (data.isPdf) { pdfEl.src = data.url; pdfEl.classList.remove('hidden'); }
            else { imgEl.src = data.url; imgEl.classList.remove('hidden'); }
            modal.style.display = 'flex'; modal.classList.remove('hidden');
        }

        function closeModal() {
            var modal = document.getElementById('doc-preview-modal');
            modal.style.display = 'none'; modal.classList.add('hidden');
            document.getElementById('doc-modal-img').src = '';
            document.getElementById('doc-modal-pdf').src = '';
        }

        // Wire file inputs
        document.getElementById('id_document')?.addEventListener('change', function() {
            showPreview('id_document', 'id-doc-preview-wrap', 'id-doc-thumb', 'id-doc-name', 'id-doc-preview-card', 'ID Document 1');
        });
        document.getElementById('id_document_2')?.addEventListener('change', function() {
            showPreview('id_document_2', 'id-doc-2-preview-wrap', 'id-doc-2-thumb', 'id-doc-2-name', 'id-doc-2-preview-card', 'ID Document 2');
        });
        document.getElementById('business_license')?.addEventListener('change', function() {
            showPreview('business_license', 'license-preview-wrap', 'license-thumb', 'license-name', 'license-preview-card', 'Business License');
        });

        // Preview buttons
        document.querySelectorAll('.id-doc-preview-btn').forEach(function(b) { b.addEventListener('click', function(e) { e.preventDefault(); openModal('id_document'); }); });
        document.querySelectorAll('.id-doc-2-preview-btn').forEach(function(b) { b.addEventListener('click', function(e) { e.preventDefault(); openModal('id_document_2'); }); });
        document.querySelectorAll('.license-preview-btn').forEach(function(b) { b.addEventListener('click', function(e) { e.preventDefault(); openModal('business_license'); }); });

        // Card clicks
        document.querySelectorAll('.doc-preview-card').forEach(function(card) {
            card.addEventListener('click', function() {
                var t = this.getAttribute('data-doc-type');
                var id = t === 'license' ? 'business_license' : (t === 'id_2' ? 'id_document_2' : 'id_document');
                openModal(id);
            });
        });

        document.getElementById('doc-modal-close')?.addEventListener('click', closeModal);
        document.getElementById('doc-preview-modal')?.addEventListener('click', function(e) { if (e.target === this) closeModal(); });
    })();
</script>
@endpush
@endsection
