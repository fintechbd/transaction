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
        $model = app(config('fintech.transaction.manual_refund_model', ManualRefund::class));

        if (!$model instanceof Model) {
            throw new InvalidArgumentException("Eloquent repository require model class to be `Illuminate\Database\Eloquent\Model` instance.");
        }

        $this->model = $model;
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
