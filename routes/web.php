<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

// Products Routes
Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create');
Route::post('/products', [ProductsController::class, 'store'])->name('products.store');

// Route::get('/products/{id}/edit', [ProductsController::class, 'edit'])->name('products.edit');
// Route::put('/products/{id}', [ProductsController::class, 'update'])->name('products.update');
// Route::delete('/products/{id}', [ProductsController::class, 'destroy'])->name('products.destroy');


// Sales Routes
Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');




Route::get('check', function () {
    return view('index');
});



Route::get('/reports', function () {
    return view('dashboard.reports.index');
});

Route::get('/customers', function () {
    return view('dashboard.customers.index');
});

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/companies', function () {
    return view('dashboard.companies.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';








Route::resource('stock', StockController::class);
Route::get('report', [ReportController::class, 'index'])->name('report.index');
Route::get('report/create', [ReportController::class, 'create'])->name('report.create');
Route::get('report/view/{id}', [ReportController::class, 'view'])->name('report.view');

