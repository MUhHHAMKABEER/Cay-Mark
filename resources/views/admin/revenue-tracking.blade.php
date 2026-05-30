@extends('layouts.admin')
@section('title', 'Revenue Tracking — Admin')
@section('content')
<style>
    :root{--navy:#063466;--navy-light:#e8eef6;}
    .rt-header{background:#fff;border-radius:12px;padding:1.5rem 1.75rem;margin-bottom:1.5rem;border-left:4px solid var(--navy);box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem}
    .rt-header h1{font-size:1.35rem;font-weight:700;color:var(--navy);margin:0 0 .2rem;display:flex;align-items:center;gap:8px}
    .rt-header h1 .material-icons-round{font-size:1.3rem}
    .rt-header p{margin:0;color:#64748b;font-size:.875rem}
    .rt-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem}
    .rt-stat{background:#fff;border-radius:12px;padding:1.25rem 1.5rem;box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;align-items:center;gap:1rem}
    .rt-stat-ico{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .rt-stat-ico .material-icons-round{font-size:22px}
    .rt-stat-lbl{font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em}
    .rt-stat-val{font-size:1.5rem;font-weight:700;color:#0f172a;line-height:1.1;margin-top:2px}
    .rt-stat-sub{font-size:.72rem;color:#94a3b8;margin-top:2px}
    .rt-card{background:#fff;border-radius:12px;box-shadow:0 1px 4px rgba(6,52,102,.07);overflow:hidden;margin-bottom:1.25rem}
    .rt-card-hdr{padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
    .rt-card-hdr h2{font-size:.9375rem;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:6px}
    .rt-card-hdr h2 .material-icons-round{font-size:18px;color:var(--navy)}
    .rt-table{width:100%;border-collapse:collapse}
    .rt-table thead th{padding:.75rem 1.25rem;text-align:left;font-size:.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;background:#f8fafc;border-bottom:1px solid #f1f5f9;white-space:nowrap}
    .rt-table tbody tr{border-bottom:1px solid #f8fafc;transition:background .1s}
    .rt-table tbody tr:last-child{border-bottom:none}
    .rt-table tbody tr:hover{background:#fafbfc}
    .rt-table tbody td{padding:.875rem 1.25rem;font-size:.875rem;color:#374151;vertical-align:middle}
    .rt-badge{display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:700;padding:3px 10px;border-radius:999px}
    .rt-empty{text-align:center;padding:3rem 1rem;color:#94a3b8}
    .rt-empty .material-icons-round{font-size:40px;display:block;margin-bottom:.5rem;opacity:.35}
    .rt-readonly-note{display:inline-flex;align-items:center;gap:5px;font-size:.75rem;color:#64748b;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:4px 10px}
</style>

<div>
    <div class="rt-header">
        <div>
            <h1><span class="material-icons-round">bar_chart</span> Revenue Tracking</h1>
            <p>Finance-focused revenue view — all figures are read-only &amp; display only</p>
        </div>
        <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap">
            <span class="rt-readonly-note"><span class="material-icons-round" style="font-size:14px">lock</span> Read-only — no editing</span>
            <a href="{{ route('admin.revenue-tracking.export', request()->query()) }}"
               style="height:38px;padding:0 1.1rem;border-radius:8px;font-size:.8125rem;font-weight:600;border:1.5px solid #e2e8f0;background:#fff;color:#475569;display:inline-flex;align-items:center;gap:6px;text-decoration:none">
                <span class="material-icons-round" style="font-size:16px">download</span> Export CSV
            </a>
        </div>
    </div>

    {{-- Date filter --}}
    <form method="GET" action="{{ route('admin.revenue-tracking') }}">
        @include('admin.partials.date-filter', ['current' => $dateFilter])
    </form>

    {{-- Stat cards --}}
    <div class="rt-stats">
        <div class="rt-stat">
            <div class="rt-stat-ico" style="background:#dcfce7;color:#16a34a"><span class="material-icons-round">attach_money</span></div>
            <div>
                <div class="rt-stat-lbl">Buyer Commissions</div>
                <div class="rt-stat-val">${{ number_format($buyerFees, 2) }}</div>
                <div class="rt-stat-sub">6% on winning bids</div>
            </div>
        </div>
        <div class="rt-stat">
            <div class="rt-stat-ico" style="background:#dbeafe;color:#2563eb"><span class="material-icons-round">storefront</span></div>
            <div>
                <div class="rt-stat-lbl">Seller Commissions</div>
                <div class="rt-stat-val">${{ number_format($sellerFees, 2) }}</div>
                <div class="rt-stat-sub">4% of sale price</div>
            </div>
        </div>
        <div class="rt-stat">
            <div class="rt-stat-ico" style="background:#fef9c3;color:#ca8a04"><span class="material-icons-round">receipt_long</span></div>
            <div>
                <div class="rt-stat-lbl">Listing / Membership Fees</div>
                <div class="rt-stat-val">${{ number_format($listingFees, 2) }}</div>
                <div class="rt-stat-sub">Individual seller payments</div>
            </div>
        </div>
        <div class="rt-stat">
            <div class="rt-stat-ico" style="background:#e8eef6;color:#063466"><span class="material-icons-round">account_balance_wallet</span></div>
            <div>
                <div class="rt-stat-lbl">Total Payouts to Sellers</div>
                <div class="rt-stat-val">${{ number_format($totalPayouts, 2) }}</div>
                <div class="rt-stat-sub">${{ number_format($outstandingPayouts, 2) }} pending</div>
            </div>
        </div>
    </div>

    {{-- Revenue breakdown table --}}
    <div class="rt-card">
        <div class="rt-card-hdr">
            <h2><span class="material-icons-round">table_chart</span> Revenue Breakdown</h2>
        </div>
        <div style="overflow-x:auto">
            <table class="rt-table">
                <thead><tr>
                    <th>Revenue Type</th>
                    <th>Description</th>
                    <th style="text-align:right">Total (Period)</th>
                </tr></thead>
                <tbody>
                    <tr><td class="font-semibold">Buyer Commissions</td><td style="color:#64748b">6% fee charged to buyers on winning bids (min $100)</td><td style="text-align:right;font-weight:700;color:#16a34a">${{ number_format($buyerFees,2) }}</td></tr>
                    <tr><td class="font-semibold">Seller Commissions</td><td style="color:#64748b">4% fee deducted from seller payouts (min $150)</td><td style="text-align:right;font-weight:700;color:#2563eb">${{ number_format($sellerFees,2) }}</td></tr>
                    <tr><td class="font-semibold">Listing / Membership Fees</td><td style="color:#64748b">Flat fees from subscription payments</td><td style="text-align:right;font-weight:700;color:#ca8a04">${{ number_format($listingFees,2) }}</td></tr>
                    <tr style="background:#f8fafc">
                        <td colspan="2" class="font-semibold">Total Platform Revenue</td>
                        <td style="text-align:right;font-size:1.1rem;font-weight:800;color:#063466">${{ number_format($buyerFees+$sellerFees+$listingFees,2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Payout tracking --}}
    <div class="rt-card">
        <div class="rt-card-hdr">
            <h2><span class="material-icons-round">send_money</span> Payout Tracking</h2>
        </div>
        <div style="padding:1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem">
            @php
                $payoutStats = [
                    ['Successful Payouts', \App\Models\Payout::whereIn('status',['sent','paid_successfully'])->count(), '#16a34a'],
                    ['Pending Payouts', \App\Models\Payout::where('status','pending')->count(), '#ca8a04'],
                    ['Processing', \App\Models\Payout::where('status','processing')->count(), '#2563eb'],
                    ['On Hold', \App\Models\Payout::where('status','on_hold')->count(), '#dc2626'],
                ];
            @endphp
            @foreach($payoutStats as [$label,$count,$color])
            <div style="background:#f8fafc;border-radius:10px;padding:1rem 1.25rem;border:1px solid #e2e8f0">
                <div style="font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em">{{ $label }}</div>
                <div style="font-size:1.75rem;font-weight:700;color:{{ $color }};line-height:1.1;margin-top:4px">{{ number_format($count) }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Failed payouts log --}}
    @if($failedPayouts->count() > 0)
    <div class="rt-card">
        <div class="rt-card-hdr">
            <h2><span class="material-icons-round">error</span> Failed Payouts Log</h2>
            <span style="font-size:.75rem;font-weight:600;background:#fee2e2;color:#dc2626;padding:2px 10px;border-radius:999px">{{ $failedPayouts->count() }}</span>
        </div>
        <div style="overflow-x:auto">
            <table class="rt-table">
                <thead><tr>
                    <th>Payout #</th><th>Seller</th><th>Amount</th><th>Notes</th><th>Date</th><th>Status</th>
                </tr></thead>
                <tbody>
                    @foreach($failedPayouts as $fp)
                    <tr>
                        <td style="font-family:monospace;font-size:.8rem">{{ $fp->payout_number ?? 'PAY-'.$fp->id }}</td>
                        <td>{{ $fp->seller?->name ?? '—' }}</td>
                        <td style="font-weight:700">${{ number_format($fp->net_payout,2) }}</td>
                        <td style="color:#64748b;font-size:.8125rem">{{ $fp->finance_notes ?? $fp->notes ?? '—' }}</td>
                        <td>{{ $fp->payout_generated_at?->format('M d, Y') ?? '—' }}</td>
                        <td><span class="rt-badge" style="background:#fee2e2;color:#dc2626">Failed</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="rt-card"><div style="padding:2rem 1.5rem;display:flex;align-items:center;gap:.75rem;color:#16a34a">
        <span class="material-icons-round">check_circle</span>
        <span style="font-weight:600">No failed payouts in the selected period.</span>
    </div></div>
    @endif
</div>
@endsection
