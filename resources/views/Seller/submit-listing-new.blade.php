@extends('layouts.dashboard')

@section('title')
{{ !empty($listing ?? null) ? 'Edit Listing - CayMark' : 'Create Listing - CayMark' }}
@endsection

@section('content')
@php
    $listing = $listing ?? null;
    $isEdit = !empty($listing);
@endphp
<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    
    .animate-slide-in {
        animation: slideIn 0.4s ease-out;
    }
    
    .step-indicator {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    /* Horizontal page layout: rail (title + steps) | form */
    .create-listing-layout {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    @media (min-width: 1024px) {
        .create-listing-layout {
            flex-direction: row;
            align-items: flex-start;
            gap: 2rem;
        }
        .create-listing-rail {
            position: sticky;
            top: 1rem;
            align-self: flex-start;
            max-width: 280px;
            flex-shrink: 0;
        }
        .create-listing-rail .step-indicator {
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
            margin-bottom: 0;
            gap: 0.5rem;
        }
        .create-listing-rail .step-item {
            width: 100%;
            border-radius: 12px;
            justify-content: flex-start;
            padding: 0.75rem 1rem;
        }
        .create-listing-main {
            flex: 1;
            min-width: 0;
        }
    }

    /* VIN locked section: visually disabled */
    #vinLockedSection.vin-section-locked input:not([type="hidden"]),
    #vinLockedSection.vin-section-locked select,
    #vinLockedSection.vin-section-locked textarea {
        opacity: 0.7;
        background-color: #e5e7eb !important;
        cursor: not-allowed;
        color: #6b7280;
    }
    #vinLockedSection.vin-section-locked .form-label {
        opacity: 0.75;
        color: #6b7280;
    }

    /* Custom identifier type radio (VIN / HIN) */
    .identifier-type-group {
        display: inline-flex;
        gap: 0;
        padding: 4px;
        background: #f1f5f9;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .identifier-type-option {
        display: inline-flex;
        cursor: pointer;
        margin: 0;
    }
    .identifier-type-option input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }
    .identifier-type-option .identifier-type-label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        font-size: 0.9375rem;
        font-weight: 500;
        color: #64748b;
        background: transparent;
        border-radius: 8px;
        transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
    }
    .identifier-type-option:hover .identifier-type-label {
        color: #475569;
    }
    .identifier-type-option input:checked + .identifier-type-label {
        background: #fff;
        color: #0f172a;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .identifier-type-option input:focus-visible + .identifier-type-label {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    /* Custom checkbox */
    .custom-checkbox-wrap {
        position: relative;
    }
    .custom-checkbox-input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 1;
    }
    .custom-checkbox-box,
    .custom-checkbox-label {
        position: relative;
        z-index: 0;
    }
    .custom-checkbox-box {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.25rem;
        height: 1.25rem;
        flex-shrink: 0;
        border: 2px solid #cbd5e1;
        border-radius: 6px;
        background: #fff;
        transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .custom-checkbox-box::after {
        content: '';
        width: 5px;
        height: 9px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg) scale(0);
        margin-bottom: 3px;
        transition: transform 0.15s ease;
    }
    .custom-checkbox-wrap:hover .custom-checkbox-box {
        border-color: #94a3b8;
    }
    .custom-checkbox-input:checked + .custom-checkbox-box {
        background: #3b82f6;
        border-color: #3b82f6;
    }
    .custom-checkbox-input:checked + .custom-checkbox-box::after {
        transform: rotate(45deg) scale(1);
    }
    .custom-checkbox-input:focus-visible + .custom-checkbox-box {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.25);
    }

    /* Custom file upload (step 2) */
    .file-upload-box {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 140px;
        padding: 1.25rem 1rem;
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }
    .file-upload-box:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    .file-upload-box.has-file {
        border-style: solid;
        border-color: #22c55e;
        background: #f0fdf4;
    }
    .file-upload-input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 1;
    }
    .file-upload-box .file-upload-inner {
        pointer-events: none;
        text-align: center;
        z-index: 0;
    }
    .file-upload-box .file-upload-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        margin-bottom: 0.5rem;
        border-radius: 12px;
        background: #e2e8f0;
        color: #64748b;
        font-size: 1.5rem;
    }
    .file-upload-box:hover .file-upload-icon {
        background: #bfdbfe;
        color: #3b82f6;
    }
    .file-upload-box.has-file .file-upload-icon {
        background: #bbf7d0;
        color: #16a34a;
    }
    .file-upload-box .file-upload-btn-text {
        font-weight: 600;
        font-size: 0.9375rem;
        color: #334155;
    }
    .file-upload-box:hover .file-upload-btn-text { color: #1e40af; }
    .file-upload-box.has-file .file-upload-btn-text { color: #15803d; }
    .file-upload-box .file-upload-status {
        font-size: 0.8125rem;
        color: #64748b;
        margin-top: 0.25rem;
    }
    .file-upload-box.has-file .file-upload-status { color: #15803d; }
    
    .step-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 2rem;
        border-radius: 50px;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .step-item.active {
        background: linear-gradient(135deg, #063466 0%, #1e3a8a 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(6, 52, 102, 0.3);
    }
    
    .step-item.completed {
        background: #10b981;
        color: white;
    }
    
    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
    }
    
    .form-section {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 1.5rem 2rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        max-height: calc(100vh - 200px);
        overflow-y: auto;
        overflow-x: hidden;
        position: relative;
        /* Add padding-right to make space for scrollbar inside border-radius */
        padding-right: calc(2rem - 8px);
    }
    
    /* Custom scrollbar styling that respects border-radius */
    .form-section::-webkit-scrollbar {
        width: 8px;
    }
    
    .form-section::-webkit-scrollbar-track {
        background: transparent;
        margin: 20px 0;
    }
    
    .form-section::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
        transition: background 0.3s ease;
        /* Ensure scrollbar thumb respects container border-radius */
        margin: 10px 0;
    }
    
    .form-section::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Firefox scrollbar styling */
    .form-section {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }
    
    .form-section:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    /* Inside main floating card: keep sections white, lighter chrome */
    .create-listing-shell {
        background: #ffffff;
    }
    .create-listing-shell .form-section {
        background: #ffffff !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
        border: 1px solid #e5e7eb;
    }
    .create-listing-shell .form-section:hover {
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
    }
    
    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .section-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #063466 0%, #1e3a8a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(6, 52, 102, 0.3);
        flex-shrink: 0;
    }
    
    .form-input {
        width: 100%;
        padding: 0.625rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.9375rem;
        transition: all 0.3s ease;
        background: white;
        box-sizing: border-box;
        line-height: 1.4;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #063466;
        box-shadow: 0 0 0 3px rgba(6, 52, 102, 0.1);
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        letter-spacing: -0.2px;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #063466 0%, #1e3a8a 100%);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9375rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(6, 52, 102, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(6, 52, 102, 0.4);
    }
    
    .btn-secondary {
        background: #64748b;
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background: #475569;
        transform: translateY(-2px);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 0.875rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }
    
    .photo-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .photo-preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid #e2e8f0;
    }
    
    .photo-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .photo-preview-item .photo-preview-remove {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 26px;
        height: 26px;
        border: none;
        border-radius: 50%;
        background: rgba(239, 68, 68, 0.95);
        color: #fff;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        line-height: 1;
        transition: background 0.2s;
        z-index: 2;
    }
    .photo-preview-item .photo-preview-remove:hover {
        background: #dc2626;
    }
    .cover-photo-remove-wrap { margin-top: 0.5rem; }
    .cover-photo-remove-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.6rem;
        font-size: 0.8125rem;
        color: #dc2626;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.2s, color 0.2s;
    }
    .cover-photo-remove-btn:hover { background: #fee2e2; color: #b91c1c; }

    .duration-option {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        background: white;
        box-sizing: border-box;
        min-width: 0;
    }
    
    .duration-option:hover {
        border-color: #063466;
        background: #f8fafc;
        transform: translateY(-2px);
    }
    
    .duration-option input[type="radio"]:checked + label,
    .duration-option:has(input[type="radio"]:checked) {
        border-color: #063466;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        box-shadow: 0 4px 15px rgba(6, 52, 102, 0.2);
    }
    
    .info-box {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 0.5rem;
    }
    
    .warning-box {
        background: #fef3c7;
        border: 1px solid #fcd34d;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .step-indicator {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .step-item {
            width: 100%;
            justify-content: center;
        }
        
        .form-section {
            padding: 1.5rem;
        }
        
        .section-header {
            flex-direction: column;
            text-align: center;
        }
        
        .photo-preview-grid {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        }
        
        .duration-option {
            padding: 1rem;
        }
    }
    
    @media (max-width: 640px) {
        .form-section {
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .photo-preview-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .section-icon {
            margin-bottom: 0.5rem;
        }
    }
    
    /* Ensure all elements respect container width */
    * {
        box-sizing: border-box;
    }
</style>

<div class="flex-1 min-h-0 w-full overflow-y-auto overflow-x-hidden bg-[#FDFBF8] p-4 sm:p-6 lg:p-8">
    <div class="w-full max-w-7xl mx-auto">
        <div class="create-listing-shell rounded-2xl bg-white p-5 sm:p-6 lg:p-8 shadow-lg border border-gray-100">
        <div class="create-listing-layout">
        <aside class="create-listing-rail w-full">
            <div class="text-center lg:text-left mb-4 lg:mb-6 animate-fade-in">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">
                    {{ $isEdit ? 'Edit Listing' : 'Create New Listing' }}
                </h1>
                <p class="text-gray-600 text-sm md:text-base">{{ $isEdit ? 'Update your listing details below.' : 'List your vehicle or vessel in just a few simple steps' }}</p>
            </div>
            <div class="step-indicator animate-fade-in">
                <div class="step-item active" id="step-indicator-1">
                    <div class="step-number">1</div>
                    <span>Vehicle Info</span>
                </div>
                <div class="step-item" id="step-indicator-2">
                    <div class="step-number">2</div>
                    <span>Photos</span>
                </div>
                <div class="step-item" id="step-indicator-3">
                    <div class="step-number">3</div>
                    <span>Auction Settings</span>
                </div>
                <div class="step-item" id="step-indicator-4">
                    <div class="step-number">4</div>
                    <span>Payment</span>
                </div>
            </div>
        </aside>

        <div class="create-listing-main w-full min-w-0">
        <!-- Error Messages Display -->
        @if($errors->any() || session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg animate-fade-in">
                <div class="flex items-start">
                    <span class="material-icons-round text-red-500 mr-3 mt-0.5">error</span>
                    <div class="flex-1">
                        <h3 class="text-red-800 font-semibold mb-2">Please fix the following errors:</h3>
                        <ul class="list-disc list-inside text-red-700 space-y-1">
                            @if(session('error'))
                                <li>{{ session('error') }}</li>
                            @endif
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        @if(session('error_section'))
                            <p class="text-sm text-red-600 mt-3 font-medium">
                                <span class="material-icons-round text-sm align-middle">arrow_downward</span>
                                Please scroll to <strong>Section {{ str_replace('section', '', session('error_section')) }}</strong> to fix the errors.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <form id="listingForm" action="{{ $isEdit ? route('seller.listings.update', $listing) : route('seller.listings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($isEdit) @method('PUT') @endif

            <!-- SECTION 1: VEHICLE INFORMATION -->
            <div id="section1" class="form-section animate-slide-in">
                <div class="section-header">
                    <div class="section-icon">1</div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Vehicle Information</h2>
                        <p class="text-xs text-gray-600">Enter your vehicle or vessel details</p>
                    </div>
                </div>
                
                <!-- Vehicle type: Automobile vs Marine -->
                <div class="mb-3">
                    <label class="form-label">VEHICLE TYPE <span class="text-red-500">*</span></label>
                    <div class="identifier-type-group">
                        <label class="identifier-type-option">
                            <input type="radio" name="vin_hin_type" value="vin" checked>
                            <span class="identifier-type-label">Automobile</span>
                        </label>
                        <label class="identifier-type-option">
                            <input type="radio" name="vin_hin_type" value="hin">
                            <span class="identifier-type-label">Marine</span>
                        </label>
                    </div>
                </div>
                <!-- VIN/HIN Input -->
                <div class="mb-4">
                    <label class="form-label" id="vin_hin_label">VIN Number</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" 
                               id="vin_hin" 
                               name="vin" 
                               class="form-input flex-1 uppercase" 
                               placeholder="Enter VIN (17 characters)"
                               maxlength="17"
                               value="{{ old('vin', $listing->vin ?? '') }}">
                        <button type="button" 
                                id="searchVinBtn" 
                                class="btn-primary whitespace-nowrap px-4 py-2.5">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </div>
                    <div id="vinDecoderMessage" class="mt-1 text-xs"></div>
                </div>

                <!-- Auto-populated fields (hidden until decoded) -->
                <div id="decodedFields" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 mb-4" style="display: none;">
                    <div>
                        <label class="form-label">MAKE</label>
                        <input type="text" name="make" id="decoded_make" class="form-input uppercase bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="form-label">MODEL</label>
                        <input type="text" name="model" id="decoded_model" class="form-input uppercase bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="form-label">YEAR</label>
                        <input type="text" name="year" id="decoded_year" class="form-input uppercase bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="form-label">TRIM</label>
                        <input type="text" name="trim" id="decoded_trim" class="form-input uppercase bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="form-label">ENGINE SIZE</label>
                        <input type="text" name="engine_size" id="decoded_engine" class="form-input uppercase bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="form-label">CYLINDERS</label>
                        <input type="text" name="cylinders" id="decoded_cylinders" class="form-input uppercase bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="form-label">DRIVE TYPE</label>
                        <input type="text" name="drive_type" id="decoded_drive" class="form-input uppercase bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="form-label">FUEL TYPE</label>
                        <input type="text" name="fuel_type" id="decoded_fuel" class="form-input uppercase bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="form-label">TRANSMISSION</label>
                        <input type="text" name="transmission" id="decoded_transmission" class="form-input uppercase bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="form-label">VEHICLE TYPE</label>
                        <input type="text" name="vehicle_type" id="decoded_vehicle_type" class="form-input uppercase bg-gray-50" readonly>
                    </div>
                </div>

                <!-- Manual entry + condition fields: disabled until 3 VIN/HIN attempts (when creating); always editable when editing) -->
                <div id="vinLockedSection" class="{{ $isEdit ? '' : 'vin-section-locked' }}">
                @if(!$isEdit)
                <p id="vinLockedMessage" class="mb-3 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
                    <i class="fas fa-lock mr-2"></i>Use the VIN/HIN reader above to auto-fill vehicle details.
                </p>
                @else
                <p id="vinLockedMessage" class="mb-3 text-sm text-gray-600 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2" style="display: none;"></p>
                @endif
                <!-- Manual Entry Fields -->
                <div id="manualFields" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="form-label">MAKE</label>
                        <input type="text" name="make" class="form-input uppercase js-letters-only" style="text-transform: uppercase;" pattern="[A-Za-z\s\-]+" title="Letters only" value="{{ old('make', $listing->make ?? '') }}">
                    </div>
                    <div>
                        <label class="form-label">MODEL</label>
                        <input type="text" name="model" class="form-input uppercase js-letters-only" style="text-transform: uppercase;" pattern="[A-Za-z\s\-]+" title="Letters only" value="{{ old('model', $listing->model ?? '') }}">
                    </div>
                    <div>
                        <label class="form-label">YEAR</label>
                        <input type="text" name="year" class="form-input js-year-input" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" max="{{ date('Y') }}" title="4-digit year, cannot exceed {{ date('Y') }}" value="{{ old('year', $listing->year ?? '') }}">
                    </div>
                    <div>
                        <label class="form-label">TRIM</label>
                        <input type="text" name="trim" class="form-input uppercase" style="text-transform: uppercase;" value="{{ old('trim', $listing->trim ?? '') }}">
                    </div>
                </div>

                <!-- Required Condition Fields -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="form-label">TITLE STATUS <span class="text-red-500">*</span></label>
                        <select name="title_status" required class="form-input" id="title_status_select">
                            <option value="">Select Title Status</option>
                            <option value="yes" {{ old('title_status', isset($listing) && $listing->title_status === 'CLEAN' ? 'yes' : '') == 'yes' ? 'selected' : '' }}>Has Title</option>
                            <option value="no" {{ old('title_status', isset($listing) && $listing->title_status === 'SALVAGE' ? 'no' : '') == 'no' ? 'selected' : '' }}>No Title</option>
                        </select>
                        <div id="no-title-modal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display: none;">
                            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-3">No Title</h3>
                                <p class="text-gray-700 text-sm mb-4">No Title means the vehicle does not have an ownership title.</p>
                                <p class="text-gray-700 text-sm font-semibold mb-2">This option may only be selected if:</p>
                                <ul class="list-disc list-inside text-gray-700 text-sm space-y-1 mb-4">
                                    <li>Vehicle is sold strictly for parts, export, or salvage</li>
                                    <li>Vehicle is abandoned, imported, or from a salvage lot</li>
                                    <li>Vehicle cannot be registered for road use</li>
                                    <li>Title was never issued</li>
                                </ul>
                                <p class="text-amber-700 text-sm font-medium mb-4">Please note: Do not select No Title if the title is lost, stolen, damaged, or being withheld.</p>
                                <button type="button" onclick="document.getElementById('no-title-modal').style.display='none'; document.getElementById('no-title-modal').classList.add('hidden');" class="w-full py-2 bg-blue-600 text-white rounded-lg font-semibold">I understand</button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">ISLAND LOCATION <span class="text-red-500">*</span></label>
                        <select name="island" required class="form-input">
                            <option value="">Select Island</option>
                            @foreach(config('islands.list', []) as $island)
                                <option value="{{ $island }}" {{ old('island', $listing->island ?? '') === $island ? 'selected' : '' }}>{{ $island }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">EXTERIOR COLOR <span class="text-red-500">*</span></label>
                        <select name="color" required class="form-input">
                            <option value="">Select Color</option>
                            @php $selColor = old('color', $listing->color ?? ''); @endphp
                            @foreach(config('listing_colors.allowed', []) as $c)
                                <option value="{{ $c }}" {{ $selColor === $c ? 'selected' : '' }}>{{ ucfirst(strtolower($c)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">INTERIOR COLOR <span class="text-red-500">*</span></label>
                        <select name="interior_color" required class="form-input">
                            <option value="">Select Color</option>
                            @php $selInt = old('interior_color', $listing->interior_color ?? ''); @endphp
                            @foreach(config('listing_colors.allowed', []) as $c)
                                <option value="{{ $c }}" {{ $selInt === $c ? 'selected' : '' }}>{{ ucfirst(strtolower($c)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Primary and Secondary Damage side by side, same options -->
                    <div class="col-span-2 md:col-span-3 lg:col-span-4 grid grid-cols-1 md:grid-cols-2 gap-4 mb-2">
                        <div>
                            <label class="form-label">PRIMARY DAMAGE <span class="text-red-500">*</span></label>
                            <select name="primary_damage" required class="form-input">
                                <option value="">Select Damage Type</option>
                                @php $priD = old('primary_damage', $listing->primary_damage ?? ''); @endphp
                                <option value="NONE" {{ $priD === 'NONE' ? 'selected' : '' }}>None</option>
                                <option value="FRONT_END">Front End</option>
                                <option value="REAR_END">Rear End</option>
                                <option value="SIDE">Side</option>
                                <option value="LEFT_SIDE">Left Side</option>
                                <option value="RIGHT_SIDE">Right Side</option>
                                <option value="FRONT_LEFT">Front Left</option>
                                <option value="FRONT_RIGHT">Front Right</option>
                                <option value="REAR_LEFT">Rear Left</option>
                                <option value="REAR_RIGHT">Rear Right</option>
                                <option value="ALL_OVER">All Over</option>
                                <option value="FLOOD">Flood</option>
                                <option value="FIRE">Fire</option>
                                <option value="HAIL">Hail</option>
                                <option value="MECHANICAL">Mechanical</option>
                                <option value="ENGINE">Engine</option>
                                <option value="TRANSMISSION">Transmission</option>
                                <option value="ROLLOVER">Rollover</option>
                                <option value="THEFT_RECOVERY">Theft Recovery</option>
                                <option value="VANDALISM">Vandalism</option>
                                <option value="STRIPPED">Stripped</option>
                                <option value="BURN">Burn</option>
                                <option value="MINOR_DENTS">Minor Dents</option>
                                <option value="MAJOR_DENTS">Major Dents</option>
                                <option value="DISABLED">Disabled</option>
                                <option value="NORMAL_WEAR">Normal Wear</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">SECONDARY DAMAGE</label>
                            <select name="secondary_damage" class="form-input">
                                <option value="">Select (Optional)</option>
                                @php $secD = old('secondary_damage', $listing->secondary_damage ?? ''); @endphp
                                <option value="NONE" {{ $secD === 'NONE' ? 'selected' : '' }}>None</option>
                                <option value="FRONT_END">Front End</option>
                                <option value="REAR_END">Rear End</option>
                                <option value="SIDE">Side</option>
                                <option value="LEFT_SIDE">Left Side</option>
                                <option value="RIGHT_SIDE">Right Side</option>
                                <option value="FRONT_LEFT">Front Left</option>
                                <option value="FRONT_RIGHT">Front Right</option>
                                <option value="REAR_LEFT">Rear Left</option>
                                <option value="REAR_RIGHT">Rear Right</option>
                                <option value="ALL_OVER">All Over</option>
                                <option value="FLOOD">Flood</option>
                                <option value="FIRE">Fire</option>
                                <option value="HAIL">Hail</option>
                                <option value="MECHANICAL">Mechanical</option>
                                <option value="ENGINE">Engine</option>
                                <option value="TRANSMISSION">Transmission</option>
                                <option value="ROLLOVER">Rollover</option>
                                <option value="THEFT_RECOVERY">Theft Recovery</option>
                                <option value="VANDALISM">Vandalism</option>
                                <option value="STRIPPED">Stripped</option>
                                <option value="BURN">Burn</option>
                                <option value="MINOR_DENTS">Minor Dents</option>
                                <option value="MAJOR_DENTS">Major Dents</option>
                                <option value="DISABLED">Disabled</option>
                                <option value="NORMAL_WEAR">Normal Wear</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">HAS KEYS <span class="text-red-500">*</span></label>
                        <select name="keys_available" required class="form-input">
                            <option value="">Select</option>
                            @php $keysVal = old('keys_available', isset($listing) && $listing->keys_available ? 'yes' : 'no'); @endphp
                            <option value="yes" {{ $keysVal === 'yes' ? 'selected' : '' }}>YES</option>
                            <option value="no" {{ $keysVal === 'no' ? 'selected' : '' }}>NO</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">IS THIS VEHICLE SALVAGED? <span class="text-red-500">*</span></label>
                        <select name="is_salvaged" required class="form-input" id="is_salvaged_select">
                            <option value="">Select</option>
                            @php $salVal = old('is_salvaged', isset($listing) && $listing->condition === 'salvaged' ? '1' : '0'); @endphp
                            <option value="0" {{ $salVal === '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $salVal === '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        <div id="salvage-notice" class="mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800 hidden">
                            <strong>Salvaged vehicle:</strong> A salvaged vehicle is one that has been declared a total loss by an insurance company or has significant damage (typically 75% or more).
                        </div>
                    </div>
                    <div>
                        <label class="form-label">ODOMETER (miles)</label>
                        <input type="number" name="odometer" class="form-input" min="0" max="9999999" step="1" placeholder="e.g. 45000" value="{{ old('odometer', $listing->odometer ?? '') }}">
                        <p class="text-xs text-gray-500 mt-1">Leave blank if unknown. Enter current mileage reading.</p>
                    </div>
                    <div class="col-span-2 md:col-span-3 lg:col-span-4 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <x-custom-checkbox name="odometer_estimated" value="1" label="Estimated reading" :checked="(bool) old('odometer_estimated', $listing->odometer_estimated ?? false)" />
                        <p class="text-xs text-gray-500">Check if odometer is not actual (e.g. exempt or estimated).</p>
                    </div>
                    <div>
                        <label class="form-label">RUN & DRIVE <span class="text-red-500">*</span></label>
                        <select name="run_and_drive" required class="form-input">
                            <option value="">Select</option>
                            @php $rdVal = old('run_and_drive', $listing->run_and_drive ?? ''); @endphp
                            <option value="yes" {{ $rdVal === 'yes' ? 'selected' : '' }}>Yes</option>
                            <option value="no" {{ $rdVal === 'no' ? 'selected' : '' }}>No</option>
                        </select>
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                            <strong>Run & Drive:</strong> Indicates whether the vehicle is able to start and drive under its own power. Select "Yes" if the vehicle can be driven, or "No" if it requires towing or transport.
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-3 lg:col-span-4">
                        <label class="form-label">ADDITIONAL NOTES</label>
                        <textarea name="additional_notes" rows="2" class="form-input uppercase" style="text-transform: uppercase;" placeholder="Enter any additional notes...">{{ old('additional_notes') }}</textarea>
                    </div>
                </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" id="btn-continue-to-photos" onclick="typeof showSection === 'function' && showSection(2)" class="btn-primary">
                        Continue to Photos & Media <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- SECTION 2: PHOTOS & MEDIA -->
            <div id="section2" class="form-section" style="display: @if(session('error_section') === 'section2') block; border: 2px solid #ef4444; border-radius: 12px; @else none; @endif">
                <div class="section-header">
                    <div class="section-icon">2</div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Upload Photos & Media</h2>
                        <p class="text-xs text-gray-600">{{ $isEdit ? 'Replace by choosing new photos below, or keep current.' : 'Add high-quality photos and video of your vehicle' }}</p>
                    </div>
                </div>
                @if($isEdit && $listing->images->count() > 0)
                    <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-xl">
                        <p class="text-sm font-semibold text-gray-700 mb-2">Current photos ({{ $listing->images->count() }})</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($listing->images as $img)
                                @php $imgSrc = str_contains($img->image_path ?? '', '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path); @endphp
                                <img src="{{ $imgSrc }}" alt="" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Upload new photos below to replace all.</p>
                    </div>
                @endif

                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <p class="text-sm font-semibold text-blue-800 mb-1"><i class="fas fa-info-circle mr-1"></i> Required Photos</p>
                    <p class="text-xs text-blue-700">You must include: Front view (cover photo), Rear view, Left side, Right side, Dashboard (including odometer), and VIN on door or vehicle information label.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">COVER PHOTO (Front View) @if(!$isEdit)<span class="text-red-500">*</span>@endif</label>
                        <p class="text-xs text-gray-600 mb-2">Front view of vehicle or vessel (Main photo)</p>
                        <div class="file-upload-box" id="coverPhotoUploadBox">
                            <input type="file" name="cover_photo" id="cover_photo_input" @if(!$isEdit) required @endif accept="image/*" class="file-upload-input">
                            <div class="file-upload-inner">
                                <div class="file-upload-icon"><i class="fas fa-camera"></i></div>
                                <div class="file-upload-btn-text">{{ $isEdit ? 'Replace Cover Photo' : 'Choose Cover Photo' }}</div>
                                <div class="file-upload-status" id="coverPhotoStatus">{{ $isEdit ? 'Keep current or choose new' : 'No file chosen' }}</div>
                            </div>
                        </div>
                        <div id="coverPhotoPreview" class="mt-2"></div>
                    </div>

                    <div>
                        <label class="form-label">ADDITIONAL PHOTOS @if(!$isEdit)<span class="text-red-500">*</span>@endif</label>
                        <p class="text-xs text-gray-600 mb-2">{{ $isEdit ? 'Upload 5–10 new photos to replace existing, or leave empty to keep current.' : 'Required: Rear view, Left side, Right side, Dashboard/Odometer, VIN on door. Max: 10 photos.' }}</p>
                        <div class="file-upload-box" id="photosUploadBox">
                            <input type="file" name="photos[]" id="photos_input" multiple @if(!$isEdit) required @endif accept="image/*" class="file-upload-input" min="{{ $isEdit ? 0 : 5 }}" max="10">
                            <div class="file-upload-inner">
                                <div class="file-upload-icon"><i class="fas fa-images"></i></div>
                                <div class="file-upload-btn-text">Choose Photos (5–10)</div>
                                <div class="file-upload-status" id="photoCount">0 photos selected</div>
                            </div>
                        </div>
                        <div id="photoPreview" class="photo-preview-grid mt-2"></div>
                        <p id="photoWarning" class="text-xs text-amber-600 mt-1" style="display: none;">
                            <i class="fas fa-exclamation-triangle mr-1"></i> MINIMUM 5 REQUIRED PHOTOS.
                        </p>
                    </div>
                </div>

                <!-- Video Upload -->
                <div class="mb-4">
                    <label class="form-label">UPLOAD VIDEO <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-600 mb-2">Engine bay — Video must be 20 seconds to 1 minute long. Required for all listings.</p>
                    <div class="file-upload-box" id="videoUploadBox">
                        <input type="file" name="video" id="video_input" @if(!$isEdit) required @endif accept="video/mp4,video/quicktime,video/x-msvideo,video/webm" class="file-upload-input">
                        <div class="file-upload-inner">
                            <div class="file-upload-icon"><i class="fas fa-video"></i></div>
                            <div class="file-upload-btn-text">{{ $isEdit ? 'Replace Video' : 'Choose Video' }}</div>
                            <div class="file-upload-status" id="videoStatus">{{ $isEdit ? 'Keep current or choose new' : 'No video chosen' }}</div>
                        </div>
                    </div>
                    <div id="videoPreview" class="mt-2"></div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-between">
                    <button type="button" onclick="showSection(1)" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </button>
                    <button type="button" onclick="showSection(3)" class="btn-primary">
                        Continue to Auction Settings <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- SECTION 3: AUCTION SETTINGS & PAYMENT -->
            <div id="section3" class="form-section" style="display: @if(session('error_section') === 'section3') block; border: 2px solid #ef4444; border-radius: 12px; @else none; @endif">
                <div class="section-header">
                    <div class="section-icon">3</div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Auction Settings</h2>
                        <p class="text-xs text-gray-600">Configure your auction duration and pricing</p>
                    </div>
                </div>
                
                <!-- Auction Duration -->
                @php $dur = old('auction_duration', $listing->auction_duration ?? 7); @endphp
                <div class="mb-4">
                    <label class="form-label">AUCTION DURATION <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-5 gap-3">
                        <label class="duration-option">
                            <input type="radio" name="auction_duration" value="5" required class="hidden" {{ (string)$dur === '5' ? 'checked' : '' }}>
                            <div class="font-semibold text-lg">5</div>
                            <div class="text-sm text-gray-600">Days</div>
                        </label>
                        <label class="duration-option">
                            <input type="radio" name="auction_duration" value="7" required class="hidden" {{ (string)$dur === '7' ? 'checked' : '' }}>
                            <div class="font-semibold text-lg">7</div>
                            <div class="text-sm text-gray-600">Days</div>
                        </label>
                        <label class="duration-option">
                            <input type="radio" name="auction_duration" value="14" required class="hidden" {{ (string)$dur === '14' ? 'checked' : '' }}>
                            <div class="font-semibold text-lg">14</div>
                            <div class="text-sm text-gray-600">Days</div>
                        </label>
                        <label class="duration-option">
                            <input type="radio" name="auction_duration" value="21" required class="hidden" {{ (string)$dur === '21' ? 'checked' : '' }}>
                            <div class="font-semibold text-lg">21</div>
                            <div class="text-sm text-gray-600">Days</div>
                        </label>
                        <label class="duration-option">
                            <input type="radio" name="auction_duration" value="28" required class="hidden" {{ (string)$dur === '28' ? 'checked' : '' }}>
                            <div class="font-semibold text-lg">28</div>
                            <div class="text-sm text-gray-600">Days</div>
                        </label>
                    </div>
                </div>

                <!-- Optional Pricing -->
                <div class="mb-4">
                    <h3 class="text-base font-semibold mb-3 text-gray-900">Auction Pricing (Optional)</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="form-label">STARTING BID</label>
                            <input type="number" name="starting_price" step="0.01" min="0" class="form-input" placeholder="$0.00" value="{{ old('starting_price', $listing->starting_price ?? '') }}">
                            <p class="text-xs text-gray-500 mt-1">Must be > $0 if entered</p>
                        </div>
                        <div>
                            <label class="form-label">RESERVE PRICE</label>
                            <input type="number" name="reserve_price" step="0.01" min="0" class="form-input" placeholder="$0.00" value="{{ old('reserve_price', $listing->reserve_price ?? '') }}">
                            <p class="text-xs text-gray-500 mt-1">Must be ≥ Starting Bid</p>
                        </div>
                        <div>
                            <label class="form-label">BUY NOW PRICE</label>
                            <input type="number" name="buy_now_price" step="0.01" min="0" class="form-input" placeholder="$0.00" value="{{ old('buy_now_price', $listing->buy_now_price ?? '') }}">
                        </div>
                    </div>
                    <div class="info-box mt-3">
                        <p class="text-sm text-gray-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            If no pricing is entered, auction runs using default system pricing with no reserve.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-between">
                    <button type="button" onclick="showSection(2)" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </button>
                    <button type="button" onclick="showSection(4)" class="btn-primary">
                        Continue to Payment <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- SECTION 4: PAYMENT -->
            <div id="section4" class="form-section" style="display: none;">
                <div class="section-header">
                    <div class="section-icon">4</div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Payment & Review</h2>
                        <p class="text-xs text-gray-600">Payment details and final confirmation</p>
                    </div>
                </div>

                <!-- Payment (Individual Sellers) -->
                @php
                    $sellerPackage = $user->activeSubscription?->package;
                    $isIndividualSeller = $sellerPackage && (
                        (float) $sellerPackage->price === 25.00 ||
                        stripos($sellerPackage->title ?? '', 'individual') !== false
                    );
                @endphp
                @if($isIndividualSeller)
                <div class="mb-4 warning-box">
                    <h3 class="text-base font-semibold mb-2 text-amber-900">
                        <i class="fas fa-exclamation-circle mr-2"></i>Payment Required — $25 Listing Fee
                    </h3>
                    <p class="text-xs mb-3 text-amber-800">Individual Sellers must pay a $25 listing fee for each submission.</p>
                    <div class="mb-3">
                        <label class="form-label">PAYMENT METHOD <span class="text-red-500">*</span></label>
                        <select name="payment_method" required class="form-input">
                            <option value="">Select Payment Method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="stored_payment">Stored Payment Method</option>
                        </select>
                    </div>
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                        <p class="text-xs text-amber-800"><i class="fas fa-lock mr-1"></i> Your payment of <strong>$25.00</strong> will be processed upon submission.</p>
                    </div>
                </div>
                @else
                <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-sm text-gray-700">No listing fee applies to your business account. Review and submit below.</p>
                </div>
                @endif

                <!-- Final Acknowledgment -->
                <div class="mb-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <p class="text-xs font-semibold text-center text-gray-700">
                        <i class="fas fa-shield-alt mr-2 text-blue-600"></i>
                        By submitting this listing, you agree that all information provided is accurate and you accept CayMark's Terms and Conditions. All listings are subject to admin approval before going live.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-between">
                    <button type="button" onclick="showSection(3)" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </button>
                    <button type="submit" class="btn-success">
                        @if($isEdit)
                            <i class="fas fa-save mr-2"></i> Save changes
                        @else
                            <i class="fas fa-check-circle mr-2"></i> Complete Submission
                        @endif
                    </button>
                </div>
            </div>
        </form>
        </div>
        </div>
        </div>
    </div>
</div>

<style>
    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        animation: fadeIn 0.3s ease-in;
    }
    
    .error-message .material-icons-round {
        font-size: 1rem;
    }
    
    .field-error {
        border-color: #ef4444 !important;
        background-color: #fef2f2 !important;
    }
    
    .field-success {
        border-color: #10b981 !important;
        background-color: #f0fdf4 !important;
    }
    
    .success-indicator {
        color: #10b981;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .form-group {
        position: relative;
    }
    
    .error-tooltip {
        position: absolute;
        top: 100%;
        left: 0;
        background: #ef4444;
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        z-index: 50;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        margin-top: 0.25rem;
        white-space: nowrap;
    }
    
    .error-tooltip::before {
        content: '';
        position: absolute;
        top: -4px;
        left: 12px;
        width: 8px;
        height: 8px;
        background: #ef4444;
        transform: rotate(45deg);
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    
    .shake {
        animation: shake 0.5s ease-in-out;
    }
</style>

<script>
    // Section navigation - define first so "Continue to Photos" etc. always work
    function showSection(sectionNum) {
        var ids = ['section1', 'section2', 'section3', 'section4'];
        for (var i = 0; i < ids.length; i++) {
            var el = document.getElementById(ids[i]);
            if (el) {
                el.style.display = 'none';
                el.style.border = '';
                el.style.borderRadius = '';
            }
        }
        var section = document.getElementById('section' + sectionNum);
        if (section) {
            section.style.display = 'block';
            section.classList.add('animate-slide-in');
            setTimeout(function() {
                try {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } catch (e) {}
            }, 50);
        }
        for (var j = 1; j <= 4; j++) {
            var ind = document.getElementById('step-indicator-' + j);
            if (!ind) continue;
            if (j < sectionNum) {
                ind.classList.remove('active');
                ind.classList.add('completed');
            } else if (j === sectionNum) {
                ind.classList.remove('completed');
                ind.classList.add('active');
            } else {
                ind.classList.remove('active', 'completed');
            }
        }
    }

    // Global error handler for runtime JavaScript errors
    window.addEventListener('error', function(e) {
        console.error('JavaScript Runtime Error:', {
            message: e.message,
            filename: e.filename,
            lineno: e.lineno,
            colno: e.colno,
            error: e.error
        });
        
        // Display user-friendly error message
        showRuntimeError('An unexpected error occurred. Please refresh the page and try again.');
        
        // Prevent default browser error handling
        return true;
    });
    
    // Handle unhandled promise rejections
    window.addEventListener('unhandledrejection', function(e) {
        console.error('Unhandled Promise Rejection:', e.reason);
        showRuntimeError('A network or processing error occurred. Please check your connection and try again.');
    });
    
    // Function to display runtime errors to user
    function showRuntimeError(message) {
        // Remove existing error if any
        const existing = document.querySelector('.runtime-error-toast');
        if (existing) existing.remove();
        
        const errorToast = document.createElement('div');
        errorToast.className = 'runtime-error-toast fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-xl z-50 animate-fade-in max-w-md';
        errorToast.innerHTML = `
            <div class="flex items-start">
                <span class="material-icons-round mr-3">error</span>
                <div class="flex-1">
                    <h4 class="font-semibold mb-1">Error</h4>
                    <p class="text-sm">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                    <span class="material-icons-round text-lg">close</span>
                </button>
            </div>
        `;
        
        document.body.appendChild(errorToast);
        
        // Auto-remove after 8 seconds
        setTimeout(() => {
            if (errorToast.parentElement) {
                errorToast.style.opacity = '0';
                errorToast.style.transition = 'opacity 0.3s';
                setTimeout(() => errorToast.remove(), 300);
            }
        }, 8000);
    }
    
    // Global error tracking
    const formErrors = {
        section1: {},
        section2: {},
        section3: {}
    };
    
    // Console logging helper
    function logError(context, error, data = {}) {
        console.error(`[Form Validation Error - ${context}]:`, {
            error: error,
            data: data,
            timestamp: new Date().toISOString()
        });
    }
    
    // Real-time validation functions
    function validateField(fieldName, value, section) {
        try {
            const field = document.querySelector(`[name="${fieldName}"]`) || document.querySelector(`input[name="${fieldName}[]"]`);
            if (!field) {
                console.warn(`Field not found: ${fieldName}`);
                return true; // Skip validation if field doesn't exist
            }
            
            const errorKey = `${section}_${fieldName}`;
            
            // Remove previous errors
            removeFieldError(fieldName, section);
            
            let isValid = true;
            let errorMessage = '';
        
        // Validation rules
        switch(fieldName) {
            case 'title_status':
                if (!value || value === '') {
                    isValid = false;
                    errorMessage = 'Please select the title status (Yes/No) for your vehicle.';
                }
                break;
            case 'island':
                if (!value || value === '') {
                    isValid = false;
                    errorMessage = 'Please select the island location where your vehicle is located.';
                }
                break;
            case 'color':
                if (!value || value === '') {
                    isValid = false;
                    errorMessage = 'Please select the exterior color of your vehicle.';
                }
                break;
            case 'interior_color':
                if (!value || value === '') {
                    isValid = false;
                    errorMessage = 'Please select the interior color of your vehicle.';
                }
                break;
            case 'primary_damage':
                if (!value || value === '') {
                    isValid = false;
                    errorMessage = 'Please select the primary damage type for your vehicle.';
                }
                break;
            case 'keys_available':
                if (!value || value === '') {
                    isValid = false;
                    errorMessage = 'Please indicate if keys are available for your vehicle.';
                }
                break;
            case 'cover_photo':
                if (!value || (value.files && value.files.length === 0)) {
                    isValid = false;
                    errorMessage = 'Cover photo is required. Please upload a cover image.';
                } else if (value.files && value.files[0]) {
                    const file = value.files[0];
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    if (file.size > maxSize) {
                        isValid = false;
                        errorMessage = 'Cover photo size must not exceed 5MB. Please compress your image.';
                    }
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        isValid = false;
                        errorMessage = 'Cover photo must be in JPEG, PNG, JPG, GIF, or WEBP format.';
                    }
                }
                break;
            case 'photos':
                const photoInput = document.querySelector('input[name="photos[]"]');
                if (photoInput && photoInput.files) {
                    const additionalPhotos = photoInput.files.length;
                    const totalPhotos = additionalPhotos + (document.querySelector('input[name="cover_photo"]').files.length > 0 ? 1 : 0);
                    if (additionalPhotos < 5) {
                        isValid = false;
                        errorMessage = `You need to upload at least 5 additional photos (plus 1 cover photo = 6 total minimum). Currently you have ${additionalPhotos} additional photo(s).`;
                    } else if (totalPhotos > 11) {
                        isValid = false;
                        errorMessage = `Maximum 10 additional photos allowed (plus 1 cover photo = 11 total). You have uploaded ${totalPhotos} photos.`;
                    }
                    // Check file sizes
                    for (let i = 0; i < photoInput.files.length; i++) {
                        const file = photoInput.files[i];
                        const maxSize = 5 * 1024 * 1024; // 5MB
                        if (file.size > maxSize) {
                            isValid = false;
                            errorMessage = `Photo ${i + 1} exceeds 5MB size limit. Please compress your images.`;
                            break;
                        }
                    }
                }
                break;
            case 'auction_duration':
                if (!value || value === '') {
                    isValid = false;
                    errorMessage = 'Please select the auction duration (5, 7, 14, 21, or 28 days).';
                }
                break;
            case 'starting_price':
                if (value && value !== '') {
                    const numValue = parseFloat(value);
                    if (isNaN(numValue) || numValue <= 0) {
                        isValid = false;
                        errorMessage = 'Starting Bid must be greater than $0 if entered.';
                    }
                }
                break;
            case 'reserve_price':
                if (value && value !== '') {
                    const numValue = parseFloat(value);
                    const startingPrice = parseFloat(document.querySelector('input[name="starting_price"]')?.value || 0);
                    if (isNaN(numValue) || numValue < 0) {
                        isValid = false;
                        errorMessage = 'Reserve Price cannot be negative.';
                    } else if (startingPrice > 0 && numValue < startingPrice) {
                        isValid = false;
                        errorMessage = 'Reserve Price must be greater than or equal to Starting Bid.';
                    }
                }
                break;
            case 'payment_method':
                @if($isIndividualSeller ?? false)
                if (!value || value === '') {
                    isValid = false;
                    errorMessage = 'Payment method is required for Individual Sellers. Please select a payment method.';
                }
                @endif
                break;
        }
        
            // Display error or success
            if (!isValid) {
                showFieldError(fieldName, errorMessage, section);
                formErrors[section][fieldName] = errorMessage;
                logError('Field Validation', errorMessage, { fieldName, section, value });
            } else {
                showFieldSuccess(fieldName, section);
                delete formErrors[section][fieldName];
            }
            
            return isValid;
        } catch (error) {
            logError('validateField', error, { fieldName, section, value });
            showRuntimeError(`Validation error for ${fieldName}. Please check the field and try again.`);
            return false;
        }
    }
    
    function showFieldError(fieldName, message, section) {
        try {
            const field = document.querySelector(`[name="${fieldName}"]`) || document.querySelector(`input[name="${fieldName}[]"]`);
            if (!field) {
                console.warn(`Cannot show error for field: ${fieldName} - field not found`);
                return;
            }
        
        // Add error class
        field.classList.add('field-error');
        field.classList.remove('field-success');
        field.classList.add('shake');
        setTimeout(() => field.classList.remove('shake'), 500);
        
        // Remove existing error message
        const existingError = field.parentElement.querySelector('.error-message');
        if (existingError) existingError.remove();
        
        // Create error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.innerHTML = `
            <span class="material-icons-round">error</span>
            <span>${message}</span>
        `;
        
            // Insert after field
            const formGroup = field.closest('.form-group') || field.parentElement;
            if (formGroup) {
                formGroup.appendChild(errorDiv);
            } else {
                field.parentElement.appendChild(errorDiv);
            }
        } catch (error) {
            logError('showFieldError', error, { fieldName, message, section });
        }
    }
    
    function removeFieldError(fieldName, section) {
        const field = document.querySelector(`[name="${fieldName}"]`) || document.querySelector(`input[name="${fieldName}[]"]`);
        if (!field) return;
        
        field.classList.remove('field-error');
        const errorDiv = field.parentElement.querySelector('.error-message');
        if (errorDiv) errorDiv.remove();
    }
    
    function showFieldSuccess(fieldName, section) {
        const field = document.querySelector(`[name="${fieldName}"]`) || document.querySelector(`input[name="${fieldName}[]"]`);
        if (!field) return;
        
        field.classList.remove('field-error');
        field.classList.add('field-success');
        
        // Remove error message if exists
        const errorDiv = field.parentElement.querySelector('.error-message');
        if (errorDiv) errorDiv.remove();
    }
    
    // Validate entire section
    function validateSection(sectionNum) {
        try {
            const section = `section${sectionNum}`;
            let isValid = true;
            const errors = [];
        
        if (sectionNum === 1) {
            // Section 1: Vehicle Information
            const titleStatus = document.querySelector('select[name="title_status"]')?.value;
            const island = document.querySelector('select[name="island"]')?.value;
            const color = document.querySelector('select[name="color"]')?.value;
            const interiorColor = document.querySelector('select[name="interior_color"]')?.value;
            const primaryDamage = document.querySelector('select[name="primary_damage"]')?.value;
            const keysAvailable = document.querySelector('select[name="keys_available"]')?.value;
            const isSalvaged = document.querySelector('select[name="is_salvaged"]')?.value;
            
            if (!validateField('title_status', titleStatus, section)) isValid = false;
            if (!validateField('is_salvaged', isSalvaged, section)) isValid = false;
            if (!validateField('island', island, section)) isValid = false;
            if (!validateField('color', color, section)) isValid = false;
            if (!validateField('interior_color', interiorColor, section)) isValid = false;
            if (!validateField('primary_damage', primaryDamage, section)) isValid = false;
            if (!validateField('keys_available', keysAvailable, section)) isValid = false;
        } else if (sectionNum === 2) {
            // Section 2: Photos
            const coverPhoto = document.querySelector('input[name="cover_photo"]');
            const photos = document.querySelector('input[name="photos[]"]');
            
            if (!validateField('cover_photo', coverPhoto, section)) isValid = false;
            if (!validateField('photos', photos, section)) isValid = false;
        } else if (sectionNum === 3) {
            // Section 3: Auction Settings
            const auctionDuration = document.querySelector('input[name="auction_duration"]:checked')?.value;
            const startingPrice = document.querySelector('input[name="starting_price"]')?.value;
            const reservePrice = document.querySelector('input[name="reserve_price"]')?.value;
            const paymentMethod = document.querySelector('select[name="payment_method"]')?.value;
            
            if (!validateField('auction_duration', auctionDuration, section)) isValid = false;
            if (startingPrice && !validateField('starting_price', startingPrice, section)) isValid = false;
            if (reservePrice && !validateField('reserve_price', reservePrice, section)) isValid = false;
            @if($isIndividualSeller ?? false)
            if (!validateField('payment_method', paymentMethod, section)) isValid = false;
            @endif
            }
            
            return isValid;
        } catch (error) {
            logError('validateSection', error, { sectionNum });
            showRuntimeError(`Error validating section ${sectionNum}. Please check all fields and try again.`);
            return false;
        }
    }
    
    // No Title modal: show when user selects "No Title"
    document.getElementById('title_status_select')?.addEventListener('change', function() {
        if (this.value === 'no') {
            const modal = document.getElementById('no-title-modal');
            if (modal) { modal.style.display = 'flex'; modal.classList.remove('hidden'); }
        }
    });
    // Salvage notice: show when user selects "Yes" for salvaged
    document.getElementById('is_salvaged_select')?.addEventListener('change', function() {
        const notice = document.getElementById('salvage-notice');
        if (notice) notice.classList.toggle('hidden', this.value !== '1');
    });

    // Real-time validation on input change
    document.addEventListener('DOMContentLoaded', function() {
        try {
            console.log('Form validation initialized');
            
            // Section 1 fields
            const section1Fields = document.querySelectorAll('select[name="title_status"], select[name="island"], select[name="color"], select[name="interior_color"], select[name="primary_damage"], select[name="keys_available"]');
            if (section1Fields.length === 0) {
                console.warn('Section 1 fields not found');
            } else {
                section1Fields.forEach(field => {
                    field.addEventListener('change', function() {
                        try {
                            validateField(this.name, this.value, 'section1');
                        } catch (error) {
                            logError('Section 1 field change', error, { fieldName: this.name });
                        }
                    });
                });
            }
        
        // Section 2 fields
        const coverPhoto = document.querySelector('input[name="cover_photo"]');
        if (coverPhoto) {
            coverPhoto.addEventListener('change', function() {
                validateField('cover_photo', this, 'section2');
            });
        }
        
        const photosInput = document.querySelector('input[name="photos[]"]');
        if (photosInput) {
            photosInput.addEventListener('change', function() {
                validateField('photos', this, 'section2');
            });
        }
        
        // Section 3 fields
        document.querySelectorAll('input[name="auction_duration"]').forEach(field => {
            field.addEventListener('change', function() {
                validateField('auction_duration', this.value, 'section3');
            });
        });
        
        document.querySelectorAll('input[name="starting_price"], input[name="reserve_price"]').forEach(field => {
            field.addEventListener('blur', function() {
                validateField(this.name, this.value, 'section3');
            });
            field.addEventListener('input', function() {
                // Clear error on input
                if (this.classList.contains('field-error')) {
                    removeFieldError(this.name, 'section3');
                }
            });
        });
        
        @if($isIndividualSeller ?? false)
        const paymentMethod = document.querySelector('select[name="payment_method"]');
        if (paymentMethod) {
            paymentMethod.addEventListener('change', function() {
                validateField('payment_method', this.value, 'section3');
            });
        }
        @endif
        
            // Form submission validation
            const listingForm = document.getElementById('listingForm');
            if (!listingForm) {
                console.error('Listing form not found!');
                showRuntimeError('Form not found. Please refresh the page.');
                return;
            }
            
            listingForm.addEventListener('submit', function(e) {
                try {
                    e.preventDefault();
                    console.log('Form submission started');
                    
                    // Clear previous errors
                    document.querySelectorAll('.error-message').forEach(el => el.remove());
                    document.querySelectorAll('.field-error').forEach(el => el.classList.remove('field-error'));
                    
                    // Validate all sections
                    let isValid = true;
                    let firstErrorSection = null;
                    
                    console.log('Validating section 1...');
                    if (!validateSection(1)) {
                        isValid = false;
                        if (!firstErrorSection) firstErrorSection = 1;
                    }
                    
                    console.log('Validating section 2...');
                    if (!validateSection(2)) {
                        isValid = false;
                        if (!firstErrorSection) firstErrorSection = 2;
                    }
                    
                    console.log('Validating section 3...');
                    if (!validateSection(3)) {
                        isValid = false;
                        if (!firstErrorSection) firstErrorSection = 3;
                    }
                    
                    if (!isValid) {
                        console.warn('Form validation failed', { firstErrorSection, errors: formErrors });
                        // Show error summary
                        showErrorSummary();
                        
                        // Scroll to first error section
                        if (firstErrorSection) {
                            showSection(firstErrorSection);
                            setTimeout(() => {
                                const firstError = document.querySelector('.field-error');
                                if (firstError) {
                                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            }, 300);
                        }
                        
                        return false;
                    }
                    
                    console.log('Form validation passed, submitting...');
                    // If all valid, submit form
                    this.submit();
                } catch (error) {
                    logError('Form submission', error);
                    showRuntimeError('An error occurred while submitting the form. Please try again.');
                    return false;
                }
            });
        
            // Error summary function
            function showErrorSummary() {
                try {
                    // Remove existing summary
                    const existing = document.querySelector('.error-summary');
                    if (existing) existing.remove();
                    
                    // Count errors
                    const errorCount = Object.keys(formErrors.section1).length + 
                                     Object.keys(formErrors.section2).length + 
                                     Object.keys(formErrors.section3).length;
                    
                    if (errorCount === 0) return;
                    
                    // Create error summary
                    const summary = document.createElement('div');
                    summary.className = 'error-summary mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg animate-fade-in';
                    summary.innerHTML = `
                        <div class="flex items-start">
                            <span class="material-icons-round text-red-500 mr-3 mt-0.5">error</span>
                            <div class="flex-1">
                                <h3 class="text-red-800 font-semibold mb-2">Please fix ${errorCount} error(s) before submitting:</h3>
                                <ul class="list-disc list-inside text-red-700 space-y-1">
                                    ${Object.values(formErrors.section1).map(msg => `<li>${msg}</li>`).join('')}
                                    ${Object.values(formErrors.section2).map(msg => `<li>${msg}</li>`).join('')}
                                    ${Object.values(formErrors.section3).map(msg => `<li>${msg}</li>`).join('')}
                                </ul>
                            </div>
                        </div>
                    `;
                    
                    // Insert at top of form
                    const form = document.getElementById('listingForm');
                    if (form && form.parentElement) {
                        form.parentElement.insertBefore(summary, form);
                        // Scroll to summary
                        summary.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                } catch (error) {
                    logError('showErrorSummary', error);
                }
            }
        
            @if(session('error_section'))
                const errorSection = '{{ session('error_section') }}';
                if (errorSection) {
                    try {
                        const sectionNum = parseInt(errorSection.replace('section', ''));
                        showSection(sectionNum);
                        
                        setTimeout(() => {
                            const section = document.getElementById(errorSection);
                            if (section) {
                                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }
                        }, 300);
                    } catch (error) {
                        logError('Error section handling', error, { errorSection });
                    }
                }
            @endif
            
            console.log('Form validation setup complete');
        } catch (error) {
            logError('DOMContentLoaded', error);
            showRuntimeError('Error initializing form validation. Please refresh the page.');
        }
    });

    // Ensure section buttons work even if inline onclick is blocked
    document.addEventListener('DOMContentLoaded', function() {
        var btnPhotos = document.getElementById('btn-continue-to-photos');
        if (btnPhotos) {
            btnPhotos.addEventListener('click', function(e) {
                e.preventDefault();
                if (typeof showSection === 'function') showSection(2);
            });
        }
    });

    // Auto-scroll to error section on page load
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('error_section'))
            const errorSection = '{{ session('error_section') }}';
            if (errorSection) {
                // Show the error section
                const sectionNum = parseInt(errorSection.replace('section', ''));
                showSection(sectionNum);
                
                // Scroll to error section after a short delay
                setTimeout(() => {
                    const section = document.getElementById(errorSection);
                    if (section) {
                        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 300);
            }
        @endif
    });

    // VIN/HIN type selector: update label and placeholder
    document.querySelectorAll('input[name="vin_hin_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const label = document.getElementById('vin_hin_label');
            const input = document.getElementById('vin_hin');
            if (this.value === 'hin') {
                if (label) label.textContent = 'HIN Number';
                if (input) input.placeholder = 'Enter HIN (12 characters)';
                if (input) input.maxLength = 12;
            } else {
                if (label) label.textContent = 'VIN Number';
                if (input) input.placeholder = 'Enter VIN (17 characters)';
                if (input) input.maxLength = 17;
            }
        });
    });

    // VIN/HIN attempt count: lock manual/condition fields until 3 attempts
    var vinAttemptCount = 0;
    var VIN_UNLOCK_AFTER = 3;
    function setVinLockedFieldsDisabled(disabled) {
        var section = document.getElementById('vinLockedSection');
        if (!section) return;
        section.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(function(el) {
            el.disabled = disabled;
        });
        if (disabled) section.classList.add('vin-section-locked');
        else section.classList.remove('vin-section-locked');
        var lockedMsg = document.getElementById('vinLockedMessage');
        if (lockedMsg) lockedMsg.style.display = disabled ? 'block' : 'none';
    }
    function onVinAttemptDone() {
        vinAttemptCount++;
        if (vinAttemptCount >= VIN_UNLOCK_AFTER) {
            setVinLockedFieldsDisabled(false);
        } else {
            var msg = document.getElementById('vinLockedMessage');
            if (msg) {
                if (vinAttemptCount >= VIN_UNLOCK_AFTER - 1) {
                    msg.innerHTML = '<i class="fas fa-info-circle mr-2"></i>Please enter item details manually.';
                    msg.className = 'mb-3 text-sm text-blue-700 bg-blue-50 border border-blue-200 rounded-lg px-4 py-2';
                } else {
                    msg.innerHTML = '<i class="fas fa-times-circle mr-2"></i>Unsuccessful. Please try again.';
                    msg.className = 'mb-3 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-2';
                }
            }
        }
    }
    setVinLockedFieldsDisabled(true);

    // VIN/HIN Decoder
    var searchVinBtn = document.getElementById('searchVinBtn');
    if (searchVinBtn) {
    searchVinBtn.addEventListener('click', function() {
        const vinHin = document.getElementById('vin_hin').value.trim().toUpperCase();
        const readerType = document.querySelector('input[name="vin_hin_type"]:checked')?.value || 'vin';
        if (!vinHin) {
            alert(readerType === 'hin' ? 'Please enter a HIN' : 'Please enter a VIN');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Searching...';

        fetch('{{ route("seller.listings.decode-vin-hin") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ vin_hin: vinHin, reader_type: readerType })
        })
        .then(response => response.json())
        .then(data => {
            const messageDiv = document.getElementById('vinDecoderMessage');
            if (data.success) {
                Object.keys(data.data).forEach(key => {
                    const field = document.getElementById('decoded_' + key);
                    if (field) field.value = data.data[key];
                });
                document.getElementById('decodedFields').style.display = 'grid';
                document.getElementById('manualFields').style.display = 'none';
                messageDiv.innerHTML = '<span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>VIN/HIN decoded successfully</span>';
                setVinLockedFieldsDisabled(false);
                var decodedBlock = document.getElementById('decodedFields');
                if (decodedBlock) {
                    decodedBlock.querySelectorAll('input').forEach(function(input) {
                        input.removeAttribute('readonly');
                        input.classList.remove('bg-gray-50');
                    });
                }
            } else {
                document.getElementById('decodedFields').style.display = 'none';
                document.getElementById('manualFields').style.display = 'grid';
                messageDiv.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>' + data.message + '</span>';
                onVinAttemptDone();
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search mr-2"></i>Search';
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('vinDecoderMessage').innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>Error decoding VIN/HIN. Please enter details manually.</span>';
            onVinAttemptDone();
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search mr-2"></i>Search';
        });
    });
    }

    // Photo preview and validation (custom file upload UI) + delete photo
    var coverPhotoInput = document.querySelector('input[name="cover_photo"]');
    var coverPhotoUploadBox = document.getElementById('coverPhotoUploadBox');
    var coverPhotoStatusEl = document.getElementById('coverPhotoStatus');
    var coverPhotoPreviewEl = document.getElementById('coverPhotoPreview');
    if (coverPhotoInput) {
    coverPhotoInput.addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (coverPhotoUploadBox) coverPhotoUploadBox.classList.toggle('has-file', !!file);
        if (coverPhotoStatusEl) coverPhotoStatusEl.textContent = file ? file.name : 'No file chosen';
        if (file) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                coverPhotoPreviewEl.innerHTML =
                    '<div class="photo-preview-item"><img src="' + ev.target.result + '" alt="Cover Photo"></div>' +
                    '<div class="cover-photo-remove-wrap"><button type="button" class="cover-photo-remove-btn" id="coverPhotoRemoveBtn"><i class="fas fa-trash-alt"></i> Remove cover photo</button></div>';
                var removeBtn = document.getElementById('coverPhotoRemoveBtn');
                if (removeBtn) removeBtn.addEventListener('click', function() {
                    coverPhotoInput.value = '';
                    if (coverPhotoUploadBox) coverPhotoUploadBox.classList.remove('has-file');
                    if (coverPhotoStatusEl) coverPhotoStatusEl.textContent = 'No file chosen';
                    coverPhotoPreviewEl.innerHTML = '';
                });
            };
            reader.readAsDataURL(file);
        } else {
            coverPhotoPreviewEl.innerHTML = '';
        }
    });
    }

    var additionalPhotosFiles = [];
    var photosInputEl = document.querySelector('input[name="photos[]"]');
    var photosUploadBox = document.getElementById('photosUploadBox');
    var photoPreviewEl = document.getElementById('photoPreview');
    var photoCountEl = document.getElementById('photoCount');
    var photoWarningEl = document.getElementById('photoWarning');

    function setAdditionalPhotosInput(files) {
        var dt = new DataTransfer();
        files.forEach(function(f) { dt.items.add(f); });
        if (photosInputEl) photosInputEl.files = dt.files;
    }
    function renderAdditionalPhotosPreviews() {
        var count = additionalPhotosFiles.length;
        if (photoCountEl) photoCountEl.textContent = count + ' photo' + (count !== 1 ? 's' : '') + ' selected';
        if (photosUploadBox) photosUploadBox.classList.toggle('has-file', count > 0);
        if (photoWarningEl) photoWarningEl.style.display = (count > 0 && count < 7) ? 'block' : 'none';
        if (!photoPreviewEl) return;
        photoPreviewEl.innerHTML = '';
        additionalPhotosFiles.forEach(function(file, index) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                var div = document.createElement('div');
                div.className = 'photo-preview-item';
                var img = document.createElement('img');
                img.src = ev.target.result;
                img.alt = 'Preview';
                div.appendChild(img);
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'photo-preview-remove';
                btn.setAttribute('aria-label', 'Remove photo');
                btn.innerHTML = '&times;';
                btn.addEventListener('click', function() {
                    additionalPhotosFiles.splice(index, 1);
                    setAdditionalPhotosInput(additionalPhotosFiles);
                    renderAdditionalPhotosPreviews();
                });
                div.appendChild(btn);
                photoPreviewEl.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    if (photosInputEl) {
    photosInputEl.addEventListener('change', function(e) {
        var files = e.target.files;
        if (files.length > 10) {
            alert('Maximum 10 additional photos allowed. Please select fewer photos.');
            e.target.value = '';
            return;
        }
        additionalPhotosFiles = Array.from(files);
        setAdditionalPhotosInput(additionalPhotosFiles);
        renderAdditionalPhotosPreviews();
    });
    }

    // Auto-uppercase all text inputs
    document.querySelectorAll('input[type="text"], textarea').forEach(input => {
        input.addEventListener('input', function() {
            if (this.name !== 'vin' && !this.readOnly) {
                this.value = this.value.toUpperCase();
            }
        });
    });
    
    // Radio button visual feedback
    document.querySelectorAll('.duration-option').forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                document.querySelectorAll('input[name="auction_duration"]').forEach(r => r.checked = false);
                radio.checked = true;
                document.querySelectorAll('.duration-option').forEach(opt => {
                    opt.classList.remove('border-blue-600', 'bg-blue-50');
                });
                this.classList.add('border-blue-600', 'bg-blue-50');
            }
        });
    });

    // Year: numbers only, max 4 digits, cannot exceed current year
    document.querySelectorAll('.js-year-input').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
            var currentYear = new Date().getFullYear();
            if (this.value.length === 4 && parseInt(this.value) > currentYear) {
                this.value = String(currentYear);
            }
        });
        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key)) e.preventDefault();
        });
    });

    // Make/Model: letters only
    document.querySelectorAll('.js-letters-only').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^A-Za-z\s\-]/g, '').toUpperCase();
        });
        input.addEventListener('keypress', function(e) {
            if (!/[A-Za-z\s\-]/.test(e.key)) e.preventDefault();
        });
    });

    // Video upload preview + duration validation
    var videoInput = document.getElementById('video_input');
    if (videoInput) {
        videoInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            var statusEl = document.getElementById('videoStatus');
            var previewEl = document.getElementById('videoPreview');
            var box = document.getElementById('videoUploadBox');
            if (previewEl) previewEl.innerHTML = '';
            if (!file) {
                if (statusEl) statusEl.textContent = 'No video chosen';
                if (box) box.classList.remove('has-file');
                return;
            }
            if (statusEl) statusEl.textContent = file.name + ' (' + (file.size / (1024*1024)).toFixed(1) + ' MB)';
            if (box) box.classList.add('has-file');
            var video = document.createElement('video');
            video.preload = 'metadata';
            video.onloadedmetadata = function() {
                window.URL.revokeObjectURL(video.src);
                var dur = video.duration;
                if (dur < 20 || dur > 60) {
                    alert('Video must be between 20 seconds and 1 minute. Your video is ' + Math.round(dur) + ' seconds.');
                    videoInput.value = '';
                    if (statusEl) statusEl.textContent = 'No video chosen';
                    if (box) box.classList.remove('has-file');
                }
            };
            video.src = URL.createObjectURL(file);
        });
    }
</script>
@endsection

