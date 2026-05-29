@extends('layouts.welcome')
@section('title', 'Select Your Plan — CayMark Island Exchange')
@section('content')

<style>
    /* Role card selected state */
    label:has(input[name="role"]:checked) .role-radio-indicator {
        border-color: #C8A84B !important;
        background-color: #C8A84B !important;
    }
    label:has(input[name="role"]:checked) .role-radio-check { opacity: 1 !important; }
    label:has(input[name="role"]:checked) .role-card {
        border-color: #002452 !important;
        background-color: #f0f4fb !important;
    }
    /* Package card selected state */
    label:has(input[name="package_id"]:checked) .package-radio-indicator {
        border-color: #C8A84B !important;
        background-color: #C8A84B !important;
    }
    label:has(input[name="package_id"]:checked) .package-radio-check { opacity: 1 !important; }
</style>

<div class="bg-[#f8fafd] py-12 px-4">
    <div class="max-w-5xl mx-auto">

        {{-- ── Step progress ─────────────────────────────────────── --}}
        <div class="mb-10 flex justify-center">
            <x-auth.stepper :current="2" />
        </div>

        <div class="text-center mb-10">
            <p class="text-[11px] font-bold text-secondary-fixed-dim uppercase tracking-[0.3em] mb-2">Step 2 of 3</p>
            <h1 class="text-3xl font-bold text-primary uppercase tracking-tight font-headline-lg">Select Your Role &amp; Plan</h1>
            <p class="text-gray-400 text-sm mt-2">Choose how you'll use CayMark, then select your membership package.</p>
        </div>

        {{-- Flash messages --}}
        @if (session('error'))
            <div class="border-l-4 border-error bg-red-50 px-5 py-4 flex items-start gap-3 text-sm text-red-800 mb-6" style="border-radius:0">
                <span class="material-symbols-outlined text-error text-[18px] flex-shrink-0 mt-0.5">error</span>
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="border-l-4 border-amber-400 bg-amber-50 px-5 py-4 text-sm text-amber-800 mb-6" style="border-radius:0">
                <p class="font-bold mb-1.5">Please fix the following:</p>
                <ul class="space-y-0.5 list-disc list-inside text-amber-700">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('finish.registration.membership') }}" id="membership-form">
            @csrf

            {{-- ── Role Selection ─────────────────────────────────── --}}
            <div class="bg-white border-t-4 border-primary shadow-md mb-6" style="border-radius:0">
                <div class="px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">manage_accounts</span>
                    <h2 class="text-sm font-bold text-primary uppercase tracking-widest">I want to register as a:</h2>
                </div>

                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Buyer card --}}
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="role" value="buyer" class="sr-only" required onchange="loadPackages('buyer')">
                        <div class="role-card border-2 border-gray-200 p-6 flex gap-5 items-start hover:border-primary transition-colors relative" style="border-radius:0">
                            <div class="role-radio-indicator w-5 h-5 border-2 border-gray-300 flex items-center justify-center flex-shrink-0 mt-0.5 transition-all" style="border-radius:0">
                                <svg class="role-radio-check w-3 h-3 text-primary opacity-0 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="w-12 h-12 bg-primary flex items-center justify-center flex-shrink-0" style="border-radius:0">
                                <span class="material-symbols-outlined text-white text-[22px]">gavel</span>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-primary uppercase tracking-wider mb-1">Buyer</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Browse the marketplace and bid in live auctions. $64.99/year membership.</p>
                                <span class="inline-block mt-2 text-[10px] font-bold text-secondary-fixed-dim uppercase tracking-widest">Best for purchasing</span>
                            </div>
                        </div>
                    </label>

                    {{-- Seller card --}}
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="role" value="seller" class="sr-only" required onchange="loadPackages('seller')">
                        <div class="role-card border-2 border-gray-200 p-6 flex gap-5 items-start hover:border-primary transition-colors relative" style="border-radius:0">
                            <div class="role-radio-indicator w-5 h-5 border-2 border-gray-300 flex items-center justify-center flex-shrink-0 mt-0.5 transition-all" style="border-radius:0">
                                <svg class="role-radio-check w-3 h-3 text-primary opacity-0 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="w-12 h-12 bg-[#1b3a6b] flex items-center justify-center flex-shrink-0" style="border-radius:0">
                                <span class="material-symbols-outlined text-secondary-fixed-dim text-[22px]">sell</span>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-primary uppercase tracking-wider mb-1">Seller</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">List vehicles for auction or marketplace sale. Free individual plan or $599.99/year business plan.</p>
                                <span class="inline-block mt-2 text-[10px] font-bold text-secondary-fixed-dim uppercase tracking-widest">Best for selling</span>
                            </div>
                        </div>
                    </label>

                </div>

                @error('role')
                    <div class="px-8 pb-4">
                        <p class="text-xs text-error flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                        </p>
                    </div>
                @enderror
            </div>

            {{-- ── Package Selection ───────────────────────────────── --}}
            <div class="bg-white border-t-4 border-secondary-fixed-dim shadow-md mb-8" style="border-radius:0">
                <div class="px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">workspace_premium</span>
                    <h2 class="text-sm font-bold text-primary uppercase tracking-widest" id="package-section-title">Choose Your Membership Package</h2>
                </div>

                <div id="package-section-subtitle" class="hidden px-8 pt-5 pb-0">
                    <div class="border-l-4 border-secondary-fixed-dim bg-[#fff8e7] px-4 py-3" style="border-radius:0">
                        <p class="text-xs text-gray-700 leading-relaxed">
                            <strong class="text-primary">Individual Seller</strong> — free registration, 4% commission per sale (min $150).<br/>
                            <strong class="text-primary">Business Seller</strong> — $599.99/year, free relisting within 48h, dedicated account manager.
                        </p>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    <div id="package-selection" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Default empty state --}}
                        <div class="col-span-2 py-14 text-center">
                            <span class="material-symbols-outlined text-gray-200 text-[56px] block mb-3">arrow_upward</span>
                            <p class="text-gray-400 text-sm">Select a role above to see available packages.</p>
                        </div>
                    </div>

                    @error('package_id')
                        <p class="text-xs text-error mt-3 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- ── Actions ─────────────────────────────────────────── --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-8">
                <a href="{{ route('dashboard.default') }}"
                   class="w-full sm:w-auto px-8 py-4 border-2 border-gray-200 text-gray-500 font-bold uppercase tracking-widest text-sm hover:border-gray-400 hover:text-gray-700 transition-colors flex items-center justify-center gap-2"
                   style="border-radius:0">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                    Cancel
                </a>
                <button type="submit" id="submit-btn" disabled
                        class="w-full sm:w-auto px-10 py-4 bg-secondary-fixed-dim text-primary font-bold uppercase tracking-widest text-sm hover:bg-[#b8943b] transition-colors flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed"
                        style="border-radius:0">
                    Continue to Verification
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </button>
            </div>

        </form>
    </div>
