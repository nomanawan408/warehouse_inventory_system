<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Company;
use App\Models\Payment;
use App\Models\PurchaseItem;

class CompanyTransaction extends Model
{
    protected $fillable = [
        'company_id',
        'purchase_id',
        'transaction_type',
        'amount',
        'transaction_date',
        'detail',
        'payment_method',
        'reference'
    ];

    protected $casts = [
        'transaction_date' => 'datetime'
    ];

    protected $dates = [
        'date'
    ];

    /**
     * Get the company that owns the transaction
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the purchase items for this transaction
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'company_transaction_id');
    }

    /**
     * Get the payments for this transaction
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
