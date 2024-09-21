<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/user-details', [AuthController::class, 'getUser']);

    Route::get('/merchant', [MerchantController::class, 'show']);
    Route::put('/merchant', [MerchantController::class, 'update']);
    Route::post('/merchant/upload', [MerchantController::class, 'upload']);
    Route::get('/merchants', [MerchantController::class, 'index']);
    Route::get('/merchants/{id}', [MerchantController::class, 'showDetail']);

    Route::get('/customer', [CustomerController::class, 'show']);
    Route::put('/customer', [CustomerController::class, 'update']);
    Route::post('/customer/upload', [CustomerController::class, 'upload']);

    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/{id}', [MenuController::class, 'show']);
    Route::post('/menus', [MenuController::class, 'store']);
    Route::put('/menus/{id}', [MenuController::class, 'update']);
    Route::delete('/menus/{id}', [MenuController::class, 'destroy']);
    Route::get('/search-menus', [MenuController::class, 'search']);
    Route::get('/all-menus', [MenuController::class, 'allMenus']);
    Route::get('/all-menus/{id}', [MenuController::class, 'menuDetail']);

    Route::get('customer/order/{id}', [OrderController::class, 'getOrderById']);
    Route::get('customer/orders', [OrderController::class, 'getAllOrdersForCustomer']);
    Route::get('merchant/orders', [OrderController::class, 'getAllOrdersForMerchant']);


    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::delete('orders/{id}', [OrderController::class, 'destroy']);
    Route::post('orders/{id}/upload-receipt', [OrderController::class, 'uploadPaymentReceipt']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});
