<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
<script>
    const MAX_ADDITIONAL_PHOTOS = 14;
    const MIN_ADDITIONAL_PHOTOS = 5;
    const MAX_YEAR = {{ $maxYear ?? ((int) date('Y') + 1) }};

    /* ── Per-field validation helpers ──────────────────────────────────── */
    var CM_FIELD_LABELS = {
        make:'Make', model:'Model', year:'Year', vehicle_type:'Vehicle Type',
        island:'Island Location', color:'Exterior Color', interior_color:'Interior Color',
        title_status:'Title Status', is_salvaged:'Condition',
        run_and_drive:'Runs & Drives', engine_starts:'Engine Starts',
        keys_available:'Keys Available', primary_damage:'Primary Damage',
        secondary_damage:'Secondary Damage', cover_photo:'Cover Photo',
        photos:'Additional Photos', engine_video:'Engine Video',
        auction_duration:'Auction Duration', starting_price:'Starting Bid',
        reserve_price:'Reserve Price', buy_now_price:'Buy Now Price',
        terms_accepted:'Terms & Conditions', cardholder_name:'Cardholder Name',
        card_number:'Card Number', card_expiry:'Card Expiry', card_cvc:'Card Security Code',
    };
    var CM_SECTION_MAP = {
        make:1, model:1, year:1, vehicle_type:1, island:1, color:1, interior_color:1, vin:1,
        title_status:2, is_salvaged:2, run_and_drive:2, engine_starts:2, keys_available:2,
        primary_damage:2, secondary_damage:2, cover_photo:2, photos:2, engine_video:2, additional_notes:2,
        auction_duration:3, starting_price:3, reserve_price:3, buy_now_price:3,
        terms_accepted:3, cardholder_name:3, card_number:3, card_expiry:3, card_cvc:3,
    };

    function cmFieldToast(label, msg) {
        if (window.CaymarkUI && typeof CaymarkUI.showError === 'function') {
            CaymarkUI.showError(label, msg);
        }
    }
    function cmMarkFieldError(name) {
        var el = document.querySelector('[name="' + name + '"]');
        if (el) el.classList.add('field-error');
        return el;
    }
    function cmClearFieldErrors(names) {
        (names || []).forEach(function(n) {
            var el = document.querySelector('[name="' + n + '"]');
            if (el) el.classList.remove('field-error');
        });
    }

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

    // Fallback for non-field errors (network issues etc.)
    function cmError(msg) {
        cmFieldToast('Check your form', msg);
    }

    function validateStep1() {
        var checks = [
            { name:'make',           msg:'Make is required' },
            { name:'model',          msg:'Model is required' },
            { name:'year',           msg:'Year is required' },
            { name:'vehicle_type',   msg:'Vehicle type is required' },
            { name:'island',         msg:'Island location is required' },
            { name:'color',          msg:'Exterior color is required' },
            { name:'interior_color', msg:'Interior color is required' },
        ];
        cmClearFieldErrors(checks.map(function(c){ return c.name; }));

        var errs = [], firstEl = null;
        checks.forEach(function(c) {
            var el = document.querySelector('[name="' + c.name + '"]');
            if (!el || !String(el.value || '').trim()) {
                errs.push({ el:el, label:CM_FIELD_LABELS[c.name], msg:c.msg });
                if (el) el.classList.add('field-error');
                if (!firstEl && el) firstEl = el;
            }
        });

        // Year range check (if value exists but out of bounds)
        var yearEl = document.querySelector('[name="year"]');
        if (yearEl && String(yearEl.value || '').trim()) {
            var y = parseInt(yearEl.value, 10);
            if (isNaN(y) || y < 1995 || y > MAX_YEAR) {
                var alreadyAdded = errs.some(function(e){ return e.el === yearEl; });
                if (!alreadyAdded) {
                    errs.push({ el:yearEl, label:'Year', msg:'Year must be between 1995 and ' + MAX_YEAR });
                    yearEl.classList.add('field-error');
                    if (!firstEl) firstEl = yearEl;
                }
            }
        }

        if (errs.length) {
            errs.forEach(function(err, i) {
                setTimeout(function(){ cmFieldToast(err.label, err.msg); }, i * 180);
            });
            if (firstEl) setTimeout(function(){ firstEl.focus(); }, 60);
            return false;
        }
        return true;
    }

    function validateStep2() {
        var required = [
            { name:'title_status',      msg:'Title status is required' },
            { name:'is_salvaged',       msg:'Condition (salvaged/used) is required' },
            { name:'run_and_drive',     msg:'Runs & Drives answer is required' },
            { name:'engine_starts',     msg:'Engine Starts answer is required' },
            { name:'keys_available',    msg:'Keys Available answer is required' },
            { name:'primary_damage',    msg:'Primary damage type is required' },
            { name:'secondary_damage',  msg:'Secondary damage type is required' },
        ];
        cmClearFieldErrors(required.map(function(c){ return c.name; }));

        var errs = [], firstEl = null;
        required.forEach(function(c) {
            var el = document.querySelector('[name="' + c.name + '"]');
            if (!el || !String(el.value || '').trim()) {
                errs.push({ el:el, label:CM_FIELD_LABELS[c.name], msg:c.msg });
                if (el) el.classList.add('field-error');
                if (!firstEl && el) firstEl = el;
            }
        });

        @if(!$isEdit)
        var cover = document.getElementById('cover_photo_input');
        if (!cover || !cover.files || !cover.files.length) {
            errs.push({ el:null, label:'Cover Photo', msg:'A cover photo is required' });
        }
        var addCount = typeof additionalPhotosFiles !== 'undefined' ? additionalPhotosFiles.length : 0;
        if (addCount < MIN_ADDITIONAL_PHOTOS) {
            errs.push({ el:null, label:'Additional Photos',
                msg:'At least ' + MIN_ADDITIONAL_PHOTOS + ' additional photos required (you have ' + addCount + ')' });
        } else if (addCount > MAX_ADDITIONAL_PHOTOS) {
            errs.push({ el:null, label:'Additional Photos',
                msg:'Maximum ' + MAX_ADDITIONAL_PHOTOS + ' additional photos allowed' });
        }
        @endif

        var startsEl = document.getElementById('engine_starts_select');
        if (startsEl && startsEl.value === 'yes') {
            var vid = document.getElementById('engine_video_input');
            if (!vid || !vid.files || !vid.files.length) {
                errs.push({ el:null, label:'Engine Video', msg:'Engine video is required when engine starts is Yes' });
            } else if (typeof vid._duration === 'number' && isFinite(vid._duration)) {
                if (vid._duration < 30 || vid._duration > 60) {
                    errs.push({ el:vid, label:'Engine Video', msg:'Video must be between 30 and 60 seconds' });
                    vid.classList.add('field-error');
                }
            }
        }

        if (errs.length) {
            errs.forEach(function(err, i) {
                setTimeout(function(){ cmFieldToast(err.label, err.msg); }, i * 180);
            });
            if (firstEl) setTimeout(function(){ firstEl.focus(); }, 60);
            return false;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {

        /* ── Server-side validation errors → per-field toasts ──────────── */
        if (window.__cmValidationErrors || window.__cmSessionError) {
            var seen = {}, toFire = [], targetSection = 4;

            if (window.__cmSessionError) {
                toFire.push({ base:null, label:'Error', msg:window.__cmSessionError, sect:null });
            }
            if (window.__cmValidationErrors) {
                Object.keys(window.__cmValidationErrors).forEach(function(key) {
                    var base = key.split('.')[0];
                    if (seen[base]) return;
                    seen[base] = true;
                    var msgs  = window.__cmValidationErrors[key];
                    var msg   = Array.isArray(msgs) ? msgs[0] : String(msgs);
                    var label = CM_FIELD_LABELS[base] || base;
                    var sect  = CM_SECTION_MAP[base] || 1;
                    if (sect < targetSection) targetSection = sect;
                    toFire.push({ base:base, label:label, msg:msg, sect:sect });
                });
            }

            if (toFire.length) {
                // Navigate to the section that contains the first error
                var goTo = (window.__cmErrorSection > 0)
                    ? window.__cmErrorSection
                    : (targetSection < 4 ? targetSection : 1);
                showSection(goTo);

                toFire.forEach(function(item, i) {
                    if (item.base) cmMarkFieldError(item.base);
                    setTimeout(function(){ cmFieldToast(item.label, item.msg); }, i * 200);
                });
            }
        }
        /* ─────────────────────────────────────────────────────────────── */

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
        var opts = { maxSizeMB: 1.5, maxWidthOrHeight: 1920, useWebWorker: false, fileType: 'image/jpeg', initialQuality: 0.82 };
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
        var form = this;
        var submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;
        cmShowCompressOverlay();

        // Safety net: if compression hangs for any reason (worker crash, env issue),
        // submit the form after 30 s with the original files rather than hanging forever.
        var compressionDone = false;
        var safetyTimer = setTimeout(function() {
            if (!compressionDone) {
                compressionDone = true;
                cmSetCompressProgress('Uploading…');
                form._cmReady = true;
                form.submit();
            }
        }, 30000);

        try {
            // Race the two compression tasks against a 28-second internal deadline
            // (2 s shorter than the safety timer so the normal path always wins the race).
            var compressionWork = (async function() {
                await cmCompressInputFiles(document.getElementById('cover_photo_input'), 'cover photo');
                await cmCompressInputFiles(document.getElementById('photos_input'), 'photo');
            })();
            var compressionTimeout = new Promise(function(resolve) { setTimeout(resolve, 28000); });
            await Promise.race([compressionWork, compressionTimeout]);
            cmSetCompressProgress('Uploading…');
        } catch (err) { /* swallow — submit anyway with whatever files we have */ }

        if (!compressionDone) {
            compressionDone = true;
            clearTimeout(safetyTimer);
            form._cmReady = true;
            form.submit();
        }
    });

    {{-- error_section navigation is now handled by the per-field toast handler above --}}

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
