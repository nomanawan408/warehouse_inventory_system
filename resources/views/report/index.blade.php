@extends('layouts.app')
@section('content')
<style>
    /* Modern Futuristic Styles */
    .dashboard-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    
    .dashboard-header {
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
    }
    
    .dashboard-title {
        font-weight: 700;
        color: #2c3e50;
        font-size: 1.8rem;
        position: relative;
        padding-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .dashboard-title:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, #4776E6 0%, #8E54E9 100%);
        border-radius: 3px;
    }
    
    .date-range-container {
        background: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 25px;
        border-left: 4px solid #4776E6;
    }
    
    .form-control {
        border-radius: 8px;
        border: 1px solid #e0e6ed;
        padding: 12px 15px;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(71, 118, 230, 0.2);
        border-color: #4776E6;
    }
    
    .stat-card {
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        border: none;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        border-bottom: none;
        padding: 20px;
        font-weight: 600;
    }
    
    .card-body {
        padding: 25px;
    }
    
    .stat-icon {
        background: rgba(255, 255, 255, 0.2);
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin: 0 auto 15px;
    }
    
    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .stat-label {
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.8rem;
        opacity: 0.8;
    }
    
    .table-container {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .table thead th {
        background: #f8f9fa;
        border-bottom: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
        padding: 15px;
    }
    
    .table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-color: #f1f4f8;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(71, 118, 230, 0.05);
    }
    
    /* Gradient backgrounds for stat cards */
    .daily-card {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
    }
    
    .weekly-card {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }
    
    .monthly-card {
        background: linear-gradient(135deg, #0061ff 0%, #60efff 100%);
        color: white;
    }
    
    /* New styles for comparison charts */
    .comparison-chart-container {
        position: relative;
        height: 300px;
    }
    
    .profit-ratio-card {
        background: linear-gradient(135deg, #FF8008 0%, #FFC837 100%);
        color: white;
    }
    
    .kpi-card {
        border-left: 4px solid;
        transition: transform 0.3s;
    }
    
    .kpi-card:hover {
        transform: translateY(-5px);
    }
    
    .kpi-positive {
        border-color: #38ef7d;
    }
    
    .kpi-negative {
        border-color: #ff5e62;
    }
    
    .kpi-neutral {
        border-color: #4776E6;
    }
    
    .trend-indicator {
        font-size: 0.8rem;
        padding: 3px 8px;
        border-radius: 12px;
        margin-left: 5px;
    }
    
    .trend-up {
        background-color: rgba(56, 239, 125, 0.2);
        color: #11998e;
    }
    
    .trend-down {
        background-color: rgba(255, 94, 98, 0.2);
        color: #ff5e62;
    }
    
    /* New styles for enhanced charts */
    .comparison-chart {
        height: 350px;
    }
    
    .insight-card {
        border-radius: 15px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .insight-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .insight-header {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-weight: 600;
    }
    
    .insight-body {
        padding: 20px;
    }
    
    .profit-card {
        background: linear-gradient(135deg, #FF416C 0%, #FF4B2B 100%);
        color: white;
    }
</style>

<div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <div class="dashboard-container">
                <div class="dashboard-header d-flex justify-content-between align-items-center">
                    <h4 class="dashboard-title">Business Analytics Dashboard</h4>
                </div>

                <!-- Date Range Picker -->
                <div class="date-range-container">
                    <label for="dateRange" class="form-label fw-bold">Select Date Range:</label>
                    <input type="text" id="dateRange" name="dateRange" class="form-control" placeholder="Select Date Range">
                </div>

                <!-- Time Period Statistics -->
                <div class="row mb-4">
                    <!-- Daily Stats -->
                    <div class="col-md-4">
                        <div class="card stat-card daily-card h-100">
                            <div class="card-body text-center">
                                <div class="stat-icon">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                                <h6 class="stat-label">Today's Overview</h6>
                                <div class="mt-3">
                                    <div class="mb-3">
                                        <p class="mb-1 stat-label">Sales</p>
                                        <h4 class="stat-value mb-0">Rs. <span id="dailySales">{{ number_format($dailyStats['sales'], 2) }}</span></h4>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1 stat-label">Purchases</p>
                                        <h4 class="stat-value mb-0">Rs. <span id="dailyPurchases">{{ number_format($dailyStats['purchases'], 2) }}</span></h4>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1 stat-label">Profit Margin</p>
                                        <h4 class="stat-value mb-0">Rs. <span id="dailyProfit">{{ number_format($dailyStats['profit_margin'], 2) }}</span></h4>
                                    </div>
                                    <div>
                                        <p class="mb-1 stat-label">New Customers</p>
                                        <h4 class="stat-value mb-0"><span id="dailyNewCustomers">{{ $dailyStats['new_customers'] }}</span></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Stats -->
                    <div class="col-md-4">
                        <div class="card stat-card weekly-card h-100">
                            <div class="card-body text-center">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-week fa-2x"></i>
                                </div>
                                <h6 class="stat-label">This Week</h6>
                                <div class="mt-3">
                                    <div class="mb-3">
                                        <p class="mb-1 stat-label">Sales</p>
                                        <h4 class="stat-value mb-0">Rs. <span id="weeklySales">{{ number_format($weeklyStats['sales'], 2) }}</span></h4>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1 stat-label">Purchases</p>
                                        <h4 class="stat-value mb-0">Rs. <span id="weeklyPurchases">{{ number_format($weeklyStats['purchases'], 2) }}</span></h4>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1 stat-label">Profit Margin</p>
                                        <h4 class="stat-value mb-0">Rs. <span id="weeklyProfit">{{ number_format($weeklyStats['profit_margin'], 2) }}</span></h4>
                                    </div>
                                    <div>
                                        <p class="mb-1 stat-label">New Customers</p>
                                        <h4 class="stat-value mb-0"><span id="weeklyNewCustomers">{{ $weeklyStats['new_customers'] }}</span></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Stats -->
                    <div class="col-md-4">
                        <div class="card stat-card monthly-card h-100">
                            <div class="card-body text-center">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-alt fa-2x"></i>
                                </div>
                                <h6 class="stat-label">This Month</h6>
                                <div class="mt-3">
                                    <div class="mb-3">
                                        <p class="mb-1 stat-label">Sales</p>
                                        <h4 class="stat-value mb-0">Rs. <span id="monthlySales">{{ number_format($monthlyStats['sales'], 2) }}</span></h4>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1 stat-label">Purchases</p>
                                        <h4 class="stat-value mb-0">Rs. <span id="monthlyPurchases">{{ number_format($monthlyStats['purchases'], 2) }}</span></h4>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1 stat-label">Profit Margin</p>
                                        <h4 class="stat-value mb-0">Rs. <span id="monthlyProfit">{{ number_format($monthlyStats['profit_margin'], 2) }}</span></h4>
                                    </div>
                                    <div>
                                        <p class="mb-1 stat-label">New Customers</p>
                                        <h4 class="stat-value mb-0"><span id="monthlyNewCustomers">{{ $monthlyStats['new_customers'] }}</span></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card kpi-card kpi-positive h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Profit Ratio</h6>
                                <h4 class="mb-2">{{ number_format(($dailyStats['profit_margin'] / max($dailyStats['sales'], 1)) * 100, 1) }}%</h4>
                                <p class="mb-0 text-muted">Daily Average</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card kpi-card kpi-neutral h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Sales to Purchase Ratio</h6>
                                <h4 class="mb-2">{{ number_format(($dailyStats['sales'] / max($dailyStats['purchases'], 1)) * 100, 1) }}%</h4>
                                <p class="mb-0 text-muted">Daily Average</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card kpi-card kpi-positive h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Customer Growth</h6>
                                <h4 class="mb-2">{{ number_format($monthlyStats['new_customers'] / 30, 1) }}</h4>
                                <p class="mb-0 text-muted">Daily Average</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card kpi-card kpi-negative h-100">
                            <div class="card-body">
                                <h6 class="text-muted mb-1">Profit Growth</h6>
                                <h4 class="mb-2">{{ number_format((($monthlyStats['profit_margin'] - $weeklyStats['profit_margin']) / max($weeklyStats['profit_margin'], 1)) * 100, 1) }}%</h4>
                                <p class="mb-0 text-muted">Month over Week</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Containers Section -->
<div class="container my-4">
    <div class="row">
        <div class="col-md-12">
            <div class="dashboard-container">
                <div class="dashboard-header">
                    <h4 class="dashboard-title">Performance Analytics</h4>
                </div>
                
                <!-- Charts Row -->
                <div class="row mb-4">
                    <!-- Sales vs Profit Chart -->
                    <div class="col-md-6">
                        <div class="card insight-card h-100">
                            <div class="card-header bg-white">
                                Sales vs Profit Comparison
                            </div>
                            <div class="card-body">
                                <div class="comparison-chart">
                                    <canvas id="salesProfitChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profit Trend Chart -->
                    <div class="col-md-6">
                        <div class="card insight-card h-100">
                            <div class="card-header bg-white">
                                Profit Trend Analysis
                            </div>
                            <div class="card-body">
                                <div class="comparison-chart">
                                    <canvas id="profitTrendChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Profit Ratio Chart -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card insight-card profit-card">
                            <div class="insight-header">
                                Profit-to-Sales Ratio
                            </div>
                            <div class="insight-body">
                                <div class="comparison-chart">
                                    <canvas id="profitRatioChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Required Scripts -->
<script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sample data for charts
    const monthlySales = [12500, 15000, 17500, 16000, 18000, 20000];
    const monthlyProfits = [4500, 5200, 6000, 5500, 6200, 7000];
    const monthlyProfitRatios = [0.36, 0.35, 0.34, 0.34, 0.34, 0.35];
    
    // Initialize date range picker
    $(function() {
        $('#dateRange').daterangepicker({
            opens: 'left',
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });
    });
</script>
<script src="{{ asset('js/report-charts.js') }}"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection