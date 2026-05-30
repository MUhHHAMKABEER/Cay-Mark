@extends('layouts.admin')
@section('title', 'Membership Payments — Admin')
@section('content')
<style>
    :root{--navy:#063466;--navy-light:#e8eef6;}
    .mp-header{background:#fff;border-radius:12px;padding:1.5rem 1.75rem;margin-bottom:1.5rem;border-left:4px solid var(--navy);box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem}
    .mp-header h1{font-size:1.35rem;font-weight:700;color:var(--navy);margin:0 0 .2rem;display:flex;align-items:center;gap:8px}
    .mp-header h1 .material-icons-round{font-size:1.3rem}
    .mp-header p{margin:0;color:#64748b;font-size:.875rem}
    .mp-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem}
    .mp-stat{background:#fff;border-radius:12px;padding:1.25rem 1.5rem;box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;align-items:center;gap:1rem}
    .mp-stat-ico{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .mp-stat-ico .material-icons-round{font-size:22px}
    .mp-stat-lbl{font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em}
    .mp-stat-val{font-size:1.75rem;font-weight:700;color:#0f172a;line-height:1.1;margin-top:2px}
    .mp-filter-bar{background:#fff;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;flex-wrap:wrap;gap:.75rem;align-items:center}
    .mp-filter-bar input,.mp-filter-bar select{height:38px;padding:0 .85rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;color:#374151;background:#f9fafb;outline:none}
    .mp-filter-bar input:focus,.mp-filter-bar select:focus{border-color:var(--navy);box-shadow:0 0 0 3px rgba(6,52,102,.1);background:#fff}
    .mp-filter-bar input{min-width:220px;flex:1}
    .mp-btn{height:38px;padding:0 1.1rem;background:var(--navy);color:#fff;border:none;border-radius:8px;font-size:.875rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px}
    .mp-btn:hover{background:#074585}
    .mp-btn-light{background:#f1f5f9;color:#475569;border:1.5px solid #e2e8f0;text-decoration:none}
    .mp-btn-light:hover{background:#e2e8f0;color:#374151}
    .mp-card{background:#fff;border-radius:12px;box-shadow:0 1px 4px rgba(6,52,102,.07);overflow:hidden}
    .mp-card-hdr{padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
    .mp-card-hdr h2{font-size:.9375rem;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:6px}
    .mp-card-hdr h2 .material-icons-round{font-size:18px;color:var(--navy)}
    .mp-count{font-size:.75rem;font-weight:600;color:var(--navy);background:var(--navy-light);padding:2px 10px;border-radius:999px}
    .mp-table{width:100%;border-collapse:collapse}
    .mp-table thead th{padding:.75rem 1.25rem;text-align:left;font-size:.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;background:#f8fafc;border-bottom:1px solid #f1f5f9;white-space:nowrap}
    .mp-table tbody tr{border-bottom:1px solid #f8fafc;transition:background .1s}
    .mp-table tbody tr:last-child{border-bottom:none}
    .mp-table tbody tr:hover{background:#fafbfc}
    .mp-table tbody td{padding:.875rem 1.25rem;font-size:.875rem;color:#374151;vertical-align:middle}
    .mp-avatar{width:34px;height:34px;border-radius:50%;background:var(--navy-light);color:var(--navy);font-weight:700;font-size:.875rem;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .mp-badge{display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:700;padding:3px 10px;border-radius:999px}
    .mp-badge--completed{background:#dcfce7;color:#15803d}
    .mp-badge--pending{background:#fef9c3;color:#a16207}
    .mp-badge--failed{background:#fee2e2;color:#dc2626}
    .mp-empty{text-align:center;padding:3.5rem 1rem;color:#94a3b8}
    .mp-empty .material-icons-round{font-size:48px;display:block;margin-bottom:.75rem;opacity:.4}
    .mp-pagination{padding:1rem 1.25rem;border-top:1px solid #f1f5f9}
</style>

<div>
    <div class="mp-header">
        <div>
            <h1><span class="material-icons-round">card_membership</span> Membership Payments</h1>
            <p>Buyer Membership · Business Seller · Individual Seller — all subscription payments</p>
        </div>
        <a href="{{ route('admin.membership-payments.export', request()->query()) }}"
           class="mp-btn mp-btn-light" style="display:inline-flex;align-items:center">
            <span class="material-icons-round" style="font-size:16px">download</span> Export CSV
        </a>
    </div>

    {{-- Date filter --}}
    <form method="GET" action="{{ route('admin.membership-payments') }}">
        @include('admin.partials.date-filter', ['current' => $filter])
    </form>

    {{-- Stats --}}
    <div class="mp-stats">
        <div class="mp-stat">
            <div class="mp-stat-ico" style="background:#e8eef6;color:#063466"><span class="material-icons-round">receipt_long</span></div>
            <div>
                <div class="mp-stat-lbl">Total in Period</div>
                <div class="mp-stat-val">{{ number_format($stats['total']) }}</div>
            </div>
        </div>
        <div class="mp-stat">
            <div class="mp-stat-ico" style="background:#dcfce7;color:#16a34a"><span class="material-icons-round">check_circle</span></div>
            <div>
                <div class="mp-stat-lbl">Completed</div>
                <div class="mp-stat-val">{{ number_format($stats['completed']) }}</div>
            </div>
        </div>
        <div class="mp-stat">
            <div class="mp-stat-ico" style="background:#fef9c3;color:#ca8a04"><span class="material-icons-round">schedule</span></div>
            <div>
                <div class="mp-stat-lbl">Pending</div>
                <div class="mp-stat-val">{{ number_format($stats['pending']) }}</div>
            </div>
        </div>
        <div class="mp-stat">
            <div class="mp-stat-ico" style="background:#dcfce7;color:#16a34a"><span class="material-icons-round">attach_money</span></div>
            <div>
                <div class="mp-stat-lbl">Revenue (Period)</div>
                <div class="mp-stat-val" style="font-size:1.4rem">${{ number_format($stats['revenue'],2) }}</div>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.membership-payments') }}" class="mp-filter-bar">
        <input type="hidden" name="date_filter" value="{{ $filter }}">
        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
        <input type="hidden" name="date_to" value="{{ request('date_to') }}">
        <input type="search" name="search" placeholder="Search by name or email…" value="{{ request('search') }}">
        <select name="status">
            <option value="">All Statuses</option>
            <option value="completed" @selected(request('status')==='completed')>Completed</option>
            <option value="pending"   @selected(request('status')==='pending')>Pending</option>
            <option value="failed"    @selected(request('status')==='failed')>Failed</option>
        </select>
        <button type="submit" class="mp-btn"><span class="material-icons-round" style="font-size:16px">search</span> Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.membership-payments',['date_filter'=>$filter]) }}" class="mp-btn mp-btn-light" style="display:inline-flex">
                <span class="material-icons-round" style="font-size:15px">close</span> Clear
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="mp-card">
        <div class="mp-card-hdr">
            <h2><span class="material-icons-round">card_membership</span> Membership Payments</h2>
            <span class="mp-count">{{ $payments->total() }} records</span>
        </div>
        <div style="overflow-x:auto">
            <table class="mp-table">
                <thead><tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Renewal Date</th>
                </tr></thead>
                <tbody>
                    @forelse($payments as $payment)
                    @php
                        $pkg = $payment->subscription?->package;
                        $statusClass = match($payment->status) {
                            'completed' => 'mp-badge--completed',
                            'pending'   => 'mp-badge--pending',
                            default     => 'mp-badge--failed',
                        };
                    @endphp
                    <tr>
                        <td>{{ $payment->created_at->format('M d, Y') }}<div style="font-size:.72rem;color:#94a3b8">{{ $payment->created_at->format('H:i') }}</div></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div class="mp-avatar">{{ strtoupper(substr($payment->user?->name ?? 'U',0,1)) }}</div>
                                <div>
                                    <div style="font-weight:600;color:#0f172a">{{ $payment->user?->name ?? '—' }}</div>
                                    <div style="font-size:.72rem;color:#94a3b8">{{ $payment->user?->email ?? '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#0f172a">{{ $pkg?->title ?? '—' }}</div>
                            @if($pkg?->price)
                            <div style="font-size:.72rem;color:#94a3b8">${{ number_format($pkg->price,2) }}</div>
                            @endif
                        </td>
                        <td style="font-weight:700">${{ number_format($payment->amount,2) }}</td>
                        <td><span class="mp-badge {{ $statusClass }}">{{ ucfirst($payment->status) }}</span></td>
                        <td>
                            @if($payment->subscription?->ends_at)
                                {{ $payment->subscription->ends_at->format('M d, Y') }}
                                @if($payment->subscription->ends_at->isPast())
                                    <div style="font-size:.72rem;color:#dc2626;font-weight:600">Expired</div>
                                @elseif($payment->subscription->ends_at->diffInDays(now()) <= 30)
                                    <div style="font-size:.72rem;color:#ea580c;font-weight:600">Expires soon</div>
                                @endif
                            @elseif($payment->subscription?->ends_at === null)
                                <span style="color:#94a3b8">No expiry</span>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6">
                        <div class="mp-empty">
                            <span class="material-icons-round">card_membership</span>
                            <p>No membership payments found for the selected filters.</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="mp-pagination">{{ $payments->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
