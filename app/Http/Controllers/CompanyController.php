<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Account;
use App\Models\CompanyAccount;
use App\Models\CompanyTransaction;
use App\Models\Customer;
use Exception;


class CompanyController extends Controller
{
    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            
            // Delete related records
            CompanyTransaction::where('company_id', $id)->delete();
            CompanyAccount::where('company_id', $id)->delete();
            $company->delete();
            
            return redirect()->route('companies.index')
                ->with('success', 'Company deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('companies.index')
                ->with('error', 'Error deleting company: ' . $e->getMessage());
        }
    }

    public function index(){
        $companies = Company::all();
        return view('dashboard.companies.index', compact('companies'));
    }

    public function create(){
        return view('dashboard.companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $company = new Company();
        $company->name = $request->name;
        $company->business_name = $request->business_name;
        $company->phone_no = $request->phone_no;
        $company->address = $request->address;
        $company->cnic = $request->cnic;
        $company->save();

        $account = new CompanyAccount();
        $account->company_id = $company->id;
        $account->total_purchases = 0;
        $account->total_paid = 0;
        $account->pending_balance = 0;
        $account->last_payment_date = null;
        $account->save();

        return redirect()->route('companies.index')->with('success', 'Company created successfully');
    }

    public function edit($id){
        $company = Company::find($id);
        return view('dashboard.companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'phone_no' => 'required|string|max:20',
            'address' => 'required|string|max:255'
        ]);

        try {
            $company = Company::findOrFail($id);
            $company->name = $request->name;
            $company->business_name = $request->business_name;
            $company->phone_no = $request->phone_no;
            $company->address = $request->address;
            $company->save();

            return redirect()->route('companies.index')
                ->with('success', 'Company updated successfully');
        } catch (\Exception $e) {
            return redirect()->route('companies.edit', $id)
                ->with('error', 'Error updating company: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function accounts()
    {
        $companies = CompanyAccount::all();
        return view('dashboard.companies.accounts', compact('companies'));
    }

    public function transactions($id)
    {
        $account = CompanyAccount::with('company')->findOrFail($id);
        
        // Fetch transactions for the company account, ordered by transaction_date
        $transactions = CompanyTransaction::where('company_id', $account->company_id)
                        ->orderBy('transaction_date', 'asc')
                        ->get();
    
        $balance = 0;
        $formattedTransactions = [];
    
        foreach ($transactions as $transaction) {
            $debit = 0.00;
            $credit = 0.00;
            $detail = $transaction->detail;
    
            if ($transaction->transaction_type === 'credit') {
                // A purchase on credit increases the pending balance.
                $debit = $transaction->amount;
                $balance += $transaction->amount;
            } elseif ($transaction->transaction_type === 'debit') {
                // A payment reduces the pending balance.
                $credit = $transaction->amount;
                $balance -= $transaction->amount;
            }
    
            $formattedTransactions[] = [
                'transaction_date' => $transaction->transaction_date
                    ? $transaction->transaction_date->format('Y-m-d H:i:s')
                    : 'N/A',
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $balance,
                'detail' => $detail,
                'reference' => $transaction->reference ?? null,
                'payment_method' => $transaction->payment_method ?? null,
            ];
        }
    
        return view('dashboard.companies.transactions', compact('account', 'formattedTransactions'));
    }

public function recordPayment(Request $request, $id)
{
    $request->validate([
        'amount' => 'required|numeric|min:0',
        'payment_date' => 'required|date',
        'notes' => 'nullable|string',
        'reference' => 'nullable|string|max:255' // Added reference validation
    ]);

    $company = Company::findOrFail($id);
    $account = $company->account;

    // Create transaction record
    $transaction = new CompanyTransaction();
    $transaction->company_id = $id;
    $transaction->amount = $request->amount;
    $transaction->transaction_type = 'debit';
    $transaction->detail = $request->notes ?? 'Payment received';
    $transaction->transaction_date = $request->payment_date;
    $transaction->reference = 'Payment received'; // Added reference field
    $transaction->save();

    // Update account balances
    $account->total_paid += $request->amount;
    $account->pending_balance -= $request->amount;
    $account->last_payment_date = $request->payment_date;
    $account->save();

    return redirect()->back()->with('success', 'Payment recorded successfully');
    }

    /**
     * Show the form for editing a company transaction.
     */
    public function editTransaction($accountId, $transactionId)
    {
        $account = CompanyAccount::findOrFail($accountId);
        $transaction = CompanyTransaction::findOrFail($transactionId);
        
        // Ensure the transaction belongs to the company account
        if ($transaction->company_id != $account->company_id) {
            return redirect()->back()->with('error', 'Transaction does not belong to this account.');
        }
        
        return view('dashboard.companies.edit_transaction', compact('account', 'transaction'));
    }

    /**
     * Update the specified company transaction in storage.
     */
    public function updateTransaction(Request $request, $accountId, $transactionId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'detail' => 'nullable|string',
            'reference' => 'nullable|string',
        ]);

        $account = CompanyAccount::findOrFail($accountId);
        $transaction = CompanyTransaction::findOrFail($transactionId);
        
        // Ensure the transaction belongs to the account
        if ($transaction->company_id != $account->company_id) {
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
        if ($originalType == 'debit') {
            // If it was a payment (debit), adjust the paid and pending amounts
            $account->total_paid -= $originalAmount;
            $account->pending_balance += $originalAmount;
        } else {
            // If it was a purchase (credit), adjust the purchases and pending amounts
            $account->total_purchases -= $originalAmount;
            $account->pending_balance -= $originalAmount;
        }

        // Apply the new values
        if ($transaction->transaction_type == 'debit') {
            // If it's now a payment (debit)
            $account->total_paid += $request->amount;
            $account->pending_balance -= $request->amount;
        } else {
            // If it's now a purchase (credit)
            $account->total_purchases += $request->amount;
            $account->pending_balance += $request->amount;
        }

        // Ensure pending balance doesn't go negative
        if ($account->pending_balance < 0) {
            $account->pending_balance = 0;
        }

        $account->save();

        return redirect()->route('companies.transactions', $accountId)->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified company transaction from storage.
     */
    public function deleteTransaction($accountId, $transactionId)
    {
        $account = CompanyAccount::findOrFail($accountId);
        $transaction = CompanyTransaction::findOrFail($transactionId);
        
        // Ensure the transaction belongs to the account
        if ($transaction->company_id != $account->company_id) {
            return redirect()->back()->with('error', 'Transaction does not belong to this account.');
        }

        // Store transaction details for account adjustment
        $amount = $transaction->amount;
        $type = $transaction->transaction_type;
        
        // Adjust account balances before deletion
        if ($type == 'debit') {
            // If it was a payment (debit), reduce paid amount and increase pending
            $account->total_paid -= $amount;
            $account->pending_balance += $amount;
        } else {
            // If it was a purchase (credit), reduce purchases and pending
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

        return redirect()->route('companies.transactions', $accountId)->with('success', 'Transaction deleted successfully.');
    }
}

