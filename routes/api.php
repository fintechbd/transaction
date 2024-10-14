<?php

use Fintech\RestApi\Http\Controllers\Transaction\ChartClassController;
use Fintech\RestApi\Http\Controllers\Transaction\ChartEntryController;
use Fintech\RestApi\Http\Controllers\Transaction\Charts\OrderSummaryController;
use Fintech\RestApi\Http\Controllers\Transaction\Charts\UserAccountSummaryController;
use Fintech\RestApi\Http\Controllers\Transaction\Charts\UserAccountUsagePieChartController;
use Fintech\RestApi\Http\Controllers\Transaction\ChartTypeController;
use Fintech\RestApi\Http\Controllers\Transaction\ManualRefundController;
use Fintech\RestApi\Http\Controllers\Transaction\OrderController;
use Fintech\RestApi\Http\Controllers\Transaction\OrderDetailController;
use Fintech\RestApi\Http\Controllers\Transaction\OrderQueueController;
use Fintech\RestApi\Http\Controllers\Transaction\OrderStatusDropdownController;
use Fintech\RestApi\Http\Controllers\Transaction\RedeemPointController;
use Fintech\RestApi\Http\Controllers\Transaction\RequestPlatformDropdownController;
use Fintech\RestApi\Http\Controllers\Transaction\RewardPointController;
use Fintech\RestApi\Http\Controllers\Transaction\TransactionFormController;
use Fintech\RestApi\Http\Controllers\Transaction\UserAccountController;
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
    Route::prefix(config('fintech.transaction.root_prefix', 'api/'))->middleware(['api'])->group(function () {
        Route::prefix('transaction')->name('transaction.')->group(function () {
            Route::apiResource('transaction-forms', TransactionFormController::class);
            //         Route::post('transaction-forms/{transaction_form}/restore', [TransactionFormController::class, 'restore'])->name('transaction-forms.restore');

            Route::apiResource('orders', OrderController::class)->only(['index', 'store', 'show']);
            //         Route::post('orders/{order}/restore', [OrderController::class, 'restore'])->name('orders.restore');
            Route::get('orders/{order}/track', [OrderController::class, 'track'])->name('orders.track');

            Route::apiResource('order-details', OrderDetailController::class)->only(['index', 'show']);
            //         Route::post('order-details/{order_detail}/restore', [OrderDetailController::class, 'restore'])->name('order-details.restore');

            Route::apiResource('chart-classes', ChartClassController::class);
            //         Route::post('chart-classes/{chart_class}/restore', [ChartClassController::class, 'restore'])->name('chart-classes.restore');

            Route::apiResource('chart-types', ChartTypeController::class);
            //         Route::post('chart-types/{chart_type}/restore', [ChartTypeController::class, 'restore'])->name('chart-types.restore');

            Route::apiResource('chart-entries', ChartEntryController::class);
            //         Route::post('chart-entries/{chart_entry}/restore', [ChartEntryController::class, 'restore'])->name('chart-entries.restore');

            Route::apiResource('user-accounts', UserAccountController::class)->except('update');
            //         Route::post('user-accounts/{user_account}/restore', [UserAccountController::class, 'restore'])->name('user-accounts.restore');
            Route::get('user-accounts/{user_account}/toggle', [UserAccountController::class, 'toggle'])->name('user-accounts.toggle');

            Route::apiResource('order-queues', OrderQueueController::class)->only(['index', 'show', 'destroy']);

            Route::prefix('charts')->name('charts.')->group(function () {
                Route::get('user-account-usages-pie-chart',
                    UserAccountUsagePieChartController::class)
                    ->name('user-account-usages');

                Route::get('order-summary',
                    OrderSummaryController::class)
                    ->name('order-summary');

                Route::get('user-account-summary',
                    UserAccountSummaryController::class)
                    ->name('user-account-summary');

            });

            Route::apiResource('manual-refunds', ManualRefundController::class)
                ->only(['index', 'store', 'show']);

            Route::apiResource('reward-points', RewardPointController::class)
                ->only(['index', 'store', 'show']);
            //         Route::post('reward-points/{reward_point}/restore', [RewardPointController::class, 'restore'])->name('reward-points.restore');

            Route::apiResource('redeem-points', RedeemPointController::class)
                ->only(['index', 'store', 'show']);
            //         Route::post('redeem-points/{redeem_point}/restore', [RedeemPointController::class, 'restore'])->name('redeem-points.restore');

            //DO NOT REMOVE THIS LINE//
        });
        Route::prefix('dropdown')->name('transaction.')->group(function () {
            Route::get('transaction-forms', [TransactionFormController::class, 'dropdown'])
                ->name('transaction-forms.dropdown');
            Route::get('order-statuses', OrderStatusDropdownController::class)
                ->name('order-statuses.dropdown');
            Route::get('request-platforms', RequestPlatformDropdownController::class)
                ->name('request-platforms.dropdown');

        });
    });
}
