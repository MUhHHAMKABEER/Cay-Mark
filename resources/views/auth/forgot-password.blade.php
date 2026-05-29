@extends('layouts.welcome')
@section('title', 'Reset Password — CayMark Island Exchange')
@section('content')

<x-auth.split maxw="440px">

    {{-- Heading --}}
    <div class="mb-8">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-5" style="background:#1B3A6B">
            <span class="material-symbols-outlined text-white" style="font-size:22px">key</span>
        </div>
        <h1 class="text-[28px] font-bold leading-tight" style="color:#1A1A1A">Reset Your Password</h1>
        <p class="text-sm mt-1" style="color:#6B7280">Enter your email and we'll send you a secure link to reset your password.</p>
    </div>

    {{-- Status / errors --}}
    @if (session('status'))
        <div class="flex items-start gap-2.5 px-4 py-3 rounded-lg text-sm mb-6" style="background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0">
            <span class="material-symbols-outlined" style="font-size:18px;color:#16A34A">check_circle</span>
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="px-4 py-3 rounded-lg text-sm mb-6" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
            <ul class="space-y-0.5 list-disc list-inside">
                @foreach ($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate class="space-y-5">
        @csrf

        <div class="cm-field-wrap">
            <label for="email" class="cm-auth-label">Email Address</label>
            <div class="cm-auth-fieldwrap">
                <span class="material-symbols-outlined cm-auth-licon">mail</span>
                <input type="email" id="email" name="email" required autofocus autocomplete="email"
                       data-cm-label="Email" value="{{ old('email') }}"
                       placeholder="Enter your email"
                       class="cm-auth-input has-licon {{ $errors->has('email') ? 'is-error' : '' }}"/>
            </div>
            @error('email')
                <p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="cm-auth-btn">
            <span class="material-symbols-outlined" style="font-size:18px">send</span>
            Send Reset Link
        </button>

        <a href="{{ route('login') }}" class="flex items-center justify-center gap-1.5 text-sm" style="color:#6B7280">
            <span class="material-symbols-outlined" style="font-size:16px">arrow_back</span>
            Back to Sign In
        </a>
    </form>

</x-auth.split>

@endsection
