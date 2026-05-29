@props([
    'maxw' => '480px',
])

{{--
    CayMark auth split-screen layout component.
    Left  : navy gradient brand panel (hidden < lg).
    Right : white form area — page content goes in the default slot.
    Used by: login, register (step 1), forgot/reset password, etc.
    Renders inside layouts.welcome, so caymark-ui-kit JS + brand tokens stay available.
--}}

@once
@push('styles')
<style>
/* ===== CayMark Auth (Login / Registration) ============================= */
.cm-auth-wrap   { font-family:'Inter',sans-serif; min-height:calc(100vh - 72px); }
.cm-auth-left   { background:linear-gradient(160deg,#1B3A6B 0%,#0F2347 100%); }
.cm-auth-left::before {
    content:''; position:absolute; inset:0; opacity:.05; pointer-events:none;
    background-image:
        repeating-linear-gradient(0deg,#fff 0,#fff 1px,transparent 1px,transparent 44px),
        repeating-linear-gradient(90deg,#fff 0,#fff 1px,transparent 1px,transparent 44px);
}
.cm-auth-feature { display:flex; align-items:center; gap:12px; }
.cm-auth-feature .tick {
    width:28px; height:28px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    background:rgba(200,168,75,.16); color:#C8A84B;
}

/* ----- form primitives ----- */
.cm-auth-label    { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.cm-auth-fieldwrap{ position:relative; }
.cm-auth-input {
    width:100%; height:52px; background:#fff;
    border:1.5px solid #E2E5E9; border-radius:8px;
    padding:12px 16px; font-size:15px; color:#1A1A1A;
    transition:border-color .2s ease, box-shadow .2s ease; outline:none;
}
.cm-auth-input::placeholder { color:#9CA3AF; }
.cm-auth-input.has-licon    { padding-left:46px; }
.cm-auth-input.has-ricon    { padding-right:46px; }
.cm-auth-input:focus        { border-color:#1B3A6B; box-shadow:0 0 0 3px rgba(27,58,107,.12); }
.cm-auth-input.is-error,
.cm-auth-input[aria-invalid="true"] { border-color:#DC2626; box-shadow:0 0 0 3px rgba(220,38,38,.12); }
.cm-auth-input.is-valid     { border-color:#16A34A; }
.cm-auth-select             { appearance:none; -webkit-appearance:none; background-image:none; }

/* Icons anchor to the 52px input's centre (top:26px), NOT the wrap centre,
   so they never shift when the form kit appends a strength meter / error
   message inside the field wrapper. */
.cm-auth-licon {
    position:absolute; left:14px; top:26px; transform:translateY(-50%);
    color:#9CA3AF; font-size:20px; pointer-events:none; line-height:1;
}
.cm-auth-eye {
    position:absolute; right:12px; top:26px; transform:translateY(-50%);
    color:#9CA3AF; cursor:pointer; background:none; border:none; padding:4px;
    display:flex; align-items:center; line-height:0;
}
.cm-auth-eye:hover { color:#6B7280; }
.cm-auth-chevron {
    position:absolute; right:12px; top:26px; transform:translateY(-50%);
    color:#9CA3AF; font-size:20px; pointer-events:none; line-height:1;
}
/* Material Symbols glyphs must keep their 1:1 box (prevents stretched icons) */
.cm-auth-licon.material-symbols-outlined,
.cm-auth-chevron.material-symbols-outlined,
.cm-auth-eye .material-symbols-outlined {
    width:20px; text-align:center; font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;
}
.cm-auth-error {
    display:flex; align-items:center; gap:4px;
    font-size:13px; color:#DC2626; margin-top:6px;
}
.cm-auth-error .material-symbols-outlined { font-size:15px; }

.cm-auth-btn {
    width:100%; height:52px; border:none; border-radius:8px;
    background:#1B3A6B; color:#fff; font-weight:700; font-size:16px;
    cursor:pointer; transition:background .2s ease, transform .05s ease;
    display:flex; align-items:center; justify-content:center; gap:8px;
}
.cm-auth-btn:hover    { background:#0F2347; }
.cm-auth-btn:active   { transform:scale(.99); }
.cm-auth-btn:disabled { opacity:.6; cursor:not-allowed; }
.cm-auth-btn--ghost {
    background:#fff; color:#1B3A6B; border:1.5px solid #E2E5E9;
}
.cm-auth-btn--ghost:hover { background:#F5F6F7; }

.cm-auth-link      { color:#C8A84B; font-weight:600; }
.cm-auth-link:hover{ text-decoration:underline; }

.cm-auth-divider   { display:flex; align-items:center; gap:12px; color:#9CA3AF; font-size:13px; }
.cm-auth-divider::before, .cm-auth-divider::after { content:''; flex:1; height:1px; background:#E2E5E9; }

/* ----- animations ----- */
@keyframes cmShake { 0%,100%{transform:translateX(0)} 20%{transform:translateX(-6px)} 40%{transform:translateX(6px)} 60%{transform:translateX(-4px)} 80%{transform:translateX(4px)} }
.cm-shake { animation:cmShake .4s ease; }
.cm-spin  { width:18px; height:18px; border:2px solid rgba(255,255,255,.4); border-top-color:#fff; border-radius:50%; animation:cmSpin .7s linear infinite; display:inline-block; }
@keyframes cmSpin { to { transform:rotate(360deg) } }
</style>
@endpush
@endonce

<div class="cm-auth-wrap flex items-stretch">

    {{-- ══ LEFT · brand panel (hidden on mobile) ════════════════════════ --}}
    <div class="cm-auth-left hidden lg:flex flex-col justify-between w-2/5 flex-shrink-0 relative overflow-hidden px-12 py-14 text-white">

        <div class="relative">
            <span class="block text-2xl font-extrabold tracking-tight text-white">CayMark</span>
            <p class="text-[#C8A84B] font-semibold text-sm tracking-wide mb-14">Island Exchange &amp; Auction House</p>

            <h1 class="text-3xl xl:text-[2.5rem] font-extrabold leading-[1.15] mb-4">
                The Bahamas' Premier Vehicle Auction Platform
            </h1>
            <p class="text-white/55 text-base leading-relaxed max-w-sm">
                Buy and sell cars, trucks, boats and heavy equipment across the islands.
            </p>
        </div>

        <div class="relative space-y-4">
            @foreach (['Secure online auctions', 'Verified buyers and sellers', 'Island-wide vehicle marketplace'] as $feat)
                <div class="cm-auth-feature">
                    <span class="tick"><span class="material-symbols-outlined" style="font-size:16px">check</span></span>
                    <span class="text-white/80 text-sm">{{ $feat }}</span>
                </div>
            @endforeach
        </div>

        {{-- decorative wave silhouette --}}
        <svg class="absolute bottom-0 left-0 w-full opacity-[0.06] pointer-events-none" viewBox="0 0 400 140" preserveAspectRatio="none" fill="none" aria-hidden="true">
            <path d="M0 70 Q100 25 200 70 T400 70 V140 H0 Z" fill="#fff"/>
            <path d="M0 95 Q100 55 200 95 T400 95 V140 H0 Z" fill="#C8A84B"/>
        </svg>
    </div>

    {{-- ══ RIGHT · form area ════════════════════════════════════════════ --}}
    {{-- my-auto centres short pages but lets tall pages (uploads/payment) scroll --}}
    <div class="flex-1 bg-white flex flex-col px-6 py-10 sm:px-10 lg:px-16 overflow-y-auto">
        <div class="w-full mx-auto my-auto" style="max-width:{{ $maxw }}">

            {{-- mobile brand (left panel hidden < lg) --}}
            <div class="lg:hidden mb-8 text-center">
                <span class="text-xl font-extrabold tracking-tight" style="color:#1B3A6B">CayMark</span>
                <p class="text-[#C8A84B] font-semibold text-xs tracking-wide mt-0.5">Island Exchange &amp; Auction House</p>
            </div>

            {{ $slot }}
        </div>
    </div>
</div>
