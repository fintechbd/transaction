<?php

namespace Fintech\Transaction\Facades;

use Fintech\Core\Abstracts\BaseModel;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Paginator|Collection|\Fintech\Transaction\Services\TransactionFormService transactionForm(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\OrderService order(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\OrderDetailService orderDetail(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\ChartClassService chartClass(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\ChartTypeService chartType(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\ChartEntryService chartEntry(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\UserAccountService userAccount(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\OrderQueueService orderQueue(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\ManualRefundService manualRefund(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\RewardPointService rewardPoint(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\RedeemPointService redeemPoint(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\ComplianceService compliance(array $filters = null)
 * @method static Paginator|Collection|\Fintech\Transaction\Services\PolicyService policy(array $filters = null)
 * @method static \Fintech\Transaction\Support\Accounting accounting(BaseModel $order, string|int $user_id = null)
 *
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
