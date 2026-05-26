@extends('layouts.welcome')
@section('title', 'Sign In — CayMark Island Exchange')
@section('content')

<div class="min-h-[calc(100vh-160px)] flex items-stretch">

    {{-- ── Left brand panel (hidden on mobile) ─────────────────── --}}
    <div class="hidden lg:flex flex-col justify-between w-[420px] flex-shrink-0 bg-primary px-12 py-16 relative overflow-hidden">
        {{-- grid texture --}}
        <div class="absolute inset-0 opacity-[0.04]"
             style="background-image:repeating-linear-gradient(0deg,#fff 0,#fff 1px,transparent 1px,transparent 40px),repeating-linear-gradient(90deg,#fff 0,#fff 1px,transparent 1px,transparent 40px)"></div>

        {{-- top --}}
        <div class="relative">
            <div class="inline-flex items-center gap-2 mb-12">
                <span class="text-secondary-fixed-dim font-bold text-lg tracking-widest uppercase">CayMark</span>
            </div>
            <h2 class="text-3xl font-bold text-white font-headline-lg uppercase tracking-tight leading-tight mb-4">
                Welcome<br/>Back
            </h2>
            <p class="text-white/60 text-sm leading-relaxed">
                Sign in to access your dashboard, manage bids, and continue where you left off.
            </p>
        </div>

        {{-- trust items --}}
        <div class="relative space-y-5">
            @php
            $trust = [
                ['icon'=>'lock','text'=>'Secure, encrypted sign-in'],
                ['icon'=>'verified_user','text'=>'Identity-verified platform'],
                ['icon'=>'support_agent','text'=>'Support available 24/7'],
            ];
            @endphp
            @foreach($trust as $t)
            <div class="flex items-center gap-4">
                <div class="w-9 h-9 border border-white/20 flex items-center justify-center flex-shrink-0" style="border-radius:0">
                    <span class="material-symbols-outlined text-secondary-fixed-dim text-[18px]">{{ $t['icon'] }}</span>
                </div>
                <span class="text-white/70 text-sm">{{ $t['text'] }}</span>
            </div>
            @endforeach

            <div class="pt-6 border-t border-white/10">
                <p class="text-white/40 text-xs">Don't have an account?</p>
                <a href="{{ route('register') }}" class="text-secondary-fixed-dim text-sm font-bold hover:text-[#b8943b] transition-colors">
                    Create one — it's free to start →
                </a>
            </div>
        </div>
    </div>

    {{-- ── Right form panel ──────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col justify-center px-6 py-12 lg:px-16 bg-white">
        <div class="w-full max-w-md mx-auto">

            {{-- mobile brand --}}
            <div class="lg:hidden mb-8">
                <span class="text-primary font-bold tracking-widest uppercase text-sm">CayMark</span>
            </div>

            <div class="mb-8">
                <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-2">Account Access</p>
                <h1 class="text-3xl font-bold text-primary uppercase tracking-tight font-headline-lg">Sign In</h1>
                <p class="text-gray-400 text-sm mt-2">Enter your credentials to continue.</p>
            </div>

            {{-- Flash messages --}}
            <div class="space-y-3 mb-6" role="status" aria-live="polite">
                @if (session('status'))
                    <div class="border-l-4 border-green-500 bg-green-50 px-4 py-3 flex items-center gap-3 text-sm text-green-800" style="border-radius:0">
                        <span class="material-symbols-outlined text-green-500 text-[18px] flex-shrink-0">check_circle</span>
                        {{ session('status') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="border-l-4 border-error bg-red-50 px-4 py-3 flex items-center gap-3 text-sm text-red-800" style="border-radius:0">
                        <span class="material-symbols-outlined text-error text-[18px] flex-shrink-0">error</span>
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="border-l-4 border-amber-400 bg-amber-50 px-4 py-3 text-sm text-amber-800" style="border-radius:0">
                        <p class="font-bold mb-1">Please fix the following:</p>
                        <ul class="space-y-0.5 list-disc list-inside text-amber-700">
                            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('login') }}" novalidate class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                    <div class="flex items-center border-2 border-gray-200 focus-within:border-primary transition-colors" style="border-radius:0">
                        <span class="material-symbols-outlined text-gray-300 text-[20px] flex-shrink-0 ml-4">mail</span>
                        <input type="email" id="email" name="email" required autocomplete="email"
                               value="{{ old('email') }}"
                               placeholder="you@example.com"
                               class="flex-1 px-4 py-3.5 bg-transparent text-gray-900 placeholder-gray-300 focus:outline-none text-sm font-medium"
                               style="border-radius:0"/>
                    </div>
                    @error('email') <p class="text-xs text-error mt-1.5">{{ $message }}</p> @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Password</label>
                    <div class="flex items-center border-2 border-gray-200 focus-within:border-primary transition-colors" style="border-radius:0">
                        <span class="material-symbols-outlined text-gray-300 text-[20px] flex-shrink-0 ml-4">lock</span>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="flex-1 px-4 py-3.5 bg-transparent text-gray-900 placeholder-gray-300 focus:outline-none text-sm font-medium"
                               style="border-radius:0"/>
                        <button type="button" id="toggle-password"
                                class="mr-4 text-gray-300 hover:text-gray-500 transition-colors focus:outline-none flex-shrink-0"
                                aria-label="Toggle password visibility">
                            <svg id="eye-icon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg id="eye-slash-icon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>
                    @error('password') <p class="text-xs text-error mt-1.5">{{ $message }}</p> @enderror
                </div>

                {{-- Remember & Forgot --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" id="remember_me" name="remember"
                               class="w-4 h-4 border-gray-300 text-primary focus:ring-primary/20"
                               style="border-radius:0"/>
                        <span class="text-sm text-gray-500">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm font-semibold text-primary hover:text-[#003377] transition-colors">
                            Forgot password?
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-4 bg-primary text-white font-bold uppercase tracking-widest text-sm hover:bg-[#003377] transition-colors flex items-center justify-center gap-2"
                        style="border-radius:0">
                    <span class="material-symbols-outlined text-[18px]">login</span>
                    Sign In
                </button>

                <p class="text-center text-sm text-gray-400 pt-1">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-bold text-primary hover:text-[#003377] transition-colors">Create one</a>
                </p>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var btn   = document.getElementById('toggle-password');
    var input = document.getElementById('password');
    var eye   = document.getElementById('eye-icon');
    var slash = document.getElementById('eye-slash-icon');
    if (btn && input && eye && slash) {
        btn.addEventListener('click', function() {
            if (input.type === 'password') {
                input.type = 'text'; eye.classList.add('hidden'); slash.classList.remove('hidden');
            } else {
                input.type = 'password'; eye.classList.remove('hidden'); slash.classList.add('hidden');
            }
        });
    }
});
</script>

@endsection
