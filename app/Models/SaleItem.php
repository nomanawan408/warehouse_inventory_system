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
     * Calculate profit margin attribute based on original product prices
     */
    public function getProfitMarginAttribute()
    {
        $cost = $this->product->purchase_price * $this->quantity;
        $revenue = $this->product->sale_price * $this->quantity;
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
     * 
     * @param float $proportionalDiscount Additional invoice-level discount proportionally applied
     * @return float
     */
    public function calculateTotalProfit($proportionalDiscount = 0)
    {
        // Get profit after item-level discount
        $profitAfterItemDiscount = $this->profit_after_discount;
        
        // Apply proportional invoice-level discount
        return $profitAfterItemDiscount - $proportionalDiscount;
    }
}
