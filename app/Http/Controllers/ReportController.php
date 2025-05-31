<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Company;


class ReportController extends Controller
{
    /**
     * Display a listing of reports.
     */
    public function index()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Daily statistics
        $dailyStats = [
            'sales' => Sale::whereDate('created_at', $today)->sum('total_amount'),
            'purchases' => Purchase::whereDate('created_at', $today)->sum('total_amount'),
            'new_customers' => Customer::whereDate('created_at', $today)->count(),
            'profit_margin' => SaleItem::whereHas('sale', function($query) use ($today) {
                $query->whereDate('created_at', $today);
            })->sum('profit_margin'),
        ];

        // Weekly statistics
        $weeklyStats = [
            'sales' => Sale::where('created_at', '>=', $startOfWeek)->sum('total_amount'),
            'purchases' => Purchase::where('created_at', '>=', $startOfWeek)->sum('total_amount'),
            'new_customers' => Customer::where('created_at', '>=', $startOfWeek)->count(),
            'profit_margin' => SaleItem::whereHas('sale', function($query) use ($startOfWeek) {
                $query->where('created_at', '>=', $startOfWeek);
            })->sum('profit_margin'),
        ];

        // Monthly statistics
        $monthlyStats = [
            'sales' => Sale::where('created_at', '>=', $startOfMonth)->sum('total_amount'),
            'purchases' => Purchase::where('created_at', '>=', $startOfMonth)->sum('total_amount'),
            'new_customers' => Customer::where('created_at', '>=', $startOfMonth)->count(),
            'profit_margin' => SaleItem::whereHas('sale', function($query) use ($startOfMonth) {
                $query->where('created_at', '>=', $startOfMonth);
            })->sum('profit_margin'),
        ];

