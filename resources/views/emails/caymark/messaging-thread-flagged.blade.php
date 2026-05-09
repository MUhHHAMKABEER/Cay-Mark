<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Messaging Thread Flagged - CayMark</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0;">CayMark</h1>
            <p style="margin: 5px 0 0 0;">Messaging Center · Admin Alert</p>
        </div>

        <div style="background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px;">
            <h2 style="color: #1e293b;">A messaging thread requires your attention</h2>

            <p>The post-auction Messaging Center thread for the listing below has been flagged for admin review.</p>

            <table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
                <tr>
                    <td style="padding: 8px 0; color: #475569; font-weight: 600; width: 160px;">Reason</td>
                    <td style="padding: 8px 0;">{{ $reasonLabel }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #475569; font-weight: 600;">Listing</td>
                    <td style="padding: 8px 0;">{{ $listingTitle }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #475569; font-weight: 600;">Invoice</td>
                    <td style="padding: 8px 0;">#{{ $invoiceNumber }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #475569; font-weight: 600;">Buyer</td>
                    <td style="padding: 8px 0;">{{ $buyerName }} &lt;{{ $buyerEmail }}&gt;</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #475569; font-weight: 600;">Seller</td>
                    <td style="padding: 8px 0;">{{ $sellerName }} &lt;{{ $sellerEmail }}&gt;</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #475569; font-weight: 600;">Exchanges so far</td>
                    <td style="padding: 8px 0;">{{ $exchangesCount }} of {{ $maxExchanges }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #475569; font-weight: 600;">First exchange</td>
                    <td style="padding: 8px 0;">{{ $firstExchangeAt ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #475569; font-weight: 600;">Last exchange</td>
                    <td style="padding: 8px 0;">{{ $lastExchangeAt ?? '—' }}</td>
                </tr>
            </table>

            <p>The thread is <strong>not locked</strong>. The buyer and seller can keep editing while your team steps in to assist.</p>

            <div style="text-align: center; margin: 28px 0;">
                <a href="{{ $reviewUrl }}" style="display: inline-block; background: #2563eb; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    Review thread
                </a>
            </div>

            <p style="color: #475569; font-size: 14px;">If everything looks fine, you can clear the flag from the admin dashboard.</p>
        </div>

        <div style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 12px;">
            <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
