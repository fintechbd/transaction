<?php

namespace Fintech\Transaction\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Fintech\Transaction\Services\TransactionFormService transactionForm()
 * @method static \Fintech\Transaction\Services\OrderService order()
 * @method static \Fintech\Transaction\Services\OrderDetailService orderDetail()
 * @method static \Fintech\Transaction\Services\ChartClassService chartClass()
 * @method static \Fintech\Transaction\Services\ChartTypeService chartType()
 * @method static \Fintech\Transaction\Services\ChartEntryService chartEntry()
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
