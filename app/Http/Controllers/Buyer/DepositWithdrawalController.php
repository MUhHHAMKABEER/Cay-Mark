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
        $user          = Auth::user();
        $walletSummary = $this->depositService->getWalletSummary($user);

        $pendingWithdrawals = \App\Models\Deposit::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->get();

        // Wire deposit requests the buyer submitted (pending or recent)
        $depositRequests = \App\Models\DepositRequest::where('buyer_id', $user->id)
            ->orderByDesc('requested_at')
            ->limit(10)
            ->get();

        return view('Buyer.deposit-withdrawal', compact('walletSummary', 'pendingWithdrawals', 'depositRequests'));
    }

    /**
     * Submit a deposit request (pending_wire).
     *
     * Does NOT touch the wallet. Creates a DepositRequest record and notifies
     * the buyer that we're waiting for their wire. The admin confirms the wire
     * via the Security Deposits panel, which then credits the wallet.
     */
    public function addDeposit(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:200', 'max:20000'],
        ], [
            'amount.min' => 'The minimum security deposit is $200.',
            'amount.max' => 'The maximum security deposit is $20,000.',
        ]);

        $user   = Auth::user();
        $amount = (float) $request->amount;

        \App\Models\DepositRequest::create([
            'buyer_id'     => $user->id,
            'amount'       => $amount,
            'status'       => 'pending_wire',
            'requested_at' => now(),
            'notes'        => $request->input('notes'),
        ]);

        try {
            (new \App\Services\NotificationService())->depositWireRequestReceived($user, $amount);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('depositWireRequestReceived notification failed: ' . $e->getMessage());
        }

        return redirect()->route('buyer.deposit-withdrawal')
            ->with('deposit_request_submitted', number_format($amount, 2));
    }

    /**
     * Request withdrawal.
     */
    public function requestWithdrawal(BuyerDepositWithdrawalRequest $request)
    {
        return DepositWithdrawalOps::requestWithdrawal($request, $this->depositService);
    }
}
