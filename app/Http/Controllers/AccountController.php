<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
class AccountController extends Controller
{
    //
    public function index(){
        $accounts = Account::all();
        return view('dashboard.accounts.index', compact('accounts'));
    }
}
