<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Company;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

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
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.profit_margin) as total_profit'))
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

        return view('report.index', compact(
            'dailyStats',
            'weeklyStats',
            'monthlyStats',
            'topProducts',
            'mostProfitableProducts',
            'lowStockProducts',
            'salesTrend',
            'profitTrend'
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
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_quantity'), DB::raw('SUM(sale_items.profit_margin) as total_profit'))
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
}
