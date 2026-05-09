<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payout processed - CayMark</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0;">CayMark</h1>
            <p style="margin: 5px 0 0 0;">Island Exchange &amp; Auction House</p>
        </div>
        <div style="background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px;">
            <h2 style="color: #1e293b;">Your payout has been processed</h2>
            <p>Hello {{ $seller->name ?? 'Seller' }},</p>
            <p>Your payout @isset($payout->amount)of <strong>${{ number_format((float) $payout->amount, 2) }}</strong> @endisset has been processed.</p>
            <p>Thank you for selling on CayMark.</p>
            <p>Best regards,<br>The CayMark Team</p>
        </div>
        <div style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 12px;">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
