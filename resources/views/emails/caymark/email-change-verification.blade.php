<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your email change - CayMark</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px; }
        .code-box { background: #fff; border: 2px dashed #2563eb; padding: 20px; text-align: center; margin: 20px 0; border-radius: 10px; font-size: 28px; font-weight: bold; letter-spacing: 8px; color: #1e3a8a; }
        .warning-box { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .footer { text-align: center; margin-top: 30px; color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>CayMark</h1>
        <p>Island Exchange & Auction House</p>
    </div>
    <div class="content">
        <h2>Verify your email address change</h2>
        <p>You requested to change your CayMark account email to <strong>{{ $new_email ?? '' }}</strong>.</p>
        <p>Use the verification code below to confirm this change. This code was sent to your <strong>current email address</strong> for your security.</p>
        <div class="code-box">{{ $code ?? '' }}</div>
        <div class="warning-box">
            <p style="margin: 0;">This code expires in {{ $minutes ?? 15 }} minutes. If you did not request this change, ignore this email and your address will stay the same.</p>
        </div>
        <p>Best regards,<br>The CayMark Team</p>
    </div>
    <div class="footer">
        <p>This is an automated email. Please do not reply.</p>
        <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
    </div>
</body>
</html>
