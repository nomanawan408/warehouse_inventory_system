<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Report: {{ $customer->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        .header h1 {
            font-size: 20pt;
            margin-bottom: 5px;
            color: #2563eb;
        }
        .header p {
            font-size: 10pt;
            color: #666;
            margin: 0;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .info-box h2 {
            font-size: 14pt;
            margin-top: 0;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
            color: #1f2937;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            padding: 5px 0;
            color: #6b7280;
        }
        .info-value {
            display: table-cell;
            width: 70%;
            padding: 5px 0;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10pt;
        }
        table.data-table th {
            background-color: #f8f9fa;
            color: #374151;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border-bottom: 2px solid #ddd;
        }
        table.data-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-danger {
            color: #dc2626;
        }
        .text-success {
            color: #059669;
        }
        .text-bold {
            font-weight: bold;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9pt;
            color: #9ca3af;
            padding: 10px 0;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Customer Report</h1>
            <p>{{ $customer->name }} | {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
        </div>
        
        <div class="info-section">
            <div class="info-box">
                <h2>Customer Information</h2>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Name:</div>
                        <div class="info-value">{{ $customer->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone:</div>
                        <div class="info-value">{{ $customer->phone_no ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Address:</div>
                        <div class="info-value">{{ $customer->address ?? 'N/A' }}</div>
                    </div>
                </div>
            </div>
            
            <div class="info-box">
                <h2>Report Summary</h2>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Report Period:</div>
                        <div class="info-value">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Total Invoices:</div>
                        <div class="info-value">{{ $totalSales }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Total Amount:</div>
                        <div class="info-value">Rs. {{ number_format($totalAmount, 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Total Discount:</div>
                        <div class="info-value">Rs. {{ number_format($totalDiscount, 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Net Amount:</div>
                        <div class="info-value text-bold">Rs. {{ number_format($totalNetAmount, 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Pending Amount:</div>
                        <div class="info-value {{ $totalPending > 0 ? 'text-danger' : 'text-success' }} text-bold">
                            Rs. {{ number_format($totalPending, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-box">
                <h2>Most Purchased Products</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mostPurchasedProducts as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td class="text-right">{{ $product->total_quantity }}</td>
                            <td class="text-right">Rs. {{ number_format($product->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No product data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-box">
                <h2>Monthly Purchase Trend</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-right">Number of Invoices</th>
                            <th class="text-right">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($monthlyTrend as $trend)
                        <tr>
                            <td>{{ $trend->month }}</td>
                            <td class="text-right">{{ $trend->sale_count }}</td>
                            <td class="text-right">Rs. {{ number_format($trend->total_amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No trend data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-box">
                <h2>Invoice Details</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Net Amount</th>
                            <th class="text-right">Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->created_at->format('d M Y') }}</td>
                            <td class="text-right">Rs. {{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-right">Rs. {{ number_format($sale->discount_amount, 2) }}</td>
                            <td class="text-right">Rs. {{ number_format($sale->net_amount, 2) }}</td>
                            <td class="text-right {{ $sale->pending_amount > 0 ? 'text-danger' : 'text-success' }}">
                                Rs. {{ number_format($sale->pending_amount, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No invoice data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Totals</th>
                            <th class="text-right">Rs. {{ number_format($totalAmount, 2) }}</th>
                            <th class="text-right">Rs. {{ number_format($totalDiscount, 2) }}</th>
                            <th class="text-right">Rs. {{ number_format($totalNetAmount, 2) }}</th>
                            <th class="text-right {{ $totalPending > 0 ? 'text-danger' : 'text-success' }}">
                                Rs. {{ number_format($totalPending, 2) }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p>Generated on {{ now()->format('d M Y, h:i A') }} | Warehouse Inventory System</p>
    </div>
</body>
</html>
