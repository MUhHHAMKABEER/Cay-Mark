@extends('layouts.welcome')
@section('title', 'Sign In — CayMark Island Exchange')
@section('content')

<x-auth.split maxw="440px">

    {{-- Heading --}}
    <div class="mb-8">
        <h1 class="text-[28px] font-bold leading-tight" style="color:#1A1A1A">Welcome Back</h1>
        <p class="text-sm mt-1" style="color:#6B7280">Sign in to your CayMark account</p>
    </div>

    {{-- Flash messages --}}
    @if (session('status') || session('error') || $errors->any())
        <div class="space-y-3 mb-6" role="status" aria-live="polite">
            @if (session('status'))
                <div class="flex items-center gap-2.5 px-4 py-3 rounded-lg text-sm" style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0">
                    <span class="material-symbols-outlined" style="font-size:18px;color:#16A34A">check_circle</span>
                    {{ session('status') }}
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-center gap-2.5 px-4 py-3 rounded-lg text-sm" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
                    <span class="material-symbols-outlined" style="font-size:18px;color:#DC2626">error</span>
                    {{ session('error') }}
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

    <form method="POST" action="{{ route('login') }}" id="cm-login-form" novalidate class="space-y-5">
        @csrf

        {{-- Email --}}
        <div class="cm-field-wrap">
            <label for="email" class="cm-auth-label">Email Address</label>
            <div class="cm-auth-fieldwrap">
                <span class="material-symbols-outlined cm-auth-licon">mail</span>
                <input type="email" id="email" name="email" required autocomplete="email"
                       value="{{ old('email') }}" placeholder="Enter your email"
                       class="cm-auth-input has-licon {{ $errors->has('email') ? 'is-error' : '' }}"/>
            </div>
            @error('email')
                <p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="cm-field-wrap">
            <label for="password" class="cm-auth-label">Password</label>
            <div class="cm-auth-fieldwrap">
                <span class="material-symbols-outlined cm-auth-licon">lock</span>
                <input type="password" id="password" name="password" required autocomplete="current-password"
                       placeholder="Enter your password"
                       class="cm-auth-input has-licon has-ricon {{ $errors->has('password') ? 'is-error' : '' }}"/>
                <button type="button" class="cm-auth-eye" id="cm-login-eye" aria-label="Show password">
                    <span class="material-symbols-outlined" style="font-size:20px">visibility</span>
                </button>
            </div>
            @error('password')
                <p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember + forgot --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" id="remember_me" name="remember"
                       class="w-4 h-4 rounded" style="accent-color:#1B3A6B"/>
                <span class="text-sm" style="color:#6B7280">Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm cm-auth-link">Forgot your password?</a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="cm-auth-btn" id="cm-login-btn">
            <span id="cm-login-btn-label">Sign In</span>
        </button>

        {{-- Divider --}}
        <div class="cm-auth-divider py-1">or</div>

        <p class="text-center text-sm" style="color:#6B7280">
            Don't have an account?
            <a href="{{ route('register') }}" class="cm-auth-link">Create one here</a>
        </p>
    </form>

</x-auth.split>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* password show / hide */
    var eyeBtn = document.getElementById('cm-login-eye');
    var pw     = document.getElementById('password');
    if (eyeBtn && pw) {
        eyeBtn.addEventListener('click', function () {
            var icon = eyeBtn.querySelector('.material-symbols-outlined');
            if (pw.type === 'password') { pw.type = 'text'; icon.textContent = 'visibility_off'; eyeBtn.setAttribute('aria-label', 'Hide password'); }
            else { pw.type = 'password'; icon.textContent = 'visibility'; eyeBtn.setAttribute('aria-label', 'Show password'); }
        });
    }

    /* loading state on submit (form still submits natively) */
    var form  = document.getElementById('cm-login-form');
    var btn   = document.getElementById('cm-login-btn');
    var label = document.getElementById('cm-login-btn-label');
    var submitting = false;
    if (form && btn && label) {
        form.addEventListener('submit', function (e) {
            if (submitting) { e.preventDefault(); return; }
            submitting = true;
            btn.classList.add('cm-loading');
            label.textContent = 'Signing in...';
            label.insertAdjacentHTML('beforebegin', '<span class="cm-spin"></span>');
        });
    }
});
</script>

@endsection
