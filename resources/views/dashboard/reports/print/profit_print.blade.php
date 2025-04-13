<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Performance Report: {{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0.5cm;
            }
            body {
                font-size: 10pt;
                background-color: white !important;
            }
            .report-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 1cm !important;
                box-shadow: none !important;
                background-color: white !important;
            }
            .action-buttons {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                margin-bottom: 0.5cm !important;
            }
            .card-header {
                background-color: #f8f9fc !important;
                color: #333 !important;
                border-bottom: 1px solid #ddd !important;
            }
            .table {
                font-size: 9pt !important;
            }
            .table-dark th {
                background-color: #4e73df !important;
                color: white !important;
            }
            .badge {
                padding: 0.2em 0.4em !important;
                border: 1px solid #ddd !important;
            }
            .bg-success {
                background-color: white !important;
                color: #1cc88a !important;
                border-color: #1cc88a !important;
            }
            .bg-danger {
                background-color: white !important;
                color: #e74a3b !important;
                border-color: #e74a3b !important;
            }
            .text-primary {
                color: #4e73df !important;
            }
            .signature-section {
                margin-top: 2cm;
                display: flex;
                justify-content: space-between;
            }
            .signature-box {
                border-top: 1px solid #333;
                padding-top: 0.5cm;
                width: 45%;
                text-align: center;
            }
        }

        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #1abc9c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #3498db;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            background-color: #f5f7fa;
        }
        
        .report-container {
            max-width: 1200px;
            margin: 2cm auto;
            padding: 2.5cm;
            background-color: white;
            box-shadow: 0 0.5rem 1.5rem 0 rgba(22, 28, 45, 0.1);
        }
        
        .report-header {
            margin-bottom: 2cm;
            text-align: left;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .report-branding {
            display: flex;
            align-items: center;
        }
        
        .company-logo {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--primary-color);
            color: white;
            font-size: 24px;
            border-radius: 12px;
            margin-right: 20px;
        }
        
        .company-text {
            display: flex;
            flex-direction: column;
        }
        
        .company-name {
            font-size: 18pt;
            font-weight: 600;
            margin: 0;
            color: var(--primary-color);
        }
        
        .company-tagline {
            font-size: 10pt;
            color: #828890;
            margin: 0;
        }
        }
        
        .report-title {
            font-size: 24pt;
            font-weight: 700;
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .report-subtitle {
            font-size: 12pt;
            color: #505a66;
            font-weight: 400;
        }
        
        .report-meta {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            text-align: right;
            background-color: rgba(236, 240, 241, 0.5);
            padding: 1rem;
            border-radius: 8px;
        }
        
        .meta-item {
            display: flex;
            justify-content: space-between;
            gap: 1.5rem;
        }
        
        .meta-label {
            font-size: 10pt;
            color: #7b8a8b;
            font-weight: 600;
        }
        
        .meta-value {
            font-size: 10pt;
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .table {
            width: 100%;
            margin-bottom: 1cm;
            border-collapse: collapse;
        }
        
        .table th, .table td {
            padding: 0.5rem;
            border: 1px solid #e3e6f0;
        }
        
        .table th {
            background-color: #4e73df;
            color: white;
            font-weight: bold;
            text-align: left;
        }
        
        .table tr:nth-child(even) {
            background-color: #f8f9fc;
        }
        
        .badge {
            padding: 0.25em 0.4em;
            font-weight: bold;
            border-radius: 0.25rem;
        }
        
        .bg-success {
            background-color: #1cc88a;
            color: white;
        }
        
        .bg-danger {
            background-color: #e74a3b;
            color: white;
        }
        
        .card {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.25rem;
            font-weight: bold;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        .signature-section {
            margin-top: 3cm;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            border-top: 1px solid #333;
            padding-top: 0.5cm;
            width: 45%;
            text-align: center;
        }
        
        .action-buttons {
            position: fixed;
            top: 1cm;
            right: 1cm;
            display: flex;
            gap: 0.5rem;
            z-index: 1000;
        }
        
        .print-button, .export-button, .back-button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .print-button {
            background-color: var(--primary-color);
            color: white;
        }
        
        .print-button:hover {
            background-color: var(--dark-color);
        }
        
        .export-button {
            background-color: var(--success-color);
            color: white;
        }
        
        .export-button:hover {
            background-color: #27ae60;
        }
        
        .back-button {
            background-color: var(--light-color);
            color: var(--dark-color);
            text-decoration: none;
        }
        
        .back-button:hover {
            background-color: #d7d7d7;
        }
        
        .summary-card {
            text-align: center;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 0.35rem;
            border: 1px solid #dddfeb;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        /* Modern Cards Styling */
        .card-modern {
            color: white;
            border-radius: 12px;
            padding: 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .card-modern-content {
            flex-grow: 1;
            padding-bottom: 10px;
        }
        
        .card-modern-footer {
            display: flex;
            flex-direction: column;
            padding-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .card-modern-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            line-height: 1.2;
        }
        
        .card-modern-label {
            opacity: 0.9;
            margin-bottom: 0;
            font-size: 14px;
        }
        
        /* Card Colors */
        .revenue-card {
            background-color: #1e88e5;
            background-image: linear-gradient(135deg, #1e88e5 0%, #0d47a1 100%);
        }
        
        .profit-card {
            background-color: #2e7d32;
            background-image: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
        }
        
        .margin-card {
            background-color: #00bcd4;
            background-image: linear-gradient(135deg, #00bcd4 0%, #006064 100%);
        }
        
        .sales-card {
            background-color: #ffc107;
            background-image: linear-gradient(135deg, #ffc107 0%, #ff8f00 100%);
        }
        
        @media print {
            .card-modern {
                color: black !important;
                background-image: none !important;
                background-color: white !important;
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }
            
            .card-modern-footer {
                border-top: 1px solid #ddd !important;
            }
            
            .revenue-card,
            .profit-card,
            .margin-card,
            .sales-card {
                background-image: none !important;
                background-color: white !important;
            }
        }
        
        @media print {
            @page {
                size: A4;
                margin: 0.5cm;
            }
            body {
                font-size: 10pt;
                background-color: white !important;
            }
            .report-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 1cm !important;
                box-shadow: none !important;
                background-color: white !important;
            }
            .action-buttons {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                margin-bottom: 0.5cm !important;
            }
            .card-header {
                background-color: #f8f9fc !important;
                color: #333 !important;
                border-bottom: 1px solid #ddd !important;
            }
            .table {
                font-size: 9pt !important;
            }
            .table-dark th {
                background-color: #4e73df !important;
                color: white !important;
            }
            .badge {
                padding: 0.2em 0.4em !important;
                border: 1px solid #ddd !important;
            }
            .bg-success {
                background-color: white !important;
                color: #1cc88a !important;
                border-color: #1cc88a !important;
            }
            .bg-danger {
                background-color: white !important;
                color: #e74a3b !important;
                border-color: #e74a3b !important;
            }
            .text-primary {
                color: #4e73df !important;
            }
            .signature-section {
                margin-top: 2cm;
                display: flex;
                justify-content: space-between;
            }
            .signature-box {
                border-top: 1px solid #333;
                padding-top: 0.5cm;
                width: 45%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="action-buttons">
        <a href="{{ route('reports.profit') }}?start_date={{ request('start_date') }}&end_date={{ request('end_date') }}" class="back-button">
            <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
        </a>
        <button class="print-button" onclick="window.print()">
            <i class="fas fa-print me-2"></i> Print Report
        </button>
        <button class="export-button" onclick="exportToPDF()">
            <i class="fas fa-file-pdf me-2"></i> Export PDF
        </button>
    </div>
    
    <div class="report-container">
        <div class="report-header">
            <div class="report-branding">
                <div class="company-logo">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="company-text">
                    <div class="company-name">Warehouse Inventory System</div>
                    <div class="report-title">Financial Performance Report</div>
                    <div class="report-subtitle">{{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}</div>
                </div>
            </div>
            <div class="report-meta">
                <div class="meta-item">
                    <div class="meta-label">Report ID</div>
                    <div class="meta-value">FIN-{{ now()->format('Ymd') }}-{{ rand(1000, 9999) }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Generated</div>
                    <div class="meta-value">{{ now()->format('d M, Y h:i A') }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Generated By</div>
                    <div class="meta-value">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>
        
        <!-- Executive Summary -->
        <div class="executive-summary mb-5">
            <div class="section-heading d-flex align-items-center mb-3">
                <div class="section-icon me-2 bg-primary text-white">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h2 class="section-title">Executive Summary</h2>
            </div>
            <div class="summary-content p-4 bg-light rounded-3 border border-light shadow-sm">
                <p class="mb-3">
                    @if($profitMargin > 30)
                        This report presents financial performance data for the period analyzed, showing <strong>exceptional profitability</strong> with a margin of <strong class="text-success">{{ number_format($profitMargin, 2) }}%</strong>. Total revenue reached <strong>Rs. {{ number_format($totalRevenue, 2) }}</strong> with profits of <strong>Rs. {{ number_format($totalProfit, 2) }}</strong> across <strong>{{ $totalSales }}</strong> completed sales.
                    @elseif($profitMargin > 20)
                        This report presents financial performance data for the period analyzed, showing <strong>strong profitability</strong> with a margin of <strong class="text-success">{{ number_format($profitMargin, 2) }}%</strong>. Total revenue reached <strong>Rs. {{ number_format($totalRevenue, 2) }}</strong> with profits of <strong>Rs. {{ number_format($totalProfit, 2) }}</strong> across <strong>{{ $totalSales }}</strong> completed sales.
                    @elseif($profitMargin > 10)
                        This report presents financial performance data for the period analyzed, showing <strong>steady profitability</strong> with a margin of <strong>{{ number_format($profitMargin, 2) }}%</strong>. Total revenue reached <strong>Rs. {{ number_format($totalRevenue, 2) }}</strong> with profits of <strong>Rs. {{ number_format($totalProfit, 2) }}</strong> across <strong>{{ $totalSales }}</strong> completed sales.
                    @else
                        This report presents financial performance data for the period analyzed, showing <strong class="text-warning">lower than target profitability</strong> with a margin of <strong class="text-warning">{{ number_format($profitMargin, 2) }}%</strong>. Total revenue reached <strong>Rs. {{ number_format($totalRevenue, 2) }}</strong> with profits of <strong>Rs. {{ number_format($totalProfit, 2) }}</strong> across <strong>{{ $totalSales }}</strong> completed sales.
                    @endif
                </p>
                
                @if($topProfitProducts->isNotEmpty())
                    <p class="mb-0">
                        <strong>Key Performers:</strong> The top 5 products by profitability account for approximately {{ $totalProfit > 0 ? number_format(($topProfitProducts->sum('total_profit') / $totalProfit) * 100, 1) : 0 }}% of total profit, with "{{ $topProfitProducts->first()->name }}" leading at Rs. {{ number_format($topProfitProducts->first()->total_profit, 2) }}.
                    </p>
                @endif
            </div>
        </div>
        
        <!-- KPI Summary -->
        <div class="kpi-summary mb-5">
            <div class="section-heading d-flex align-items-center mb-3">
                <div class="section-icon me-2">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h2 class="section-title">Key Performance Indicators</h2>
            </div>
            
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="kpi-card h-100 bg-white shadow-sm rounded-3 overflow-hidden">
                        <div class="kpi-header d-flex align-items-center p-3 bg-primary bg-opacity-10">
                            <div class="kpi-icon rounded-circle bg-primary text-white p-2 me-2">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h4 class="kpi-title mb-0">Revenue</h4>
                        </div>
                        <div class="kpi-body p-3">
                            <div class="kpi-value text-primary fw-bold">Rs. {{ number_format($totalRevenue, 2) }}</div>
                            <div class="kpi-description text-muted small">Total revenue generated during the period</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="kpi-card h-100 bg-white shadow-sm rounded-3 overflow-hidden">
                        <div class="kpi-header d-flex align-items-center p-3 bg-success bg-opacity-10">
                            <div class="kpi-icon rounded-circle bg-success text-white p-2 me-2">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4 class="kpi-title mb-0">Profit</h4>
                        </div>
                        <div class="kpi-body p-3">
                            <div class="kpi-value text-success fw-bold">Rs. {{ number_format($totalProfit, 2) }}</div>
                            <div class="kpi-description text-muted small">Net profit earned during the period</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="kpi-card h-100 bg-white shadow-sm rounded-3 overflow-hidden">
                        <div class="kpi-header d-flex align-items-center p-3 bg-info bg-opacity-10">
                            <div class="kpi-icon rounded-circle bg-info text-white p-2 me-2">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <h4 class="kpi-title mb-0">Margin</h4>
                        </div>
                        <div class="kpi-body p-3">
                            <div class="kpi-value text-info fw-bold">{{ number_format($profitMargin, 2) }}%</div>
                            <div class="kpi-description text-muted small">Profit as percentage of revenue</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="kpi-card h-100 bg-white shadow-sm rounded-3 overflow-hidden">
                        <div class="kpi-header d-flex align-items-center p-3 bg-warning bg-opacity-10">
                            <div class="kpi-icon rounded-circle bg-warning text-white p-2 me-2">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h4 class="kpi-title mb-0">Sales</h4>
                        </div>
                        <div class="kpi-body p-3">
                            <div class="kpi-value text-warning fw-bold">{{ $totalSales }}</div>
                            <div class="kpi-description text-muted small">Total number of completed transactions</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modern KPI Cards Layout -->        
        <div class="summary-cards my-5">
            <div class="row g-4">
                <!-- Revenue Card -->                
                <div class="col-xl-3 col-md-6">
                    <div class="card-modern revenue-card">
                        <div class="card-modern-content">
                            <h4 class="card-modern-value">Rs. {{ number_format($totalRevenue, 2) }}</h4>
                            <p class="card-modern-label">Total Revenue (Period)</p>
                        </div>
                        <div class="card-modern-footer">
                            @if(isset($revenueGrowth))
                                @if($revenueGrowth > 0)
                                    <span class="text-success small"><i class="fas fa-arrow-up me-1"></i> {{ number_format(abs($revenueGrowth), 1) }}%</span>
                                @elseif($revenueGrowth < 0)
                                    <span class="text-danger small"><i class="fas fa-arrow-down me-1"></i> {{ number_format(abs($revenueGrowth), 1) }}%</span>
                                @else
                                    <span class="text-muted small"><i class="fas fa-minus me-1"></i> No change</span>
                                @endif
                                <span class="text-muted small">vs previous period</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Profit Card -->                
                <div class="col-xl-3 col-md-6">
                    <div class="card-modern profit-card">
                        <div class="card-modern-content">
                            <h4 class="card-modern-value">Rs. {{ number_format($totalProfit, 2) }}</h4>
                            <p class="card-modern-label">Total Profit (Period)</p>
                        </div>
                        <div class="card-modern-footer">
                            @if(isset($profitGrowth))
                                @if($profitGrowth > 0)
                                    <span class="text-success small"><i class="fas fa-arrow-up me-1"></i> {{ number_format(abs($profitGrowth), 1) }}%</span>
                                @elseif($profitGrowth < 0)
                                    <span class="text-danger small"><i class="fas fa-arrow-down me-1"></i> {{ number_format(abs($profitGrowth), 1) }}%</span>
                                @else
                                    <span class="text-muted small"><i class="fas fa-minus me-1"></i> No change</span>
                                @endif
                                <span class="text-muted small">vs previous period</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Margin Card -->                
                <div class="col-xl-3 col-md-6">
                    <div class="card-modern margin-card">
                        <div class="card-modern-content">
                            <h4 class="card-modern-value">{{ number_format($profitMargin, 2) }}%</h4>
                            <p class="card-modern-label">Profit Margin</p>
                        </div>
                        <div class="card-modern-footer">
                            @if(isset($marginGrowth))
                                @if($marginGrowth > 0)
                                    <span class="text-success small"><i class="fas fa-arrow-up me-1"></i> {{ number_format(abs($marginGrowth), 1) }}%</span>
                                @elseif($marginGrowth < 0)
                                    <span class="text-danger small"><i class="fas fa-arrow-down me-1"></i> {{ number_format(abs($marginGrowth), 1) }}%</span>
                                @else
                                    <span class="text-muted small"><i class="fas fa-minus me-1"></i> No change</span>
                                @endif
                                <span class="text-muted small">vs previous period</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sales Card -->                
                <div class="col-xl-3 col-md-6">
                    <div class="card-modern sales-card">
                        <div class="card-modern-content">
                            <h4 class="card-modern-value">{{ number_format($totalSales) }}</h4>
                            <p class="card-modern-label">Total Sales</p>
                        </div>
                        <div class="card-modern-footer">
                            @if(isset($salesGrowth))
                                @if($salesGrowth > 0)
                                    <span class="text-success small"><i class="fas fa-arrow-up me-1"></i> {{ number_format(abs($salesGrowth), 1) }}%</span>
                                @elseif($salesGrowth < 0)
                                    <span class="text-danger small"><i class="fas fa-arrow-down me-1"></i> {{ number_format(abs($salesGrowth), 1) }}%</span>
                                @else
                                    <span class="text-muted small"><i class="fas fa-minus me-1"></i> No change</span>
                                @endif
                                <span class="text-muted small">vs previous period</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Most Profitable Products</h3>
            </div>
            <div class="card-body bg-light">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Product</th>
                                <th>Quantity Sold</th>
                                <th>Revenue</th>
                                <th>Profit</th>
                                <th>Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProfitProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->total_quantity }}</td>
                                <td>Rs. {{ number_format($product->total_amount, 2) }}</td>
                                <td>Rs. {{ number_format($product->total_profit, 2) }}</td>
                                <td>{{ number_format($product->total_amount > 0 ? ($product->total_profit / $product->total_amount) * 100 : 0, 2) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Monthly Profit Analysis</h3>
            </div>
            <div class="card-body bg-light">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Month</th>
                                <th>Revenue</th>
                                <th>Profit</th>
                                <th>Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyProfitData as $month)
                            <tr>
                                <td>{{ $month->month }}</td>
                                <td>Rs. {{ number_format($month->revenue, 2) }}</td>
                                <td>Rs. {{ number_format($month->profit, 2) }}</td>
                                <td>{{ number_format($month->margin, 2) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Weekly Profit Analysis</h3>
            </div>
            <div class="card-body bg-light">
                <!-- Weekly Chart -->
                <div style="height: 300px; margin-bottom: 20px;">
                    <canvas id="weeklyProfitChart"></canvas>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Week</th>
                                <th>Revenue</th>
                                <th>Profit</th>
                                <th>Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weeklyProfitData as $week)
                            <tr>
                                <td>{{ $week->week_start }} to {{ $week->week_end }}</td>
                                <td>Rs. {{ number_format($week->revenue, 2) }}</td>
                                <td>Rs. {{ number_format($week->profit, 2) }}</td>
                                <td>{{ number_format($week->margin, 2) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Profit by Company</h3>
            </div>
            <div class="card-body bg-light">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Company</th>
                                <th>Items Sold</th>
                                <th>Revenue</th>
                                <th>Cost</th>
                                <th>Profit</th>
                                <th>Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($profitByCompany as $company)
                            <tr>
                                <td>{{ $company->name }}</td>
                                <td>{{ $company->items_sold }}</td>
                                <td>Rs. {{ number_format($company->revenue, 2) }}</td>
                                <td>Rs. {{ number_format($company->cost, 2) }}</td>
                                <td>Rs. {{ number_format($company->profit, 2) }}</td>
                                <td>{{ number_format($company->margin, 2) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="signature-section">
            <div class="signature-box">
                <div>Prepared By</div>
                <div>{{ auth()->user()->name }}</div>
            </div>
            <div class="signature-box">
                <div>Authorized Signature</div>
            </div>
        </div>
        
        <div class="mt-5 pt-3 text-center text-muted">
            <p>Report generated on {{ now()->format('d M, Y h:i A') }}</p>
            <p>© {{ date('Y') }} Warehouse Inventory System</p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        document.getElementById('print-report').addEventListener('click', function() {
            window.print();
        });
        
        // Initialize Chart.js charts
        document.addEventListener('DOMContentLoaded', function() {
            // Weekly Profit Chart
            var weeklyProfitCtx = document.getElementById("weeklyProfitChart");
            if (weeklyProfitCtx) {
                var weeklyProfitChart = new Chart(weeklyProfitCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($weeklyProfitData->pluck('week_start')) !!},
                        datasets: [{
                            label: "Profit",
                            lineTension: 0.4,
                            backgroundColor: "rgba(78, 115, 223, 0.05)",
                            borderColor: "rgba(78, 115, 223, 1)",
                            pointRadius: 3,
                            pointBackgroundColor: "rgba(78, 115, 223, 1)",
                            pointBorderColor: "rgba(78, 115, 223, 1)",
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: {!! json_encode($weeklyProfitData->pluck('profit')) !!},
                            fill: true,
                        }, {
                            label: "Revenue",
                            lineTension: 0.4,
                            backgroundColor: "rgba(28, 200, 138, 0.05)",
                            borderColor: "rgba(28, 200, 138, 1)",
                            pointRadius: 3,
                            pointBackgroundColor: "rgba(28, 200, 138, 1)",
                            pointBorderColor: "rgba(28, 200, 138, 1)",
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
                            pointHoverBorderColor: "rgba(28, 200, 138, 1)",
                            pointHitRadius: 10,
                            pointBorderWidth: 2,
                            data: {!! json_encode($weeklyProfitData->pluck('revenue')) !!},
                            fill: true,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    maxTicksLimit: 7
                                }
                            },
                            y: {
                                ticks: {
                                    maxTicksLimit: 5,
                                    callback: function(value) {
                                        return 'Rs. ' + value.toLocaleString();
                                    }
                                },
                                grid: {
                                    color: "rgba(234, 236, 244, 0.4)",
                                    borderDash: [2],
                                },
                                beginAtZero: true
                            },
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                backgroundColor: "rgba(255,255,255,0.9)",
                                bodyColor: "#858796",
                                titleColor: '#6e707e',
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                displayColors: false,
                                mode: 'index',
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        return label + ': Rs. ' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // If we have product profit data chart
            var productProfitCtx = document.getElementById("productProfitChart");
            if (productProfitCtx) {
                var productProfitChart = new Chart(productProfitCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($topProfitProducts->pluck('name')) !!},
                        datasets: [{
                            data: {!! json_encode($topProfitProducts->pluck('total_profit')) !!},
                            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b'],
                            hoverBorderColor: "rgba(234, 236, 244, 1)",
                        }],
                    },
                    options: {
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: "rgb(255,255,255)",
                                bodyColor: "#858796",
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.raw || 0;
                                        var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        var percentage = Math.round((value / total) * 100);
                                        return label + ': Rs. ' + value.toLocaleString() + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
        // Auto-print when the page loads (only when directly accessing the print view)
        window.onload = function() {
            // Check if the URL contains "print=true" parameter
            if (window.location.search.includes('print=true')) {
                window.print();
            }
        };
        
        // Export to PDF function
        function exportToPDF() {
            const element = document.querySelector('.report-container');
            const filename = 'Financial_Report_{{ $startDate->format("Ymd") }}_{{ $endDate->format("Ymd") }}.pdf';
            
            // Hide buttons before generating PDF
            const actionButtons = document.querySelector('.action-buttons');
            actionButtons.style.display = 'none';
            
            const opt = {
                margin: [10, 10, 10, 10],
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            // Generate PDF
            html2pdf().set(opt).from(element).save().then(function() {
                // Show buttons again after PDF is generated
                actionButtons.style.display = 'flex';
            });
        }
    </script>
</body>
</html>
