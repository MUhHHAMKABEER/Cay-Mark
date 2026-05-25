@extends('layouts.Seller')

@section('title', 'Payout History')

@section('content')
<style>
    :root { --navy:#063466; --navy-light:#e8eef6; --navy-mid:#0d4d8c; }

    .ph-header {
        background:#fff;
        border-radius:12px;
        padding:1.5rem 1.75rem;
        margin-bottom:1.5rem;
        border-left:4px solid var(--navy);
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
    }
    .ph-header h1 { font-size:1.35rem; font-weight:700; color:var(--navy); margin:0 0 0.2rem; }
    .ph-header p  { margin:0; color:#64748b; font-size:0.875rem; }

    .ph-card {
        background:#fff;
        border-radius:12px;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        overflow:hidden;
    }
    .ph-card-header {
        padding:1rem 1.5rem;
        border-bottom:1px solid #f1f5f9;
        display:flex;
        align-items:center;
        justify-content:space-between;
    }
    .ph-card-header h2 { font-size:0.9375rem; font-weight:700; color:#0f172a; margin:0; }
    .ph-count { font-size:0.75rem; font-weight:600; color:var(--navy); background:var(--navy-light); padding:2px 10px; border-radius:999px; }

    .ph-table { width:100%; border-collapse:collapse; }
    .ph-table thead th {
        padding:0.75rem 1.25rem;
        text-align:left;
        font-size:0.6875rem;
        font-weight:700;
        text-transform:uppercase;
        letter-spacing:0.06em;
        color:#64748b;
        background:#f8fafc;
        border-bottom:1px solid #f1f5f9;
        white-space:nowrap;
    }
    .ph-table tbody tr { border-bottom:1px solid #f8fafc; transition:background 0.1s; }
    .ph-table tbody tr:last-child { border-bottom:none; }
    .ph-table tbody tr:hover { background:#fafbfc; }
    .ph-table tbody td { padding:0.875rem 1.25rem; font-size:0.875rem; color:#374151; vertical-align:middle; }

    .ph-ref    { font-size:0.75rem; font-weight:600; color:var(--navy); font-family:monospace; }
    .ph-vehicle { font-weight:600; color:#0f172a; font-size:0.875rem; }
    .ph-buyer   { font-size:0.75rem; color:#94a3b8; margin-top:2px; }
    .ph-amount  { font-weight:700; color:#0f172a; }
    .ph-comm    { font-size:0.72rem; color:#94a3b8; margin-top:2px; }
    .ph-net     { font-weight:700; color:#16a34a; }

    .ph-badge {
        display:inline-flex; align-items:center; gap:4px;
        font-size:0.72rem; font-weight:700; padding:3px 9px;
        border-radius:999px; white-space:nowrap;
    }
    .ph-badge::before { content:''; display:inline-block; width:6px; height:6px; border-radius:50%; background:currentColor; opacity:0.8; }
    .ph-badge--completed { background:#dcfce7; color:#16a34a; }
    .ph-badge--pending   { background:#fef9c3; color:#a16207; }
    .ph-badge--processing{ background:#dbeafe; color:#2563eb; }
    .ph-badge--cancelled { background:#f1f5f9; color:#64748b; }

    .ph-date { color:#374151; }
    .ph-date-rel { font-size:0.72rem; color:#94a3b8; margin-top:2px; }

    .ph-empty { text-align:center; padding:3.5rem 1rem; color:#94a3b8; }
    .ph-empty .material-icons-round { font-size:48px; display:block; margin-bottom:0.75rem; opacity:0.4; }
    .ph-empty p { margin:0; font-size:0.9375rem; }

    .ph-pagination { padding:1rem 1.25rem; border-top:1px solid #f1f5f9; }
</style>

<div>
    <div class="ph-header">
        <h1>
            <span class="material-icons-round" style="font-size:1.25rem;vertical-align:-3px;margin-right:6px">account_balance_wallet</span>
            Payout History
        </h1>
        <p>All payouts issued for your completed auctions</p>
    </div>

    <div class="ph-card">
        <div class="ph-card-header">
            <h2>Payouts</h2>
            <span class="ph-count">{{ $payouts->total() }} {{ Str::plural('record', $payouts->total()) }}</span>
        </div>

        <div style="overflow-x:auto">
            <table class="ph-table">
                <thead>
                    <tr>
                        <th>Payout #</th>
                        <th>Vehicle</th>
                        <th>Buyer</th>
                        <th>Sale Price</th>
                        <th>Commission</th>
                        <th>Net Payout</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payouts as $payout)
                    @php
                        $listing = $payout->listing;
                        $buyer   = $payout->invoice?->buyer;
                        $status  = strtolower($payout->status ?? 'pending');
                        $vehicle = $listing
                            ? trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? ''))
                            : ($payout->item_title ?? '—');
                    @endphp
                    <tr>
                        <td>
                            <span class="ph-ref">{{ $payout->payout_number ?? '#' . $payout->id }}</span>
                        </td>
                        <td>
                            <div class="ph-vehicle">{{ $vehicle ?: '—' }}</div>
                        </td>
                        <td>
                            @if($buyer)
                                <div style="font-size:0.875rem;color:#374151">{{ $buyer->name }}</div>
                                <div class="ph-buyer">{{ $buyer->email }}</div>
                            @elseif($payout->buyer_name)
                                <div style="font-size:0.875rem;color:#374151">{{ $payout->buyer_name }}</div>
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="ph-amount">${{ number_format($payout->sale_price ?? 0, 2) }}</div>
                        </td>
                        <td>
                            <div style="color:#dc2626;font-weight:600">-${{ number_format($payout->seller_commission ?? 0, 2) }}</div>
                        </td>
                        <td>
                            <div class="ph-net">${{ number_format($payout->net_payout ?? 0, 2) }}</div>
                        </td>
                        <td>
                            <span class="ph-badge ph-badge--{{ $status }}">{{ ucfirst($status) }}</span>
                            @if($payout->payment_method)
                                <div style="font-size:0.7rem;color:#94a3b8;margin-top:3px">{{ ucfirst($payout->payment_method) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($payout->payout_processed_at)
                                <div class="ph-date">{{ \Carbon\Carbon::parse($payout->payout_processed_at)->format('M j, Y') }}</div>
                                <div class="ph-date-rel">{{ \Carbon\Carbon::parse($payout->payout_processed_at)->diffForHumans() }}</div>
                            @elseif($payout->created_at)
                                <div class="ph-date">{{ $payout->created_at->format('M j, Y') }}</div>
                                <div class="ph-date-rel">{{ $payout->created_at->diffForHumans() }}</div>
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="ph-empty">
                                <span class="material-icons-round">account_balance_wallet</span>
                                <p>No payouts yet — they appear here once an auction is completed and funds are released</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payouts->hasPages())
        <div class="ph-pagination">
            {{ $payouts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
