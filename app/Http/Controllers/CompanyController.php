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
            'notes' => 'nullable|string'
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
        $transaction->save();

        // Update account balances
        $account->total_paid += $request->amount;
        $account->pending_balance -= $request->amount;
        $account->last_payment_date = $request->payment_date;
        $account->save();

        return redirect()->back()->with('success', 'Payment recorded successfully');
    }
}