<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name',
        'purchase_price',
        'sale_price',
        'quantity',
        'company_id',
        'status',
    ];
}
