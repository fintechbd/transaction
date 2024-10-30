<?php

namespace Fintech\Transaction;

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

class Transaction
{
    public function transactionForm($filters = null)
    {
        return \singleton(TransactionFormService::class, $filters);
    }

    public function order($filters = null)
    {
        return \singleton(OrderService::class, $filters);
    }

    public function orderDetail($filters = null)
    {
        return \singleton(OrderDetailService::class, $filters);
    }

    public function chartClass($filters = null)
    {
        return \singleton(ChartClassService::class, $filters);
    }

    public function chartType($filters = null)
    {
        return \singleton(ChartTypeService::class, $filters);
    }

    public function chartEntry($filters = null)
    {
        return \singleton(ChartEntryService::class, $filters);
    }

    public function userAccount($filters = null)
    {
        return \singleton(UserAccountService::class, $filters);
    }

    public function orderQueue($filters = null)
    {
        return \singleton(OrderQueueService::class, $filters);
    }

    public function manualRefund($filters = null)
    {
        return \singleton(ManualRefundService::class, $filters);
    }

    public function rewardPoint($filters = null)
    {
        return \singleton(RewardPointService::class, $filters);
    }

    public function redeemPoint($filters = null)
    {
        return \singleton(RedeemPointService::class, $filters);
    }

    /**
     * @return \Fintech\Transaction\Services\ComplianceService
     */
    public function compliance()
    {
        return app(\Fintech\Transaction\Services\ComplianceService::class);
    }

    //** Crud Service Method Point Do not Remove **//


}
