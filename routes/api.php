<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\PurchaseOrderApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/get-profile', [AuthApiController::class, 'get_profile']);
    Route::post('/update-profile', [AuthApiController::class, 'update_profile']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/dashboard', [DashboardApiController::class, 'index']);
    Route::get('/purchase-orders', [PurchaseOrderApiController::class, 'index']);
    Route::get('/purchase-orders/create_po', [PurchaseOrderApiController::class, 'create_po']);
    Route::get('/purchase-orders/item_details', [PurchaseOrderApiController::class, 'item_details']);
    Route::get('/purchase-orders/additional_info', [PurchaseOrderApiController::class, 'additional_info']);
    Route::get('/purchase-orders/tax_summary', [PurchaseOrderApiController::class, 'tax_summary']);
    Route::post('/purchase-orders/store', [PurchaseOrderApiController::class, 'store']);
    Route::get('/purchase-orders/{id}', [PurchaseOrderApiController::class, 'show']);
    Route::post('/purchase-orders/{id}/update-status', [PurchaseOrderApiController::class, 'update_status']);
});






