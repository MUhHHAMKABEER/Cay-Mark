@php
    $maxYear = $maxYear ?? ((int) date('Y') + 1);
    $minYear = 1995;
    $identifierKind = old('identifier_kind', 'vehicle');
    if (old('vin_hin_type') === 'hin') {
        $identifierKind = 'marine';
    }
@endphp
<div id="section1" class="form-section animate-slide-in">
    <div class="section-header">
        <div class="section-icon">1</div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Vehicle Information</h2>
            <p class="text-sm text-gray-600">Enter identification and vehicle details</p>
        </div>
    </div>

    <input type="hidden" name="identifier_kind" id="identifier_kind" value="{{ $identifierKind }}">
    <input type="hidden" name="vin_decode_success" id="vin_decode_success" value="{{ old('vin_decode_success', $isEdit ? '1' : '0') }}">

    <div class="mb-4">
        <label class="form-label">Category <span class="text-red-500">*</span></label>
        <div class="identifier-type-group">
            <label class="identifier-type-option">
                <input type="radio" name="category_type_radio" value="vehicle" {{ $identifierKind === 'vehicle' ? 'checked' : '' }}>
                <span class="identifier-type-label">Vehicle</span>
            </label>
            <label class="identifier-type-option">
                <input type="radio" name="category_type_radio" value="marine" {{ $identifierKind === 'marine' ? 'checked' : '' }}>
                <span class="identifier-type-label">Marine</span>
            </label>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label" id="vin_hin_label">{{ $identifierKind === 'marine' ? 'HIN' : 'VIN' }}</label>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="vin_hin" name="vin" class="form-input flex-1 uppercase"
                   placeholder="{{ $identifierKind === 'marine' ? 'Enter 14-character HIN' : 'Enter 17-character VIN' }}"
                   maxlength="{{ $identifierKind === 'marine' ? 14 : 17 }}"
                   value="{{ old('vin', $listing->vin ?? '') }}">
            <button type="button" id="searchVinBtn" class="btn-primary whitespace-nowrap px-4 py-2.5 inline-flex items-center gap-2 transition-all">
                {{-- Idle state --}}
                <span id="vinBtn_idle" class="inline-flex items-center gap-2">
                    <i class="fas fa-search"></i>
                    Lookup
                </span>
                {{-- Loading state (hidden until fetch starts) --}}
                <span id="vinBtn_loading" class="hidden inline-flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Looking up…
                </span>
            </button>
        </div>
        <div id="vinDecoderMessage" class="mt-1 text-sm"></div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
        <div id="mmyLockGroup">
            <label class="form-label">Make <span class="text-red-500">*</span></label>
            <input type="text" name="make" id="field_make" required class="form-input uppercase"
                   value="{{ old('make', $listing->make ?? '') }}">
        </div>
        <div id="mmyLockGroupModel">
            <label class="form-label">Model <span class="text-red-500">*</span></label>
            <input type="text" name="model" id="field_model" required class="form-input uppercase"
                   value="{{ old('model', $listing->model ?? '') }}">
        </div>
        <div id="mmyLockGroupYear">
            <label class="form-label">Year <span class="text-red-500">*</span></label>
            <input type="number" name="year" id="field_year" required class="form-input"
                   min="{{ $minYear }}" max="{{ $maxYear }}" step="1"
                   value="{{ old('year', $listing->year ?? '') }}">
        </div>
        <div>
            <label class="form-label">Trim</label>
            <input type="text" name="trim" class="form-input" value="{{ old('trim', $listing->trim ?? '') }}">
        </div>
        <div>
            <label class="form-label">Fuel Type</label>
            <select name="fuel_type" class="form-input">
                <option value="">Select</option>
                @foreach(config('listing_fuel_types.allowed', []) as $fuel)
                    <option value="{{ $fuel }}" {{ old('fuel_type', $listing->fuel_type ?? '') === $fuel ? 'selected' : '' }}>{{ $fuel }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Transmission</label>
            <select name="transmission" class="form-input">
                <option value="">Select</option>
                @foreach(config('listing_transmissions.allowed', []) as $trans)
                    <option value="{{ $trans }}" {{ old('transmission', $listing->transmission ?? '') === strtolower($trans) || old('transmission', $listing->transmission ?? '') === $trans ? 'selected' : '' }}>{{ $trans }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Engine Size</label>
            <input type="number" name="engine_size" step="0.1" min="0" class="form-input"
                   value="{{ old('engine_size', $listing->engine_type ?? '') }}">
        </div>
        <div>
            <label class="form-label">Cylinder</label>
            <input type="number" name="cylinders" step="0.1" min="0" class="form-input"
                   value="{{ old('cylinders', $listing->cylinders ?? '') }}">
        </div>
        <div>
            <label class="form-label">Drive Type</label>
            <select name="drive_type" class="form-input">
                <option value="">Select</option>
                @foreach(config('listing_drive_types.allowed', []) as $key => $label)
                    <option value="{{ $key }}" {{ old('drive_type', $listing->drive_type ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Vehicle Type <span class="text-red-500">*</span></label>
            <select name="vehicle_type" required class="form-input">
                <option value="">Select type</option>
                @foreach(['Sedan','SUV','Truck','Coupe','Van','Convertible','Hatchback','Wagon','Motorcycle','Boat','ATV','Other'] as $vt)
                    <option value="{{ $vt }}" {{ old('vehicle_type', $listing->vehicle_type ?? '') === $vt ? 'selected' : '' }}>{{ $vt }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Island Location <span class="text-red-500">*</span></label>
            <select name="island" required class="form-input">
                <option value="">Select Island</option>
                @foreach(config('islands.list', []) as $island)
                    <option value="{{ $island }}" {{ old('island', $listing->island ?? '') === $island ? 'selected' : '' }}>{{ $island }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Exterior Color <span class="text-red-500">*</span></label>
            <select name="color" required class="form-input">
                <option value="">Select Color</option>
                @php $selColor = old('color', $listing->color ?? ''); @endphp
                @foreach(config('listing_colors.allowed', []) as $c)
                    <option value="{{ $c }}" {{ $selColor === $c ? 'selected' : '' }}>{{ ucfirst(strtolower($c)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Interior Color <span class="text-red-500">*</span></label>
            <select name="interior_color" required class="form-input">
                <option value="">Select Color</option>
                @php $selInt = old('interior_color', $listing->interior_color ?? ''); @endphp
                @foreach(config('listing_colors.allowed', []) as $c)
                    <option value="{{ $c }}" {{ $selInt === $c ? 'selected' : '' }}>{{ ucfirst(strtolower($c)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Odometer (km)</label>
            <input type="number" name="odometer" class="form-input" min="0" max="9999999" step="1"
                   value="{{ old('odometer', $listing->odometer ?? '') }}">
        </div>
        <div class="col-span-2 md:col-span-3 flex items-center gap-2">
            <x-custom-checkbox name="odometer_estimated" value="1" label="Estimate Reading" :checked="(bool) old('odometer_estimated', $listing->odometer_estimated ?? false)" />
        </div>
    </div>

    <div class="flex justify-end">
        <button type="button" id="btn-continue-step2" class="btn-primary">
            Continue to Condition + Media <i class="fas fa-arrow-right ml-2"></i>
        </button>
    </div>
</div>
