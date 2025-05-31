@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('styles')
<style>
    /* Custom styles for reports dashboard */
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
    
    .bg-gradient-primary-to-secondary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
    }
    
    .report-card {
        border-radius: 12px;
        border: none;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        height: 100%;
    }
    
    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .report-card .card-body {
        padding: 1.5rem;
    }
    
    .report-card .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 1rem;
    }
    
    .report-card h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .report-card p {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }
    
    .report-card .btn {
        border-radius: 8px;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .stats-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
    }
    
    .stats-card .card-body {
        padding: 1.5rem;
    }
    
    .stats-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stats-label {
        color: #6c757d;
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    .custom-tab-container {
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .custom-nav-tabs {
        background-color: #f8f9fa;
        padding: 1rem 1rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    }
    
    .custom-nav-tabs .nav-link {
        border-radius: 8px 8px 0 0;
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        color: #495057;
        border: 1px solid transparent;
        margin-right: 0.25rem;
        position: relative;
        transition: all 0.2s;
    }
    
    .custom-nav-tabs .nav-link:hover {
        color: var(--primary-color);
        border-color: transparent;
    }
    
    .custom-nav-tabs .nav-link.active {
        color: var(--primary-color);
        background-color: #fff;
        border-color: rgba(0, 0, 0, 0.08);
        border-bottom-color: #fff;
    }
    
    .custom-nav-tabs .nav-link i {
        margin-right: 0.5rem;
    }
    
    .custom-tab-content {
        padding: 1.5rem;
        background-color: #fff;
    }
    
    @media (max-width: 768px) {
        .report-card {
            margin-bottom: 1rem;
        }
        
        .stats-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endsection

@section('content')

<style>
    .custom-tab-content {
        padding: 1.5rem;
        background-color: #f8f9fc;
        border-radius: 0.5rem;
        margin-top: 1rem;
    }
    
    .stats-card {
        border-radius: 0.75rem;
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        transition: all 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .stats-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2e59d9;
        margin-bottom: 0.25rem;
    }
    
    .stats-label {
        color: #858796;
        font-size: 0.85rem;
        text-transform: uppercase;
        margin-bottom: 0;
    }
    
    .nav-tabs .nav-link {
        border-radius: 0.5rem 0.5rem 0 0;
        font-weight: 600;
        color: #6e707e;
        transition: all 0.2s ease;
        padding: 0.75rem 1.25rem;
    }
    
    .nav-tabs .nav-link.active {
        color: #4e73df;
        background-color: #fff;
        border-bottom-color: #fff;
    }
    
    .nav-tabs .nav-link:hover:not(.active) {
        color: #4e73df;
        background-color: rgba(78, 115, 223, 0.1);
    }
</style>

<div class="container-fluid py-4">
    <!-- Professional Header with gradient background -->
    <div class="card shadow-sm mb-4 bg-gradient-primary-to-secondary rounded-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center">
                <div class="bg-white p-2 rounded-circle me-3 d-flex justify-content-center align-items-center" style="width: 48px; height: 48px">
                    <i class="fas fa-chart-pie text-primary fa-lg"></i>
                </div>
                <div>
                    <h1 class="h2 mb-0 text-white fw-bold">Reports & Analytics</h1>
                    <p class="text-white-50 mb-0">Comprehensive business analytics and reporting tools</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Report Type Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="report-card">
                <div class="card-body d-flex flex-column">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Profit Dashboard</h3>
                    <p>Analyze revenue, profit margins, and financial performance trends</p>
                    <a href="{{ route('reports.profit') }}" class="btn btn-primary mt-auto">
                        <i class="fas fa-arrow-right"></i>
                        <span>View Report</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="report-card">
                <div class="card-body d-flex flex-column">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Customer Reports</h3>
                    <p>Track customer purchase trends, payment history and profiles</p>
                    <button class="btn btn-success mt-auto" onclick="document.getElementById('customer-tab').click()">
                        <i class="fas fa-arrow-right"></i>
                        <span>View Report</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="report-card">
                <div class="card-body d-flex flex-column">
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Company Reports</h3>
                    <p>Analyze supplier performance, inventory status and transactions</p>
                    <button class="btn btn-info mt-auto" onclick="document.getElementById('company-tab').click()">
                        <i class="fas fa-arrow-right"></i>
                        <span>View Report</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
            <div class="report-card">
                <div class="card-body d-flex flex-column">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <h3>Inventory Status</h3>
                    <p>Track product stock levels, movements and valuation analysis</p>
                    <button class="btn btn-warning mt-auto" onclick="document.getElementById('dashboard-tab').click()">
                        <i class="fas fa-arrow-right"></i>
                        <span>View Status</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dashboard Tabs -->
    <div class="custom-tab-container mb-4">
        <ul class="nav nav-tabs custom-nav-tabs" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard" 
                    type="button" role="tab" aria-controls="dashboard" aria-selected="true">
                    <i class="fas fa-tachometer-alt"></i> Dashboard Overview
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" 
                    type="button" role="tab" aria-controls="customer" aria-selected="false">
                    <i class="fas fa-users"></i> Customer Reports
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="company-tab" data-bs-toggle="tab" data-bs-target="#company" 
                    type="button" role="tab" aria-controls="company" aria-selected="false">
                    <i class="fas fa-building"></i> Company Reports
                </button>
            </li>
        </ul>

        <div class="tab-content custom-tab-content" id="reportTabsContent">
            <!-- Dashboard Tab -->
            <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                <!-- Stats Cards with Fixed Icons -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="stats-card h-100 bg-white">
                            <div class="card-body d-flex flex-column position-relative">
                                <div class="rounded-circle p-3 d-inline-flex" style="background: rgba(78, 115, 223, 0.1)">
                                    <i class="fas fa-cash-register text-primary"></i>
                                </div>
                                <h2 class="stats-value mt-3">Rs. {{ number_format($dailyStats['sales'], 2) }}</h2>
                                <p class="stats-label">Today's Sales</p>
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 d-flex align-items-center">
                                        <i class="fas fa-calendar-day me-2"></i><span>Today</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card h-100 bg-white">
                            <div class="card-body d-flex flex-column position-relative">
                                <div class="rounded-circle p-3 d-inline-flex" style="background: rgba(28, 200, 138, 0.1)">
                                    <i class="fas fa-chart-bar text-success"></i>
                                </div>
                                <h2 class="stats-value mt-3">Rs. {{ number_format($weeklyStats['sales'], 2) }}</h2>
                                <p class="stats-label">Weekly Sales</p>
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 d-flex align-items-center">
                                        <i class="fas fa-calendar-week me-2"></i><span>Week</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card h-100 bg-white">
                            <div class="card-body d-flex flex-column position-relative">
                                <div class="rounded-circle p-3 d-inline-flex" style="background: rgba(54, 185, 204, 0.1)">
                                    <i class="fas fa-chart-line text-info"></i>
                                </div>
                                <h2 class="stats-value mt-3">Rs. {{ number_format($monthlyStats['sales'], 2) }}</h2>
                                <p class="stats-label">Monthly Sales</p>
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2 d-flex align-items-center">
                                        <i class="fas fa-calendar-alt me-2"></i><span>Month</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card h-100 bg-white">
                            <div class="card-body d-flex flex-column position-relative">
                                <div class="rounded-circle p-3 d-inline-flex" style="background: rgba(246, 194, 62, 0.1)">
                                    <i class="fas fa-boxes text-warning"></i>
                                </div>
                                <h2 class="stats-value mt-3">{{ number_format($productsCount) }}</h2>
                                <p class="stats-label">Total Products</p>
                                <div class="position-absolute top-0 end-0 p-3">
                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2 d-flex align-items-center">
                                        <i class="fas fa-box me-2"></i><span>Stock</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-header bg-white py-3 border-0">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle p-2 bg-primary bg-opacity-10 me-3" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-crown text-primary"></i>
                                    </div>
                                    <h5 class="mb-0 fw-bold">Top Selling Products</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th class="text-center">Quantity Sold</th>
                                                <th class="text-end">Profit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topProducts as $product)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2 text-primary"><i class="fas fa-box"></i></span>
                                                        <span>{{ $product->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $product->total_quantity }}</td>
                                                <td class="text-end fw-medium">Rs. {{ number_format($product->total_profit, 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 rounded-3 h-100">
                            <div class="card-header bg-white py-3 border-0">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle p-2 bg-danger bg-opacity-10 me-3" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-exclamation-triangle text-danger"></i>
                                    </div>
                                    <h5 class="mb-0 fw-bold">Low Stock Alert</h5>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th class="text-center">Available Stock</th>
                                                <th class="text-end">Sale Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lowStockProducts as $product)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2 {{ $product->quantity <= 5 ? 'text-danger' : 'text-warning' }}"><i class="fas fa-box"></i></span>
                                                        <span>{{ $product->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge rounded-pill bg-{{ $product->quantity <= 5 ? 'danger' : 'warning' }} bg-opacity-10 text-{{ $product->quantity <= 5 ? 'danger' : 'warning' }} px-3 py-2">
                                                        {{ $product->quantity }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-medium">Rs. {{ number_format($product->sale_price, 2) }}</td>
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

            <!-- Customer Reports Tab -->
            <div class="tab-pane fade" id="customer" role="tabpanel" aria-labelledby="customer-tab">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 bg-success bg-opacity-10 me-3" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-users text-success"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Customer Performance Report</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-4">Select a customer and date range to generate a detailed performance report including purchase history, payment status, and product preferences.</p>
                        
                        <form action="{{ route('reports.customer') }}" method="GET" target="_blank" class="row g-3">
                            <div class="col-md-4">
                                <label for="customer_id" class="form-label fw-medium">Select Customer</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-user text-muted"></i>
                                    </span>
                                    <select name="customer_id" id="customer_id" class="form-select border-start-0 ps-0" required>
                                        <option value="">Select Customer</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="customer_start_date" class="form-label fw-medium">Start Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-calendar-alt text-muted"></i>
                                    </span>
                                    <input type="date" class="form-control border-start-0 ps-0" id="customer_start_date" name="start_date" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="customer_end_date" class="form-label fw-medium">End Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-calendar-check text-muted"></i>
                                    </span>
                                    <input type="date" class="form-control border-start-0 ps-0" id="customer_end_date" name="end_date" value="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <div class="d-flex flex-column gap-3">
                                    <button type="submit" class="btn btn-primary px-4 d-flex align-items-center">
                                        <i class="fas fa-file-alt me-2"></i>
                                        <span>Generate Report</span>
                                    </button>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-success rounded-pill px-3 date-preset-btn" data-target="customer" data-days="7">Last 7 Days</a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-success rounded-pill px-3 date-preset-btn" data-target="customer" data-days="30">Last 30 Days</a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-success rounded-pill px-3 date-preset-btn" data-target="customer" data-days="90">Last Quarter</a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-success rounded-pill px-3 date-preset-btn" data-target="customer" data-period="month">This Month</a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-success rounded-pill px-3 date-preset-btn" data-target="customer" data-period="year">Year to Date</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Company Reports Tab -->
            <div class="tab-pane fade" id="company" role="tabpanel" aria-labelledby="company-tab">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 bg-info bg-opacity-10 me-3" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-building text-info"></i>
                            </div>
                            <h5 id="company-report-title" class="mb-0 fw-bold">Company Performance Report</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <p class="text-muted mb-4">Select a company and date range to generate a detailed performance report including product inventory, sales analysis, and financial summary.</p>
                        
                        <form action="{{ route('reports.company') }}" method="GET" target="_blank" class="row g-3">
                            <div class="col-md-4">
                                <label for="company_id" class="form-label fw-medium">Select Company</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-building text-muted"></i>
                                    </span>
                                    <select name="company_id" id="company_id" class="form-select border-start-0 ps-0" required>
                                        <option value="">Select Company</option>
                                        @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="company_start_date" class="form-label fw-medium">Start Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-calendar-alt text-muted"></i>
                                    </span>
                                    <input type="date" class="form-control border-start-0 ps-0" id="company_start_date" name="start_date" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="company_end_date" class="form-label fw-medium">End Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-calendar-check text-muted"></i>
                                    </span>
                                    <input type="date" class="form-control border-start-0 ps-0" id="company_end_date" name="end_date" value="{{ now()->format('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <div class="d-flex flex-column gap-3">
                                    <button type="submit" class="btn btn-info text-white px-4 d-flex align-items-center">
                                        <i class="fas fa-file-alt me-2"></i>
                                        <span>Generate Report</span>
                                    </button>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-info rounded-pill px-3 date-preset-btn" data-target="company" data-days="7">Last 7 Days</a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-info rounded-pill px-3 date-preset-btn" data-target="company" data-days="30">Last 30 Days</a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-info rounded-pill px-3 date-preset-btn" data-target="company" data-days="90">Last Quarter</a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-info rounded-pill px-3 date-preset-btn" data-target="company" data-period="month">This Month</a>
                                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-info rounded-pill px-3 date-preset-btn" data-target="company" data-period="year">Year to Date</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Bootstrap components
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        
        // Initialize tabs
        var tabEl = document.querySelectorAll('#reportTabs .nav-link')
        tabEl.forEach(function(el) {
            el.addEventListener('click', function(event) {
                event.preventDefault();
                var tab = new bootstrap.Tab(el);
                tab.show();
            });
        });
        
        // Date preset buttons - unified handler for both customer and company
        $(document).on('click', '.date-preset-btn', function(e) {
            e.preventDefault();
            
            const target = $(this).data('target');
            const days = $(this).data('days');
            const period = $(this).data('period');
            const today = new Date();
            let startDate = new Date();
            
            if (days) {
                startDate.setDate(today.getDate() - days);
            } else if (period === 'month') {
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            } else if (period === 'year') {
                startDate = new Date(today.getFullYear(), 0, 1);
            }
            
            // Highlight the active button
            $(`.date-preset-btn[data-target="${target}"]`).removeClass('active');
            $(this).addClass('active');
            
            if (target === 'customer') {
                $('#customer_start_date').val(formatDate(startDate));
                $('#customer_end_date').val(formatDate(today));
                console.log('Customer date preset:', days || period);
            } else if (target === 'company') {
                $('#company_start_date').val(formatDate(startDate));
                $('#company_end_date').val(formatDate(today));
                console.log('Company date preset:', days || period);
            }
            
            console.log('Set start date to:', formatDate(startDate));
            console.log('Set end date to:', formatDate(today));
        });
        
        // Helper function to format date as YYYY-MM-DD
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    });
</script>
@endsection