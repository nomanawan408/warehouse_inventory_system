<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    public function account()
    {
        return $this->hasOne(Account::class);
    }
}
