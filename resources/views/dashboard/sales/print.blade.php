<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Invoice #{{ $sale->id }}</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 10px;
                font-family: Arial, sans-serif;
                font-size: 12px;
                width: 80mm; /* Standard thermal paper width */
            }
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .invoice-header h2 {
            margin: 5px 0;
        }
        .invoice-header .company-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            padding: 5px 0;
            letter-spacing: 2px;
        }
        .invoice-header .invoice-title {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            padding: 5px 0;
        }
        .invoice-details {
            margin-bottom: 10px;
            font-size: 12px;
        }
        .customer-details {
            margin-bottom: 10px;
            font-size: 12px;
        }
        .customer-details h3 {
            margin: 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 12px;
        }
        th, td {
            padding: 4px;
            text-align: left;
            border-bottom: 1px dotted #ddd;
        }
        th {
            background-color: #f8f9fa;
        }
        .totals {
            width: 100%;
            margin-top: 10px;
        }
        .totals table {
            margin-top: 10px;
            float: right;
            width: 100%;
        }
        .totals td {
            padding: 2px 4px;
        }
        .print-button {
            margin-bottom: 10px;
        }
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()">Print Invoice</button>
    </div>

    <div class="invoice-header">
        <div class="company-name">MEHMOOD TRADERS</div>
        <div class="invoice-title">SALES INVOICE</div>
    </div>

    <div class="invoice-details">
        <strong>Invoice #:</strong> {{ $sale->id }}<br>
        <strong>Date:</strong> {{ $sale->created_at->format('Y-m-d H:i') }}
    </div>

    <div class="customer-details">
        <h3>Customer Information</h3>
        <strong>Name:</strong> {{ $sale->customer->name }}<br>
        <strong>Phone:</strong> {{ $sale->customer->phone_no }}<br>
        <strong>Address:</strong> {{ $sale->customer->address }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Dis</th>
                <th>Tot</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->saleItems as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price, 2) }}</td>
                <td>{{ number_format($item->discount, 2) }}</td>
                <td>{{ number_format(($item->price - $item->discount) * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>


    <div class="totals">
        <table>
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td align="right">{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Discount:</strong></td>
                <td align="right">{{ number_format($sale->discount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Net Total:</strong></td>
                <td align="right">{{ number_format($sale->net_total, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Amount Paid:</strong></td>
                <td align="right">{{ number_format($sale->amount_paid, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Balance Due:</strong></td>
                <td align="right">{{ number_format($sale->pending_amount, 2) }}</td>
            </tr>
            @if($sale->customer->name != 'Counter sale')
            <tr>
                <td><strong>Last Pending:</strong></td>
                <td align="right">{{ number_format($sale->customer->account->pending_balance - $sale->pending_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Pending:</strong></td>
                <td align="right">{{ number_format($sale->customer->account->pending_balance, 2) }}</td>
            </tr>
            @endif
        </table>
    </div>

    </div>
</body>
</html>