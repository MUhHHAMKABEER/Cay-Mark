@extends('layouts.dashboard')
@section('title', 'Upgrade to Business Seller - CayMark')

@section('content')
@php
    $idTypes = ['Passport', 'NIB', "Driver's License", "Voter's Card", 'National ID'];
    $relationships = ['Owner','Founder','Shareholder','Employee','Authorized Representative','Manager'];
@endphp

<style>
.upgrade-upload-zone {
    border: 2px dashed #e5e7eb;
    border-radius: 14px;
    padding: 1.25rem;
    text-align: center;
    transition: border-color 0.2s, background 0.2s;
    cursor: pointer;
    background: #fafafa;
}
.upgrade-upload-zone:hover,
.upgrade-upload-zone.drag-over {
    border-color: #2563eb;
    background: #eff6ff;
}
.upgrade-upload-zone.has-file {
    border-style: solid;
    border-color: #bfdbfe;
    background: #f0f7ff;
}
.plan-card-radio:checked ~ .plan-card-inner {
    border-color: #063466;
    background: #eff6ff;
    box-shadow: 0 0 0 3px rgba(6,52,102,0.10);
}
.plan-card-inner {
    transition: border-color 0.15s, background 0.15s, box-shadow 0.15s;
}
.step-badge {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    color: #fff;
    flex-shrink: 0;
    background: linear-gradient(135deg, #063466, #1e3a8a);
}
.upgrade-input {
    width: 100%;
    padding: 0.65rem 0.875rem;
    border-radius: 12px;
    border: 2px solid #e5e7eb;
    background: #fff;
    font-size: 0.875rem;
    color: #111827;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    font-family: 'Poppins', sans-serif;
}
.upgrade-input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
}
.upgrade-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.35rem;
    letter-spacing: 0.01em;
}
.upgrade-section-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 0.625rem;
    background: #fff;
}
.upgrade-section-body {
    padding: 1.5rem;
}
</style>

