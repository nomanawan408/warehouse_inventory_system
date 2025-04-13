<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfitCalculationService
{
    /**
     * Calculate profit after all discounts for a specific date range
     * 
     * @param Carbon|string $startDate Start date
     * @param Carbon|string $endDate End date
     * @return array Returns total revenue, cost, and profit data
     */
    public function calculateProfitForDateRange($startDate, $endDate)
    {
        // Convert string dates to Carbon if necessary
        if (!$startDate instanceof Carbon) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }
        
        if (!$endDate instanceof Carbon) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }
        
        // Get all sales in the date range
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])->with('items.product')->get();
        
        // Initialize result variables
        $result = [
            'total_revenue' => 0,
            'total_cost' => 0,
            'total_profit_before_discount' => 0,
            'total_profit_after_discount' => 0,
            'total_item_discounts' => 0,
            'total_invoice_discounts' => 0
        ];
        
        foreach ($sales as $sale) {
            // Calculate invoice-level metrics
            $invoiceTotal = $sale->total_amount;
            $invoiceDiscount = $sale->discount;
            $invoiceNetTotal = $sale->net_total;
            
            // Add to totals
            $result['total_revenue'] += $invoiceNetTotal;
            $result['total_invoice_discounts'] += $invoiceDiscount;
            
            // Calculate item-level metrics
            $saleItemsTotalCost = 0;
            $saleItemsDiscounts = 0;
            
            foreach ($sale->items as $item) {
                // Calculate cost
                $itemCost = $item->product->purchase_price * $item->quantity;
                $saleItemsTotalCost += $itemCost;
                
                // Track item discounts
                $saleItemsDiscounts += $item->discount;
                
                // Add to profit before discount
                $profitBeforeDiscount = ($item->price * $item->quantity) - $itemCost;
                $result['total_profit_before_discount'] += $profitBeforeDiscount;
            }
            
            // Add to cost total
            $result['total_cost'] += $saleItemsTotalCost;
            $result['total_item_discounts'] += $saleItemsDiscounts;
            
            // Calculate profit after all discounts (both item and invoice level)
            $totalProfitForSale = $invoiceNetTotal - $saleItemsTotalCost;
            $result['total_profit_after_discount'] += $totalProfitForSale;
        }
        
        // Calculate other business metrics
        $result['gross_margin_percentage'] = ($result['total_revenue'] > 0) 
            ? ($result['total_profit_after_discount'] / $result['total_revenue']) * 100 
            : 0;
            
        $result['discount_impact'] = $result['total_profit_before_discount'] - $result['total_profit_after_discount'];
        
        return $result;
    }
    
    /**
     * Get top products by profit after discount
     * 
     * @param Carbon|string $startDate Start date
     * @param Carbon|string $endDate End date
     * @param int $limit Number of products to return
     * @return \Illuminate\Support\Collection
     */
    public function getTopProductsByProfit($startDate, $endDate, $limit = 5)
    {
        // Convert string dates to Carbon if necessary
        if (!$startDate instanceof Carbon) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }
        
        if (!$endDate instanceof Carbon) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }
        
        return DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.total) as total_revenue'),
                DB::raw('SUM(sale_items.profit_margin) as total_profit')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_profit')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Get profit trend by day for a date range
     * 
     * @param Carbon|string $startDate Start date
     * @param Carbon|string $endDate End date
     * @return \Illuminate\Support\Collection
     */
    public function getProfitTrendByDay($startDate, $endDate)
    {
        // Convert string dates to Carbon if necessary
        if (!$startDate instanceof Carbon) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }
        
        if (!$endDate instanceof Carbon) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }
        
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(sales.created_at) as date'),
                DB::raw('SUM(sale_items.profit_margin) as total_profit'),
                DB::raw('SUM(sale_items.total) as total_revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}
