<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Report: {{ $company->name }}</title>
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
            width: 40%;
            font-weight: bold;
            padding: 5px 0;
            color: #6b7280;
        }
        .info-value {
            display: table-cell;
            width: 60%;
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
        .metric-badge {
            display: inline-block;
            background-color: #f3f4f6;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 10pt;
        }
        .low-stock {
            background-color: #fee2e2;
            color: #dc2626;
        }
        .medium-stock {
            background-color: #fef3c7;
            color: #d97706;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Company Report</h1>
            <p>{{ $company->name }} | {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
        </div>
        
        <div class="info-section">
            <div class="info-box">
                <h2>Company Information</h2>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Name:</div>
                        <div class="info-value">{{ $company->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone:</div>
                        <div class="info-value">{{ $company->phone_no ?? 'N/A' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Total Products:</div>
                        <div class="info-value">{{ $inventoryStatus->count() }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Inventory Value:</div>
                        <div class="info-value">Rs. {{ number_format($totalInventoryValue, 2) }}</div>
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
                        <div class="info-label">Total Quantity Sold:</div>
                        <div class="info-value">{{ number_format($totalQuantitySold) }} units</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Total Revenue:</div>
                        <div class="info-value">Rs. {{ number_format($totalRevenue, 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Total Profit:</div>
                        <div class="info-value">Rs. {{ number_format($totalProfit, 2) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Profit Margin:</div>
                        <div class="info-value {{ $profitMargin > 30 ? 'text-success' : ($profitMargin > 15 ? '' : 'text-danger') }} text-bold">
                            {{ number_format($profitMargin, 2) }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-box">
                <h2>Top Selling Products</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($topSellingProducts as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td class="text-right">{{ $product->total_quantity }}</td>
                            <td class="text-right">Rs. {{ number_format($product->total_amount, 2) }}</td>
                            <td class="text-right">Rs. {{ number_format($product->total_profit, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No product data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="info-section">
            <div class="info-box">
                <h2>Current Inventory Status</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-right">Stock</th>
                            <th class="text-right">Purchase Price</th>
                            <th class="text-right">Sale Price</th>
                            <th class="text-right">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($inventoryStatus as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td class="text-right">
                                @if($product->quantity <= 5)
                                <span class="metric-badge low-stock">{{ $product->quantity }}</span>
                                @elseif($product->quantity <= 15)
                                <span class="metric-badge medium-stock">{{ $product->quantity }}</span>
                                @else
                                <span class="metric-badge">{{ $product->quantity }}</span>
                                @endif
                            </td>
                            <td class="text-right">Rs. {{ number_format($product->purchase_price, 2) }}</td>
                            <td class="text-right">Rs. {{ number_format($product->sale_price, 2) }}</td>
                            <td class="text-right">Rs. {{ number_format($product->quantity * $product->purchase_price, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No inventory data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total Inventory Value:</th>
                            <th class="text-right">Rs. {{ number_format($totalInventoryValue, 2) }}</th>
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
