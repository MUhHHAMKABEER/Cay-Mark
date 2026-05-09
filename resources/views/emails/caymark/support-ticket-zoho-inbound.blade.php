@php
    $category = $ticket->title;
    $tz = config('app.timezone');
    $dateStr = $ticket->created_at->timezone($tz)->format('l, F j, Y \a\t g:i A T');
    $ticketNumber = $ticket->public_ticket_number ?? (string) $ticket->id;
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Support Ticket – {{ $category }} - CayMark</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0;">CayMark</h1>
            <p style="margin: 5px 0 0 0;">Island Exchange &amp; Auction House</p>
        </div>

        <div style="background: #f9fafb; padding: 30px; border-radius: 0 0 10px 10px;">
            <h2 style="color: #1e293b; margin-top: 0;">New Support Ticket – {{ $category }}</h2>
            <p style="font-weight: bold; color: #1e293b;">New Support Ticket Received</p>

            <h3 style="color: #334155; font-size: 16px; margin-top: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">Ticket Details</h3>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #475569; width: 160px; vertical-align: top;">Ticket Number</td>
                    <td style="padding: 8px 0; color: #0f172a;">{{ $ticketNumber }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #475569; vertical-align: top;">Date Submitted</td>
                    <td style="padding: 8px 0; color: #0f172a;">{{ $dateStr }}</td>
                </tr>
            </table>

            <h3 style="color: #334155; font-size: 16px; margin-top: 20px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;">User Information</h3>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #475569; width: 160px; vertical-align: top;">Name</td>
                    <td style="padding: 8px 0; color: #0f172a;">{{ $user->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #475569; vertical-align: top;">Email</td>
                    <td style="padding: 8px 0; color: #0f172a;">{{ $user->email ?? '—' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; color: #475569; vertical-align: top;">Category</td>
                    <td style="padding: 8px 0; color: #0f172a;">{{ $category }}</td>
                </tr>
            </table>

            <p style="font-weight: bold; color: #475569; margin-bottom: 8px;">Message</p>
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; color: #0f172a; white-space: pre-wrap;">{{ $ticket->message }}</div>

            <p style="margin-top: 24px; font-size: 13px; color: #64748b;">Reply using the customer email (Reply-To) to continue the conversation in your help desk.</p>
        </div>

        <div style="text-align: center; margin-top: 20px; color: #6b7280; font-size: 12px;">
            <p>This is an automated message from CayMark.</p>
            <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
