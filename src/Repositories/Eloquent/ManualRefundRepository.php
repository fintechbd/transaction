<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Transaction\Interfaces\ManualRefundRepository as InterfacesManualRefundRepository;
use Fintech\Transaction\Models\ManualRefund;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

/**
 * Class ManualRefundRepository
 */
class ManualRefundRepository extends OrderRepository implements InterfacesManualRefundRepository
{
    public function __construct()
    {
        parent::__construct(config('fintech.transaction.manual_refund_model', ManualRefund::class));
    }

    /**
     * return a list or pagination of items from
     * filtered options
     *
     * @return Paginator|Collection
     *
     * @throws BindingResolutionException
     */
    public function list(array $filters = [])
    {
        return parent::list($filters);
    }
}
