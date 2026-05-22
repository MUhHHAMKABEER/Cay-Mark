<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email change requested - CayMark</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .warning-box { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CayMark</h1>
        <p>Island Exchange &amp; Auction House</p>
    </div>
    <div class="content">
        <h2>Email change requested</h2>
        <p>Hi {{ $user->name ?? 'there' }},</p>
        <p>A request was made to change the email address on your CayMark account to <strong>{{ $new_email ?? '' }}</strong>.</p>
        <p>A verification code has been sent to the new email address to confirm this change.</p>
        <div class="warning-box">
            <p style="margin: 0;"><strong>Didn't request this?</strong> If you did not initiate this change, please contact CayMark support immediately as your account may be compromised.</p>
        </div>
        <p>Best regards,<br>The CayMark Team</p>
    </div>
    <div class="footer">
        <p>This is an automated security notification. Please do not reply.</p>
        <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
    </div>
</body>
</html>
