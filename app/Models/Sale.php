<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\SaleItem;

class Sale extends Model
{
    //
    protected $fillable = [
        'customer_id',
        'total_amount',
        'discount',
        'tax',
        'net_total',
        'amount_paid',
        'pending_amount'
    ];
    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function transactions(){
        return $this->hasMany(CustomerTransaction::class);
    }


    public function saleItems(){
        return $this->hasMany(SaleItem::class);
    }

    // Define the relationship with SaleItem
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
