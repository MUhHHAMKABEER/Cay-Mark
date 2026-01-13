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
                    <h1 class="text-2xl md:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#063466] via-[#1e3a8a] to-[#2563eb] mb-1">
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
            <div id="section2" class="form-section" style="display: none;">
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
                        <p class="text-xs text-gray-600 mb-2">Required: 5-10 photos. Recommended: Left side, Right side, Rear, Interior (2), Dashboard/Odometer, VIN/HIN photo, Engine bay</p>
                        <input type="file" name="photos[]" multiple required accept="image/*" class="form-input" min="5" max="10">
                        <p class="text-xs text-gray-500 mt-1" id="photoCount">0 photos selected</p>
                        <div id="photoPreview" class="photo-preview-grid mt-2"></div>
                        <p id="photoWarning" class="text-xs text-amber-600 mt-1" style="display: none;">
                            <i class="fas fa-exclamation-triangle mr-1"></i> We recommend at least 7 photos for best results.
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
            <div id="section3" class="form-section" style="display: none;">
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

<script>
    function showSection(sectionNum) {
        // Hide all sections
        document.getElementById('section1').style.display = 'none';
        document.getElementById('section2').style.display = 'none';
        document.getElementById('section3').style.display = 'none';
        
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
        
        if (count < 5) {
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

