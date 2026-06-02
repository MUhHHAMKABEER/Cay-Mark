@extends('layouts.welcome')
@section('title', 'Verify Reset Code — CayMark')
@section('content')

<x-auth.split maxw="440px">
    <div class="mb-8">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5" style="background:#1B3A6B">
            <span class="material-symbols-outlined text-white" style="font-size:22px">mark_email_read</span>
        </div>
        <h1 class="text-[28px] font-bold leading-tight" style="color:#1A1A1A">Check Your Email</h1>
        <p class="text-sm mt-1" style="color:#6B7280">
            We sent a 6-digit code to <strong>{{ session('otp_email', 'your email') }}</strong>.
        </p>
    </div>

    @if (session('status'))
        <div class="flex items-start gap-2.5 px-4 py-3 rounded-lg text-sm mb-5" style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0">
            <span class="material-symbols-outlined" style="font-size:18px;color:#16A34A">check_circle</span>
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg text-sm mb-5" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
            @foreach ($errors->all() as $err)<p>{{ $err }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.verify.store') }}" novalidate class="space-y-5">
        @csrf
        <input type="hidden" name="email" value="{{ session('otp_email', old('email')) }}">

        {{-- 6 individual digit boxes --}}
        <div>
            <label class="cm-auth-label">Enter 6-digit code</label>
            <div id="otp-boxes" class="flex gap-2 justify-start">
                @for ($i = 0; $i < 6; $i++)
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                           id="otp_{{ $i }}"
                           class="w-12 h-14 text-center text-xl font-bold rounded-lg focus:outline-none transition-all"
                           style="border:2px solid #E2E5E9;font-family:monospace"
                           autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                           aria-label="Digit {{ $i + 1 }}">
                @endfor
            </div>
            <input type="hidden" name="code" id="otp-combined">
            @error('code')<p class="cm-auth-error mt-2"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
        </div>

        {{-- Timer --}}
        <p class="text-sm" style="color:#6B7280">
            Code expires in <span id="otp-timer" class="font-semibold" style="color:#1B3A6B">10:00</span>
        </p>

        <button type="submit" class="cm-auth-btn">
            <span class="material-symbols-outlined" style="font-size:18px">verified</span>
            Verify Code
        </button>

        <div class="flex items-center justify-between text-sm">
            <a href="{{ route('password.request') }}" class="cm-auth-link">← Back</a>
            <form method="POST" action="{{ route('password.email') }}" style="display:inline">
                @csrf
                <input type="hidden" name="email" value="{{ session('otp_email') }}">
                <button type="submit" id="resend-btn" class="cm-auth-link" disabled style="opacity:.45;cursor:not-allowed;background:none;border:none;padding:0">
                    Resend Code
                </button>
            </form>
        </div>
    </form>

</x-auth.split>

<script>
/* ── OTP digit boxes — auto-advance + backspace ── */
(function() {
    var boxes = Array.from(document.querySelectorAll('#otp-boxes input'));
    var combined = document.getElementById('otp-combined');

    function sync() {
        combined.value = boxes.map(b => b.value).join('');
    }

    boxes.forEach(function(box, i) {
        box.addEventListener('input', function(e) {
            var v = e.target.value.replace(/\D/g,'');
            e.target.value = v.slice(-1);
            sync();
            if (v && i < boxes.length - 1) boxes[i + 1].focus();
        });
        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !e.target.value && i > 0) {
                boxes[i - 1].focus();
                boxes[i - 1].value = '';
                sync();
            }
        });
        box.addEventListener('focus', function() {
            this.style.borderColor = '#1B3A6B';
            this.style.boxShadow = '0 0 0 3px rgba(27,58,107,.12)';
        });
        box.addEventListener('blur', function() {
            this.style.borderColor = '#E2E5E9';
            this.style.boxShadow = 'none';
        });
    });

    /* ── Countdown timer (10 min) ── */
    var timerEl  = document.getElementById('otp-timer');
    var resendBtn = document.getElementById('resend-btn');
    var secs = 600;
    var interval = setInterval(function() {
        secs--;
        if (secs <= 0) {
            clearInterval(interval);
            timerEl.textContent = '00:00';
            timerEl.style.color = '#DC2626';
            if (resendBtn) {
                resendBtn.disabled = false;
                resendBtn.style.opacity = '1';
                resendBtn.style.cursor  = 'pointer';
            }
            return;
        }
        var m = Math.floor(secs / 60);
        var s = secs % 60;
        timerEl.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }, 1000);
})();
</script>
@endsection
