<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTransaction extends Model
{
    //
    protected $fillable = [
        'customer_id',
        'sale_id',
        'transaction_type',
        'amount',
        'payment_method',
        'reference',
        'transaction_date'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // public function getFormattedDateAttribute()
    // {
    //     return $this->transaction_date->format('d M, Y');
    // }

}
