@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-user-circle me-2"></i>Customer Report: {{ $customer->name }}</h5>
                    <div>
                        <button onclick="window.print()" class="btn btn-light btn-sm shadow-sm">
                            <i class="ti ti-printer me-1"></i> Print Report
                        </button>
                        <a href="{{ route('reports.index') }}" class="btn btn-light btn-sm ms-2 shadow-sm">
                            <i class="ti ti-arrow-left me-1"></i> Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100 border-0">
                                <div class="card-body">
                                    <h6 class="card-title text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                                        <i class="ti ti-id-badge me-2"></i>Customer Information
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="30%" class="text-muted">Name:</th>
                                            <td class="fw-medium">{{ $customer->name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Phone:</th>
                                            <td class="fw-medium">{{ $customer->phone_no }}</td>
                                        </tr>
                                        <!-- <tr>
                                            <th class="text-muted">Email:</th>
                                            <td class="fw-medium">{{ $customer->email ?? 'N/A' }}</td>
                                        </tr> -->
                                        <tr>
                                            <th class="text-muted">Address:</th>
                                            <td class="fw-medium">{{ $customer->address ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm h-100 border-0">
                                <div class="card-body">
                                    <h6 class="card-title text-uppercase text-muted fw-bold mb-3 border-bottom pb-2">
                                        <i class="ti ti-report-analytics me-2"></i>Report Summary
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="40%" class="text-muted">Report Period:</th>
                                            <td class="fw-medium">{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Total Invoices:</th>
                                            <td class="fw-medium">{{ $totalSales }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Total Amount:</th>
                                            <td class="fw-medium">Rs. {{ number_format($totalAmount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Total Discount:</th>
                                            <td class="fw-medium">Rs. {{ number_format($totalDiscount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Net Amount:</th>
                                            <td class="fw-bold text-dark">Rs. {{ number_format($totalNetAmount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Pending Amount:</th>
                                            <td class="fw-bold {{ $totalPending > 0 ? 'text-danger' : 'text-success' }}">
                                                Rs. {{ number_format($totalPending, 2) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light py-3">
                                    <h6 class="card-title mb-0 fw-bold">
                                        <i class="ti ti-shopping-cart me-2"></i>Most Purchased Products
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="py-2">#</th>
                                                    <th class="py-2">Product</th>
                                                    <th class="py-2 text-center">Quantity</th>
                                                    <th class="py-2 text-end">Total Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topProducts as $index => $product)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $product->name }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary rounded-pill">{{ $product->total_quantity }}</span>
                                                    </td>
                                                    <td class="text-end fw-medium">Rs. {{ number_format($product->total_amount, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light py-3">
                                    <h6 class="card-title mb-0 fw-bold">
                                        <i class="ti ti-chart-line me-2"></i>Monthly Purchase Trend
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyTrendChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light py-3">
                                    <h6 class="card-title mb-0 fw-bold">
                                        <i class="ti ti-receipt me-2"></i>Invoices
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="py-2">Invoice #</th>
                                                    <th class="py-2">Date</th>
                                                    <th class="py-2 text-center">Items</th>
                                                    <th class="py-2 text-end">Total</th>
                                                    <th class="py-2 text-end">Discount</th>
                                                    <th class="py-2 text-end">Net Amount</th>
                                                    <th class="py-2 text-end">Paid</th>
                                                    <th class="py-2 text-end">Pending</th>
                                                    <th class="py-2 text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sales as $sale)
                                                <tr>
                                                    <td class="fw-medium">INV-{{ $sale->id }}</td>
                                                    <td>{{ $sale->created_at->format('d M Y') }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-secondary rounded-pill">{{ $sale->items->count() }}</span>
                                                    </td>
                                                    <td class="text-end">Rs. {{ number_format($sale->total_amount, 2) }}</td>
                                                    <td class="text-end">Rs. {{ number_format($sale->discount, 2) }}</td>
                                                    <td class="text-end fw-medium">Rs. {{ number_format($sale->net_total, 2) }}</td>
                                                    <td class="text-end text-success">Rs. {{ number_format($sale->amount_paid, 2) }}</td>
                                                    <td class="text-end {{ $sale->pending_amount > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                                        Rs. {{ number_format($sale->pending_amount, 2) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-primary" title="View">
                                                            <i class="ti ti-eye"></i>
                                                        </a>
                                                        <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="btn btn-sm btn-secondary ms-1" title="Print">
                                                            <i class="ti ti-printer"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light text-center text-muted py-3">
                    <small>Generated on {{ now()->format('d M Y, h:i A') }} | Warehouse Inventory System</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Format the data for Chart.js
        const months = @json($monthlyTrend->pluck('month'));
        const salesData = @json($monthlyTrend->pluck('total_amount'));
        const saleCountData = @json($monthlyTrend->pluck('sale_count'));
        
        // Create the monthly trend chart
        const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
        const monthlyTrendChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Total Sales (Rs.)',
                        data: salesData,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Number of Invoices',
                        data: saleCountData,
                        backgroundColor: 'rgba(236, 72, 153, 0.5)',
                        borderColor: 'rgb(236, 72, 153)',
                        borderWidth: 1,
                        type: 'line',
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        usePointStyle: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    if (context.datasetIndex === 0) {
                                        label += 'Rs. ' + new Intl.NumberFormat('en-US').format(context.parsed.y);
                                    } else {
                                        label += context.parsed.y;
                                    }
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rs. ' + value;
                            }
                        },
                        title: {
                            display: true,
                            text: 'Sales Amount (Rs.)',
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false
                        },
                        title: {
                            display: true,
                            text: 'Number of Invoices',
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        title: {
                            display: true,
                            text: 'Month',
                            font: {
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<style>
    @page {
        size: A4;
        margin: 10mm 15mm;
    }

    @media print {
        body {
            background-color: #fff !important;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
        
        .btn, nav, footer, .no-print {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .card-body {
            padding: 0.5rem !important;
        }
        
        .card-header {
            background-color: #f8f9fa !important;
            color: #000 !important;
            border-bottom: 1px solid #ddd !important;
            padding: 0.5rem !important;
            font-size: 12pt;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        
        .table th, .table td {
            padding: 3px 5px;
            border-bottom: 1px solid #ddd;
        }
        
        .row {
            margin-bottom: 0.5rem !important;
        }
        
        h5, h6 {
            font-size: 11pt !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .container-fluid {
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .badge {
            border: 1px solid #333;
            padding: 1px 5px;
            border-radius: 10px;
            color: #333 !important;
            background-color: transparent !important;
            font-size: 8pt;
        }
        
        canvas {
            max-height: 200px !important;
        }
        
        .text-danger {
            color: #dc3545 !important;
        }
        
        .text-success {
            color: #28a745 !important;
        }
        
        .card-footer {
            margin-top: 10px;
            text-align: center;
            font-size: 8pt;
            color: #6c757d;
            padding: 0.25rem !important;
            border-top: 1px solid #ddd;
        }

        /* Show only the important columns in the detailed invoice table */
        .invoice-detail-table th:nth-child(9),
        .invoice-detail-table td:nth-child(9) {
            display: none !important;
        }

        /* Optimize table spacing */
        .table th {
            font-weight: bold;
            font-size: 8pt;
        }
        
        .table td {
            font-size: 8pt;
        }
    }
    
    .table th {
        font-weight: 600;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(to right, #4b6cb7, #182848);
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .shadow-hover:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection
