<?php

namespace App\Services\Admin;

use App\Models\Listing;
use App\Models\User;
use App\Models\Payment;
use Carbon\Carbon;

class AdminActionHub
{
    public static function disapproveListing($request, $id)
    {
        $request->validated();

        $listing = Listing::findOrFail($id);
        $listing->status = 'rejected';
        $listing->rejected_at = now();
        $listing->rejected_by = auth()->id();
        $listing->rejection_reason = $request->rejection_reason;
        $listing->rejection_notes = $request->rejection_notes;
        $listing->save();

        // Send rejection email to seller
        try {
            \Mail::send('emails.listing-rejected', [
                'listing' => $listing,
                'seller' => $listing->seller,
                'rejectionReason' => $request->rejection_reason,
                'rejectionNotes' => $request->rejection_notes,
            ], function ($message) use ($listing) {
                $message->to($listing->seller->email, $listing->seller->name)
                    ->subject('Listing Rejected – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '[VEHICLE_NAME]'));
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Listing rejected successfully.');
    }

    public static function updateUserProfile($request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validated();
        $user->update($validated);

        return back()->with('success', 'User updated successfully.');
    }

    public static function resetUserPassword($request, $id)
    {
        $request->validated();
        $user = User::findOrFail($id);
        $user->password = \Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password reset successfully.');
    }

    public static function toggleUserStatus($request, $id)
    {
        $request->validated();
        $user = User::findOrFail($id);

        if ($request->action === 'suspend') {
            $user->update([
                'is_restricted' => true,
                'restriction_reason' => $request->reason,
                'restriction_ends_at' => now()->addDays(30),
            ]);
        } else {
            $user->update([
                'is_restricted' => false,
                'restriction_reason' => null,
                'restriction_ends_at' => null,
            ]);
        }

        return back()->with('success', 'User status updated successfully.');
    }

    public static function editListing($request, $id)
    {
        $listing = Listing::findOrFail($id);
        $validated = $request->validated();
        $listing->update($validated);

        return back()->with('success', 'Listing updated successfully.');
    }

    public static function extendAuction($request, $id)
    {
        $request->validated();
        $listing = Listing::findOrFail($id);

        if ($listing->auction_end_time) {
            $listing->auction_end_time = Carbon::parse($listing->auction_end_time)
                ->addDays($request->additional_days);
        } else {
            $listing->auction_end_time = now()->addDays($listing->auction_duration + $request->additional_days);
        }

        $listing->save();

        return back()->with('success', 'Auction time extended successfully.');
    }

    public static function updatePaymentStatus($request, $id)
    {
        $payment = Payment::findOrFail($id);
        $request->validated();
        $payment->update(['status' => $request->status]);

        if ($request->status === 'completed' && $payment->invoice) {
            try {
                \Mail::send('emails.payment-successful', [
                    'invoice' => $payment->invoice,
                    'buyer' => $payment->user,
                    'payment' => $payment,
                ], function ($message) use ($payment) {
                    $message->to($payment->user->email, $payment->user->name)
                        ->subject('Payment Successful – ' . ($payment->invoice->item_name ?? '[VEHICLE_NAME]'));
                });
            } catch (\Exception $e) {
                \Log::error('Failed to resend payment confirmation email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Payment status updated successfully.');
    }

    public static function updateDisputeStatus($request, $id)
    {
        $request->validated();
        // Placeholder - implement when Dispute model exists
        return back()->with('success', 'Dispute status updated successfully.');
    }

    public static function rejectListing($request, Listing $listing)
    {
        $request->validated();

        $listing->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
            'rejection_notes' => $request->rejection_notes,
        ]);

        try {
            \Mail::send('emails.listing-rejected', [
                'listing' => $listing,
                'seller' => $listing->seller,
                'rejectionReason' => $request->rejection_reason,
            ], function ($message) use ($listing) {
                $message->to($listing->seller->email, $listing->seller->name)
                    ->subject('Listing Rejected – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '[VEHICLE_NAME]'));
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email: ' . $e->getMessage());
        }

        return back()->with('success', 'Listing rejected successfully.');
    }

    public static function updatePayoutStatus($request, $payoutId)
    {
        $request->validated();
        $payout = \App\Models\Payout::findOrFail($payoutId);

        $payout->update([
            'status' => $request->status,
            'transaction_reference' => $request->transaction_reference,
            'date_sent' => $request->date_sent,
            'finance_notes' => $request->finance_notes,
        ]);

        if (in_array($request->status, ['sent', 'paid_successfully'])) {
            try {
                \Mail::send('emails.payout-status-updated', [
                    'payout' => $payout,
                    'seller' => $payout->seller,
                ], function ($message) use ($payout) {
                    $message->to($payout->seller->email, $payout->seller->name)
                        ->subject('Payout Status Updated - CayMark');
                });
            } catch (\Exception $e) {
                \Log::error('Failed to send payout status email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Payout status updated successfully.');
    }

    public static function resolveDefaultByRelist($request, $defaultId)
    {
        $request->validated();
        $default = \App\Models\BuyerDefault::findOrFail($defaultId);
        $defaultService = new \App\Services\DefaultService();
        $defaultService->resolveByRelist($default, $request->admin_notes ?? '');

        return back()->with('success', 'Default resolved by relisting.');
    }

    public static function closeUnpaidAuction($request, $defaultId)
    {
        $request->validated();
        $default = \App\Models\BuyerDefault::findOrFail($defaultId);
        $default->update([
            'status' => 'resolved',
            'resolution_type' => 'closed',
            'admin_notes' => $request->admin_notes ?? 'Auction closed permanently by admin.',
        ]);

        $default->listing->update([
            'status' => 'closed',
        ]);

        return back()->with('success', 'Auction closed successfully.');
    }
}

