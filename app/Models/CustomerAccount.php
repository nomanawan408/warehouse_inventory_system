<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\Payment;

class CustomerAccount extends Model
{
    //
    protected $fillable = [
        'customer_id',
        'total_purchases',
        'total_paid',
        'pending_balance',
        'last_payment_date'
    ];


    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function transactions()
    {
        return $this->hasMany(CustomerTransaction::class, 'customer_id', 'customer_id');
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
