<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    //
    protected $fillable = [
        'company_transaction_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_amount'
    ];

    /**
     * Get the transaction that owns the item
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(CompanyTransaction::class, 'company_transaction_id');
    }

    /**
     * Get the product associated with this item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
