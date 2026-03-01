@extends('layouts.welcome')
@section('title', 'Set Up Two-Factor Authentication - CayMark')
@section('content')

<div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 bg-gradient-to-br from-slate-50 via-white to-blue-50/40">
    <div class="fixed inset-0 opacity-[0.02] pointer-events-none" style="background-image: linear-gradient(#063466 1px, transparent 1px), linear-gradient(90deg, #063466 1px, transparent 1px); background-size: 48px 48px;"></div>

    <div class="w-full max-w-md relative z-10">
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 border border-gray-100/80 overflow-hidden">
            <div class="h-1 bg-gradient-to-r from-[#063466] via-[#1e3a8a] to-[#2563eb]"></div>
            <div class="p-8 md:p-10">
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 font-heading tracking-tight">Set Up Two-Factor Authentication</h1>
                    <p class="text-gray-500 text-sm mt-1">Admin accounts require 2FA. Scan the QR code or enter the secret in your authenticator app.</p>
                </div>

                <div class="flex justify-center mb-6">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}&margin=2" alt="QR code for authenticator" class="rounded-lg border border-gray-200">
                </div>
                <p class="text-center text-xs text-gray-500 mb-4">Or enter this secret manually: <code class="bg-gray-100 px-2 py-1 rounded font-mono text-[11px] break-all">{{ $secret }}</code></p>

                @if ($errors->any())
                    <div class="mb-6 rounded-lg bg-amber-50 border border-amber-200/80 px-4 py-3 text-sm text-amber-800">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.2fa.confirm') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="code" class="block text-sm font-semibold text-gray-700 mb-1.5">Enter the 6-digit code from your app to confirm</label>
                        <input type="text" id="code" name="code" required autocomplete="one-time-code" inputmode="numeric" pattern="[0-9]*" maxlength="6" placeholder="000000"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50/50 focus:bg-white focus:border-[#063466] focus:ring-2 focus:ring-[#063466]/10 transition-all text-gray-900 text-center text-2xl tracking-[0.4em] font-mono placeholder-gray-400">
                    </div>
                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#063466] hover:bg-[#052a52] text-white font-semibold text-[15px] shadow-lg shadow-[#063466]/20 focus:outline-none focus:ring-2 focus:ring-[#063466] focus:ring-offset-2 transition-all">
                        Enable Two-Factor Authentication
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-4">
                    Use an app like Google Authenticator, Authy, or Microsoft Authenticator.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
