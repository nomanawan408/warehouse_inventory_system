<?php

namespace App\Http\Controllers;

use App\Models\CompanyTransaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\Company;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\CompanyAccount;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = CompanyTransaction::with(['company', 'transactionItems.product'])
            ->orderBy('transaction_date', 'desc')
            ->paginate(10);
        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $companies = Company::all();
        $products = Product::all();
        return view('purchases.create', compact('companies', 'products'));
    }

    public function store(Request $request, $companyId)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'paid_amount' => 'nullable|numeric|min:0'
        ]);

        // Retrieve paid amount or default to 0 if not provided
        $paidAmount = $request->input('paid_amount', 0);

        DB::beginTransaction();

        try {
            // Update product stock and prices
            $product = Product::findOrFail($validatedData['product_id']);
            $oldQuantity = $product->quantity;
            $product->quantity += $validatedData['quantity'];
            $product->purchase_price = $validatedData['purchase_price'];
            $product->sale_price = $validatedData['sale_price'];
            $product->save();

            // Calculate total amount
            $totalAmount = $validatedData['quantity'] * $validatedData['purchase_price'];

            // Create purchase record with paid_amount and adjusted pending_amount
            $purchase = Purchase::create([
                'company_id' => $companyId,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'pending_amount' => $totalAmount - $paidAmount,
                'purchase_date' => now()
            ]);

            // Update Company Account accordingly
            $companyAccount = CompanyAccount::firstOrCreate(
                ['company_id' => $companyId],
                ['total_purchases' => 0, 'total_paid' => 0, 'pending_balance' => 0, 'last_payment_date' => null]
            );

            $companyAccount->increment('total_purchases', $totalAmount);
            if ($paidAmount > 0) {
                $companyAccount->increment('total_paid', $paidAmount);
                $companyAccount->increment('pending_balance', $totalAmount - $paidAmount);
            } else {
                $companyAccount->increment('pending_balance', $totalAmount);
            }
            $companyAccount->last_payment_date = now();
            $companyAccount->save();

            // Record the purchase transaction (credit)
            $transaction = CompanyTransaction::create([
                'company_id' => $companyId, 
                'purchase_id' => $purchase->id,
                'transaction_type' => 'credit',
                'amount' => $totalAmount,
                'payment_method' => null,
                'reference' => 'Purchase #' . $purchase->id,
                'transaction_date' => now(),
            ]);

            // Record the payment transaction if payment was made (debit)
            if ($paidAmount > 0) {
                CompanyTransaction::create([
                    'company_id' => $companyId,
                    'purchase_id' => $purchase->id,
                    'transaction_type' => 'debit',
                    'amount' => is_null($paidAmount) ? 0 : $paidAmount,
                    'payment_method' => 'cash',
                    'reference' => 'Payment for purchase #' . $purchase->id,
                    'transaction_date' => now(),
                ]);
            }

            // Create transaction item
            PurchaseItem::create([
                'company_transaction_id' => $transaction->id,
                'product_id' => $validatedData['product_id'],
                'quantity' => $validatedData['quantity'],
                'unit_price' => $validatedData['purchase_price'],
                'total_amount' => $totalAmount
            ]);
            
            DB::commit();
            return redirect()->route('products.index')->with('success', 'Stock purchased and records updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error occurred while processing purchase: ' . $e->getMessage());
        }
    }

    public function show(CompanyTransaction $purchase)
    {
        $purchase->load(['company', 'transactionItems.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function updateStatus(Request $request, CompanyTransaction $purchase)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        $purchase->status = $request->status;
        $purchase->save();

        return redirect()->route('purchases.show', $purchase)
            ->with('success', 'Purchase status updated successfully');
    }
}