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
    /**
     * @return TransactionFormService
     */
    public function transactionForm()
    {
        return app(TransactionFormService::class);
    }

    /**
     * @return OrderService
     */
    public function order()
    {
        return app(OrderService::class);
    }

    /**
     * @return OrderDetailService
     */
    public function orderDetail()
    {
        return app(OrderDetailService::class);
    }

    /**
     * @return ChartClassService
     */
    public function chartClass()
    {
        return app(ChartClassService::class);
    }

    /**
     * @return ChartTypeService
     */
    public function chartType()
    {
        return app(ChartTypeService::class);
    }

    /**
     * @return ChartEntryService
     */
    public function chartEntry()
    {
        return app(ChartEntryService::class);
    }

    /**
     * @return UserAccountService
     */
    public function userAccount()
    {
        return app(UserAccountService::class);
    }

    /**
     * @return OrderQueueService
     */
    public function orderQueue()
    {
        return app(OrderQueueService::class);
    }

    /**
     * @return ManualRefundService
     */
    public function manualRefund()
    {
        return app(ManualRefundService::class);
    }

    /**
     * @return RewardPointService
     */
    public function rewardPoint()
    {
        return app(RewardPointService::class);
    }

    /**
     * @return RedeemPointService
     */
    public function redeemPoint()
    {
        return app(RedeemPointService::class);
    }

    //** Crud Service Method Point Do not Remove **//

}
