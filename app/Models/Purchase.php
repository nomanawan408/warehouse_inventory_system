<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
	protected $fillable = [
		'company_id',
		'total_amount',
		'paid_amount',
		'pending_amount',
		'purchase_date'
	];
}
