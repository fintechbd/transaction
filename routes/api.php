<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "API" middleware group. Enjoy building your API!
|
*/
if (Config::get('fintech.transaction.enabled')) {
    Route::prefix('transaction')->name('transaction.')->group(function () {

        Route::apiResource('transaction-forms', \Fintech\Transaction\Http\Controllers\TransactionFormController::class);
        Route::post('transaction-forms/{transaction_form}/restore', [\Fintech\Transaction\Http\Controllers\TransactionFormController::class, 'restore'])->name('transaction-forms.restore');

        Route::apiResource('orders', \Fintech\Transaction\Http\Controllers\OrderController::class);
        Route::post('orders/{order}/restore', [\Fintech\Transaction\Http\Controllers\OrderController::class, 'restore'])->name('orders.restore');

        Route::apiResource('order-details', \Fintech\Transaction\Http\Controllers\OrderDetailController::class);
        Route::post('order-details/{order_detail}/restore', [\Fintech\Transaction\Http\Controllers\OrderDetailController::class, 'restore'])->name('order-details.restore');

        //DO NOT REMOVE THIS LINE//
    });
}
