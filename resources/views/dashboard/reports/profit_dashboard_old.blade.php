@section('title', 'Profit Dashboard')

@extends('layouts.app')

@section('styles')
<style>
    /* Clean and professional profit dashboard styles */
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #3498db;
        --success-color: #2ecc71;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
        --info-color: #3498db;
        --light-color: #ecf0f1;
        --dark-color: #34495e;
    }
    
    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: none;
        padding: 1rem 1.25rem;
    }
    
    .metric-card {
        padding: 1.5rem;
        text-align: center;
    }
    
    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .metric-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .profit-positive {
        color: var(--success-color);
    }
    
    .profit-negative {
        color: var(--danger-color);
    }
    
    .chart-container {
        height: 300px;
        position: relative;
    }
    
    .date-filter {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .date-filter .btn {
        border-radius: 20px;
        font-size: 0.8rem;
        padding: 0.375rem 1rem;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .progress {
        height: 6px;
        margin-top: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Dashboard Header -->
    <div class="card mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold">Profit Dashboard</h1>
                    <p class="text-muted mb-0">Track your business profitability from {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.profit.print', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="fas fa-print me-2"></i>Print Report
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Date Range Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('reports.profit') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                </div>
            </form>
            
            <div class="date-filter mt-3">
                <a href="{{ route('reports.profit') }}?start_date={{ now()->subDays(7)->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-secondary">
                    Last 7 Days
                </a>
                <a href="{{ route('reports.profit') }}?start_date={{ now()->subDays(30)->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-secondary">
                    Last 30 Days
                </a>
                <a href="{{ route('reports.profit') }}?start_date={{ now()->subDays(90)->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-secondary">
                    Last Quarter
                </a>
                <a href="{{ route('reports.profit') }}?start_date={{ now()->startOfYear()->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-secondary">
                    Year to Date
                </a>
            </div>
        </div>
    </div>
    
    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="metric-card">
                    <div class="metric-value text-primary">Rs. {{ number_format($totalRevenue, 2) }}</div>
                    <div class="metric-title">Total Revenue</div>
                    @if(isset($revenueGrowth))
                    <div class="mt-2 small {{ $revenueGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-{{ $revenueGrowth >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                        {{ number_format(abs($revenueGrowth), 1) }}% from previous period
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="metric-card">
                    <div class="metric-value text-success">Rs. {{ number_format($totalProfit, 2) }}</div>
                    <div class="metric-title">Net Profit</div>
                    @if(isset($profitGrowth))
                    <div class="mt-2 small {{ $profitGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-{{ $profitGrowth >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                        {{ number_format(abs($profitGrowth), 1) }}% from previous period
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="metric-card">
                    <div class="metric-value text-info">{{ number_format($profitMargin, 1) }}%</div>
                    <div class="metric-title">Profit Margin</div>
                    @if(isset($marginGrowth))
                    <div class="mt-2 small {{ $marginGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-{{ $marginGrowth >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                        {{ number_format(abs($marginGrowth), 1) }}% from previous period
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="metric-card">
                    <div class="metric-value text-warning">{{ number_format($totalSales) }}</div>
                    <div class="metric-title">Total Sales</div>
                    @if(isset($salesGrowth))
                    <div class="mt-2 small {{ $salesGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fas fa-{{ $salesGrowth >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                        {{ number_format(abs($salesGrowth), 1) }}% from previous period
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Discount Impact -->
    @if(isset($profitData) && isset($profitData['discount_impact']))
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Discount Impact Analysis</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="small text-muted mb-1">Profit Before Discount</div>
                    <div class="h5 mb-0 fw-bold text-primary">Rs. {{ number_format($profitData['total_profit_before_discount'] ?? 0, 2) }}</div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="small text-muted mb-1">Discount Impact</div>
                    <div class="h5 mb-0 fw-bold text-danger">Rs. {{ number_format($profitData['discount_impact'] ?? 0, 2) }}</div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="small text-muted mb-1">Actual Profit</div>
                    <div class="h5 mb-0 fw-bold text-success">Rs. {{ number_format($profitData['total_profit_after_discount'] ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Weekly Profit Trend -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Weekly Profit Trend</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="weeklyProfitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Top Profitable Products</h5>
                </div>
                <div class="card-body">
                    @if(isset($topProfitProducts) && count($topProfitProducts) > 0)
                    <div class="chart-container">
                        <canvas id="profitByProductChart"></canvas>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <p class="text-muted">No product data available for the selected period</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Profitable Products Table -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Most Profitable Products</h5>
        </div>
        <div class="card-body">
            @if(isset($topProfitProducts) && count($topProfitProducts) > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-end">Quantity Sold</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Profit</th>
                            <th class="text-end">Profit Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProfitProducts as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td class="text-end">{{ number_format($product->total_quantity) }}</td>
                            <td class="text-end">Rs. {{ number_format($product->total_revenue ?? 0, 2) }}</td>
                            <td class="text-end">Rs. {{ number_format($product->total_profit ?? 0, 2) }}</td>
                            <td class="text-end">
                                @php
                                    $margin = ($product->total_revenue > 0) ? ($product->total_profit / $product->total_revenue) * 100 : 0;
                                @endphp
                                {{ number_format($margin, 1) }}%
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <p class="text-muted">No product data available for the selected period</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Weekly Profit Chart
        var weeklyProfitCtx = document.getElementById('weeklyProfitChart');
        if (weeklyProfitCtx) {
            new Chart(weeklyProfitCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($weeklyProfitData->pluck('week_start')) !!},
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode($weeklyProfitData->pluck('revenue')) !!},
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderColor: 'rgba(52, 152, 219, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(52, 152, 219, 1)',
                        pointRadius: 4,
                        tension: 0.3,
                        fill: true
                    }, {
                        label: 'Profit',
                        data: {!! json_encode($weeklyProfitData->pluck('profit')) !!},
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        borderColor: 'rgba(46, 204, 113, 1)',
                        borderWidth: 2,
                        pointBackgroundColor: 'rgba(46, 204, 113, 1)',
                        pointRadius: 4,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            padding: 10,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rs. ' + context.raw.toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rs. ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Product Profit Chart
        var profitByProductCtx = document.getElementById('profitByProductChart');
        if (profitByProductCtx && {!! isset($topProfitProducts) && count($topProfitProducts) > 0 ? 'true' : 'false' !!}) {
            new Chart(profitByProductCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! isset($topProfitProducts) ? json_encode($topProfitProducts->pluck('name')) : '[]' !!},
                    datasets: [{
                        data: {!! isset($topProfitProducts) ? json_encode($topProfitProducts->pluck('total_profit')) : '[]' !!},
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.8)',
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(155, 89, 182, 0.8)',
                            'rgba(52, 73, 94, 0.8)',
                            'rgba(243, 156, 18, 0.8)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.raw || 0;
                                    var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    
                                    return label + ': Rs. ' + value.toLocaleString(undefined, {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }) + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    });
</script>
@endsection
