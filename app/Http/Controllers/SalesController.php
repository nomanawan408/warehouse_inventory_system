<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;    
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\CustomerAccount;
use App\Models\CustomerTransaction;
use Carbon\Carbon;
use DB;

class SalesController extends Controller
{
    //  
   
    
    public function index()
    {
        $sales = Sale::with('customer', 'saleItems.product')->get();
        return view('dashboard.sales.index', compact('sales'));
    }

    public function create(){
        $sales = Cart::all();
        $customers = Customer::all();

        return view('welcome', compact('sales','customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.price' => 'required|numeric|min:0',
            'cart.*.discount' => 'required|numeric|min:0',
            'sub_total' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'net_total' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 1️⃣ Create Sale Entry
            $sale = Sale::create([
            'customer_id'    => $validated['customer_id'],
            'total_amount'   => $validated['sub_total'],
            'discount'       => $validated['discount'],
            'tax'            => 0, // Assuming tax is not provided in the request
            'net_total'      => $validated['sub_total'] - $validated['discount'] - 0, // Assuming tax is 0
            'amount_paid'    => $validated['paid_amount'],
            'pending_amount' => max(0, $validated['net_total'] - $validated['paid_amount']),
            ]);

            // 2️⃣ Process Sale Items and Deduct Stock
            foreach ($validated['cart'] as $item) {
            
                $product = Product::find($item['id']);

                if (!$product) {
                    return response()->json(['error' => "Product ID {$item['id']} not found."], 400);
                }
                
                if ($product->quantity < $item['qty']) {
                    return response()->json(['error' => "Not enough stock for {$product->name}."], 400);
                }
                
            $product->decrement('quantity', $item['qty']);

            // Add Sale Item
            SaleItem::create([
                'sale_id'    => $sale->id,
                'product_id' => $item['id'],
                'quantity'   => $item['qty'],
                'price'      => $item['price'],
                'discount'   => $item['discount'],
                'total'      => ($item['qty'] * $item['price']) - $item['discount'],
            ]);

            }

            // 3️⃣ Update Customer Account
            $customerAccount = CustomerAccount::firstOrCreate(
                ['customer_id' => $validated['customer_id']],
                ['total_purchases' => 0, 'total_paid' => 0, 'pending_balance' => 0, 'last_payment_date' => null]
            );

            $customerAccount->increment('total_purchases', $validated['net_total']);
            $customerAccount->increment('total_paid', $validated['paid_amount']);
            $customerAccount->increment('pending_balance', $validated['net_total'] - $validated['paid_amount']);
            $customerAccount->last_payment_date = now();
            $customerAccount->save();

            // Create payment record
            $payment = Payment::create([
                'customer_id' => $validated['customer_id'],
                'sale_id' => $sale->id,
                'amount_paid' => $validated['paid_amount'],
                'payment_type' => 'Cash', // Default to Cash, modify if payment type is passed in request
                'payment_date' => now()
            ]);

            // 4️⃣ Record Customer Transaction
            CustomerTransaction::create([
                'customer_id'      => $validated['customer_id'],
                'sale_id'          => $sale->id,
                'transaction_type' => ($validated['paid_amount'] > 0) ? 'credit' : 'debit',
                'amount'           => $validated['paid_amount'],
                'payment_method'   => 'cash', // Assuming payment method is cash
                'reference'        => 'Payment for sale #' . $sale->id,
                'transaction_date' => now(),
            ]);

            DB::commit();

            return response()->json(['message' => 'Sale completed successfully!', 'sale_id' => $sale->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Sale processing failed. Please check your data.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
   
}
