<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $sale->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.total td {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
            .invoice-box {
                box-shadow: none;
                border: 0;
            }
        }
        .button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button class="button" onclick="window.print()">Print Invoice</button>
        <a href="{{ route('sales.index') }}" class="button" style="text-decoration: none; background: #666;">Back to Sales</a>
    </div>

    <div class="invoice-box">
        <table>
            <tr>
                <td colspan="2">
                    <h2 style="margin: 0;">INVOICE</h2>
                    <p>Invoice #: {{ $sale->id }}<br>
                    Date: {{ $sale->created_at->format('M d, Y') }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding-top: 20px; width: 50%;">
                    <strong>From:</strong><br>
                    Your Company Name<br>
                    123 Business Street<br>
                    City, Country<br>
                    Phone: +1234567890
                </td>
                <td style="padding-top: 20px; width: 50%;">
                    <strong>Bill To:</strong><br>
                    {{ $sale->customer->name }}<br>
                    {{ $sale->customer->business_name }}<br>
                    Phone: {{ $sale->customer->phone_no }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table style="margin-top: 20px;">
                        <tr class="heading">
                            <td>Item</td>
                            <td>Quantity</td>
                            <td>Unit Price</td>
                            <td>Discount</td>
                            <td>Total</td>
                        </tr>
                        @foreach($sale->saleItems as $item)
                        <tr class="item">
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>Rs. {{ number_format($item->price, 2) }}</td>
                            <td>Rs. {{ number_format($item->discount, 2) }}</td>
                            <td>Rs. {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="total">
                            <td colspan="3"></td>
                            <td>Subtotal:</td>
                            <td>Rs. {{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                        <tr class="total">
                            <td colspan="3"></td>
                            <td>Discount:</td>
                            <td>Rs. {{ number_format($sale->discount, 2) }}</td>
                        </tr>
                        <tr class="total">
                            <td colspan="3"></td>
                            <td>Net Total:</td>
                            <td>Rs. {{ number_format($sale->net_total, 2) }}</td>
                        </tr>
                        <tr class="total">
                            <td colspan="3"></td>
                            <td>Amount Paid:</td>
                            <td>Rs. {{ number_format($sale->amount_paid, 2) }}</td>
                        </tr>
                        <tr class="total">
                            <td colspan="3"></td>
                            <td>Balance Due:</td>
                            <td>Rs. {{ number_format($sale->pending_amount, 2) }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>