        // Top selling products
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.total) as total_amount'), DB::raw('SUM(sale_items.profit_margin) as total_profit'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Most profitable products
        $mostProfitableProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(sale_items.profit_margin) as total_profit'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_profit')
            ->limit(5)
            ->get();

        // Low stock products
        $lowStockProducts = Product::where('quantity', '<=', 10)
            ->orderBy('quantity')
            ->limit(5)
            ->get();

        // Sales trend data (last 7 days)
        $salesTrend = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total_sales')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Profit margin trend data (last 7 days)
        $profitTrend = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select(
                DB::raw('DATE(sales.created_at) as date'),
                DB::raw('SUM(sale_items.profit_margin) as total_profit')
            )
            ->where('sales.created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get customers and companies for report filters
        $customers = Customer::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        
        // Get total products count for dashboard stat
        $productsCount = Product::count();
        
        // Pending balance calculations removed - relocated to account pages

        return view('dashboard.reports.index', compact(
            'dailyStats', 
            'weeklyStats', 
            'monthlyStats', 
            'topProducts', 
            'mostProfitableProducts', 
            'lowStockProducts', 
            'salesTrend', 
            'profitTrend',
            'customers',
            'companies',
            'productsCount'
        ));
    }

    /**
     * Show the form for creating a new report.
     */
    public function create()
    {
        return view('report.create');
    }

    /**
     * Display a generated report.
     */
    public function view($id)
    {
        return view('report.view', compact('id'));
    }
    
    /**
     * Get report data based on date range for AJAX requests
     */
    public function getData(Request $request): JsonResponse
    {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::today();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::today()->endOfDay();
        
        // Daily statistics (based on selected date range)
        $dailyStats = [
            'sales' => Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'purchases' => Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('total_amount'),
            'new_customers' => Customer::whereBetween('created_at', [$startDate, $endDate])->count(),
            'profit_margin' => SaleItem::whereHas('sale', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })->sum('profit_margin'),
        ];

        // Weekly statistics (based on selected date range)
        $weeklyStats = [
            'sales' => Sale::whereBetween('created_at', [$startDate->copy()->subDays(7), $endDate])->sum('total_amount'),
            'purchases' => Purchase::whereBetween('created_at', [$startDate->copy()->subDays(7), $endDate])->sum('total_amount'),
            'new_customers' => Customer::whereBetween('created_at', [$startDate->copy()->subDays(7), $endDate])->count(),
            'profit_margin' => SaleItem::whereHas('sale', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate->copy()->subDays(7), $endDate]);
            })->sum('profit_margin'),
        ];

        // Monthly statistics (based on selected date range)
        $monthlyStats = [
            'sales' => Sale::whereBetween('created_at', [$startDate->copy()->subDays(30), $endDate])->sum('total_amount'),
            'purchases' => Purchase::whereBetween('created_at', [$startDate->copy()->subDays(30), $endDate])->sum('total_amount'),
            'new_customers' => Customer::whereBetween('created_at', [$startDate->copy()->subDays(30), $endDate])->count(),
            'profit_margin' => SaleItem::whereHas('sale', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate->copy()->subDays(30), $endDate]);
            })->sum('profit_margin'),
        ];

        // Top selling products for the date range
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.total) as total_amount'), DB::raw('SUM(sale_items.profit_margin) as total_profit'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Most profitable products for the date range
        $mostProfitableProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select('products.name', DB::raw('SUM(sale_items.profit_margin) as total_profit'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_profit')
            ->limit(5)
            ->get();
            
        // Pending balance calculations removed - relocated to account pages

        // Low stock products
        $lowStockProducts = Product::where('quantity', '<=', 10)
            ->orderBy('quantity')
            ->limit(5)
            ->get();

        // Sales trend data for the date range
        $salesTrend = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total_sales')
        )
            ->whereBetween('created_at', [$startDate->copy()->subDays(7), $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Profit margin trend data for the date range
        $profitTrend = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select(
                DB::raw('DATE(sales.created_at) as date'),
                DB::raw('SUM(sale_items.profit_margin) as total_profit')
            )
            ->whereBetween('sales.created_at', [$startDate->copy()->subDays(7), $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'dailyStats' => $dailyStats,
            'weeklyStats' => $weeklyStats,
            'monthlyStats' => $monthlyStats,
            'topProducts' => $topProducts,
            'mostProfitableProducts' => $mostProfitableProducts,
            'lowStockProducts' => $lowStockProducts,
            'salesTrend' => $salesTrend,
            'profitTrend' => $profitTrend
        ]);
    }

    /**
     * Get customer report based on date range
     */
    public function customerReport(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $customerId = $validated['customer_id'];
        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();

        $customer = Customer::findOrFail($customerId);
        
        // Get all sales for this customer in the date range
        $sales = Sale::where('customer_id', $customerId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Calculate summary statistics
        $totalSales = $sales->count();
        $totalAmount = $sales->sum('total_amount');
        $totalDiscount = $sales->sum('discount');
        $totalNetAmount = $sales->sum('net_total');
        $totalPending = $sales->sum('pending_amount');
        
        // Get most purchased products for this customer
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.customer_id', $customerId)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                'products.id', 
                'products.name', 
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_amount')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
            
        // Monthly trend for this customer
        $monthlyTrend = DB::table('sales')
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.customer_id', $customerId)
            ->whereBetween('sales.created_at', [$startDate->copy()->startOfYear(), $endDate])
            ->select(
                DB::raw('DATE_FORMAT(sales.created_at, "%b %Y") as month'),
                DB::raw('SUM(sale_items.total) as total_amount'),
                DB::raw('COUNT(DISTINCT sales.id) as sale_count')
            )
            ->groupBy('month')
            ->orderBy(DB::raw('MIN(sales.created_at)'))
            ->get();
            
        return view('dashboard.reports.customer_report', compact(
            'customer',
            'sales',
            'totalSales',
            'totalAmount',
            'totalDiscount',
            'totalNetAmount',
            'totalPending',
            'topProducts',
            'monthlyTrend',
            'startDate',
            'endDate'
        ));
    }
    
    /**
     * Get company report based on date range
     */
    public function companyReport(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $companyId = $validated['company_id'];
        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();

        $company = Company::findOrFail($companyId);
        
        // Get all products from this company
        $products = Product::where('company_id', $companyId)->get();
        $productIds = $products->pluck('id')->toArray();
        
        // Get sales items for this company's products
        $salesData = SaleItem::whereIn('product_id', $productIds)
            ->whereHas('sale', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with(['sale', 'product'])
            ->get();
            
        // Calculate summary statistics
        $totalQuantitySold = $salesData->sum('quantity');
        $totalRevenue = $salesData->sum('total');
        $totalProfit = $salesData->sum('profit_margin');
        
        // Get top selling products for this company
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereIn('sale_items.product_id', $productIds)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                'products.id', 
                'products.name', 
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_amount'),
                DB::raw('SUM(sale_items.profit_margin) as total_profit')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();
            
        // Monthly trend for this company
        $monthlyTrend = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereIn('products.id', $productIds)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(sales.created_at, "%Y-%m") as month'),
                DB::raw('SUM(sale_items.total) as total_amount'),
                DB::raw('SUM(sale_items.quantity) as total_quantity')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        // Current inventory for this company
        $currentInventory = Product::where('company_id', $companyId)
            ->select('id', 'name', 'quantity', 'purchase_price', 'sale_price')
            ->orderBy('name')
            ->get();
            
        return view('dashboard.reports.company_report', compact(
            'company',
            'totalQuantitySold',
            'totalRevenue',
            'totalProfit',
            'topProducts',
            'monthlyTrend',
            'currentInventory',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export customer report as print-friendly HTML
     */
    public function exportCustomerPdf(Request $request)
    {
        $customerId = $request->input('customer_id');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::today()->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::today()->endOfDay();
        
        $customer = Customer::findOrFail($customerId);
        
        // Get all sales for this customer within the date range
        $sales = Sale::where('customer_id', $customerId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate totals
        $totalSales = $sales->count();
        $totalAmount = $sales->sum('total_amount');
        $totalDiscount = $sales->sum('discount_amount');
        $totalNetAmount = $sales->sum('net_amount');
        $totalPending = $sales->sum('pending_amount');
        
        // Get most purchased products
        $mostPurchasedProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.customer_id', $customerId)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_amount')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
        
        // Monthly sales trend
        $monthlyTrend = DB::table('sales')
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.customer_id', $customerId)
            ->whereBetween('sales.created_at', [$startDate->copy()->startOfYear(), $endDate])
            ->select(
                DB::raw('DATE_FORMAT(sales.created_at, "%b %Y") as month'),
                DB::raw('SUM(sale_items.total) as total_amount'),
                DB::raw('COUNT(DISTINCT sales.id) as sale_count')
            )
            ->groupBy('month')
            ->orderBy(DB::raw('MIN(sales.created_at)'))
            ->get();
        
        // Return a print-friendly HTML view
        return view('dashboard.reports.print.customer_print', compact(
            'customer', 
            'sales', 
            'startDate', 
            'endDate', 
            'totalSales', 
            'totalAmount', 
            'totalDiscount', 
            'totalNetAmount', 
            'totalPending',
            'mostPurchasedProducts',
            'monthlyTrend'
        ));
    }
    
    /**
     * Export company report as print-friendly HTML
     */
    public function exportCompanyPdf(Request $request)
    {
        $companyId = $request->input('company_id');
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::today()->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::today()->endOfDay();
        
        $company = Company::findOrFail($companyId);
        
        // Get all products from this company
        $productIds = Product::where('company_id', $companyId)->pluck('id')->toArray();
        
        // Calculate total quantity sold, revenue and profit
        $salesData = SaleItem::whereIn('product_id', $productIds)
            ->whereHas('sale', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with(['sale', 'product'])
            ->get();
            
        $totalQuantitySold = $salesData->sum('quantity');
        $totalRevenue = $salesData->sum('total');
        $totalProfit = $salesData->sum('profit_margin');
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
        
        // Top selling products 
        $topSellingProducts = collect($productIds)->isEmpty() ? collect([]) : DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereIn('sale_items.product_id', $productIds)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_amount'),
                DB::raw('SUM(sale_items.profit_margin) as total_profit')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
        
        // Monthly trend for this company
        $monthlyTrend = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereIn('products.id', $productIds)
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(sales.created_at, "%Y-%m") as month'),
                DB::raw('SUM(sale_items.total) as total_amount'),
                DB::raw('SUM(sale_items.quantity) as total_quantity')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
    // Current inventory for this company
    $currentInventory = Product::where('company_id', $companyId)
        ->select('id', 'name', 'quantity', 'purchase_price', 'sale_price')
        ->orderBy('name')
        ->get();
        
    // Get all purchases from this company
    $purchases = Purchase::where('company_id', $companyId)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->with(['transactions', 'transactions.transactionItems', 'transactions.transactionItems.product'])
        ->orderBy('created_at', 'desc')
        ->get();
        
    // Calculate purchase totals
    $totalPurchases = $purchases->count();
    $totalPurchaseAmount = $purchases->sum('total_amount');
    $totalPurchasePaid = $purchases->sum('paid_amount');
    $totalPurchasePending = $purchases->sum('pending_amount');
        
    // Calculate total inventory value
    $totalInventoryValue = $currentInventory->sum(function($product) {
        return $product->quantity * $product->purchase_price;
    });
    
    $inventoryStatus = $currentInventory;
    
    return view('dashboard.reports.print.company_print', compact(
        'company',
        'startDate', 
        'endDate', 
        'totalQuantitySold', 
        'totalRevenue', 
        'totalProfit', 
        'profitMargin',
        'topSellingProducts',
        'inventoryStatus',
        'totalInventoryValue',
        'purchases',
        'totalPurchases',
        'totalPurchaseAmount',
        'totalPurchasePaid',
        'totalPurchasePending'
    ));
}


}