<div class="w-full px-3 sm:px-5 lg:px-7 py-5 max-w-5xl mx-auto">

    {{-- ── Page Header ── --}}
    <div class="flex items-center gap-4 mb-7">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0"
             style="background:linear-gradient(135deg,#063466,#1e3a8a);box-shadow:0 8px 20px rgba(6,52,102,0.28)">
            <span class="material-icons-round text-white" style="font-size:22px">workspace_premium</span>
        </div>
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-bold text-gray-900 tracking-tight leading-tight">Upgrade to Business Seller</h1>
            <p class="text-sm text-gray-500 mt-0.5">Complete all four sections below to activate your business account.</p>
        </div>
        <a href="{{ route('seller.account') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-150 shadow-sm flex-shrink-0">
            <span class="material-icons-round" style="font-size:16px">arrow_back</span>
            <span class="hidden sm:inline">Back</span>
        </a>
    </div>

    {{-- ── Step Progress ── --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 mb-6">
        <div class="flex items-center justify-between relative">
            <div class="absolute left-0 right-0 top-1/2 -translate-y-1/2 h-0.5 bg-gray-100 mx-8 hidden sm:block"></div>
            @foreach([
                ['icon' => 'workspace_premium', 'label' => 'Choose Plan',     'num' => '1'],
                ['icon' => 'badge',             'label' => 'Identity',         'num' => '2'],
                ['icon' => 'business_center',   'label' => 'Business Details', 'num' => '3'],
                ['icon' => 'credit_card',       'label' => 'Payment',          'num' => '4'],
            ] as $step)
            <div class="relative flex flex-col items-center gap-1 flex-1">
                <div class="w-9 h-9 rounded-full flex items-center justify-center z-10"
                     style="background:linear-gradient(135deg,#063466,#1e3a8a);box-shadow:0 4px 10px rgba(6,52,102,0.22)">
                    <span class="material-icons-round text-white" style="font-size:16px">{{ $step['icon'] }}</span>
                </div>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide hidden sm:block">{{ $step['label'] }}</span>
                <span class="sm:hidden w-5 h-5 rounded-full flex items-center justify-center text-white text-[10px] font-bold" style="background:#063466">{{ $step['num'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── Alerts ── --}}
    @if(session('error'))
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-5">
            <span class="material-icons-round text-red-500 flex-shrink-0" style="font-size:18px">error_outline</span>
            <span class="text-sm text-red-800 font-medium">{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3.5 mb-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-icons-round text-red-500 flex-shrink-0" style="font-size:18px">error_outline</span>
                <p class="text-sm font-bold text-red-800">Please fix the following:</p>
            </div>
            <ul class="space-y-1 ml-7">
                @foreach($errors->all() as $e)
                    <li class="text-sm text-red-700 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-red-400 rounded-full flex-shrink-0"></span>{{ $e }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('upgrade.membership.submit') }}" enctype="multipart/form-data" id="upgrade-form">
        @csrf

        {{-- ═══════════════════════════════
             SECTION 1 — CHOOSE PLAN
        ═══════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-5 overflow-hidden">
            <div class="upgrade-section-header">
                <span class="step-badge">1</span>
                <span class="material-icons-round text-gray-400" style="font-size:18px">workspace_premium</span>
                <h2 class="font-bold text-gray-900 text-sm">Choose Your Plan</h2>
            </div>
            <div class="upgrade-section-body">
                @if($packages->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <span class="material-icons-round text-gray-300 mb-3" style="font-size:40px">inventory_2</span>
                        <p class="text-sm text-gray-500 font-medium">No business seller plans available at this time.</p>
                        <p class="text-xs text-gray-400 mt-1">Please contact support for assistance.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 {{ $packages->count() > 1 ? 'sm:grid-cols-2' : 'max-w-sm' }} gap-4">
                        @foreach($packages as $i => $pkg)
                            @php
                                $feats = is_array($pkg->features)
                                    ? $pkg->features
                                    : (is_string($pkg->features) ? json_decode($pkg->features, true) : []);
                                $feats = is_array($feats) ? $feats : [];
                                $isSelected = $i === 0 && !old('package_id') || old('package_id') == $pkg->id;
                            @endphp
                            <label class="block cursor-pointer group">
                                <input type="radio" name="package_id" value="{{ $pkg->id }}"
                                       class="sr-only plan-card-radio"
                                       {{ $isSelected ? 'checked' : '' }} required>
                                <div class="plan-card-inner relative p-5 border-2 rounded-2xl cursor-pointer
                                    {{ $isSelected ? 'border-[#063466] bg-blue-50/60' : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50/80' }}">
                                    {{-- Selected indicator --}}
                                    <div class="plan-check-dot absolute top-4 right-4 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                         style="{{ $isSelected ? 'background:#063466;border-color:#063466' : 'border-color:#d1d5db;background:#fff' }}">
                                        <svg class="w-3 h-3 text-white {{ $isSelected ? '' : 'opacity-0' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    {{-- Plan name --}}
                                    <div class="flex items-center gap-2 mb-3 pr-7">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                             style="background:linear-gradient(135deg,#063466,#1e3a8a)">
                                            <span class="material-icons-round text-white" style="font-size:14px">workspace_premium</span>
                                        </div>
                                        <p class="font-bold text-gray-900 text-sm leading-tight">{{ $pkg->title }}</p>
                                    </div>
                                    {{-- Price --}}
                                    <div class="mb-4 pb-3 border-b border-gray-100">
                                        <p class="text-3xl font-extrabold leading-none" style="color:#063466">
                                            ${{ number_format($pkg->price, 2) }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            per {{ $pkg->duration_days ? $pkg->duration_days . ' days' : 'year' }}
                                        </p>
                                    </div>
                                    {{-- Features --}}
                                    @if(!empty($feats))
                                        <ul class="space-y-1.5">
                                            @foreach(array_slice($feats, 0, 5) as $feat)
                                                <li class="flex items-start gap-2 text-xs text-gray-600">
                                                    <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                @error('package_id')
                    <p class="text-xs text-red-600 mt-3 flex items-center gap-1">
                        <span class="material-icons-round" style="font-size:14px">error</span>{{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- ═══════════════════════════════
             SECTION 2 — IDENTITY VERIFICATION
        ═══════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-5 overflow-hidden">
            <div class="upgrade-section-header">
                <span class="step-badge">2</span>
                <span class="material-icons-round text-gray-400" style="font-size:18px">badge</span>
                <div>
                    <h2 class="font-bold text-gray-900 text-sm leading-tight">Identity Verification</h2>
                    <p class="text-xs text-gray-400 leading-tight">Two government-issued IDs required</p>
                </div>
            </div>
            <div class="upgrade-section-body grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- ID 1 --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center" style="background:#f0f4ff">
                            <span class="text-xs font-bold" style="color:#063466">1</span>
                        </div>
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wide">Government ID 1</p>
                    </div>
                    <div>
                        <label class="upgrade-label">Document Type <span class="text-red-500">*</span></label>
                        <select name="id_type" required class="upgrade-input">
                            <option value="">Select type…</option>
                            @foreach($idTypes as $opt)
                                <option value="{{ $opt }}" {{ old('id_type') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('id_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="upgrade-label">Upload Document <span class="text-red-500">*</span></label>
                        <label for="id_document" class="block cursor-pointer">
                            <div id="id1-dropzone" class="upgrade-upload-zone">
                                <div id="id1-empty-state">
                                    <span class="material-icons-round text-gray-300 block mb-1.5" style="font-size:32px">upload_file</span>
                                    <p class="text-sm font-semibold text-gray-500">Click to upload</p>
                                    <p class="text-xs text-gray-400 mt-0.5">JPG, PNG or PDF — max 5 MB</p>
                                </div>
                                <div id="id1-filled-state" class="hidden flex items-center gap-3 text-left">
                                    <div id="id1-thumb" class="w-12 h-12 rounded-lg overflow-hidden bg-white border border-blue-200 flex items-center justify-center flex-shrink-0 shadow-sm"></div>
                                    <div class="min-w-0 flex-1">
                                        <p id="id1-name" class="text-sm font-semibold text-gray-800 truncate"></p>
                                        <p class="text-xs text-blue-600 mt-0.5">Click to change</p>
                                    </div>
                                    <button type="button" class="doc-preview-btn flex-shrink-0 w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 flex items-center justify-center transition" data-doc="id_document" title="Preview">
                                        <span class="material-icons-round text-blue-600" style="font-size:14px">visibility</span>
                                    </button>
                                </div>
                            </div>
                        </label>
                        <input type="file" name="id_document" id="id_document" accept=".jpg,.jpeg,.png,.pdf" required class="sr-only">
                        @error('id_document')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- ID 2 --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <div class="w-6 h-6 rounded-md flex items-center justify-center" style="background:#f0f4ff">
                            <span class="text-xs font-bold" style="color:#063466">2</span>
                        </div>
                        <p class="text-xs font-bold text-gray-700 uppercase tracking-wide">Government ID 2</p>
                    </div>
                    <div>
                        <label class="upgrade-label">Document Type <span class="text-red-500">*</span></label>
                        <select name="id_type_2" required class="upgrade-input">
                            <option value="">Select type…</option>
                            @foreach($idTypes as $opt)
                                <option value="{{ $opt }}" {{ old('id_type_2') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('id_type_2')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="upgrade-label">Upload Document <span class="text-red-500">*</span></label>
                        <label for="id_document_2" class="block cursor-pointer">
                            <div id="id2-dropzone" class="upgrade-upload-zone">
                                <div id="id2-empty-state">
                                    <span class="material-icons-round text-gray-300 block mb-1.5" style="font-size:32px">upload_file</span>
                                    <p class="text-sm font-semibold text-gray-500">Click to upload</p>
                                    <p class="text-xs text-gray-400 mt-0.5">JPG, PNG or PDF — max 5 MB</p>
                                </div>
                                <div id="id2-filled-state" class="hidden flex items-center gap-3 text-left">
                                    <div id="id2-thumb" class="w-12 h-12 rounded-lg overflow-hidden bg-white border border-blue-200 flex items-center justify-center flex-shrink-0 shadow-sm"></div>
                                    <div class="min-w-0 flex-1">
                                        <p id="id2-name" class="text-sm font-semibold text-gray-800 truncate"></p>
                                        <p class="text-xs text-blue-600 mt-0.5">Click to change</p>
                                    </div>
                                    <button type="button" class="doc-preview-btn flex-shrink-0 w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 flex items-center justify-center transition" data-doc="id_document_2" title="Preview">
                                        <span class="material-icons-round text-blue-600" style="font-size:14px">visibility</span>
                                    </button>
                                </div>
                            </div>
                        </label>
                        <input type="file" name="id_document_2" id="id_document_2" accept=".jpg,.jpeg,.png,.pdf" required class="sr-only">
                        @error('id_document_2')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════
             SECTION 3 — BUSINESS DETAILS
        ═══════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-5 overflow-hidden">
            <div class="upgrade-section-header">
                <span class="step-badge">3</span>
                <span class="material-icons-round text-gray-400" style="font-size:18px">business_center</span>
                <h2 class="font-bold text-gray-900 text-sm">Business Details</h2>
            </div>
            <div class="upgrade-section-body grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- Business license upload --}}
                <div class="space-y-2">
                    <label class="upgrade-label">Business License <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-400 -mt-1 mb-2">Must be current and not expired</p>
                    <label for="business_license" class="block cursor-pointer">
                        <div id="lic-dropzone" class="upgrade-upload-zone">
                            <div id="lic-empty-state">
                                <span class="material-icons-round text-gray-300 block mb-1.5" style="font-size:32px">description</span>
                                <p class="text-sm font-semibold text-gray-500">Click to upload license</p>
                                <p class="text-xs text-gray-400 mt-0.5">JPG, PNG or PDF — max 5 MB</p>
                            </div>
                            <div id="lic-filled-state" class="hidden flex items-center gap-3 text-left">
                                <div id="lic-thumb" class="w-12 h-12 rounded-lg overflow-hidden bg-white border border-blue-200 flex items-center justify-center flex-shrink-0 shadow-sm"></div>
                                <div class="min-w-0 flex-1">
                                    <p id="lic-name" class="text-sm font-semibold text-gray-800 truncate"></p>
                                    <p class="text-xs text-blue-600 mt-0.5">Click to change</p>
                                </div>
                                <button type="button" class="doc-preview-btn flex-shrink-0 w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 flex items-center justify-center transition" data-doc="business_license" title="Preview">
                                    <span class="material-icons-round text-blue-600" style="font-size:14px">visibility</span>
                                </button>
                            </div>
                        </div>
                    </label>
                    <input type="file" name="business_license" id="business_license" accept=".jpg,.jpeg,.png,.pdf" required class="sr-only">
                    @error('business_license')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Relationship to business --}}
                <div class="space-y-2">
                    <label class="upgrade-label">Your Role in the Business <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-400 -mt-1 mb-2">How you are connected to the business entity</p>
                    <select name="relationship_to_business" required class="upgrade-input">
                        <option value="">Select your relationship…</option>
                        @foreach($relationships as $rel)
                            <option value="{{ $rel }}" {{ old('relationship_to_business') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                        @endforeach
                    </select>
                    @error('relationship_to_business')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

                    {{-- Info box --}}
                    <div class="flex items-start gap-2.5 mt-4 rounded-xl bg-blue-50 border border-blue-100 px-3.5 py-3">
                        <span class="material-icons-round text-blue-400 flex-shrink-0" style="font-size:16px">info</span>
                        <p class="text-xs text-blue-700 leading-relaxed">
                            Your business license and role will be reviewed by our team before your account is upgraded. This typically takes 1–2 business days.
                        </p>
                    </div>
                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════
             SECTION 4 — PAYMENT
        ═══════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm mb-5 overflow-hidden">
            <div class="upgrade-section-header justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="step-badge">4</span>
                    <span class="material-icons-round text-gray-400" style="font-size:18px">credit_card</span>
                    <h2 class="font-bold text-gray-900 text-sm">Payment</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500 font-medium">Amount due:</span>
                    <span id="plan-price-display"
                          class="text-sm font-extrabold"
                          style="color:#063466">${{ $packages->isNotEmpty() ? number_format($packages->first()->price, 2) : '—' }}</span>
                </div>
            </div>
            <div class="upgrade-section-body">

                {{-- Security strip --}}
                <div class="flex items-center gap-2 mb-5 rounded-xl bg-emerald-50 border border-emerald-100 px-3.5 py-2.5">
                    <span class="material-icons-round text-emerald-500 flex-shrink-0" style="font-size:16px">lock</span>
                    <p class="text-xs text-emerald-700 font-medium">Your payment information is encrypted and secure.</p>
                    <div class="ml-auto flex items-center gap-2">
                        <svg class="h-5 opacity-50" viewBox="0 0 38 24" fill="none"><rect width="38" height="24" rx="4" fill="#252525"/><text x="5" y="16" font-size="11" fill="white" font-family="Arial" font-weight="bold">VISA</text></svg>
                        <svg class="h-5 opacity-40" viewBox="0 0 38 24" fill="none"><rect width="38" height="24" rx="4" fill="#eb001b"/><circle cx="15" cy="12" r="7" fill="#eb001b"/><circle cx="23" cy="12" r="7" fill="#f79e1b" opacity="0.85"/></svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Card number --}}
                    <div class="sm:col-span-2">
                        <label class="upgrade-label">Card Number <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="card_number" id="upgrade_card_number"
                                   placeholder="1234  5678  9012  3456" required maxlength="19"
                                   class="upgrade-input pr-12">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 material-icons-round text-gray-300" style="font-size:20px">credit_card</span>
                        </div>
                        @error('card_number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    {{-- Expiry month --}}
                    <div>
                        <label class="upgrade-label">Expiry Month <span class="text-red-500">*</span></label>
                        <select name="expiry_month" required class="upgrade-input">
                            <option value="">MM</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        @error('expiry_month')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    {{-- Expiry year --}}
                    <div>
                        <label class="upgrade-label">Expiry Year <span class="text-red-500">*</span></label>
                        <select name="expiry_year" required class="upgrade-input">
                            <option value="">YYYY</option>
                            @for($i = date('Y'); $i <= date('Y') + 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                        @error('expiry_year')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    {{-- CVC --}}
                    <div>
                        <label class="upgrade-label">CVC <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="cvc" placeholder="123" required maxlength="4"
                                   class="upgrade-input pr-10"
                                   inputmode="numeric" pattern="[0-9]*">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 material-icons-round text-gray-300" style="font-size:16px">help_outline</span>
                        </div>
                        @error('cvc')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════
             TERMS + SUBMIT
        ═══════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="upgrade-section-body">
                <label class="flex items-start gap-3 cursor-pointer group">
                    <input type="checkbox" name="agree_terms" value="1" required id="agree_terms"
                           class="mt-0.5 w-4 h-4 rounded border-gray-300 flex-shrink-0"
                           style="accent-color:#063466">
                    <span class="text-sm text-gray-600 leading-relaxed">
                        By submitting, I agree to CayMark's
                        <a href="#" class="font-semibold hover:underline" style="color:#063466">Terms &amp; Conditions</a>
                        and confirm that all uploaded documents are genuine, valid, and currently in effect.
                    </span>
                </label>
                @error('agree_terms')
                    <p class="text-xs text-red-600 mt-2 ml-7 flex items-center gap-1">
                        <span class="material-icons-round" style="font-size:13px">error</span>{{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- Action row --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('seller.account') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all shadow-sm">
                <span class="material-icons-round" style="font-size:16px">close</span>
                Cancel
            </a>
            <button type="submit" id="upgrade-submit-btn"
                    class="inline-flex items-center gap-2 px-8 py-3 text-white text-sm font-bold rounded-xl shadow-lg transition-all duration-150"
                    style="background:linear-gradient(135deg,#063466,#1e3a8a);box-shadow:0 6px 20px rgba(6,52,102,0.30);"
                    onmouseover="this.style.opacity='.9';this.style.transform='translateY(-1px)'"
                    onmouseout="this.style.opacity='1';this.style.transform='translateY(0)'">
                <span class="material-icons-round" style="font-size:17px">check_circle</span>
                Complete Upgrade
                <span class="ml-1 px-2 py-0.5 bg-white/20 rounded-lg text-xs font-bold" id="submit-price-badge">
                    ${{ $packages->isNotEmpty() ? number_format($packages->first()->price, 2) : '—' }}
                </span>
            </button>
        </div>

    </form>
</div>

{{-- ── Document Preview Modal ── --}}
<div id="doc-modal"
     class="fixed inset-0 z-[200] hidden items-center justify-center p-4"
     style="display:none;background:rgba(15,23,42,0.65);backdrop-filter:blur(4px)">
    <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[92vh] overflow-hidden flex flex-col"
         onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <span class="material-icons-round text-gray-400" style="font-size:18px">preview</span>
                <h3 class="text-sm font-bold text-gray-900" id="doc-modal-title">Document Preview</h3>
            </div>
            <button type="button" id="doc-modal-close"
                    class="w-8 h-8 rounded-lg hover:bg-gray-100 flex items-center justify-center text-gray-500 transition">
                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-5 flex-1 overflow-auto flex items-center justify-center min-h-[280px] bg-gray-50">
            <img id="doc-modal-img" class="max-w-full max-h-[68vh] object-contain rounded-xl hidden" alt="">
            <iframe id="doc-modal-pdf" class="w-full min-h-[68vh] border-0 rounded-xl hidden" title="PDF Preview"></iframe>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {

    // ── Plan selection: radio visual state + price update ──
    var prices = {
        @foreach($packages as $pkg)
        "{{ $pkg->id }}": { price: "{{ number_format($pkg->price, 2) }}", label: "{{ addslashes($pkg->title) }}" },
        @endforeach
    };

    document.querySelectorAll('.plan-card-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            // Reset all cards
            document.querySelectorAll('.plan-card-inner').forEach(function(card) {
                card.style.borderColor = '#e5e7eb';
                card.style.background  = '';
                card.style.boxShadow   = '';
            });
            document.querySelectorAll('.plan-check-dot').forEach(function(dot) {
                dot.style.background   = '#fff';
                dot.style.borderColor  = '#d1d5db';
                dot.querySelector('svg').classList.add('opacity-0');
            });

            // Activate selected
            var inner = this.closest('label').querySelector('.plan-card-inner');
            var dot   = inner.querySelector('.plan-check-dot');
            if (inner) {
                inner.style.borderColor = '#063466';
                inner.style.background  = 'rgba(239,246,255,0.6)';
                inner.style.boxShadow   = '0 0 0 3px rgba(6,52,102,0.10)';
            }
            if (dot) {
                dot.style.background  = '#063466';
                dot.style.borderColor = '#063466';
                dot.querySelector('svg').classList.remove('opacity-0');
            }

            // Update price displays
            var d = prices[this.value];
            if (d) {
                var priceEl = document.getElementById('plan-price-display');
                var badgeEl = document.getElementById('submit-price-badge');
                if (priceEl) priceEl.textContent = '$' + d.price;
                if (badgeEl) badgeEl.textContent  = '$' + d.price;
            }
        });
    });

    // ── Card number auto-format ──
    document.getElementById('upgrade_card_number')?.addEventListener('input', function(e) {
        var v = e.target.value.replace(/\D/g, '');
        e.target.value = v.match(/.{1,4}/g)?.join('  ') || v;
    });

    // ── File upload zones ──
    var fileMap = {
        'id_document':      { zone:'id1-dropzone',  empty:'id1-empty-state',  filled:'id1-filled-state',  thumb:'id1-thumb',  name:'id1-name',  label:'ID Document 1' },
        'id_document_2':    { zone:'id2-dropzone',  empty:'id2-empty-state',  filled:'id2-filled-state',  thumb:'id2-thumb',  name:'id2-name',  label:'ID Document 2' },
        'business_license': { zone:'lic-dropzone',  empty:'lic-empty-state',  filled:'lic-filled-state',  thumb:'lic-thumb',  name:'lic-name',  label:'Business License' },
    };
    var fileStore = {};

    function isPdf(f) { return f.type === 'application/pdf' || f.name.toLowerCase().endsWith('.pdf'); }

    function handleFile(inputId, file) {
        if (!file) return;
        var m = fileMap[inputId];
        if (!m) return;
        if (fileStore[inputId]?.url) URL.revokeObjectURL(fileStore[inputId].url);
        var url = URL.createObjectURL(file);
        fileStore[inputId] = { url: url, label: m.label, pdf: isPdf(file) };

        var zone   = document.getElementById(m.zone);
        var empty  = document.getElementById(m.empty);
        var filled = document.getElementById(m.filled);
        var thumb  = document.getElementById(m.thumb);
        var nameEl = document.getElementById(m.name);

        zone.classList.add('has-file');
        empty.classList.add('hidden');
        filled.classList.remove('hidden');
        filled.style.display = 'flex';
        nameEl.textContent = file.name;

        if (!isPdf(file)) {
            thumb.innerHTML = '';
            var img = new Image();
            img.src = url;
            img.className = 'w-full h-full object-cover';
            thumb.appendChild(img);
        } else {
            thumb.innerHTML = '<span class="material-icons-round text-red-400" style="font-size:22px">picture_as_pdf</span>';
        }
    }

    Object.keys(fileMap).forEach(function(id) {
        var input = document.getElementById(id);
        if (!input) return;
        input.addEventListener('change', function() { handleFile(id, this.files[0]); });

        // Drag-and-drop on zone
        var zone = document.getElementById(fileMap[id].zone);
        if (zone) {
            zone.addEventListener('dragover',  function(e) { e.preventDefault(); this.classList.add('drag-over'); });
            zone.addEventListener('dragleave', function()  { this.classList.remove('drag-over'); });
            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                var f = e.dataTransfer.files[0];
                if (f) {
                    input.files = e.dataTransfer.files;
                    handleFile(id, f);
                }
            });
        }
    });

    // ── Document preview modal ──
    function openModal(docId) {
        var d = fileStore[docId]; if (!d) return;
        document.getElementById('doc-modal-title').textContent = d.label;
        var img = document.getElementById('doc-modal-img');
        var pdf = document.getElementById('doc-modal-pdf');
        img.classList.add('hidden');
        pdf.classList.add('hidden');
        if (d.pdf) { pdf.src = d.url; pdf.classList.remove('hidden'); }
        else        { img.src = d.url; img.classList.remove('hidden'); }
        var m = document.getElementById('doc-modal');
        m.style.display = 'flex';
        m.classList.remove('hidden');
    }
    function closeModal() {
        var m = document.getElementById('doc-modal');
        m.style.display = 'none';
        m.classList.add('hidden');
        document.getElementById('doc-modal-img').src = '';
        document.getElementById('doc-modal-pdf').src = '';
    }

    document.querySelectorAll('.doc-preview-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); openModal(this.dataset.doc); });
    });
    document.getElementById('doc-modal-close')?.addEventListener('click', closeModal);
    document.getElementById('doc-modal')?.addEventListener('click', function(e) { if (e.target === this) closeModal(); });

    // Keyboard close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

})();
</script>
@endpush
@endsection
