<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (TransportExceptionInterface $e) {
            Log::error('Password reset email failed', ['email' => $request->input('email'), 'error' => $e->getMessage()]);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'We could not send the reset email. Please try again later or contact support@caymark.co.']);
        } catch (\Throwable $e) {
            Log::error('Password reset failed', ['email' => $request->input('email'), 'error' => $e->getMessage()]);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Something went wrong. Please try again later.']);
        }

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'If that email is registered, we have sent a password reset link.');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
