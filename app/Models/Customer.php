<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
   
    public function account(){
        return $this->hasOne(CustomerAccount::class);
    }

    public function sales(){
        return $this->hasMany(Sale::class);
    }

    public function transactions(){
        return $this->hasMany(CustomerTransaction::class);
    }
}
