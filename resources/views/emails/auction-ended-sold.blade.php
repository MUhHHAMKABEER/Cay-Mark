<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction Ended - Sold - CayMark</title>
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
        <h2>Your Auction Has Ended – {{ $listing->year ?? '' }} {{ $listing->make ?? '' }} {{ $listing->model ?? '[VEHICLE_NAME]' }} Sold</h2>
        
        <p>Hi {{ $seller->name ?? $seller->first_name ?? '[SELLER_NAME]' }},</p>
        
        <p>Your auction for {{ $listing->year ?? '' }} {{ $listing->make ?? '' }} {{ $listing->model ?? '[VEHICLE_NAME]' }} has ended and the item has sold.</p>
        
        <div class="success-box">
            <p style="margin: 0;"><strong>Winning bid: ${{ number_format($winningBidAmount ?? 0, 2) }}</strong></p>
        </div>
        
        <p>Pickup coordination will begin once buyer payment is completed inside CayMark.</p>
        
        <div style="text-align: center;">
            <a href="{{ route('seller.listings.index') }}" class="button">View Your Listings</a>
        </div>
        
        <p>Best regards,<br>The CayMark Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
    </div>
</body>
</html>

