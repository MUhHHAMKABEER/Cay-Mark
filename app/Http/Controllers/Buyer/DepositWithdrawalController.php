<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\DepositService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function requestWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $amount = (float) $request->amount;

        try {
            $withdrawal = $this->depositService->requestWithdrawal($user, $amount, $request->notes);

            return redirect()->back()->with('success', 'Withdrawal request submitted. Pending admin approval.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
