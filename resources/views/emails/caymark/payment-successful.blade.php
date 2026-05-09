<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - CayMark</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .success-box {
            background: #d1fae5;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .pickup-box {
            background: #fef3c7;
            border: 2px solid #fbbf24;
            padding: 18px 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .pickup-code {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e40af;
            letter-spacing: 0.06em;
            font-family: 'Courier New', monospace;
            margin: 8px 0 0 0;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CayMark</h1>
        <p>Island Exchange & Auction House</p>
    </div>

    <div class="content">
        <h2 style="margin-top: 0;">Payment</h2>

        <div class="success-box">
            <p style="margin: 0;"><strong>Your payment has been successful.</strong></p>
        </div>

        <p>Your payment for <strong>{{ $invoice->item_name ?? '[VEHICLE_NAME]' }}</strong> has been processed.</p>

        <p>Please proceed to the <strong>Messaging Center</strong> to arrange pickup details with your seller.</p>

        <div style="text-align: center;">
            <a href="{{ $messaging_center_url ?? route('messaging.index') }}" class="button">Open Messaging Center</a>
        </div>

        @if(!empty($pickup_code))
            <div class="pickup-box">
                <p style="margin: 0; font-size: 0.75rem; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.06em;">Pickup Code</p>
                <p class="pickup-code">{{ $pickup_code }}</p>
                <p style="margin: 12px 0 0 0; color: #78350f; font-size: 0.95rem;">Provide this code to the seller after vehicle transfer to complete this sale.</p>
            </div>
        @endif

        <p style="font-size: 0.9rem; color: #4b5563;">You can also view this purchase and your pickup code anytime from your CayMark dashboard under <strong>My Auctions</strong> (Won).</p>

        <p>Best regards,<br>The CayMark Team</p>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
    </div>
</body>
</html>
