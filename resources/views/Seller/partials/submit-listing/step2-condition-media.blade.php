<div id="section2" class="form-section" style="display: @if(session('error_section') === 'section2') block; border: 2px solid #ef4444; border-radius: 12px; @else none; @endif">
    <div class="section-header">
        <div class="section-icon">2</div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Condition + Media</h2>
            <p class="text-sm text-gray-600">Vehicle condition and required photos</p>
        </div>
    </div>

    <h3 class="text-base font-semibold text-gray-900 mb-3">Condition</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div>
            <label class="form-label flex items-center gap-1">Title <span class="text-red-500">*</span>
                <button type="button" class="text-blue-600 text-xs underline condition-info-btn" data-modal="modal-title-yes">Info</button>
            </label>
            <select name="title_status" required class="form-input" id="title_status_select">
                <option value="">Select</option>
                <option value="yes" {{ old('title_status', isset($listing) && $listing->title_status === 'CLEAN' ? 'yes' : '') == 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ old('title_status', isset($listing) && $listing->title_status === 'SALVAGE' ? 'no' : '') == 'no' ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <div>
            <label class="form-label flex items-center gap-1">Salvage <span class="text-red-500">*</span>
                <button type="button" class="text-blue-600 text-xs underline condition-info-btn" data-modal="modal-salvage">Info</button>
            </label>
            <select name="is_salvaged" required class="form-input" id="is_salvaged_select">
                <option value="">Select</option>
                @php $salVal = old('is_salvaged', isset($listing) && $listing->condition === 'salvaged' ? '1' : '0'); @endphp
                <option value="0" {{ $salVal === '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ $salVal === '1' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>
        <div>
            <label class="form-label flex items-center gap-1">Runs &amp; Drives <span class="text-red-500">*</span>
                <button type="button" class="text-blue-600 text-xs underline condition-info-btn" data-modal="modal-runs-drives">Info</button>
            </label>
            <select name="run_and_drive" required class="form-input">
                <option value="">Select</option>
                @php $rdVal = old('run_and_drive', $listing->run_and_drive ?? ''); @endphp
                <option value="yes" {{ $rdVal === 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ $rdVal === 'no' ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <div>
            <label class="form-label flex items-center gap-1">Starts <span class="text-red-500">*</span>
                <button type="button" class="text-blue-600 text-xs underline condition-info-btn" data-modal="modal-starts">Info</button>
            </label>
            <select name="engine_starts" required class="form-input" id="engine_starts_select">
                <option value="">Select</option>
                @php $stVal = old('engine_starts', $listing->engine_starts ?? ''); @endphp
                <option value="yes" {{ $stVal === 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ $stVal === 'no' ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <div>
            <label class="form-label">Has Key(s) <span class="text-red-500">*</span></label>
            <select name="keys_available" required class="form-input">
                <option value="">Select</option>
                @php $keysVal = old('keys_available', isset($listing) && $listing->keys_available ? 'yes' : 'no'); @endphp
                <option value="yes" {{ $keysVal === 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ $keysVal === 'no' ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <div>
            <label class="form-label">Primary Damage <span class="text-red-500">*</span></label>
            <select name="primary_damage" required class="form-input">
                <option value="">Select</option>
                @php $priD = old('primary_damage', $listing->primary_damage ?? ''); @endphp
                @foreach(config('listing_damage_types.allowed', []) as $key => $label)
                    <option value="{{ $key }}" {{ $priD === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Secondary Damage <span class="text-red-500">*</span></label>
            <select name="secondary_damage" required class="form-input">
                <option value="">Select</option>
                @php $secD = old('secondary_damage', $listing->secondary_damage ?? ''); @endphp
                @foreach(config('listing_damage_types.allowed', []) as $key => $label)
                    <option value="{{ $key }}" {{ $secD === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <h3 class="text-base font-semibold text-gray-900 mb-2">Media Upload</h3>
    <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700">
        <p class="font-semibold mb-2">Include clear photos of the vehicle from multiple angles, including:</p>
        <ul class="list-disc list-inside space-y-0.5">
            <li>Front view</li>
            <li>Rear view</li>
            <li>Driver side</li>
            <li>Passenger side</li>
            <li>Interior/dashboard</li>
            <li>Engine area</li>
            <li>VIN plate</li>
        </ul>
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
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="form-label">Cover Photo (Front) @if(!$isEdit)<span class="text-red-500">*</span>@endif</label>
            <div class="file-upload-box" id="coverPhotoUploadBox">
                <input type="file" name="cover_photo" id="cover_photo_input" @if(!$isEdit) required @endif accept="image/*" class="file-upload-input">
                <div class="file-upload-inner">
                    <div class="file-upload-icon"><i class="fas fa-camera"></i></div>
                    <div class="file-upload-btn-text">Choose Cover Photo</div>
                    <div class="file-upload-status" id="coverPhotoStatus">No file chosen</div>
                </div>
            </div>
            <div id="coverPhotoPreview" class="mt-2"></div>
        </div>
        <div>
            <label class="form-label">Additional Photos @if(!$isEdit)<span class="text-red-500">*</span>@endif</label>
            <div class="file-upload-box" id="photosUploadBox">
                <input type="file" name="photos[]" id="photos_input" multiple @if(!$isEdit) required @endif accept="image/*" class="file-upload-input">
                <div class="file-upload-inner">
                    <div class="file-upload-icon"><i class="fas fa-images"></i></div>
                    <div class="file-upload-btn-text">Choose Photos</div>
                    <div class="file-upload-status" id="photoCount">0 photos selected</div>
                </div>
            </div>
            <input type="file" id="photos_add_more_input" multiple accept="image/*" tabindex="-1" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0;">
            <div id="photoPreview" class="photo-preview-grid mt-2"></div>
            <p id="photoWarning" class="text-xs text-amber-600 mt-1" style="display:none;">Minimum 6 photos total required.</p>
        </div>
    </div>

    <div id="engineVideoBlock" class="mb-4" style="display: {{ old('engine_starts', $listing->engine_starts ?? '') === 'yes' ? 'block' : 'none' }};">
        <label class="form-label">Engine Video <span class="text-red-500">*</span></label>
        <p class="text-xs text-gray-600 mb-2">Record the engine running (30 seconds to under 1 minute).</p>
        <div class="file-upload-box" id="engineVideoUploadBox">
            <input type="file" name="engine_video" id="engine_video_input"
                   accept="video/mp4,video/webm,video/quicktime"
                   class="file-upload-input">
            <div class="file-upload-inner">
                <div class="file-upload-icon"><i class="fas fa-video"></i></div>
                <div class="file-upload-btn-text">Choose Video</div>
                <div class="file-upload-status" id="engineVideoStatus">No file chosen</div>
            </div>
        </div>
        <p id="evDurationWarning" class="hidden text-xs text-amber-600 mt-1">Video must be between 30 seconds and under 1 minute.</p>
    </div>

    <div class="mb-4">
        <label class="form-label">Additional Notes (Optional)</label>
        <textarea name="additional_notes" rows="3" maxlength="300" class="form-input"
                  placeholder="Enter any details or conditions you wish to include about this listing.">{{ old('additional_notes', $listing->additional_notes ?? '') }}</textarea>
        <p class="text-xs text-gray-500 mt-1">Max 300 characters.</p>
    </div>

    <div class="flex flex-col sm:flex-row gap-4 justify-between">
        <button type="button" onclick="showSection(1)" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i> Back</button>
        <button type="button" id="btn-continue-step3" class="btn-primary">Continue to Auction + Payment <i class="fas fa-arrow-right ml-2"></i></button>
    </div>
</div>
