@extends('layouts.admin')

@section('title', 'Sales / Payouts - Admin')

@section('content')
@php
    $baseQuery = array_filter([
        'tab'  => $tab  ?? 'all',
        'sort' => $sort ?? 'newest',
        'q'    => request('q'),
    ], fn ($v) => $v !== null && $v !== '');

    /* ── Stat counts ─────────────────────────────────────────── */
    $statBase         = \App\Models\Invoice::whereNotNull('listing_id');
    $statTotal        = (clone $statBase)->count();
    $statAwaiting     = (clone $statBase)->where('payment_status', 'pending')->count();
    $statReadyPayout  = (clone $statBase)
        ->where('payment_status', 'paid')
        ->whereHas('listing', fn ($l) => $l->where('pickup_confirmed', true))
        ->where(function ($q) {
            $q->whereDoesntHave('payout')
              ->orWhereHas('payout', fn ($p) => $p->whereNotIn('status', ['sent', 'paid_successfully']));
        })->count();
    $statClosed       = (clone $statBase)
        ->whereHas('payout', fn ($p) => $p->whereIn('status', ['sent', 'paid_successfully']))
        ->count();
@endphp

<style>
    :root { --navy:#063466; --navy-light:#e8eef6; --navy-mid:#0d4d8c; }

    /* ── Header ─────────────────────────────────────────────── */
    .sp-header {
        background:#fff; border-radius:12px;
        padding:1.5rem 1.75rem; margin-bottom:1.5rem;
        border-left:4px solid var(--navy);
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;
    }
    .sp-header-text h1 {
        font-size:1.35rem; font-weight:700; color:var(--navy);
        margin:0 0 0.2rem; display:flex; align-items:center; gap:8px;
    }
    .sp-header-text h1 .material-icons-round { font-size:1.3rem; }
    .sp-header-text p { margin:0; color:#64748b; font-size:0.875rem; }
    .sp-export-btn {
        display:inline-flex; align-items:center; gap:6px;
        padding:0.5rem 1.1rem; border-radius:9px; font-size:0.8125rem; font-weight:600;
        background:var(--navy-light); color:var(--navy); text-decoration:none; transition:background 0.2s;
    }
    .sp-export-btn:hover { background:#cddaf0; }
    .sp-export-btn .material-icons-round { font-size:16px; }

    /* ── Stat cards ──────────────────────────────────────────── */
    .sp-stats {
        display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
        gap:1rem; margin-bottom:1.5rem;
    }
    .sp-stat-card {
        background:#fff; border-radius:12px; padding:1.25rem 1.5rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07); display:flex; align-items:center; gap:1rem;
    }
    .sp-stat-icon {
        width:44px; height:44px; border-radius:10px;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .sp-stat-icon .material-icons-round { font-size:22px; }
    .sp-stat-label { font-size:0.75rem; font-weight:600; color:#64748b; margin-bottom:2px; }
    .sp-stat-value { font-size:1.5rem; font-weight:700; line-height:1; }

    /* ── Filter bar ──────────────────────────────────────────── */
    .sp-filter-bar {
        background:#fff; border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.5rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center;
    }
    .sp-filter-tabs { display:flex; flex-wrap:wrap; gap:4px; }
    .sp-filter-tab {
        padding:0.45rem 1rem; border-radius:8px; font-size:0.8125rem; font-weight:600;
        border:1.5px solid #e2e8f0; background:#fff; color:#64748b;
        cursor:pointer; text-decoration:none; transition:all 0.15s; white-space:nowrap;
    }
    .sp-filter-tab:hover { background:var(--navy); color:#fff; border-color:var(--navy); }
    .sp-filter-tab.is-active { background:var(--navy); color:#fff; border-color:var(--navy); }
    .sp-divider { width:1px; height:28px; background:#e2e8f0; flex-shrink:0; }
    .sp-search-wrap {
        flex:1; min-width:220px; position:relative;
    }
    .sp-search-wrap .material-icons-round {
        position:absolute; left:10px; top:50%; transform:translateY(-50%);
        font-size:18px; color:#94a3b8; pointer-events:none;
    }
    .sp-search-wrap input {
        width:100%; padding:0.5rem 0.875rem 0.5rem 2.25rem;
        border:1.5px solid #e2e8f0; border-radius:8px;
        font-size:0.875rem; color:#374151; outline:none; transition:border-color 0.2s; box-sizing:border-box;
    }
    .sp-search-wrap input:focus { border-color:var(--navy); }
    .sp-btn {
        padding:0.5rem 1.1rem; border-radius:8px; font-size:0.8125rem; font-weight:600;
        border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px;
        transition:background 0.2s; text-decoration:none; white-space:nowrap;
    }
    .sp-btn--primary { background:var(--navy); color:#fff; }
    .sp-btn--primary:hover { background:var(--navy-mid); }
    .sp-btn--light { background:#f1f5f9; color:#475569; border:1.5px solid #e2e8f0; }
    .sp-btn--light:hover { background:#e2e8f0; }
    .sp-btn .material-icons-round { font-size:15px; }
    .sp-sort-select {
        padding:0.5rem 2rem 0.5rem 0.75rem; border:1.5px solid #e2e8f0; border-radius:8px;
        font-size:0.8125rem; color:#374151; background:#fff; outline:none; cursor:pointer;
        appearance:none; -webkit-appearance:none;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
        background-repeat:no-repeat; background-position:right 8px center;
    }
    .sp-sort-select:focus { border-color:var(--navy); }

    /* ── Table card ──────────────────────────────────────────── */
    .sp-card {
        background:#fff; border-radius:12px;
        box-shadow:0 1px 4px rgba(6,52,102,0.07); overflow:hidden;
    }
    .sp-card-header {
        padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between;
    }
    .sp-card-header h2 {
        font-size:0.9375rem; font-weight:700; color:#0f172a; margin:0;
        display:flex; align-items:center; gap:6px;
    }
    .sp-card-header h2 .material-icons-round { font-size:18px; color:var(--navy); }
    .sp-count {
        font-size:0.75rem; font-weight:600; color:var(--navy);
        background:var(--navy-light); padding:2px 10px; border-radius:999px;
    }

    .sp-table { width:100%; border-collapse:collapse; }
    .sp-table thead th {
        padding:0.75rem 1.25rem; text-align:left;
        font-size:0.6875rem; font-weight:700; text-transform:uppercase;
        letter-spacing:0.06em; color:#64748b; background:#f8fafc;
        border-bottom:1px solid #f1f5f9; white-space:nowrap;
    }
    .sp-table thead th:last-child { text-align:right; }
    .sp-table tbody tr { border-bottom:1px solid #f8fafc; transition:background 0.1s; }
    .sp-table tbody tr:last-child { border-bottom:none; }
    .sp-table tbody tr:hover { background:#fafbfc; }
    .sp-table tbody td { padding:0.875rem 1.25rem; font-size:0.875rem; color:#374151; vertical-align:middle; }

    /* ── Cell helpers ────────────────────────────────────────── */
    .sp-item-id   { font-size:0.8rem; font-weight:700; color:var(--navy); font-family:monospace; }
    .sp-sub       { font-size:0.72rem; color:#94a3b8; margin-top:2px; }
    .sp-seller    { font-weight:600; color:#0f172a; }
    .sp-price     { font-weight:700; color:#0f172a; }
    .sp-payout    { font-weight:600; color:#15803d; }
    .sp-payout--pending { font-size:0.75rem; color:#94a3b8; font-style:italic; }

    /* ── Action buttons ──────────────────────────────────────── */
    .sp-btn-pay {
        display:inline-flex; align-items:center; gap:5px;
        padding:0.4rem 0.95rem; border-radius:7px;
        background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 100%);
        color:#fff; font-size:0.8125rem; font-weight:600;
        border:none; cursor:pointer; transition:opacity 0.15s; white-space:nowrap;
    }
    .sp-btn-pay:hover { opacity:0.9; }
    .sp-btn-pay .material-icons-round { font-size:15px; }

    /* ── Empty state ─────────────────────────────────────────── */
    .sp-empty { text-align:center; padding:3.5rem 1rem; color:#94a3b8; }
    .sp-empty .material-icons-round { font-size:48px; display:block; margin-bottom:0.75rem; opacity:0.35; }
    .sp-empty p { margin:0; font-size:0.9375rem; }

    /* ── Pagination ──────────────────────────────────────────── */
    .sp-pagination { padding:1rem 1.25rem; border-top:1px solid #f1f5f9; }
</style>

<div>

    {{-- ── Page header ── --}}
    <div class="sp-header">
        <div class="sp-header-text">
            <h1>
                <span class="material-icons-round">account_balance_wallet</span>
                Sales / Payouts
            </h1>
            <p>Manage completed auctions and process seller payouts</p>
        </div>
        <a href="{{ route('admin.payments', array_merge($baseQuery, ['export' => 'csv'])) }}" class="sp-export-btn">
            <span class="material-icons-round">download</span>
            Export CSV
        </a>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="sp-stats">
        <div class="sp-stat-card">
            <div class="sp-stat-icon" style="background:#e8eef6">
                <span class="material-icons-round" style="color:#063466">receipt_long</span>
            </div>
            <div>
                <div class="sp-stat-label">Total Sales</div>
                <div class="sp-stat-value" style="color:#063466">{{ number_format($statTotal) }}</div>
            </div>
        </div>
        <div class="sp-stat-card">
            <div class="sp-stat-icon" style="background:#fef9c3">
                <span class="material-icons-round" style="color:#a16207">hourglass_top</span>
            </div>
            <div>
                <div class="sp-stat-label">Awaiting Payment</div>
                <div class="sp-stat-value" style="color:#a16207">{{ number_format($statAwaiting) }}</div>
            </div>
        </div>
        <div class="sp-stat-card">
            <div class="sp-stat-icon" style="background:#dbeafe">
                <span class="material-icons-round" style="color:#1d4ed8">payments</span>
            </div>
            <div>
                <div class="sp-stat-label">Ready for Payout</div>
                <div class="sp-stat-value" style="color:#1d4ed8">{{ number_format($statReadyPayout) }}</div>
            </div>
        </div>
        <div class="sp-stat-card">
            <div class="sp-stat-icon" style="background:#dcfce7">
                <span class="material-icons-round" style="color:#16a34a">check_circle</span>
            </div>
            <div>
                <div class="sp-stat-label">Closed / Paid</div>
                <div class="sp-stat-value" style="color:#16a34a">{{ number_format($statClosed) }}</div>
            </div>
        </div>
    </div>

    {{-- ── Filter bar ── --}}
    <div class="sp-filter-bar">
        {{-- Status tabs --}}
        <nav class="sp-filter-tabs" aria-label="Filter by status">
            @foreach([
                'all'              => 'All',
                'awaiting_payment' => 'Awaiting Payment',
                'payment_received' => 'Payment Received',
                'ready_for_payout' => 'Ready for Payout',
                'closed'           => 'Closed',
            ] as $key => $label)
                <a href="{{ route('admin.payments', array_merge($baseQuery, ['tab' => $key])) }}"
                   class="sp-filter-tab {{ ($tab ?? 'all') === $key ? 'is-active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>

        <div class="sp-divider"></div>

        {{-- Search --}}
        <form method="get" action="{{ route('admin.payments') }}" class="sp-search-wrap" style="min-width:220px;max-width:320px">
            <input type="hidden" name="tab"  value="{{ $tab  ?? 'all' }}">
            <input type="hidden" name="sort" value="{{ $sort ?? 'newest' }}">
            <span class="material-icons-round">search</span>
            <input type="search" name="q" value="{{ request('q') }}"
                   placeholder="Search Item ID or Seller…"
                   onchange="this.form.submit()">
        </form>

        {{-- Sort --}}
        <form method="get" action="{{ route('admin.payments') }}" style="display:contents">
            <input type="hidden" name="tab" value="{{ $tab ?? 'all' }}">
            @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
            <select name="sort" class="sp-sort-select" onchange="this.form.submit()" aria-label="Sort order">
                <option value="newest"    @selected(($sort ?? 'newest') === 'newest')>Newest first</option>
                <option value="oldest"    @selected(($sort ?? '') === 'oldest')>Oldest first</option>
                <option value="sale_high" @selected(($sort ?? '') === 'sale_high')>Sale: high → low</option>
                <option value="sale_low"  @selected(($sort ?? '') === 'sale_low')>Sale: low → high</option>
            </select>
        </form>
    </div>

    {{-- ── Table card ── --}}
    <div class="sp-card">
        <div class="sp-card-header">
            <h2>
                <span class="material-icons-round">table_rows</span>
                Sales Records
            </h2>
            <span class="sp-count">{{ $invoices->total() }} {{ $invoices->total() === 1 ? 'record' : 'records' }}</span>
        </div>

        <div style="overflow-x:auto">
            <table class="sp-table">
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Seller</th>
                        <th>Sale Price</th>
                        <th>Payout</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        @php
                            $listing = $invoice->listing;
                            $itemRef = $invoice->item_id
                                ?? ($listing?->item_number
                                    ?? ('CM' . str_pad((string) ($listing?->id ?? $invoice->listing_id), 6, '0', STR_PAD_LEFT)));
                            $pipe   = $invoice->adminSalesPayoutPipelineStatus();
                            $payout = $invoice->payout;
                            $sellerName = $invoice->seller?->name
                                ? ucwords(strtolower($invoice->seller->name))
                                : '—';
                        @endphp
                        <tr>
                            <td>
                                <div class="sp-item-id">#{{ $itemRef }}</div>
                                @if($listing?->year || $listing?->make)
                                    <div class="sp-sub">{{ strtoupper(trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? ''))) }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="sp-seller">{{ $sellerName }}</div>
                                @if($invoice->seller?->email)
                                    <div class="sp-sub">{{ $invoice->seller->email }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="sp-price">${{ number_format((float) $invoice->winning_bid_amount, 2) }}</div>
                            </td>
                            <td>
                                @if($payout && $payout->net_payout !== null)
                                    <div class="sp-payout">${{ number_format((float) $payout->net_payout, 2) }}</div>
                                @elseif($invoice->payment_status === 'paid')
                                    <span class="sp-payout--pending">Pending</span>
                                @else
                                    <span style="color:#cbd5e1">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $pipe['badge_class'] }}">
                                    {{ $pipe['label'] }}
                                </span>
                            </td>
                            <td style="text-align:right">
                                @if($pipe['key'] === 'ready_for_payout' && $payout && in_array($payout->status, ['pending', 'processing', 'on_hold'], true))
                                    @php
                                        $pm = $invoice->seller?->payoutMethod;
                                        $bankPayload = json_encode([
                                            'bank'      => $pm?->bank_name ?? '',
                                            'holder'    => $pm?->account_holder_name ?? '',
                                            'account'   => $pm?->account_number ?? '',
                                            'routing'   => $pm?->routing_number ?? '',
                                            'swift'     => $pm?->swift_number ?? '',
                                            'country'   => $pm?->country ?? '',
                                            'card_last4'=> ($pm && $pm->card_number && strlen($pm->card_number) >= 4) ? substr($pm->card_number, -4) : '',
                                            'extra'     => $pm?->additional_instructions ?? '',
                                        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                                    @endphp
                                    <button type="button"
                                            class="sp-btn-pay"
                                            data-open-pay-seller
                                            data-action-url="{{ route('admin.payouts.update-status', $payout) }}"
                                            data-payout-number="{{ e($payout->payout_number) }}"
                                            data-amount="{{ number_format((float) $payout->net_payout, 2, '.', '') }}"
                                            data-seller="{{ e($sellerName) }}"
                                            data-bank-payload="{{ $bankPayload }}">
                                        <span class="material-icons-round">payments</span>
                                        Pay Seller
                                    </button>
                                @else
                                    <span style="color:#cbd5e1;font-size:0.875rem"
                                          title="{{ $pipe['key'] === 'awaiting_payment' ? 'No action until buyer pays.' : '' }}">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="sp-empty">
                                    <span class="material-icons-round">receipt_long</span>
                                    <p>No sales match this filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($invoices->hasPages())
            <div class="sp-pagination" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;font-size:0.875rem;color:#64748b">
                <p style="margin:0">
                    Showing <strong style="color:#0f172a">{{ $invoices->firstItem() }}</strong>
                    – <strong style="color:#0f172a">{{ $invoices->lastItem() }}</strong>
                    of <strong style="color:#0f172a">{{ $invoices->total() }}</strong>
                </p>
                <div>{{ $invoices->withQueryString()->links() }}</div>
            </div>
        @elseif($invoices->total() > 0)
            <div class="sp-pagination" style="font-size:0.875rem;color:#64748b">
                Showing <strong style="color:#0f172a">{{ $invoices->total() }}</strong>
                {{ $invoices->total() === 1 ? 'record' : 'records' }}
            </div>
        @endif
    </div>

</div>

{{-- ── Pay Seller modal (functionality preserved) ── --}}
<div id="pay-seller-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true" role="dialog" aria-labelledby="pay-seller-modal-title">
    <div class="absolute inset-0 bg-slate-900/50 transition-opacity" data-close-pay-seller></div>
    <div class="relative flex min-h-full items-center justify-center p-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-lg rounded-2xl bg-white shadow-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 flex items-start justify-between gap-3">
                <div>
                    <h3 id="pay-seller-modal-title" class="text-lg font-bold text-slate-900">Confirm seller payout</h3>
                    <p class="text-xs text-slate-500 mt-1">Record the wire after you have sent funds to the seller&apos;s bank.</p>
                </div>
                <button type="button" class="p-1 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" data-close-pay-seller aria-label="Close">
                    <span class="material-icons-round text-xl">close</span>
                </button>
            </div>
            <form id="pay-seller-modal-form" method="POST" class="px-5 py-4 space-y-4">
                @csrf
                <input type="hidden" name="status" value="paid_successfully">

                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 space-y-2">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Payout amount</p>
                    <p class="text-2xl font-bold text-slate-900" id="pay-modal-amount">$0.00</p>
                    <p class="text-xs text-slate-500"><span class="font-medium text-slate-600">Payout #</span> <span id="pay-modal-payout-number"></span></p>
                    <p class="text-xs text-slate-500"><span class="font-medium text-slate-600">Seller</span> <span id="pay-modal-seller"></span></p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Banking information</p>
                    <div id="pay-modal-banking" class="rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-800 space-y-1.5 font-mono text-xs"></div>
                </div>

                <div>
                    <label for="pay-modal-transaction-ref" class="block text-xs font-semibold text-slate-700 mb-1">
                        Transaction / wire reference <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="transaction_reference" id="pay-modal-transaction-ref" required maxlength="255"
                           class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Bank confirmation / reference number">
                </div>
                <div>
                    <label for="pay-modal-date-sent" class="block text-xs font-semibold text-slate-700 mb-1">Date sent</label>
                    <input type="date" name="date_sent" id="pay-modal-date-sent" required
                           class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="pay-modal-notes" class="block text-xs font-semibold text-slate-700 mb-1">Finance notes (optional)</label>
                    <textarea name="finance_notes" id="pay-modal-notes" rows="2" maxlength="1000"
                              class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Internal notes"></textarea>
                </div>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                            data-close-pay-seller>Cancel</button>
                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm hover:opacity-95"
                            style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 100%)">
                        Confirm payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('pay-seller-modal');
    var form  = document.getElementById('pay-seller-modal-form');
    if (!modal || !form) return;

    function todayYmd() {
        var d = new Date();
        return d.getFullYear() + '-' +
               String(d.getMonth() + 1).padStart(2, '0') + '-' +
               String(d.getDate()).padStart(2, '0');
    }

    function esc(s) {
        if (s == null || s === '') return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function openModal(btn) {
        var url = btn.getAttribute('data-action-url');
        if (!url) return;
        form.action = url;
        document.getElementById('pay-modal-amount').textContent        = '$' + (btn.getAttribute('data-amount') || '0');
        document.getElementById('pay-modal-payout-number').textContent = btn.getAttribute('data-payout-number') || '—';
        document.getElementById('pay-modal-seller').textContent        = btn.getAttribute('data-seller') || '—';

        var bankingEl = document.getElementById('pay-modal-banking');
        var payload   = {};
        try { payload = JSON.parse(btn.getAttribute('data-bank-payload') || '{}'); } catch (e) {}
        var lines = [];
        if (payload.bank)      lines.push('<p><span class="text-slate-500 font-sans">Bank</span><br>'             + esc(payload.bank)      + '</p>');
        if (payload.holder)    lines.push('<p><span class="text-slate-500 font-sans">Account holder</span><br>'   + esc(payload.holder)    + '</p>');
        if (payload.account)   lines.push('<p><span class="text-slate-500 font-sans">Account</span><br>'          + esc(payload.account)   + '</p>');
        if (payload.routing)   lines.push('<p><span class="text-slate-500 font-sans">Routing / transfer</span><br>'+ esc(payload.routing)  + '</p>');
        if (payload.swift)     lines.push('<p><span class="text-slate-500 font-sans">SWIFT</span><br>'            + esc(payload.swift)     + '</p>');
        if (payload.country)   lines.push('<p><span class="text-slate-500 font-sans">Region</span><br>'           + esc(payload.country)   + '</p>');
        if (payload.card_last4)lines.push('<p><span class="text-slate-500 font-sans">Payout card</span><br>****' + esc(payload.card_last4) + '</p>');
        if (payload.extra)     lines.push('<p><span class="text-slate-500 font-sans">Instructions</span><br>'     + esc(payload.extra)     + '</p>');
        if (!lines.length) {
            lines.push('<p class="text-amber-800 font-sans">No payout method on file. Verify banking details before confirming.</p>');
        }
        bankingEl.innerHTML = lines.join('');

        form.querySelector('#pay-modal-transaction-ref').value = '';
        form.querySelector('#pay-modal-notes').value           = '';
        var ds = form.querySelector('#pay-modal-date-sent');
        if (ds) ds.value = todayYmd();

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-open-pay-seller]').forEach(function (btn) {
        btn.addEventListener('click', function () { openModal(btn); });
    });
    modal.querySelectorAll('[data-close-pay-seller]').forEach(function (el) {
        el.addEventListener('click', closeModal);
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
})();
</script>

@endsection
