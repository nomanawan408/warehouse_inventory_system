<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CompanyAccount;

class Company extends Model
{
    //
    public function account()
    {
        return $this->hasOne(CompanyAccount::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
