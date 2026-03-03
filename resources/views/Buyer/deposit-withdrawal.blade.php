@extends('layouts.Buyer')

@section('title', 'Wallet / Deposits & Withdrawals - CayMark')

@section('content')
<div class="container mx-auto px-4 py-8 relative z-10">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Wallet / Deposits & Withdrawals</h1>
        <p class="text-gray-600">Manage your buying power and withdrawal requests</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <p class="text-sm font-medium text-gray-500 mb-1">Available Balance</p>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($walletSummary['available_balance'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <p class="text-sm font-medium text-gray-500 mb-1">Locked Balance</p>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($walletSummary['locked_balance'] ?? 0, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <p class="text-sm font-medium text-gray-500 mb-1">Total Balance</p>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($walletSummary['total_balance'] ?? 0, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Deposit & Withdraw</h2>
        <p class="text-gray-600 mb-4">Bids at or above ${{ number_format($walletSummary['deposit_threshold'] ?? 2000, 0) }} require a {{ $walletSummary['deposit_percentage'] ?? 10 }}% deposit. Add funds when you checkout after winning an auction, or contact support for deposit options.</p>
        <a href="{{ route('buyer.bids') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">My Bids</a>
    </div>

    @if(isset($pendingWithdrawals) && $pendingWithdrawals->isNotEmpty())
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Pending Withdrawal Requests</h2>
        <ul class="space-y-3">
            @foreach($pendingWithdrawals as $w)
            <li class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <span class="font-medium">${{ number_format($w->amount, 2) }}</span>
                <span class="text-sm text-amber-600 font-medium">Pending</span>
                <span class="text-sm text-gray-500">{{ $w->created_at->format('M j, Y') }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endsection
