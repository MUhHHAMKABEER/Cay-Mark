<?php

namespace App\Services;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SupportOps
{
    public static function buyerStore($request)
    {
        return self::storeFromValidated($request->validated());
    }

    public static function sellerStore($request)
    {
        return self::storeFromValidated($request->validated());
    }

    /**
     * @param  array{title: string, message: string}  $validated
     */
    private static function storeFromValidated(array $validated)
    {
        $publicNumber = SupportTicket::generateUniquePublicTicketNumber();

        $ticket = SupportTicket::create([
            'public_ticket_number' => $publicNumber,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        $user = Auth::user();
        self::notifySupportInbox($ticket, $user);
        self::notifyUserReceipt($ticket, $user);

        return back()->with('success', 'Support ticket submitted successfully. We will respond soon.');
    }

    private static function notifySupportInbox(SupportTicket $ticket, $user): void
    {
        $to = config('support.inbox');
        if (! is_string($to) || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Support ticket email skipped: invalid SUPPORT_INBOX', ['to' => $to]);

            return;
        }

        $from = config('support.mail_from');
        if (! is_string($from) || ! filter_var($from, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Support ticket email skipped: invalid SUPPORT_MAIL_FROM', ['from' => $from]);

            return;
        }

        $fromName = (string) config('support.mail_from_name', 'CayMark Support');

        $replyTo = $user->email ?? null;
        $replyToValid = is_string($replyTo) && filter_var($replyTo, FILTER_VALIDATE_EMAIL);
        if (! $replyToValid) {
            Log::warning('Support ticket inbound email: invalid user email, Reply-To omitted', ['user_id' => $user->id ?? null]);
        }

        $cc = array_values(array_filter(
            config('support.inbox_cc', []),
            fn ($email) => is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)
        ));

        $category = $ticket->title;
        $subject = 'New Support Ticket – '.$category;

        try {
            Mail::send('emails.caymark.support-ticket-zoho-inbound', [
                'ticket' => $ticket,
                'user' => $user,
            ], function ($message) use ($to, $cc, $subject, $from, $fromName, $replyTo, $replyToValid, $user) {
                $message->to($to)
                    ->from($from, $fromName)
                    ->subject($subject);
                if ($replyToValid) {
                    $message->replyTo($replyTo, $user->name ?? '');
                }
                if ($cc !== []) {
                    $message->cc($cc);
                }
            });
        } catch (Throwable $e) {
            Log::error('Support ticket inbound email failed', [
                'ticket_id' => $ticket->id,
                'public_ticket_number' => $ticket->public_ticket_number,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    private static function notifyUserReceipt(SupportTicket $ticket, $user): void
    {
        $to = $user->email ?? null;
        if (! is_string($to) || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Support ticket confirmation email skipped: invalid user email', ['user_id' => $user->id ?? null]);

            return;
        }

        $from = config('support.mail_from');
        if (! is_string($from) || ! filter_var($from, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $fromName = (string) config('support.mail_from_name', 'CayMark Support');
        $subject = 'Your CayMark support ticket #'.($ticket->public_ticket_number ?? $ticket->id);

        try {
            Mail::send('emails.caymark.support-ticket-received-user', [
                'ticket' => $ticket,
                'user' => $user,
            ], function ($message) use ($to, $subject, $from, $fromName) {
                $message->to($to)->from($from, $fromName)->subject($subject);
            });
        } catch (Throwable $e) {
            Log::error('Support ticket user confirmation email failed', [
                'ticket_id' => $ticket->id,
                'public_ticket_number' => $ticket->public_ticket_number,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
