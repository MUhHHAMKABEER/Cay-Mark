<style>
.cond-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    backdrop-filter: blur(2px);
}
.cond-modal-card {
    background: #fff;
    border-radius: 16px;
    max-width: 460px;
    width: 100%;
    overflow: hidden;
    box-shadow: 0 24px 64px rgba(6, 52, 102, 0.18);
    animation: condModalIn 0.22s cubic-bezier(0.34, 1.36, 0.64, 1) both;
}
@keyframes condModalIn {
    from { opacity: 0; transform: scale(0.92) translateY(12px); }
    to   { opacity: 1; transform: scale(1)    translateY(0);     }
}
.cond-modal-header {
    background: #063466;
    padding: 1.1rem 1.4rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.cond-modal-header-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #fff;
}
.cond-modal-title {
    font-size: 1rem;
    font-weight: 700;
    color: #fff;
    flex: 1;
    margin: 0;
}
.cond-modal-close-x {
    background: rgba(255,255,255,0.12);
    border: none;
    border-radius: 8px;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255,255,255,0.85);
    cursor: pointer;
    transition: background 0.15s;
    flex-shrink: 0;
}
.cond-modal-close-x:hover { background: rgba(255,255,255,0.22); color: #fff; }
.cond-modal-body {
    padding: 1.4rem 1.4rem 1rem;
}
.cond-modal-desc {
    font-size: 0.875rem;
    color: #4b5563;
    margin-bottom: 1rem;
    line-height: 1.6;
}
.cond-modal-list-label {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #063466;
    margin-bottom: 0.5rem;
}
.cond-modal-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
}
.cond-modal-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.55rem;
    font-size: 0.875rem;
    color: #374151;
    line-height: 1.5;
}
.cond-modal-list li::before {
    content: '';
    display: block;
    width: 18px;
    height: 18px;
    min-width: 18px;
    background: #e8f0fb;
    border-radius: 50%;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%23063466' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: center;
    margin-top: 2px;
}
.cond-modal-footer {
    padding: 0 1.4rem 1.4rem;
}
.cond-modal-btn {
    width: 100%;
    padding: 0.7rem 1rem;
    background: #063466;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s;
}
.cond-modal-btn:hover { background: #052a52; }
</style>

{{-- Close modal when clicking backdrop --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cond-modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) overlay.style.display = 'none';
        });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.cond-modal-overlay').forEach(function (o) {
                o.style.display = 'none';
            });
        }
    });
});
</script>

{{-- SVG helpers --}}
@php
$iconInfo = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>';
$iconX = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
@endphp

{{-- ── Title (Yes) ─────────────────────────────── --}}
<div id="modal-title-yes" class="cond-modal-overlay" style="display:none;">
    <div class="cond-modal-card">
        <div class="cond-modal-header">
            <div class="cond-modal-header-icon">{!! $iconInfo !!}</div>
            <h3 class="cond-modal-title">Has Title</h3>
            <button type="button" class="cond-modal-close-x condition-modal-close" aria-label="Close">{!! $iconX !!}</button>
        </div>
        <div class="cond-modal-body">
            <p class="cond-modal-desc">A vehicle title is the official document proving legal ownership. Select <strong>Yes</strong> only when a valid, transferable title will be provided to the buyer.</p>
            <p class="cond-modal-list-label">This applies when</p>
            <ul class="cond-modal-list">
                <li>You have the original title in your possession</li>
                <li>The title is valid and can be legally transferred</li>
                <li>The buyer will receive the title upon purchase</li>
                <li>The vehicle can be registered in the buyer's name</li>
            </ul>
        </div>
        <div class="cond-modal-footer">
            <button type="button" class="cond-modal-btn condition-modal-close">Got it</button>
        </div>
    </div>
</div>

