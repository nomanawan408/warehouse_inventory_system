<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerProductDiscount extends Model
{
    protected $fillable = [
        'customer_id',
        'product_id',
        'discount',
    ];
}
