<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerAccount;
use App\Models\Payment;

class AccountController extends Controller
{
    //
    public function index(){
        $accounts = CustomerAccount::all();
        return view('dashboard.accounts.index', compact('accounts'));
    }

    public function addPayment($id)
    {
        $account = CustomerAccount::findOrFail($id);
        return view('dashboard.accounts.add_payment', compact('account'));
    }

    public function storePayment(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'payment_amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);
    
        // Find the customer's account
        $account = CustomerAccount::findOrFail($id);
        $customer = $account->customer;
    
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found.');
        }
    
        // Prevent overpayment
        if ($request->payment_amount > $account->pending_balance) {
            return redirect()->back()->with('error', 'Payment exceeds pending balance.');
        }
    
        // Create a new payment record
        $payment = Payment::create([
            'customer_id' => $customer->id,
            'sale_id' => $request->sale_id ?? null, // Associate with sale if provided
            'amount_paid' => $request->payment_amount,
            'payment_type' => 'Cash', // Assume all payments are in cash
            'payment_date' => $request->payment_date,
        ]);
    
        // Update the customer's account balance
        $account->total_paid += $request->payment_amount;
        $account->pending_balance -= $request->payment_amount;
        $account->last_payment_date = $request->payment_date;
    
        // Ensure pending balance does not go negative
        if ($account->pending_balance < 0) {
            $account->pending_balance = 0;
        }
    
        $account->save();
    
        return redirect()->route('accounts.index')->with('success', 'Payment recorded successfully.');
    }
    
    public function transactions($id)
    {
        // Find the customer account
        $account = CustomerAccount::with('transactions')->findOrFail($id);

        // Ensure transactions exist
        if ($account->transactions->isEmpty()) {
            return redirect()->back()->with('info', 'No transactions found for this account.');
        }

        // Pass data to the view
        return view('dashboard.accounts.show', compact('account'));
    }

}
