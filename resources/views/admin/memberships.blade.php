@extends('layouts.admin')

@section('title', 'Membership Management - Admin')

@section('content')
<style>
    /* ── Brand variables ─────────────────────────────────────── */
    :root {
        --navy:      #063466;
        --navy-hover:#074585;
        --navy-light:#e8eef6;
        --navy-mid:  #0d4d8c;
    }

    /* ── Page header ─────────────────────────────────────────── */
    .mem-page-header {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--navy);
        box-shadow: 0 1px 4px rgba(6,52,102,0.07);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .mem-page-header h1 {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--navy);
        margin: 0 0 0.2rem;
    }
    .mem-page-header p { margin: 0; color: #64748b; font-size: 0.875rem; }

    /* ── Stat cards ──────────────────────────────────────────── */
    .mem-stats {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1200px) { .mem-stats { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 640px)  { .mem-stats { grid-template-columns: repeat(2, 1fr); } }

    .mem-stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.25rem 1.25rem 1.1rem;
        box-shadow: 0 1px 4px rgba(6,52,102,0.07);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .mem-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .mem-stat-icon .material-icons-round { font-size: 22px; }
    .mem-stat-icon--navy   { background: var(--navy-light);          color: var(--navy); }
    .mem-stat-icon--green  { background: #dcfce7;                    color: #16a34a; }
    .mem-stat-icon--red    { background: #fee2e2;                    color: #dc2626; }
    .mem-stat-icon--yellow { background: #fef9c3;                    color: #ca8a04; }
    .mem-stat-icon--orange { background: #ffedd5;                    color: #ea580c; }

    .mem-stat-label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
    .mem-stat-value { font-size: 1.75rem; font-weight: 700; color: #0f172a; line-height: 1.1; margin-top: 2px; }

    /* ── Filter bar ──────────────────────────────────────────── */
    .mem-filter-bar {
        background: #ffffff;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 4px rgba(6,52,102,0.07);
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .mem-filter-bar input,
    .mem-filter-bar select {
        height: 38px;
        padding: 0 0.85rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #374151;
        background: #f9fafb;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .mem-filter-bar input:focus,
    .mem-filter-bar select:focus {
        border-color: var(--navy);
        box-shadow: 0 0 0 3px rgba(6,52,102,0.1);
        background: #fff;
    }
    .mem-filter-bar input { min-width: 240px; flex: 1; }
    .mem-filter-bar select { min-width: 140px; }
    .mem-btn-filter {
        height: 38px;
        padding: 0 1.1rem;
        background: var(--navy);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s;
    }
    .mem-btn-filter:hover { background: var(--navy-hover); }
    .mem-btn-clear {
        height: 38px;
        padding: 0 1rem;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s;
    }
    .mem-btn-clear:hover { background: #e2e8f0; text-decoration: none; }

    /* ── Table card ──────────────────────────────────────────── */
    .mem-table-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(6,52,102,0.07);
        overflow: hidden;
    }
    .mem-table-card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }
    .mem-table-card-header h2 {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .mem-table-card-header .mem-count-badge {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--navy);
        background: var(--navy-light);
        padding: 2px 10px;
        border-radius: 999px;
    }

    /* Table */
    .mem-table { width: 100%; border-collapse: collapse; }
    .mem-table thead th {
        padding: 0.75rem 1.25rem;
        text-align: left;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        white-space: nowrap;
    }
    .mem-table tbody tr {
        border-bottom: 1px solid #f8fafc;
        transition: background 0.1s;
    }
    .mem-table tbody tr:last-child { border-bottom: none; }
    .mem-table tbody tr:hover { background: #fafbfc; }
    .mem-table tbody td {
        padding: 0.875rem 1.25rem;
        font-size: 0.875rem;
        color: #374151;
        vertical-align: middle;
    }

    /* User cell */
    .mem-user-cell { display: flex; align-items: center; gap: 0.7rem; }
    .mem-avatar {
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }
    .mem-user-name  { font-size: 0.875rem; font-weight: 600; color: #0f172a; line-height: 1.2; }
    .mem-user-email { font-size: 0.75rem; color: #94a3b8; margin-top: 1px; }

    /* Role badge */
    .mem-role-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 3px 9px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .mem-role-badge--buyer  { background: var(--navy-light); color: var(--navy); }
    .mem-role-badge--seller { background: #f0fdf4;           color: #15803d; border: 1px solid #bbf7d0; }

    /* Status badge */
    .mem-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .mem-status-badge::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        opacity: 0.8;
    }
    .mem-status-badge--active    { background: #dcfce7; color: #16a34a; }
    .mem-status-badge--expired   { background: #fee2e2; color: #dc2626; }
    .mem-status-badge--pending   { background: #fef9c3; color: #a16207; }
    .mem-status-badge--cancelled { background: #f1f5f9; color: #64748b; }

    /* Package cell */
    .mem-package-name  { font-weight: 600; color: #0f172a; }
    .mem-package-price { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }

    /* Date cell */
    .mem-date-main { color: #374151; }
    .mem-date-sub  { font-size: 0.72rem; margin-top: 2px; font-weight: 600; }
    .mem-date-sub--expired      { color: #dc2626; }
    .mem-date-sub--expiring     { color: #ea580c; }

    /* Empty state */
    .mem-empty {
        text-align: center;
        padding: 3.5rem 1rem;
        color: #94a3b8;
    }
    .mem-empty .material-icons-round { font-size: 48px; display: block; margin-bottom: 0.75rem; opacity: 0.4; }
    .mem-empty p { margin: 0; font-size: 0.9375rem; }

    /* Pagination wrapper */
    .mem-pagination { padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9; }
</style>

<div>
    {{-- ── Page Header ──────────────────────────────────────────── --}}
    <div class="mem-page-header">
        <div>
            <h1>
                <span class="material-icons-round" style="font-size:1.25rem;vertical-align:-3px;margin-right:6px;color:var(--navy)">card_membership</span>
                Membership Management
            </h1>
            <p>One row per member — showing each user's current active plan</p>
        </div>
    </div>

    {{-- ── Stat Cards ───────────────────────────────────────────── --}}
    <div class="mem-stats">
        <div class="mem-stat-card">
            <div class="mem-stat-icon mem-stat-icon--navy">
                <span class="material-icons-round">group</span>
            </div>
            <div>
                <div class="mem-stat-label">Total Members</div>
                <div class="mem-stat-value">{{ $membershipStats['total'] ?? 0 }}</div>
            </div>
        </div>
        <div class="mem-stat-card">
            <div class="mem-stat-icon mem-stat-icon--green">
                <span class="material-icons-round">check_circle</span>
            </div>
            <div>
                <div class="mem-stat-label">Active</div>
                <div class="mem-stat-value">{{ $membershipStats['active'] ?? 0 }}</div>
            </div>
        </div>
        <div class="mem-stat-card">
            <div class="mem-stat-icon mem-stat-icon--red">
                <span class="material-icons-round">cancel</span>
            </div>
            <div>
                <div class="mem-stat-label">Expired</div>
                <div class="mem-stat-value">{{ $membershipStats['expired'] ?? 0 }}</div>
            </div>
        </div>
        <div class="mem-stat-card">
            <div class="mem-stat-icon mem-stat-icon--yellow">
                <span class="material-icons-round">schedule</span>
            </div>
            <div>
                <div class="mem-stat-label">Pending</div>
                <div class="mem-stat-value">{{ $membershipStats['pending_renewal'] ?? 0 }}</div>
            </div>
        </div>
        <div class="mem-stat-card">
            <div class="mem-stat-icon mem-stat-icon--orange">
                <span class="material-icons-round">timer</span>
            </div>
            <div>
                <div class="mem-stat-label">Expiring Soon</div>
                <div class="mem-stat-value">{{ $membershipStats['expiring_soon'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- ── Filter Bar ────────────────────────────────────────────── --}}
    <form class="mem-filter-bar" method="GET" action="{{ route('admin.memberships') }}">
        <input type="text" name="search" placeholder="Search by name or email…"
               value="{{ request('search') }}">

        <select name="role">
            <option value="">All Roles</option>
            <option value="buyer"  {{ request('role') === 'buyer'  ? 'selected' : '' }}>Buyers</option>
            <option value="seller" {{ request('role') === 'seller' ? 'selected' : '' }}>Sellers</option>
        </select>

        <select name="status">
            <option value="">All Statuses</option>
            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
            <option value="expired"   {{ request('status') === 'expired'   ? 'selected' : '' }}>Expired</option>
            <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>

        <button type="submit" class="mem-btn-filter">
            <span class="material-icons-round" style="font-size:16px">search</span>
            Search
        </button>

        @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('admin.memberships') }}" class="mem-btn-clear">
            <span class="material-icons-round" style="font-size:15px">close</span>
            Clear
        </a>
        @endif
    </form>

    {{-- ── Memberships Table ────────────────────────────────────── --}}
    <div class="mem-table-card">
        <div class="mem-table-card-header">
            <h2>All Members</h2>
            <span class="mem-count-badge">{{ $memberships->total() }} {{ Str::plural('member', $memberships->total()) }}</span>
        </div>

        <div style="overflow-x:auto">
            <table class="mem-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Role</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Started</th>
                        <th>Expires</th>
                        <th>Joined Platform</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($memberships as $membership)
                    @php
                        $memberUser   = $membership->user;
                        $memberName   = $memberUser
                            ? (trim((string)($memberUser->name ?? '')) ?: ($memberUser->email ?? 'User #' . $membership->user_id))
                            : ('User #' . $membership->user_id);
                        $memberEmail  = $memberUser?->email ?? '—';
                        $memberRole   = $memberUser?->role ?? '';
                        $memberInitial = strtoupper(substr(trim((string)($memberUser->name ?? $memberUser->email ?? 'U')), 0, 1));

                        $pkg      = $membership->package;
                        $pkgName  = $pkg?->title ?? '—';
                        $pkgPrice = $pkg ? '$' . number_format($pkg->price, 2) : '—';

                        $status = strtolower($membership->status ?? 'unknown');

                        $startsAt  = $membership->starts_at;
                        $endsAt    = $membership->ends_at;
                        $joinedAt  = $memberUser?->created_at;

                        $isExpired  = $endsAt && $endsAt->isPast();
                        $isExpiring = $endsAt && !$isExpired && $endsAt->diffInDays(now()) <= 7;
                    @endphp
                    <tr>
                        {{-- Member --}}
                        <td>
                            <div class="mem-user-cell">
                                <div class="mem-avatar">{{ $memberInitial }}</div>
                                <div>
                                    <div class="mem-user-name">{{ Str::ucfirst($memberName) }}</div>
                                    <div class="mem-user-email">{{ $memberEmail }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Role --}}
                        <td>
                            @if($memberRole === 'buyer')
                                <span class="mem-role-badge mem-role-badge--buyer">
                                    <span class="material-icons-round" style="font-size:11px">shopping_cart</span>
                                    Buyer
                                </span>
                            @elseif($memberRole === 'seller')
                                <span class="mem-role-badge mem-role-badge--seller">
                                    <span class="material-icons-round" style="font-size:11px">storefront</span>
                                    Seller
                                </span>
                            @else
                                <span style="color:#94a3b8;font-size:0.8125rem">—</span>
                            @endif
                        </td>

                        {{-- Plan --}}
                        <td>
                            <div class="mem-package-name">{{ $pkgName }}</div>
                            <div class="mem-package-price">{{ $pkgPrice }}</div>
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="mem-status-badge mem-status-badge--{{ $status }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>

                        {{-- Started --}}
                        <td>
                            <span class="mem-date-main">
                                {{ $startsAt ? $startsAt->format('M j, Y') : '—' }}
                            </span>
                        </td>

                        {{-- Expires --}}
                        <td>
                            @if($endsAt)
                                <span class="mem-date-main">{{ $endsAt->format('M j, Y') }}</span>
                                @if($isExpired)
                                    <div class="mem-date-sub mem-date-sub--expired">Expired {{ $endsAt->diffForHumans() }}</div>
                                @elseif($isExpiring)
                                    <div class="mem-date-sub mem-date-sub--expiring">Expires in {{ $endsAt->diffForHumans(now(), true) }}</div>
                                @endif
                            @else
                                <span style="color:#94a3b8">No expiry</span>
                            @endif
                        </td>

                        {{-- Joined Platform --}}
                        <td>
                            <span class="mem-date-main">
                                {{ $joinedAt ? $joinedAt->format('M j, Y') : '—' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="mem-empty">
                                <span class="material-icons-round">card_membership</span>
                                <p>No memberships found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($memberships->hasPages())
        <div class="mem-pagination">
            {{ $memberships->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
