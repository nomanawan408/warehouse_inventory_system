<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Account;

class Company extends Model
{
    //
    public function account()
    {
        return $this->hasOne(Account::class);
    }
}
