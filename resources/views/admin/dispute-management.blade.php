@extends('layouts.admin')
@section('title', 'Dispute Management — Admin')
@section('content')
<style>
    :root{--navy:#063466;--navy-light:#e8eef6;}
    .dm-header{background:#fff;border-radius:12px;padding:1.5rem 1.75rem;margin-bottom:1.5rem;border-left:4px solid var(--navy);box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem}
    .dm-header h1{font-size:1.35rem;font-weight:700;color:var(--navy);margin:0 0 .2rem;display:flex;align-items:center;gap:8px}
    .dm-header h1 .material-icons-round{font-size:1.3rem}
    .dm-header p{margin:0;color:#64748b;font-size:.875rem}
    .dm-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem}
    .dm-stat{background:#fff;border-radius:12px;padding:1.25rem 1.5rem;box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;align-items:center;gap:1rem}
    .dm-stat-ico{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .dm-stat-ico .material-icons-round{font-size:22px}
    .dm-stat-lbl{font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.05em}
    .dm-stat-val{font-size:1.75rem;font-weight:700;color:#0f172a;line-height:1.1;margin-top:2px}
    .dm-filter-bar{background:#fff;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;box-shadow:0 1px 4px rgba(6,52,102,.07);display:flex;flex-wrap:wrap;gap:.75rem;align-items:center}
    .dm-filter-bar input,.dm-filter-bar select{height:38px;padding:0 .85rem;border:1px solid #d1d5db;border-radius:8px;font-size:.875rem;color:#374151;background:#f9fafb;outline:none}
    .dm-filter-bar input:focus,.dm-filter-bar select:focus{border-color:var(--navy);box-shadow:0 0 0 3px rgba(6,52,102,.1);background:#fff}
    .dm-filter-bar input{min-width:220px;flex:1}
    .dm-btn{height:38px;padding:0 1.1rem;background:var(--navy);color:#fff;border:none;border-radius:8px;font-size:.875rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px}
    .dm-btn:hover{background:#074585}
    .dm-btn-light{background:#f1f5f9;color:#475569;border:1.5px solid #e2e8f0;text-decoration:none}
    .dm-btn-light:hover{background:#e2e8f0}
    .dm-card{background:#fff;border-radius:12px;box-shadow:0 1px 4px rgba(6,52,102,.07);overflow:hidden}
    .dm-card-hdr{padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
    .dm-card-hdr h2{font-size:.9375rem;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:6px}
    .dm-card-hdr h2 .material-icons-round{font-size:18px;color:var(--navy)}
    .dm-count{font-size:.75rem;font-weight:600;color:var(--navy);background:var(--navy-light);padding:2px 10px;border-radius:999px}
    .dm-table{width:100%;border-collapse:collapse}
    .dm-table thead th{padding:.75rem 1.25rem;text-align:left;font-size:.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;background:#f8fafc;border-bottom:1px solid #f1f5f9;white-space:nowrap}
    .dm-table tbody tr{border-bottom:1px solid #f8fafc;transition:background .1s}
    .dm-table tbody tr:last-child{border-bottom:none}
    .dm-table tbody tr:hover{background:#fafbfc}
    .dm-table tbody td{padding:.875rem 1.25rem;font-size:.875rem;color:#374151;vertical-align:middle}
    .dm-badge{display:inline-flex;align-items:center;gap:4px;font-size:.72rem;font-weight:700;padding:3px 10px;border-radius:999px}
    .dm-badge--open{background:#fee2e2;color:#dc2626}
    .dm-badge--resolved{background:#dcfce7;color:#15803d}
    .dm-badge--flagged{background:#fef9c3;color:#a16207}
    .dm-avatar{width:30px;height:30px;border-radius:50%;background:var(--navy-light);color:var(--navy);font-weight:700;font-size:.8rem;display:flex;align-items:center;justify-content:center;flex-shrink:0}
    .dm-empty{text-align:center;padding:3.5rem 1rem;color:#94a3b8}
    .dm-empty .material-icons-round{font-size:48px;display:block;margin-bottom:.75rem;opacity:.4}
    .dm-empty p{margin:0;font-size:.9375rem}
    .dm-pagination{padding:1rem 1.25rem;border-top:1px solid #f1f5f9}
    .dm-flag-reason{font-size:.75rem;color:#a16207;background:#fef9c3;border-radius:5px;padding:2px 7px;font-weight:600;max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
</style>

<div>
    <div class="dm-header">
        <div>
            <h1><span class="material-icons-round">gavel</span> Dispute Management</h1>
            <p>Flagged post-auction threads requiring admin review</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="dm-stats">
        <div class="dm-stat">
            <div class="dm-stat-ico" style="background:#e8eef6;color:#063466"><span class="material-icons-round">flag</span></div>
            <div><div class="dm-stat-lbl">Total Flagged</div><div class="dm-stat-val">{{ $disputeStats['total'] }}</div></div>
        </div>
        <div class="dm-stat">
            <div class="dm-stat-ico" style="background:#fee2e2;color:#dc2626"><span class="material-icons-round">error</span></div>
            <div><div class="dm-stat-lbl">Open</div><div class="dm-stat-val">{{ $disputeStats['open'] }}</div></div>
        </div>
        <div class="dm-stat">
            <div class="dm-stat-ico" style="background:#fef9c3;color:#a16207"><span class="material-icons-round">pending</span></div>
            <div><div class="dm-stat-lbl">In Progress</div><div class="dm-stat-val">{{ $disputeStats['in_progress'] }}</div></div>
        </div>
        <div class="dm-stat">
            <div class="dm-stat-ico" style="background:#dcfce7;color:#16a34a"><span class="material-icons-round">check_circle</span></div>
            <div><div class="dm-stat-lbl">Resolved</div><div class="dm-stat-val">{{ $disputeStats['resolved'] }}</div></div>
        </div>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.disputes') }}" class="dm-filter-bar">
        <input type="search" name="search" placeholder="Search buyer, seller, or item…" value="{{ request('search') }}">
        <select name="status" onchange="this.form.submit()">
            <option value="">All Flagged</option>
            <option value="open"     @selected(request('status')==='open')>Open / Unresolved</option>
            <option value="resolved" @selected(request('status')==='resolved')>Resolved</option>
        </select>
        <button type="submit" class="dm-btn"><span class="material-icons-round" style="font-size:16px">search</span> Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.disputes') }}" class="dm-btn dm-btn-light" style="display:inline-flex;align-items:center">
                <span class="material-icons-round" style="font-size:15px">close</span> Clear
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="dm-card">
        <div class="dm-card-hdr">
            <h2><span class="material-icons-round">flag</span> Flagged Transactions</h2>
            <span class="dm-count">{{ $disputes->total() }} total</span>
        </div>
        <div style="overflow-x:auto">
            <table class="dm-table">
                <thead><tr>
                    <th>ID</th>
                    <th>Item</th>
                    <th>Buyer</th>
                    <th>Seller</th>
                    <th>Reason</th>
                    <th>Exchanges</th>
                    <th>Flagged</th>
                    <th>Status</th>
                    <th style="text-align:right">Action</th>
                </tr></thead>
                <tbody>
                    @forelse($disputes as $d)
                    @php
                        $vehicle = $d->listing
                            ? trim(($d->listing->year ?? '').' '.($d->listing->make ?? '').' '.($d->listing->model ?? ''))
                            : 'Listing #'.($d->listing_id ?? '—');
                        $itemId  = $d->listing?->item_number ?? ('CM'.str_pad($d->listing_id ?? 0,6,'0',STR_PAD_LEFT));
                        $isResolved = $d->pickup_confirmed;
                    @endphp
                    <tr>
                        <td style="font-family:monospace;font-size:.8rem;color:var(--navy)">{{ $itemId }}</td>
                        <td>
                            <div style="font-weight:600;color:#0f172a;font-size:.875rem">{{ $vehicle }}</div>
                            @if($d->listing?->images?->first())
                                <div style="font-size:.72rem;color:#94a3b8;margin-top:2px">Has photos</div>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:7px">
                                <div class="dm-avatar">{{ strtoupper(substr($d->buyer?->name ?? 'B',0,1)) }}</div>
                                <div>
                                    <div style="font-weight:600;font-size:.8125rem">{{ $d->buyer?->name ?? '—' }}</div>
                                    <div style="font-size:.7rem;color:#94a3b8">{{ $d->buyer?->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:7px">
                                <div class="dm-avatar">{{ strtoupper(substr($d->seller?->name ?? 'S',0,1)) }}</div>
                                <div>
                                    <div style="font-weight:600;font-size:.8125rem">{{ $d->seller?->name ?? '—' }}</div>
                                    <div style="font-size:.7rem;color:#94a3b8">{{ $d->seller?->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($d->flag_reason)
                                <span class="dm-flag-reason" title="{{ $d->flag_reason }}">{{ $d->flag_reason }}</span>
                            @else
                                <span style="color:#94a3b8;font-size:.8rem">—</span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            <span style="font-weight:700;color:{{ $d->exchanges_count >= 3 ? '#dc2626' : '#374151' }}">
                                {{ $d->exchanges_count }}/{{ \App\Models\PostAuctionThread::MAX_EXCHANGES }}
                            </span>
                        </td>
                        <td>
                            {{ $d->flagged_at ? $d->flagged_at->format('M d, Y') : '—' }}
                            @if($d->flagged_at)
                                <div style="font-size:.72rem;color:#94a3b8">{{ $d->flagged_at->diffForHumans() }}</div>
                            @endif
                        </td>
                        <td>
                            @if($isResolved)
                                <span class="dm-badge dm-badge--resolved">Resolved</span>
                            @else
                                <span class="dm-badge dm-badge--open">Open</span>
                            @endif
                        </td>
                        <td style="text-align:right">
                            <a href="{{ route('messaging.thread.show', $d->invoice_id ?? 0) }}"
                               style="display:inline-flex;align-items:center;gap:5px;padding:.4rem .9rem;border-radius:7px;background:var(--navy);color:#fff;font-size:.8rem;font-weight:600;text-decoration:none">
                                <span class="material-icons-round" style="font-size:14px">forum</span>
                                View Thread
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9">
                        <div class="dm-empty">
                            <span class="material-icons-round">gavel</span>
                            <p>No flagged transactions found.</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($disputes->hasPages())
            <div class="dm-pagination">{{ $disputes->withQueryString()->links() }}</div>
        @endif
    </div>

    <div style="margin-top:1rem;padding:.75rem 1rem;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;font-size:.8125rem;color:#1e40af">
        <span class="material-icons-round" style="font-size:16px;vertical-align:-3px">info</span>
        Disputes are flagged automatically when messaging exchange limits are exceeded or flagged manually by admin.
        Click <strong>View Thread</strong> to review the full conversation, evidence, and timeline before taking action.
    </div>
</div>
@endsection
