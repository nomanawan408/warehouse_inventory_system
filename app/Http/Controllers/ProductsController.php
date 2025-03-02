<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Company;

class ProductsController extends Controller
{
    //
    public function index(){
        $products = Product::all();
        return view('dashboard.products.index', compact('products'));
    }

    public function create(){
        $companies = Company::all();
        return view('dashboard.products.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            // 'status' => 'required|boolean',
        ]);

        $product = new Product();
        $product->name = $validatedData['name'];
        $product->purchase_price = $validatedData['purchase_price'];
        $product->sale_price = $validatedData['sale_price'];
        $product->quantity = $validatedData['quantity'];
        $product->company_id = $request->company_id;
        
            $product->status = 1;
        $product->save();

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $companies = Company::all();
        return view('dashboard.products.edit', compact('product', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->name = $validatedData['name'];
        $product->purchase_price = $validatedData['purchase_price'];
        $product->sale_price = $validatedData['sale_price'];
        $product->quantity = $validatedData['quantity'];
        $product->company_id = $request->company_id;
        $product->save();

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }


    public function updateStock(Request $request, $id)
    {
        $validatedData = $request->validate([
            'new_quantity' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->quantity += $validatedData['new_quantity'];
        $product->save();

        return redirect()->route('products.index')->with('success', 'Product stock updated successfully');
    }


    public function purchaseStock(Request $request, $companyId)
    {
        $validatedData = $request->validate([
            'stock_amount' => 'required|integer|min:1',
            'purchase_date' => 'required|date',
        ]);

        $company = Company::findOrFail($companyId);
        $company->stock += $validatedData['stock_amount'];
        $company->save();

        return redirect()->route('companies.index')->with('success', 'Stock purchased successfully');
    }
}