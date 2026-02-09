<?php

namespace App\Http\Controllers;

use App\Http\Requests\TowProviderSignupStoreRequest;
use App\Models\TowProviderSignup;
use App\Services\DemoPaymentGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TowProviderSignupController extends Controller
{
    public function create(Request $request): View
    {
        $signupFeeCents = config('services.tow_provider.signup_fee_cents', 5000);
        $signupFeeDollars = number_format($signupFeeCents / 100, 2);

        return view('tow-provider.signup', [
            'signupFeeCents' => $signupFeeCents,
            'signupFeeDollars' => $signupFeeDollars,
        ]);
    }

    public function store(TowProviderSignupStoreRequest $request): RedirectResponse
    {
        $signupFeeCents = config('services.tow_provider.signup_fee_cents', 5000);

        // 1. Store business license file
        $licensePath = null;
        $licenseFilename = null;
        if ($request->hasFile('business_license')) {
            $file = $request->file('business_license');
            $licensePath = $file->store('tow_provider_licenses', 'public');
            $licenseFilename = $file->getClientOriginalName();
        }

        // 2. Create signup record (before payment so we have an ID)
        $signup = TowProviderSignup::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'business_name' => $request->business_name,
            'license_path' => $licensePath,
            'license_filename' => $licenseFilename,
            'terms_accepted_at' => now(),
            'amount_cents' => $signupFeeCents,
            'payment_status' => 'pending',
        ]);

        // 3. Process payment
        $expiry = preg_replace('/\s+/', '', $request->card_expiry);
        if (strlen($expiry) === 4) {
            $expiry = substr($expiry, 0, 2) . '/' . substr($expiry, 2, 2);
        }
        $gateway = new DemoPaymentGateway();
        $result = $gateway->charge(
            $signupFeeCents,
            $request->card_number,
            $expiry,
            $request->card_cvv,
            $request->cardholder_name
        );

        if ($result['success']) {
            $signup->update([
                'payment_status' => 'paid',
                'payment_reference' => $result['transaction_id'],
            ]);
            Log::info('Tow provider signup completed', [
                'signup_id' => $signup->id,
                'email' => $signup->email,
                'payment_reference' => $result['transaction_id'],
            ]);
            return redirect()->route('tow-provider.signup.thanks')->with('success', true);
        }

        $signup->update(['payment_status' => 'failed']);
        return back()
            ->withInput($request->except('card_number', 'card_cvv'))
            ->withErrors(['payment' => $result['message'] ?? 'Payment could not be processed. Please try again.']);
    }

    public function thanks(Request $request): View|RedirectResponse
    {
        if (!session('success')) {
            return redirect()->route('tow-provider.signup');
        }
        return view('tow-provider.thanks');
    }
}
