<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmed - CayMark</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background: #f3f4f6; }
        .wrapper { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: #fff; padding: 32px 30px; text-align: center; }
        .header h1 { margin: 0 0 4px; font-size: 1.6rem; letter-spacing: -0.5px; }
        .header p { margin: 0; font-size: 0.9rem; opacity: 0.85; }
        .content { padding: 32px 30px; }
        .success-banner { background: #d1fae5; border-left: 4px solid #10b981; border-radius: 6px; padding: 14px 18px; margin-bottom: 24px; }
        .success-banner p { margin: 0; font-weight: 600; color: #065f46; font-size: 1rem; }
        .pickup-box { background: #fef3c7; border: 2px solid #fbbf24; border-radius: 10px; padding: 20px 24px; margin: 24px 0; }
        .pickup-label { font-size: 0.7rem; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.08em; margin: 0 0 6px; }
        .pickup-code { font-size: 2rem; font-weight: 800; color: #1e40af; letter-spacing: 0.08em; font-family: 'Courier New', monospace; margin: 0 0 10px; }
        .pickup-warning { margin: 0; color: #78350f; font-size: 0.9rem; display: flex; align-items: flex-start; gap: 6px; }
        .detail-table { width: 100%; border-collapse: collapse; margin: 24px 0; }
        .detail-table tr { border-bottom: 1px solid #f1f5f9; }
        .detail-table tr:last-child { border-bottom: none; }
        .detail-table td { padding: 10px 0; font-size: 0.9rem; }
        .detail-table td:first-child { color: #6b7280; width: 40%; }
        .detail-table td:last-child { font-weight: 600; color: #111827; text-align: right; }
        .cta-btn { display: block; background: #2563eb; color: #fff !important; text-align: center; padding: 14px 30px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 0.95rem; margin: 24px 0; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
        .footer { text-align: center; padding: 20px 30px 28px; color: #9ca3af; font-size: 0.78rem; line-height: 1.6; }
        .footer a { color: #6b7280; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <h1>CayMark</h1>
        <p>Island Exchange &amp; Auction House</p>
    </div>

    <div class="content">

        <p style="margin: 0 0 20px; font-size: 1rem; color: #111827;">
            Hi <strong>{{ $buyer->name ?? 'Buyer' }}</strong>,
        </p>

        <div class="success-banner">
            <p>✓ Your payment has been successful.</p>
        </div>

        <p style="margin: 0 0 8px; color: #374151; font-size: 0.95rem;">
            Please proceed to the <strong>Messaging Center</strong> to arrange pickup details with your seller.
        </p>

        {{-- ── Pickup Code ── --}}
        @if(!empty($pickup_code))
        <div class="pickup-box">
            <p class="pickup-label">Your Pickup Code</p>
            <p class="pickup-code">{{ $pickup_code }}</p>
            <p class="pickup-warning">
                <span style="flex-shrink:0;">⚠</span>
                <span>Provide this code to the seller after vehicle transfer to complete this sale.</span>
            </p>
        </div>
        @endif

        {{-- ── Transaction Details ── --}}
        <table class="detail-table">
            <tr>
                <td>Item</td>
                <td>{{ $invoice->item_name ?? '—' }}</td>
            </tr>
            <tr>
                <td>Item ID</td>
                <td style="font-family:'Courier New',monospace;">
                    {{ $invoice->item_id ? strtoupper($invoice->item_id) : ('CM' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT)) }}
                </td>
            </tr>
            <tr>
                <td>Amount Paid</td>
                <td style="color:#059669;">${{ number_format((float) $invoice->total_amount_due, 2) }}</td>
            </tr>
            <tr>
                <td>Payment Date</td>
                <td>{{ ($invoice->paid_at ?? ($payment->created_at ?? now()))->format('M d, Y') }}</td>
            </tr>
            <tr>
                <td>Invoice</td>
                <td style="font-family:'Courier New',monospace;">#{{ $invoice->invoice_number ?? $invoice->id }}</td>
            </tr>
        </table>

        <a href="{{ $messaging_center_url ?? route('messaging.index') }}" class="cta-btn">
            Open Messaging Center →
        </a>

        <hr class="divider">

        <p style="margin: 0; font-size: 0.9rem; color: #374151;">
            Thank you,<br>
            <strong>The CayMark Team</strong><br>
            <a href="mailto:support@caymark.co" style="color:#2563eb;">support@caymark.co</a>
        </p>

    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply directly to this email.</p>
        <p>You can view your purchase and pickup code at any time in your<br>
           CayMark dashboard under <strong>My Auctions → Won</strong>.</p>
        <p style="margin-top:12px;">&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
    </div>

</div>
</body>
</html>
