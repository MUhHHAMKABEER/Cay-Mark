<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Auction Invoice - CayMark</title>
    <style>
        @page {
            margin: 50px;
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
        }
        .invoice-details {
            margin: 30px 0;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .invoice-details td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }
        .invoice-details td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .financial-section {
            margin: 30px 0;
            background: #f9fafb;
            padding: 20px;
            border-radius: 5px;
        }
        .financial-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .financial-section td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .financial-section .label {
            font-weight: bold;
            width: 70%;
        }
        .financial-section .amount {
            text-align: right;
            width: 30%;
        }
        .total-due {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #2563eb;
            padding-top: 10px;
        }
        .payment-instructions {
            margin-top: 40px;
            padding: 20px;
            background: #eff6ff;
            border-left: 4px solid #2563eb;
            border-radius: 5px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">CayMark</div>
        <div style="color: #64748b; font-size: 14px;">Island Exchange & Auction House</div>
        <div class="invoice-title">Auction Invoice</div>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td>Buyer Name:</td>
                <td>{{ $buyer->name }}</td>
            </tr>
            <tr>
                <td>Seller Name:</td>
                <td>{{ $seller->name }}</td>
            </tr>
            <tr>
                <td>Sale Date:</td>
                <td>{{ $invoice->sale_date->format('F d, Y') }}</td>
            </tr>
            <tr>
                <td>Item Name:</td>
                <td>{{ $invoice->item_name }}</td>
            </tr>
            <tr>
                <td>Item ID:</td>
                <td>{{ $invoice->item_id }}</td>
            </tr>
            <tr>
                <td>Invoice Number:</td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
        </table>
    </div>

    <div class="financial-section">
        <table>
            <tr>
                <td class="label">Winning Bid Amount:</td>
                <td class="amount">${{ number_format($invoice->winning_bid_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Buyer Fees:</td>
                <td class="amount">${{ number_format($invoice->buyer_commission, 2) }}</td>
            </tr>
            <tr class="total-due">
                <td class="label">Total Amount Due:</td>
                <td class="amount">${{ number_format($invoice->total_amount_due, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="payment-instructions">
        <strong>Payment Instructions:</strong><br>
        Please log in to your CayMark dashboard to submit payment.
    </div>

    <div class="footer">
        <p>This is an official invoice from CayMark.</p>
        <p>For questions, please contact support through your dashboard.</p>
        <p>&copy; {{ date('Y') }} CayMark. All rights reserved.</p>
    </div>
</body>
</html>
