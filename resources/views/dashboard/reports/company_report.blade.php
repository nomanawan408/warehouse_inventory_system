@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-building me-2"></i>Company Report: {{ $company->name }}</h5>
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
                                        <i class="ti ti-building-store me-2"></i>Company Information
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <th width="30%" class="text-muted">Name:</th>
                                            <td class="fw-medium">{{ $company->name }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Address:</th>
                                            <td class="fw-medium">{{ $company->address ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Phone:</th>
                                            <td class="fw-medium">{{ $company->phone_no ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Total Products:</th>
                                            <td class="fw-medium">{{ $currentInventory->count() }}</td>
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
                                            <th class="text-muted">Total Quantity Sold:</th>
                                            <td class="fw-medium">{{ $totalQuantitySold }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Total Revenue:</th>
                                            <td class="fw-medium">Rs. {{ number_format($totalRevenue, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Total Profit:</th>
                                            <td class="fw-bold text-success">Rs. {{ number_format($totalProfit, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Profit Margin:</th>
                                            <td class="fw-bold text-primary">
                                                {{ $totalRevenue > 0 ? number_format(($totalProfit / $totalRevenue) * 100, 2) : 0 }}%
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
                                        <i class="ti ti-shopping-cart me-2"></i>Top Selling Products
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="py-2">#</th>
                                                    <th class="py-2">Product</th>
                                                    <th class="py-2 text-center">Quantity Sold</th>
                                                    <th class="py-2 text-end">Total Revenue</th>
                                                    <th class="py-2 text-end">Total Profit</th>
                                                    <th class="py-2 text-end">Profit Margin</th>
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
                                                    <td class="text-end fw-medium">Rs. {{ number_format($product->total_profit, 2) }}</td>
                                                    <td class="text-end">
                                                        <span class="badge bg-{{ ($product->total_amount > 0 && ($product->total_profit / $product->total_amount) * 100) > 20 ? 'success' : 'info' }} rounded-pill">
                                                            {{ $product->total_amount > 0 ? number_format(($product->total_profit / $product->total_amount) * 100, 2) : 0 }}%
                                                        </span>
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

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light py-3">
                                    <h6 class="card-title mb-0 fw-bold">
                                        <i class="ti ti-chart-line me-2"></i>Monthly Sales Trend
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
                                        <i class="ti ti-packages me-2"></i>Current Inventory
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="py-2">#</th>
                                                    <th class="py-2">Product</th>
                                                    <th class="py-2 text-center">Current Stock</th>
                                                    <th class="py-2 text-end">Purchase Price</th>
                                                    <th class="py-2 text-end">Sale Price</th>
                                                    <th class="py-2 text-end">Potential Profit</th>
                                                    <th class="py-2 text-end">Stock Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $totalStockValue = 0; $totalPotentialProfit = 0; @endphp
                                                @foreach($currentInventory as $index => $product)
                                                @php 
                                                    $stockValue = $product->quantity * $product->purchase_price;
                                                    $potentialProfit = $product->quantity * ($product->sale_price - $product->purchase_price);
                                                    $totalStockValue += $stockValue;
                                                    $totalPotentialProfit += $potentialProfit;
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $product->name }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-{{ $product->quantity <= 5 ? 'danger' : ($product->quantity <= 10 ? 'warning' : 'success') }} rounded-pill">
                                                            {{ $product->quantity }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">Rs. {{ number_format($product->purchase_price, 2) }}</td>
                                                    <td class="text-end">Rs. {{ number_format($product->sale_price, 2) }}</td>
                                                    <td class="text-end fw-medium text-success">Rs. {{ number_format($potentialProfit, 2) }}</td>
                                                    <td class="text-end fw-medium">Rs. {{ number_format($stockValue, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <th colspan="5" class="text-end fw-bold">Total:</th>
                                                    <th class="text-end text-success fw-bold">Rs. {{ number_format($totalPotentialProfit, 2) }}</th>
                                                    <th class="text-end fw-bold">Rs. {{ number_format($totalStockValue, 2) }}</th>
                                                </tr>
                                            </tfoot>
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
        const quantityData = @json($monthlyTrend->pluck('total_quantity'));
        
        // Create the monthly trend chart
        const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
        const monthlyTrendChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Total Revenue (Rs.)',
                        data: salesData,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Quantity Sold',
                        data: quantityData,
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
                            text: 'Revenue (Rs.)',
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
                            text: 'Quantity Sold',
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

        /* Optimize table spacing */
        .table th {
            font-weight: bold;
            font-size: 8pt;
        }
        
        .table td {
            font-size: 8pt;
        }
        
        /* Make inventory table more compact */
        .inventory-table th:nth-child(4),
        .inventory-table td:nth-child(4),
        .inventory-table th:nth-child(5),
        .inventory-table td:nth-child(5) {
            display: none !important;
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
