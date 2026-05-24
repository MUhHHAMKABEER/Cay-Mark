@extends('layouts.dashboard')

@section('title')
{{ !empty($listing ?? null) ? 'Edit Listing - CayMark' : 'Create Listing - CayMark' }}
@endsection

@section('content')
@php
    $listing = $listing ?? null;
    $isEdit = !empty($listing);
    $missingRequirements = $missingRequirements ?? [];
@endphp

{{-- Seller block message: centered modal when profile is incomplete --}}
@if(!$isEdit && count($missingRequirements) > 0)
<!-- Modal Backdrop -->
<div id="profile-block-modal" style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.55);backdrop-filter:blur(2px);">
    <!-- Modal Card -->
    <div style="background:#fff;border-radius:18px;box-shadow:0 24px 60px rgba(0,0,0,0.18);max-width:480px;width:calc(100% - 2rem);padding:2rem 2rem 1.75rem;position:relative;animation:pbm-in 0.25s ease;">
        <!-- Icon + Title -->
        <div style="display:flex;align-items:flex-start;gap:0.9rem;margin-bottom:1rem;">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:50%;background:#fff7ed;flex-shrink:0;">
                <span class="material-icons" style="color:#ea580c;font-size:1.5rem;">warning</span>
            </span>
            <div>
                <p style="font-weight:800;color:#ea580c;font-size:1.05rem;margin:0 0 0.2rem;line-height:1.3;">Profile Incomplete</p>
                <p style="font-weight:600;color:#7c3aed;font-size:0.8rem;margin:0;letter-spacing:0.01em;">Listing Submission Blocked</p>
            </div>
        </div>

        <!-- Divider -->
        <div style="height:1px;background:#f1f5f9;margin-bottom:1rem;"></div>

        <!-- Body text -->
        <p style="font-size:0.875rem;color:#64748b;margin:0 0 0.85rem;line-height:1.6;">
            Please complete the following in your profile before submitting a listing:
        </p>

        <!-- Requirements list -->
        <ul style="margin:0 0 1.25rem 0;padding:0;list-style:none;display:flex;flex-direction:column;gap:0.55rem;">
            @foreach($missingRequirements as $req)
            <li style="display:flex;align-items:center;gap:0.55rem;font-size:0.875rem;font-weight:500;color:#1e293b;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:0.5rem 0.75rem;">
                <span class="material-icons" style="font-size:1rem;color:#dc2626;flex-shrink:0;">cancel</span>
                {{ $req }}
            </li>
            @endforeach
        </ul>

        <!-- Actions -->
        <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
            <a href="{{ route('seller.account') }}"
               style="flex:1;min-width:160px;display:inline-flex;align-items:center;justify-content:center;gap:0.4rem;background:#ea580c;color:#fff;font-size:0.875rem;font-weight:700;padding:0.65rem 1.25rem;border-radius:10px;text-decoration:none;transition:background 0.15s;">
                <span class="material-icons" style="font-size:1rem;">manage_accounts</span>
                Go to Account Settings
            </a>
            <a href="{{ route('seller.dashboard') }}"
               style="display:inline-flex;align-items:center;justify-content:center;gap:0.4rem;background:#f1f5f9;color:#475569;font-size:0.875rem;font-weight:600;padding:0.65rem 1.1rem;border-radius:10px;text-decoration:none;">
                <span class="material-icons" style="font-size:1rem;">arrow_back</span>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>

