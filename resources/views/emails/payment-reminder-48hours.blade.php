<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FINAL NOTICE – Payment Overdue - CayMark</title>
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
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #f87171 100%);
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
        .danger-box {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            background: #dc2626;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
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
        <h2>FINAL NOTICE — Payment Overdue for {{ $invoice->item_name ?? '[VEHICLE_NAME]' }}</h2>
        
        <p>Your payment for {{ $invoice->item_name ?? '[VEHICLE_NAME]' }} is now overdue.</p>
        
        <div class="danger-box">
            <p style="margin: 0;"><strong>Failure to complete payment may result in loss of this item and possible account restrictions:</strong></p>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ route('buyer.payment.checkout-single', $invoice->id) }}" class="button">Pay Now</a>
        </div>
        
        <p>Best regards,<br>The CayMark Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
    </div>
</body>
</html>

