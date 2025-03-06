<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\Company;
use App\Models\CompanyAccount;
use Illuminate\Support\Facades\DB;

class CustomerAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $customers = Customer::all();

        // foreach ($customers as $customer) {
        //     if (!CustomerAccount::where('customer_id', $customer->id)->exists()) {
        //         CustomerAccount::create([
        //             'customer_id'      => $customer->id,
        //             'total_purchases'  => 0,
        //             'total_paid'       => 0,
        //             'pending_balance'  => 0,
        //             'last_payment_date'=> null,
        //         ]);
        //     }
        // }

        $companies = Company::all();

        foreach ($companies as $company) {
            if (!CompanyAccount::where('company_id', $company->id)->exists()) {
                CompanyAccount::create([
                    'company_id'      => $company->id,
                    'total_purchases'  => 0,
                    'total_paid'       => 0,
                    'pending_balance'  => 0,
                    'last_payment_date'=> null,
                ]);
            }
        }
    }
}
