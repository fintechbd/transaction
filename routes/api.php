<?php

use Fintech\Transaction\Http\Controllers\ChartClassController;
use Fintech\Transaction\Http\Controllers\ChartEntryController;
use Fintech\Transaction\Http\Controllers\Charts\UserAccountUsagePieChartController;
use Fintech\Transaction\Http\Controllers\ChartTypeController;
use Fintech\Transaction\Http\Controllers\ManualRefundController;
use Fintech\Transaction\Http\Controllers\OrderController;
use Fintech\Transaction\Http\Controllers\OrderDetailController;
use Fintech\Transaction\Http\Controllers\OrderQueueController;
use Fintech\Transaction\Http\Controllers\TransactionFormController;
use Fintech\Transaction\Http\Controllers\UserAccountController;
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

        Route::apiResource('transaction-forms', TransactionFormController::class);
        Route::post('transaction-forms/{transaction_form}/restore', [TransactionFormController::class, 'restore'])->name('transaction-forms.restore');

        Route::apiResource('orders', OrderController::class);
        Route::post('orders/{order}/restore', [OrderController::class, 'restore'])->name('orders.restore');

        Route::apiResource('order-details', OrderDetailController::class);
        Route::post('order-details/{order_detail}/restore', [OrderDetailController::class, 'restore'])->name('order-details.restore');

        Route::apiResource('chart-classes', ChartClassController::class);
        Route::post('chart-classes/{chart_class}/restore', [ChartClassController::class, 'restore'])->name('chart-classes.restore');

        Route::apiResource('chart-types', ChartTypeController::class);
        Route::post('chart-types/{chart_type}/restore', [ChartTypeController::class, 'restore'])->name('chart-types.restore');

        Route::apiResource('chart-entries', ChartEntryController::class);
        Route::post('chart-entries/{chart_entry}/restore', [ChartEntryController::class, 'restore'])->name('chart-entries.restore');

        Route::apiResource('user-accounts', UserAccountController::class)->except('update');
        Route::post('user-accounts/{user_account}/restore', [UserAccountController::class, 'restore'])->name('user-accounts.restore');
        Route::get('user-accounts/{user_account}/toggle', [UserAccountController::class, 'toggle'])->name('user-accounts.toggle');

        Route::apiResource('order-queues', OrderQueueController::class)->only(['index', 'show', 'destroy']);

        Route::prefix('charts')->name('charts.')->group(function () {
            Route::get('user-account-usages-pie-chart', UserAccountUsagePieChartController::class)
                ->name('user-account-usages');
        });

        Route::apiResource('manual-refunds', ManualRefundController::class);

        //DO NOT REMOVE THIS LINE//
    });
    Route::prefix('dropdown')->name('transaction.')->group(function () {
        Route::get('transaction-forms', [TransactionFormController::class, 'dropdown'])->name('transaction-forms.dropdown');
    });
}
