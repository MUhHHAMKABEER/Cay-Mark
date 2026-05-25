@extends('layouts.admin')

@section('title', 'User Management - Admin')

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
    .um-page-header {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid var(--navy);
        box-shadow: 0 1px 4px rgba(6,52,102,0.07);
    }
    .um-page-header h1 {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--navy);
        margin: 0 0 0.2rem;
    }
    .um-page-header p { margin: 0; color: #64748b; font-size: 0.875rem; }

    /* ── Stat cards ──────────────────────────────────────────── */
    .um-stats {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1200px) { .um-stats { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 640px)  { .um-stats { grid-template-columns: repeat(2, 1fr); } }

    .um-stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.25rem 1.25rem 1.1rem;
        box-shadow: 0 1px 4px rgba(6,52,102,0.07);
        display: flex;
        align-items: center;
        gap: 1rem;
        cursor: pointer;
        text-decoration: none;
        transition: box-shadow 0.15s, transform 0.15s;
    }
    .um-stat-card:hover { box-shadow: 0 4px 12px rgba(6,52,102,0.12); transform: translateY(-1px); text-decoration: none; }
    .um-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .um-stat-icon .material-icons-round { font-size: 22px; }
    .um-stat-icon--navy   { background: var(--navy-light); color: var(--navy); }
    .um-stat-icon--blue   { background: #dbeafe;           color: #2563eb; }
    .um-stat-icon--green  { background: #dcfce7;           color: #16a34a; }
    .um-stat-icon--red    { background: #fee2e2;           color: #dc2626; }
    .um-stat-icon--amber  { background: #fef3c7;           color: #d97706; }
    .um-stat-label { font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
    .um-stat-value { font-size: 1.75rem; font-weight: 700; color: #0f172a; line-height: 1.1; margin-top: 2px; }

    /* ── Filter bar ──────────────────────────────────────────── */
    .um-filter-bar {
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
    .um-filter-bar input,
    .um-filter-bar select {
        height: 38px;
        padding: 0 0.85rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #374151;
        background: #f9fafb;
        outline: none;
        font-family: inherit;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .um-filter-bar input:focus,
    .um-filter-bar select:focus {
        border-color: var(--navy);
        box-shadow: 0 0 0 3px rgba(6,52,102,0.1);
        background: #fff;
    }
    .um-filter-bar input { min-width: 240px; flex: 1; }
    .um-filter-bar select { min-width: 140px; }
    .um-btn-filter {
        height: 38px;
        padding: 0 1.1rem;
        background: var(--navy);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s;
    }
    .um-btn-filter:hover { background: var(--navy-hover); }
    .um-btn-clear {
        height: 38px;
        padding: 0 1rem;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        font-family: inherit;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.15s;
    }
    .um-btn-clear:hover { background: #e2e8f0; text-decoration: none; }

    /* ── Table card ──────────────────────────────────────────── */
    .um-table-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(6,52,102,0.07);
        overflow: hidden;
    }
    .um-table-card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }
    .um-table-card-header h2 {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .um-count-badge {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--navy);
        background: var(--navy-light);
        padding: 2px 10px;
        border-radius: 999px;
    }

    /* Table */
    .um-table { width: 100%; border-collapse: collapse; }
    .um-table thead th {
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
    .um-table tbody tr {
        border-bottom: 1px solid #f8fafc;
        transition: background 0.1s;
    }
    .um-table tbody tr:last-child { border-bottom: none; }
    .um-table tbody tr:hover { background: #fafbfc; }
    .um-table tbody td {
        padding: 0.875rem 1.25rem;
        font-size: 0.875rem;
        color: #374151;
        vertical-align: middle;
    }

    /* User cell */
    .um-user-cell { display: flex; align-items: center; gap: 0.75rem; }
    .um-avatar {
        flex-shrink: 0;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--navy) 0%, var(--navy-mid) 100%);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }
    .um-user-name  { font-size: 0.875rem; font-weight: 600; color: #0f172a; line-height: 1.2; }
    .um-user-email { font-size: 0.75rem; color: #94a3b8; margin-top: 1px; }

    /* Badges */
    .um-badge {
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
    .um-badge--buyer      { background: var(--navy-light); color: var(--navy); }
    .um-badge--seller     { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .um-badge--incomplete { background: #fef9c3; color: #92400e; border: 1px solid #fde68a; }

    .um-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .um-status-badge::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        opacity: 0.8;
    }
    .um-status-badge--active     { background: #dcfce7; color: #16a34a; }
    .um-status-badge--restricted { background: #fee2e2; color: #dc2626; }

    /* Phone cell */
    .um-phone       { color: #374151; }
    .um-phone-none  { color: #cbd5e1; font-style: italic; }
    .um-phone-verified   { display: inline-flex; align-items: center; gap: 3px; font-size: 0.7rem; color: #16a34a; margin-top: 2px; }
    .um-phone-unverified { display: inline-flex; align-items: center; gap: 3px; font-size: 0.7rem; color: #f59e0b; margin-top: 2px; }

    /* Plan cell */
    .um-plan-name { font-weight: 600; color: #0f172a; font-size: 0.8125rem; }
    .um-plan-none { color: #cbd5e1; font-style: italic; font-size: 0.8125rem; }

    /* Action btn */
    .um-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 12px;
        border-radius: 7px;
        font-size: 0.8rem;
        font-weight: 600;
        font-family: inherit;
        text-decoration: none;
        background: var(--navy-light);
        color: var(--navy);
        border: 1px solid rgba(6,52,102,0.15);
        transition: background 0.15s, color 0.15s;
        white-space: nowrap;
    }
    .um-action-btn:hover { background: var(--navy); color: #fff; text-decoration: none; }
    .um-action-btn .material-icons-round { font-size: 14px; }

    /* Empty state */
    .um-empty {
        text-align: center;
        padding: 3.5rem 1rem;
        color: #94a3b8;
    }
    .um-empty .material-icons-round { font-size: 48px; display: block; margin-bottom: 0.75rem; opacity: 0.4; }
    .um-empty p { margin: 0; font-size: 0.9375rem; }

    /* Pagination */
    .um-pagination { padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9; }
</style>

<div>
    {{-- ── Page Header ──────────────────────────────────────────── --}}
    <div class="um-page-header">
        <h1>
            <span class="material-icons-round" style="font-size:1.25rem;vertical-align:-3px;margin-right:6px;color:var(--navy)">manage_accounts</span>
            User Management
        </h1>
        <p>All registered platform users — buyers, sellers, and incomplete accounts</p>
    </div>

    {{-- ── Stat Cards ───────────────────────────────────────────── --}}
    <div class="um-stats">
        <a class="um-stat-card" href="{{ route('admin.users') }}">
            <div class="um-stat-icon um-stat-icon--navy">
                <span class="material-icons-round">group</span>
            </div>
            <div>
                <div class="um-stat-label">Total Users</div>
                <div class="um-stat-value">{{ $userStats['total'] ?? 0 }}</div>
            </div>
        </a>
        <a class="um-stat-card" href="{{ route('admin.users', ['role' => 'buyer']) }}">
            <div class="um-stat-icon um-stat-icon--blue">
                <span class="material-icons-round">shopping_cart</span>
            </div>
            <div>
                <div class="um-stat-label">Buyers</div>
                <div class="um-stat-value">{{ $userStats['buyers'] ?? 0 }}</div>
            </div>
        </a>
        <a class="um-stat-card" href="{{ route('admin.users', ['role' => 'seller']) }}">
            <div class="um-stat-icon um-stat-icon--green">
                <span class="material-icons-round">storefront</span>
            </div>
            <div>
                <div class="um-stat-label">Sellers</div>
                <div class="um-stat-value">{{ $userStats['sellers'] ?? 0 }}</div>
            </div>
        </a>
        <a class="um-stat-card" href="{{ route('admin.users', ['status' => 'restricted']) }}">
            <div class="um-stat-icon um-stat-icon--red">
                <span class="material-icons-round">block</span>
            </div>
            <div>
                <div class="um-stat-label">Restricted</div>
                <div class="um-stat-value">{{ $userStats['restricted'] ?? 0 }}</div>
            </div>
        </a>
        <a class="um-stat-card" href="{{ route('admin.users', ['role' => 'incomplete']) }}">
            <div class="um-stat-icon um-stat-icon--amber">
                <span class="material-icons-round">pending</span>
            </div>
            <div>
                <div class="um-stat-label">Incomplete</div>
                <div class="um-stat-value">{{ $userStats['incomplete'] ?? 0 }}</div>
            </div>
        </a>
    </div>

    {{-- ── Filter Bar ────────────────────────────────────────────── --}}
    <form class="um-filter-bar" method="GET" action="{{ route('admin.users') }}">
        <input type="text" name="search"
               placeholder="Search by name, email, or phone…"
               value="{{ request('search') }}">

        <select name="role">
            <option value="">All Roles</option>
            <option value="buyer"      {{ request('role') === 'buyer'      ? 'selected' : '' }}>Buyers</option>
            <option value="seller"     {{ request('role') === 'seller'     ? 'selected' : '' }}>Sellers</option>
            <option value="incomplete" {{ request('role') === 'incomplete' ? 'selected' : '' }}>Incomplete</option>
        </select>

        <select name="status">
            <option value="">All Statuses</option>
            <option value="active"     {{ request('status') === 'active'     ? 'selected' : '' }}>Active</option>
            <option value="restricted" {{ request('status') === 'restricted' ? 'selected' : '' }}>Restricted</option>
        </select>

        <button type="submit" class="um-btn-filter">
            <span class="material-icons-round" style="font-size:16px">search</span>
            Search
        </button>

        @if(request()->hasAny(['search','role','status']))
        <a href="{{ route('admin.users') }}" class="um-btn-clear">
            <span class="material-icons-round" style="font-size:15px">close</span>
            Clear
        </a>
        @endif
    </form>

    {{-- ── Users Table ──────────────────────────────────────────── --}}
    <div class="um-table-card">
        <div class="um-table-card-header">
            <h2>All Users</h2>
            <span class="um-count-badge">{{ $users->total() }} {{ Str::plural('user', $users->total()) }}</span>
        </div>

        <div style="overflow-x:auto">
            <table class="um-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Phone</th>
                        <th>Current Plan</th>
                        <th>Account Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $uName    = trim((string)($user->name ?? ''));
                        $uInitial = strtoupper(substr($uName ?: ($user->email ?? 'U'), 0, 1));
                        $uRole    = strtolower($user->role ?? '');

                        $phone         = $user->phone ?? null;
                        $phoneVerified = !is_null($user->phone_verified_at);

                        $plan    = $user->activeSubscription?->package?->title ?? null;
                        $planPrice = $user->activeSubscription?->package?->price ?? null;

                        $isRestricted = (bool) ($user->is_restricted ?? false);
                    @endphp
                    <tr>
                        {{-- User --}}
                        <td>
                            <div class="um-user-cell">
                                <div class="um-avatar">{{ $uInitial }}</div>
                                <div>
                                    <div class="um-user-name">{{ $uName ?: '—' }}</div>
                                    <div class="um-user-email">{{ $user->email ?? '—' }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Role --}}
                        <td>
                            @if($uRole === 'buyer')
                                <span class="um-badge um-badge--buyer">
                                    <span class="material-icons-round" style="font-size:11px">shopping_cart</span>
                                    Buyer
                                </span>
                            @elseif($uRole === 'seller')
                                <span class="um-badge um-badge--seller">
                                    <span class="material-icons-round" style="font-size:11px">storefront</span>
                                    Seller
                                </span>
                            @else
                                <span class="um-badge um-badge--incomplete">
                                    <span class="material-icons-round" style="font-size:11px">pending</span>
                                    Incomplete
                                </span>
                            @endif
                        </td>

                        {{-- Phone --}}
                        <td>
                            @if($phone)
                                <div class="um-phone">{{ $phone }}</div>
                                @if($phoneVerified)
                                    <div class="um-phone-verified">
                                        <span class="material-icons-round" style="font-size:11px">verified</span>
                                        Verified
                                    </div>
                                @else
                                    <div class="um-phone-unverified">
                                        <span class="material-icons-round" style="font-size:11px">warning</span>
                                        Unverified
                                    </div>
                                @endif
                            @else
                                <span class="um-phone-none">No phone</span>
                            @endif
                        </td>

                        {{-- Current Plan --}}
                        <td>
                            @if($plan)
                                <div class="um-plan-name">{{ $plan }}</div>
                                @if($planPrice)
                                    <div style="font-size:0.72rem;color:#94a3b8;margin-top:2px">${{ number_format($planPrice, 2) }}</div>
                                @endif
                            @else
                                <span class="um-plan-none">No active plan</span>
                            @endif
                        </td>

                        {{-- Account Status --}}
                        <td>
                            @if($isRestricted)
                                <span class="um-status-badge um-status-badge--restricted">Restricted</span>
                                @if($user->restriction_ends_at)
                                    <div style="font-size:0.7rem;color:#94a3b8;margin-top:3px">
                                        Until {{ $user->restriction_ends_at->format('M j, Y') }}
                                    </div>
                                @endif
                            @else
                                <span class="um-status-badge um-status-badge--active">Active</span>
                            @endif
                        </td>

                        {{-- Joined --}}
                        <td>
                            <div style="color:#374151">{{ $user->created_at->format('M j, Y') }}</div>
                            <div style="font-size:0.72rem;color:#94a3b8;margin-top:2px">{{ $user->created_at->diffForHumans() }}</div>
                        </td>

                        {{-- Actions --}}
                        <td>
                            <a href="{{ route('admin.users.view', $user->id) }}" class="um-action-btn">
                                <span class="material-icons-round">open_in_new</span>
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="um-empty">
                                <span class="material-icons-round">manage_accounts</span>
                                <p>No users found matching your filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="um-pagination">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
