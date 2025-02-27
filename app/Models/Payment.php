<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CustomerAccount;

class Payment extends Model
{
    //
    protected $fillable = [
        'customer_id',
        'sale_id',
        'amount_paid',
        'payment_type',
        'payment_date'
    ];

    public function account()
    {
        return $this->belongsTo(CustomerAccount::class);
    }

    public function getFormattedDateAttribute()
    {
        return $this->payment_date->format('d M, Y');
    }
}
