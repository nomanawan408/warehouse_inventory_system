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
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function print($id)
    {
        $sale = Sale::with(['customer', 'saleItems.product'])->findOrFail($id);

        // We don't need to calculate previousPending separately anymore
        // The view will calculate the last pending amount as:
        // $sale->customer->account->pending_balance - $sale->pending_amount
        
        return view('dashboard.sales.print', compact('sale'));
    }
    public function show($id)
    {
        $sale = Sale::with(['customer', 'saleItems.product'])->findOrFail($id);
        
        if (request()->ajax()) {
            // Format sale data for edit modal
            $formattedSale = [
                'id' => $sale->id,
                'invoice_number' => str_pad($sale->id, 3, '0', STR_PAD_LEFT),
                'customer_id' => $sale->customer_id,
                'items' => $sale->saleItems->map(function($item) {
                    return [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'discount' => $item->discount
                    ];
                }),
                'discount' => $sale->discount,
                'amount_paid' => $sale->amount_paid
            ];
            
            return response()->json([
                'sale' => $formattedSale,
                'customers' => Customer::all(),
                'products' => Product::all()
            ]);
        }
        
        return view('dashboard.sales.invoice', compact('sale'));
    }

    public function index()
    {
        // Check for success message in query params and flash it to session
        if (request()->has('success')) {
            session()->flash('success', request('success'));
        }
        
        $sales = Sale::with('customer', 'saleItems.product')->get();
        $customers = Customer::all();
        return view('dashboard.sales.index', compact('sales', 'customers'));
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
            'paid_amount' => 'nullable|numeric|min:0',
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

            // Calculate regular profit margin (without considering discounts)
            $profit_margin = $item['price'] - $product->purchase_price;
            $profit_margin_total = $profit_margin * $item['qty'];
            
            // Calculate profit after item-level discount
            $total_before_discount = $item['qty'] * $item['price'];
            $total_after_discount = $total_before_discount - $item['discount'];
            $profit_after_discount = $total_after_discount - ($product->purchase_price * $item['qty']);
            
            // Create the sale item with both profit calculations
            SaleItem::create([
                'sale_id'       => $sale->id,
                'product_id'    => $item['id'],
                'company_id'    => $product->company_id,
                'quantity'      => $item['qty'],
                'price'         => $item['price'],
                'discount'      => $item['discount'],
                'profit_margin' => $profit_after_discount, // Store the accurate profit after discount
                'total'         => $total_after_discount,
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

            // Create payment record if paid amount is greater than 0
            if ($validated['paid_amount'] > 0) {
                Payment::create([
                    'customer_id' => $validated['customer_id'],
                    'sale_id' => $sale->id,
                    'amount_paid' => $validated['paid_amount'],
                    'payment_type' => 'Cash', // Default to Cash, modify if payment type is passed in request
                    'payment_date' => now()
                ]);
            }

            // 4️⃣ Record Customer Transactions
            // Record the sale transaction (debit)
            CustomerTransaction::create([
                'customer_id'      => $validated['customer_id'],
                'sale_id'          => $sale->id,
                'transaction_type' => 'debit',
                'amount'           => $validated['net_total'],
                'payment_method'   => null,
                'reference'        => 'Sale #' . $sale->id,
                'transaction_date' => now(),
            ]);

            // Record the payment transaction if payment was made (credit)
            if ($validated['paid_amount'] > 0) {
                CustomerTransaction::create([
                    'customer_id'      => $validated['customer_id'],
                    'sale_id'          => $sale->id,
                    'transaction_type' => 'credit',
                    'amount'           => $validated['paid_amount'],
                    'payment_method'   => 'cash',
                    'reference'        => 'Payment for sale #' . $sale->id,
                    'transaction_date' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Sale completed successfully!',
                'sale_id' => $sale->id,
                'print_url' => route('sales.print', $sale->id),
                'invoice_url' => route('sales.show', $sale->id)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Sale processing failed. Please check your data.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        // Fetch sale with its related sale items
        $sale = Sale::with('items')->findOrFail($id);
        $customers = Customer::all();
    
        return view('dashboard.sales.edit', compact('sale', 'customers'));
    }
    

        /**
         * Update an existing sale record.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  int  $id
         * @return \Illuminate\Http\Response
         */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'cart' => 'required|json',
            'sub_total' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'net_total' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::findOrFail($id);

            // Preserve original values before any modifications so we can compute accurate diffs
            $oldCustomerId  = $sale->customer_id;
            $oldNetTotal    = $sale->net_total;
            $oldAmountPaid  = $sale->amount_paid;
            
            // Parse cart items from JSON
            $cartItems = json_decode($validated['cart'], true);
            
            if (empty($cartItems)) {
                throw new \Exception('No items in cart');
            }

            // 1️⃣ Restore Stock for the Old Sale
            foreach ($sale->items as $oldItem) {
                $product = Product::find($oldItem->product_id);
                if ($product) {
                    $product->increment('quantity', $oldItem->quantity); // Add the old stock back
                }
            }

            // 2️⃣ Remove Old Sale Items
            $sale->items()->delete();

            // 3️⃣ Update Sale Details
            $sale->update([
                'customer_id' => $validated['customer_id'],
                'total_amount' => $validated['sub_total'],
                'discount' => $validated['discount'],
                'tax' => 0,  // Assuming no tax is being applied
                'net_total' => $validated['net_total'],
                'amount_paid' => $validated['paid_amount'],
                'pending_amount' => max(0, $validated['net_total'] - $validated['paid_amount']),
            ]);

            // 4️⃣ Add New Items & Deduct Stock
            $totalProfit = 0;
            
            foreach ($cartItems as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->quantity < $item['qty']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $product->decrement('quantity', $item['qty']); // Deduct stock for the new sale
                
                // Calculate total before any discount
                $totalBeforeDiscount = $item['qty'] * $item['price'];
                
                // Calculate total after item-level discount
                $totalAfterItemDiscount = $totalBeforeDiscount - $item['discount'];
                
                // Calculate the cost of the product
                $productCost = $product->purchase_price * $item['qty'];
                
                // Calculate profit after item-level discount
                $profitAfterDiscount = $totalAfterItemDiscount - $productCost;
                $totalProfit += $profitAfterDiscount;
                
                // Create sale item with accurate profit calculation
                $saleItem = new SaleItem([
                    'product_id' => $item['id'],
                    'company_id' => $product->company_id,
                    'quantity' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'profit_margin' => $profitAfterDiscount, // Store profit after discount
                    'total' => $totalAfterItemDiscount,
                ]);
                
                $sale->items()->save($saleItem);
            }

            // 5️⃣ Update Customer Account & Transactions

            // 5.a) Revert figures from the original sale if the customer remains the same
            if ($oldCustomerId === (int) $validated['customer_id']) {
                $purchaseDiff = $validated['net_total'] - $oldNetTotal;
                $paidDiff     = $validated['paid_amount'] - $oldAmountPaid;

                $customerAccount = CustomerAccount::firstOrCreate(
                    ['customer_id' => $validated['customer_id']],
                    ['total_purchases' => 0, 'total_paid' => 0, 'pending_balance' => 0]
                );

                $customerAccount->increment('total_purchases', $purchaseDiff);
                $customerAccount->increment('total_paid', $paidDiff);
                $customerAccount->increment('pending_balance', $purchaseDiff - $paidDiff);
            } else {
                // 5.b) Customer changed – rollback old customer account and add to new
                $oldAccount = CustomerAccount::firstOrCreate(
                    ['customer_id' => $oldCustomerId],
                    ['total_purchases' => 0, 'total_paid' => 0, 'pending_balance' => 0]
                );
                $oldAccount->decrement('total_purchases', $oldNetTotal);
                $oldAccount->decrement('total_paid', $oldAmountPaid);
                $oldAccount->decrement('pending_balance', max(0, $oldNetTotal - $oldAmountPaid));

                $customerAccount = CustomerAccount::firstOrCreate(
                    ['customer_id' => $validated['customer_id']],
                    ['total_purchases' => 0, 'total_paid' => 0, 'pending_balance' => 0]
                );
                $customerAccount->increment('total_purchases', $validated['net_total']);
                $customerAccount->increment('total_paid', $validated['paid_amount']);
                $customerAccount->increment('pending_balance', $validated['net_total'] - $validated['paid_amount']);
            }

            if ($validated['paid_amount'] > 0) {
                $customerAccount->last_payment_date = now();
            }
            $customerAccount->save();

            // 5.c) Refresh Customer Transactions – delete old and create new reflecting current amounts
            CustomerTransaction::where('sale_id', $sale->id)->delete();

            // Debit for sale amount
            CustomerTransaction::create([
                'customer_id'      => $validated['customer_id'],
                'sale_id'          => $sale->id,
                'transaction_type' => 'debit',
                'amount'           => $validated['net_total'],
                'payment_method'   => null,
                'reference'        => 'Sale #'.$sale->id.' (edited)',
                'transaction_date' => now(),
            ]);

            // Credit for payment (if any)
            if ($validated['paid_amount'] > 0) {
                CustomerTransaction::create([
                    'customer_id'      => $validated['customer_id'],
                    'sale_id'          => $sale->id,
                    'transaction_type' => 'credit',
                    'amount'           => $validated['paid_amount'],
                    'payment_method'   => 'cash',
                    'reference'        => 'Payment for sale #'.$sale->id.' (edited)',
                    'transaction_date' => now(),
                ]);
            }

            // 6️⃣ Update Payment Record (create / update / delete as needed)
            $payment = Payment::where('sale_id', $id)->first();
            if ($validated['paid_amount'] > 0) {
                if ($payment) {
                    $payment->update([
                        'amount_paid'  => $validated['paid_amount'],
                        'payment_date' => now(),
                    ]);
                } else {
                    Payment::create([
                        'customer_id'  => $validated['customer_id'],
                        'sale_id'      => $id,
                        'amount_paid'  => $validated['paid_amount'],
                        'payment_type' => 'Cash',
                        'payment_date' => now(),
                    ]);
                }
            } else {
                // No amount paid now – remove any existing payment record
                if ($payment) {
                    $payment->delete();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Invoice updated successfully',
                'sale' => $sale->fresh(['items', 'customer']),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to update invoice',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function searchProducts(Request $request)
    {
        $query = $request->get('query');
        $products = Product::where('name', 'like', "%{$query}%")
            ->with('company')
            ->get();
            
        // For debugging - log raw products
        \Log::info('Raw product search results:', ['products' => $products->toArray()]);
            
        return response()->json($products);
    }
}
