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
    public function calculateProfitForDateRange($startDate, $endDate, $specificSaleId = null)
    {
        // Convert string dates to Carbon if necessary
        if (!$startDate instanceof Carbon) {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }
        
        if (!$endDate instanceof Carbon) {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }
        
        // Get all sales in the date range, optionally filter by specific sale ID
        $salesQuery = Sale::whereBetween('created_at', [$startDate, $endDate])->with('items.product');
        if ($specificSaleId) {
            $salesQuery->where('id', $specificSaleId);
        }
        $sales = $salesQuery->get();
        
        // Initialize result variables
        $result = [
            'total_revenue' => 0,
            'total_cost' => 0,
            'total_profit_before_discount' => 0,
            'total_profit_after_discount' => 0,
            'total_item_discounts' => 0,
            'total_invoice_discounts' => 0,
            'discount_impact' => 0
        ];
        
        foreach ($sales as $sale) {
            // Calculate invoice-level metrics
            $invoiceSubtotal = $sale->total_amount; // This is before any discounts
            $invoiceDiscount = $sale->discount;
            $invoiceNetTotal = $sale->net_total; // This is after all discounts
            
            // Add to revenue total (after all discounts)
            $result['total_revenue'] += $invoiceNetTotal;
            $result['total_invoice_discounts'] += $invoiceDiscount;
            
            // Calculate item-level metrics
            $saleItemsTotalCost = 0;
            $saleItemsDiscounts = 0;
            $saleItemsSubtotal = 0; // Sum of all items price * quantity before discounts
            
            foreach ($sale->items as $item) {
                // Calculate item cost (based on purchase price)
                $itemCost = $item->product->purchase_price * $item->quantity;
                $saleItemsTotalCost += $itemCost;
                
                // Calculate item revenue before any discounts
                $itemSubtotal = $item->price * $item->quantity;
                $saleItemsSubtotal += $itemSubtotal;
                
                // Track item-level discounts
                $saleItemsDiscounts += $item->discount;
                
                // Calculate profit before any discounts
                $profitBeforeDiscount = $itemSubtotal - $itemCost;
                $result['total_profit_before_discount'] += $profitBeforeDiscount;
            }
            
            // Add to cost total
            $result['total_cost'] += $saleItemsTotalCost;
            $result['total_item_discounts'] += $saleItemsDiscounts;
            
            // Calculate profit after all discounts (both item and invoice level)
            // This is the actual net revenue minus the total cost
            $totalProfitForSale = $invoiceNetTotal - $saleItemsTotalCost;
            $result['total_profit_after_discount'] += $totalProfitForSale;
            
            // Calculate how much profit was lost due to discounts
            $profitBeforeAllDiscounts = $saleItemsSubtotal - $saleItemsTotalCost;
            $discountImpact = $profitBeforeAllDiscounts - $totalProfitForSale;
            $result['discount_impact'] += $discountImpact;
        }
        
        // Calculate other business metrics
        $result['gross_margin_percentage'] = ($result['total_revenue'] > 0) 
            ? ($result['total_profit_after_discount'] / $result['total_revenue']) * 100 
            : 0;
            
        // Ensure discount_impact is calculated correctly
        if (!isset($result['discount_impact']) || $result['discount_impact'] <= 0) {
            $result['discount_impact'] = $result['total_profit_before_discount'] - $result['total_profit_after_discount'];
        }
        
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
