<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Core\Repositories\EloquentRepository;
use Fintech\Transaction\Interfaces\ManualRefundRepository as InterfacesManualRefundRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Class ManualRefundRepository
 * @package Fintech\Transaction\Repositories\Eloquent
 */
class ManualRefundRepository extends OrderRepository implements InterfacesManualRefundRepository
{
    public function __construct()
    {
       $model = app(config('fintech.transaction.manual_refund_model', \Fintech\Transaction\Models\ManualRefund::class));

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
     * @throws BindingResolutionException
     */
    public function list(array $filters = [])
    {
        return parent::list($filters);
    }
}