{{-- ── Title (No) ──────────────────────────────── --}}
<div id="modal-title-no" class="cond-modal-overlay" style="display:none;">
    <div class="cond-modal-card">
        <div class="cond-modal-header">
            <div class="cond-modal-header-icon">{!! $iconInfo !!}</div>
            <h3 class="cond-modal-title">No Title</h3>
            <button type="button" class="cond-modal-close-x condition-modal-close" aria-label="Close">{!! $iconX !!}</button>
        </div>
        <div class="cond-modal-body">
            <p class="cond-modal-desc">Selecting <strong>No</strong> means the vehicle does not have an ownership title. Buyers should be aware the vehicle cannot be registered for road use.</p>
            <p class="cond-modal-list-label">This applies when</p>
            <ul class="cond-modal-list">
                <li>No title is available for the vehicle</li>
                <li>The vehicle cannot be registered for road use</li>
                <li>The vehicle is being sold for parts, export, or salvage</li>
            </ul>
        </div>
        <div class="cond-modal-footer">
            <button type="button" class="cond-modal-btn condition-modal-close">Got it</button>
        </div>
    </div>
</div>

{{-- ── Salvage ──────────────────────────────────── --}}
<div id="modal-salvage" class="cond-modal-overlay" style="display:none;">
    <div class="cond-modal-card">
        <div class="cond-modal-header">
            <div class="cond-modal-header-icon">{!! $iconInfo !!}</div>
            <h3 class="cond-modal-title">Salvage</h3>
            <button type="button" class="cond-modal-close-x condition-modal-close" aria-label="Close">{!! $iconX !!}</button>
        </div>
        <div class="cond-modal-body">
            <p class="cond-modal-desc">A salvage vehicle has been declared a total loss by an insurance company due to severe damage or the cost to repair exceeding its market value.</p>
            <p class="cond-modal-list-label">Select Yes when</p>
            <ul class="cond-modal-list">
                <li>The vehicle has been written off as a total loss</li>
                <li>70% or more of the vehicle has sustained damage</li>
                <li>Repair costs exceed the vehicle's market value</li>
                <li>The vehicle carries a salvage or rebuilt title</li>
            </ul>
        </div>
        <div class="cond-modal-footer">
            <button type="button" class="cond-modal-btn condition-modal-close">Got it</button>
        </div>
    </div>
</div>

{{-- ── Runs & Drives ───────────────────────────── --}}
<div id="modal-runs-drives" class="cond-modal-overlay" style="display:none;">
    <div class="cond-modal-card">
        <div class="cond-modal-header">
            <div class="cond-modal-header-icon">{!! $iconInfo !!}</div>
            <h3 class="cond-modal-title">Runs &amp; Drives</h3>
            <button type="button" class="cond-modal-close-x condition-modal-close" aria-label="Close">{!! $iconX !!}</button>
        </div>
        <div class="cond-modal-body">
            <p class="cond-modal-desc">This confirms the vehicle can start and move under its own power without external assistance.</p>
            <p class="cond-modal-list-label">Select Yes when</p>
            <ul class="cond-modal-list">
                <li>The engine starts and sustains running</li>
                <li>The vehicle can shift into gear</li>
                <li>The vehicle moves forward and backward on its own</li>
                <li>No towing or pushing is required to move it</li>
            </ul>
        </div>
        <div class="cond-modal-footer">
            <button type="button" class="cond-modal-btn condition-modal-close">Got it</button>
        </div>
    </div>
</div>

{{-- ── Starts ───────────────────────────────────── --}}
<div id="modal-starts" class="cond-modal-overlay" style="display:none;">
    <div class="cond-modal-card">
        <div class="cond-modal-header">
            <div class="cond-modal-header-icon">{!! $iconInfo !!}</div>
            <h3 class="cond-modal-title">Starts</h3>
            <button type="button" class="cond-modal-close-x condition-modal-close" aria-label="Close">{!! $iconX !!}</button>
        </div>
        <div class="cond-modal-body">
            <p class="cond-modal-desc">This confirms the engine turns on when started. If <strong>Yes</strong> is selected, an engine video (30 seconds to under 1 minute) is required as proof.</p>
            <p class="cond-modal-list-label">Select Yes when</p>
            <ul class="cond-modal-list">
                <li>The engine turns on when the ignition is activated</li>
                <li>The engine can run on its own, even briefly</li>
                <li>You can provide a 30–60 second engine video</li>
            </ul>
        </div>
        <div class="cond-modal-footer">
            <button type="button" class="cond-modal-btn condition-modal-close">Got it</button>
        </div>
    </div>
</div>
