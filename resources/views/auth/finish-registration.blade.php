@extends('layouts.welcome')
@section('title', 'Select Your Plan — CayMark Island Exchange')
@section('content')

@php
    // Render Buyer first, then sellers cheapest-first (Individual before Business)
    $cmPlans = collect($buyerPackages)->sortBy('price')
        ->concat(collect($sellerPackages)->sortBy('price'))
        ->values();
@endphp

@push('styles')
<style>
    .cm-plan-card {
        position:relative; display:flex; gap:16px; align-items:flex-start;
        background:#fff; border:1.5px solid #E2E5E9; border-radius:12px;
        padding:20px 22px; cursor:pointer; transition:border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }
    .cm-plan-card:hover { border-color:#1B3A6B; }
    .cm-plan-input:focus-visible + .cm-plan-card { box-shadow:0 0 0 3px rgba(27,58,107,.18); }
    .cm-plan.is-selected .cm-plan-card { border-color:#C8A84B; background:#FFFBEB; box-shadow:0 0 0 1px #C8A84B inset; }
    .cm-plan.is-dimmed   { opacity:.5; }
    .cm-plan-ico {
        width:46px; height:46px; border-radius:12px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
        background:#EEF2F9; color:#1B3A6B;
    }
    .cm-plan.is-selected .cm-plan-ico { background:#1B3A6B; color:#fff; }
    .cm-plan-check {
        position:absolute; top:14px; right:14px; width:22px; height:22px; border-radius:50%;
        background:#C8A84B; color:#fff; display:none; align-items:center; justify-content:center;
    }
    .cm-plan.is-selected .cm-plan-check { display:flex; }
    .cm-plan-badge {
        position:absolute; top:-10px; right:18px;
        background:#C8A84B; color:#fff; font-size:10px; font-weight:800; letter-spacing:.06em;
        text-transform:uppercase; padding:3px 10px; border-radius:999px;
    }
    .cm-plan-price { font-size:20px; font-weight:800; color:#1B3A6B; line-height:1; }
    .cm-plan-period{ font-size:11px; color:#9CA3AF; margin-top:2px; }
    .cm-plan-feat  { display:flex; align-items:flex-start; gap:6px; font-size:12.5px; color:#4B5563; margin-top:5px; }
    .cm-plan-feat .material-symbols-outlined { font-size:15px; color:#16A34A; flex-shrink:0; margin-top:1px; }
</style>
@endpush

<x-auth.split maxw="600px">

    {{-- Stepper --}}
    <div class="mb-8"><x-auth.stepper :current="2" /></div>

    {{-- Heading --}}
    <div class="mb-7">
        <h1 class="text-[28px] font-bold leading-tight" style="color:#1A1A1A">Choose Your Membership</h1>
        <p class="text-sm mt-1" style="color:#6B7280">Select the plan that fits your needs</p>
    </div>

    {{-- Alerts --}}
    @if (session('error') || $errors->any())
        <div class="space-y-3 mb-6">
            @if (session('error'))
                <div class="flex items-start gap-2.5 px-4 py-3 rounded-lg text-sm" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
                    <span class="material-symbols-outlined" style="font-size:18px;color:#DC2626">error</span>{{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="px-4 py-3 rounded-lg text-sm" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca">
                    <ul class="space-y-0.5 list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('finish.registration.membership') }}" id="membership-form" class="space-y-4">
        @csrf
        <input type="hidden" name="role" id="selected-role" value="{{ old('role') }}">

        @foreach ($cmPlans as $pkg)
            @php
                $role        = $pkg->role;
                $price       = (float) $pkg->price;
                $isBusiness  = $role === 'seller' && $price > 0;
                $isFreeSell  = $role === 'seller' && $price <= 0;
                $icon        = $role === 'buyer' ? 'gavel' : ($isBusiness ? 'business' : 'directions_car');
                $priceLabel  = $price > 0 ? '$' . number_format($price, 2) : 'Free';
                $period      = $pkg->duration_days ? ($pkg->duration_days == 365 ? '/year' : '/' . $pkg->duration_days . ' days') : ($isFreeSell ? 'no annual fee' : 'one-time');
                $feats       = is_array($pkg->features) ? $pkg->features : (is_string($pkg->features) ? (json_decode($pkg->features, true) ?: []) : []);
                $checked     = (string) old('package_id') === (string) $pkg->id;
            @endphp
            <label class="cm-plan block {{ $checked ? 'is-selected' : '' }}" data-role="{{ $role }}">
                <input type="radio" name="package_id" value="{{ $pkg->id }}" data-role="{{ $role }}"
                       class="cm-plan-input sr-only" required @checked($checked)>
                <div class="cm-plan-card">
                    @if ($isBusiness)<span class="cm-plan-badge">Most Popular</span>@endif
                    <span class="cm-plan-check"><span class="material-symbols-outlined" style="font-size:15px">check</span></span>

                    <span class="cm-plan-ico"><span class="material-symbols-outlined">{{ $icon }}</span></span>

                    <div class="flex-1 min-w-0 pr-6">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="text-[15px] font-bold" style="color:#1A1A1A">{{ $pkg->title }}</h3>
                                @if (!empty($pkg->description))
                                    <p class="text-[12.5px] mt-0.5" style="color:#6B7280">{{ $pkg->description }}</p>
                                @endif
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="cm-plan-price">{{ $priceLabel }}</div>
                                <div class="cm-plan-period">{{ $period }}</div>
                            </div>
                        </div>
                        @foreach (array_slice($feats, 0, 3) as $f)
                            <div class="cm-plan-feat"><span class="material-symbols-outlined">check_circle</span>{{ $f }}</div>
                        @endforeach
                    </div>
                </div>
            </label>
        @endforeach

        @error('role')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror
        @error('package_id')<p class="cm-auth-error"><span class="material-symbols-outlined">error</span>{{ $message }}</p>@enderror

        {{-- Actions --}}
        <div class="flex flex-col-reverse sm:flex-row gap-3 pt-3">
            <a href="{{ route('dashboard.default') }}" class="cm-auth-btn cm-auth-btn--ghost" style="text-decoration:none">
                Cancel
            </a>
            <button type="submit" id="submit-btn" class="cm-auth-btn" disabled>
                <span>Continue</span>
                <span class="material-symbols-outlined" style="font-size:18px">arrow_forward</span>
            </button>
        </div>
    </form>

</x-auth.split>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var plans   = Array.prototype.slice.call(document.querySelectorAll('.cm-plan'));
    var roleEl  = document.getElementById('selected-role');
    var submit  = document.getElementById('submit-btn');

    function select(radio) {
        var role = radio.dataset.role;
        roleEl.value = role;
        plans.forEach(function (p) {
            var r = p.querySelector('.cm-plan-input');
            var isSel = r === radio;
            p.classList.toggle('is-selected', isSel);
            // dim the opposite-role cards
            p.classList.toggle('is-dimmed', !isSel && p.dataset.role !== role);
        });
        submit.disabled = false;
    }

    plans.forEach(function (p) {
        var radio = p.querySelector('.cm-plan-input');
        radio.addEventListener('change', function () { if (radio.checked) select(radio); });
    });

    // Restore selection on validation re-render
    var pre = document.querySelector('.cm-plan-input:checked');
    if (pre) select(pre);
});
</script>

@endsection
