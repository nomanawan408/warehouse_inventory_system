<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerAccount;
use App\Models\Payment;
use App\Models\CustomerTransaction;

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
        
        // Create a customer transaction record
        CustomerTransaction::create([
            'customer_id' => $customer->id,
            'sale_id' => $request->sale_id ?? null,
            'transaction_type' => 'credit',
            'amount' => $request->payment_amount,
            'payment_method' => 'cash',
            'detail' => 'Payment received',
            'reference' => 'Payment received',
            'transaction_date' => $request->payment_date,
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
    
    // public function transactions($id)
    // {
    //     // Find the customer account
    //     $account = CustomerAccount::with('transactions')->findOrFail($id);

    //     // Ensure transactions exist
    //     if ($account->transactions->isEmpty()) {
    //         return redirect()->back()->with('info', 'No transactions found for this account.');
    //     }

    //     // Pass data to the view
    //     return view('dashboard.accounts.show', compact('account'));
    // }

    public function transactions($id)
    {
        $account = CustomerAccount::findOrFail($id);
    
        // Fetch transactions related to the customer account, sorted by date
        $transactions = CustomerTransaction::where('customer_id', $account->customer_id)
                        ->orderBy('transaction_date', 'asc')
                        ->get();
      
        // Initialize variables for totals
        $balance = 0;
        $totalPurchases = 0;
        $totalPaid = 0;
        $formattedTransactions = [];
    
        foreach ($transactions as $transaction) {
            // Update totals based on transaction type
            if ($transaction->transaction_type == 'debit') {
                $totalPurchases += $transaction->amount;
                $balance += $transaction->amount;
                $debit = $transaction->amount;
                $credit = null;
                
                // Determine detail text
                if ($transaction->detail == 'Pending amount added') {
                    $detail = $transaction->reference;
                } elseif ($transaction->detail) {
                    $detail = $transaction->detail;
                } else {
                    $detail = 'Purchase';
                }

            } else {
                $totalPaid += $transaction->amount;
                $balance -= $transaction->amount;
                $debit = null;
                $credit = $transaction->amount;
                
                // Determine detail text
                if ($transaction->detail == 'Payment received') {
                    $detail = $transaction->reference;
                } elseif ($transaction->detail) {
                    $detail = $transaction->detail;
                } else {
                    $detail = 'Payment Received';
                }
            }
    
            // Add sale reference if available
            if ($transaction->sale_id) {
                $detail .= ' - Sale #' . $transaction->sale_id;
            }
    
            // Format the transaction data
            $formattedTransactions[] = [
                'id' => $transaction->id,
                'transaction_date' => \Carbon\Carbon::parse($transaction->transaction_date)->format('Y-m-d H:i:s'),
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'detail' => $detail,
            ];
        }

        return view('dashboard.accounts.show', compact('account', 'formattedTransactions', 'transactions'));
    }

    public function storePendingAmount(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'pending_amount' => 'required|numeric|min:1',
            'pending_date' => 'required|date',
        ]);

        // Find the customer's account
        $account = CustomerAccount::findOrFail($id);
        $customer = $account->customer;
     
        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found.');
        }

        // Create transaction record for pending amount
        CustomerTransaction::create([
            'customer_id' => $customer->id,
            'transaction_type' => 'debit',
            'amount' => $request->pending_amount,
            'payment_method' => 'pending',
            'detail' => 'Pending amount added',
            'reference' => 'Manual pending amount entry',
            'transaction_date' => now(),
        ]);

        // Update the customer's account pending balance
        $account->total_purchases += $request->pending_amount;
        $account->pending_balance += $request->pending_amount;
        $account->last_payment_date = $request->pending_date;

        // Ensure the account is saved
        $account->save();

        return redirect()->route('accounts.index')->with('success', 'Pending amount added successfully.');
    }

    /**
     * Show the form for editing a transaction.
     */
    public function editTransaction($accountId, $transactionId)
    {
        $account = CustomerAccount::findOrFail($accountId);
        $transaction = CustomerTransaction::findOrFail($transactionId);
        
        // Ensure the transaction belongs to the account
        if ($transaction->customer_id != $account->customer_id) {
            return redirect()->back()->with('error', 'Transaction does not belong to this account.');
        }
        
        return view('dashboard.accounts.edit_transaction', compact('account', 'transaction'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function updateTransaction(Request $request, $accountId, $transactionId)
    {
        // Validate the request
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'detail' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        $account = CustomerAccount::findOrFail($accountId);
        $transaction = CustomerTransaction::findOrFail($transactionId);
        
        // Ensure the transaction belongs to the account
        if ($transaction->customer_id != $account->customer_id) {
            return redirect()->back()->with('error', 'Transaction does not belong to this account.');
        }

        // Store original values for account adjustment
        $originalAmount = $transaction->amount;
        $originalType = $transaction->transaction_type;
        
        // Update the transaction
        $transaction->amount = $request->amount;
        $transaction->transaction_date = $request->transaction_date;
        $transaction->detail = $request->detail;
        $transaction->reference = $request->reference;
        $transaction->save();

        // Adjust account balances based on transaction type
        if ($originalType == 'credit') {
            // If it was a payment (credit), adjust the paid and pending amounts
            $account->total_paid -= $originalAmount;
            $account->pending_balance += $originalAmount;
        } else {
            // If it was a purchase (debit), adjust the purchases and pending amounts
            $account->total_purchases -= $originalAmount;
            $account->pending_balance -= $originalAmount;
        }

        // Apply the new values
        if ($transaction->transaction_type == 'credit') {
            // If it's now a payment (credit)
            $account->total_paid += $request->amount;
            $account->pending_balance -= $request->amount;
        } else {
            // If it's now a purchase (debit)
            $account->total_purchases += $request->amount;
            $account->pending_balance += $request->amount;
        }

        // Ensure pending balance doesn't go negative
        if ($account->pending_balance < 0) {
            $account->pending_balance = 0;
        }

        $account->save();

        return redirect()->route('accounts.transactions', $accountId)->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function deleteTransaction($accountId, $transactionId)
    {
        $account = CustomerAccount::findOrFail($accountId);
        $transaction = CustomerTransaction::findOrFail($transactionId);
        
        // Ensure the transaction belongs to the account
        if ($transaction->customer_id != $account->customer_id) {
            return redirect()->back()->with('error', 'Transaction does not belong to this account.');
        }

        // Store transaction details for account adjustment
        $amount = $transaction->amount;
        $type = $transaction->transaction_type;
        
        // Adjust account balances before deletion
        if ($type == 'credit') {
            // If it was a payment (credit), reduce paid amount and increase pending
            $account->total_paid -= $amount;
            $account->pending_balance += $amount;
        } else {
            // If it was a purchase (debit), reduce purchases and pending
            $account->total_purchases -= $amount;
            $account->pending_balance -= $amount;
        }

        // Ensure pending balance doesn't go negative
        if ($account->pending_balance < 0) {
            $account->pending_balance = 0;
        }

        $account->save();
        
        // Delete the transaction
        $transaction->delete();

        return redirect()->route('accounts.transactions', $accountId)->with('success', 'Transaction deleted successfully.');
    }

}
