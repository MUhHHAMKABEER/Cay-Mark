@extends('layouts.welcome')
@section('title', 'Sign in - CayMark')
@section('content')

<div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 bg-gradient-to-br from-slate-50 via-white to-blue-50/40">
    <!-- Subtle grid pattern -->
    <div class="fixed inset-0 opacity-[0.02] pointer-events-none" style="background-image: linear-gradient(#063466 1px, transparent 1px), linear-gradient(90deg, #063466 1px, transparent 1px); background-size: 48px 48px;"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Single focused card -->
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 border border-gray-100/80 overflow-hidden">
            <!-- Thin brand accent (replaces heavy top bar) -->
            <div class="h-1 bg-gradient-to-r from-[#063466] via-[#1e3a8a] to-[#2563eb]"></div>

            <div class="p-8 md:p-10">
                <!-- Clear, minimal header -->
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 font-heading tracking-tight">Sign in</h1>
                    <p class="text-gray-500 text-sm mt-1">Welcome back. Enter your details to continue.</p>
                    </div>

                <!-- Compact flash messages -->
                <div class="mb-6 space-y-3" role="status" aria-live="polite">
                    @if (session('status'))
                        <div class="rounded-lg bg-emerald-50 border border-emerald-200/80 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
                            <svg class="h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="rounded-lg bg-red-50 border border-red-200/80 px-4 py-3 text-sm text-red-800 flex items-center gap-2">
                            <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="rounded-lg bg-amber-50 border border-amber-200/80 px-4 py-3 text-sm text-amber-800">
                            <p class="font-medium mb-1">Please fix the following:</p>
                            <ul class="list-disc list-inside space-y-0.5 text-amber-700">
                                @foreach ($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
            </div>

                <form method="POST" action="{{ route('login') }}" novalidate class="space-y-5">
                        @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </span>
                            <input type="email" id="email" name="email" required value="{{ old('email') }}" autocomplete="email"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-[#063466] focus:ring-2 focus:ring-[#063466]/10 transition-all text-gray-900 placeholder-gray-400 text-[15px]"
                                placeholder="you@example.com">
                            @error('email')
                                <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                        </div>

                        <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </span>
                            <input type="password" id="password" name="password" required autocomplete="current-password"
                                class="w-full pl-11 pr-12 py-3 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-[#063466] focus:ring-2 focus:ring-[#063466]/10 transition-all text-gray-900 placeholder-gray-400 text-[15px]"
                                placeholder="••••••••">
                            <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600 transition-colors" aria-label="Toggle password visibility">
                                <svg id="eye-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg id="eye-slash-icon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                            @error('password')
                                <p class="text-sm text-red-600 mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="remember_me" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#063466] focus:ring-[#063466]/20">
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#063466] hover:text-[#1e3a8a] transition-colors">Forgot password?</a>
                        @endif
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#063466] hover:bg-[#052a52] text-white font-semibold text-[15px] shadow-lg shadow-[#063466]/20 hover:shadow-[#063466]/30 focus:outline-none focus:ring-2 focus:ring-[#063466] focus:ring-offset-2 transition-all duration-200">
                        Sign in
                    </button>

                    <p class="text-center text-sm text-gray-500 pt-1">
                        Don't have an account? <a href="{{ route('register') }}" class="font-semibold text-[#063466] hover:text-[#1e3a8a] transition-colors">Create one</a>
                    </p>
                </form>
            </div>
        </div>

        <!-- Single, subtle trust line (replaces 3 cards) -->
        <p class="text-center text-xs text-gray-400 mt-6 flex items-center justify-center gap-1.5">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            Secure sign-in. Your data is protected.
        </p>
    </div>
</div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var btn = document.getElementById('toggle-password');
        var input = document.getElementById('password');
        var eye = document.getElementById('eye-icon');
        var slash = document.getElementById('eye-slash-icon');
        if (btn && input && eye && slash) {
            btn.addEventListener('click', function() {
                if (input.type === 'password') {
                    input.type = 'text';
                    eye.classList.add('hidden');
                    slash.classList.remove('hidden');
            } else {
                    input.type = 'password';
                    eye.classList.remove('hidden');
                    slash.classList.add('hidden');
                }
            });
        }
        });
    </script>

@endsection
