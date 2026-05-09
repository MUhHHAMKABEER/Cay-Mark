@php
    $num = $ticket->public_ticket_number ?? $ticket->id;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Support ticket received - CayMark</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0;">CayMark</h1>
            <p style="margin: 5px 0 0 0;">Island Exchange &amp; Auction House</p>
        </div>

        <div style="background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px;">
            <p style="color: #1e293b; font-size: 16px;">Hello {{ $user->name }},</p>

            <p style="color: #334155;">Your ticket (<strong>#{{ $num }}</strong>) has been received successfully and is currently being reviewed by our team.</p>

            <p style="color: #334155;">You can expect a response within <strong>1 to 2 business days</strong>.</p>

            <p style="color: #334155;">Thank you for contacting CayMark.</p>

            <p style="color: #1e293b; margin-top: 24px;">— CayMark Support Team</p>
        </div>

        <div style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 12px;">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
