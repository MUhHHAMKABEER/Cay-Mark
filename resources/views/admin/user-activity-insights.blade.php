@extends('layouts.admin')
@section('title', 'User Activity Insights — Admin')
@section('content')
<style>
    :root{--navy:#063466;--navy-light:#e8eef6;}
    .ua-header{background:#fff;border-radius:12px;padding:1.5rem 1.75rem;margin-bottom:1.5rem;border-left:4px solid var(--navy);box-shadow:0 1px 4px rgba(6,52,102,.07)}
    .ua-header h1{font-size:1.35rem;font-weight:700;color:var(--navy);margin:0 0 .2rem;display:flex;align-items:center;gap:8px}
    .ua-header h1 .material-icons-round{font-size:1.3rem}
    .ua-header p{margin:0;color:#64748b;font-size:.875rem}
    .ua-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1rem;margin-bottom:1.5rem}
    .ua-stat{background:#fff;border-radius:12px;padding:1.25rem 1.5rem;box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;align-items:center;gap:1rem}
    .ua-stat-ico{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .ua-stat-ico .material-icons-round{font-size:22px}
    .ua-stat-lbl{font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em}
    .ua-stat-val{font-size:1.75rem;font-weight:700;color:#0f172a;line-height:1.1;margin-top:2px}
    .ua-stat-sub{font-size:.72rem;color:#94a3b8;margin-top:2px}
    .ua-card{background:#fff;border-radius:12px;box-shadow:0 1px 4px rgba(6,52,102,.07);overflow:hidden;margin-bottom:1.25rem}
    .ua-card-hdr{padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
    .ua-card-hdr h2{font-size:.9375rem;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:6px}
    .ua-card-hdr h2 .material-icons-round{font-size:18px;color:var(--navy)}
    .ua-table{width:100%;border-collapse:collapse}
    .ua-table thead th{padding:.75rem 1.25rem;text-align:left;font-size:.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;background:#f8fafc;border-bottom:1px solid #f1f5f9;white-space:nowrap}
    .ua-table tbody tr{border-bottom:1px solid #f8fafc;transition:background .1s}
    .ua-table tbody tr:last-child{border-bottom:none}
    .ua-table tbody tr:hover{background:#fafbfc}
    .ua-table tbody td{padding:.875rem 1.25rem;font-size:.875rem;color:#374151;vertical-align:middle}
    .ua-avatar{width:34px;height:34px;border-radius:50%;background:var(--navy-light);color:var(--navy);font-weight:700;font-size:.875rem;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .ua-badge{display:inline-block;font-size:.68rem;font-weight:700;padding:2px 8px;border-radius:999px;text-transform:uppercase;letter-spacing:.04em}
</style>

<div>
    <div class="ua-header">
        <h1><span class="material-icons-round">people_alt</span> User Activity Insights</h1>
        <p>Active users, bidding behaviour, and platform engagement metrics</p>
    </div>

    {{-- Date filter --}}
    <form method="GET" action="{{ route('admin.user-activity-insights') }}">
        @include('admin.partials.date-filter', ['current' => $dateFilter])
    </form>

    {{-- Stat cards --}}
    <div class="ua-stats">
        <div class="ua-stat">
            <div class="ua-stat-ico" style="background:#dbeafe;color:#2563eb"><span class="material-icons-round">shopping_cart</span></div>
            <div>
                <div class="ua-stat-lbl">Active Buyers (Period)</div>
                <div class="ua-stat-val">{{ number_format($activeBuyers) }}</div>
                <div class="ua-stat-sub">Placed bids or have invoices</div>
            </div>
        </div>
        <div class="ua-stat">
            <div class="ua-stat-ico" style="background:#dcfce7;color:#16a34a"><span class="material-icons-round">storefront</span></div>
            <div>
                <div class="ua-stat-lbl">Active Sellers (Period)</div>
                <div class="ua-stat-val">{{ number_format($activeSellers) }}</div>
                <div class="ua-stat-sub">Created listings</div>
            </div>
        </div>
        <div class="ua-stat">
            <div class="ua-stat-ico" style="background:#fef9c3;color:#ca8a04"><span class="material-icons-round">repeat</span></div>
            <div>
                <div class="ua-stat-lbl">Repeat Buyer Rate</div>
                <div class="ua-stat-val">{{ number_format($repeatBuyerRate, 1) }}%</div>
                <div class="ua-stat-sub">Won more than 1 auction</div>
            </div>
        </div>
        <div class="ua-stat">
            <div class="ua-stat-ico" style="background:#e8eef6;color:#063466"><span class="material-icons-round">group</span></div>
            <div>
                <div class="ua-stat-lbl">Total Buyers</div>
                <div class="ua-stat-val">{{ number_format(\App\Models\User::where('role','buyer')->count()) }}</div>
                <div class="ua-stat-sub">Registered buyer accounts</div>
            </div>
        </div>
    </div>

    {{-- Most active bidders --}}
    <div class="ua-card">
        <div class="ua-card-hdr">
            <h2><span class="material-icons-round">leaderboard</span> Top 10 Most Active Bidders</h2>
            <span style="font-size:.75rem;color:#64748b">Ranked by bids placed in period</span>
        </div>
        <div style="overflow-x:auto">
            <table class="ua-table">
                <thead><tr>
                    <th>#</th>
                    <th>Buyer</th>
                    <th>Bids Placed</th>
                    <th>Total Bid Value</th>
                    <th>Auctions Won</th>
                    <th>Win Rate</th>
                </tr></thead>
                <tbody>
                    @forelse($mostActiveBidders as $i => $buyer)
                    @php
                        $wonCount   = $buyer->getWonAuctions()->count();
                        $bidCount   = $buyer->bids_count ?? 0;
                        $winRate    = $bidCount > 0 ? round($wonCount / $bidCount * 100, 1) : 0;
                        $totalValue = $buyer->bids()->sum('amount');
                    @endphp
                    <tr>
                        <td style="font-weight:700;color:#063466">{{ $i + 1 }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div class="ua-avatar">{{ strtoupper(substr($buyer->name,0,1)) }}</div>
                                <div>
                                    <div style="font-weight:600;color:#0f172a">{{ $buyer->name }}</div>
                                    <div style="font-size:.72rem;color:#94a3b8">{{ $buyer->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-weight:700">{{ number_format($bidCount) }}</td>
                        <td style="font-weight:700;color:#16a34a">${{ number_format($totalValue,2) }}</td>
                        <td>{{ number_format($wonCount) }}</td>
                        <td>
                            <span class="ua-badge" style="{{ $winRate >= 50 ? 'background:#dcfce7;color:#15803d' : ($winRate >= 20 ? 'background:#fef9c3;color:#ca8a04' : 'background:#f1f5f9;color:#64748b') }}">
                                {{ $winRate }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:2rem;color:#94a3b8">No bidding activity in the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
