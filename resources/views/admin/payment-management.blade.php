@extends('layouts.admin')

@section('title', 'Sales / Payouts - Admin')

@section('content')
@php
    $baseQuery = array_filter([
        'tab' => $tab ?? 'all',
        'sort' => $sort ?? 'newest',
        'q' => request('q'),
    ], fn ($v) => $v !== null && $v !== '');
@endphp

<div class="max-w-7xl mx-auto pb-10">
    {{-- Page header --}}
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Sales / Payouts</h1>
        <p class="text-slate-600 text-sm mt-1">Manage completed auctions and process seller payouts.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        {{-- Toolbar: tabs + search + export + sort --}}
        <div class="p-4 sm:p-5 border-b border-slate-200 space-y-4">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                <nav class="flex flex-wrap gap-2" aria-label="Filter by status">
                    @foreach ([
                        'all' => 'All',
                        'awaiting_payment' => 'Awaiting Payment',
                        'payment_received' => 'Payment Received',
                        'closed' => 'Closed',
                    ] as $key => $label)
                        @php
                            $active = ($tab ?? 'all') === $key;
                            $href = route('admin.payments', array_merge($baseQuery, ['tab' => $key]));
                            $inactiveTint = match ($key) {
                                'awaiting_payment' => 'bg-amber-50 text-amber-900 border-amber-200 hover:bg-amber-100',
                                'payment_received' => 'bg-sky-50 text-sky-900 border-sky-200 hover:bg-sky-100',
                                'closed' => 'bg-emerald-50 text-emerald-900 border-emerald-200 hover:bg-emerald-100',
                                default => 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50',
                            };
                        @endphp
                        <a href="{{ $href }}"
                           class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold transition border
                               {{ $active ? 'bg-white text-blue-700 border-blue-600 ring-2 ring-blue-500/25 shadow-sm' : $inactiveTint }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>

                <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <form method="get" action="{{ route('admin.payments') }}" class="flex flex-1 sm:max-w-md gap-2">
                        <input type="hidden" name="tab" value="{{ $tab ?? 'all' }}">
                        <input type="hidden" name="sort" value="{{ $sort ?? 'newest' }}">
                        <div class="relative flex-1">
                            <span class="material-icons-round absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg pointer-events-none">search</span>
                            <input type="search" name="q" value="{{ request('q') }}"
                                   placeholder="Search by Item ID or Seller"
                                   class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition">Search</button>
                    </form>

                    <div class="flex items-center gap-2 shrink-0">
                        <form method="get" action="{{ route('admin.payments') }}" class="flex items-center gap-2">
                            <input type="hidden" name="tab" value="{{ $tab ?? 'all' }}">
                            @if(request('q'))
                                <input type="hidden" name="q" value="{{ request('q') }}">
                            @endif
                            <label for="sales-sort" class="text-xs font-medium text-slate-500 whitespace-nowrap">Sort</label>
                            <select id="sales-sort" name="sort" onchange="this.form.submit()"
                                    class="rounded-xl border border-slate-200 text-sm py-2 pl-3 pr-8 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="newest" @selected(($sort ?? 'newest') === 'newest')>Newest first</option>
                                <option value="oldest" @selected(($sort ?? '') === 'oldest')>Oldest first</option>
                                <option value="sale_high" @selected(($sort ?? '') === 'sale_high')>Sale price: high to low</option>
                                <option value="sale_low" @selected(($sort ?? '') === 'sale_low')>Sale price: low to high</option>
                            </select>
                        </form>

                        <a href="{{ route('admin.payments', array_merge($baseQuery, ['export' => 'csv'])) }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 hover:bg-slate-50 transition whitespace-nowrap">
                            <span class="material-icons-round text-lg">download</span>
                            Export
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-slate-50/80 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3">Item ID</th>
                        <th class="px-5 py-3">Seller</th>
                        <th class="px-5 py-3">Sale Price</th>
                        <th class="px-5 py-3">Payout</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($invoices as $invoice)
                        @php
                            $listing = $invoice->listing;
                            $itemRef = $invoice->item_id
                                ?? ($listing?->item_number ?? ('CM' . str_pad((string) ($listing?->id ?? $invoice->listing_id), 6, '0', STR_PAD_LEFT)));
                            $pipe = $invoice->adminSalesPayoutPipelineStatus();
                            $payout = $invoice->payout;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900 whitespace-nowrap">#{{ $itemRef }}</td>
                            <td class="px-5 py-4 text-sm text-slate-800">{{ $invoice->seller?->name ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm font-semibold text-slate-900 whitespace-nowrap">${{ number_format((float) $invoice->winning_bid_amount, 2) }}</td>
                            <td class="px-5 py-4 text-sm text-slate-800 whitespace-nowrap">
                                @if($payout)
                                    ${{ number_format((float) $payout->net_payout, 2) }}
                                @elseif($invoice->payment_status === 'paid')
                                    @php $est = $commissionService->calculateSellerPayout((float) $invoice->winning_bid_amount); @endphp
                                    <span class="text-slate-500" title="Estimated until payout is recorded">~${{ number_format((float) $est['net_payout'], 2) }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $pipe['badge_class'] }}">
                                    {{ $pipe['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                @if($pipe['key'] === 'ready_for_payout' && $payout && in_array($payout->status, ['pending', 'processing', 'on_hold'], true))
                                    @php
                                        $pm = $invoice->seller?->payoutMethod;
                                        $bankPayload = htmlspecialchars(json_encode([
                                            'bank' => $pm?->bank_name ?? '',
                                            'holder' => $pm?->account_holder_name ?? '',
                                            'account' => $pm?->account_number ?? '',
                                            'routing' => $pm?->routing_number ?? '',
                                            'swift' => $pm?->swift_number ?? '',
                                            'extra' => $pm?->additional_instructions ?? '',
                                        ]), ENT_QUOTES, 'UTF-8');
                                    @endphp
                                    <button type="button"
                                            data-open-pay-seller
                                            data-action-url="{{ route('admin.payouts.update-status', $payout) }}"
                                            data-payout-number="{{ e($payout->payout_number) }}"
                                            data-amount="{{ number_format((float) $payout->net_payout, 2, '.', '') }}"
                                            data-seller="{{ e($invoice->seller?->name ?? '') }}"
                                            data-bank-payload="{{ $bankPayload }}"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-95 active:scale-[0.99]"
                                            style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                                        <span class="material-icons-round text-base">payments</span>
                                        Pay Seller
                                    </button>
                                @else
                                    <span class="text-slate-400 text-sm" title="{{ $pipe['key'] === 'awaiting_payment' ? 'No admin action until the buyer pays.' : '' }}">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-slate-500 text-sm">
                                <span class="material-icons-round text-4xl text-slate-300 block mb-2">receipt_long</span>
                                No sales match this filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-sm text-slate-600">
                <p>
                    Showing <span class="font-semibold text-slate-900">{{ $invoices->firstItem() }}</span>
                    to <span class="font-semibold text-slate-900">{{ $invoices->lastItem() }}</span>
                    of <span class="font-semibold text-slate-900">{{ $invoices->total() }}</span> entries
                </p>
                <div>{{ $invoices->withQueryString()->links() }}</div>
            </div>
        @elseif($invoices->total() > 0)
            <div class="px-5 py-3 border-t border-slate-200 text-sm text-slate-600">
                Showing <span class="font-semibold text-slate-900">{{ $invoices->total() }}</span>
                {{ $invoices->total() === 1 ? 'entry' : 'entries' }}
            </div>
        @endif
    </div>

    {{-- Status flow (reference for admins) --}}
    <div class="mt-6 rounded-2xl border border-sky-200 bg-gradient-to-br from-sky-50 to-indigo-50/80 p-5 sm:p-6 space-y-5">
        <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wide">Status flow</h2>

        <div class="space-y-4 text-sm text-slate-700 leading-relaxed">
            <div>
                <p class="font-bold text-slate-900">1. Awaiting Payment</p>
                <p class="text-xs text-slate-600 mt-1">When:</p>
                <ul class="list-disc list-inside text-xs text-slate-600 mt-1 space-y-0.5">
                    <li>Auction has ended</li>
                    <li>Reserve price was met (or no reserve)</li>
                    <li>Buyer has <strong>not</strong> paid yet</li>
                </ul>
                <p class="text-xs mt-2 text-slate-700">Status shows: <strong>Awaiting Payment</strong>. No admin action yet.</p>
            </div>
            <div>
                <p class="font-bold text-slate-900">2. Payment Received</p>
                <p class="text-xs text-slate-600 mt-1">When the buyer successfully completes payment through the platform, the system updates the record, records payment internally, and unlocks the Messaging Center for buyer and seller.</p>
                <p class="text-xs mt-2 text-slate-700">Status shows: <strong>Payment Received</strong>. No admin payout action yet.</p>
            </div>
            <div>
                <p class="font-bold text-slate-900">3. Ready for Payout</p>
                <p class="text-xs text-slate-600 mt-1">When the buyer gives the seller the pick-up code, the seller enters it on the Seller Dashboard, and the code validates, the system marks pick-up completed, locks messaging, and closes buyer/seller chat for that sale.</p>
                <p class="text-xs mt-2 text-slate-700">Status shows: <strong>Ready for Payout</strong> — CayMark pays the seller. The <strong>Pay Seller</strong> button appears only in this state.</p>
            </div>
            <div>
                <p class="font-bold text-slate-900">4. Closed — date</p>
                <p class="text-xs text-slate-600 mt-1">After finance confirms the wire in the modal, the payout is marked completed, timestamp and completing officer are stored, and the action button is removed.</p>
                <p class="text-xs mt-2 text-slate-700">Status shows: <strong>Closed — [date]</strong>. Seller has been paid; CayMark&apos;s obligation for this sale is complete.</p>
            </div>
        </div>

        <div class="pt-4 mt-4 border-t border-sky-200/80">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wide mb-2">System triggers (table status)</h3>
            <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
                <li><strong>Trigger 1 — Buyer payment:</strong> Buyer pays through the platform → row status <strong>Payment Received</strong>.</li>
                <li><strong>Trigger 2 — Pick-up completion:</strong> Seller enters a valid pick-up code → status <strong>Ready for Payout</strong> (admin payout trigger; Messaging Center becomes <strong>read-only</strong> for that thread).</li>
                <li><strong>Trigger 3 — Admin sends payment:</strong> Finance confirms the wire in the modal → status <strong>Closed — [date]</strong>.</li>
            </ul>
        </div>

        <div class="pt-4 mt-4 border-t border-sky-200/80">
            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wide mb-2">End-to-end flow</h3>
            <ol class="text-xs text-slate-600 space-y-1 list-decimal list-inside">
                <li>Auction ends</li>
                <li>Buyer pays</li>
                <li>Status → Payment Received</li>
                <li>Buyer gives pick-up code to seller</li>
                <li>Seller enters pick-up code</li>
                <li>Status → Ready for Payout</li>
                <li>Admin pays seller (off-platform wire)</li>
                <li>Admin confirms payment sent</li>
                <li>Status → Closed + date</li>
                <li>Transaction complete</li>
            </ol>
        </div>
    </div>

    {{-- Confirm payout modal --}}
    <div id="pay-seller-modal" class="fixed inset-0 z-50 hidden" aria-hidden="true" role="dialog" aria-labelledby="pay-seller-modal-title">
        <div class="absolute inset-0 bg-slate-900/50 transition-opacity" data-close-pay-seller></div>
        <div class="relative flex min-h-full items-center justify-center p-4 pointer-events-none">
            <div class="pointer-events-auto w-full max-w-lg rounded-2xl bg-white shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-start justify-between gap-3">
                    <div>
                        <h3 id="pay-seller-modal-title" class="text-lg font-bold text-slate-900">Confirm seller payout</h3>
                        <p class="text-xs text-slate-500 mt-1">Record the wire after you have sent funds to the seller&apos;s bank.</p>
                    </div>
                    <button type="button" class="p-1 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" data-close-pay-seller aria-label="Close">
                        <span class="material-icons-round text-xl">close</span>
                    </button>
                </div>
                <form id="pay-seller-modal-form" method="POST" class="px-5 py-4 space-y-4">
                    @csrf
                    <input type="hidden" name="status" value="paid_successfully">

                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 space-y-2 text-sm">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Payout amount</p>
                        <p class="text-2xl font-bold text-slate-900" id="pay-modal-amount">$0.00</p>
                        <p class="text-xs text-slate-500"><span class="font-medium text-slate-600">Payout #</span> <span id="pay-modal-payout-number"></span></p>
                        <p class="text-xs text-slate-500"><span class="font-medium text-slate-600">Seller</span> <span id="pay-modal-seller"></span></p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">Banking information</p>
                        <div id="pay-modal-banking" class="rounded-xl border border-slate-200 bg-white p-4 text-sm text-slate-800 space-y-1.5 font-mono text-xs"></div>
                    </div>

                    <div>
                        <label for="pay-modal-transaction-ref" class="block text-xs font-semibold text-slate-700 mb-1">Transaction / wire reference <span class="text-red-600">*</span></label>
                        <input type="text" name="transaction_reference" id="pay-modal-transaction-ref" required maxlength="255"
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Bank confirmation / reference number">
                    </div>
                    <div>
                        <label for="pay-modal-date-sent" class="block text-xs font-semibold text-slate-700 mb-1">Date sent</label>
                        <input type="date" name="date_sent" id="pay-modal-date-sent" required
                               class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="pay-modal-notes" class="block text-xs font-semibold text-slate-700 mb-1">Finance notes (optional)</label>
                        <textarea name="finance_notes" id="pay-modal-notes" rows="2" maxlength="1000"
                                  class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Internal notes"></textarea>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50" data-close-pay-seller>Cancel</button>
                        <button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm hover:opacity-95" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);">
                            Confirm payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var modal = document.getElementById('pay-seller-modal');
    var form = document.getElementById('pay-seller-modal-form');
    if (!modal || !form) return;

    function todayYmd() {
        var d = new Date();
        var m = String(d.getMonth() + 1).padStart(2, '0');
        var day = String(d.getDate()).padStart(2, '0');
        return d.getFullYear() + '-' + m + '-' + day;
    }

    function esc(s) {
        if (s == null || s === '') return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function openModal(btn) {
        var url = btn.getAttribute('data-action-url');
        if (!url) return;
        form.action = url;
        var amt = btn.getAttribute('data-amount') || '0';
        document.getElementById('pay-modal-amount').textContent = '$' + amt;
        document.getElementById('pay-modal-payout-number').textContent = btn.getAttribute('data-payout-number') || '—';
        document.getElementById('pay-modal-seller').textContent = btn.getAttribute('data-seller') || '—';

        var bankingEl = document.getElementById('pay-modal-banking');
        var payload = {};
        try {
            payload = JSON.parse(btn.getAttribute('data-bank-payload') || '{}');
        } catch (e) {
            payload = {};
        }
        var lines = [];
        if (payload.bank) lines.push('<p><span class="text-slate-500 font-sans">Bank</span><br>' + esc(payload.bank) + '</p>');
        if (payload.holder) lines.push('<p><span class="text-slate-500 font-sans">Account holder</span><br>' + esc(payload.holder) + '</p>');
        if (payload.account) lines.push('<p><span class="text-slate-500 font-sans">Account</span><br>' + esc(payload.account) + '</p>');
        if (payload.routing) lines.push('<p><span class="text-slate-500 font-sans">Routing / transfer</span><br>' + esc(payload.routing) + '</p>');
        if (payload.swift) lines.push('<p><span class="text-slate-500 font-sans">SWIFT</span><br>' + esc(payload.swift) + '</p>');
        if (payload.extra) lines.push('<p><span class="text-slate-500 font-sans">Instructions</span><br>' + esc(payload.extra) + '</p>');
        if (!lines.length) {
            lines.push('<p class="text-amber-800 font-sans">No payout method on file for this seller. Confirm only after you have verified banking details elsewhere.</p>');
        }
        bankingEl.innerHTML = lines.join('');

        form.querySelector('#pay-modal-transaction-ref').value = '';
        form.querySelector('#pay-modal-notes').value = '';
        var ds = form.querySelector('#pay-modal-date-sent');
        if (ds) ds.value = todayYmd();

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-open-pay-seller]').forEach(function (btn) {
        btn.addEventListener('click', function () { openModal(btn); });
    });
    modal.querySelectorAll('[data-close-pay-seller]').forEach(function (el) {
        el.addEventListener('click', function () { closeModal(); });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });
})();
</script>
@endsection
