<?php

namespace Fintech\Transaction;

use Fintech\Core\Abstracts\BaseModel;
use Fintech\Transaction\Services\ChartClassService;
use Fintech\Transaction\Services\ChartEntryService;
use Fintech\Transaction\Services\ChartTypeService;
use Fintech\Transaction\Services\ComplianceService;
use Fintech\Transaction\Services\ManualRefundService;
use Fintech\Transaction\Services\OrderDetailService;
use Fintech\Transaction\Services\OrderQueueService;
use Fintech\Transaction\Services\OrderService;
use Fintech\Transaction\Services\PolicyService;
use Fintech\Transaction\Services\RedeemPointService;
use Fintech\Transaction\Services\RewardPointService;
use Fintech\Transaction\Services\TransactionFormService;
use Fintech\Transaction\Services\UserAccountService;
use Fintech\Transaction\Support\Accounting;
use Illuminate\Database\Eloquent\Collection;

class Transaction
{
    /**
     * @param $filters
     * @return TransactionFormService|Collection|BaseModel
     */
    public function transactionForm($filters = null)
    {
        return \singleton(TransactionFormService::class, $filters);
    }

    /**
     * @param $filters
     * @return OrderService|Collection|BaseModel
     */
    public function order($filters = null)
    {
        return \singleton(OrderService::class, $filters);
    }

    /**
     * @param $filters
     * @return OrderDetailService|Collection|BaseModel
     */
    public function orderDetail($filters = null)
    {
        return \singleton(OrderDetailService::class, $filters);
    }

    /**
     * @param $filters
     * @return ChartClassService|Collection|BaseModel
     */
    public function chartClass($filters = null)
    {
        return \singleton(ChartClassService::class, $filters);
    }

    /**
     * @param $filters
     * @return ChartTypeService|Collection|BaseModel
     */
    public function chartType($filters = null)
    {
        return \singleton(ChartTypeService::class, $filters);
    }

    /**
     * @param $filters
     * @return ChartEntryService|Collection|BaseModel
     */
    public function chartEntry($filters = null)
    {
        return \singleton(ChartEntryService::class, $filters);
    }

    /**
     * @param $filters
     * @return UserAccountService|Collection|BaseModel
     */
    public function userAccount($filters = null)
    {
        return \singleton(UserAccountService::class, $filters);
    }

    /**
     * @param $filters
     * @return OrderQueueService|Collection|BaseModel
     */
    public function orderQueue($filters = null)
    {
        return \singleton(OrderQueueService::class, $filters);
    }

    /**
     * @param $filters
     * @return ManualRefundService|Collection|BaseModel
     */
    public function manualRefund($filters = null)
    {
        return \singleton(ManualRefundService::class, $filters);
    }

    /**
     * @param $filters
     * @return RewardPointService|Collection|BaseModel
     */
    public function rewardPoint($filters = null)
    {
        return \singleton(RewardPointService::class, $filters);
    }

    /**
     * @param $filters
     * @return RedeemPointService|Collection|BaseModel
     */
    public function redeemPoint($filters = null)
    {
        return \singleton(RedeemPointService::class, $filters);
    }

    /**
     * @param $filters
     * @return ComplianceService|Collection|BaseModel
     */
    public function compliance($filters = null)
    {
        return \singleton(ComplianceService::class, $filters);
    }

    /**
     * @param $filters
     * @return PolicyService|Collection|BaseModel
     */
    public function policy($filters = null)
    {
        return \singleton(PolicyService::class, $filters);
    }

    /**
     * @param BaseModel $order
     * @param string|int|null $user_id
     * @return Accounting
     */
    public function accounting(\Fintech\Core\Abstracts\BaseModel $order, string|int|null $user_id = null)
    {
        return new Accounting($order, $user_id);
    }

    // ** Crud Service Method Point Do not Remove **//

}
