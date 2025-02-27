<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerAccount;

class CustomerController extends Controller
{
    //
    public function index(){
        $customers = Customer::all();
        return view('dashboard.customers.index', compact('customers'));
    }

    public function create(){
        return view('dashboard.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->business_name = $request->business_name;
        $customer->phone_no = $request->phone_no;
        $customer->address = $request->address;
        $customer->cnic = $request->cnic;
        $customer->save();

        $account = new CustomerAccount();
        $account->customer_id = $customer->id;
        $account->total_purchases = 0;
        $account->total_paid = 0;
        $account->pending_balance = 0;
        $account->last_payment_date = null;
        $account->save();


        return redirect()->route('customers.index')->with('success', 'Customer created successfully');
    }


    public function edit($id){
        $customer = Customer::find($id);
        return view('dashboard.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'phone_no' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'cnic' => 'required|string|max:15|unique:customers,cnic,'.$id,
        ]);

        $customer = Customer::find($id);
        $customer->name = $request->name;
        $customer->business_name = $request->business_name;
        $customer->phone_no = $request->phone_no;
        $customer->address = $request->address;
        $customer->cnic = $request->cnic;
        $customer->save();

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully');
    }

    public function search(Request $request)
    {
        $query = $request->input('search');
        if(empty($query))
            $customers = Customer::all();
        else
            $customers = Customer::where('name', 'LIKE', "%{$query}%")
                ->orWhere('business_name', 'LIKE', "%{$query}%")
                ->orWhere('phone_no', 'LIKE', "%{$query}%")
                ->orWhere('cnic', 'LIKE', "%{$query}%")
                ->get();

        return view('dashboard.partials.table', compact('customers'))->render();
    }

}
