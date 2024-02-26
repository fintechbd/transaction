<?php

namespace Fintech\Transaction\Facades;

use Fintech\Transaction\Services\ChartClassService;
use Fintech\Transaction\Services\ChartEntryService;
use Fintech\Transaction\Services\ChartTypeService;
use Fintech\Transaction\Services\ManualRefundService;
use Fintech\Transaction\Services\OrderDetailService;
use Fintech\Transaction\Services\OrderQueueService;
use Fintech\Transaction\Services\OrderService;
use Fintech\Transaction\Services\TransactionFormService;
use Fintech\Transaction\Services\UserAccountService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static TransactionFormService transactionForm()
 * @method static OrderService order()
 * @method static OrderDetailService orderDetail()
 * @method static ChartClassService chartClass()
 * @method static ChartTypeService chartType()
 * @method static ChartEntryService chartEntry()
 * @method static UserAccountService userAccount()
 * @method static OrderQueueService orderQueue()
 * @method static ManualRefundService manualRefund()
 *                                                                                 // Crud Service Method Point Do not Remove //
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
