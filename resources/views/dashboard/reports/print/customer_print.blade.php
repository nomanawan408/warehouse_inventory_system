<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Report: {{ $customer->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        /* Modern styling */
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #1abc9c;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #3498db;
        }

        body {
            font-family: 'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .report-container {
            max-width: 1140px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
        }

        .summary-card {
            height: 100%;
            border-radius: 0.5rem;
            border: none;
            overflow: hidden;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .summary-card .card-body {
            padding: 1.5rem;
        }

        .summary-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .summary-label {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .table th {
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
            border-top: none;
        }

        .table td {
            vertical-align: middle;
        }

        /* Custom badges */
        .badge-soft-success {
            background-color: rgba(46, 204, 113, 0.15);
            color: #2ecc71;
        }

        .badge-soft-danger {
            background-color: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
        }

        .badge-soft-warning {
            background-color: rgba(243, 156, 18, 0.15);
            color: #f39c12;
        }

        .badge-soft-info {
            background-color: rgba(52, 152, 219, 0.15);
            color: #3498db;
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
                padding: 0 !important;
                box-shadow: none !important;
                background-color: white !important;
            }
            .print-button {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
                margin-bottom: 0.5cm !important;
            }
            .card-header {
                background-color: #f8f9fa !important;
                color: #212529 !important;
                border-bottom: 1px solid #dee2e6 !important;
                padding: 0.3cm 0.5cm !important;
            }
            .card-body {
                padding: 0.3cm 0.5cm !important;
            }
            .card-title {
                font-size: 12pt !important;
                margin-bottom: 0 !important;
            }
            h1 {
                font-size: 16pt !important;
                margin-bottom: 0.2cm !important;
            }
            h2 {
                font-size: 14pt !important;
            }
            h3 {
                font-size: 12pt !important;
            }
            p.lead {
                font-size: 10pt !important;
                margin-bottom: 0.2cm !important;
            }
            .table {
                font-size: 9pt !important;
                width: 100% !important;
                margin-bottom: 0.3cm !important;
            }
            .table th, .table td {
                padding: 0.2cm !important;
            }
            .badge {
                font-size: 8pt !important;
                padding: 0.1cm 0.2cm !important;
            }
            .signature-section {
                margin-top: 1cm !important;
                display: flex !important;
                justify-content: space-between !important;
                page-break-inside: avoid !important;
            }
            .signature-box {
                width: 45% !important;
                border-top: 1px solid #333 !important;
                padding-top: 0.2cm !important;
                text-align: center !important;
                font-size: 9pt !important;
            }
            .row {
                margin: 0 !important;
            }
            .report-header {
                margin-bottom: 0.5cm !important;
                border-bottom: 1px solid #dee2e6 !important;
                padding-bottom: 0.3cm !important;
            }
            /* Ensure no page breaks inside elements */
            tr, .card-header, .card-body {
                page-break-inside: avoid !important;
            }
            /* Force specific page breaks */
            .page-break-after {
                page-break-after: always !important;
            }
        }
        body {
            background-color: #f8f9fa;
        }
        .report-container {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .report-header {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            border-top: 1px solid #333;
            padding-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body class="bg-light">
    <div class="report-container p-4">
        <button class="btn btn-success mb-4 print-button" onclick="window.print()">Print Report</button>
        
        <div class="report-header text-center">
            <h1 class="display-5 fw-bold">Customer Sales Report</h1>
            <p class="lead text-muted">
                Period: {{ $startDate->format('d M, Y') }} - {{ $endDate->format('d M, Y') }}
            </p>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Customer Information</h3>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Customer Name:</div>
                    <div class="col-md-9">{{ $customer->name }}</div>
                </div>
                <!-- <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Email:</div>
                    <div class="col-md-9">{{ $customer->email }}</div>
                </div> -->
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Phone:</div>
                    <div class="col-md-9">{{ $customer->phone_no }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3 fw-bold">Address:</div>
                    <div class="col-md-9">{{ $customer->address }}</div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0">Summary</h3>
            </div>
            <div class="card-body bg-light">
                <div class="row text-center">
                    <div class="col mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Total Sales</h5>
                                <h2 class="card-text">{{ $totalSales }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Total Amount</h5>
                                <h2 class="card-text">Rs. {{ number_format($totalAmount, 2) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Total Discount</h5>
                                <h2 class="card-text">Rs. {{ number_format($totalDiscount, 2) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col mb-3">
                        <div class="card h-100">
                            <div class="card-body">
        </div>
        
        <!-- Customer Information Card -->
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 bg-primary bg-opacity-10 me-3 d-flex justify-content-center align-items-center" style="width: 42px; height: 42px">
                        <i class="fas fa-info-circle text-primary"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $customer->name }}</h4>
                        <p class="text-muted small mb-0">Report generated on {{ now()->format('d M, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="p-4 bg-light rounded-3">
                            <h6 class="text-uppercase text-secondary small fw-bold mb-3">Customer Details</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150" class="text-muted">Name:</th>
                                    <td class="fw-medium">{{ $customer->name }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Email:</th>
                                    <td>{{ $customer->email }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Phone:</th>
                                    <td>{{ $customer->phone }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Address:</th>
                                    <td>{{ $customer->address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="p-4 bg-light rounded-3">
                            <h6 class="text-uppercase text-secondary small fw-bold mb-3">Sales Summary</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150" class="text-muted">Total Sales:</th>
                                    <td class="fw-medium">{{ $totalSales }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Total Amount:</th>
                                    <td>Rs. {{ number_format($totalAmount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Total Discount:</th>
                                    <td>Rs. {{ number_format($totalDiscount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Net Amount:</th>
                                    <td>Rs. {{ number_format($totalNetAmount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Pending Amount:</th>
                                    <td>Rs. {{ number_format($totalPending, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sales History Table -->
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 bg-primary bg-opacity-10 me-3 d-flex justify-content-center align-items-center" style="width: 42px; height: 42px">
                        <i class="fas fa-table text-primary"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Sales History</h4>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Invoice #</th>
                                <th>Date</th>
                                <th>Total Items</th>
                                <th>Amount</th>
                                <th>Discount</th>
                                <th>Net Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                            <tr>
                                <td>#{{ $sale->id }}</td>
                                <td>{{ $sale->created_at->format('d M, Y') }}</td>
                                <td>{{ $sale->items->count() }}</td>
                                <td>Rs. {{ number_format($sale->total_amount, 2) }}</td>
                                <td>Rs. {{ number_format($sale->discount_amount, 2) }}</td>
                                <td>Rs. {{ number_format($sale->net_amount, 2) }}</td>
                                <td>
                                    @if($sale->pending_amount > 0)
                                        <span class="badge bg-danger">Pending</span>
                                    @else
                                        <span class="badge bg-success">Paid</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Most Purchased Products Table -->
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 bg-primary bg-opacity-10 me-3 d-flex justify-content-center align-items-center" style="width: 42px; height: 42px">
                        <i class="fas fa-chart-bar text-primary"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Most Purchased Products</h4>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mostPurchasedProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->total_quantity }}</td>
                                <td>Rs. {{ number_format($product->total_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Monthly Sales Trend Table -->
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 bg-primary bg-opacity-10 me-3 d-flex justify-content-center align-items-center" style="width: 42px; height: 42px">
                        <i class="fas fa-chart-line text-primary"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Monthly Sales Trend</h4>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Month</th>
                                <th>Number of Sales</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthlyTrend as $trend)
                            <tr>
                                <td>{{ $trend->month }}</td>
                                <td>{{ $trend->sale_count }}</td>
                                <td>Rs. {{ number_format($trend->total_amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div>Prepared By</div>
                <div>{{ auth()->user()->name }}</div>
            </div>
            <div class="signature-box">
                <div>Authorized Signature</div>
            </div>
        </div>
        
        <!-- Report Footer -->
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
        
        function exportToPDF() {
            const element = document.querySelector('.report-container');
            const opt = {
                margin: [10, 10, 10, 10],
                filename: 'customer_report_{{ $customer->name }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            html2pdf().set(opt).from(element).save();
        }
        
        // Auto-print when the page loads (only when directly accessing the print view)
        window.onload = function() {
            // Check if the URL contains "print=true" parameter
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('auto_print') === 'true') {
                window.print();
            }
        };
    </script>
</body>
</html>
