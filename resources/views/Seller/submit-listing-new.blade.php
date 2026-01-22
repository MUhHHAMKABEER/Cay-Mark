@extends('layouts.Seller')

@section('content')
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
    }
    
    .form-section:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
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

<div class="w-full bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 py-4 px-4" style="min-height: calc(100vh - 100px);">
    <div class="w-full max-w-full mx-auto">
        <!-- Header -->
        <div class="text-center mb-4 animate-fade-in">
            <div class="inline-block mb-2">
                <div class="bg-white rounded-xl px-6 py-4 shadow-lg border border-gray-100">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">
                        Create New Listing
                    </h1>
                    <p class="text-gray-600 text-sm md:text-base">List your vehicle or vessel in just a few simple steps</p>
                </div>
            </div>
        </div>

        <!-- Step Indicator -->
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
        </div>
        
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

        <form id="listingForm" action="{{ route('seller.listings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- SECTION 1: VEHICLE INFORMATION -->
            <div id="section1" class="form-section animate-slide-in">
                <div class="section-header">
                    <div class="section-icon">1</div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Vehicle Information</h2>
                        <p class="text-xs text-gray-600">Enter your vehicle or vessel details</p>
                    </div>
                </div>
                
                <!-- VIN/HIN Input -->
                <div class="mb-4">
                    <label class="form-label">VIN / HIN Number</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" 
                               id="vin_hin" 
                               name="vin" 
                               class="form-input flex-1 uppercase" 
                               placeholder="Enter VIN or HIN (17 characters)"
                               maxlength="17">
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

                <!-- Manual Entry Fields -->
                <div id="manualFields" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="form-label">MAKE</label>
                        <input type="text" name="make" class="form-input uppercase" style="text-transform: uppercase;">
                    </div>
                    <div>
                        <label class="form-label">MODEL</label>
                        <input type="text" name="model" class="form-input uppercase" style="text-transform: uppercase;">
                    </div>
                    <div>
                        <label class="form-label">YEAR</label>
                        <input type="text" name="year" class="form-input uppercase" style="text-transform: uppercase;">
                    </div>
                    <div>
                        <label class="form-label">TRIM</label>
                        <input type="text" name="trim" class="form-input uppercase" style="text-transform: uppercase;">
                    </div>
                </div>

                <!-- Required Condition Fields -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="form-label">TITLE STATUS <span class="text-red-500">*</span></label>
                        <select name="title_status" required class="form-input">
                            <option value="">Select Title Status</option>
                            <option value="yes">YES (Clean Title)</option>
                            <option value="no">NO (Salvage Title)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">ISLAND LOCATION <span class="text-red-500">*</span></label>
                        <select name="island" required class="form-input">
                            <option value="">Select Island</option>
                            <option value="GRAND_CAYMAN">Grand Cayman</option>
                            <option value="CAYMAN_BRAC">Cayman Brac</option>
                            <option value="LITTLE_CAYMAN">Little Cayman</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">EXTERIOR COLOR <span class="text-red-500">*</span></label>
                        <select name="color" required class="form-input">
                            <option value="">Select Color</option>
                            <option value="BLACK">Black</option>
                            <option value="WHITE">White</option>
                            <option value="SILVER">Silver</option>
                            <option value="GRAY">Gray</option>
                            <option value="RED">Red</option>
                            <option value="BLUE">Blue</option>
                            <option value="GREEN">Green</option>
                            <option value="BROWN">Brown</option>
                            <option value="YELLOW">Yellow</option>
                            <option value="ORANGE">Orange</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">INTERIOR COLOR <span class="text-red-500">*</span></label>
                        <select name="interior_color" required class="form-input">
                            <option value="">Select Color</option>
                            <option value="BLACK">Black</option>
                            <option value="GRAY">Gray</option>
                            <option value="BEIGE">Beige</option>
                            <option value="BROWN">Brown</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">PRIMARY DAMAGE <span class="text-red-500">*</span></label>
                        <select name="primary_damage" required class="form-input">
                            <option value="">Select Damage Type</option>
                            <option value="NONE">None</option>
                            <option value="FRONT_END">Front End</option>
                            <option value="REAR_END">Rear End</option>
                            <option value="SIDE">Side</option>
                            <option value="FLOOD">Flood</option>
                            <option value="FIRE">Fire</option>
                            <option value="VANDALISM">Vandalism</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">HAS KEYS <span class="text-red-500">*</span></label>
                        <select name="keys_available" required class="form-input">
                            <option value="">Select</option>
                            <option value="yes">YES</option>
                            <option value="no">NO</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">SECONDARY DAMAGE</label>
                        <select name="secondary_damage" class="form-input">
                            <option value="">Select (Optional)</option>
                            <option value="NONE">None</option>
                            <option value="MINOR">Minor</option>
                            <option value="MODERATE">Moderate</option>
                            <option value="SEVERE">Severe</option>
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-3 lg:col-span-4">
                        <label class="form-label">ADDITIONAL NOTES</label>
                        <textarea name="additional_notes" rows="2" class="form-input uppercase" style="text-transform: uppercase;" placeholder="Enter any additional notes..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="showSection(2)" class="btn-primary">
                        Continue to Photos <i class="fas fa-arrow-right ml-2"></i>
                </button>
                </div>
            </div>

            <!-- SECTION 2: PHOTOS -->
            <div id="section2" class="form-section" style="display: @if(session('error_section') === 'section2') block; border: 2px solid #ef4444; border-radius: 12px; @else none; @endif">
                <div class="section-header">
                    <div class="section-icon">2</div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Upload Photos</h2>
                        <p class="text-xs text-gray-600">Add high-quality photos of your vehicle</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label">COVER PHOTO <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-600 mb-2">Front view of vehicle or vessel (Main photo)</p>
                        <input type="file" name="cover_photo" required accept="image/*" class="form-input">
                        <div id="coverPhotoPreview" class="mt-2"></div>
                    </div>

                    <div>
                        <label class="form-label">ADDITIONAL PHOTOS <span class="text-red-500">*</span></label>
                        <p class="text-xs text-gray-600 mb-2">Required: At least 5 photos. Maximum: 10 photos. Recommended: Left side, Right side, Rear, Interior (2), Dashboard/Odometer, VIN/HIN photo, Engine bay</p>
                        <input type="file" name="photos[]" multiple required accept="image/*" class="form-input" min="5" max="10">
                        <p class="text-xs text-gray-500 mt-1" id="photoCount">0 photos selected</p>
                        <div id="photoPreview" class="photo-preview-grid mt-2"></div>
                        <p id="photoWarning" class="text-xs text-amber-600 mt-1" style="display: none;">
                            <i class="fas fa-exclamation-triangle mr-1"></i> WE RECOMMEND AT LEAST 7 PHOTOS FOR BEST RESULTS.
                        </p>
                    </div>
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
                <div class="mb-4">
                    <label class="form-label">AUCTION DURATION <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-5 gap-3">
                        <label class="duration-option">
                            <input type="radio" name="auction_duration" value="5" required class="hidden">
                            <div class="font-semibold text-lg">5</div>
                            <div class="text-sm text-gray-600">Days</div>
                        </label>
                        <label class="duration-option">
                            <input type="radio" name="auction_duration" value="7" required class="hidden">
                            <div class="font-semibold text-lg">7</div>
                            <div class="text-sm text-gray-600">Days</div>
                        </label>
                        <label class="duration-option">
                            <input type="radio" name="auction_duration" value="14" required class="hidden">
                            <div class="font-semibold text-lg">14</div>
                            <div class="text-sm text-gray-600">Days</div>
                        </label>
                        <label class="duration-option">
                            <input type="radio" name="auction_duration" value="21" required class="hidden">
                            <div class="font-semibold text-lg">21</div>
                            <div class="text-sm text-gray-600">Days</div>
                        </label>
                        <label class="duration-option">
                            <input type="radio" name="auction_duration" value="28" required class="hidden">
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
                            <input type="number" name="starting_price" step="0.01" min="0" class="form-input" placeholder="$0.00">
                            <p class="text-xs text-gray-500 mt-1">Must be > $0 if entered</p>
                        </div>
                        <div>
                            <label class="form-label">RESERVE PRICE</label>
                            <input type="number" name="reserve_price" step="0.01" min="0" class="form-input" placeholder="$0.00">
                            <p class="text-xs text-gray-500 mt-1">Must be ≥ Starting Bid</p>
                        </div>
                        <div>
                            <label class="form-label">BUY NOW PRICE</label>
                            <input type="number" name="buy_now_price" step="0.01" min="0" class="form-input" placeholder="$0.00">
                        </div>
                    </div>
                    <div class="info-box mt-3">
                        <p class="text-sm text-gray-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            If no pricing is entered, auction runs using default system pricing with no reserve.
                        </p>
                    </div>
                </div>

                <!-- Payment (Individual Sellers only) -->
                @if($user->activeSubscription?->package?->price == 25.00)
                <div class="mb-4 warning-box">
                    <h3 class="text-base font-semibold mb-2 text-amber-900">
                        <i class="fas fa-exclamation-circle mr-2"></i>Payment Required
                    </h3>
                    <p class="text-xs mb-3 text-amber-800">Individual Sellers must pay a $25 listing fee before submission.</p>
                    <div>
                        <label class="form-label">PAYMENT METHOD <span class="text-red-500">*</span></label>
                        <select name="payment_method" required class="form-input">
                            <option value="">Select Payment Method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="stored_payment">Stored Payment Method</option>
                        </select>
                    </div>
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
                    <button type="button" onclick="showSection(2)" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </button>
                    <button type="submit" class="btn-success">
                        <i class="fas fa-check-circle mr-2"></i> Complete Submission
                    </button>
                </div>
            </div>
        </form>
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
                    if (additionalPhotos < 1) {
                        isValid = false;
                        errorMessage = `You need to upload at least 1 additional photo (plus 1 cover photo = 2 total minimum). Currently you have ${additionalPhotos} additional photo(s).`;
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
            
            if (!validateField('title_status', titleStatus, section)) isValid = false;
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

    function showSection(sectionNum) {
        // Hide all sections
        document.getElementById('section1').style.display = 'none';
        document.getElementById('section2').style.display = 'none';
        document.getElementById('section3').style.display = 'none';
        
        // Remove error highlighting
        document.getElementById('section1').style.border = '';
        document.getElementById('section2').style.border = '';
        document.getElementById('section3').style.border = '';
        
        // Show selected section with animation
        const section = document.getElementById('section' + sectionNum);
        section.style.display = 'block';
        section.classList.add('animate-slide-in');
        
        // Update step indicators
        for (let i = 1; i <= 3; i++) {
            const indicator = document.getElementById('step-indicator-' + i);
            if (i < sectionNum) {
                indicator.classList.remove('active');
                indicator.classList.add('completed');
            } else if (i === sectionNum) {
                indicator.classList.remove('completed');
                indicator.classList.add('active');
            } else {
                indicator.classList.remove('active', 'completed');
            }
        }
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

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

    // VIN/HIN Decoder
    document.getElementById('searchVinBtn').addEventListener('click', function() {
        const vinHin = document.getElementById('vin_hin').value.trim().toUpperCase();
        if (!vinHin) {
            alert('Please enter a VIN or HIN');
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
            body: JSON.stringify({ vin_hin: vinHin })
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
            } else {
                document.getElementById('decodedFields').style.display = 'none';
                document.getElementById('manualFields').style.display = 'grid';
                messageDiv.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>' + data.message + '</span>';
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search mr-2"></i>Search';
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('vinDecoderMessage').innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>Error decoding VIN/HIN. Please enter details manually.</span>';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-search mr-2"></i>Search';
        });
    });

    // Photo preview and validation
    document.querySelector('input[name="cover_photo"]').addEventListener('change', function(e) {
        if (e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('coverPhotoPreview').innerHTML = 
                    '<div class="photo-preview-item"><img src="' + e.target.result + '" alt="Cover Photo"></div>';
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    document.querySelector('input[name="photos[]"]').addEventListener('change', function(e) {
        const files = e.target.files;
        const count = files.length;
        document.getElementById('photoCount').textContent = count + ' photo' + (count !== 1 ? 's' : '') + ' selected';
        
        // Show recommendation warning if less than 5 photos (but only 1 is required)
        if (count > 0 && count < 5) {
            document.getElementById('photoWarning').style.display = 'block';
        } else {
            document.getElementById('photoWarning').style.display = 'none';
        }

        if (count > 10) {
            alert('Maximum 10 additional photos allowed. Please select fewer photos.');
            e.target.value = '';
            return;
        }

        // Preview images
        const preview = document.getElementById('photoPreview');
        preview.innerHTML = '';
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'photo-preview-item';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Preview';
                div.appendChild(img);
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

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
                // Remove checked from all
                document.querySelectorAll('input[name="auction_duration"]').forEach(r => r.checked = false);
                // Check this one
                radio.checked = true;
                // Update visual state
                document.querySelectorAll('.duration-option').forEach(opt => {
                    opt.classList.remove('border-blue-600', 'bg-blue-50');
                });
                this.classList.add('border-blue-600', 'bg-blue-50');
            }
        });
    });
</script>
@endsection

