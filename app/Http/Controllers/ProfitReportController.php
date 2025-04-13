<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Company;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Services\ProfitCalculationService;

class ProfitReportController extends Controller
{
    protected $profitCalculationService;

    /**
     * Constructor to inject dependencies
     */
    public function __construct(ProfitCalculationService $profitCalculationService)
    {
        $this->profitCalculationService = $profitCalculationService;
    }
    /**
     * Display the profit tracking dashboard
     */
    public function index(Request $request)
    {
        // Set default date range (30 days)
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::today()->endOfDay();
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::today()->subDays(30)->startOfDay();
        
        // Get profit data with accurate discount calculations
        $profitData = $this->profitCalculationService->calculateProfitForDateRange($startDate, $endDate);
        
        // Calculate summary statistics
        $totalSales = Sale::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalRevenue = $profitData['total_revenue'];
        $totalProfit = $profitData['total_profit_after_discount'];
        $profitMargin = $profitData['gross_margin_percentage'];
        
        // Calculate previous period for growth comparison
        $periodLength = $endDate->diffInDays($startDate) + 1;
        $prevEndDate = $startDate->copy()->subDay();
        $prevStartDate = $prevEndDate->copy()->subDays($periodLength - 1);
        
        // Get previous period profit data with accurate discount calculations
        $prevProfitData = $this->profitCalculationService->calculateProfitForDateRange($prevStartDate, $prevEndDate);
        
        // Calculate previous period statistics
        $prevTotalSales = Sale::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $prevTotalRevenue = $prevProfitData['total_revenue'];
        $prevTotalProfit = $prevProfitData['total_profit_after_discount'];
        $prevProfitMargin = $prevProfitData['gross_margin_percentage'];
        
        // Calculate growth percentages
        $revenueGrowth = $prevTotalRevenue > 0 ? (($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100 : 0;
        $profitGrowth = $prevTotalProfit > 0 ? (($totalProfit - $prevTotalProfit) / $prevTotalProfit) * 100 : 0;
        $marginGrowth = $prevProfitMargin > 0 ? (($profitMargin - $prevProfitMargin) / $prevProfitMargin) * 100 : 0;
        $salesGrowth = $prevTotalSales > 0 ? (($totalSales - $prevTotalSales) / $prevTotalSales) * 100 : 0;
        
        // Get top 5 most profitable products with accurate profit calculations
        $topProfitProducts = $this->profitCalculationService->getTopProductsByProfit($startDate, $endDate, 5);
        
        // Calculate profit by company
        $profitByCompany = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('companies', 'products.company_id', '=', 'companies.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                'companies.id',
                'companies.name',
                DB::raw('SUM(sale_items.quantity) as items_sold'),
                DB::raw('SUM(sale_items.total) as revenue'),
                DB::raw('SUM(sale_items.quantity * products.purchase_price) as cost'),
                DB::raw('SUM(sale_items.profit_margin) as profit')
            )
            ->groupBy('companies.id', 'companies.name')
            ->orderByDesc('profit')
            ->get();
        
        // Calculate margin for each company
        foreach ($profitByCompany as $company) {
            $company->margin = $company->revenue > 0 ? ($company->profit / $company->revenue) * 100 : 0;
        }
        
        // Calculate monthly profit data
        $monthlyProfitData = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(sales.created_at, "%b %Y") as month'),
                DB::raw('SUM(sale_items.total) as revenue'),
                DB::raw('SUM(sale_items.profit_margin) as profit')
            )
            ->groupBy('month')
            ->orderBy(DB::raw('MIN(sales.created_at)'))
            ->get();
        
        // Calculate margin for each month
        foreach ($monthlyProfitData as $month) {
            $month->margin = $month->revenue > 0 ? ($month->profit / $month->revenue) * 100 : 0;
        }
        
        // Get recent sales data
        $recentSales = Sale::with('customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->limit(5)
            ->get();
        
        // Calculate profit for each recent sale with accurate discount calculations
        foreach ($recentSales as $sale) {
            // Calculate proper sale profit by getting sale items and their discounts
            $saleData = $this->profitCalculationService->calculateProfitForDateRange(
                $sale->created_at, 
                $sale->created_at->copy()->endOfDay(), 
                $sale->id
            );
            $sale->profit = $saleData['total_profit_after_discount'];
        }
        
        // Calculate weekly profit data
        $weeklyProfitData = $this->getWeeklyProfitData($startDate, $endDate);
        
        return view('dashboard.reports.profit_dashboard', compact(
            'totalSales',
            'totalRevenue',
            'totalProfit',
            'profitMargin',
            'topProfitProducts',
            'profitByCompany',
            'monthlyProfitData',
            'weeklyProfitData',
            'recentSales',
            'startDate',
            'endDate',
            'revenueGrowth',
            'profitGrowth',
            'marginGrowth',
            'salesGrowth'
        ));
    }
    
    /**
     * Generate printable profit report
     */
    public function printReport(Request $request)
    {
        // Set default date range (30 days)
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : Carbon::today()->endOfDay();
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : Carbon::today()->subDays(30)->startOfDay();
        
        // Get all sales data within date range
        $salesData = SaleItem::whereHas('sale', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->with(['sale', 'product'])
            ->get();
        
        // Calculate summary statistics
        $totalSales = Sale::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalRevenue = $salesData->sum('total');
        $totalProfit = $salesData->sum('profit_margin');
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
        
        // Calculate previous period for growth comparison
        $periodLength = $endDate->diffInDays($startDate) + 1;
        $prevEndDate = $startDate->copy()->subDay();
        $prevStartDate = $prevEndDate->copy()->subDays($periodLength - 1);
        
        // Get previous period sales data
        $prevSalesData = SaleItem::whereHas('sale', function($query) use ($prevStartDate, $prevEndDate) {
                $query->whereBetween('created_at', [$prevStartDate, $prevEndDate]);
            })
            ->with(['sale', 'product'])
            ->get();
        
        // Calculate previous period statistics
        $prevTotalSales = Sale::whereBetween('created_at', [$prevStartDate, $prevEndDate])->count();
        $prevTotalRevenue = $prevSalesData->sum('total');
        $prevTotalProfit = $prevSalesData->sum('profit_margin');
        $prevProfitMargin = $prevTotalRevenue > 0 ? ($prevTotalProfit / $prevTotalRevenue) * 100 : 0;
        
        // Calculate growth percentages
        $revenueGrowth = $prevTotalRevenue > 0 ? (($totalRevenue - $prevTotalRevenue) / $prevTotalRevenue) * 100 : 0;
        $profitGrowth = $prevTotalProfit > 0 ? (($totalProfit - $prevTotalProfit) / $prevTotalProfit) * 100 : 0;
        $marginGrowth = $prevProfitMargin > 0 ? (($profitMargin - $prevProfitMargin) / $prevProfitMargin) * 100 : 0;
        $salesGrowth = $prevTotalSales > 0 ? (($totalSales - $prevTotalSales) / $prevTotalSales) * 100 : 0;
        
        // Get most profitable products
        $topProfitProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_amount'),
                DB::raw('SUM(sale_items.profit_margin) as total_profit')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_profit')
            ->limit(10)
            ->get();
        
        // Calculate profit by company
        $profitByCompany = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('companies', 'products.company_id', '=', 'companies.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                'companies.id',
                'companies.name',
                DB::raw('SUM(sale_items.quantity) as items_sold'),
                DB::raw('SUM(sale_items.total) as revenue'),
                DB::raw('SUM(sale_items.quantity * products.purchase_price) as cost'),
                DB::raw('SUM(sale_items.profit_margin) as profit')
            )
            ->groupBy('companies.id', 'companies.name')
            ->orderByDesc('profit')
            ->get();
        
        // Calculate margin for each company
        foreach ($profitByCompany as $company) {
            $company->margin = $company->revenue > 0 ? ($company->profit / $company->revenue) * 100 : 0;
        }
        
        // Calculate monthly profit data
        $monthlyProfitData = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(sales.created_at, "%b %Y") as month'),
                DB::raw('SUM(sale_items.total) as revenue'),
                DB::raw('SUM(sale_items.profit_margin) as profit')
            )
            ->groupBy('month')
            ->orderBy(DB::raw('MIN(sales.created_at)'))
            ->get();
        
        // Calculate margin for each month
        foreach ($monthlyProfitData as $month) {
            $month->margin = $month->revenue > 0 ? ($month->profit / $month->revenue) * 100 : 0;
        }
        
        // Calculate weekly profit data
        $weeklyProfitData = $this->getWeeklyProfitData($startDate, $endDate);
        
        return view('dashboard.reports.profit_dashboard', compact(
            'totalSales',
            'totalRevenue',
            'totalProfit',
            'profitMargin',
            'topProfitProducts',
            'profitByCompany',
            'monthlyProfitData',
            'weeklyProfitData',
            'startDate',
            'endDate',
            'revenueGrowth',
            'profitGrowth',
            'marginGrowth',
            'salesGrowth'
        ));
    }
    
    /**
     * Get weekly profit data for the given date range
     */
    private function getWeeklyProfitData($startDate, $endDate)
    {
        // Create an array to hold weekly data
        $weeklyData = [];
        
        // Generate weeks in the date range
        $interval = CarbonPeriod::create($startDate->copy()->startOfWeek(), '1 week', $endDate);
        
        foreach ($interval as $weekStart) {
            $weekEnd = $weekStart->copy()->endOfWeek();
            
            // Adjust the last week to end at the $endDate
            if ($weekEnd->gt($endDate)) {
                $weekEnd = $endDate->copy();
            }
            
            // Only include complete or partial weeks within the date range
            if ($weekStart->lte($endDate) && $weekEnd->gte($startDate)) {
                // Get accurate profit data for this week
                $weekProfitData = $this->profitCalculationService->calculateProfitForDateRange($weekStart, $weekEnd);
            
                // Calculate weekly totals with accurate discount handling
                $weekRevenue = $weekProfitData['total_revenue'];
                $weekProfit = $weekProfitData['total_profit_after_discount'];
                $weekMargin = $weekProfitData['gross_margin_percentage'];
                
                // Add to weekly data array
                $weeklyData[] = (object)[
                    'week_start' => $weekStart->format('M d'),
                    'week_end' => $weekEnd->format('M d'),
                    'revenue' => $weekRevenue,
                    'profit' => $weekProfit,
                    'margin' => $weekMargin
                ];
            }
        }
        
        return collect($weeklyData);
    }
}
