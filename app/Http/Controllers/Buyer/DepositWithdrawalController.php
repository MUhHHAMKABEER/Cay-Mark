<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\DepositService;
use Illuminate\Http\Request;
use App\Http\Requests\BuyerDepositWithdrawalRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\Buyer\DepositWithdrawalOps;

class DepositWithdrawalController extends Controller
{
    protected $depositService;

    public function __construct()
    {
        $this->depositService = new DepositService();
    }

    /**
     * Show withdrawal request form.
     */
    public function index()
    {
        $user = Auth::user();
        $walletSummary = $this->depositService->getWalletSummary($user);
        
        // Get pending withdrawal requests
        $pendingWithdrawals = \App\Models\Deposit::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        return view('Buyer.deposit-withdrawal', compact('walletSummary', 'pendingWithdrawals'));
    }

    /**
     * Request withdrawal.
     */
    public function requestWithdrawal(BuyerDepositWithdrawalRequest $request)
    {
        return DepositWithdrawalOps::requestWithdrawal($request, $this->depositService);
    }
}
