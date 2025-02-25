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
}