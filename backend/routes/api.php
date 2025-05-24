<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpiredProductsReportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Common routes for all authenticated users
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::get('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
});


// Saler-only routes
Route::middleware(['auth:sanctum' ,'saler'])->group(function () {
    Route::apiResource('/sales', SaleController::class)->only(['index', 'store', 'show']);
    Route::get('sales/{sale}/payments', [SaleController::class, 'getPayments']);
    Route::get('/sales/{sale}/invoices/{invoice}', [SaleController::class, 'getInvoice']);
    Route::apiResource('/customers', CustomerController::class)->only(['index', 'store', 'show']);
    Route::get('/get_customers', [CustomerController::class, 'getCustomers']);
    Route::get('/sale-report', [SaleReportController::class, 'index']);
    Route::get('/customers-for-select', [CustomerController::class, 'getCustomersForSelect']);

});

// Purchaser-only routes
Route::middleware(['auth:sanctum','purchaser'])->group(function () {
    Route::apiResource('/purchases', PurchaseController::class)->only(['index', 'store', 'show']);
    Route::get('purchases/{purchase}/payments', [PurchaseController::class, 'getPayments']);
    Route::get('/purchases/{purchase}/invoices/{invoice}', [PurchaseController::class, 'getInvoice']);
    Route::apiResource('/suppliers', SupplierController::class)->only(['index', 'store', 'show']);
    Route::get('/get_suppliers', [SupplierController::class, 'getSuppliersForSelect']);
    Route::get('/purchase-report', [PurchaseReportController::class, 'index']);
    Route::get('/suppliers-for-select', [SupplierController::class, 'getSuppliersForSelect']);

});


// Admin-only routes
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/brands', BrandController::class);
    Route::get('/get_brands', [BrandController::class, 'getBrands']);
    Route::apiResource('/categories', CategoryController::class);
    Route::get('/get_category', [CategoryController::class, 'getCategory']);
    Route::get('/product_status_update/{product}', [ProductController::class, 'updateStatus']);
    Route::apiResource('/suppliers', SupplierController::class);
    Route::get('/get_suppliers', [SupplierController::class, 'getSuppliersForSelect']);
    Route::post('/products/search', [ProductController::class, 'search']);
    Route::apiResource('/purchases', PurchaseController::class);
    Route::get('purchases/{purchase}/payments', [PurchaseController::class, 'getPayments']);
    Route::delete('payments/{payment}', [PurchaseController::class, 'destroyPayment']);
    Route::get('/purchases/{purchase}/invoices/{invoice}', [PurchaseController::class, 'getInvoice']);
    Route::apiResource('/sales', SaleController::class);
    Route::get('sales/{sale}/payments', [SaleController::class, 'getPayments']);
    Route::delete('payments/{payment}', [SaleController::class, 'destroyPayment']);
    Route::get('/sales/{sale}/invoices/{invoice}', [SaleController::class, 'getInvoice']);
    Route::apiResource('/customers', CustomerController::class);
    Route::get('/get_customers', [CustomerController::class, 'getCustomers']);
    Route::get('/company-profile', [CompanyProfileController::class, 'show']);
    Route::post('/company-profile', [CompanyProfileController::class, 'update']);
    Route::get('/purchase-report', [PurchaseReportController::class, 'index']);
    Route::get('/suppliers-for-select', [SupplierController::class, 'getSuppliersForSelect']);
    Route::get('/sale-report', [SaleReportController::class, 'index']);
    Route::get('/customers-for-select', [CustomerController::class, 'getCustomersForSelect']);
    Route::get('/expired-products-report', [ExpiredProductsReportController::class, 'index']);
});


Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);

require __DIR__.'/auth.php';
