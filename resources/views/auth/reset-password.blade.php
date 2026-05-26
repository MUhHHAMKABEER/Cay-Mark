@extends('layouts.welcome')
@section('title', 'Set New Password — CayMark Island Exchange')
@section('content')

<div class="min-h-[calc(100vh-160px)] flex items-center justify-center py-16 px-4 bg-[#f8fafd]">

    <div class="w-full max-w-md">

        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-primary transition-colors mb-8">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Back to Sign In
        </a>

        <div class="bg-white border-t-4 border-primary shadow-lg overflow-hidden" style="border-radius:0">
            <div class="px-8 py-8">
                <div class="mb-8">
                    <div class="w-12 h-12 bg-primary flex items-center justify-center mb-5" style="border-radius:0">
                        <span class="material-symbols-outlined text-white text-[22px]">lock_reset</span>
                    </div>
                    <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-2">Account Recovery</p>
                    <h1 class="text-2xl font-bold text-primary uppercase tracking-tight font-headline-md">Set New Password</h1>
                    <p class="text-gray-400 text-sm mt-2">Choose a strong password for your CayMark account.</p>
                </div>

                @if ($errors->any())
                    <div class="border-l-4 border-amber-400 bg-amber-50 px-4 py-3 text-sm text-amber-800 mb-6" style="border-radius:0">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" class="space-y-5" novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
                        <div class="flex items-center border-2 border-gray-200 focus-within:border-primary transition-colors" style="border-radius:0">
                            <span class="material-symbols-outlined text-gray-300 text-[20px] flex-shrink-0 ml-4">mail</span>
                            <input type="email" id="email" name="email" required autofocus autocomplete="username"
                                   value="{{ old('email', $request->email) }}"
                                   class="flex-1 px-4 py-3.5 bg-transparent text-gray-900 placeholder-gray-300 focus:outline-none text-sm font-medium"
                                   style="border-radius:0"/>
                        </div>
                    </div>

                    {{-- New password --}}
                    <div>
                        <label for="password" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">New Password</label>
                        <div class="flex items-center border-2 border-gray-200 focus-within:border-primary transition-colors" style="border-radius:0">
                            <span class="material-symbols-outlined text-gray-300 text-[20px] flex-shrink-0 ml-4">lock</span>
                            <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password"
                                   data-password-strength data-cm-label="New password"
                                   placeholder="8–15 characters"
                                   class="flex-1 px-4 py-3.5 bg-transparent text-gray-900 placeholder-gray-300 focus:outline-none text-sm font-medium"
                                   style="border-radius:0"/>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1.5">8–15 chars · Uppercase · Number · Special char</p>
                    </div>

                    {{-- Confirm password --}}
                    <div>
                        <label for="password_confirmation" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Confirm New Password</label>
                        <div class="flex items-center border-2 border-gray-200 focus-within:border-primary transition-colors" style="border-radius:0">
                            <span class="material-symbols-outlined text-gray-300 text-[20px] flex-shrink-0 ml-4">lock_reset</span>
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" autocomplete="new-password"
                                   data-cm-match="#password" data-cm-label="Confirm password"
                                   placeholder="Re-enter new password"
                                   class="flex-1 px-4 py-3.5 bg-transparent text-gray-900 placeholder-gray-300 focus:outline-none text-sm font-medium"
                                   style="border-radius:0"/>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-4 bg-primary text-white font-bold uppercase tracking-widest text-sm hover:bg-[#003377] transition-colors flex items-center justify-center gap-2"
                            style="border-radius:0">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        Set New Password
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
