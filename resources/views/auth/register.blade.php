@extends('layouts.welcome')
@section('title', 'Create Account — CayMark Island Exchange')
@section('content')

<div class="flex items-stretch" id="reg-layout" style="min-height:calc(100vh - 160px)">

    {{-- ══ Form panel ══════════════════════════════════════════════════ --}}
    <div class="flex-1 bg-white overflow-y-auto">
        <div class="w-full max-w-2xl mx-auto px-6 sm:px-10 py-8">

            {{-- Step indicator --}}
            <div class="mb-8">
                <div class="flex items-center gap-0 max-w-lg">
                    @php
                    $steps1 = [
                        ['n'=>'1','label'=>'Account','done'=>false,'active'=>true],
                        ['n'=>'2','label'=>'Role & Plan','done'=>false,'active'=>false],
                        ['n'=>'3','label'=>'Verify & Complete','done'=>false,'active'=>false],
                    ];
                    @endphp
                    @foreach($steps1 as $i => $st)
                    <div class="flex items-center {{ $i < count($steps1)-1 ? 'flex-1' : '' }}">
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <div class="w-8 h-8 flex items-center justify-center text-xs font-bold
                                        {{ $st['done'] ? 'bg-secondary-fixed-dim text-primary' : ($st['active'] ? 'bg-primary text-white' : 'bg-gray-200 text-gray-400') }}"
                                 style="border-radius:0">
                                @if($st['done'])
                                    <span class="material-symbols-outlined text-[14px]">check</span>
                                @else
                                    {{ $st['n'] }}
                                @endif
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest hidden sm:block
                                         {{ $st['active'] ? 'text-primary' : ($st['done'] ? 'text-secondary-fixed-dim' : 'text-gray-400') }}">
                                {{ $st['label'] }}
                            </span>
                        </div>
                        @if($i < count($steps1)-1)
                            <div class="flex-1 h-px mx-3 {{ $st['done'] ? 'bg-secondary-fixed-dim' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Heading --}}
            <div class="mb-8">
                <h1 class="font-headline-lg text-headline-lg text-primary mb-2">CREATE ACCOUNT</h1>
                <p class="text-on-surface-variant font-body-md text-body-md">In Step 2 you'll choose your role: <span class="font-bold">Buyer</span> or <span class="font-bold">Seller</span>.</p>
            </div>

            {{-- Alerts --}}
            @if (session('error') || session('success') || $errors->any())
            <div class="space-y-2 mb-6">
                @if (session('error'))
                    <div class="flex items-start gap-3 p-4 rounded-lg bg-error-container text-on-surface text-body-sm">
                        <span class="material-symbols-outlined text-error text-[18px] flex-shrink-0 mt-0.5">error</span>{{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="flex items-start gap-3 p-4 rounded-lg bg-green-50 text-green-800 text-body-sm">
                        <span class="material-symbols-outlined text-green-500 text-[18px] flex-shrink-0 mt-0.5">check_circle</span>{{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="p-4 rounded-lg bg-error-container/40 text-body-sm text-on-surface">
                        <p class="font-bold mb-2 text-error">Please fix the following:</p>
                        <ul class="space-y-1 list-disc list-inside text-on-surface-variant">
                            @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                        </ul>
                    </div>
                @endif
            </div>
            @endif

            <form method="POST" action="{{ route('register.step1') }}" id="step1-form" novalidate class="space-y-6">
                @csrf

                {{-- First Name + Last Name --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                    <div class="space-y-2">
                        <label for="first_name" class="text-label-md font-label-md text-on-surface-variant flex items-center gap-1">FIRST NAME <span class="text-error">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">person</span>
                            <input type="text" id="first_name" name="first_name" required
                                   value="{{ old('first_name', request()->query('first_name','')) }}"
                                   placeholder="First name"
                                   class="w-full pl-12 pr-4 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-outline/50 bg-white"/>
                        </div>
                        @error('first_name')<p class="text-label-sm font-label-sm text-error mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-2">
                        <label for="last_name" class="text-label-md font-label-md text-on-surface-variant flex items-center gap-1">LAST NAME <span class="text-error">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">person</span>
                            <input type="text" id="last_name" name="last_name" required
                                   value="{{ old('last_name', request()->query('last_name','')) }}"
                                   placeholder="Last name"
                                   class="w-full pl-12 pr-4 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-outline/50 bg-white"/>
                        </div>
                        @error('last_name')<p class="text-label-sm font-label-sm text-error mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="space-y-2">
                    <label for="email" class="text-label-md font-label-md text-on-surface-variant flex items-center gap-1">EMAIL ADDRESS <span class="text-error">*</span></label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">mail</span>
                        <input type="email" id="email" name="email" required
                               value="{{ old('email', request()->query('email','')) }}"
                               placeholder="your.email@example.com"
                               class="w-full pl-12 pr-4 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all placeholder:text-outline/50 bg-white"/>
                    </div>
                    @error('email')<p class="text-label-sm font-label-sm text-error mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                </div>

                {{-- Mobile Number --}}
                @php
                    $registrationDialRows = config('phone_country_codes', []);
                    $regPhoneVerified     = session('registration_phone_verified') && session('registration_verified_phone');
                    $regVerifiedDigits    = (string) session('registration_verified_phone', '');
                    $regDisplayCountry    = '1242';
                    $regDisplayNational   = '';
                    if ($regPhoneVerified && $regVerifiedDigits !== '') {
                        $sortedDial = collect($registrationDialRows)->sortByDesc(fn($r) => strlen((string)($r['code']??'')));
                        foreach ($sortedDial as $row) {
                            $c = (string)($row['code']??'');
                            if ($c !== '' && str_starts_with($regVerifiedDigits, $c)) {
                                $regDisplayCountry  = $c;
                                $regDisplayNational = substr($regVerifiedDigits, strlen($c)) ?: '';
                                break;
                            }
                        }
                        if ($regDisplayNational === '' && $regVerifiedDigits !== '') $regDisplayNational = $regVerifiedDigits;
                    }
                @endphp

                <div class="space-y-4 p-4 rounded-xl border border-outline-variant/30 bg-ui-soft-gray/30">
                    <label class="text-label-md font-label-md text-on-surface-variant block">
                        MOBILE NUMBER <span class="text-outline font-normal">(optional)</span>
                    </label>
                    <div class="flex flex-col md:flex-row gap-3">
                        <div class="relative w-full md:w-1/3">
                            <select id="reg_phone_country" name="phone_country"
                                class="w-full pl-4 pr-10 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white appearance-none"
                                @if($regPhoneVerified) disabled @endif>
                                @foreach($registrationDialRows as $row)
                                    <option value="{{ $row['code'] }}" @selected((string)($row['code']??'') === $regDisplayCountry)>{{ $row['label'] }}</option>
                                @endforeach
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-outline">expand_more</span>
                        </div>
                        <div class="relative flex-grow flex gap-2">
                            <div class="relative flex-grow">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">phone</span>
                                <input type="text" id="reg_phone_input" name="phone_local"
                                    value="{{ old('phone_local', $regDisplayNational) }}"
                                    placeholder="(242) 555-1234"
                                    class="js-digits-only js-phone-format w-full pl-12 pr-4 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white"
                                    data-phone-country-select="#reg_phone_country"
                                    inputmode="numeric" autocomplete="tel-national"
                                    data-cm-validate="phone"
                                    @if($regPhoneVerified) readonly @endif/>
                            </div>
                            <button type="button" id="reg-send-code-btn"
                                class="px-6 py-3 bg-primary text-on-primary rounded-lg font-label-md text-label-md hover:bg-primary-container transition-colors whitespace-nowrap {{ $regPhoneVerified ? 'hidden' : '' }}">
                                SEND CODE
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="phone_full"    id="reg_phone_full"    value="{{ $regPhoneVerified ? '+'.$regVerifiedDigits : old('phone_full','') }}">
                    <input type="hidden" name="phone_verified" id="reg_phone_verified" value="{{ $regPhoneVerified ? '1' : '0' }}">

                    <div id="reg-phone-verify-row" class="flex gap-2 {{ $regPhoneVerified ? 'hidden' : '' }}">
                        <div class="relative flex-grow">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">pin</span>
                            <input type="text" id="reg_phone_code_input" placeholder="6-digit code" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                                class="w-full pl-12 pr-4 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white font-mono tracking-widest"
                                autocomplete="one-time-code"/>
                        </div>
                        <button type="button" id="reg-verify-phone-btn"
                            class="px-8 py-3 bg-primary text-on-primary rounded-lg font-label-md text-label-md hover:bg-primary-container transition-colors">
                            VERIFY
                        </button>
                    </div>

                    <div id="reg-phone-verified-badge"
                         class="inline-flex items-center gap-2 px-3 py-1.5 text-label-sm font-label-sm text-green-800 bg-green-50 border border-green-200 rounded-lg {{ !$regPhoneVerified ? 'hidden' : '' }}">
                        <span class="material-symbols-outlined text-green-500 text-[15px]">check_circle</span>
                        Phone verified
                    </div>
                    @error('phone')<p class="text-label-sm font-label-sm text-error mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                </div>

                {{-- Password + Confirm --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-gutter">
                    <div class="space-y-2">
                        <label for="password" class="text-label-md font-label-md text-on-surface-variant flex items-center gap-1">PASSWORD <span class="text-error">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock</span>
                            <input type="password" id="password" name="password" required minlength="8" maxlength="15"
                                   autocomplete="new-password"
                                   data-password-strength data-cm-validate="password-register" data-cm-label="Password"
                                   placeholder="8–15 characters"
                                   class="w-full pl-12 pr-12 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white"/>
                            <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-outline focus:outline-none"
                                    onclick="togglePassword('password','password-eye')">
                                <span class="material-symbols-outlined text-[20px]" id="password-eye">visibility</span>
                            </button>
                        </div>
                        <p class="text-label-sm font-label-sm text-outline px-1">8–15 chars · Uppercase · Number · Special char</p>
                        @error('password')<p class="text-label-sm font-label-sm text-error mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-2">
                        <label for="password_confirmation" class="text-label-md font-label-md text-on-surface-variant flex items-center gap-1">CONFIRM PASSWORD <span class="text-error">*</span></label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline">lock_reset</span>
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" maxlength="15"
                                   autocomplete="new-password"
                                   data-cm-match="#password" data-cm-label="Confirm password"
                                   placeholder="Re-enter password"
                                   class="w-full pl-12 pr-12 py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-1 focus:ring-primary outline-none bg-white"/>
                            <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-outline focus:outline-none"
                                    onclick="togglePassword('password_confirmation','password-confirm-eye')">
                                <span class="material-symbols-outlined text-[20px]" id="password-confirm-eye">visibility</span>
                            </button>
                        </div>
                        @error('password_confirmation')<p class="text-label-sm font-label-sm text-error mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Terms + Submit --}}
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 pt-6 border-t border-outline-variant/30">
                    @php $agreeTermsChecked = old('agree_terms') || request()->query('agree_terms'); @endphp
                    <div>
                        <label class="flex items-start gap-3 cursor-pointer max-w-sm">
                            <input type="checkbox" name="agree_terms" value="1" required
                                   class="mt-1 w-5 h-5 rounded border-outline-variant text-secondary focus:ring-secondary cursor-pointer flex-shrink-0"
                                   @if($agreeTermsChecked) checked @endif/>
                            <span class="text-body-sm text-on-surface-variant">
                                I agree to CayMark's
                                <a href="{{ route('terms.of.service') }}" target="_blank" rel="noopener noreferrer"
                                   class="text-primary font-bold hover:underline">Terms</a>
                                and
                                <a href="{{ route('privacy.policy') }}" target="_blank" rel="noopener noreferrer"
                                   class="text-primary font-bold hover:underline">Privacy Policy</a>.
                            </span>
                        </label>
                        @error('agree_terms')<p class="text-label-sm font-label-sm text-error mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">error</span>{{ $message }}</p>@enderror
                    </div>
                    <div id="reg-submit-wrap">
                        <button type="submit" id="reg-submit-btn"
                                class="w-full md:w-auto px-10 py-4 bg-secondary-container text-on-secondary-container rounded-lg font-headline-sm text-headline-sm flex items-center justify-center gap-3 hover:bg-secondary transition-all active:scale-95 shadow-lg shadow-secondary/20">
                            <span class="material-symbols-outlined">person_add</span>
                            CREATE ACCOUNT
                        </button>
                    </div>
                </div>

                <p class="text-center text-body-sm text-on-surface-variant pt-1">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">Sign in</a>
                </p>

            </form>
        </div>
    </div>

</div>

<script>
/* ── Fit layout to remaining viewport height ── */
(function () {
    function fitLayout() {
        var siteHeader = document.querySelector('body > header');
        if (!siteHeader) return;
        var h = siteHeader.offsetHeight;
        var secNav = siteHeader.nextElementSibling;
        if (secNav && window.getComputedStyle(secNav).display !== 'none') h += secNav.offsetHeight;
        var layout = document.getElementById('reg-layout');
        if (layout) layout.style.minHeight = (window.innerHeight - h) + 'px';
    }
    fitLayout();
    window.addEventListener('resize', fitLayout);
})();

/* ── Password show/hide ── */
function togglePassword(inputId, eyeId) {
    var input = document.getElementById(inputId);
    var icon  = document.getElementById(eyeId);
    if (!input || !icon) return;
    if (input.type === 'password') { input.type = 'text'; icon.textContent = 'visibility_off'; }
    else { input.type = 'password'; icon.textContent = 'visibility'; }
}

/* ── Phone verification AJAX ── */
(function () {
    var sendBtn       = document.getElementById('reg-send-code-btn');
    var verifyBtn     = document.getElementById('reg-verify-phone-btn');
    var phoneInput    = document.getElementById('reg_phone_input');
    var countrySelect = document.getElementById('reg_phone_country');
    var phoneFull     = document.getElementById('reg_phone_full');
    var codeInput     = document.getElementById('reg_phone_code_input');
    var verifyRow     = document.getElementById('reg-phone-verify-row');
    var verifiedBadge = document.getElementById('reg-phone-verified-badge');
    var hiddenVerified= document.getElementById('reg_phone_verified');
    var submitBtn     = document.getElementById('reg-submit-btn');
    var submitWrap    = document.getElementById('reg-submit-wrap');
    var sendUrl       = '{{ route("register.phone.send-code") }}';
    var verifyUrl     = '{{ route("register.phone.verify") }}';
    var csrf          = document.querySelector('input[name="_token"]');
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
        if (submitWrap)     submitWrap.classList.remove('reg-pending-verify');
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
