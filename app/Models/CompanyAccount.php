<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'total_purchases',
        'total_paid',
        'pending_balance',
        'last_payment_date'
    ];

    protected $dates = [
        'last_payment_date'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