<style>
@keyframes pbm-in {
    from { opacity:0; transform:scale(0.93) translateY(16px); }
    to   { opacity:1; transform:scale(1) translateY(0); }
}
</style>
@endif

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
    .photo-preview-add-tile {
        position: relative;
        aspect-ratio: 1;
        border-radius: 10px;
        border: 2px dashed #10b981;
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        color: #047857;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-align: center;
        padding: 0.5rem;
        transition: border-color 0.2s, background 0.2s, color 0.2s;
    }
    .photo-preview-add-tile:hover {
        border-color: #059669;
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }
    .photo-preview-add-tile .fa-plus {
        font-size: 1.35rem;
        opacity: 0.9;
    }
    .photo-preview-add-tile .add-hint {
        font-size: 0.65rem;
        font-weight: 600;
        opacity: 0.85;
        line-height: 1.2;
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
            <div class="mb-4 hidden lg:block" id="listing-progress-bar">
                <x-ui.progress-steps
                    :step="1"
                    :total="3"
                    :labels="['Vehicle Information', 'Condition + Media', 'Auction + Payment']"
                />
            </div>
            <div class="step-indicator animate-fade-in">
                <div class="step-item active" id="step-indicator-1">
                    <div class="step-number">1</div>
                    <span>Vehicle Information</span>
                </div>
                <div class="step-item" id="step-indicator-2">
                    <div class="step-number">2</div>
                    <span>Condition + Media</span>
                </div>
                <div class="step-item" id="step-indicator-3">
                    <div class="step-number">3</div>
                    <span>Auction + Payment</span>
                </div>
            </div>
        </aside>

        <div class="create-listing-main w-full min-w-0">
        @if($isEdit && ($listing->status ?? null) === 'rejected')
            @php
                $rejReason = $listing->rejection_reason ?? null;
                $rejNotes = $listing->rejection_notes ?? null;
                $hoursLeft = method_exists($listing, 'getEditHoursRemaining') ? $listing->getEditHoursRemaining() : null;
            @endphp
            <div id="rejection-reason-banner" class="mb-4 p-4 bg-amber-50 border-l-4 border-amber-500 rounded-lg animate-fade-in">
                <div class="flex items-start gap-3">
                    <span class="material-icons-round text-amber-600 mt-0.5">report_problem</span>
                    <div class="flex-1">
                        <h3 class="text-amber-900 font-semibold mb-1">This submission was rejected</h3>
                        @if($rejReason)
                            <p class="text-sm text-amber-900"><span class="font-medium">Reason for Rejection:</span> {{ $rejReason }}</p>
                        @endif
                        @if($rejNotes)
                            <p class="text-sm text-amber-900 mt-1"><span class="font-medium">Notes:</span> {{ $rejNotes }}</p>
                        @endif
                        <p class="text-sm text-amber-800 mt-2">Please make the necessary adjustments before resubmitting.</p>
                        @if(is_int($hoursLeft) && $hoursLeft > 0)
                            <p class="text-xs text-amber-700 mt-1">Editing closes in approximately {{ $hoursLeft }} hour{{ $hoursLeft === 1 ? '' : 's' }}.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Modal popup on first arrival --}}
            <div id="rejection-reason-modal" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" style="display:flex;">
                <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Reason for Rejection</h3>
                    <p class="text-gray-700 text-sm mb-2">{{ $rejReason ?: 'Not specified.' }}</p>
                    @if($rejNotes)
                        <p class="text-gray-700 text-sm mb-4"><span class="font-semibold">Notes:</span> {{ $rejNotes }}</p>
                    @endif
                    <p class="text-gray-700 text-sm mb-4">Please make the necessary adjustments before submitting.</p>
                    <button type="button" onclick="document.getElementById('rejection-reason-modal').style.display='none';" class="w-full py-2 bg-blue-600 text-white rounded-lg font-semibold">I understand</button>
                </div>
            </div>
        @endif

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

        <form id="listingForm" data-cm-validate="off" action="{{ $isEdit ? route('seller.listings.update', $listing) : route('seller.listings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($isEdit) @method('PUT') @endif

            @php
                $maxYear = $maxYear ?? ((int) date('Y') + 1);
                $sellerPackage = $user->activeSubscription?->package ?? null;
                $isIndividualSeller = $isIndividualSeller ?? ($sellerPackage && (float) ($sellerPackage->price ?? 0) === 25.00);
            @endphp
            @include('Seller.partials.submit-listing.step1-vehicle')
            @include('Seller.partials.submit-listing.step2-condition-media')
            @include('Seller.partials.submit-listing.step3-auction-payment')
            @include('Seller.partials.submit-listing.condition-modals')

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

@include('Seller.partials.submit-listing.scripts')

@endsection

