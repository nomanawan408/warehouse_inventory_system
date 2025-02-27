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
}
