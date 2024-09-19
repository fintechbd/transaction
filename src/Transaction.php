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
    public function transactionForm($filters = null)
{
	return \singleton(TransactionFormService::class, $filters);
    }

    /**
     * @return OrderService
     */
    public function order($filters = null)
{
	return \singleton(OrderService::class, $filters);
    }

    /**
     * @return OrderDetailService
     */
    public function orderDetail($filters = null)
{
	return \singleton(OrderDetailService::class, $filters);
    }

    /**
     * @return ChartClassService
     */
    public function chartClass($filters = null)
{
	return \singleton(ChartClassService::class, $filters);
    }

    /**
     * @return ChartTypeService
     */
    public function chartType($filters = null)
{
	return \singleton(ChartTypeService::class, $filters);
    }

    /**
     * @return ChartEntryService
     */
    public function chartEntry($filters = null)
{
	return \singleton(ChartEntryService::class, $filters);
    }

    /**
     * @return UserAccountService
     */
    public function userAccount($filters = null)
{
	return \singleton(UserAccountService::class, $filters);
    }

    /**
     * @return OrderQueueService
     */
    public function orderQueue($filters = null)
{
	return \singleton(OrderQueueService::class, $filters);
    }

    /**
     * @return ManualRefundService
     */
    public function manualRefund($filters = null)
{
	return \singleton(ManualRefundService::class, $filters);
    }

    /**
     * @return RewardPointService
     */
    public function rewardPoint($filters = null)
{
	return \singleton(RewardPointService::class, $filters);
    }

    /**
     * @return RedeemPointService
     */
    public function redeemPoint($filters = null)
{
	return \singleton(RedeemPointService::class, $filters);
    }

    //** Crud Service Method Point Do not Remove **//

}
