@extends('layouts.Buyer')

@section('title', 'Security Deposit – CayMark')

@section('content')
@php
    $available   = $walletSummary['available_balance']  ?? 0;
    $locked      = $walletSummary['locked_balance']     ?? 0;
    $total       = $walletSummary['total_balance']      ?? 0;
    $threshold   = $walletSummary['deposit_threshold']  ?? 2000;
    $pct         = $walletSummary['deposit_percentage'] ?? 10;
    $buyingPower = $available * 10;

    // Pending wire requests (submitted but not yet confirmed by admin)
    $pendingWire = ($depositRequests ?? collect())->where('status', 'pending_wire');
@endphp

<div class="bg-gray-50 min-h-screen p-4 sm:p-6">
    <div class="max-w-4xl mx-auto">

        {{-- ── Wire-request submitted banner (flash) ─────────────────────── --}}
        @if(session('deposit_request_submitted'))
        @php $submittedAmt = session('deposit_request_submitted'); @endphp
        <div class="mb-5 bg-blue-50 border border-blue-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <span class="material-icons text-blue-600 text-xl">account_balance</span>
                </div>
                <div>
                    <p class="font-bold text-blue-900 text-base">Deposit request received!</p>
                    <p class="text-blue-800 text-sm mt-1">
                        Thank you — your deposit request of <strong>${{ $submittedAmt }}</strong> has been received.
                        Once your wire transfer clears, our team will manually confirm and your buying power will be updated.
                        This usually takes <strong>1–3 business days</strong>.
                    </p>
                    <p class="text-blue-700 text-xs mt-2">Use reference <strong>DEP-{{ Auth::id() }}</strong> on your wire transfer.</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Generic success / error --}}
        @if(session('success'))
            <div class="mb-5 flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3 shadow-sm">
                <span class="material-icons text-green-600 mt-0.5 text-xl">check_circle</span>
                <p class="text-green-800 text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3 shadow-sm">
                <span class="material-icons text-red-600 mt-0.5 text-xl">error</span>
                <p class="text-red-800 text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Page header --}}
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Security Deposit</h1>
            <p class="text-gray-500 text-sm mt-1">Your deposit unlocks buying power. Every $1 deposited gives you $10 in bidding power.</p>
        </div>

        {{-- ── Buying power hero ────────────────────────────────────────── --}}
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-6 mb-6 text-white shadow-lg">
            <p class="text-blue-200 text-xs font-semibold uppercase tracking-widest mb-1">Available Buying Power</p>
            <p class="text-4xl sm:text-5xl font-extrabold mb-4">${{ number_format($buyingPower, 0) }}</p>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-white/10 rounded-xl p-4">
                    <p class="text-blue-200 text-xs font-medium mb-1">Deposit Available</p>
                    <p class="text-xl font-bold">${{ number_format($available, 2) }}</p>
                    <p class="text-blue-300 text-xs mt-0.5">Free to bid with</p>
                </div>
                <div class="bg-white/10 rounded-xl p-4">
                    <p class="text-blue-200 text-xs font-medium mb-1">Deposit Used</p>
                    <p class="text-xl font-bold">${{ number_format($locked, 2) }}</p>
                    <p class="text-blue-300 text-xs mt-0.5">Committed to active bids</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button onclick="openDepositModal()"
                    class="flex items-center gap-2 bg-white text-blue-700 font-bold px-5 py-2.5 rounded-xl hover:bg-blue-50 transition shadow text-sm">
                    <span class="material-icons text-base">add_circle</span> Add Deposit
                </button>
                <button onclick="openWithdrawModal()"
                    class="flex items-center gap-2 bg-white/15 border border-white/30 text-white font-semibold px-5 py-2.5 rounded-xl hover:bg-white/25 transition text-sm">
                    <span class="material-icons text-base">account_balance</span> Request Withdrawal
                </button>
            </div>
        </div>

        {{-- ── How it works ─────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
            <h2 class="font-bold text-gray-900 mb-3 text-base">How buying power works</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-icons text-blue-600 text-base">savings</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Deposit funds</p>
                        <p class="text-gray-500 text-xs mt-0.5">Min $200 deposit via bank wire</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-icons text-green-600 text-base">gavel</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Bid up to 10×</p>
                        <p class="text-gray-500 text-xs mt-0.5">Bids ≥ ${{ number_format($threshold) }} require a 10% deposit</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <span class="material-icons text-amber-600 text-base">verified_user</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Fully refundable</p>
                        <p class="text-gray-500 text-xs mt-0.5">Unused deposits can be withdrawn anytime</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Pending wire deposit requests ─────────────────────────────── --}}
        @if($pendingWire->isNotEmpty())
        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-5 mb-6">
            <h2 class="font-bold text-gray-900 mb-1 text-base flex items-center gap-2">
                <span class="material-icons text-blue-500 text-lg">pending</span>
                Pending Wire Deposits
            </h2>
            <p class="text-xs text-gray-500 mb-3">These requests are waiting for your wire transfer to clear. Our team will confirm and update your balance.</p>
            <ul class="divide-y divide-gray-100">
                @foreach($pendingWire as $dr)
                <li class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-semibold text-gray-900">${{ number_format($dr->amount, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Submitted {{ $dr->requested_at->format('M j, Y g:i A') }}
                            &bull; Reference: <span class="font-mono font-medium">DEP-{{ Auth::id() }}</span>
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">
                        <span class="material-icons text-xs">schedule</span> Awaiting Wire
                    </span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- ── Pending withdrawal requests ──────────────────────────────── --}}
        @if(isset($pendingWithdrawals) && $pendingWithdrawals->isNotEmpty())
        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-5 mb-6">
            <h2 class="font-bold text-gray-900 mb-3 text-base flex items-center gap-2">
                <span class="material-icons text-amber-500 text-lg">hourglass_top</span>
                Pending Withdrawal Requests
            </h2>
            <ul class="divide-y divide-gray-100">
                @foreach($pendingWithdrawals as $w)
                <li class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-semibold text-gray-900">${{ number_format($w->amount, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Requested {{ $w->created_at->format('M j, Y') }}</p>
                    </div>
                    <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1 rounded-full">
                        <span class="material-icons text-xs">schedule</span> Pending Admin Review
                    </span>
                </li>
                @endforeach
            </ul>
            <p class="text-xs text-gray-500 mt-3 flex items-center gap-1">
                <span class="material-icons text-xs">info</span>
                Processing time: up to 3 business days after approval.
            </p>
        </div>
        @endif

    </div>{{-- /max-w-4xl --}}
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     WIRE DEPOSIT REQUEST MODAL
     Submits a DepositRequest (pending_wire) — does NOT credit the wallet.
