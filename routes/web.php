<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfitReportController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PurchaseController;
use App\Models\Product;
use Illuminate\Http\Request;


Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect()->route('sales.create');
    })->name('welcome');

    // Reports Routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/report/data', [ReportController::class, 'getData'])->name('reports.data');
    Route::get('/reports/customer', [ReportController::class, 'customerReport'])->name('reports.customer');
    Route::get('/reports/company', [ReportController::class, 'companyReport'])->name('reports.company');
    Route::get('/reports/customer/pdf', [ReportController::class, 'exportCustomerPdf'])->name('reports.customer.pdf');
    Route::get('/reports/company/pdf', [ReportController::class, 'exportCompanyPdf'])->name('reports.company.pdf');
    
    // Profit Reports Routes
    Route::get('/reports/profit', [ProfitReportController::class, 'index'])->name('reports.profit');
    Route::get('/reports/profit/print', [ProfitReportController::class, 'printReport'])->name('reports.profit.print');

    // Products Routes
    Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductsController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [ProductsController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [ProductsController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductsController::class, 'destroy'])->name('products.destroy');
    Route::put('/products/{id}/update-stock', [ProductsController::class, 'updateStock'])->name('products.updateStock');

    // Sales Routes
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/{id}', [SalesController::class, 'show'])->name('sales.show');
    Route::get('/sales/{id}/print', [SalesController::class, 'print'])->name('sales.print');
    Route::get('/sales/{id}/edit', [SalesController::class, 'edit'])->name('sales.edit');
    Route::put('/sales/{id}', [SalesController::class, 'update'])->name('sales.update');
    Route::get('/products/search', [SalesController::class, 'searchProducts'])->name('products.search');

    // Customers Routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{id}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Stock Routes
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');

    //Add payments to customer accounts 
    Route::get('/accounts/{id}/payments/add', [AccountController::class, 'addPayment'])->name('accounts.payments.add');
    Route::post('/accounts/{id}/payments', [AccountController::class, 'storePayment'])->name('accounts.payments.store');
    // Edit and update customer transactions
    Route::get('/accounts/{id}/transactions/{transactionId}/edit', [AccountController::class, 'editTransaction'])->name('accounts.transactions.edit');
    Route::put('/accounts/{id}/transactions/{transactionId}', [AccountController::class, 'updateTransaction'])->name('accounts.transactions.update');

    // Add Pending Amount to Account
    Route::post('/accounts/{id}/pending', [AccountController::class, 'storePendingAmount'])->name('accounts.pending.store');


    // Company Routes
    Route::get('/companies/accounts', [CompanyController::class, 'accounts'])->name('companies.accounts');
    Route::get('/companies/{id}/transactions', [CompanyController::class, 'transactions'])->name('companies.transactions');
    Route::post('/companies/{id}/record-payment', [CompanyController::class, 'recordPayment'])->name('companies.record-payment');

    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{id}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{id}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/{id}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    // Stock Purchase Routes
    Route::post('/stocks/{company}/purchase', [PurchaseController::class, 'store'])->name('stocks.purchase');

    // Accounts Routes
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/show', [AccountController::class, 'index'])->name('accounts.show');

    // Account Transections
    Route::get('/accounts/{id}/transactions', [AccountController::class, 'transactions'])->name('accounts.transactions');



    Route::get('/search', function (Request $request) {
        $query = $request->get('query');

        if ($query) {
            $products = Product::where('name', 'LIKE', "%{$query}%")
                ->with('company:id,name') // Load the related company data
                ->limit(10)
                ->get(['id', 'name', 'sale_price', 'quantity', 'company_id']);
        } else {
            $products = [];
        }

        // Map products to include company_name in the response
        $results = collect($products)->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sale_price' => $product->sale_price,
                'quantity' => $product->quantity,
                'company_name' => $product->company->name ?? 'N/A',
            ];
        });

        return response()->json($results);
    });
    
    Route::get('/products/all', function (Request $request) {
        $products = Product::with('company:id,name')
            ->get(['id', 'name', 'sale_price', 'quantity', 'company_id']);
            
        // Map products to include company_name in the response
        $results = collect($products)->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sale_price' => $product->sale_price,
                'quantity' => $product->quantity,
                'company_name' => $product->company->name ?? 'N/A',
            ];
        });
        
        return response()->json($results);
    });
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
