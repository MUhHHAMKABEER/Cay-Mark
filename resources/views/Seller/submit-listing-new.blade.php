@extends('layouts.Seller')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-center">SUBMIT VEHICLE LISTING</h1>
        
        <form id="listingForm" action="{{ route('seller.listings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- SECTION 1: VEHICLE INFORMATION -->
            <div id="section1" class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-2xl font-bold mb-4 border-b pb-2">SECTION 1 — VEHICLE INFORMATION</h2>
                
                <!-- VIN/HIN Input -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">ENTER VIN / HIN</label>
                    <div class="flex gap-2">
                        <input type="text" 
                               id="vin_hin" 
                               name="vin" 
                               class="flex-1 border rounded px-4 py-2 uppercase" 
                               placeholder="Enter VIN or HIN"
                               maxlength="17">
                        <button type="button" 
                                id="searchVinBtn" 
                                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            SEARCH
                        </button>
                    </div>
                    <div id="vinDecoderMessage" class="mt-2 text-sm"></div>
                </div>

                <!-- Auto-populated fields (hidden until decoded) -->
                <div id="decodedFields" class="grid grid-cols-2 gap-4 mb-6" style="display: none;">
                    <div>
                        <label class="block text-sm font-semibold mb-1">MAKE</label>
                        <input type="text" name="make" id="decoded_make" class="w-full border rounded px-3 py-2 uppercase" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">MODEL</label>
                        <input type="text" name="model" id="decoded_model" class="w-full border rounded px-3 py-2 uppercase" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">YEAR</label>
                        <input type="text" name="year" id="decoded_year" class="w-full border rounded px-3 py-2 uppercase" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">TRIM</label>
                        <input type="text" name="trim" id="decoded_trim" class="w-full border rounded px-3 py-2 uppercase" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">ENGINE SIZE</label>
                        <input type="text" name="engine_size" id="decoded_engine" class="w-full border rounded px-3 py-2 uppercase" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">CYLINDERS</label>
                        <input type="text" name="cylinders" id="decoded_cylinders" class="w-full border rounded px-3 py-2 uppercase" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">DRIVE TYPE</label>
                        <input type="text" name="drive_type" id="decoded_drive" class="w-full border rounded px-3 py-2 uppercase" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">FUEL TYPE</label>
                        <input type="text" name="fuel_type" id="decoded_fuel" class="w-full border rounded px-3 py-2 uppercase" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">TRANSMISSION</label>
                        <input type="text" name="transmission" id="decoded_transmission" class="w-full border rounded px-3 py-2 uppercase" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">VEHICLE TYPE</label>
                        <input type="text" name="vehicle_type" id="decoded_vehicle_type" class="w-full border rounded px-3 py-2 uppercase" readonly>
                    </div>
                </div>

                <!-- Manual Entry Fields (shown if decode fails or user chooses manual) -->
                <div id="manualFields" class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold mb-1">MAKE</label>
                        <input type="text" name="make" class="w-full border rounded px-3 py-2 uppercase" style="text-transform: uppercase;">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">MODEL</label>
                        <input type="text" name="model" class="w-full border rounded px-3 py-2 uppercase" style="text-transform: uppercase;">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">YEAR</label>
                        <input type="text" name="year" class="w-full border rounded px-3 py-2 uppercase" style="text-transform: uppercase;">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">TRIM</label>
                        <input type="text" name="trim" class="w-full border rounded px-3 py-2 uppercase" style="text-transform: uppercase;">
                    </div>
                </div>

                <!-- Required Condition Fields -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-semibold mb-1">TITLE <span class="text-red-500">*</span></label>
                        <select name="title_status" required class="w-full border rounded px-3 py-2">
                            <option value="">Select</option>
                            <option value="yes">YES</option>
                            <option value="no">NO</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">ISLAND LOCATION <span class="text-red-500">*</span></label>
                        <select name="island" required class="w-full border rounded px-3 py-2">
                            <option value="">Select Island</option>
                            <option value="GRAND_CAYMAN">GRAND CAYMAN</option>
                            <option value="CAYMAN_BRAC">CAYMAN BRAC</option>
                            <option value="LITTLE_CAYMAN">LITTLE CAYMAN</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">EXTERIOR COLOR <span class="text-red-500">*</span></label>
                        <select name="color" required class="w-full border rounded px-3 py-2">
                            <option value="">Select Color</option>
                            <option value="BLACK">BLACK</option>
                            <option value="WHITE">WHITE</option>
                            <option value="SILVER">SILVER</option>
                            <option value="GRAY">GRAY</option>
                            <option value="RED">RED</option>
                            <option value="BLUE">BLUE</option>
                            <option value="GREEN">GREEN</option>
                            <option value="BROWN">BROWN</option>
                            <option value="YELLOW">YELLOW</option>
                            <option value="ORANGE">ORANGE</option>
                            <option value="OTHER">OTHER</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">INTERIOR COLOR <span class="text-red-500">*</span></label>
                        <select name="interior_color" required class="w-full border rounded px-3 py-2">
                            <option value="">Select Color</option>
                            <option value="BLACK">BLACK</option>
                            <option value="GRAY">GRAY</option>
                            <option value="BEIGE">BEIGE</option>
                            <option value="BROWN">BROWN</option>
                            <option value="OTHER">OTHER</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">PRIMARY DAMAGE <span class="text-red-500">*</span></label>
                        <select name="primary_damage" required class="w-full border rounded px-3 py-2">
                            <option value="">Select Damage</option>
                            <option value="NONE">NONE</option>
                            <option value="FRONT_END">FRONT END</option>
                            <option value="REAR_END">REAR END</option>
                            <option value="SIDE">SIDE</option>
                            <option value="FLOOD">FLOOD</option>
                            <option value="FIRE">FIRE</option>
                            <option value="VANDALISM">VANDALISM</option>
                            <option value="OTHER">OTHER</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">HAS KEYS <span class="text-red-500">*</span></label>
                        <select name="keys_available" required class="w-full border rounded px-3 py-2">
                            <option value="">Select</option>
                            <option value="yes">YES</option>
                            <option value="no">NO</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">SECONDARY DAMAGE</label>
                        <select name="secondary_damage" class="w-full border rounded px-3 py-2">
                            <option value="">Select (Optional)</option>
                            <option value="NONE">NONE</option>
                            <option value="MINOR">MINOR</option>
                            <option value="MODERATE">MODERATE</option>
                            <option value="SEVERE">SEVERE</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-1">ADDITIONAL NOTES</label>
                        <textarea name="additional_notes" rows="3" class="w-full border rounded px-3 py-2 uppercase" style="text-transform: uppercase;"></textarea>
                    </div>
                </div>

                <button type="button" onclick="showSection(2)" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    CONTINUE TO PHOTOS →
                </button>
            </div>

            <!-- SECTION 2: PHOTOS -->
            <div id="section2" class="bg-white rounded-lg shadow-md p-6 mb-6" style="display: none;">
                <h2 class="text-2xl font-bold mb-4 border-b pb-2">SECTION 2 — PHOTOS</h2>
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">COVER PHOTO (REQUIRED) <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-600 mb-2">Front view of vehicle or vessel</p>
                    <input type="file" name="cover_photo" required accept="image/*" class="w-full border rounded px-3 py-2">
                    <div id="coverPhotoPreview" class="mt-2"></div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2">ADDITIONAL PHOTOS (REQUIRED: 5-10 photos) <span class="text-red-500">*</span></label>
                    <p class="text-xs text-gray-600 mb-2">Recommended: LEFT SIDE, RIGHT SIDE, REAR, INTERIOR (1), INTERIOR (2), DASHBOARD/ODOMETER, VIN/HIN PHOTO, ENGINE BAY</p>
                    <input type="file" name="photos[]" multiple required accept="image/*" class="w-full border rounded px-3 py-2" min="5" max="10">
                    <p class="text-xs text-gray-500 mt-1" id="photoCount">0 photos selected</p>
                    <div id="photoPreview" class="mt-4 grid grid-cols-4 gap-2"></div>
                    <p id="photoWarning" class="text-xs text-amber-600 mt-2" style="display: none;">WE RECOMMEND AT LEAST 7 PHOTOS FOR BEST RESULTS.</p>
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="showSection(1)" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                        ← BACK
                    </button>
                    <button type="button" onclick="showSection(3)" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        CONTINUE TO AUCTION SETTINGS →
                    </button>
                </div>
            </div>

            <!-- SECTION 3: AUCTION SETTINGS & PAYMENT -->
            <div id="section3" class="bg-white rounded-lg shadow-md p-6 mb-6" style="display: none;">
                <h2 class="text-2xl font-bold mb-4 border-b pb-2">SECTION 3 — AUCTION SETTINGS & PAYMENT</h2>
                
                <!-- Auction Duration -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold mb-2">AUCTION DURATION <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-5 gap-4">
                        <label class="border rounded p-4 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="auction_duration" value="5" required class="mr-2">
                            5 DAYS
                        </label>
                        <label class="border rounded p-4 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="auction_duration" value="7" required class="mr-2">
                            7 DAYS
                        </label>
                        <label class="border rounded p-4 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="auction_duration" value="14" required class="mr-2">
                            14 DAYS
                        </label>
                        <label class="border rounded p-4 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="auction_duration" value="21" required class="mr-2">
                            21 DAYS
                        </label>
                        <label class="border rounded p-4 cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="auction_duration" value="28" required class="mr-2">
                            28 DAYS
                        </label>
                    </div>
                </div>

                <!-- Optional Pricing -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">AUCTION PRICING (OPTIONAL)</h3>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1">STARTING BID</label>
                            <input type="number" name="starting_price" step="0.01" min="0" class="w-full border rounded px-3 py-2" placeholder="$0.00">
                            <p class="text-xs text-gray-500 mt-1">Must be > $0 if entered</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">RESERVE PRICE</label>
                            <input type="number" name="reserve_price" step="0.01" min="0" class="w-full border rounded px-3 py-2" placeholder="$0.00">
                            <p class="text-xs text-gray-500 mt-1">Must be ≥ Starting Bid</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1">BUY NOW PRICE</label>
                            <input type="number" name="buy_now_price" step="0.01" min="0" class="w-full border rounded px-3 py-2" placeholder="$0.00">
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 mt-2">If no pricing is entered, auction runs using default system pricing with no reserve.</p>
                </div>

                <!-- Payment (Individual Sellers only) -->
                @if($user->activeSubscription?->package?->price == 25.00)
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded">
                    <h3 class="text-lg font-semibold mb-2">PAYMENT REQUIRED</h3>
                    <p class="text-sm mb-4">Individual Sellers must pay a $25 listing fee before submission.</p>
                    <div>
                        <label class="block text-sm font-semibold mb-2">PAYMENT METHOD</label>
                        <select name="payment_method" required class="w-full border rounded px-3 py-2">
                            <option value="">Select Payment Method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="stored_payment">Stored Payment Method</option>
                        </select>
                        <!-- TODO: Add payment form fields here -->
                    </div>
                </div>
                @endif

                <!-- Final Acknowledgment -->
                <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded">
                    <p class="text-sm font-semibold text-center uppercase">
                        BY SUBMITTING THIS LISTING, YOU AGREE THAT ALL INFORMATION PROVIDED IS ACCURATE AND YOU ACCEPT CAYMARK'S TERMS AND CONDITIONS. ALL LISTINGS ARE SUBJECT TO ADMIN APPROVAL BEFORE GOING LIVE.
                    </p>
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="showSection(2)" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                        ← BACK
                    </button>
                    <button type="submit" class="bg-green-600 text-white px-8 py-2 rounded hover:bg-green-700 font-semibold">
                        COMPLETE SUBMISSION
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
        
        // Show selected section
        document.getElementById('section' + sectionNum).style.display = 'block';
    }

    // VIN/HIN Decoder
    document.getElementById('searchVinBtn').addEventListener('click', function() {
        const vinHin = document.getElementById('vin_hin').value.trim().toUpperCase();
        if (!vinHin) {
            alert('Please enter a VIN or HIN');
            return;
        }

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
                // Populate decoded fields
                Object.keys(data.data).forEach(key => {
                    const field = document.getElementById('decoded_' + key);
                    if (field) field.value = data.data[key];
                });
                document.getElementById('decodedFields').style.display = 'grid';
                document.getElementById('manualFields').style.display = 'none';
                messageDiv.innerHTML = '<span class="text-green-600">✓ VIN/HIN decoded successfully</span>';
            } else {
                document.getElementById('decodedFields').style.display = 'none';
                document.getElementById('manualFields').style.display = 'grid';
                messageDiv.innerHTML = '<span class="text-red-600">' + data.message + '</span>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('vinDecoderMessage').innerHTML = '<span class="text-red-600">Error decoding VIN/HIN. Please enter details manually.</span>';
        });
    });

    // Photo preview and validation
    document.querySelector('input[name="cover_photo"]').addEventListener('change', function(e) {
        if (e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('coverPhotoPreview').innerHTML = '<img src="' + e.target.result + '" class="w-32 h-32 object-cover rounded">';
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    document.querySelector('input[name="photos[]"]').addEventListener('change', function(e) {
        const files = e.target.files;
        const count = files.length;
        document.getElementById('photoCount').textContent = count + ' photos selected';
        
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
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-full h-24 object-cover rounded';
                preview.appendChild(img);
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
</script>
@endsection
