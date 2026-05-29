@extends('layouts.welcome')
@section('title', 'Create Account — CayMark Island Exchange')
@section('content')

<x-auth.split maxw="540px">

    {{-- ══ Step indicator (1 Create Account · 2 Select Membership · 3 Verify & Pay) ══ --}}
    <div class="mb-8">
        <div class="cm-step-row">
            <div class="cm-step">
                <div class="cm-step-dot is-active">1</div>
                <span class="cm-step-label is-active">Create Account</span>
            </div>
            <div class="cm-step-line"></div>
            <div class="cm-step">
                <div class="cm-step-dot is-upcoming">2</div>
                <span class="cm-step-label is-upcoming">Select Membership</span>
            </div>
            <div class="cm-step-line"></div>
            <div class="cm-step">
                <div class="cm-step-dot is-upcoming">3</div>
                <span class="cm-step-label is-upcoming">Verify &amp; Pay</span>
            </div>
        </div>
    </div>

    {{-- Heading --}}
    <div class="mb-7">
        <h1 class="text-[28px] font-bold leading-tight" style="color:#1A1A1A">Create Your Account</h1>
        <p class="text-sm mt-1" style="color:#6B7280">Join CayMark and start buying or selling today</p>
    </div>

    {{-- Alerts --}}
    @if (session('error') || session('success') || $errors->any())
        <div class="space-y-3 mb-6">
            @if (session('error'))
                <div class="flex items-start gap-2.5 px-4 py-3 rounded-lg text-sm" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
                    <span class="material-symbols-outlined" style="font-size:18px;color:#DC2626">error</span>{{ session('error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="flex items-start gap-2.5 px-4 py-3 rounded-lg text-sm" style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0">
                    <span class="material-symbols-outlined" style="font-size:18px;color:#16A34A">check_circle</span>{{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="px-4 py-3 rounded-lg text-sm" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
                    <p class="font-semibold mb-1">Please fix the following:</p>
                    <ul class="space-y-0.5 list-disc list-inside" style="color:#b91c1c">
                        @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('register.step1') }}" id="step1-form" novalidate class="space-y-5">
        @csrf

        {{-- First + Last name --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="cm-auth-label">First Name</label>
                <div class="cm-auth-fieldwrap">
                    <span class="material-symbols-outlined cm-auth-licon">person</span>
                    <input type="text" id="first_name" name="first_name" required
                           value="{{ old('first_name', request()->query('first_name', '')) }}"
                           placeholder="First name"
                           class="cm-auth-input has-licon {{ $errors->has('first_name') ? 'is-error' : '' }}"/>
                </div>
                @error('first_name')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="last_name" class="cm-auth-label">Last Name</label>
                <div class="cm-auth-fieldwrap">
                    <span class="material-symbols-outlined cm-auth-licon">person</span>
                    <input type="text" id="last_name" name="last_name" required
                           value="{{ old('last_name', request()->query('last_name', '')) }}"
                           placeholder="Last name"
                           class="cm-auth-input has-licon {{ $errors->has('last_name') ? 'is-error' : '' }}"/>
                </div>
                @error('last_name')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="cm-auth-label">Email Address</label>
            <div class="cm-auth-fieldwrap">
                <span class="material-symbols-outlined cm-auth-licon">mail</span>
                <input type="email" id="email" name="email" required
                       value="{{ old('email', request()->query('email', '')) }}"
                       placeholder="Enter your email address"
                       class="cm-auth-input has-licon {{ $errors->has('email') ? 'is-error' : '' }}"/>
            </div>
            @error('email')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
        </div>

        {{-- ══ Mobile number + SMS verification (functionality preserved) ══ --}}
        @php
            $registrationDialRows = config('phone_country_codes', []);
            $regPhoneVerified     = session('registration_phone_verified') && session('registration_verified_phone');
            $regVerifiedDigits    = (string) session('registration_verified_phone', '');
            $regDisplayCountry    = '1242';
            $regDisplayNational   = '';
            if ($regPhoneVerified && $regVerifiedDigits !== '') {
                $sortedDial = collect($registrationDialRows)->sortByDesc(fn ($r) => strlen((string) ($r['code'] ?? '')));
                foreach ($sortedDial as $row) {
                    $c = (string) ($row['code'] ?? '');
                    if ($c !== '' && str_starts_with($regVerifiedDigits, $c)) {
                        $regDisplayCountry  = $c;
                        $regDisplayNational = substr($regVerifiedDigits, strlen($c)) ?: '';
                        break;
                    }
                }
                if ($regDisplayNational === '' && $regVerifiedDigits !== '') $regDisplayNational = $regVerifiedDigits;
            }
        @endphp

        <div class="rounded-xl p-4" style="border:1px solid #E2E5E9;background:#F5F6F7">
            <label class="cm-auth-label" style="margin-bottom:10px">
                Mobile Number <span style="color:#9CA3AF;font-weight:400">(optional)</span>
            </label>

            <div class="flex flex-col sm:flex-row gap-3">
                <div class="cm-auth-fieldwrap sm:w-2/5">
                    <select id="reg_phone_country" name="phone_country"
                            class="cm-auth-input cm-auth-select"
                            @if ($regPhoneVerified) disabled @endif>
                        @foreach ($registrationDialRows as $row)
                            <option value="{{ $row['code'] }}" @selected((string) ($row['code'] ?? '') === $regDisplayCountry)>{{ $row['label'] }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined cm-auth-chevron">expand_more</span>
                </div>
                <div class="flex gap-2 flex-grow">
                    <div class="cm-auth-fieldwrap flex-grow">
                        <span class="material-symbols-outlined cm-auth-licon">phone</span>
                        <input type="text" id="reg_phone_input" name="phone_local"
                               value="{{ old('phone_local', $regDisplayNational) }}"
                               placeholder="(242) 555-1234"
                               class="js-digits-only js-phone-format cm-auth-input has-licon"
                               data-phone-country-select="#reg_phone_country"
                               inputmode="numeric" autocomplete="tel-national"
                               data-cm-validate="phone"
                               @if ($regPhoneVerified) readonly @endif/>
                    </div>
                    <button type="button" id="reg-send-code-btn"
                            class="cm-auth-btn {{ $regPhoneVerified ? 'hidden' : '' }}"
                            style="width:auto;padding:0 18px;font-size:13px;white-space:nowrap">
                        Send Code
                    </button>
                </div>
            </div>

            <input type="hidden" name="phone_full"     id="reg_phone_full"     value="{{ $regPhoneVerified ? '+' . $regVerifiedDigits : old('phone_full', '') }}">
            <input type="hidden" name="phone_verified" id="reg_phone_verified" value="{{ $regPhoneVerified ? '1' : '0' }}">

            <div id="reg-phone-verify-row" class="flex gap-2 mt-3 {{ $regPhoneVerified ? 'hidden' : '' }}">
                <div class="cm-auth-fieldwrap flex-grow">
                    <span class="material-symbols-outlined cm-auth-licon">pin</span>
                    <input type="text" id="reg_phone_code_input" placeholder="6-digit code" maxlength="6"
                           inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code"
                           class="cm-auth-input has-licon" style="letter-spacing:.2em;font-family:monospace"/>
                </div>
                <button type="button" id="reg-verify-phone-btn"
                        class="cm-auth-btn" style="width:auto;padding:0 26px;font-size:13px">
                    Verify
                </button>
            </div>

            <div id="reg-phone-verified-badge"
                 class="inline-flex items-center gap-1.5 mt-3 px-3 py-1.5 rounded-lg text-xs font-semibold {{ !$regPhoneVerified ? 'hidden' : '' }}"
                 style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0">
                <span class="material-symbols-outlined" style="font-size:15px;color:#16A34A">check_circle</span>
                Phone verified
            </div>
            @error('phone')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
        </div>

        {{-- Password + Confirm --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="password" class="cm-auth-label">Password</label>
                <div class="cm-auth-fieldwrap">
                    <span class="material-symbols-outlined cm-auth-licon">lock</span>
                    <input type="password" id="password" name="password" required minlength="8" maxlength="15"
                           autocomplete="new-password"
                           data-password-strength data-cm-validate="password-register" data-cm-label="Password"
                           placeholder="Create a password"
                           class="cm-auth-input has-licon has-ricon {{ $errors->has('password') ? 'is-error' : '' }}"/>
                    <button type="button" class="cm-auth-eye" onclick="togglePassword('password','password-eye')" aria-label="Show password">
                        <span class="material-symbols-outlined" style="font-size:20px" id="password-eye">visibility</span>
                    </button>
                </div>
                <p class="text-xs mt-1.5" style="color:#9CA3AF">8–15 chars · uppercase · number · special char</p>
                @error('password')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="cm-auth-label">Confirm Password</label>
                <div class="cm-auth-fieldwrap">
                    <span class="material-symbols-outlined cm-auth-licon">lock_reset</span>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" maxlength="15"
                           autocomplete="new-password"
                           data-cm-match="#password" data-cm-label="Confirm password"
                           placeholder="Confirm your password"
                           class="cm-auth-input has-licon has-ricon {{ $errors->has('password_confirmation') ? 'is-error' : '' }}"/>
                    <button type="button" class="cm-auth-eye" onclick="togglePassword('password_confirmation','password-confirm-eye')" aria-label="Show password">
                        <span class="material-symbols-outlined" style="font-size:20px" id="password-confirm-eye">visibility</span>
                    </button>
                </div>
                @error('password_confirmation')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Terms --}}
        @php $agreeTermsChecked = old('agree_terms') || request()->query('agree_terms'); @endphp
        <div>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="agree_terms" value="1" required
                       class="mt-0.5 w-5 h-5 rounded flex-shrink-0" style="accent-color:#1B3A6B"
                       @if ($agreeTermsChecked) checked @endif/>
                <span class="text-sm" style="color:#374151">
                    I agree to CayMark's
                    <a href="{{ route('terms.of.service') }}" target="_blank" rel="noopener noreferrer" class="cm-auth-link">Terms of Service</a>
                    and
                    <a href="{{ route('privacy.policy') }}" target="_blank" rel="noopener noreferrer" class="cm-auth-link">Privacy Policy</a>.
                </span>
            </label>
            @error('agree_terms')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
        </div>

        {{-- Submit --}}
        <div id="reg-submit-wrap">
            <button type="submit" id="reg-submit-btn" class="cm-auth-btn">
                <span>Create Account</span>
                <span class="material-symbols-outlined" style="font-size:18px">arrow_forward</span>
            </button>
        </div>

        <p class="text-center text-sm" style="color:#6B7280">
            Already have an account?
            <a href="{{ route('login') }}" class="cm-auth-link">Sign In</a>
        </p>
    </form>

</x-auth.split>

<script>
/* ── Password show / hide ── */
function togglePassword(inputId, eyeId) {
    var input = document.getElementById(inputId);
    var icon  = document.getElementById(eyeId);
    if (!input || !icon) return;
    if (input.type === 'password') { input.type = 'text'; icon.textContent = 'visibility_off'; }
    else { input.type = 'password'; icon.textContent = 'visibility'; }
}

/* ── Phone verification AJAX (unchanged behaviour) ── */
(function () {
    var sendBtn        = document.getElementById('reg-send-code-btn');
    var verifyBtn      = document.getElementById('reg-verify-phone-btn');
    var phoneInput     = document.getElementById('reg_phone_input');
    var countrySelect  = document.getElementById('reg_phone_country');
    var phoneFull      = document.getElementById('reg_phone_full');
    var codeInput      = document.getElementById('reg_phone_code_input');
    var verifyRow      = document.getElementById('reg-phone-verify-row');
    var verifiedBadge  = document.getElementById('reg-phone-verified-badge');
    var hiddenVerified = document.getElementById('reg_phone_verified');
    var submitBtn      = document.getElementById('reg-submit-btn');
    var submitWrap     = document.getElementById('reg-submit-wrap');
    var sendUrl        = '{{ route("register.phone.send-code") }}';
    var verifyUrl      = '{{ route("register.phone.verify") }}';
    var csrf           = document.querySelector('input[name="_token"]');
    if (!csrf) return;

    function getFullPhone() {
        var code = (countrySelect && !countrySelect.disabled && countrySelect.value) ? countrySelect.value.trim() : '';
        var num  = (phoneInput && !phoneInput.readOnly && phoneInput.value) ? phoneInput.value.trim().replace(/^0+/, '') : '';
        if (!code || !num) return '';
        return '+' + code + num;
    }
    function setFullPhoneInput() { if (phoneFull) phoneFull.value = getFullPhone(); }

    function applyVerifiedUi() {
        if (verifiedBadge)  verifiedBadge.classList.remove('hidden');
        if (hiddenVerified) hiddenVerified.value = '1';
        if (verifyRow)      verifyRow.classList.add('hidden');
        if (phoneInput)     phoneInput.readOnly = true;
        if (countrySelect)  countrySelect.disabled = true;
        if (sendBtn)        sendBtn.classList.add('hidden');
        if (submitBtn)      submitBtn.disabled = false;
    }

    if (hiddenVerified && hiddenVerified.value === '1') applyVerifiedUi();

    var step1Form = document.getElementById('step1-form');
    if (step1Form) {
        step1Form.addEventListener('submit', function () {
            var phoneFullEl = document.getElementById('reg_phone_full');
            if (phoneFullEl && countrySelect && phoneInput && phoneInput.value.trim() && !phoneInput.readOnly) {
                var code = countrySelect.disabled ? '' : countrySelect.value.trim();
                var num  = phoneInput.value.trim().replace(/^0+/, '');
                if (code && num) phoneFullEl.value = '+' + code + num;
            } else if (phoneFullEl && hiddenVerified && hiddenVerified.value === '1' && !phoneFullEl.value.trim()) {
                setFullPhoneInput();
            }
        });
    }

    if (!sendBtn || !verifyBtn || !phoneInput) return;

    function cmToast(type, title, sub) {
        if (window.CaymarkUI) {
            type === 'success' ? CaymarkUI.showSuccess(title, sub || '') : CaymarkUI.showError(title, sub || '');
        } else { alert(title + (sub ? '\n' + sub : '')); }
    }

    sendBtn.addEventListener('click', function () {
        var phone = getFullPhone();
        if (!phone) { cmToast('error', 'Phone number required', 'Please select a country code and enter your number.'); return; }
        setFullPhoneInput();
        sendBtn.disabled = true; sendBtn.textContent = 'Sending…';
        fetch(sendUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf.value, 'Accept': 'application/json' },
            body: JSON.stringify({ phone: phone })
        }).then(function (r) { return r.json(); }).then(function (data) {
            sendBtn.disabled = false; sendBtn.textContent = 'Send Code';
            if (data.success) {
                if (verifyRow) verifyRow.classList.remove('hidden');
                if (codeInput) { codeInput.value = ''; codeInput.focus(); }
                cmToast('success', 'Code sent!', data.message || 'Enter the 6-digit code sent to your phone.');
            } else { cmToast('error', 'Could not send code', data.message || 'Please check your number and try again.'); }
        }).catch(function () { sendBtn.disabled = false; sendBtn.textContent = 'Send Code'; cmToast('error', 'Request failed', 'Check your connection and try again.'); });
    });

    verifyBtn.addEventListener('click', function () {
        var phone = getFullPhone();
        var code  = (codeInput && codeInput.value) ? codeInput.value.trim() : '';
        if (!phone || !code) { cmToast('error', 'Missing information', 'Enter your phone number and the 6-digit code.'); return; }
        setFullPhoneInput();
        verifyBtn.disabled = true; verifyBtn.textContent = 'Verifying…';
        fetch(verifyUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf.value, 'Accept': 'application/json' },
            body: JSON.stringify({ phone: phone, code: code })
        }).then(function (r) { return r.json(); }).then(function (data) {
            verifyBtn.disabled = false; verifyBtn.textContent = 'Verify';
            if (data.success) { setFullPhoneInput(); applyVerifiedUi(); cmToast('success', 'Phone verified!', 'Your number has been confirmed.'); }
            else { cmToast('error', 'Verification failed', data.message || 'Invalid or expired code.'); }
        }).catch(function () { verifyBtn.disabled = false; verifyBtn.textContent = 'Verify'; cmToast('error', 'Request failed', 'Check your connection and try again.'); });
    });
})();
</script>

@endsection
