<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum','admin'])->group(function (){
    Route::get('/user',[AuthController::class,'getUser']);
    Route::get('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy']);
    Route::apiResource('/users',UserController::class);
    Route::apiResource('/products',ProductController::class);
    Route::apiResource('/brands',BrandController::class);
    Route::get('/get_brands',[BrandController::class, 'getBrands']);
    Route::apiResource('/categories',CategoryController::class);
    Route::get('/get_category',[CategoryController::class, 'getCategory']);
    Route::get('/product_status_update/{product}',[ProductController::class, 'updateStatus']);
    Route::apiResource('/suppliers',SupplierController::class);
    Route::get('/get_suppliers',[SupplierController::class, 'getSuppliers']);
    Route::post('/products/search', [ProductController::class, 'search']);
    Route::apiResource('/purchases',PurchaseController::class);
    Route::get('purchases/{purchase}/payments', [\App\Http\Controllers\PurchaseController::class, 'getPayments']);
    Route::delete('payments/{payment}', [\App\Http\Controllers\PurchaseController::class, 'destroyPayment']);
    Route::get('/purchases/{purchase}/invoices/{invoice}', [PurchaseController::class, 'getInvoice']);
    Route::apiResource('/sales', SaleController::class);
    Route::get('sales/{sale}/payments', [\App\Http\Controllers\SaleController::class, 'getPayments']);
    Route::delete('payments/{payment}', [\App\Http\Controllers\SaleController::class, 'destroyPayment']);
    Route::get('/sales/{sale}/invoices/{invoice}', [SaleController::class, 'getInvoice']);
    Route::apiResource('/customers', CustomerController::class);
    Route::get('/get_customers', [CustomerController::class, 'getCustomers']);
    Route::get('/company-profile', [CompanyProfileController::class, 'show']);
    Route::post('/company-profile', [CompanyProfileController::class, 'update']);
});
Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);



require __DIR__.'/auth.php';
