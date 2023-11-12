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

    //** Crud Service Method Point Do not Remove **//

}
