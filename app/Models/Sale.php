<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    public function items(){
        return $this->hasMany(SaleItem::class);
    }

}
