@extends('layouts.admin')

@section('title', 'Security Deposits – Admin')

@section('content')
<div class="max-w-7xl mx-auto pb-10">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Security Deposits</h1>
        <p class="text-gray-500 text-sm mt-1">Confirm wire transfers, manage withdrawal requests, and view all buyer wallets.</p>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-800 text-sm font-medium flex items-center gap-2">
            <span class="material-icons text-green-600 text-base">check_circle</span> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-red-800 text-sm font-medium flex items-center gap-2">
            <span class="material-icons text-red-600 text-base">error</span> {{ session('error') }}
        </div>
    @endif

    {{-- ── Summary stats ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total in Wallets</p>
            <p class="text-2xl font-extrabold text-gray-900">${{ number_format($stats['total_deposited'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Available</p>
            <p class="text-2xl font-extrabold text-green-700">${{ number_format($stats['total_available'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Locked (active bids)</p>
            <p class="text-2xl font-extrabold text-amber-600">${{ number_format($stats['total_locked'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-orange-100 shadow-sm p-4 {{ $stats['pending_wire_count'] > 0 ? 'border-orange-300 bg-orange-50' : '' }}">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Pending Wire Requests</p>
            <p class="text-2xl font-extrabold {{ $stats['pending_wire_count'] > 0 ? 'text-orange-600' : 'text-gray-900' }}">
                {{ $stats['pending_wire_count'] }}
                @if($stats['pending_wire_count'] > 0)
                    <span class="text-base font-medium text-orange-500 ml-1">({{ number_format($stats['pending_wire_amount'], 2) }})</span>
                @endif
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION 1: PENDING WIRE DEPOSIT REQUESTS
         These are submitted by buyers — admin must confirm wire received.
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border {{ $pendingWireRequests->isNotEmpty() ? 'border-orange-200' : 'border-gray-100' }} shadow-sm mb-8 overflow-hidden">
        <div class="px-6 py-4 border-b {{ $pendingWireRequests->isNotEmpty() ? 'border-orange-100 bg-orange-50' : 'border-gray-100' }} flex items-center justify-between">
            <h2 class="font-bold text-gray-900 text-base flex items-center gap-2">
                <span class="material-icons {{ $pendingWireRequests->isNotEmpty() ? 'text-orange-500' : 'text-gray-400' }} text-lg">account_balance</span>
                Pending Wire Deposits
                @if($pendingWireRequests->isNotEmpty())
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-orange-500 text-white text-xs font-bold">{{ $pendingWireRequests->count() }}</span>
                @endif
            </h2>
            <p class="text-xs text-gray-500">Click "Confirm Wire Received" once you have verified the bank transfer.</p>
        </div>

        @if($pendingWireRequests->isEmpty())
            <div class="px-6 py-10 text-center">
                <span class="material-icons text-gray-300 text-5xl">check_circle</span>
                <p class="text-gray-500 text-sm mt-2">No pending wire deposit requests.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3 text-left">Buyer</th>
                        <th class="px-4 py-3 text-left">Amount</th>
                        <th class="px-4 py-3 text-left">Wire Reference</th>
                        <th class="px-4 py-3 text-left">Requested</th>
                        <th class="px-4 py-3 text-left">Notes</th>
                        <th class="px-4 py-3 text-center" style="min-width:220px;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pendingWireRequests as $dr)
                    <tr class="hover:bg-orange-50/40 transition">
                        <td class="px-4 py-3">
                            @if($dr->buyer)
                                <div class="font-semibold text-gray-900">{{ $dr->buyer->name }}</div>
                                <div class="text-xs text-gray-500">{{ $dr->buyer->email }}</div>
                                <a href="{{ route('admin.users.view', $dr->buyer->id) }}" class="text-xs text-blue-600 hover:underline">#{{ $dr->buyer->id }}</a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold text-gray-900 text-base">${{ number_format($dr->amount, 2) }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600 font-medium">DEP-{{ $dr->buyer_id }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $dr->requested_at->format('M j, Y g:i A') }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs max-w-[160px] truncate" title="{{ $dr->notes }}">{{ $dr->notes ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Confirm --}}
                                <form action="{{ route('admin.deposit-requests.confirm', $dr->id) }}" method="POST"
                                    onsubmit="return confirm('Confirm wire of ${{ number_format($dr->amount, 2) }} received from {{ addslashes($dr->buyer?->name ?? '') }}?\n\nThis will immediately credit their wallet.')">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-2 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition flex items-center gap-1.5 whitespace-nowrap">
                                        <span class="material-icons text-sm">check_circle</span>
                                        Confirm Wire Received
                                    </button>
                                </form>
                                {{-- Reject --}}
                                <form action="{{ route('admin.deposit-requests.reject', $dr->id) }}" method="POST"
                                    onsubmit="return confirm('Reject this deposit request? The buyer will be notified that the wire was not confirmed.')">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-2 bg-red-50 text-red-700 border border-red-200 text-xs font-bold rounded-lg hover:bg-red-100 transition flex items-center gap-1 whitespace-nowrap">
                                        <span class="material-icons text-sm">cancel</span>
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION 2: PENDING WITHDRAWAL REQUESTS
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900 text-base flex items-center gap-2">
                <span class="material-icons text-amber-500 text-lg">hourglass_top</span>
                Pending Withdrawal Requests
                @if($stats['pending_withdrawals_count'] > 0)
                    <span class="ml-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-amber-500 text-white text-xs font-bold">{{ $stats['pending_withdrawals_count'] }}</span>
                @endif
            </h2>
            @if($stats['pending_withdrawals_amount'] > 0)
            <p class="text-xs text-gray-500">Total pending: <strong>${{ number_format($stats['pending_withdrawals_amount'], 2) }}</strong></p>
            @endif
        </div>

        @if($pendingWithdrawals->isEmpty())
            <div class="px-6 py-10 text-center">
                <span class="material-icons text-gray-300 text-5xl">check_circle</span>
                <p class="text-gray-500 text-sm mt-2">No pending withdrawal requests.</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3 text-left">Buyer</th>
                        <th class="px-4 py-3 text-left">Amount</th>
                        <th class="px-4 py-3 text-left">Wallet Balance</th>
                        <th class="px-4 py-3 text-left">Requested</th>
                        <th class="px-4 py-3 text-left">Notes</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pendingWithdrawals as $wr)
                    @php $wWallet = $wr->user?->wallet; @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            @if($wr->user)
                                <div class="font-semibold text-gray-900">{{ $wr->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $wr->user->email }}</div>
                                <a href="{{ route('admin.users.view', $wr->user->id) }}" class="text-xs text-blue-600 hover:underline">#{{ $wr->user->id }}</a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold text-gray-900">${{ number_format($wr->amount, 2) }}</td>
                        <td class="px-4 py-3 text-xs">
                            @if($wWallet)
                                <span class="text-green-700 font-semibold">Avail: ${{ number_format($wWallet->available_balance, 2) }}</span><br>
                                <span class="text-amber-600">Locked: ${{ number_format($wWallet->locked_balance, 2) }}</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $wr->created_at->format('M j, Y g:i A') }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs max-w-[160px] truncate" title="{{ $wr->notes }}">{{ $wr->notes ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('admin.withdrawals.approve', $wr->id) }}" method="POST"
                                    onsubmit="return confirm('Approve withdrawal of ${{ number_format($wr->amount, 2) }} for {{ addslashes($wr->user?->name ?? '') }}?')">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-green-600 text-white text-xs font-bold rounded-lg hover:bg-green-700 transition flex items-center gap-1">
                                        <span class="material-icons text-xs">check</span> Approve
                                    </button>
                                </form>
                                <form action="{{ route('admin.withdrawals.reject', $wr->id) }}" method="POST"
                                    onsubmit="return confirm('Reject this withdrawal? Funds will be returned to available balance.')">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-red-50 border border-red-200 text-red-700 text-xs font-bold rounded-lg hover:bg-red-100 transition flex items-center gap-1">
                                        <span class="material-icons text-xs">close</span> Reject
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         SECTION 3: ALL BUYER WALLETS
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-900 text-base flex items-center gap-2">
                <span class="material-icons text-blue-600 text-lg">account_balance_wallet</span>
                All Buyer Wallets
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <th class="px-4 py-3 text-left">Buyer</th>
                        <th class="px-4 py-3 text-right">Available</th>
                        <th class="px-4 py-3 text-right">Locked</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Buying Power</th>
                        <th class="px-4 py-3 text-center">Add Deposit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($wallets as $wallet)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            @if($wallet->user)
                                <div class="font-semibold text-gray-900">{{ $wallet->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $wallet->user->email }}</div>
                                <a href="{{ route('admin.users.view', $wallet->user->id) }}" class="text-xs text-blue-600 hover:underline">#{{ $wallet->user->id }}</a>
                            @else
                                <span class="text-gray-400">Deleted user</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-green-700">${{ number_format($wallet->available_balance, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-amber-600">${{ number_format($wallet->locked_balance, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">${{ number_format($wallet->total_balance, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-700">${{ number_format($wallet->available_balance * 10, 0) }}</td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="openAddDepositModal({{ $wallet->user_id }}, '{{ addslashes($wallet->user?->name ?? 'User') }}')"
                                class="px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg hover:bg-blue-100 transition flex items-center gap-1 mx-auto">
                                <span class="material-icons text-xs">add</span> Manual Add
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400 text-sm">No wallets found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($wallets->hasPages())
        <div class="px-4 py-4 border-t border-gray-100">
            {{ $wallets->links() }}
        </div>
        @endif
    </div>
</div>

{{-- ── Manual Add Deposit Modal (admin override — bypasses wire flow) ──────── --}}
<div id="adminDepositModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Manual Deposit (Admin Override)</h3>
                <p id="adminDepositBuyerName" class="text-xs text-gray-500 mt-0.5"></p>
            </div>
            <button onclick="closeAddDepositModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                <span class="material-icons text-xl">close</span>
            </button>
        </div>
        <form id="adminDepositForm" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Amount</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-bold">$</span>
                    <input type="number" name="amount" min="1" step="0.01" required
                        class="w-full pl-9 pr-4 py-3 border border-gray-300 rounded-xl text-lg font-bold text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="0.00">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Notes</label>
                <input type="text" name="notes" maxlength="500"
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="e.g. Bank wire ref #XYZ123 confirmed">
            </div>
            <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-2 flex items-start gap-2">
                <span class="material-icons text-xs mt-0.5">warning</span>
                Use the "Confirm Wire Received" button above for buyer-submitted wire requests. This manual form is for corrections and exceptions only.
            </p>
            <div class="flex gap-3">
                <button type="button" onclick="closeAddDepositModal()"
                    class="flex-1 py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">Cancel</button>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition text-sm shadow">Add Deposit</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddDepositModal(userId, buyerName) {
    document.getElementById('adminDepositBuyerName').textContent = 'Buyer: ' + buyerName;
    document.getElementById('adminDepositForm').action = '/admin/deposits/' + userId + '/add';
    document.getElementById('adminDepositModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeAddDepositModal() {
    document.getElementById('adminDepositModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.getElementById('adminDepositModal').addEventListener('click', closeAddDepositModal);
</script>
@endsection
