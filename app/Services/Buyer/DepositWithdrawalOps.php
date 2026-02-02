<?php

namespace App\Services\Buyer;

class DepositWithdrawalOps
{
    public static function requestWithdrawal($request, $depositService)
    {
        $request->validated();
        $user = \Illuminate\Support\Facades\Auth::user();
        $amount = (float) $request->amount;

        try {
            $depositService->requestWithdrawal($user, $amount, $request->notes);
            return redirect()->back()->with('success', 'Withdrawal request submitted. Pending admin approval.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

