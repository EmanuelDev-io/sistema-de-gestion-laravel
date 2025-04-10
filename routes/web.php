<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\InvoiceController;

// Dashboard / Home
Route::get('/', [HomeController::class, 'index'])->name('dashboard');

// Accounts Routes
Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
Route::get('/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
Route::post('/accounts', [AccountController::class, 'store'])->name('accounts.store');
Route::get('/accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');
Route::get('/accounts/{account}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
Route::put('/accounts/{account}', [AccountController::class, 'update'])->name('accounts.update');
Route::delete('/accounts/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');
Route::get('/accounts-expiring', [AccountController::class, 'expiringSoon'])->name('accounts.expiring');

// Invoices Routes
Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

// API Routes para futura implementación
Route::prefix('api')->group(function () {
    // Rutas de Cuentas
    Route::get('/accounts', [AccountController::class, 'apiIndex']);
    Route::post('/accounts', [AccountController::class, 'apiStore']);
    Route::get('/accounts/{account}', [AccountController::class, 'apiShow']);
    Route::put('/accounts/{account}', [AccountController::class, 'apiUpdate']);
    Route::delete('/accounts/{account}', [AccountController::class, 'apiDestroy']);
    Route::get('/accounts-expiring', [AccountController::class, 'apiExpiringSoon']);
    
    // Rutas de Facturas
    Route::get('/invoices', [InvoiceController::class, 'apiIndex']);
    Route::post('/invoices', [InvoiceController::class, 'apiStore']);
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'apiShow']);
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'apiUpdate']);
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'apiDestroy']);
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'apiDownload']);
    
    // Dashboard data
    Route::get('/dashboard', [HomeController::class, 'dashboardData']);
});
