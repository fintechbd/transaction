<?php

namespace Fintech\Transaction;

class Transaction
{
    /**
     * @return \Fintech\Transaction\Services\TransactionFormService
     */
    public function transactionForm()
    {
        return app(\Fintech\Transaction\Services\TransactionFormService::class);
    }

    /**
     * @return \Fintech\Transaction\Services\OrderService
     */
    public function order()
    {
        return app(\Fintech\Transaction\Services\OrderService::class);
    }

    /**
     * @return \Fintech\Transaction\Services\OrderDetailService
     */
    public function orderDetail()
    {
        return app(\Fintech\Transaction\Services\OrderDetailService::class);
    }

    /**
     * @return \Fintech\Transaction\Services\ChartClassService
     */
    public function chartClass()
    {
        return app(\Fintech\Transaction\Services\ChartClassService::class);
    }

    /**
     * @return \Fintech\Transaction\Services\ChartTypeService
     */
    public function chartType()
    {
        return app(\Fintech\Transaction\Services\ChartTypeService::class);
    }

    /**
     * @return \Fintech\Transaction\Services\ChartEntryService
     */
    public function chartEntry()
    {
        return app(\Fintech\Transaction\Services\ChartEntryService::class);
    }

    /**
     * @return \Fintech\Transaction\Services\UserAccountService
     */
    public function userAccount()
    {
        return app(\Fintech\Transaction\Services\UserAccountService::class);
    }

    //** Crud Service Method Point Do not Remove **//





}
