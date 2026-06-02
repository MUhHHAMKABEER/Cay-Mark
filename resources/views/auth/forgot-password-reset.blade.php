@extends('layouts.welcome')
@section('title', 'Create New Password — CayMark')
@section('content')

<x-auth.split maxw="440px">
    <div class="mb-8">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5" style="background:#1B3A6B">
            <span class="material-symbols-outlined text-white" style="font-size:22px">lock_reset</span>
        </div>
        <h1 class="text-[28px] font-bold leading-tight" style="color:#1A1A1A">Create New Password</h1>
        <p class="text-sm mt-1" style="color:#6B7280">Choose a strong password for your CayMark account.</p>
    </div>

    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg text-sm mb-5" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
            @foreach ($errors->all() as $err)<p>{{ $err }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.reset-otp.store') }}" novalidate class="space-y-5">
        @csrf
        <input type="hidden" name="email" value="{{ session('otp_email', old('email')) }}">

        {{-- New password --}}
        <div class="cm-field-wrap">
            <label for="password" class="cm-auth-label">New Password</label>
            <div class="cm-auth-fieldwrap">
                <span class="material-symbols-outlined cm-auth-licon">lock</span>
                <input type="password" id="password" name="password" required minlength="8"
                       autocomplete="new-password"
                       data-password-strength data-cm-validate="password-register" data-cm-label="Password"
                       placeholder="Create a new password"
                       class="cm-auth-input has-licon has-ricon {{ $errors->has('password') ? 'is-error' : '' }}"/>
                <button type="button" class="cm-auth-eye" onclick="togglePassword('password','rp-eye-1')" aria-label="Show password">
                    <span class="material-symbols-outlined" style="font-size:20px" id="rp-eye-1">visibility</span>
                </button>
            </div>
            <p class="text-xs mt-1.5" style="color:#9CA3AF">8+ chars · uppercase · number · special char</p>
            @error('password')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
        </div>

        {{-- Confirm password --}}
        <div class="cm-field-wrap">
            <label for="password_confirmation" class="cm-auth-label">Confirm New Password</label>
            <div class="cm-auth-fieldwrap">
                <span class="material-symbols-outlined cm-auth-licon">lock_reset</span>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                       autocomplete="new-password"
                       data-cm-match="#password" data-cm-label="Confirm password"
                       placeholder="Re-enter new password"
                       class="cm-auth-input has-licon has-ricon {{ $errors->has('password_confirmation') ? 'is-error' : '' }}"/>
                <button type="button" class="cm-auth-eye" onclick="togglePassword('password_confirmation','rp-eye-2')" aria-label="Show password">
                    <span class="material-symbols-outlined" style="font-size:20px" id="rp-eye-2">visibility</span>
                </button>
            </div>
            @error('password_confirmation')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
        </div>

        <button type="submit" class="cm-auth-btn">
            <span class="material-symbols-outlined" style="font-size:18px">check_circle</span>
            Reset Password
        </button>

        <a href="{{ route('login') }}" class="flex items-center justify-center gap-1.5 text-sm" style="color:#6B7280">
            <span class="material-symbols-outlined" style="font-size:16px">arrow_back</span>
            Back to Sign In
        </a>
    </form>
</x-auth.split>

<script>
function togglePassword(inputId, eyeId) {
    var input = document.getElementById(inputId);
    var icon  = document.getElementById(eyeId);
    if (!input || !icon) return;
    if (input.type === 'password') { input.type = 'text'; icon.textContent = 'visibility_off'; }
    else { input.type = 'password'; icon.textContent = 'visibility'; }
}
</script>
@endsection
