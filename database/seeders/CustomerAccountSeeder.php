<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\CustomerAccount;

class CustomerAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();

        foreach ($customers as $customer) {
            CustomerAccount::create([
                'customer_id'      => $customer->id,
                'total_purchases'  => 0,
                'total_paid'       => 0,
                'pending_balance'  => 0,
                'last_payment_date'=> null,
            ]);
        }
    }
}
