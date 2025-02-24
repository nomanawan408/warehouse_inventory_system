<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;    
use App\Models\Customer;

class SalesController extends Controller
{
    //  
    public function index(){
        $sales = Cart::all();
        $customers = Customer::all();

        return view('welcome', compact('sales','customers'));
    }

    public function store(Request $request)
    {
        $cart = $request->input('cart'); // Get cart data
        $customerId = $request->input('customer_id');

        if (!$cart || count($cart) === 0) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        DB::beginTransaction();

        try {
            // Step 1: Create Sale Record
            $sale = Sale::create([
                'customer_id' => $customerId,
                'total_amount' => array_sum(array_column($cart, 'total')),
            ]);

            // Step 2: Insert Sale Items
            foreach ($cart as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'total' => $item['total'],
                ]);

                // Step 3: Update Product Stock
                Product::where('id', $item['id'])->decrement('stock', $item['qty']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Sale recorded successfully!',
                'sale_id' => $sale->id
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Sale failed: ' . $e->getMessage()], 500);
        }
    }
}
