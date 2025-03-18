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
                padding: 20px;
                font-family: Arial, sans-serif;
            }
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .customer-details {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        .totals {
            float: right;
            width: 300px;
        }
        .totals table {
            margin-top: 20px;
        }
        .print-button {
            margin-bottom: 20px;
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
        <h2>MEHMOOD TRADERS</h2>
        <h2>SALES INVOICE</h2>
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
                <th>Quantity</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Total Discount</th>
                <th>Total</th>
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
                <td>{{ number_format($item->discount * $item->quantity, 2) }}</td>
                <td>{{ number_format(($item->price - $item->discount) * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td>{{ number_format($sale->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Discount:</strong></td>
                <td>{{ number_format($sale->discount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Tax:</strong></td>
                <td>{{ number_format($sale->tax, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Net Total:</strong></td>
                <td>{{ number_format($sale->net_total, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Amount Paid:</strong></td>
                <td>{{ number_format($sale->amount_paid, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Balance Due:</strong></td>
                <td>{{ number_format($sale->pending_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total Pending Amount:</strong></td>
                <td>{{ number_format($sale->customer->account->pending_balance, 2) }}</td>
            </tr>
        </table>
    </div>
</body>
</html>