<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Account;
use App\Models\CompanyAccount;

class CompanyController extends Controller
{
    //
     //
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


        return redirect()->route('companies.index')->with('success', 'Customer created successfully');
    }


    public function edit($id){
        $company = Company::find($id);
        return view('dashboard.companies.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'phone_no' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'cnic' => 'required|string|max:15|unique:companies,cnic,'.$id,
        ]);

        $company = Company::find($id);
        $company->name = $request->name;
        $company->business_name = $request->business_name;
        $company->phone_no = $request->phone_no;
        $company->address = $request->address;
        $company->cnic = $request->cnic;
        $company->save();

        return redirect()->route('companies.index')->with('success', 'Customer updated successfully');
    }

    public function destroy($id)
    {
        $company = Company::find($id);
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Customer deleted successfully');
    }
}
