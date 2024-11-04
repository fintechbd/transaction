<?php

namespace Fintech\Transaction\Facades;

use Fintech\Transaction\Services\ChartClassService;
use Fintech\Transaction\Services\ChartEntryService;
use Fintech\Transaction\Services\ChartTypeService;
use Fintech\Transaction\Services\ManualRefundService;
use Fintech\Transaction\Services\OrderDetailService;
use Fintech\Transaction\Services\OrderQueueService;
use Fintech\Transaction\Services\OrderService;
use Fintech\Transaction\Services\RedeemPointService;
use Fintech\Transaction\Services\RewardPointService;
use Fintech\Transaction\Services\TransactionFormService;
use Fintech\Transaction\Services\UserAccountService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|TransactionFormService transactionForm(array $filters = null)
 * @method static \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|OrderService order(array $filters = null)
 * @method static \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|OrderDetailService orderDetail(array $filters = null)
 * @method static \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|ChartClassService chartClass(array $filters = null)
 * @method static \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|ChartTypeService chartType(array $filters = null)
 * @method static \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|ChartEntryService chartEntry(array $filters = null)
 * @method static \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|UserAccountService userAccount(array $filters = null)
 * @method static \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|OrderQueueService orderQueue(array $filters = null)
 * @method static \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|ManualRefundService manualRefund(array $filters = null)
 * @method static \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|RewardPointService rewardPoint(array $filters = null)
 * @method static \Illuminate\Contracts\Pagination\Paginator|\Illuminate\Support\Collection|RedeemPointService redeemPoint(array $filters = null)
 * @method static \Fintech\Transaction\Services\ComplianceService compliance()
 *                                                                             @method static \Fintech\Transaction\Services\PolicyService policy()
 * // Crud Service Method Point Do not Remove //
 *
 * @see \Fintech\Transaction\Transaction
 */
class Transaction extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Fintech\Transaction\Transaction::class;
    }
}
