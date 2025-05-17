
@section('title', 'Profit Tracking Dashboard')

@extends('layouts.app')

@section('styles')
<style>
    /* Custom styles for professional profit dashboard */
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
        
        /* Modern Card Colors */
        --revenue-color: #1e88e5;
        --profit-color: #2e7d32;
        --margin-color: #00bcd4;
        --sales-color: #ffc107;
    }
    
    .bg-gradient-primary-to-secondary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
    }
    
    .rounded-4 {
        border-radius: 0.75rem !important;
    }
    
    .rounded-5 {
        border-radius: 1.25rem !important;
    }
    
    .display-6 {
        font-size: 1.75rem;
        font-weight: 600;
    }
    
    .chart-area, .chart-pie {
        position: relative;
        height: 100%;
        width: 100%;
    }
    
    /* Card professional styling */
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
    }
    
    /* Custom color for positive/negative indicators */
    .text-success {
        color: var(--success-color) !important;
    }
    
    .text-danger {
        color: var(--danger-color) !important;
    }
    
    .text-primary {
        color: var(--primary-color) !important;
    }
    
    .text-secondary {
        color: var(--secondary-color) !important;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
        background-color: var(--dark-color);
        border-color: var(--dark-color);
    }
    
    .btn-outline-primary {
        color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        color: white;
    }
    
    /* Professional filter styling */
    .filter-container {
        background-color: white;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 800px;
    }
    
    .filter-input {
        border-radius: 8px;
        border: 1px solid #e0e6ed;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
    }
    
    .filter-input:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.15);
    }
    
    /* Table styling */
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05rem;
        color: var(--dark-color);
        background-color: rgba(236, 240, 241, 0.5);
        border-top: none;
    }
    
    .table td {
        vertical-align: middle;
        border-color: #edf2f7;
        padding: 1rem 0.75rem;
    }
    
    /* Button styling */
    .action-button {
        border-radius: 8px;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    
    /* Modern KPI Cards Styling */
    .modern-card {
        height: 100%;
        border-radius: 12px;
        color: white;
        padding: 20px;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
    }
    
    .modern-card-content {
        flex-grow: 1;
        padding-bottom: 10px;
    }
    
    .modern-card-footer {
        display: flex;
        flex-direction: column;
        padding-top: 10px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .modern-card-value {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
        line-height: 1.2;
    }
    
    .modern-card-label {
        opacity: 0.9;
        margin-bottom: 0;
        font-size: 14px;
    }
    
    .growth-indicator {
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .growth-indicator.positive {
        color: white;
    }
    
    .growth-indicator.negative {
        color: rgba(255, 255, 255, 0.8);
    }
    
    .growth-indicator.neutral {
        color: rgba(255, 255, 255, 0.7);
    }
    
    /* Card Colors */
    .revenue-card {
        background-color: var(--revenue-color);
        background-image: linear-gradient(135deg, var(--revenue-color) 0%, #0d47a1 100%);
    }
    
    .profit-card {
        background-color: var(--profit-color);
        background-image: linear-gradient(135deg, var(--profit-color) 0%, #1b5e20 100%);
    }
    
    .margin-card {
        background-color: var(--margin-color);
        background-image: linear-gradient(135deg, var(--margin-color) 0%, #006064 100%);
    }
    
    .sales-card {
        background-color: var(--sales-color);
        background-image: linear-gradient(135deg, var(--sales-color) 0%, #ff8f00 100%);
    }
    
    /* Responsive fixes */
    @media (max-width: 768px) {
        .display-6 {
            font-size: 1.5rem;
        }
        
        .filter-container {
            padding: 1rem;
            width: 100%;
        }
        
        .modern-card-value {
            font-size: 22px;
        }
        
        .chart-area, .chart-pie {
            height: 250px !important;
        }
        
        .rounded-pill {
            margin-bottom: 0.5rem;
        }
        
        .card {
            margin-bottom: 1rem;
        }
        
        .card-header {
            padding: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Professional Header with gradient background -->
    <div class="card shadow-sm mb-4 bg-gradient-primary-to-secondary rounded-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-white p-2 rounded-circle me-3 d-flex justify-content-center align-items-center" style="width: 48px; height: 48px">
                            <i class="fas fa-chart-bar text-primary fa-lg"></i>
                        </div>
                        <h1 class="h2 mb-0 text-white fw-bold">Financial Analytics</h1>
                    </div>
                    <p class="text-white-50 mb-0">Comprehensive business profit performance & metrics analysis</p>
                </div>
                <div class="filter-container">
                    <form action="{{ route('reports.profit') }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-4 col-lg-3">
                                <label class="text-muted small mb-1 fw-medium">Start Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-calendar-alt text-secondary"></i>
                                    </span>
                                    <input type="date" class="form-control filter-input border-start-0 ps-0" name="start_date" value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3">
                                <label class="text-muted small mb-1 fw-medium">End Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-calendar-check text-secondary"></i>
                                    </span>
                                    <input type="date" class="form-control filter-input border-start-0 ps-0" name="end_date" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary action-button w-100">
                                    <i class="fas fa-filter"></i>
                                    <span>Generate Report</span>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a href="{{ route('reports.profit') }}?start_date={{ now()->subDays(7)->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-calendar-alt me-1"></i> Last 7 Days
                            </a>
                            <a href="{{ route('reports.profit') }}?start_date={{ now()->subDays(30)->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-calendar-week me-1"></i> Last 30 Days
                            </a>
                            <a href="{{ route('reports.profit') }}?start_date={{ now()->subDays(90)->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-calendar-check me-1"></i> Last Quarter
                            </a>
                            <a href="{{ route('reports.profit') }}?start_date={{ now()->startOfYear()->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-calendar me-1"></i> Year to Date
                            </a>
                            <a href="{{ route('reports.profit') }}?start_date={{ now()->subYear()->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-history me-1"></i> Last 12 Months
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Discount Impact Analysis Card -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-light py-3 px-4 border-0">
            <div class="d-flex align-items-center">
                <div class="rounded-circle p-2 bg-warning bg-opacity-10 me-3 d-flex justify-content-center align-items-center" style="width: 42px; height: 42px">
                    <i class="fas fa-tags text-warning"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Discount Impact Analysis</h5>
                    <p class="text-muted small mb-0">How discounts affect your bottom line</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4 mb-4">
                <!-- Item Discounts Card -->
                <div class="col-md-6 col-xl-3">
                    <div class="card border-left-primary shadow h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Item-Level Discounts</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($profitData['total_item_discounts'] ?? 0, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-tag fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Invoice Discounts Card -->
                <div class="col-md-6 col-xl-3">
                    <div class="card border-left-success shadow h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Invoice-Level Discounts</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($profitData['total_invoice_discounts'] ?? 0, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-receipt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Profit Before Discounts Card -->
                <div class="col-md-6 col-xl-3">
                    <div class="card border-left-info shadow h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Profit Before Discounts</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($profitData['total_profit_before_discount'] ?? 0, 2) }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Profit After Discounts Card -->
                <div class="col-md-6 col-xl-3">
                    <div class="card border-left-danger shadow h-100">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Discount Impact</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format(($profitData['discount_impact'] ?? 0), 2) }}</div>
                                    <div class="small text-muted mt-1">
                                        {{ number_format(($profitData['total_profit_before_discount'] ?? 0) > 0 ? (($profitData['discount_impact'] ?? 0) / ($profitData['total_profit_before_discount'] ?? 1)) * 100 : 0, 1) }}% of potential profit
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header bg-light py-3 px-4 border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold mb-0">Profit Comparison (Before & After Discounts)</h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="refresh-chart">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div style="height: 250px;">
                                <canvas id="discountComparisonChart"></canvas>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-4 text-center">
                                    <div class="small text-muted mb-1">Potential Profit</div>
                                    <div class="h5 mb-0 fw-bold text-info">Rs. {{ number_format($profitData['total_profit_before_discount'] ?? 0, 2) }}</div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="small text-muted mb-1">Discount Impact</div>
                                    <div class="h5 mb-0 fw-bold text-danger">Rs. {{ number_format($profitData['discount_impact'] ?? 0, 2) }}</div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-danger" role="progressbar" 
                                            style="width: {{ $profitData['total_profit_before_discount'] > 0 ? ($profitData['discount_impact'] / $profitData['total_profit_before_discount']) * 100 : 0 }}%" 
                                            aria-valuenow="{{ $profitData['total_profit_before_discount'] > 0 ? ($profitData['discount_impact'] / $profitData['total_profit_before_discount']) * 100 : 0 }}" 
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <div class="small text-muted mb-1">Actual Profit</div>
                                    <div class="h5 mb-0 fw-bold text-success">Rs. {{ number_format($profitData['total_profit_after_discount'] ?? 0, 2) }}</div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                            style="width: {{ $profitData['total_profit_before_discount'] > 0 ? ($profitData['total_profit_after_discount'] / $profitData['total_profit_before_discount']) * 100 : 0 }}%" 
                                            aria-valuenow="{{ $profitData['total_profit_before_discount'] > 0 ? ($profitData['total_profit_after_discount'] / $profitData['total_profit_before_discount']) * 100 : 0 }}" 
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <a href="{{ route('reports.profit') }}?start_date={{ now()->subDays(7)->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-calendar-alt me-1"></i> Last 7 Days
                                </a>
                                <a href="{{ route('reports.profit') }}?start_date={{ now()->subDays(30)->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-calendar-week me-1"></i> Last 30 Days
                                </a>
                                <a href="{{ route('reports.profit') }}?start_date={{ now()->subDays(90)->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-calendar-check me-1"></i> Last Quarter
                                </a>
                                <a href="{{ route('reports.profit') }}?start_date={{ now()->startOfYear()->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-calendar me-1"></i> Year to Date
                                </a>
                                <a href="{{ route('reports.profit') }}?start_date={{ now()->subYear()->format('Y-m-d') }}&end_date={{ now()->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-history me-1"></i> Last 12 Months
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Discount Impact Analysis Card -->
        <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
            <div class="card-header bg-light py-3 px-4 border-0">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 bg-warning bg-opacity-10 me-3 d-flex justify-content-center align-items-center" style="width: 42px; height: 42px">
                        <i class="fas fa-tags text-warning"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Discount Impact Analysis</h5>
                        <p class="text-muted small mb-0">How discounts affect your bottom line</p>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-4 mb-4">
                    <!-- Item Discounts Card -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-left-primary shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Item-Level Discounts</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($profitData['total_item_discounts'] ?? 0, 2) }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tag fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Invoice Discounts Card -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-left-success shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Invoice-Level Discounts</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($profitData['total_invoice_discounts'] ?? 0, 2) }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-receipt fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profit Before Discounts Card -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-left-info shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Profit Before Discounts</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($profitData['total_profit_before_discount'] ?? 0, 2) }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profit After Discounts Card -->
                    <div class="col-md-6 col-xl-3">
                        <div class="card border-left-danger shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Discount Impact</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format(($profitData['discount_impact'] ?? 0), 2) }}</div>
                                        <div class="small text-muted mt-1">
                                            {{ number_format(($profitData['total_profit_before_discount'] > 0 ? ($profitData['discount_impact'] / $profitData['total_profit_before_discount']) * 100 : 0), 1) }}% of potential profit
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="card-header bg-light py-3 px-4 border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0">Profit Comparison (Before & After Discounts)</h6>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="refresh-chart">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div style="height: 250px;">
                                    <canvas id="discountComparisonChart"></canvas>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4 text-center">
                                        <div class="small text-muted mb-1">Potential Profit</div>
                                        <div class="h5 mb-0 fw-bold text-info">Rs. {{ number_format($profitData['total_profit_before_discount'] ?? 0, 2) }}</div>
                                        <div class="progress mt-2" style="height: 4px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="small text-muted mb-1">Discount Impact</div>
                                        <div class="h5 mb-0 fw-bold text-danger">Rs. {{ number_format($profitData['discount_impact'] ?? 0, 2) }}</div>
                                        <div class="progress mt-2" style="height: 4px;">
                                            <div class="progress-bar bg-danger" role="progressbar" 
                                                style="width: {{ $profitData['total_profit_before_discount'] > 0 ? ($profitData['discount_impact'] / $profitData['total_profit_before_discount']) * 100 : 0 }}%" 
                                                aria-valuenow="{{ $profitData['total_profit_before_discount'] > 0 ? ($profitData['discount_impact'] / $profitData['total_profit_before_discount']) * 100 : 0 }}" 
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <div class="small text-muted mb-1">Actual Profit</div>
                                        <div class="h5 mb-0 fw-bold text-success">Rs. {{ number_format($profitData['total_profit_after_discount'] ?? 0, 2) }}</div>
                                        <div class="progress mt-2" style="height: 4px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                style="width: {{ $profitData['total_profit_before_discount'] > 0 ? ($profitData['total_profit_after_discount'] / $profitData['total_profit_before_discount']) * 100 : 0 }}%" 
                                                aria-valuenow="{{ $profitData['total_profit_before_discount'] > 0 ? ($profitData['total_profit_after_discount'] / $profitData['total_profit_before_discount']) * 100 : 0 }}" 
                                                aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    // Initialize the discount comparison chart
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var ctx = document.getElementById('discountComparisonChart');
                                        var discountChart;
                                    var discountChart;
                                    
                                    function initDiscountChart() {
                                        // Destroy existing chart if it exists
                                        if (discountChart) {
                                            discountChart.destroy();
                                        }
                                        
                                        if (ctx) {
                                            discountChart = new Chart(ctx, {
                                                type: 'bar',
                                                data: {
                                                    labels: ['Potential Profit', 'Discount Impact', 'Actual Profit'],
                                                    datasets: [{
                                                        label: 'Amount (Rs.)',
                                                        data: [
                                                            {{ $profitData['total_profit_before_discount'] ?? 0 }},
                                                            {{ $profitData['discount_impact'] ?? 0 }},
                                                            {{ $profitData['total_profit_after_discount'] ?? 0 }}
                                                        ],
                                                        backgroundColor: ['#36b9cc', '#e74a3b', '#1cc88a'],
                                                        borderColor: ['#2c9faf', '#e02d1b', '#17a673'],
                                                        borderWidth: 1
                                                    }]
                                                },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false,
                                                scales: {
                                                    y: {
                                                        beginAtZero: true,
                                                        grid: {
                                                            drawBorder: false,
                                                            color: 'rgba(0, 0, 0, 0.05)'
                                                        },
                                                        ticks: {
                                                            callback: function(value) {
                                                                return 'Rs. ' + value.toLocaleString();
                                                            }
                                                        }
                                                    },
                                                    x: {
                                                        grid: {
                                                            display: false
                                                        }
                                                    }
                                                },
                                                plugins: {
                                                    tooltip: {
                                                        callbacks: {
                                                            label: function(context) {
                                                                return 'Rs. ' + context.raw.toLocaleString();
                                                            }
                                                        }
                                                    },
                                                    legend: {
                                                        display: false
                                                    }
                                                }
                                            }
                                        });
                                    }
                                    
                                    // Initialize chart when DOM is loaded
                                    initDiscountChart();
                                    
                                    // Add refresh button functionality
                                    document.getElementById('refresh-chart').addEventListener('click', function() {
                                        initDiscountChart();
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Professional Executive Summary Card -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-light py-3 px-4 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle p-2 bg-primary bg-opacity-10 me-3 d-flex justify-content-center align-items-center" style="width: 42px; height: 42px">
                        <i class="fas fa-file-alt text-primary"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">Executive Summary</h5>
                        <p class="text-muted small mb-0">{{ $startDate->format('MMM d, Y') }} to {{ $endDate->format('MMM d, Y') }}</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('reports.profit.print', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-print me-1"></i> Print Report
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="p-4 bg-light rounded-4">
                        <h6 class="text-uppercase text-secondary small fw-bold mb-3">Business Performance Analysis</h6>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    @if($profitMargin > 30)
                                        <span class="badge bg-success text-white p-2"><i class="fas fa-chart-line me-1"></i> Outstanding</span>
                                    @elseif($profitMargin > 20)
                                        <span class="badge bg-success text-white p-2"><i class="fas fa-chart-line me-1"></i> Strong</span>
                                    @elseif($profitMargin > 10)
                                        <span class="badge bg-info text-white p-2"><i class="fas fa-chart-line me-1"></i> Stable</span>
                                    @else
                                        <span class="badge bg-warning text-dark p-2"><i class="fas fa-exclamation-triangle me-1"></i> Needs Attention</span>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="mb-0">Profit Margin: <span class="
                                        @if($profitMargin > 30) text-success
                                        @elseif($profitMargin > 20) text-success
                                        @elseif($profitMargin > 10) text-info
                                        @else text-warning
                                        @endif fw-bold">{{ number_format($profitMargin, 1) }}%</span>
                                    </h5>
                                </div>
                            </div>
                            
                            <p class="mb-0">
                                @if($profitMargin > 30)
                                    The business is demonstrating <strong class="text-success">exceptional profitability</strong> during this period, operating well above industry standards. This strong financial position provides opportunities for strategic investments or expansion.
                                @elseif($profitMargin > 20)
                                    The business is showing <strong class="text-success">healthy financial performance</strong> during this period, with profit margins indicating effective cost management and pricing strategies.
                                @elseif($profitMargin > 10)
                                    The business maintains <strong class="text-primary">stable operations</strong> with an acceptable profit margin, though there may be opportunities to further optimize pricing or reduce costs.
                                @else
                                    Financial analysis indicates <strong class="text-warning">potential areas for improvement</strong> in profitability. Consider evaluating cost structures, product pricing strategies, and operational efficiencies.
                                @endif
                            </p>
                        </div>
                        
                        @php
                            $topProduct = $topProfitProducts->first();
                            $totalProfitFromTop5 = $topProfitProducts->sum('total_profit');
                            $percentageOfTotalProfit = $totalProfit > 0 ? ($totalProfitFromTop5 / $totalProfit) * 100 : 0;
                        @endphp
                        
                        @if($topProduct)
                        <div>
                            <h6 class="text-uppercase text-secondary small fw-bold mb-3">Product Performance</h6>
                            <p class="mb-0">
                                <span class="fw-medium">"{{ $topProduct->name }}"</span> is the highest performing product, contributing <strong>Rs. {{ number_format($topProduct->total_profit, 2) }}</strong> to the bottom line. The top 5 products represent <strong>{{ number_format($percentageOfTotalProfit, 1) }}%</strong> of total profits, indicating {{ $percentageOfTotalProfit > 70 ? 'a high concentration of profit in select products' : 'a balanced product portfolio distribution' }}.
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="d-flex flex-column h-100">
                        <div class="card h-100 shadow-sm border-0 rounded-4 mb-3">
                            <div class="card-header bg-light py-3 border-0">
                                <h6 class="mb-0 fw-bold">Actions</h6>
                            </div>
                            <div class="card-body p-4 d-flex flex-column gap-3">
                                <a href="{{ route('reports.profit.print') }}?start_date={{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}&end_date={{ request('end_date', now()->format('Y-m-d')) }}" target="_blank" class="btn btn-primary action-button w-100">
                                    <i class="fas fa-print"></i>
                                    <span>Generate PDF Report</span>
                                </a>
                                <button class="btn btn-outline-primary action-button w-100">
                                    <i class="fas fa-download"></i>
                                    <span>Export Excel Report</span>
                                </button>
                                <button class="btn btn-outline-secondary action-button w-100">
                                    <i class="fas fa-share-alt"></i>
                                    <span>Share Report</span>
                                </button>
                                <a href="{{ route('reports.index') }}" class="btn btn-light action-button w-100">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Back to Reports</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern KPI Cards Section -->
    <div class="row mb-4 g-3">
        <!-- Revenue Card -->
        <div class="col-xl-3 col-md-6">
            <div class="modern-card revenue-card">
                <div class="modern-card-content">
                    <h2 class="modern-card-value">Rs. {{ number_format($totalRevenue, 2) }}</h2>
                    <p class="modern-card-label">Total Revenue (Period)</p>
                </div>
                @if(isset($revenueGrowth))
                <div class="modern-card-footer">
                    <div class="d-flex align-items-center">
                        @if($revenueGrowth > 0)
                            <span class="growth-indicator positive"><i class="fas fa-arrow-up"></i> {{ number_format(abs($revenueGrowth), 1) }}%</span>
                        @elseif($revenueGrowth < 0)
                            <span class="growth-indicator negative"><i class="fas fa-arrow-down"></i> {{ number_format(abs($revenueGrowth), 1) }}%</span>
                        @else
                            <span class="growth-indicator neutral"><i class="fas fa-minus"></i> 0%</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Profit Card -->
        <div class="col-xl-3 col-md-6">
            <div class="modern-card profit-card">
                <div class="modern-card-content">
                    <h2 class="modern-card-value">Rs. {{ number_format($totalProfit, 2) }}</h2>
                    <p class="modern-card-label">Total Profit (Period)</p>
                </div>
                @if(isset($profitGrowth))
                <div class="modern-card-footer">
                    <div class="d-flex align-items-center">
                        @if($profitGrowth > 0)
                            <span class="growth-indicator positive"><i class="fas fa-arrow-up"></i> {{ number_format(abs($profitGrowth), 1) }}%</span>
                        @elseif($profitGrowth < 0)
                            <span class="growth-indicator negative"><i class="fas fa-arrow-down"></i> {{ number_format(abs($profitGrowth), 1) }}%</span>
                        @else
                            <span class="growth-indicator neutral"><i class="fas fa-minus"></i> 0%</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Margin Card -->
        <div class="col-xl-3 col-md-6">
            <div class="modern-card margin-card">
                <div class="modern-card-content">
                    <h2 class="modern-card-value">{{ number_format($profitMargin, 2) }}%</h2>
                    <p class="modern-card-label">Profit Margin</p>
                </div>
                @if(isset($marginGrowth))
                <div class="modern-card-footer">
                    <div class="d-flex align-items-center">
                        @if($marginGrowth > 0)
                            <span class="growth-indicator positive"><i class="fas fa-arrow-up"></i> {{ number_format(abs($marginGrowth), 1) }}%</span>
                        @elseif($marginGrowth < 0)
                            <span class="growth-indicator negative"><i class="fas fa-arrow-down"></i> {{ number_format(abs($marginGrowth), 1) }}%</span>
                        @else
                            <span class="growth-indicator neutral"><i class="fas fa-minus"></i> 0%</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sales Card -->
        <div class="col-xl-3 col-md-6">
            <div class="modern-card sales-card">
                <div class="modern-card-content">
                    <h2 class="modern-card-value">{{ number_format($totalSales) }}</h2>
                    <p class="modern-card-label">Total Sales</p>
                </div>
                @if(isset($salesGrowth))
                <div class="modern-card-footer">
                    <div class="d-flex align-items-center">
                        @if($salesGrowth > 0)
                            <span class="growth-indicator positive"><i class="fas fa-arrow-up"></i> {{ number_format(abs($salesGrowth), 1) }}%</span>
                        @elseif($salesGrowth < 0)
                            <span class="growth-indicator negative"><i class="fas fa-arrow-down"></i> {{ number_format(abs($salesGrowth), 1) }}%</span>
                        @else
                            <span class="growth-indicator neutral"><i class="fas fa-minus"></i> 0%</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Weekly Profit Trend Chart -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent py-4 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 fw-bold text-primary">Revenue & Profit Analysis</h5>
                        <p class="text-muted mb-0 small">Week-over-week financial performance trends</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light rounded-pill px-3" type="button" id="chartOptions" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="chartOptions">
                            <li><a class="dropdown-item" href="{{ route('reports.profit.print') }}?start_date={{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}&end_date={{ request('end_date', now()->format('Y-m-d')) }}"><i class="fas fa-print me-2"></i>Print Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-download me-2"></i>Export Data</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $revenueData = $weeklyProfitData->pluck('revenue')->toArray();
                        $profitData = $weeklyProfitData->pluck('profit')->toArray();
                        $lastWeekRevenueChange = count($revenueData) >= 2 ? (($revenueData[count($revenueData)-1] - $revenueData[count($revenueData)-2]) / ($revenueData[count($revenueData)-2] ?: 1)) * 100 : 0;
                        $lastWeekProfitChange = count($profitData) >= 2 ? (($profitData[count($profitData)-1] - $profitData[count($profitData)-2]) / ($profitData[count($profitData)-2] ?: 1)) * 100 : 0;
                    @endphp
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="rounded p-2 bg-primary bg-opacity-10 me-3">
                                    <i class="fas fa-chart-line text-primary"></i>
                                </div>
                                <div>
                                    <span class="d-block text-muted small">Latest Revenue Trend</span>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold">{{ $lastWeekRevenueChange >= 0 ? '+' : '' }}{{ number_format($lastWeekRevenueChange, 1) }}%</span>
                                        <span class="ms-2 badge {{ $lastWeekRevenueChange >= 0 ? 'bg-success' : 'bg-danger' }} rounded-pill">{{ $lastWeekRevenueChange >= 0 ? 'Increasing' : 'Decreasing' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="rounded p-2 bg-success bg-opacity-10 me-3">
                                    <i class="fas fa-chart-pie text-success"></i>
                                </div>
                                <div>
                                    <span class="d-block text-muted small">Latest Profit Trend</span>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold">{{ $lastWeekProfitChange >= 0 ? '+' : '' }}{{ number_format($lastWeekProfitChange, 1) }}%</span>
                                        <span class="ms-2 badge {{ $lastWeekProfitChange >= 0 ? 'bg-success' : 'bg-danger' }} rounded-pill">{{ $lastWeekProfitChange >= 0 ? 'Increasing' : 'Decreasing' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="chart-area position-relative" style="height: 300px;">
                        <canvas id="weeklyProfitChart" style="width: 100%; height: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Most Profitable Products -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent py-4 border-0">
                    <h5 class="mb-1 fw-bold text-primary">Product Contribution Analysis</h5>
                    <p class="text-muted mb-0 small">Profit distribution across top-performing inventory</p>
                </div>
                <div class="card-body">
                    @php
                        $totalTopProfit = $topProfitProducts->sum('total_profit');
                        $topProductContribution = $topProfitProducts->first() ? ($topProfitProducts->first()->total_profit / $totalProfit) * 100 : 0;
                    @endphp
                    
                    <!-- KPI Summary -->
                    <div class="d-flex justify-content-between mb-3">
                        <div class="text-center px-3 py-2 border rounded">
                            <span class="d-block text-muted small">Top Product</span>
                            <span class="fw-bold text-primary d-block">{{ number_format($topProductContribution, 1) }}%</span>
                            <span class="small">of total profit</span>
                        </div>
                        <div class="text-center px-3 py-2 border rounded">
                            <span class="d-block text-muted small">Top 5 Products</span>
                            <span class="fw-bold text-primary d-block">{{ $totalProfit > 0 ? number_format(($totalTopProfit / $totalProfit) * 100, 1) : 0 }}%</span>
                            <span class="small">of total profit</span>
                        </div>
                    </div>
                    
                    <div class="chart-pie position-relative" style="height: 220px;">
                        <canvas id="profitByProductChart" style="width: 100%; height: 100%;"></canvas>
                    </div>
                    
                    <div class="mt-3 d-flex flex-wrap justify-content-center gap-2">
                        @foreach($topProfitProducts as $index => $product)
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                <i class="fas fa-circle me-1" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'][$index % 5] }}"></i> {{ $product->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Monthly Profit Table -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent py-4 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 fw-bold text-primary">Monthly Profitability Metrics</h5>
                        <p class="text-muted mb-0 small">Key financial indicators tracked monthly</p>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="fas fa-sort-amount-down me-1"></i> Sort
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Month</th>
                                    <th>Revenue</th>
                                    <th>Profit</th>
                                    <th class="text-end pe-4">Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyProfitData as $month)
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $month->month }}</td>
                                    <td>Rs. {{ number_format($month->revenue, 2) }}</td>
                                    <td>Rs. {{ number_format($month->profit, 2) }}</td>
                                    <td class="text-end pe-4">
                                        <span class="badge rounded-pill {{ $month->margin >= 30 ? 'bg-success' : ($month->margin >= 15 ? 'bg-info' : 'bg-warning') }} px-3 py-2">
                                            {{ number_format($month->margin, 2) }}%
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

        <!-- Weekly Profit Table -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent py-4 border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 fw-bold text-primary">Weekly Operational Performance</h5>
                        <p class="text-muted mb-0 small">Short-term ROI and efficiency indicators</p>
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="fas fa-calendar-week me-1"></i> View All
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Week Period</th>
                                    <th>Revenue</th>
                                    <th>Profit</th>
                                    <th class="text-end pe-4">Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weeklyProfitData as $week)
                                <tr>
                                    <td class="ps-4 fw-medium">
                                        <i class="fas fa-calendar-week me-2 text-primary"></i> 
                                        {{ $week->week_start }} - {{ $week->week_end }}
                                    </td>
                                    <td>Rs. {{ number_format($week->revenue, 2) }}</td>
                                    <td>Rs. {{ number_format($week->profit, 2) }}</td>
                                    <td class="text-end pe-4">
                                        <span class="badge rounded-pill {{ $week->margin >= 30 ? 'bg-success' : ($week->margin >= 15 ? 'bg-info' : 'bg-warning') }} px-3 py-2">
                                            {{ number_format($week->margin, 2) }}%
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

    <!-- Category Profit Analysis -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profit by Company</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
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
        </div>
    </div>

    <!-- Enhanced KPI Cards -->
    <div class="row g-4 mb-4">
        <!-- Revenue Card -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, rgba(52, 152, 219, 0.1) 0%, rgba(52, 152, 219, 0.3) 100%); width: 42px; height: 42px;">
                                    <i class="fas fa-dollar-sign text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="fw-bold mb-0">Total Revenue</h6>
                                    <p class="text-muted mb-0 small">Gross sales amount</p>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-chart-line me-2 small"></i> View Trend</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-export me-2 small"></i> Export Data</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h3 class="fw-bold mb-0">Rs. {{ number_format($totalRevenue, 2) }}</h3>
                        </div>
                    </div>
                    <div class="px-4 py-2 d-flex align-items-center justify-content-between" style="background-color: rgba(248, 249, 250, 0.7)">
                        <p class="text-muted mb-0 small">
                            @if($revenueGrowth > 0)
                                <span class="text-success"><i class="fas fa-arrow-up me-1"></i> {{ number_format(abs($revenueGrowth), 1) }}%</span>
                            @elseif($revenueGrowth < 0)
                                <span class="text-danger"><i class="fas fa-arrow-down me-1"></i> {{ number_format(abs($revenueGrowth), 1) }}%</span>
                            @else
                                <span class="text-muted"><i class="fas fa-minus me-1"></i> 0%</span>
                            @endif
                            <span class="ms-1">vs previous</span>
                        </p>
                        <div class="text-{{ $revenueGrowth >= 0 ? 'success' : 'danger' }}">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
        // Enhanced Chart.js configuration with animation
        Chart.defaults.font.family = "'Nunito', 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
        Chart.defaults.color = '#858796';
        if (Chart.defaults.plugins) {
            Chart.defaults.plugins.tooltip.padding = 10;
            Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(255, 255, 255, 0.9)';
            Chart.defaults.plugins.tooltip.titleColor = '#6e707e';
            Chart.defaults.plugins.tooltip.bodyColor = '#858796';
            Chart.defaults.plugins.tooltip.borderColor = '#dddfeb';
            Chart.defaults.plugins.tooltip.borderWidth = 1;
            Chart.defaults.plugins.tooltip.displayColors = false;
            Chart.defaults.plugins.tooltip.mode = 'index';
            Chart.defaults.plugins.tooltip.intersect = false;
        }

        // Weekly Profit Chart
        document.addEventListener('DOMContentLoaded', function() {
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
                        layout: {
                            padding: {
                                left: 10,
                                right: 25,
                                top: 25,
                                bottom: 0
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    maxTicksLimit: 7,
                                    font: {
                                        size: 11,
                                    }
                                }
                            },
                            y: {
                                ticks: {
                                    maxTicksLimit: 5,
                                    padding: 10,
                                    font: {
                                        size: 11,
                                    },
                                    callback: function(value, index, values) {
                                        return 'Rs. ' + value.toLocaleString();
                                    }
                                },
                                grid: {
                                    color: "rgba(234, 236, 244, 0.4)",
                                    drawBorder: false,
                                    borderDash: [2],
                                },
                                beginAtZero: true
                            },
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    padding: 20,
                                    font: {
                                        size: 12,
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: "rgba(255,255,255,0.9)",
                                bodyColor: "#858796",
                                titleMarginBottom: 10,
                                titleColor: '#6e707e',
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                padding: 15,
                                displayColors: false,
                                intersect: false,
                                mode: 'index',
                                caretPadding: 10,
                            }
                        }
                    }
                });
            }

            // Most Profitable Products Pie Chart
            var profitByProductCtx = document.getElementById("profitByProductChart");
            if (profitByProductCtx) {
                var profitByProductChart = new Chart(profitByProductCtx, {
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
                                padding: 15,
                                displayColors: false,
                                caretPadding: 10,
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
                },
                legend: {
                    display: false
                }
            },
            cutout: '70%',
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1000,
                easing: 'easeOutQuart'
            },
        },
    });
    
    // Add hover effect to table rows
    document.querySelectorAll('.table tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.classList.add('bg-light');
        });
        row.addEventListener('mouseleave', function() {
            this.classList.remove('bg-light');
        });
    });
</script>
@endsection