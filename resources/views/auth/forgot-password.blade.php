@extends('layouts.welcome')
@section('title', 'Forgot password - CayMark')
@section('content')

<div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 bg-gradient-to-br from-slate-50 via-white to-blue-50/40">
    <div class="fixed inset-0 opacity-[0.02] pointer-events-none" style="background-image: linear-gradient(#063466 1px, transparent 1px), linear-gradient(90deg, #063466 1px, transparent 1px); background-size: 48px 48px;"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 border border-gray-100/80 overflow-hidden">
            <div class="h-1 bg-gradient-to-r from-[#063466] via-[#1e3a8a] to-[#2563eb]"></div>

            <div class="p-8 md:p-10">
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 font-heading tracking-tight">Forgot password</h1>
                    <p class="text-gray-500 text-sm mt-2">
                        Enter your email and we will send you a link to reset your password.
                    </p>
                </div>

                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200/80 px-4 py-3 text-sm text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200/80 px-4 py-3 text-sm text-amber-800">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                        <input type="email" id="email" name="email" required autofocus autocomplete="email"
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-[#063466] focus:ring-2 focus:ring-[#063466]/10 transition-all text-gray-900 placeholder-gray-400 text-[15px]"
                            placeholder="you@example.com">
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 px-4 rounded-xl bg-[#063466] hover:bg-[#052a52] text-white font-semibold text-[15px] shadow-lg shadow-[#063466]/20 transition-all">
                        Email password reset link
                    </button>

                    <p class="text-center text-sm text-gray-500">
                        <a href="{{ route('login') }}" class="font-semibold text-[#063466] hover:text-[#1e3a8a]">Back to sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
