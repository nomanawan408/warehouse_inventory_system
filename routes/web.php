<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('check', function () {
    return view('index');
});

Route::get('/stocks', function () {
    return view('dashboard.stock.index');
});


Route::get('/reports', function () {
    return view('dashboard.reports.index');
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

