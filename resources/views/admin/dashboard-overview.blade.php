@extends('layouts.admin')
@section('title', 'Dashboard — Admin')

@section('content')
<style>
    :root { --navy:#063466; --navy-light:#e8eef6; --navy-mid:#0d4d8c; }

    /* ── Page header ── */
    .adm-header {
        background:#fff; border-radius:12px; padding:1.5rem 1.75rem; margin-bottom:1.5rem;
        border-left:4px solid var(--navy); box-shadow:0 1px 4px rgba(6,52,102,.07);
        display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem;
    }
    .adm-header h1 { font-size:1.35rem; font-weight:700; color:var(--navy); margin:0 0 .2rem; display:flex; align-items:center; gap:8px; }
    .adm-header h1 .material-icons-round { font-size:1.3rem; }
    .adm-header p  { margin:0; color:#64748b; font-size:.875rem; }

    /* ── Stat cards ── */
    .adm-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .adm-stat {
        background:#fff; border-radius:12px; padding:1.25rem 1.25rem 1.1rem;
        box-shadow:0 1px 4px rgba(6,52,102,.07); display:flex; align-items:center; gap:1rem;
        text-decoration:none; transition:box-shadow .15s,transform .15s;
    }
    .adm-stat:hover { box-shadow:0 4px 12px rgba(6,52,102,.12); transform:translateY(-1px); text-decoration:none; }
    .adm-stat-ico { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .adm-stat-ico .material-icons-round { font-size:22px; }
    .ico-navy   { background:var(--navy-light); color:var(--navy); }
    .ico-blue   { background:#dbeafe; color:#2563eb; }
    .ico-green  { background:#dcfce7; color:#16a34a; }
    .ico-amber  { background:#fef3c7; color:#d97706; }
    .ico-red    { background:#fee2e2; color:#dc2626; }
    .ico-purple { background:#ede9fe; color:#7c3aed; }
    .adm-stat-lbl { font-size:.72rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.05em; }
    .adm-stat-val { font-size:1.75rem; font-weight:700; color:#0f172a; line-height:1.1; margin-top:2px; }
    .adm-stat-sub { font-size:.72rem; color:#94a3b8; margin-top:2px; }

    /* ── Section cards ── */
    .adm-card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(6,52,102,.07); overflow:hidden; margin-bottom:1.25rem; }
    .adm-card-hdr {
        padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between; gap:.75rem;
    }
    .adm-card-hdr h2 { font-size:.9375rem; font-weight:700; color:#0f172a; margin:0; display:flex; align-items:center; gap:6px; }
    .adm-card-hdr h2 .material-icons-round { font-size:18px; color:var(--navy); }
    .adm-card-hdr a { font-size:.8rem; font-weight:600; color:var(--navy); text-decoration:none; }
    .adm-card-hdr a:hover { text-decoration:underline; }
    .adm-card-body { padding:1.25rem 1.5rem; }

    /* ── Alert strip ── */
    .adm-alert { display:flex; align-items:flex-start; gap:10px; padding:.75rem 1rem; border-radius:8px; font-size:.875rem; margin-bottom:.625rem; border-left:3px solid transparent; }
    .adm-alert:last-child { margin-bottom:0; }
    .adm-alert.danger  { background:#fef2f2; border-color:#dc2626; color:#991b1b; }
    .adm-alert.warning { background:#fffbeb; border-color:#d97706; color:#92400e; }
    .adm-alert.info    { background:#eff6ff; border-color:#2563eb; color:#1e40af; }
    .adm-alert .material-icons-round { font-size:18px; flex-shrink:0; margin-top:1px; }

    /* ── Activity list ── */
    .adm-activity { display:flex; align-items:flex-start; gap:12px; padding:.75rem 0; border-bottom:1px solid #f8fafc; }
    .adm-activity:last-child { border-bottom:none; padding-bottom:0; }
    .adm-activity-ico { width:36px; height:36px; border-radius:10px; background:var(--navy-light); color:var(--navy); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .adm-activity-ico .material-icons-round { font-size:17px; }
    .adm-activity-msg { font-size:.875rem; color:#1e293b; }
    .adm-activity-time { font-size:.72rem; color:#94a3b8; margin-top:2px; }

    /* ── User signup row ── */
    .adm-user-row { display:flex; align-items:center; justify-content:space-between; padding:.625rem 0; border-bottom:1px solid #f8fafc; }
    .adm-user-row:last-child { border-bottom:none; }
    .adm-avatar { width:38px; height:38px; border-radius:50%; background:var(--navy-light); color:var(--navy); font-weight:700; font-size:.95rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .adm-role-badge { font-size:.68rem; font-weight:700; padding:2px 8px; border-radius:999px; text-transform:uppercase; letter-spacing:.04em; }
    .role-buyer  { background:#dbeafe; color:#1d4ed8; }
    .role-seller { background:#dcfce7; color:#15803d; }
    .role-admin  { background:#ede9fe; color:#6d28d9; }
    .role-guest  { background:#f1f5f9; color:#64748b; }

    /* ── Quick-action chips ── */
    .adm-actions { display:flex; flex-wrap:wrap; gap:.625rem; }
    .adm-action-chip {
        display:inline-flex; align-items:center; gap:6px; padding:.5rem 1rem;
        background:var(--navy-light); color:var(--navy); border-radius:8px;
        font-size:.8125rem; font-weight:600; text-decoration:none;
        transition:background .15s,color .15s;
    }
    .adm-action-chip:hover { background:var(--navy); color:#fff; text-decoration:none; }
    .adm-action-chip .material-icons-round { font-size:16px; }

    /* ── Empty state ── */
    .adm-empty { text-align:center; padding:2.5rem 1rem; color:#94a3b8; }
    .adm-empty .material-icons-round { font-size:40px; display:block; margin-bottom:.5rem; opacity:.35; }

    /* ── Responsive ── */
    @media (max-width:768px) {
        .adm-two-col { grid-template-columns:1fr !important; }
    }
</style>

<div>

    {{-- ── Header ── --}}
    <div class="adm-header">
        <div>
            <h1>
                <span class="material-icons-round">dashboard</span>
                Admin Dashboard
            </h1>
            <p>Welcome back, <strong>{{ auth()->user()->name }}</strong> — here's what's happening today.</p>
        </div>
        <div style="display:flex;gap:.625rem;flex-wrap:wrap">
            <a href="{{ route('admin.listing-review') }}" class="adm-action-chip">
                <span class="material-icons-round">rule</span> Review Listings
            </a>
            <a href="{{ route('admin.payments') }}" class="adm-action-chip">
                <span class="material-icons-round">payments</span> Sales / Payouts
            </a>
        </div>
    </div>

    {{-- ── Stats ── --}}
    <div class="adm-stats">
        <a href="{{ route('admin.auctions') }}" class="adm-stat">
            <div class="adm-stat-ico ico-navy">
                <span class="material-icons-round">gavel</span>
            </div>
            <div>
                <div class="adm-stat-lbl">Active Auctions</div>
                <div class="adm-stat-val">{{ $stats['active_auctions'] ?? 0 }}</div>
            </div>
        </a>

        <a href="{{ route('admin.listing-review') }}" class="adm-stat">
            <div class="adm-stat-ico ico-amber">
                <span class="material-icons-round">pending_actions</span>
            </div>
            <div>
                <div class="adm-stat-lbl">Pending Approval</div>
                <div class="adm-stat-val">{{ $stats['listings_awaiting_approval'] ?? 0 }}</div>
            </div>
        </a>

        <a href="{{ route('admin.users') }}" class="adm-stat">
            <div class="adm-stat-ico ico-blue">
                <span class="material-icons-round">group</span>
            </div>
            <div>
                <div class="adm-stat-lbl">Total Users</div>
                <div class="adm-stat-val">{{ $stats['total_users'] ?? 0 }}</div>
                <div class="adm-stat-sub">{{ $stats['total_buyers'] ?? 0 }} buyers · {{ $stats['total_sellers'] ?? 0 }} sellers</div>
            </div>
        </a>

        <a href="{{ route('admin.pending-payments') }}" class="adm-stat">
            <div class="adm-stat-ico ico-red">
                <span class="material-icons-round">credit_card_off</span>
            </div>
            <div>
                <div class="adm-stat-lbl">Pending Payments</div>
                <div class="adm-stat-val">{{ $stats['payments_pending'] ?? 0 }}</div>
                <div class="adm-stat-sub">Buyer auction debts</div>
            </div>
        </a>

        <a href="{{ route('admin.payouts') }}" class="adm-stat">
            <div class="adm-stat-ico ico-green">
                <span class="material-icons-round">account_balance_wallet</span>
            </div>
            <div>
                <div class="adm-stat-lbl">Payouts Pending</div>
                <div class="adm-stat-val">{{ $stats['payouts_pending'] ?? 0 }}</div>
            </div>
        </a>

        <div class="adm-stat" style="cursor:default">
            <div class="adm-stat-ico ico-purple">
                <span class="material-icons-round">report_problem</span>
            </div>
            <div>
                <div class="adm-stat-lbl">Open Disputes</div>
                <div class="adm-stat-val">{{ $stats['open_disputes'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- ── Alerts ── --}}
    @if(isset($alerts) && count($alerts) > 0)
    <div class="adm-card">
        <div class="adm-card-hdr">
            <h2><span class="material-icons-round">notifications_active</span> System Alerts</h2>
            <span style="font-size:.72rem;font-weight:600;background:#fee2e2;color:#dc2626;padding:2px 10px;border-radius:999px;">{{ count($alerts) }}</span>
        </div>
        <div class="adm-card-body" style="padding-top:.875rem;padding-bottom:.875rem">
            @foreach($alerts as $alert)
                <div class="adm-alert {{ $alert['type'] ?? 'info' }}">
                    <span class="material-icons-round">
                        {{ $alert['type'] === 'danger' ? 'error' : ($alert['type'] === 'warning' ? 'warning' : 'info') }}
                    </span>
                    <span class="flex-1">{{ $alert['message'] }}</span>
                    @if(isset($alert['link']))
                        <a href="{{ $alert['link'] }}" style="font-size:.8rem;font-weight:600;color:inherit;text-decoration:underline;white-space:nowrap">View →</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Two-column lower grid ── --}}
    <div class="adm-two-col" style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">

        {{-- Recent Activity --}}
        <div class="adm-card">
            <div class="adm-card-hdr">
                <h2><span class="material-icons-round">timeline</span> Recent Activity</h2>
            </div>
            <div class="adm-card-body">
                @if(isset($recentActivities) && count($recentActivities) > 0)
                    @foreach($recentActivities as $activity)
                        <div class="adm-activity">
                            <div class="adm-activity-ico">
                                <span class="material-icons-round">{{ $activity['icon'] ?? 'circle' }}</span>
                            </div>
                            <div>
                                <div class="adm-activity-msg">{{ $activity['message'] }}</div>
                                <div class="adm-activity-time">
                                    @if(isset($activity['time']))
                                        {{ $activity['time']->diffForHumans() }}
                                    @else
                                        {{ $activity['timestamp'] ?? 'Recently' }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="adm-empty">
                        <span class="material-icons-round">inbox</span>
                        <p style="margin:0;font-size:.875rem">No recent activity</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Signups --}}
        <div class="adm-card">
            <div class="adm-card-hdr">
                <h2><span class="material-icons-round">person_add</span> Recent Signups</h2>
                <a href="{{ route('admin.users') }}">View all →</a>
            </div>
            <div class="adm-card-body">
                @if(isset($recentSignups) && $recentSignups->count() > 0)
                    @foreach($recentSignups->take(8) as $user)
                        <div class="adm-user-row">
                            <div style="display:flex;align-items:center;gap:10px;min-width:0">
                                <div class="adm-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div style="min-width:0">
                                    <div style="font-size:.875rem;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px">{{ $user->name }}</div>
                                    <div style="font-size:.72rem;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px">{{ $user->email }}</div>
                                </div>
                            </div>
                            <div style="text-align:right;flex-shrink:0">
                                <span class="adm-role-badge role-{{ $user->role ?? 'guest' }}">{{ ucfirst($user->role ?? 'guest') }}</span>
                                <div style="font-size:.72rem;color:#94a3b8;margin-top:3px">{{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="adm-empty">
                        <span class="material-icons-round">group</span>
                        <p style="margin:0;font-size:.875rem">No recent signups</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── Quick Actions ── --}}
    <div class="adm-card" style="margin-top:1.25rem">
        <div class="adm-card-hdr">
            <h2><span class="material-icons-round">bolt</span> Quick Actions</h2>
        </div>
        <div class="adm-card-body">
            <div class="adm-actions">
                <a href="{{ route('admin.listing-review') }}" class="adm-action-chip">
                    <span class="material-icons-round">rule</span> Review Listings
                </a>
                <a href="{{ route('admin.users') }}" class="adm-action-chip">
                    <span class="material-icons-round">manage_accounts</span> Manage Users
                </a>
                <a href="{{ route('admin.payments') }}" class="adm-action-chip">
                    <span class="material-icons-round">payments</span> Sales / Payouts
                </a>
                <a href="{{ route('admin.auctions') }}" class="adm-action-chip">
                    <span class="material-icons-round">gavel</span> Auction Management
                </a>
                <a href="{{ route('admin.security-deposits') }}" class="adm-action-chip">
                    <span class="material-icons-round">security</span> Security Deposits
                </a>
                <a href="{{ route('admin.support-tickets') }}" class="adm-action-chip">
                    <span class="material-icons-round">support_agent</span> Support Tickets
                </a>
                <a href="{{ route('admin.pending-payments') }}" class="adm-action-chip">
                    <span class="material-icons-round">credit_card_off</span> Pending Payments
                </a>
                <a href="{{ route('admin.buyer-defaults') }}" class="adm-action-chip">
                    <span class="material-icons-round">block</span> Buyer Defaults
                </a>
            </div>
        </div>
    </div>

</div>

@endsection
