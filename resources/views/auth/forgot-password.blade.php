@extends('layouts.welcome')
@section('title', 'Reset Password — CayMark Island Exchange')
@section('content')

<div class="min-h-[calc(100vh-160px)] flex items-center justify-center py-16 px-4 bg-[#f8fafd]">

    <div class="w-full max-w-md">

        {{-- back link --}}
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-primary transition-colors mb-8">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            Back to Sign In
        </a>

        {{-- card --}}
        <div class="bg-white border-t-4 border-primary shadow-lg overflow-hidden" style="border-radius:0">

            <div class="px-8 py-8">
                <div class="mb-8">
                    <div class="w-12 h-12 bg-primary flex items-center justify-center mb-5" style="border-radius:0">
                        <span class="material-symbols-outlined text-white text-[22px]">key</span>
                    </div>
                    <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-2">Account Recovery</p>
                    <h1 class="text-2xl font-bold text-primary uppercase tracking-tight font-headline-md">Forgot Password</h1>
                    <p class="text-gray-400 text-sm mt-2 leading-relaxed">
                        Enter your registered email and we'll send you a secure link to reset your password.
                    </p>
                </div>

                {{-- Status --}}
                @if (session('status'))
                    <div class="border-l-4 border-green-500 bg-green-50 px-4 py-3 flex items-start gap-3 text-sm text-green-800 mb-6" style="border-radius:0">
                        <span class="material-symbols-outlined text-green-500 text-[18px] flex-shrink-0 mt-0.5">check_circle</span>
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="border-l-4 border-amber-400 bg-amber-50 px-4 py-3 text-sm text-amber-800 mb-6" style="border-radius:0">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email Address</label>
                        <div class="flex items-center border-2 border-gray-200 focus-within:border-primary transition-colors" style="border-radius:0">
                            <span class="material-symbols-outlined text-gray-300 text-[20px] flex-shrink-0 ml-4">mail</span>
                            <input type="email" id="email" name="email" required autofocus autocomplete="email"
                                   data-cm-label="Email"
                                   value="{{ old('email') }}"
                                   placeholder="you@example.com"
                                   class="flex-1 px-4 py-3.5 bg-transparent text-gray-900 placeholder-gray-300 focus:outline-none text-sm font-medium"
                                   style="border-radius:0"/>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full py-4 bg-primary text-white font-bold uppercase tracking-widest text-sm hover:bg-[#003377] transition-colors flex items-center justify-center gap-2"
                            style="border-radius:0">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        Send Reset Link
                    </button>

                    <p class="text-center text-sm text-gray-400">
                        Remembered it?
                        <a href="{{ route('login') }}" class="font-bold text-primary hover:text-[#003377] transition-colors">Sign in</a>
                    </p>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection
