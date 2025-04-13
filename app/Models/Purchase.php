<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
	protected $fillable = [
		'company_id',
		'total_amount',
		'paid_amount',
		'pending_amount',
		'purchase_date',
		'reference_no'
	];
	
	/**
	 * Get the company transactions for this purchase
	 */
	public function transactions(): HasMany
	{
		return $this->hasMany(CompanyTransaction::class, 'purchase_id');
	}
	
	/**
	 * Get the items for this purchase through company transactions
	 */
	public function items(): HasManyThrough
	{
		return $this->hasManyThrough(
			PurchaseItem::class,
			CompanyTransaction::class,
			'purchase_id', // Foreign key on CompanyTransaction table
			'company_transaction_id', // Foreign key on PurchaseItem table
			'id', // Local key on Purchase table
			'id' // Local key on CompanyTransaction table
		);
	}
	
	/**
	 * Get the company that owns the purchase
	 */
	public function company(): BelongsTo
	{
		return $this->belongsTo(Company::class);
	}
}
