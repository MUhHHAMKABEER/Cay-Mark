@extends('layouts.dashboard')
@section('title', 'Upgrade to Business Seller - CayMark')

@section('content')
@php $idTypes = ['Passport', 'NIB', "Driver's License", "Voter's Card", 'National ID']; @endphp

<div class="p-6 max-w-4xl mx-auto">

    {{-- Page header --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm flex-shrink-0"
             style="background:linear-gradient(135deg,#063466,#1e3a8a)">
            <span class="material-icons-round text-white" style="font-size:20px">workspace_premium</span>
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-900">Upgrade to Business Seller</h1>
            <p class="text-sm text-gray-500">Complete all sections below to activate your business account.</p>
        </div>
        <a href="{{ route('seller.account') }}"
           class="ml-auto inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
            <span class="material-icons-round" style="font-size:16px">arrow_back</span>
            Back
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('error'))
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-5 text-sm text-red-800">
            <span class="material-icons-round text-red-500 flex-shrink-0" style="font-size:18px">error_outline</span>
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-5">
            <p class="text-sm font-semibold text-red-800 mb-1">Please fix the following:</p>
            <ul class="space-y-0.5">
                @foreach($errors->all() as $e)
                    <li class="text-sm text-red-700 flex items-center gap-1.5">
                        <span class="w-1 h-1 bg-red-400 rounded-full flex-shrink-0"></span>{{ $e }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('upgrade.membership.submit') }}" enctype="multipart/form-data" id="upgrade-form">
        @csrf

        {{-- ─── 1. CHOOSE PLAN ─── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                <span class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:#063466">1</span>
                <h2 class="text-sm font-bold text-gray-900">Choose Your Plan</h2>
            </div>
            <div class="p-5">
                @if($packages->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-6">No business seller plans available. Contact support.</p>
                @else
                    <div class="grid grid-cols-1 {{ $packages->count() > 1 ? 'sm:grid-cols-2' : '' }} gap-3">
                        @foreach($packages as $i => $pkg)
                            @php
                                $feats = is_array($pkg->features)
                                    ? $pkg->features
                                    : (is_string($pkg->features) ? json_decode($pkg->features, true) : []);
                                $feats = is_array($feats) ? $feats : [];
                            @endphp
                            <label class="block cursor-pointer">
                                <input type="radio" name="package_id" value="{{ $pkg->id }}"
                                       class="sr-only peer" {{ $i === 0 || old('package_id') == $pkg->id ? 'checked' : '' }} required>
                                <div class="relative p-4 border-2 rounded-xl cursor-pointer transition-all duration-150
                                    border-gray-200 hover:border-blue-400
                                    peer-checked:border-blue-600 peer-checked:bg-blue-50
                                    {{ ($i === 0 && !old('package_id')) || old('package_id') == $pkg->id ? 'border-blue-600 bg-blue-50' : '' }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-gray-900 mb-0.5">{{ $pkg->title }}</p>
                                            <p class="text-xl font-extrabold" style="color:#063466">
                                                ${{ number_format($pkg->price, 2) }}
                                                <span class="text-xs font-medium text-gray-400">
                                                    /{{ $pkg->duration_days ? $pkg->duration_days . ' days' : 'year' }}
                                                </span>
                                            </p>
                                        </div>
                                        <div class="plan-check w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 mt-0.5 transition-all"
                                             style="{{ ($i === 0 && !old('package_id')) || old('package_id') == $pkg->id ? 'background:#063466;border-color:#063466' : 'border-color:#d1d5db' }}">
                                            <svg class="w-3 h-3 text-white {{ ($i === 0 && !old('package_id')) || old('package_id') == $pkg->id ? '' : 'opacity-0' }}"
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @if(!empty($feats))
                                        <ul class="mt-3 space-y-1">
                                            @foreach(array_slice($feats, 0, 4) as $feat)
                                                <li class="flex items-center gap-1.5 text-xs text-gray-600">
                                                    <svg class="w-3.5 h-3.5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    {{ $feat }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
                @error('package_id')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- ─── 2. IDENTITY DOCUMENTS ─── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                <span class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:#063466">2</span>
                <h2 class="text-sm font-bold text-gray-900">Identity Verification</h2>
                <span class="ml-1 text-xs text-gray-400">Two government-issued IDs required</span>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- ID 1 --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Government ID 1</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Document Type *</label>
                            <select name="id_type" required
                                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm">
                                <option value="">Select type</option>
                                @foreach($idTypes as $opt)
                                    <option value="{{ $opt }}" {{ old('id_type') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('id_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Upload File *</label>
                            <input type="file" name="id_document" id="id_document" accept=".jpg,.jpeg,.png,.pdf" required
                                class="block w-full text-xs text-gray-600 file:mr-3 file:border file:border-gray-200 file:rounded-lg file:px-3 file:py-2 file:bg-white file:text-gray-700 file:text-xs file:cursor-pointer hover:file:bg-gray-50">
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG or PDF — max 5 MB</p>
                            @error('id_document')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            <div id="id1-preview" class="mt-2 hidden">
                                <div class="doc-preview-card flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer hover:border-blue-400 transition-all" data-doc="id_document">
                                    <div id="id1-thumb" class="w-10 h-10 rounded bg-white border border-gray-200 overflow-hidden flex items-center justify-center flex-shrink-0"></div>
                                    <div class="min-w-0">
                                        <p id="id1-name" class="text-xs font-medium text-gray-700 truncate"></p>
                                        <button type="button" class="doc-preview-btn text-xs text-blue-600 hover:underline" data-doc="id_document">Preview</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ID 2 --}}
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Government ID 2</p>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Document Type *</label>
                            <select name="id_type_2" required
                                class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm">
                                <option value="">Select type</option>
                                @foreach($idTypes as $opt)
                                    <option value="{{ $opt }}" {{ old('id_type_2') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            @error('id_type_2')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Upload File *</label>
                            <input type="file" name="id_document_2" id="id_document_2" accept=".jpg,.jpeg,.png,.pdf" required
                                class="block w-full text-xs text-gray-600 file:mr-3 file:border file:border-gray-200 file:rounded-lg file:px-3 file:py-2 file:bg-white file:text-gray-700 file:text-xs file:cursor-pointer hover:file:bg-gray-50">
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG or PDF — max 5 MB</p>
                            @error('id_document_2')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            <div id="id2-preview" class="mt-2 hidden">
                                <div class="doc-preview-card flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer hover:border-blue-400 transition-all" data-doc="id_document_2">
                                    <div id="id2-thumb" class="w-10 h-10 rounded bg-white border border-gray-200 overflow-hidden flex items-center justify-center flex-shrink-0"></div>
                                    <div class="min-w-0">
                                        <p id="id2-name" class="text-xs font-medium text-gray-700 truncate"></p>
                                        <button type="button" class="doc-preview-btn text-xs text-blue-600 hover:underline" data-doc="id_document_2">Preview</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- ─── 3. BUSINESS DETAILS ─── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                <span class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:#063466">3</span>
                <h2 class="text-sm font-bold text-gray-900">Business Details</h2>
            </div>
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Business licence --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Business License *</label>
                    <input type="file" name="business_license" id="business_license" accept=".jpg,.jpeg,.png,.pdf" required
                        class="block w-full text-xs text-gray-600 file:mr-3 file:border file:border-gray-200 file:rounded-lg file:px-3 file:py-2 file:bg-white file:text-gray-700 file:text-xs file:cursor-pointer hover:file:bg-gray-50">
                    <p class="text-xs text-gray-400 mt-1">Current, not expired — JPG, PNG or PDF, max 5 MB</p>
                    @error('business_license')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    <div id="lic-preview" class="mt-2 hidden">
                        <div class="doc-preview-card flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-gray-50 cursor-pointer hover:border-blue-400 transition-all" data-doc="business_license">
                            <div id="lic-thumb" class="w-10 h-10 rounded bg-white border border-gray-200 overflow-hidden flex items-center justify-center flex-shrink-0"></div>
                            <div class="min-w-0">
                                <p id="lic-name" class="text-xs font-medium text-gray-700 truncate"></p>
                                <button type="button" class="doc-preview-btn text-xs text-blue-600 hover:underline" data-doc="business_license">Preview</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Relationship --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Relationship to Business *</label>
                    <select name="relationship_to_business" required
                        class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm">
                        <option value="">Select relationship</option>
                        @foreach(['Owner','Founder','Shareholder','Employee','Authorized Representative','Manager'] as $rel)
                            <option value="{{ $rel }}" {{ old('relationship_to_business') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                        @endforeach
                    </select>
                    @error('relationship_to_business')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- ─── 4. PAYMENT ─── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center gap-2">
                <span class="w-5 h-5 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:#063466">4</span>
                <h2 class="text-sm font-bold text-gray-900">Payment</h2>
                <span class="ml-auto text-xs font-semibold text-gray-500">Amount due: <span id="plan-price-display" class="font-bold text-gray-800">${{ $packages->isNotEmpty() ? number_format($packages->first()->price, 2) : '—' }}</span></span>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Card Number *</label>
                        <input type="text" name="card_number" id="upgrade_card_number"
                               placeholder="1234 5678 9012 3456" required maxlength="19"
                               class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        @error('card_number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Expiry Month *</label>
                        <select name="expiry_month" required
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm">
                            <option value="">MM</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        @error('expiry_month')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Expiry Year *</label>
                        <select name="expiry_year" required
                            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-sm">
                            <option value="">YYYY</option>
                            @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('expiry_year')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">CVC *</label>
                        <input type="text" name="cvc" placeholder="123" required maxlength="4"
                               class="w-full px-3 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        @error('cvc')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Terms + Submit ─── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="agree_terms" value="1" required
                       class="mt-0.5 w-4 h-4 rounded border-gray-300 focus:ring-blue-500" style="accent-color:#063466">
                <span class="text-sm text-gray-600 leading-relaxed">
                    By submitting, I agree to CayMark's Terms and Conditions and confirm that all uploaded documents are genuine and current.
                </span>
            </label>
            @error('agree_terms')<p class="text-xs text-red-600 mt-2 ml-7">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('seller.account') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-8 py-2.5 text-white text-sm font-bold rounded-xl shadow hover:shadow-md transition-all duration-150"
                    style="background:linear-gradient(135deg,#063466,#1e3a8a)"
                    onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                <span class="material-icons-round" style="font-size:16px">check_circle</span>
                Complete Upgrade
            </button>
        </div>

    </form>
</div>

{{-- Document preview modal --}}
<div id="doc-modal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/60 p-4" style="display:none">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-bold text-gray-900" id="doc-modal-title">Preview</h3>
            <button type="button" id="doc-modal-close" class="p-1 rounded-lg hover:bg-gray-200 text-gray-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-4 flex-1 overflow-auto flex items-center justify-center min-h-[260px] bg-gray-100">
            <img id="doc-modal-img" class="max-w-full max-h-[65vh] object-contain rounded-lg hidden" alt="">
            <iframe id="doc-modal-pdf" class="w-full min-h-[65vh] border-0 rounded-lg hidden" title="PDF"></iframe>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Card number auto-format
document.getElementById('upgrade_card_number')?.addEventListener('input', function(e) {
    var v = e.target.value.replace(/\D/g, '');
    e.target.value = v.match(/.{1,4}/g)?.join(' ') || v;
});

// Plan selection: update price display + visual state
(function() {
    var prices = {
        @foreach($packages as $pkg)
        "{{ $pkg->id }}": "{{ number_format($pkg->price, 2) }}",
        @endforeach
    };
    document.querySelectorAll('input[name="package_id"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var priceEl = document.getElementById('plan-price-display');
            if (priceEl && prices[this.value]) priceEl.textContent = '$' + prices[this.value];
            // Update check indicators
            document.querySelectorAll('.plan-check').forEach(function(dot) {
                dot.style.background = '#d1d5db';
                dot.style.borderColor = '#d1d5db';
                dot.querySelector('svg').classList.add('opacity-0');
            });
            var card = radio.closest('label').querySelector('.plan-check');
            if (card) {
                card.style.background = '#063466';
                card.style.borderColor = '#063466';
                card.querySelector('svg').classList.remove('opacity-0');
            }
        });
    });
})();

// Document preview system
(function() {
    var store = {};
    var map = {
        'id_document':   { wrap:'id1-preview', thumb:'id1-thumb', name:'id1-name', label:'ID Document 1' },
        'id_document_2': { wrap:'id2-preview', thumb:'id2-thumb', name:'id2-name', label:'ID Document 2' },
        'business_license': { wrap:'lic-preview', thumb:'lic-thumb', name:'lic-name', label:'Business License' },
    };

    function isPdf(f) { return f.type === 'application/pdf' || f.name.toLowerCase().endsWith('.pdf'); }

    Object.keys(map).forEach(function(id) {
        var el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('change', function() {
            var f = this.files[0]; if (!f) return;
            var m = map[id];
            if (store[id]?.url) URL.revokeObjectURL(store[id].url);
            var url = URL.createObjectURL(f);
            store[id] = { url, label: m.label, pdf: isPdf(f) };
            document.getElementById(m.wrap).classList.remove('hidden');
            document.getElementById(m.name).textContent = f.name;
            var th = document.getElementById(m.thumb);
            if (!isPdf(f)) {
                th.innerHTML = '';
                var img = new Image(); img.src = url; img.className = 'w-full h-full object-contain';
                th.appendChild(img);
            } else {
                th.innerHTML = '<svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>';
            }
        });
    });

    function openModal(id) {
        var d = store[id]; if (!d) return;
        document.getElementById('doc-modal-title').textContent = d.label;
        var img = document.getElementById('doc-modal-img');
        var pdf = document.getElementById('doc-modal-pdf');
        img.classList.add('hidden'); pdf.classList.add('hidden');
        if (d.pdf) { pdf.src = d.url; pdf.classList.remove('hidden'); }
        else       { img.src = d.url; img.classList.remove('hidden'); }
        var m = document.getElementById('doc-modal');
        m.style.display = 'flex'; m.classList.remove('hidden');
    }
    function closeModal() {
        var m = document.getElementById('doc-modal');
        m.style.display = 'none'; m.classList.add('hidden');
        document.getElementById('doc-modal-img').src = '';
        document.getElementById('doc-modal-pdf').src = '';
    }

    document.querySelectorAll('.doc-preview-btn, .doc-preview-card').forEach(function(el) {
        el.addEventListener('click', function(e) { e.preventDefault(); openModal(this.dataset.doc); });
    });
    document.getElementById('doc-modal-close')?.addEventListener('click', closeModal);
    document.getElementById('doc-modal')?.addEventListener('click', function(e) { if (e.target === this) closeModal(); });
})();
</script>
@endpush
@endsection
