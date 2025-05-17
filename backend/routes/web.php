<?php

use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseInvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/purchases/{id}/invoice', [PurchaseController::class, 'showInvoice'])
    ->name('purchases.invoice')
    ->middleware('auth');

require __DIR__.'/auth.php';
