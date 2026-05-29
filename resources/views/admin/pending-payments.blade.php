@extends('layouts.admin')

@section('title', 'Pending Payments - Admin')

@section('content')
@php
    $statOverdue = $invoices->getCollection()->filter(
        fn ($inv) => $inv->payment_deadline && $inv->payment_deadline->isPast()
    )->count();
@endphp

<style>
    :root { --navy:#063466; --navy-light:#e8eef6; --navy-mid:#0d4d8c; }

    /* ── Header ─────────────────────────────────────────────── */
    .pp-header {
        background:#fff; border-radius:12px;
        padding:1.5rem 1.75rem; margin-bottom:1.5rem;
        border-left:4px solid var(--navy);
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
    }
    .pp-header h1 {
        font-size:1.35rem; font-weight:700; color:var(--navy);
        margin:0 0 0.2rem; display:flex; align-items:center; gap:8px;
    }
    .pp-header h1 .material-icons-round { font-size:1.3rem; }
    .pp-header p { margin:0; color:#64748b; font-size:0.875rem; }

    /* ── Stat cards ──────────────────────────────────────────── */
    .pp-stats {
        display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
        gap:1rem; margin-bottom:1.5rem;
    }
    .pp-stat-card {
        background:#fff; border-radius:12px; padding:1.25rem 1.5rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07); display:flex; align-items:center; gap:1rem;
    }
    .pp-stat-icon {
        width:44px; height:44px; border-radius:10px;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .pp-stat-icon .material-icons-round { font-size:22px; }
    .pp-stat-label { font-size:0.75rem; font-weight:600; color:#64748b; margin-bottom:2px; }
    .pp-stat-value { font-size:1.5rem; font-weight:700; line-height:1; }

    /* ── Table card ──────────────────────────────────────────── */
    .pp-card {
        background:#fff; border-radius:12px;
        box-shadow:0 1px 4px rgba(6,52,102,0.07); overflow:hidden;
    }
    .pp-card-header {
        padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between;
    }
    .pp-card-header h2 {
        font-size:0.9375rem; font-weight:700; color:#0f172a; margin:0;
        display:flex; align-items:center; gap:6px;
    }
    .pp-card-header h2 .material-icons-round { font-size:18px; color:var(--navy); }
    .pp-count {
        font-size:0.75rem; font-weight:600; color:var(--navy);
        background:var(--navy-light); padding:2px 10px; border-radius:999px;
    }

    .pp-table { width:100%; border-collapse:collapse; }
    .pp-table thead th {
        padding:0.75rem 1.25rem; text-align:left;
        font-size:0.6875rem; font-weight:700; text-transform:uppercase;
        letter-spacing:0.06em; color:#64748b; background:#f8fafc;
        border-bottom:1px solid #f1f5f9; white-space:nowrap;
    }
    .pp-table tbody tr { border-bottom:1px solid #f8fafc; transition:background 0.1s; }
    .pp-table tbody tr:last-child { border-bottom:none; }
    .pp-table tbody tr:hover { background:#fafbfc; }
    .pp-table tbody td { padding:0.875rem 1.25rem; font-size:0.875rem; color:#374151; vertical-align:middle; }

    /* ── Cell helpers ────────────────────────────────────────── */
    .pp-inv-num  { font-size:0.8rem; font-weight:700; color:var(--navy); font-family:monospace; }
    .pp-sub      { font-size:0.72rem; color:#94a3b8; margin-top:2px; }
    .pp-buyer    { font-weight:600; color:#0f172a; }
    .pp-amount   { font-weight:700; color:#ea580c; }
    .pp-bid      { font-size:0.72rem; color:#94a3b8; margin-top:2px; }

    /* ── Deadline badge ──────────────────────────────────────── */
    .pp-overdue  { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:999px; font-size:0.7rem; font-weight:700; background:#fee2e2; color:#b91c1c; }
    .pp-due-soon { display:inline-flex; align-items:center; gap:4px; padding:2px 8px; border-radius:999px; font-size:0.7rem; font-weight:700; background:#fef3c7; color:#92400e; }

    /* ── Empty state ─────────────────────────────────────────── */
    .pp-empty { text-align:center; padding:3.5rem 1rem; color:#94a3b8; }
    .pp-empty .material-icons-round { font-size:48px; display:block; margin-bottom:0.75rem; opacity:0.35; }
    .pp-empty p { margin:0; font-size:0.9375rem; }

    .pp-pagination { padding:1rem 1.25rem; border-top:1px solid #f1f5f9; }
</style>

<div class="pb-10">

    {{-- ── Page header ── --}}
    <div class="pp-header">
        <h1>
            <span class="material-icons-round">schedule</span>
            Pending Payments
        </h1>
        <p>Buyers who owe payment after winning an auction</p>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="pp-stats">
        <div class="pp-stat-card">
            <div class="pp-stat-icon" style="background:#e8eef6">
                <span class="material-icons-round" style="color:#063466">receipt_long</span>
            </div>
            <div>
                <div class="pp-stat-label">Total Pending Invoices</div>
                <div class="pp-stat-value" style="color:#063466">{{ number_format($invoices->total()) }}</div>
            </div>
        </div>
        <div class="pp-stat-card">
            <div class="pp-stat-icon" style="background:#ffedd5">
                <span class="material-icons-round" style="color:#ea580c">payments</span>
            </div>
            <div>
                <div class="pp-stat-label">Total Amount Owed</div>
                <div class="pp-stat-value" style="color:#ea580c">${{ number_format($totalOwed, 2) }}</div>
            </div>
        </div>
        @if($statOverdue > 0)
        <div class="pp-stat-card">
            <div class="pp-stat-icon" style="background:#fee2e2">
                <span class="material-icons-round" style="color:#b91c1c">warning</span>
            </div>
            <div>
                <div class="pp-stat-label">Overdue</div>
                <div class="pp-stat-value" style="color:#b91c1c">{{ number_format($statOverdue) }}</div>
            </div>
        </div>
        @endif
    </div>

    {{-- ── Table card ── --}}
    <div class="pp-card">
        <div class="pp-card-header">
            <h2>
                <span class="material-icons-round">table_rows</span>
                Outstanding Invoices
            </h2>
            <span class="pp-count">{{ $invoices->total() }} {{ $invoices->total() === 1 ? 'invoice' : 'invoices' }}</span>
        </div>

        <div style="overflow-x:auto">
            <table class="pp-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Buyer</th>
                        <th>Item</th>
                        <th>Amount Owed</th>
                        <th>Payment Deadline</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    @php
                        $isOverdue  = $invoice->payment_deadline && $invoice->payment_deadline->isPast();
                        $isDueSoon  = !$isOverdue && $invoice->payment_deadline
                            && $invoice->payment_deadline->diffInHours(now()) < 24
                            && $invoice->payment_deadline->isFuture();
                        $listingName = $invoice->item_name
                            ?? ($invoice->listing
                                ? strtoupper(trim(($invoice->listing->year ?? '') . ' ' . ($invoice->listing->make ?? '') . ' ' . ($invoice->listing->model ?? '')))
                                : null);
                    @endphp
                    <tr>
                        <td>
                            <div class="pp-inv-num">{{ $invoice->invoice_number }}</div>
                            <div class="pp-sub">{{ $invoice->created_at->format('M j, Y') }}</div>
                        </td>
                        <td>
                            @if($invoice->buyer)
                                <div class="pp-buyer">{{ ucwords(strtolower($invoice->buyer->name)) }}</div>
                                <div class="pp-sub">{{ $invoice->buyer->email }}</div>
                                @if($invoice->buyer->phone)
                                    <div class="pp-sub" style="font-family:monospace">{{ $invoice->buyer->phone }}</div>
                                @endif
                            @else
                                <span style="color:#94a3b8;font-style:italic;font-size:0.875rem">Buyer not found</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;color:#0f172a;font-size:0.875rem">{{ $listingName ?: '—' }}</div>
                            @if($invoice->listing->item_number ?? false)
                                <div class="pp-sub" style="font-family:monospace">{{ $invoice->listing->item_number }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="pp-amount">${{ number_format($invoice->total_amount_due, 2) }}</div>
                            <div class="pp-bid">Winning bid: ${{ number_format($invoice->winning_bid_amount, 2) }}</div>
                        </td>
                        <td>
                            @if($invoice->payment_deadline)
                                <div style="font-size:0.875rem;font-weight:{{ $isOverdue ? '700' : '400' }};color:{{ $isOverdue ? '#b91c1c' : '#374151' }}">
                                    {{ $invoice->payment_deadline->format('M j, Y g:i A') }}
                                </div>
                                @if($isOverdue)
                                    <span class="pp-overdue" style="margin-top:4px;display:inline-flex">
                                        <span class="material-icons-round" style="font-size:12px">warning</span>
                                        Overdue
                                    </span>
                                @elseif($isDueSoon)
                                    <span class="pp-due-soon" style="margin-top:4px;display:inline-flex">
                                        <span class="material-icons-round" style="font-size:12px">schedule</span>
                                        Due soon
                                    </span>
                                @else
                                    <div class="pp-sub">{{ $invoice->payment_deadline->diffForHumans() }}</div>
                                @endif
                            @else
                                <span style="color:#cbd5e1;font-size:0.875rem">No deadline</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="pp-empty">
                                <span class="material-icons-round">check_circle</span>
                                <p>No pending payments — all buyers are up to date.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="pp-pagination" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;font-size:0.875rem;color:#64748b">
                <p style="margin:0">
                    Showing <strong style="color:#0f172a">{{ $invoices->firstItem() }}</strong>
                    – <strong style="color:#0f172a">{{ $invoices->lastItem() }}</strong>
                    of <strong style="color:#0f172a">{{ $invoices->total() }}</strong>
                </p>
                <div>{{ $invoices->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>

</div>

@endsection
