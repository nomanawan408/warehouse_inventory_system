<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;    

class SalesController extends Controller
{
    //  
    public function index(){
        $sales = Cart::all();
        return view('dashboard.sales.index', compact('sales'));
    }
}
