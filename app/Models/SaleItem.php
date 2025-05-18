<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    //
    protected $fillable = [
        'sale_id',
        'product_id',
        'company_id',
        'quantity',
        'price',
        'discount',
        'tax',
        'profit_margin',
        'total'
    ];

    protected $appends = ['profit_after_discount'];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    
    public function sale(){
        return $this->belongsTo(Sale::class);
    }

    /**
     * Calculate profit margin attribute based on actual sale price and quantity
     * This is the profit before any discounts are applied
     */
    public function getProfitMarginAttribute()
    {
        // Use the actual sale price (not the product's current sale price which might have changed)
        $cost = $this->product->purchase_price * $this->quantity;
        $revenue = $this->price * $this->quantity;
        return $revenue - $cost;
    }

    /**
     * Calculate profit after applying item-level discounts
     * 
     * @return float
     */
    public function getProfitAfterDiscountAttribute()
    {
        // Calculate base cost
        $cost = $this->product->purchase_price * $this->quantity;
        
        // Calculate actual revenue after item discount
        $revenue = ($this->price * $this->quantity) - $this->discount;
        
        return $revenue - $cost;
    }
    
    /**
     * Calculate profit after both item-level and invoice-level discounts
     * This method properly distributes the invoice-level discount across items
     * 
     * @param float $invoiceDiscount Total invoice-level discount amount
     * @param float $invoiceSubtotal Total invoice subtotal before invoice-level discount
     * @return float
     */
    public function calculateTotalProfit($invoiceDiscount = 0, $invoiceSubtotal = 0)
    {
        // Get profit after item-level discount
        $profitAfterItemDiscount = $this->profit_after_discount;
        
        // If no invoice discount or subtotal is zero, return profit after item discount
        if ($invoiceDiscount <= 0 || $invoiceSubtotal <= 0) {
            return $profitAfterItemDiscount;
        }
        
        // Calculate this item's proportion of the total invoice
        $itemTotal = ($this->price * $this->quantity) - $this->discount;
        $proportion = $itemTotal / $invoiceSubtotal;
        
        // Calculate this item's share of the invoice discount
        $itemInvoiceDiscount = $invoiceDiscount * $proportion;
        
        // Apply proportional invoice-level discount
        return $profitAfterItemDiscount - $itemInvoiceDiscount;
    }
}
