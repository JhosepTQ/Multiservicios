<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\QuotationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\PdfController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Auth routes (sin protección para login)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Test route (sin protección)
Route::get('/test', function () {
    return ['message' => 'API funcionando correctamente', 'time' => now()];
});

// Rutas públicas - Catálogo de productos
Route::get('/public/products', [ProductController::class, 'publicIndex']);
Route::get('/public/products/{id}', [ProductController::class, 'publicShow']);
Route::get('/public/categories', [CategoryController::class, 'index']);

// Rutas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // User & Auth
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Categories
    Route::apiResource('categories', CategoryController::class);
    
    // Products
    Route::apiResource('products', ProductController::class);
    Route::post('/products/{id}/image', [ProductController::class, 'uploadImage']);
    
    // Sales
    Route::apiResource('sales', SaleController::class);
    Route::get('/sales/{id}/pdf', [PdfController::class, 'saleReceipt']);
    Route::get('/sales/receipt/{receiptNumber}', [SaleController::class, 'getByReceiptNumber']);
    
    // Expenses
    Route::apiResource('expenses', ExpenseController::class);
    
    // Quotations
    Route::apiResource('quotations', QuotationController::class);
    Route::post('/quotations/{quotation}/send', [QuotationController::class, 'send']);
    Route::get('/quotations/{id}/pdf', [PdfController::class, 'quotationPdf']);
    Route::post('/quotation-templates', [QuotationController::class, 'storeTemplate']);
    Route::get('/quotation-templates', [QuotationController::class, 'getTemplates']);
    Route::put('/quotation-templates/{template}', [QuotationController::class, 'updateTemplate']);
    
    // Dashboard & Reports
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    
    // Reports
    Route::get('/reports/sales', [ReportController::class, 'salesReport']);
    Route::get('/reports/expenses', [ReportController::class, 'expenseReport']);
    Route::get('/reports/inventory', [ReportController::class, 'inventoryReport']);
    Route::get('/reports/profit-loss', [ReportController::class, 'profitLossReport']);
    
    // PDF Reports
    Route::get('/pdf/profit-loss', [PdfController::class, 'profitLossReport']);
    Route::get('/pdf/inventory', [PdfController::class, 'inventoryReport']);
});
