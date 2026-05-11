@extends('layouts.dashboard')

@section('title', 'Messaging Center - CayMark')

@push('styles')
@endpush

@section('content')
<style>
    .messaging-shell { background: #f8fafc; min-height: calc(100vh - 0px); padding: 1rem; }
    .messaging-grid { display: grid; grid-template-columns: 320px 1fr; gap: 1rem; min-height: calc(100vh - 32px); }
    @media (max-width: 1023px) { .messaging-grid { grid-template-columns: 1fr; } .messaging-aside.has-active { display: none; } }
    .messaging-aside { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow-y: auto; max-height: calc(100vh - 32px); }
    .messaging-main { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; display: flex; flex-direction: column; overflow: hidden; max-height: calc(100vh - 32px); }
    .messaging-aside-header { padding: 1.25rem 1.25rem 0.75rem; border-bottom: 1px solid #f1f5f9; }
    .messaging-aside-header h2 { font-size: 0.95rem; font-weight: 700; color: #0f172a; letter-spacing: 0.04em; text-transform: uppercase; }

    .thread-card { display: flex; gap: 0.75rem; padding: 0.875rem; border-radius: 12px; cursor: pointer; transition: background 0.15s ease, border 0.15s ease; border: 2px solid transparent; text-decoration: none; color: inherit; }
    .thread-card:hover { background: #f8fafc; }
    .thread-card.active { background: #f0fdfa; border-color: #14b8a6; }
    .thread-card img { width: 64px; height: 64px; border-radius: 10px; object-fit: cover; flex-shrink: 0; background: #e2e8f0; }
    .thread-card .meta { min-width: 0; flex: 1; }
    .thread-card .title { font-size: 0.875rem; font-weight: 600; color: #0f172a; line-height: 1.25; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    .pill { display: inline-flex; align-items: center; gap: 4px; font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 999px; text-transform: uppercase; letter-spacing: 0.04em; }
    .pill-paid { background: #ccfbf1; color: #0f766e; }
    .pill-pending { background: #fef3c7; color: #92400e; }
    .pill-completed { background: #dcfce7; color: #166534; }

    .messaging-main-body { flex: 1; overflow-y: auto; padding: 1.25rem; }
    .messaging-main-body::-webkit-scrollbar { width: 8px; }
    .messaging-main-body::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .messaging-main-body::-webkit-scrollbar-track { background: transparent; }

    .messaging-topbar { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9; flex-shrink: 0; gap: 1rem; flex-wrap: wrap; }
    .messaging-topbar .back-link { display: inline-flex; align-items: center; gap: 6px; color: #2563eb; text-decoration: none; font-size: 0.875rem; font-weight: 600; }
    .messaging-topbar .back-link:hover { text-decoration: underline; }
    .messaging-topbar .order-num { font-size: 0.8rem; color: #64748b; font-weight: 600; font-family: 'Courier New', monospace; }
    .messaging-topbar .view-item { color: #2563eb; text-decoration: none; font-size: 0.85rem; font-weight: 600; }
    .messaging-topbar .view-item:hover { text-decoration: underline; }

    .pickup-code-band { position: sticky; top: 0; z-index: 20; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px solid #fbbf24; border-radius: 14px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 4px 12px rgba(251, 191, 36, 0.18); margin-bottom: 1.25rem; flex-wrap: wrap; }
    .pickup-code-band .lock-icon { width: 40px; height: 40px; border-radius: 10px; background: #fbbf24; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .pickup-code-band .lock-icon .material-icons-round { color: #fff; }
    .pickup-code-band .label { font-size: 0.65rem; font-weight: 700; color: #92400e; letter-spacing: 0.08em; text-transform: uppercase; }
    .pickup-code-band .code { font-size: 1.75rem; font-weight: 800; color: #1d4ed8; letter-spacing: 0.05em; font-family: 'Courier New', monospace; line-height: 1.2; }
    .pickup-code-band .hint { color: #78350f; font-size: 0.8rem; flex: 1; min-width: 200px; display: flex; align-items: center; gap: 6px; }
    .pickup-code-band .copy-btn { background: #fff; border: 2px solid #fbbf24; color: #92400e; font-weight: 600; padding: 0.5rem 1rem; border-radius: 10px; cursor: pointer; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; transition: background 0.15s ease; }
    .pickup-code-band .copy-btn:hover { background: #fef3c7; }

    .header-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem; margin-bottom: 1.25rem; display: grid; grid-template-columns: auto 1fr; gap: 1.25rem; align-items: start; }
    .header-card img.car-thumb { width: 140px; height: 100px; border-radius: 10px; object-fit: cover; background: #e2e8f0; flex-shrink: 0; }
    .header-card .meta-row { color: #64748b; font-size: 0.85rem; margin-top: 4px; display: flex; align-items: center; gap: 6px; }
    .header-card .meta-row .material-icons-round { font-size: 1rem; color: #94a3b8; }
    .header-card .pills { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }

    .system-notice { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 0.875rem 1rem; display: flex; align-items: flex-start; gap: 10px; color: #1e40af; font-size: 0.85rem; margin-bottom: 1.25rem; }
    .system-notice .material-icons-round { color: #2563eb; flex-shrink: 0; }

    .flagged-banner { background: #fef3c7; border: 1px solid #fbbf24; border-radius: 12px; padding: 0.875rem 1rem; display: flex; align-items: flex-start; gap: 10px; color: #92400e; font-size: 0.85rem; margin-bottom: 1.25rem; }
    .flagged-banner .material-icons-round { color: #d97706; flex-shrink: 0; }

    .convo-section-title { font-size: 0.875rem; font-weight: 700; color: #475569; margin: 0.5rem 0 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; display: flex; align-items: center; gap: 8px; }
    .convo-section-title .material-icons-round { font-size: 1rem; color: #94a3b8; }

    .event-card { background: #fff; border: 1px solid #e2e8f0; border-left-width: 4px; border-radius: 12px; padding: 0.875rem 1rem; margin-bottom: 0.75rem; }
    .event-card.from-seller { border-left-color: #14b8a6; }
    .event-card.from-buyer { border-left-color: #3b82f6; background: #eff6ff; }
    .event-card.from-system { border-left-color: #94a3b8; background: #f8fafc; }
    .event-card.confirmed { border-left-color: #10b981; background: #f0fdf4; }
    .event-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 6px; flex-wrap: wrap; }
    .event-head .who { display: flex; align-items: center; gap: 8px; font-weight: 600; color: #0f172a; font-size: 0.9rem; }
    .event-head .who .material-icons-round { font-size: 1.1rem; color: #64748b; }
    .event-head .when { font-size: 0.7rem; color: #94a3b8; }
    .event-body { font-size: 0.85rem; color: #1e293b; line-height: 1.55; }
    .event-body .field-line { margin: 2px 0; }
    .event-body .field-label { color: #64748b; font-weight: 600; }

    .inline-actions { display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
    .inline-actions .btn { padding: 0.55rem 1rem; border-radius: 10px; font-weight: 600; font-size: 0.85rem; cursor: pointer; border: 2px solid transparent; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s ease; text-decoration: none; }
    .inline-actions .btn-accept { background: #10b981; color: #fff; }
    .inline-actions .btn-accept:hover { background: #059669; }
    .inline-actions .btn-secondary { background: #fff; color: #475569; border-color: #cbd5e1; }
    .inline-actions .btn-secondary:hover { background: #f1f5f9; }

    .action-row { display: flex; flex-wrap: wrap; gap: 8px; margin: 1rem 0 0; padding-top: 1rem; border-top: 1px solid #f1f5f9; }
    .action-chip { background: #fff; border: 2px solid #e2e8f0; border-radius: 12px; padding: 0.875rem 1rem; cursor: pointer; transition: all 0.15s ease; display: flex; flex-direction: column; align-items: center; gap: 6px; min-width: 100px; flex: 1; max-width: 160px; font-size: 0.75rem; color: #475569; font-weight: 600; text-align: center; line-height: 1.2; }
    .action-chip:hover { border-color: #2563eb; background: #eff6ff; color: #1e40af; }
    .action-chip:disabled { opacity: 0.4; cursor: not-allowed; }
    .action-chip .material-icons-round { font-size: 1.5rem; color: #2563eb; }
    .action-chip.danger:hover { border-color: #dc2626; background: #fef2f2; color: #991b1b; }
    .action-chip.danger .material-icons-round { color: #dc2626; }

    .footer-card { background: #f8fafc; border-top: 1px solid #f1f5f9; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; flex-shrink: 0; }
    .footer-card .help { display: flex; flex-direction: column; gap: 2px; }
    .footer-card .help-title { font-weight: 700; color: #0f172a; font-size: 0.8rem; }
    .footer-card .help-row { display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem; color: #475569; }
    .footer-card .help-row .material-icons-round { font-size: 1rem; color: #94a3b8; }
    .footer-card .exchange-counter { font-size: 0.75rem; color: #475569; font-weight: 600; }
    .footer-card .exchange-counter strong { color: #0f172a; font-size: 0.875rem; }

    /* Modal styling matching mockups */
    .messaging-modal { display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); z-index: 60; align-items: center; justify-content: center; padding: 1rem; }
    .messaging-modal.show { display: flex; }
    .messaging-modal-card { background: #fff; border-radius: 16px; max-width: 480px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25); }
    .messaging-modal-header { padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
    .messaging-modal-header h3 { font-size: 1rem; font-weight: 700; color: #0f172a; }
    .messaging-modal-header .close-btn { background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 1.5rem; line-height: 1; padding: 0 4px; }
    .messaging-modal-body { padding: 1.25rem; }
    .messaging-modal-notice { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 0.75rem 0.875rem; color: #1e40af; font-size: 0.8rem; margin-bottom: 1rem; display: flex; gap: 8px; }
    .messaging-modal-notice .material-icons-round { font-size: 1rem; color: #2563eb; flex-shrink: 0; }
    .messaging-modal-field { margin-bottom: 0.875rem; }
    .messaging-modal-field label { display: block; font-size: 0.8rem; font-weight: 600; color: #334155; margin-bottom: 6px; }
    .messaging-modal-field input[type=text],
    .messaging-modal-field input[type=date],
    .messaging-modal-field input[type=time],
    .messaging-modal-field select,
    .messaging-modal-field textarea { width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.875rem; }
    .messaging-modal-field textarea { resize: vertical; min-height: 80px; }
    .messaging-modal-field .char-count { text-align: right; font-size: 0.7rem; color: #94a3b8; margin-top: 2px; }
    .messaging-modal-footer { padding: 1rem 1.25rem; border-top: 1px solid #e2e8f0; display: flex; gap: 8px; justify-content: flex-end; }
    .messaging-modal-footer button { padding: 0.55rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.85rem; cursor: pointer; border: none; }
    .messaging-modal-footer .btn-cancel { background: #fff; color: #475569; border: 1px solid #cbd5e1; }
    .messaging-modal-footer .btn-submit { background: #2563eb; color: #fff; }
    .messaging-modal-footer .btn-submit:hover { background: #1d4ed8; }

    .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; padding: 3rem 1rem; color: #94a3b8; text-align: center; }
    .empty-state .material-icons-round { font-size: 4rem; color: #cbd5e1; margin-bottom: 1rem; }
</style>

<div class="messaging-shell">
    <div class="messaging-grid">
        <aside class="messaging-aside {{ $activeThread ? 'has-active-md' : '' }}">
            @include('messaging._thread-list', ['threads' => $threads, 'activeId' => $activeInvoice?->id])
        </aside>
        <section class="messaging-main">
            @if (! $activeThread)
                @include('messaging._empty')
            @elseif (! $activeThread->isUnlocked())
                @include('messaging._locked', ['hideBuyerName' => $isSeller])
            @elseif ($activeThread->pickup_confirmed)
                @include('messaging._thread-closed')
            @else
                @include('messaging._thread')
            @endif
        </section>
    </div>
</div>

@if (session('success'))
    <div id="msg-toast" style="position: fixed; bottom: 24px; right: 24px; background: #0f172a; color: #fff; padding: 0.875rem 1.25rem; border-radius: 12px; z-index: 100; box-shadow: 0 10px 30px rgba(0,0,0,0.25); font-size: 0.875rem;">
        {{ session('success') }}
    </div>
    <script>setTimeout(() => { var t=document.getElementById('msg-toast'); if(t) t.remove(); }, 4000);</script>
@endif

@if ($errors->any())
    <div style="position: fixed; bottom: 24px; right: 24px; background: #b91c1c; color: #fff; padding: 0.875rem 1.25rem; border-radius: 12px; z-index: 100; box-shadow: 0 10px 30px rgba(0,0,0,0.25); font-size: 0.875rem; max-width: 360px;">
        <strong>Validation issues</strong><br>
        @foreach ($errors->all() as $err)<div>· {{ $err }}</div>@endforeach
    </div>
@endif

<script>
    function openMessagingModal(id) { document.getElementById(id)?.classList.add('show'); }
    function closeMessagingModal(id) { document.getElementById(id)?.classList.remove('show'); }
    document.querySelectorAll('.messaging-modal').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) m.classList.remove('show'); });
    });
    document.querySelectorAll('textarea[data-charcount]').forEach(t => {
        var counter = document.getElementById(t.dataset.charcount);
        if (! counter) return;
        var update = () => { counter.textContent = t.value.length + ' / ' + (t.maxLength > 0 ? t.maxLength : '—'); };
        t.addEventListener('input', update); update();
    });
    function copyPickupCode(code) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(code).then(() => {
                var btn = document.getElementById('pickup-copy-btn');
                if (btn) { var orig = btn.innerHTML; btn.innerHTML = '<span class="material-icons-round" style="font-size:1rem;">check</span> Copied'; setTimeout(() => btn.innerHTML = orig, 1800); }
            });
        }
    }
</script>

@endsection