</div>

@push('styles')
<style>
    .pkg-selected { border-color: #002452 !important; background-color: #f0f4fb !important; }
</style>
@endpush

@push('scripts')
<script>
    let selectedRole = null;
    let selectedPackage = null;

    async function loadPackages(role) {
        selectedRole = role;
        const container = document.getElementById('package-selection');
        const submitBtn = document.getElementById('submit-btn');
        const sectionTitle = document.getElementById('package-section-title');
        const sectionSubtitle = document.getElementById('package-section-subtitle');

        if (role === 'seller') {
            if (sectionTitle) sectionTitle.textContent = 'Choose Your Seller Type';
            if (sectionSubtitle) sectionSubtitle.classList.remove('hidden');
        } else {
            if (sectionTitle) sectionTitle.textContent = 'Choose Your Membership Package';
            if (sectionSubtitle) sectionSubtitle.classList.add('hidden');
        }

        container.innerHTML = `
            <div class="col-span-2 py-14 text-center">
                <div class="inline-block">
                    <div class="animate-spin w-10 h-10 border-4 border-gray-200 border-t-primary mx-auto mb-3"></div>
                    <p class="text-gray-400 text-sm">Loading packages…</p>
                </div>
            </div>`;
        submitBtn.disabled = true;

        try {
            const response = await fetch('/api/packages/' + role);
            if (!response.ok) throw new Error('Failed');
            const packages = await response.json();

            if (!Array.isArray(packages) || packages.length === 0) {
                container.innerHTML = `<div class="col-span-2 py-10 text-center text-gray-400 text-sm">No packages available for this role.</div>`;
                return;
            }

            container.innerHTML = '';
            packages.forEach((pkg, index) => container.appendChild(createPackageCard(pkg, role, index)));
        } catch (error) {
            container.innerHTML = `
                <div class="col-span-2 py-10 text-center">
                    <span class="material-symbols-outlined text-error text-[40px] block mb-2">error</span>
                    <p class="text-error text-sm">Failed to load packages. Please refresh and try again.</p>
                </div>`;
        }
    }

    function createPackageCard(pkg, role, index) {
        const label = document.createElement('label');
        label.className = 'block cursor-pointer';

        const price = pkg.price > 0 ? '$' + Number(pkg.price).toFixed(2) : 'Free';
        const isFree = pkg.price == 0;
        const isBusinessSeller = role === 'seller' && pkg.price > 0;
        const sellerTypeLabel = role === 'seller' ? (isFree ? 'Individual Seller' : 'Business Seller') : null;
        const sellerTypeDescription = role === 'seller'
            ? (isFree ? 'For individuals — no registration fee. 4% commission (min $150) per sale.'
                      : 'For businesses — annual membership, free relisting, account manager.')
            : null;
        const features = Array.isArray(pkg.features) ? pkg.features
            : (typeof pkg.features === 'string' ? JSON.parse(pkg.features) : []);

        const input = document.createElement('input');
        input.type = 'radio';
        input.name = 'package_id';
        input.value = pkg.id;
        input.className = 'sr-only';
        input.required = true;

        input.addEventListener('change', () => {
            document.querySelectorAll('#package-selection .pkg-card').forEach(c => {
                c.classList.remove('pkg-selected');
                c.querySelector('.pkg-radio-ind').style.borderColor = '';
                c.querySelector('.pkg-radio-ind').style.backgroundColor = '';
                c.querySelector('.pkg-radio-chk').style.opacity = '0';
            });
            if (input.checked) {
                const card = label.querySelector('.pkg-card');
                card.classList.add('pkg-selected');
                card.querySelector('.pkg-radio-ind').style.borderColor = '#C8A84B';
                card.querySelector('.pkg-radio-ind').style.backgroundColor = '#C8A84B';
                card.querySelector('.pkg-radio-chk').style.opacity = '1';
                selectedPackage = pkg.id;
                document.getElementById('submit-btn').disabled = false;
            }
        });

        label.innerHTML = `
            <div class="pkg-card border-2 border-gray-200 hover:border-primary transition-colors h-full flex flex-col" style="border-radius:0">

                ${sellerTypeLabel ? `
                <div class="px-6 pt-5 pb-0">
                    <span class="inline-block px-3 py-1 text-[10px] font-bold uppercase tracking-widest
                                 ${isFree ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-[#fdf8ee] text-secondary-fixed-dim border border-secondary-fixed-dim'}"
                          style="border-radius:0">
                        ${escapeHtml(sellerTypeLabel)}
                    </span>
                    ${sellerTypeDescription ? `<p class="text-xs text-gray-400 mt-1.5 leading-relaxed">${escapeHtml(sellerTypeDescription)}</p>` : ''}
                </div>` : ''}

                <div class="p-6 flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        <div class="pkg-radio-ind w-5 h-5 border-2 border-gray-300 flex items-center justify-center flex-shrink-0 mt-0.5 transition-all" style="border-radius:0">
                            <svg class="pkg-radio-chk w-3 h-3 text-primary transition-opacity" style="opacity:0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-bold text-primary uppercase tracking-wide mb-0.5">${escapeHtml(pkg.title || 'Package')}</h3>
                            ${pkg.description ? `<p class="text-xs text-gray-400 leading-relaxed">${escapeHtml(pkg.description)}</p>` : ''}
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-2xl font-bold text-primary">${price}</div>
                        ${pkg.duration_days ? `<p class="text-[10px] text-gray-400 uppercase tracking-widest mt-0.5">per ${pkg.duration_days === 365 ? 'year' : pkg.duration_days + ' days'}</p>` : '<p class="text-[10px] text-gray-400 uppercase tracking-widest mt-0.5">one-time</p>'}
                    </div>
                </div>

                ${features.length > 0 ? `
                <div class="border-t border-gray-100 px-6 py-4 flex-1">
                    <ul class="space-y-2">
                        ${features.slice(0, 5).map(f => `
                            <li class="flex items-start gap-2 text-xs text-gray-600">
                                <span class="material-symbols-outlined text-secondary-fixed-dim text-[14px] flex-shrink-0 mt-0.5">check_circle</span>
                                ${escapeHtml(f)}
                            </li>
                        `).join('')}
                    </ul>
                </div>` : ''}

                ${isFree ? `
                <div class="mx-6 mb-5 border-l-4 border-green-400 bg-green-50 px-4 py-2" style="border-radius:0">
                    <p class="text-xs text-green-700 font-semibold">No payment required at registration.</p>
                </div>` : ''}
            </div>`;

        label.insertBefore(input, label.firstChild);
        return label;
    }

    function escapeHtml(str) {
        if (str === null || typeof str === 'undefined') return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    document.addEventListener('change', function(e) {
        if (e.target.name === 'package_id') {
            document.getElementById('submit-btn').disabled = false;
        }
    });
</script>
@endpush

@endsection
