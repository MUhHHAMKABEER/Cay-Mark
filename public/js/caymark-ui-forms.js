/**
 * CayMark UI — Section 2: Forms & Input
 * Requires CaymarkUI (caymark-ui.js) optional for toast feedback on submit errors.
 */
(function (global) {
    'use strict';

    var EYE_OPEN = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
    var EYE_CLOSED = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>';

    function digitsOnly(str) {
        return String(str).replace(/\D/g, '');
    }

    function cssEscape(id) {
        if (global.CSS && global.CSS.escape) return global.CSS.escape(id);
        return String(id).replace(/["\\]/g, '\\$&');
    }

    function getFieldLabel(field) {
        if (field.dataset.cmLabel) return field.dataset.cmLabel;
        var id = field.id;
        if (id) {
            var label = document.querySelector('label[for="' + cssEscape(id) + '"]');
            if (label) {
                return label.textContent.replace(/\*/g, '').replace(/\s+/g, ' ').trim();
            }
        }
        var name = field.name || 'This field';
        return name.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
    }

    function getWrap(field) {
        return field.closest('.cm-field-wrap') || field.closest('.group') || field.parentElement;
    }

    function clearFieldError(field) {
        field.classList.remove('cm-field-invalid');
        var wrap = getWrap(field);
        if (!wrap) return;
        wrap.querySelectorAll('.cm-field-error').forEach(function (el) {
            if (el.dataset.cmFor === (field.name || field.id || '')) el.remove();
        });
    }

    function showFieldError(field, message) {
        clearFieldError(field);
        field.classList.add('cm-field-invalid');
        var err = document.createElement('p');
        err.className = 'cm-field-error';
        err.setAttribute('role', 'alert');
        err.dataset.cmFor = field.name || field.id || '';
        err.textContent = message;
        getWrap(field).appendChild(err);
    }

    function scorePassword(pw) {
        if (!pw) return { level: 'weak', score: 0, pct: 0 };
        var s = 0;
        if (pw.length >= 8) s++;
        if (pw.length >= 12) s++;
        if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) s++;
        if (/\d/.test(pw)) s++;
        if (/[^A-Za-z0-9]/.test(pw)) s++;
        var level = s <= 2 ? 'weak' : (s <= 3 ? 'fair' : 'strong');
        var pct = level === 'weak' ? 33 : (level === 'fair' ? 66 : 100);
        return { level: level, score: s, pct: pct };
    }

    function validateField(field) {
        if (field.disabled || field.type === 'hidden' || field.type === 'submit' || field.type === 'button') {
            return true;
        }

        var val = (field.type === 'checkbox' || field.type === 'radio') ? (field.checked ? '1' : '') : String(field.value || '').trim();
        var label = getFieldLabel(field);
        var customMsg = field.dataset.cmError;

        if (field.required || field.dataset.cmRequired === 'true') {
            if (field.type === 'checkbox' && !field.checked) {
                showFieldError(field, customMsg || 'You must agree to continue.');
                return false;
            }
            if (field.type !== 'checkbox' && field.type !== 'radio' && val === '') {
                showFieldError(field, customMsg || (label + ' is required.'));
                return false;
            }
        }

        if (val === '' && !field.required) {
            clearFieldError(field);
            return true;
        }

        if (field.type === 'email' || field.dataset.cmValidate === 'email') {
            var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRe.test(val)) {
                showFieldError(field, customMsg || 'Please enter a valid email address.');
                return false;
            }
        }

        var minLen = field.minLength > 0 ? field.minLength : parseInt(field.getAttribute('minlength') || '0', 10);
        if (minLen > 0 && val.length < minLen) {
            showFieldError(field, customMsg || (label + ' must be at least ' + minLen + ' characters.'));
            return false;
        }

        if (field.type === 'password' && field.dataset.cmValidate === 'password-register') {
            if (val.length < 8 || val.length > 15) {
                showFieldError(field, customMsg || 'Password must be 8–15 characters.');
                return false;
            }
            if (!/[A-Z]/.test(val) || !/\d/.test(val) || !/[^A-Za-z0-9]/.test(val)) {
                showFieldError(field, customMsg || 'Password needs uppercase, a number, and a special character.');
                return false;
            }
        }

        if (field.dataset.cmMatch) {
            var other = document.querySelector(field.dataset.cmMatch);
            if (other && val !== String(other.value || '').trim()) {
                showFieldError(field, customMsg || 'Passwords do not match.');
                return false;
            }
        }

        if (field.classList.contains('js-digits-only') || field.dataset.cmValidate === 'phone' || field.classList.contains('js-phone-format')) {
            var d = digitsOnly(field.value);
            if (field.required && d.length < 7) {
                showFieldError(field, customMsg || 'Phone number is invalid.');
                return false;
            }
        }

        if (field.validity && !field.validity.valid && field.validationMessage) {
            showFieldError(field, customMsg || field.validationMessage);
            return false;
        }

        clearFieldError(field);
        return true;
    }

    function validateForm(form) {
        var fields = form.querySelectorAll('input, select, textarea');
        var valid = true;
        var firstInvalid = null;

        fields.forEach(function (field) {
            if (!validateField(field)) {
                valid = false;
                if (!firstInvalid) firstInvalid = field;
            }
        });

        if (!valid && firstInvalid) {
            firstInvalid.focus();
            if (global.CaymarkUI && CaymarkUI.showError) {
                CaymarkUI.showError('Please fix the errors below', 'Some required fields are missing or invalid.');
            }
        }

        return valid;
    }

    function attachFormValidation(form) {
        if (form.dataset.cmValidateBound === '1') return;
        form.dataset.cmValidateBound = '1';

        if (!form.hasAttribute('novalidate')) {
            form.setAttribute('novalidate', 'novalidate');
        }

        form.addEventListener('submit', function (e) {
            if (form.dataset.cmValidate === 'off') return;
            if (!validateForm(form)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });

        form.querySelectorAll('input, select, textarea').forEach(function (field) {
            field.addEventListener('blur', function () {
                if (form.dataset.cmValidate === 'off') return;
                validateField(field);
            });
            field.addEventListener('input', function () {
                if (field.classList.contains('cm-field-invalid')) {
                    validateField(field);
                }
            });
        });
    }

    function hasExistingPasswordToggle(field) {
        var parent = field.parentElement;
        if (!parent) return false;
        return !!parent.querySelector('button[aria-label*="password" i], button[aria-label*="Show" i], .cm-password-toggle');
    }

    function initPasswordToggle(field) {
        if (field.dataset.cmPasswordToggle === '1' || hasExistingPasswordToggle(field)) return;
        field.dataset.cmPasswordToggle = '1';

        var wrap = document.createElement('div');
        wrap.className = 'cm-password-wrap cm-field-wrap';
        field.parentNode.insertBefore(wrap, field);
        wrap.appendChild(field);

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'cm-password-toggle';
        btn.setAttribute('aria-label', 'Show password');
        btn.innerHTML = EYE_OPEN;
        wrap.appendChild(btn);

        btn.addEventListener('click', function () {
            var show = field.type === 'password';
            field.type = show ? 'text' : 'password';
            btn.innerHTML = show ? EYE_CLOSED : EYE_OPEN;
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    }

    function initPasswordStrength(field) {
        if (field.dataset.cmStrengthInit === '1') return;
        field.dataset.cmStrengthInit = '1';

        var meter = document.createElement('div');
        meter.className = 'cm-password-strength';
        meter.innerHTML =
            '<div class="cm-password-strength__bar"><div class="cm-password-strength__fill"></div></div>' +
            '<span class="cm-password-strength__label">Enter a password</span>';

        var wrap = getWrap(field);
        wrap.appendChild(meter);

        var fill = meter.querySelector('.cm-password-strength__fill');
        var label = meter.querySelector('.cm-password-strength__label');

        function update() {
            var r = scorePassword(field.value);
            fill.style.width = r.pct + '%';
            fill.className = 'cm-password-strength__fill cm-password-strength__fill--' + r.level;
            label.textContent = field.value ? (r.level.charAt(0).toUpperCase() + r.level.slice(1)) : 'Enter a password';
            label.className = 'cm-password-strength__label cm-password-strength__label--' + r.level;
        }

        field.addEventListener('input', update);
        update();
    }

    function getCountryCodeForPhoneInput(input) {
        var sel = input.dataset.phoneCountrySelect;
        if (sel) {
            var el = document.querySelector(sel);
            if (el && el.value) return digitsOnly(el.value);
        }
        var wrap = input.closest('[data-phone-country]');
        if (wrap) return digitsOnly(wrap.dataset.phoneCountry || '1242');
        return '1242';
    }

    function formatNationalPhone(digits, countryCode) {
        digits = digitsOnly(digits);
        var cc = countryCode.replace(/^\+/, '');

        if (cc === '1242' || cc === '242') {
            var local = digits;
            if (local.indexOf('1242') === 0) local = local.slice(4);
            if (local.indexOf('242') === 0) local = local.slice(3);
            local = local.slice(0, 7);
            if (!local.length) return '';
            if (local.length <= 3) return '(242) ' + local;
            return '(242) ' + local.slice(0, 3) + '-' + local.slice(3, 7);
        }

        digits = digits.slice(0, 10);
        if (digits.length <= 3) return digits.length ? '(' + digits.slice(0, 3) + ')' : '';
        if (digits.length <= 6) return '(' + digits.slice(0, 3) + ') ' + digits.slice(3);
        return '(' + digits.slice(0, 3) + ') ' + digits.slice(3, 6) + '-' + digits.slice(6, 10);
    }

    function bindDigitsOnly(el) {
        if (el.dataset.digitsOnlyBound === '1') return;
        el.dataset.digitsOnlyBound = '1';

        el.addEventListener('keydown', function (e) {
            if (e.ctrlKey || e.metaKey || e.altKey) return;
            var allowed = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];
            if (allowed.indexOf(e.key) !== -1) return;
            if (e.key.length === 1 && !/\d/.test(e.key)) e.preventDefault();
        });
    }

    function bindPhoneFormat(input) {
        if (input.dataset.phoneFormatBound === '1') return;
        input.dataset.phoneFormatBound = '1';
        bindDigitsOnly(input);

        function refresh() {
            var raw = digitsOnly(input.value);
            var cc = getCountryCodeForPhoneInput(input);
            var max = (cc === '1242' || cc === '242') ? 7 : 10;
            raw = raw.slice(0, max);
            input.value = formatNationalPhone(raw, cc);
            input.dataset.cmPhoneRaw = raw;
        }

        input.addEventListener('input', refresh);
        input.addEventListener('blur', refresh);

        var sel = input.dataset.phoneCountrySelect;
        if (sel) {
            var countryEl = document.querySelector(sel);
            if (countryEl) countryEl.addEventListener('change', refresh);
        }

        refresh();
    }

    function initCharCounter(textarea) {
        if (textarea.dataset.cmCharcountInit === '1') return;
        textarea.dataset.cmCharcountInit = '1';

        var max = parseInt(textarea.getAttribute('maxlength') || textarea.dataset.charMax || '0', 10);
        if (!max || max < 1) return;

        var wrap = textarea.parentElement;
        if (!wrap.classList.contains('cm-textarea-wrap')) {
            var w = document.createElement('div');
            w.className = 'cm-textarea-wrap cm-field-wrap';
            textarea.parentNode.insertBefore(w, textarea);
            w.appendChild(textarea);
            wrap = w;
        }

        var counter = document.createElement('span');
        counter.className = 'cm-char-counter';
        counter.setAttribute('aria-live', 'polite');
        wrap.appendChild(counter);

        function update() {
            var len = textarea.value.length;
            counter.textContent = len + ' / ' + max;
            counter.classList.toggle('is-near-limit', len >= max * 0.85 && len < max);
            counter.classList.toggle('is-at-limit', len >= max);
        }

        textarea.addEventListener('input', update);
        update();
    }

    function initAll() {
        document.querySelectorAll('form').forEach(function (form) {
            if (form.dataset.cmValidate === 'off') return;
            if ((form.method || 'get').toLowerCase() === 'get') return;
            attachFormValidation(form);
        });

        document.querySelectorAll('input[type="password"]').forEach(initPasswordToggle);

        document.querySelectorAll('[data-password-strength]').forEach(function (el) {
            initPasswordStrength(el);
        });

        document.querySelectorAll('.js-digits-only').forEach(function (el) {
            bindDigitsOnly(el);
        });

        document.querySelectorAll('.js-phone-format').forEach(bindPhoneFormat);

        document.querySelectorAll('textarea[maxlength], textarea[data-char-max]').forEach(initCharCounter);
    }

    var formsApi = {
        validateField: validateField,
        validateForm: validateForm,
        attach: attachFormValidation,
        scorePassword: scorePassword,
        formatPhone: formatNationalPhone,
        init: initAll,
    };

    global.CaymarkUI = global.CaymarkUI || {};
    global.CaymarkUI.forms = formsApi;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})(typeof window !== 'undefined' ? window : this);
