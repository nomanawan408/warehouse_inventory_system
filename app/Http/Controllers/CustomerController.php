<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Account;

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
            'business_name' => 'required|string|max:255',
            'phone_no' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'cnic' => 'required|string|max:15|unique:customers',
        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->business_name = $request->business_name;
        $customer->phone_no = $request->phone_no;
        $customer->address = $request->address;
        $customer->cnic = $request->cnic;
        $customer->save();

        $account = new Account();
        $account->customer_id = $customer->id;
        $account->paid_amount = 0;
        $account->pedding_amount = 0;
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
}
