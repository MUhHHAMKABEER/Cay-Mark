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

        {{-- Title --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="form-label !mb-0">Title <span class="text-red-500">*</span></label>
                <button type="button" class="condition-info-btn flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[#063466] hover:bg-blue-50 transition-colors duration-150" data-modal="modal-title-yes" aria-label="About Title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </button>
            </div>
            <select name="title_status" required class="form-input" id="title_status_select">
                <option value="">Select</option>
                <option value="yes" {{ old('title_status', isset($listing) && $listing->title_status === 'CLEAN' ? 'yes' : '') == 'yes' ? 'selected' : '' }}>Title</option>
                <option value="no" {{ old('title_status', isset($listing) && $listing->title_status === 'SALVAGE' ? 'no' : '') == 'no' ? 'selected' : '' }}>No Title</option>
            </select>
        </div>

        {{-- Salvage --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="form-label !mb-0">Salvage <span class="text-red-500">*</span></label>
                <button type="button" class="condition-info-btn flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[#063466] hover:bg-blue-50 transition-colors duration-150" data-modal="modal-salvage" aria-label="About Salvage">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </button>
            </div>
            <select name="is_salvaged" required class="form-input" id="is_salvaged_select">
                <option value="">Select</option>
                @php $salVal = old('is_salvaged', isset($listing) && $listing->condition === 'salvaged' ? '1' : '0'); @endphp
                <option value="0" {{ $salVal === '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ $salVal === '1' ? 'selected' : '' }}>Yes</option>
            </select>
        </div>

        {{-- Runs & Drives --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="form-label !mb-0">Runs &amp; Drives <span class="text-red-500">*</span></label>
                <button type="button" class="condition-info-btn flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[#063466] hover:bg-blue-50 transition-colors duration-150" data-modal="modal-runs-drives" aria-label="About Runs and Drives">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </button>
            </div>
            <select name="run_and_drive" required class="form-input">
                <option value="">Select</option>
                @php $rdVal = old('run_and_drive', $listing->run_and_drive ?? ''); @endphp
                <option value="yes" {{ $rdVal === 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ $rdVal === 'no' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        {{-- Starts --}}
        <div>
            <div class="flex items-center justify-between mb-1">
                <label class="form-label !mb-0">Starts <span class="text-red-500">*</span></label>
                <button type="button" class="condition-info-btn flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-[#063466] hover:bg-blue-50 transition-colors duration-150" data-modal="modal-starts" aria-label="About Starts">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </button>
            </div>
            <select name="engine_starts" required class="form-input" id="engine_starts_select">
                <option value="">Select</option>
                @php $stVal = old('engine_starts', $listing->engine_starts ?? ''); @endphp
                <option value="yes" {{ $stVal === 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ $stVal === 'no' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        {{-- Has Key(s) --}}
        <div>
            <label class="form-label">Has Key(s) <span class="text-red-500">*</span></label>
            <select name="keys_available" required class="form-input">
                <option value="">Select</option>
                @php $keysVal = old('keys_available', isset($listing) && $listing->keys_available ? 'yes' : 'no'); @endphp
                <option value="yes" {{ $keysVal === 'yes' ? 'selected' : '' }}>Yes</option>
                <option value="no" {{ $keysVal === 'no' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        {{-- Primary Damage --}}
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

        {{-- Secondary Damage --}}
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

    @if($isEdit)
        @php
            $coverImage       = $listing->images->firstWhere('id', $listing->cover_photo_id);
            $additionalImages = $listing->images->filter(fn($img) => $img->id != $listing->cover_photo_id)->values();
        @endphp

        {{-- Cover Photo --}}
        <div class="mb-5 p-4 bg-blue-50 border-2 border-blue-200 rounded-xl">
            <p class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                <span class="w-5 h-5 bg-[#063466] text-white text-[9px] flex items-center justify-center font-bold flex-shrink-0 rounded">★</span>
                Cover Photo
            </p>
            @if($coverImage)
                @php $coverSrc = str_contains($coverImage->image_path ?? '', '/') ? asset($coverImage->image_path) : asset('uploads/listings/' . $coverImage->image_path); @endphp
                <div class="flex flex-col sm:flex-row items-start gap-4">
                    <div class="relative flex-shrink-0">
                        <img src="{{ $coverSrc }}" alt="Cover photo" class="w-36 h-28 object-cover rounded-lg border-2 border-blue-300">
                        <span class="absolute top-1.5 left-1.5 bg-[#063466] text-white text-[9px] font-bold px-1.5 py-0.5 uppercase tracking-wide rounded">Cover</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-500 mb-3 leading-relaxed">This is the main photo shown in your listing. Upload a new photo below to replace it.</p>
                        <label class="form-label !text-[11px] !font-bold !text-gray-500 uppercase tracking-widest mb-1.5">Replace Cover Photo</label>
                        <div class="file-upload-box" id="coverPhotoUploadBox">
                            <input type="file" name="cover_photo" id="cover_photo_input" accept="image/*" class="file-upload-input">
                            <div class="file-upload-inner">
                                <div class="file-upload-icon"><i class="fas fa-camera"></i></div>
                                <div class="file-upload-btn-text">Choose New Cover</div>
                                <div class="file-upload-status" id="coverPhotoStatus">No file chosen</div>
                            </div>
                        </div>
                        <div id="coverPhotoPreview" class="mt-2"></div>
                    </div>
                </div>
            @else
                <p class="text-xs text-amber-600 mb-2">No cover photo set. Please upload one.</p>
                <div class="file-upload-box" id="coverPhotoUploadBox">
                    <input type="file" name="cover_photo" id="cover_photo_input" required accept="image/*" class="file-upload-input">
                    <div class="file-upload-inner">
                        <div class="file-upload-icon"><i class="fas fa-camera"></i></div>
                        <div class="file-upload-btn-text">Choose Cover Photo</div>
                        <div class="file-upload-status" id="coverPhotoStatus">No file chosen</div>
                    </div>
                </div>
                <div id="coverPhotoPreview" class="mt-2"></div>
            @endif
        </div>

        {{-- Existing Additional Photos --}}
        <div class="mb-5 p-4 bg-gray-50 border border-gray-200 rounded-xl">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-bold text-gray-700 uppercase tracking-wider">Additional Photos</p>
                <span class="text-[11px] text-gray-400" id="existingPhotoCount">{{ $additionalImages->count() }} saved</span>
            </div>
            @if($additionalImages->count() > 0)
                <div class="flex flex-wrap gap-3 mb-2" id="existingPhotosGrid">
                    @foreach($additionalImages as $img)
                        @php $imgSrc = str_contains($img->image_path ?? '', '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path); @endphp
                        <div class="relative group" id="existing-photo-{{ $img->id }}">
                            <img src="{{ $imgSrc }}" alt="Listing photo"
                                 class="w-24 h-20 object-cover rounded-lg border border-gray-200 group-hover:border-red-300 transition-colors">
                            <button type="button"
                                    onclick="deleteListingPhoto({{ $img->id }}, {{ $listing->id }})"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-600 shadow-md"
                                    title="Remove this photo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                <p class="text-[11px] text-gray-400">Hover a photo to reveal the × button and remove it.</p>
            @else
                <p class="text-xs text-gray-400 italic">No additional photos yet.</p>
            @endif
        </div>

        {{-- Add More Photos (edit mode) --}}
        <div class="mb-4">
            <label class="form-label">Add More Photos <span class="text-gray-400 font-normal text-xs">(optional)</span></label>
            <div class="file-upload-box" id="photosUploadBox">
                <input type="file" name="photos[]" id="photos_input" multiple accept="image/*" class="file-upload-input">
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

    @else
        {{-- Create mode: two-column upload layout --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="form-label">Cover Photo (Front) <span class="text-red-500">*</span></label>
                <div class="file-upload-box" id="coverPhotoUploadBox">
                    <input type="file" name="cover_photo" id="cover_photo_input" required accept="image/*" class="file-upload-input">
                    <div class="file-upload-inner">
                        <div class="file-upload-icon"><i class="fas fa-camera"></i></div>
                        <div class="file-upload-btn-text">Choose Cover Photo</div>
                        <div class="file-upload-status" id="coverPhotoStatus">No file chosen</div>
                    </div>
                </div>
                <div id="coverPhotoPreview" class="mt-2"></div>
            </div>
            <div>
                <label class="form-label">Additional Photos <span class="text-red-500">*</span></label>
                <div class="file-upload-box" id="photosUploadBox">
                    <input type="file" name="photos[]" id="photos_input" multiple required accept="image/*" class="file-upload-input">
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
    @endif

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
