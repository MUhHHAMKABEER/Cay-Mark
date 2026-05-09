<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listing Submitted - CayMark</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0;">CayMark</h1>
            <p style="margin: 5px 0 0 0;">Island Exchange & Auction House</p>
        </div>
        
        <div style="background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px;">
            <h2 style="color: #1e293b;">Listing Submitted for Review – {{ $listing->year ?? '' }} {{ $listing->make ?? '' }} {{ $listing->model ?? '[VEHICLE_NAME]' }}</h2>
            
            <p>Your listing for {{ $listing->year ?? '' }} {{ $listing->make ?? '' }} {{ $listing->model ?? '[VEHICLE_NAME]' }} has been successfully submitted and is currently under review.</p>
            
            <p>You will be notified once a decision is made.</p>
            
            <p style="margin-top: 30px;">Thank you for using CayMark!</p>
            
            <p>Best regards,<br>The CayMark Team</p>
        </div>
        
        <div style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 12px;">
            <p>This is an automated email. Please do not reply to this message.</p>
            <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

