<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the stock items.
     */
    public function index()
    {
        $stocks = Stock::all();
        return view('stock.index', compact('stocks'));
    }

    /**
     * Show the form for creating a new stock item.
     */
    public function create()
    {
        return view('stock.create');
    }

    /**
     * Store a newly created stock item in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'quantity' => 'required|integer',
            'price' => 'required|numeric'
        ]);

        Stock::create($request->all());
        return redirect()->route('stock.index')->with('success', 'Stock item added successfully!');
    }

    /**
     * Display the specified stock item.
     */
    public function show(Stock $stock)
    {
        return view('stock.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified stock item.
     */
    public function edit(Stock $stock)
    {
        return view('stock.edit', compact('stock'));
    }

    /**
     * Update the specified stock item in database.
     */
    public function update(Request $request, Stock $stock)
    {
        $request->validate([
            'name' => 'required',
            'quantity' => 'required|integer',
            'price' => 'required|numeric'
        ]);

        $stock->update($request->all());
        return redirect()->route('stock.index')->with('success', 'Stock item updated successfully!');
    }

    /**
     * Remove the specified stock item from database.
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();
        return redirect()->route('stock.index')->with('success', 'Stock item deleted successfully!');
    }
}
