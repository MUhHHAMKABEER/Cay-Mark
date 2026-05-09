@extends('layouts.dashboard')

@section('title', 'Flagged Messaging Threads - Admin')

@section('content')
<style>
    .mflag-shell { padding: 1.5rem; max-width: 1280px; margin: 0 auto; }
    .mflag-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
    .mflag-header h1 { font-size: 1.4rem; font-weight: 700; color: #0f172a; }
    .mflag-header .count { background: #fef3c7; color: #92400e; font-weight: 700; padding: 4px 10px; border-radius: 999px; font-size: 0.8rem; }

    .mflag-table { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; }
    .mflag-table table { width: 100%; border-collapse: collapse; }
    .mflag-table th { background: #f8fafc; text-align: left; padding: 12px 16px; font-size: 0.7rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.06em; border-bottom: 1px solid #e2e8f0; }
    .mflag-table td { padding: 14px 16px; font-size: 0.85rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
    .mflag-table tr:last-child td { border-bottom: none; }
    .mflag-reason { display: inline-block; font-size: 0.7rem; font-weight: 700; padding: 3px 10px; border-radius: 999px; background: #fee2e2; color: #991b1b; text-transform: uppercase; letter-spacing: 0.04em; }
    .mflag-reason.manual { background:#dbeafe; color:#1e40af; }
    .mflag-reason.timeout { background:#fef3c7; color:#92400e; }

    .mflag-empty { padding: 3rem 1rem; text-align: center; color: #94a3b8; }
    .mflag-empty .material-icons-round { font-size: 3rem; color: #cbd5e1; }
</style>

<div class="mflag-shell">
    <div class="mflag-header">
        <div>
            <h1>Flagged Messaging Threads</h1>
            <p style="color:#64748b; font-size:0.9rem; margin-top:4px;">Threads flagged after the 3-exchange / 48-hour limit, or manually escalated by buyer/seller.</p>
        </div>
        <span class="count">{{ $threads->total() }} flagged</span>
    </div>

    @if (session('success'))
        <div style="background:#ecfdf5; border:1px solid #10b981; color:#065f46; padding:0.875rem 1rem; border-radius:12px; margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    <div class="mflag-table">
        @if ($threads->isEmpty())
            <div class="mflag-empty">
                <span class="material-icons-round">verified</span>
                <h3 style="font-size:1.05rem; font-weight:700; color:#475569; margin-top:0.5rem;">All clear</h3>
                <p style="font-size:0.875rem;">No threads currently need admin attention.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#Thread</th>
                        <th>Listing</th>
                        <th>Buyer</th>
                        <th>Seller</th>
                        <th>Exchanges</th>
                        <th>First / Last</th>
                        <th>Reason</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($threads as $t)
                        @php
                            $reason = $t->flag_reason;
                            $reasonClass = $reason === \App\Models\PostAuctionThread::FLAG_MANUAL ? 'manual'
                                : ($reason === \App\Models\PostAuctionThread::FLAG_TIMEOUT_48H ? 'timeout' : '');
                            $title = trim(($t->listing->year ?? '') . ' ' . ($t->listing->make ?? '') . ' ' . ($t->listing->model ?? ''));
                        @endphp
                        <tr>
                            <td><strong>#{{ $t->id }}</strong></td>
                            <td>
                                {{ $title ?: 'Listing #' . $t->listing_id }}
                                @if ($t->invoice)<div style="font-size:0.75rem; color:#94a3b8;">Invoice #{{ $t->invoice->invoice_number ?? $t->invoice_id }}</div>@endif
                            </td>
                            <td>{{ $t->buyer?->name ?? '—' }}<div style="font-size:0.7rem; color:#94a3b8;">{{ $t->buyer?->email }}</div></td>
                            <td>{{ $t->seller?->name ?? '—' }}<div style="font-size:0.7rem; color:#94a3b8;">{{ $t->seller?->email }}</div></td>
                            <td><strong>{{ $t->exchanges_count }}</strong> of {{ \App\Models\PostAuctionThread::MAX_EXCHANGES }}</td>
                            <td>
                                <div style="font-size:0.75rem;">First: {{ optional($t->first_exchange_at)?->format('M d g:i A') ?? '—' }}</div>
                                <div style="font-size:0.75rem;">Last: {{ optional($t->last_exchange_at)?->format('M d g:i A') ?? '—' }}</div>
                            </td>
                            <td><span class="mflag-reason {{ $reasonClass }}">{{ str_replace('_', ' ', $reason ?? '—') }}</span></td>
                            <td style="text-align:right;">
                                <a href="{{ route('admin.messaging.flags.show', $t->id) }}" style="display:inline-block; background:#2563eb; color:#fff; padding:6px 12px; border-radius:8px; text-decoration:none; font-weight:600; font-size:0.75rem;">Open</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div style="margin-top: 1rem;">{{ $threads->links() }}</div>
</div>
@endsection
