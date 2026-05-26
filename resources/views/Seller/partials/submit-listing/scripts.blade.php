<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
<script>
    const MAX_ADDITIONAL_PHOTOS = 14;
    const MIN_ADDITIONAL_PHOTOS = 5;
    const MAX_YEAR = {{ $maxYear ?? ((int) date('Y') + 1) }};

    function showSection(sectionNum) {
        ['section1', 'section2', 'section3'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.style.display = 'none';
                el.style.border = '';
                el.style.borderRadius = '';
            }
        });
        var section = document.getElementById('section' + sectionNum);
        if (section) {
            section.style.display = 'block';
            setTimeout(function() {
                try { section.scrollIntoView({ behavior: 'smooth', block: 'start' }); } catch (e) {}
            }, 50);
        }
        for (var j = 1; j <= 3; j++) {
            var ind = document.getElementById('step-indicator-' + j);
            if (!ind) continue;
            ind.classList.toggle('completed', j < sectionNum);
            ind.classList.toggle('active', j === sectionNum);
            if (j > sectionNum) ind.classList.remove('active', 'completed');
        }
        if (window.CaymarkUI && typeof CaymarkUI.updateProgress === 'function') {
            CaymarkUI.updateProgress('#listing-progress-bar', sectionNum, 3);
        }
    }

    function cmError(msg) {
        if (window.CaymarkUI && typeof CaymarkUI.showError === 'function') {
            CaymarkUI.showError('Please check your form', msg);
        } else {
            alert(msg);
        }
    }

    function validateStep1() {
        var required = ['make', 'model', 'year', 'vehicle_type', 'island', 'color', 'interior_color'];
        for (var i = 0; i < required.length; i++) {
            var el = document.querySelector('[name="' + required[i] + '"]');
            if (!el || !String(el.value || '').trim()) {
                cmError('Please complete all required fields marked with * in Vehicle Information.');
                if (el) el.focus();
                return false;
            }
        }
        var y = parseInt(document.querySelector('[name="year"]')?.value, 10);
        if (y < 1995 || y > MAX_YEAR) {
            cmError('Year must be between 1995 and ' + MAX_YEAR + '.');
            return false;
        }
        return true;
    }

    function validateStep2() {
        var fields = ['title_status', 'is_salvaged', 'run_and_drive', 'engine_starts', 'keys_available', 'primary_damage', 'secondary_damage'];
        for (var i = 0; i < fields.length; i++) {
            var el = document.querySelector('[name="' + fields[i] + '"]');
            if (!el || !String(el.value || '').trim()) {
                cmError('Please complete all condition fields.');
                return false;
            }
        }
        var cover = document.getElementById('cover_photo_input');
        var photos = document.getElementById('photos_input');
        @if(!$isEdit)
        if (!cover?.files?.length) { cmError('Cover photo is required.'); return false; }
        var addCount = typeof additionalPhotosFiles !== 'undefined' ? additionalPhotosFiles.length : (photos?.files?.length || 0);
        if (addCount < MIN_ADDITIONAL_PHOTOS) {
            cmError('Upload at least ' + (MIN_ADDITIONAL_PHOTOS + 1) + ' photos total (1 cover + ' + MIN_ADDITIONAL_PHOTOS + ' additional).');
            return false;
        }
        if (addCount > MAX_ADDITIONAL_PHOTOS) {
            cmError('Maximum ' + (MAX_ADDITIONAL_PHOTOS + 1) + ' photos allowed.');
            return false;
        }
        @endif
        if (document.getElementById('engine_starts_select')?.value === 'yes') {
            var vid = document.getElementById('engine_video_input');
            if (!vid?.files?.length) {
                cmError('Engine video is required when engine starts is Yes.');
                return false;
            }
            // Best-effort duration check (30s–60s). If browser cannot determine
            // the duration we proceed; server should also validate.
            if (typeof vid._duration === 'number' && isFinite(vid._duration)) {
                if (vid._duration < 30 || vid._duration > 60) {
                    cmError('Engine video must be between 30 seconds and under 1 minute.');
                    return false;
                }
            }
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Re-apply lock on page load if VIN was already decoded (edit mode / validation re-render)
        var vinFlag = document.getElementById('vin_decode_success');
        if (vinFlag && vinFlag.value === '1') {
            setMmyLocked(true);
        }

        document.getElementById('btn-continue-step2')?.addEventListener('click', function(e) {
            e.preventDefault();
            if (validateStep1()) showSection(2);
        });
        document.getElementById('btn-continue-step3')?.addEventListener('click', function(e) {
            e.preventDefault();
            if (validateStep2()) showSection(3);
        });

        document.querySelectorAll('input[name="category_type_radio"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var kind = this.value;
                document.getElementById('identifier_kind').value = kind;
                var label = document.getElementById('vin_hin_label');
                var input = document.getElementById('vin_hin');
                if (kind === 'marine') {
                    if (label) label.textContent = 'HIN';
                    if (input) { input.placeholder = 'Enter 12 or 14-character HIN'; input.maxLength = 14; input.value = ''; }
                } else {
                    if (label) label.textContent = 'VIN';
                    if (input) { input.placeholder = 'Enter 17-character VIN'; input.maxLength = 17; input.value = ''; }
                }
                vinAttemptCount = 0;
                var flag = document.getElementById('vin_decode_success'); if (flag) flag.value = '0';
                setMmyLocked(false);
                showVinMessage('', false);
            });
        });

        // If user edits VIN/HIN after a successful decode, drop the lock and reset success.
        document.getElementById('vin_hin')?.addEventListener('input', function() {
            var flag = document.getElementById('vin_decode_success');
            if (flag && flag.value === '1') {
                flag.value = '0';
                setMmyLocked(false);
                showVinMessage('', false);
            }
        });

        document.querySelectorAll('.condition-info-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var id = this.getAttribute('data-modal');
                var m = id ? document.getElementById(id) : null;
                if (m) m.style.display = 'flex';
            });
        });
        document.querySelectorAll('.condition-modal-close').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var overlay = this.closest('.cond-modal-overlay');
                if (overlay) overlay.style.display = 'none';
            });
        });
        document.getElementById('title_status_select')?.addEventListener('change', function() {
            if (this.value === 'yes') document.getElementById('modal-title-yes').style.display = 'flex';
            if (this.value === 'no') document.getElementById('modal-title-no').style.display = 'flex';
        });
        document.getElementById('engine_starts_select')?.addEventListener('change', function() {
            var block = document.getElementById('engineVideoBlock');
            if (block) block.style.display = this.value === 'yes' ? 'block' : 'none';
        });

        // Engine video: update status text + has-file class + probe duration.
        document.getElementById('engine_video_input')?.addEventListener('change', function() {
            var input   = this;
            input._duration = null;
            var box     = document.getElementById('engineVideoUploadBox');
            var status  = document.getElementById('engineVideoStatus');
            var warning = document.getElementById('evDurationWarning');
            var file    = input.files && input.files[0];

            if (!file) {
                if (box)    box.classList.remove('has-file');
                if (status) status.textContent = 'No file chosen';
                if (warning) warning.classList.add('hidden');
                return;
            }

            if (box)    box.classList.add('has-file');
            if (status) status.textContent = file.name;
            if (warning) warning.classList.add('hidden');

            // Probe duration
            try {
                var url = URL.createObjectURL(file);
                var vid = document.createElement('video');
                vid.preload = 'metadata';
                vid.onloadedmetadata = function() {
                    input._duration = vid.duration;
                    URL.revokeObjectURL(url);
                    if (warning) {
                        if (vid.duration < 30 || vid.duration > 60) {
                            warning.classList.remove('hidden');
                        }
                    }
                };
                vid.onerror = function() { URL.revokeObjectURL(url); };
                vid.src = url;
            } catch (e) { /* non-fatal */ }
        });
    });

    var vinAttemptCount = 0;
    // ids: optional array of element ids to lock; defaults to all three MMY fields.
    // When unlocking (locked=false), always unlocks all three regardless of ids.
    function setMmyLocked(locked, ids) {
        var allMmy = ['field_make', 'field_model', 'field_year'];
        var targets = locked ? (ids || allMmy) : allMmy;
        targets.forEach(function(id) {
            var el = document.getElementById(id);
            if (!el) return;
            el.readOnly = locked;
            el.classList.toggle('bg-gray-100', locked);
            el.classList.toggle('text-gray-500', locked);
            el.style.cursor        = locked ? 'not-allowed' : '';
            el.style.pointerEvents = locked ? 'none' : '';
        });
        // Show/hide the lock badge — only show when at least one field is locked
        var badge = document.getElementById('vinLockBadge');
        if (!badge) return;
        if (locked && targets.length) {
            badge.style.display = 'flex';
            clearTimeout(badge._hideTimer);
            badge._hideTimer = setTimeout(function() {
                badge.style.display = 'none';
            }, 4000);
        } else {
            clearTimeout(badge._hideTimer);
            badge.style.display = 'none';
        }
    }
    function applyDecodedData(data) {
        var map = {
            make: 'field_make', model: 'field_model', year: 'field_year', trim: 'trim',
            engine_size: 'engine_size', cylinders: 'cylinders', drive_type: 'drive_type',
            fuel_type: 'fuel_type', transmission: 'transmission', vehicle_type: 'vehicle_type'
        };
        Object.keys(data || {}).forEach(function(key) {
            var el = document.querySelector('[name="' + key + '"]');
            if (el && data[key] != null) el.value = data[key];
        });
    }
    function showVinMessage(text, isError) {
        var div = document.getElementById('vinDecoderMessage');
        if (!div) return;
        div.className = 'mt-1 text-sm ' + (isError ? 'text-red-600' : 'text-green-600');
        div.textContent = text;
    }

    document.getElementById('searchVinBtn')?.addEventListener('click', function() {
        var kind = document.getElementById('identifier_kind')?.value || 'vehicle';
        var vinHin = (document.getElementById('vin_hin')?.value || '').trim().toUpperCase();

        // VIN = exactly 17 chars; HIN = 12–14 chars (US or international standard)
        var validLength = kind === 'marine'
            ? (vinHin.length >= 12 && vinHin.length <= 14)
            : (vinHin.length === 17);

        if (!validLength) {
            showVinMessage(kind === 'marine'
                ? 'Please enter a valid HIN (12 or 14 characters).'
                : 'Please enter 17 characters to enable VIN reader.', true);
            return;
        }
        var btn = this;
        btn.disabled = true;
        // Show spinner, hide idle label
        var idleEl    = document.getElementById('vinBtn_idle');
        var loadingEl = document.getElementById('vinBtn_loading');
        if (idleEl)    idleEl.classList.add('hidden');
        if (loadingEl) loadingEl.classList.remove('hidden');
        fetch('{{ route("seller.listings.decode-vin-hin") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ vin_hin: vinHin, identifier_kind: kind })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                applyDecodedData(data.data);
                document.getElementById('vin_decode_success').value = '1';
                // Only lock MMY fields that the decoder actually returned a value for
                var mmyMap = { make: 'field_make', model: 'field_model', year: 'field_year' };
                var decoded = data.data || {};
                var tolock = Object.keys(mmyMap).filter(function(k) {
                    var v = decoded[k];
                    return v != null && String(v).trim() !== '';
                }).map(function(k) { return mmyMap[k]; });
                setMmyLocked(true, tolock);
                showVinMessage('Details loaded successfully.', false);
            } else {
                vinAttemptCount++;
                document.getElementById('vin_decode_success').value = '0';
                setMmyLocked(false);
                if (vinAttemptCount >= 3) {
                    showVinMessage('Entry unsuccessful. Please enter details manually.', true);
                } else {
                    showVinMessage('Invalid entry. Please try again.', true);
                }
            }
        })
        .catch(function() {
            vinAttemptCount++;
            document.getElementById('vin_decode_success').value = '0';
            setMmyLocked(false);
            showVinMessage(vinAttemptCount >= 3 ? 'Entry unsuccessful. Please enter details manually.' : 'Invalid entry. Please try again.', true);
        })
        .finally(function() {
            btn.disabled = false;
            // Restore idle state, hide spinner
            if (idleEl)    idleEl.classList.remove('hidden');
            if (loadingEl) loadingEl.classList.add('hidden');
        });
    });

    var additionalPhotosFiles = [];
    var photosInputEl = document.getElementById('photos_input');
    var photoCountEl = document.getElementById('photoCount');
    var photoPreviewEl = document.getElementById('photoPreview');
    var photoWarningEl = document.getElementById('photoWarning');

    function setAdditionalPhotosInput(files) {
        if (!photosInputEl) return;
        var dt = new DataTransfer();
        files.forEach(function(f) { dt.items.add(f); });
        photosInputEl.files = dt.files;
    }
    function renderAdditionalPhotosPreviews() {
        var count = additionalPhotosFiles.length;
        if (photoCountEl) photoCountEl.textContent = count + ' photo(s) selected';
        if (photoWarningEl) photoWarningEl.style.display = 'none'; // handled by tile now
        if (!photoPreviewEl) return;
        photoPreviewEl.innerHTML = '';

        // Render photo thumbnails
        additionalPhotosFiles.forEach(function(file, index) {
            var div = document.createElement('div');
            div.className = 'photo-preview-item';
            var url = URL.createObjectURL(file);
            var img = document.createElement('img');
            img.src = url;
            div.appendChild(img);
            var rm = document.createElement('button');
            rm.type = 'button';
            rm.className = 'photo-preview-remove';
            rm.innerHTML = '&times;';
            rm.onclick = function() {
                additionalPhotosFiles.splice(index, 1);
                setAdditionalPhotosInput(additionalPhotosFiles);
                renderAdditionalPhotosPreviews();
            };
            div.appendChild(rm);
            photoPreviewEl.appendChild(div);
        });

        // "Add more" tile — shown when photos exist but max not reached
        if (count > 0 && count < MAX_ADDITIONAL_PHOTOS) {
            var belowMin   = count < MIN_ADDITIONAL_PHOTOS;
            var stillNeed  = MIN_ADDITIONAL_PHOTOS - count;

            var tile = document.createElement('button');
            tile.type = 'button';
            tile.className = 'photo-preview-add-tile';

            // Amber warning style when below the required minimum
            if (belowMin) {
                tile.style.cssText =
                    'border-color:#f59e0b;' +
                    'background:linear-gradient(135deg,#fffbeb 0%,#fef3c7 100%);' +
                    'color:#92400e;';
            }

            tile.innerHTML =
                '<i class="fas fa-plus"></i>' +
                '<span class="add-hint">' +
                    (belowMin
                        ? 'Need&nbsp;' + stillNeed + '&nbsp;more'
                        : 'Add&nbsp;More') +
                '</span>';

            tile.addEventListener('click', function () {
                document.getElementById('photos_add_more_input')?.click();
            });

            photoPreviewEl.appendChild(tile);
        }
    }
    photosInputEl?.addEventListener('change', function(e) {
        var files = Array.from(e.target.files || []);
        if (files.length > MAX_ADDITIONAL_PHOTOS) {
            cmError('Maximum ' + MAX_ADDITIONAL_PHOTOS + ' additional photos allowed.');
            return;
        }
        additionalPhotosFiles = files;
        renderAdditionalPhotosPreviews();
    });
    document.getElementById('photos_add_more_input')?.addEventListener('change', function(e) {
        var picked = Array.from(e.target.files || []);
        e.target.value = '';
        var room = MAX_ADDITIONAL_PHOTOS - additionalPhotosFiles.length;
        if (room <= 0) return;
        additionalPhotosFiles = additionalPhotosFiles.concat(picked.slice(0, room));
        setAdditionalPhotosInput(additionalPhotosFiles);
        renderAdditionalPhotosPreviews();
    });

    document.getElementById('cover_photo_input')?.addEventListener('change', function(e) {
        var file = e.target.files[0];
        var prev = document.getElementById('coverPhotoPreview');
        if (!prev) return;
        prev.innerHTML = file ? '<div class="photo-preview-item"><img src="' + URL.createObjectURL(file) + '" alt="Cover"></div>' : '';
    });

    /* ── Client-side image compression before submit ───────────────────── */
    function cmShowCompressOverlay() {
        var overlay = document.getElementById('cmCompressOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'cmCompressOverlay';
            overlay.style.cssText = 'position:fixed;inset:0;background:rgba(10,22,40,0.88);z-index:99999;display:flex;align-items:center;justify-content:center;flex-direction:column;color:#fff;padding:1rem;backdrop-filter:blur(3px);';
            overlay.innerHTML =
                '<div style="width:60px;height:60px;border:4px solid rgba(240,208,96,0.2);border-top-color:#f0d060;border-radius:50%;animation:cmSpin 0.8s linear infinite;"></div>' +
                '<p style="margin-top:1.5rem;font-size:1rem;font-weight:700;letter-spacing:0.02em;">Preparing your submission</p>' +
                '<p id="cmCompressProgress" style="margin-top:0.4rem;font-size:0.85rem;color:rgba(255,255,255,0.75);">Optimizing photos…</p>';
            document.body.appendChild(overlay);
            var style = document.createElement('style');
            style.textContent = '@keyframes cmSpin { to { transform: rotate(360deg); } }';
            document.head.appendChild(style);
        }
        overlay.style.display = 'flex';
    }
    function cmHideCompressOverlay() {
        var overlay = document.getElementById('cmCompressOverlay');
        if (overlay) overlay.style.display = 'none';
    }
    function cmSetCompressProgress(text) {
        var el = document.getElementById('cmCompressProgress');
        if (el) el.textContent = text;
    }
    async function cmCompressInputFiles(input, label) {
        if (!input || !input.files || !input.files.length) return;
        if (typeof imageCompression === 'undefined') return; // lib failed to load — submit as-is
        var files = Array.from(input.files);
        var out = [];
        var opts = { maxSizeMB: 1.5, maxWidthOrHeight: 1920, useWebWorker: true, fileType: 'image/jpeg', initialQuality: 0.82 };
        for (var i = 0; i < files.length; i++) {
            var f = files[i];
            // Skip non-images and already-small files
            if (!f.type || !f.type.startsWith('image/') || f.size < 500 * 1024) { out.push(f); continue; }
            cmSetCompressProgress('Optimizing ' + label + ' ' + (i + 1) + ' of ' + files.length + '…');
            try {
                var c = await imageCompression(f, opts);
                var newName = f.name.replace(/\.[^.]+$/, '') + '.jpg';
                out.push(new File([c], newName, { type: 'image/jpeg', lastModified: Date.now() }));
            } catch (err) {
                out.push(f); // fall back to original
            }
        }
        try {
            var dt = new DataTransfer();
            out.forEach(function(f) { dt.items.add(f); });
            input.files = dt.files;
        } catch (e) { /* DataTransfer unsupported — leave as-is */ }
    }
    document.getElementById('listingForm')?.addEventListener('submit', async function(e) {
        if (this._cmReady) return; // already compressed — let it submit
        e.preventDefault();
        var submitBtn = this.querySelector('[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        cmShowCompressOverlay();
        try {
            await cmCompressInputFiles(document.getElementById('cover_photo_input'), 'cover photo');
            await cmCompressInputFiles(document.getElementById('photos_input'), 'photo');
            cmSetCompressProgress('Uploading…');
        } catch (err) { /* swallow — submit anyway */ }
        this._cmReady = true;
        this.submit();
    });

    @if(session('error_section'))
    document.addEventListener('DOMContentLoaded', function() {
        var n = parseInt('{{ str_replace("section", "", session("error_section")) }}', 10);
        if (n) showSection(n);
    });
    @endif

    function deleteListingPhoto(imageId, listingId) {
        if (!confirm('Remove this photo from the listing?')) return;
        fetch('/seller/listings/' + listingId + '/images/' + imageId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var el = document.getElementById('existing-photo-' + imageId);
                if (el) {
                    el.style.transition = 'opacity 0.2s';
                    el.style.opacity = '0';
                    setTimeout(function() {
                        el.remove();
                        var countEl = document.getElementById('existingPhotoCount');
                        if (countEl) {
                            var grid = document.getElementById('existingPhotosGrid');
                            var remaining = grid ? grid.querySelectorAll('[id^="existing-photo-"]').length : 0;
                            countEl.textContent = remaining + ' saved';
                        }
                    }, 220);
                }
            } else {
                alert(data.error || 'Could not delete photo. Please try again.');
            }
        })
        .catch(function() { alert('Network error. Please try again.'); });
    }
</script>