══════════════════════════════════════════════════════════════════════════ --}}
<div id="depositModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg" onclick="event.stopPropagation()">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Add Security Deposit</h3>
                <p class="text-xs text-gray-500 mt-0.5">Your deposit is fully refundable</p>
            </div>
            <button onclick="closeDepositModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                <span class="material-icons text-xl">close</span>
            </button>
        </div>

        <form action="{{ route('buyer.deposit.add') }}" method="POST" class="px-6 py-5 space-y-5">
            @csrf

            {{-- Amount --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Deposit Amount</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold text-lg">$</span>
                    <input type="number" name="amount" id="depositAmountInput"
                        min="200" max="20000" step="50" value="500"
                        class="w-full pl-9 pr-4 py-3 border border-gray-300 rounded-xl text-xl font-bold text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        oninput="updateDepositCalc(this.value)">
                </div>
                <input type="range" id="depositSlider" min="200" max="20000" step="50" value="500"
                    class="w-full mt-3 accent-blue-600"
                    oninput="syncSlider(this.value)">
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                    <span>$200 min</span><span>$20,000 max</span>
                </div>
                @error('amount')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buying power preview --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Buying Power You'll Get</p>
                    <p id="buyingPowerPreview" class="text-2xl font-extrabold text-blue-800 mt-0.5">$5,000</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-blue-600">= deposit × 10</p>
                    <p class="text-xs text-blue-500 mt-1">Wire amount: <span id="totalDuePreview" class="font-bold">$500.00</span></p>
                </div>
            </div>

            {{-- Wire instructions --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2">
                <p class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                    <span class="material-icons text-base text-gray-500">account_balance</span>
                    Send your wire to CayMark's trust account
                </p>
                <p class="text-xs text-gray-600">After submitting this form, send your wire transfer using the details below. Your balance will be updated once our team confirms receipt — typically within 1–3 business days.</p>
                <div class="mt-2 pt-2 border-t border-gray-200 space-y-1 text-xs text-gray-700 font-mono">
                    <p><span class="text-gray-500">Bank:</span> First Caribbean International Bank</p>
                    <p><span class="text-gray-500">Account Name:</span> CayMark Marketplace Ltd.</p>
                    <p><span class="text-gray-500">Account #:</span> 1234567890</p>
                    <p><span class="text-gray-500">Routing #:</span> 021000021</p>
                    <p><span class="text-gray-500">Reference:</span> DEP-{{ Auth::id() }}</p>
                </div>
            </div>

            {{-- Refund notice --}}
            <div class="flex items-start gap-2 bg-green-50 border border-green-100 rounded-xl px-4 py-3">
                <span class="material-icons text-green-600 text-base mt-0.5">shield</span>
                <p class="text-xs text-green-800">Your security deposit is <strong>fully refundable</strong>. You can request a withdrawal at any time when you are not the highest bidder on an active auction.</p>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeDepositModal()"
                    class="flex-1 py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition text-sm shadow flex items-center justify-center gap-2">
                    <span class="material-icons text-base">send</span>
                    Submit Wire Request
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════
     WITHDRAW MODAL
══════════════════════════════════════════════════════════════════════════ --}}
<div id="withdrawModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Request Withdrawal</h3>
            <button onclick="closeWithdrawModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                <span class="material-icons text-xl">close</span>
            </button>
        </div>

        <div class="px-6 py-5">
            @if($locked > 0)
            <div class="mb-4 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-start gap-2">
                <span class="material-icons text-amber-500 text-base mt-0.5">lock</span>
                <p class="text-xs text-amber-800">${{ number_format($locked, 2) }} of your deposit is locked to active bids and cannot be withdrawn until you are outbid.</p>
            </div>
            @endif

            @if($available <= 0)
            <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-6 text-center">
                <span class="material-icons text-gray-400 text-4xl">account_balance_wallet</span>
                <p class="text-gray-600 font-semibold mt-2">No available balance to withdraw</p>
                <p class="text-xs text-gray-500 mt-1">Your available deposit is $0.00</p>
            </div>
            <button onclick="closeWithdrawModal()" class="mt-4 w-full py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                Close
            </button>
            @else
            <form action="{{ route('buyer.deposit-withdrawal.request') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Amount to Withdraw</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
                        <input type="number" name="amount" step="0.01" min="1" max="{{ $available }}"
                            value="{{ $available }}"
                            class="w-full pl-9 pr-4 py-3 border border-gray-300 rounded-xl text-lg font-bold text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Maximum available: <strong>${{ number_format($available, 2) }}</strong></p>
                    @error('amount')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Notes <span class="font-normal text-gray-400">(optional)</span></label>
                    <textarea name="notes" rows="2" maxlength="500"
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition resize-none"
                        placeholder="Bank details or any note for our team…"></textarea>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 text-xs text-blue-800 flex items-start gap-2">
                    <span class="material-icons text-blue-500 text-sm mt-0.5">info</span>
                    <span>Withdrawal requests are reviewed by our team. Processing takes up to 3 business days.</span>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeWithdrawModal()"
                        class="flex-1 py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">Cancel</button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition text-sm shadow">Submit Request</button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

<script>
function openDepositModal()   { document.getElementById('depositModal').classList.remove('hidden');  document.body.style.overflow='hidden'; }
function closeDepositModal()  { document.getElementById('depositModal').classList.add('hidden');     document.body.style.overflow=''; }
function openWithdrawModal()  { document.getElementById('withdrawModal').classList.remove('hidden'); document.body.style.overflow='hidden'; }
function closeWithdrawModal() { document.getElementById('withdrawModal').classList.add('hidden');    document.body.style.overflow=''; }

document.getElementById('depositModal').addEventListener('click',  closeDepositModal);
document.getElementById('withdrawModal').addEventListener('click', closeWithdrawModal);

function updateDepositCalc(val) {
    val = Math.max(200, Math.min(20000, parseFloat(val) || 200));
    document.getElementById('depositSlider').value       = val;
    document.getElementById('buyingPowerPreview').textContent = '$' + (val * 10).toLocaleString('en-US', {maximumFractionDigits: 0});
    document.getElementById('totalDuePreview').textContent    = '$' + val.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
function syncSlider(val) {
    document.getElementById('depositAmountInput').value = val;
    updateDepositCalc(val);
}
updateDepositCalc(500);

@if(request('action') === 'deposit')
document.addEventListener('DOMContentLoaded', function(){ openDepositModal(); });
@endif
</script>
@endsection
