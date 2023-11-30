<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\OrderQueueRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class OrderQueueService
 */
class OrderQueueService
{
    /**
     * OrderQueueService constructor.
     */
    public function __construct(private readonly OrderQueueRepository $orderQueueRepository)
    {
    }

    /**
     * @param array $filters
     * @return Collection|Paginator
     */
    public function list(array $filters = []): Collection|Paginator
    {
        return $this->orderQueueRepository->list($filters);

    }

    /**
     * @param array $inputs
     * @return Model|\MongoDB\Laravel\Eloquent\Model|null
     */
    public function create(array $inputs = []): Model|\MongoDB\Laravel\Eloquent\Model|null
    {
        return $this->orderQueueRepository->create($inputs);
    }

    /**
     * @param $id
     * @param bool $onlyTrashed
     * @return Model|\MongoDB\Laravel\Eloquent\Model|null
     */
    public function find($id, bool $onlyTrashed = false): Model|\MongoDB\Laravel\Eloquent\Model|null
    {
        return $this->orderQueueRepository->find($id, $onlyTrashed);
    }

    /**
     * @param $id
     * @param array $inputs
     * @return Model|\MongoDB\Laravel\Eloquent\Model|null
     */
    public function update($id, array $inputs = []): Model|\MongoDB\Laravel\Eloquent\Model|null
    {
        return $this->orderQueueRepository->update($id, $inputs);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id): mixed
    {
        return $this->orderQueueRepository->delete($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function restore($id): mixed
    {
        return $this->orderQueueRepository->restore($id);
    }

    /**
     * @param array $filters
     * @return Paginator|Collection
     */
    public function export(array $filters): Paginator|Collection
    {
        return $this->orderQueueRepository->list($filters);
    }

    /**
     * @param array $filters
     * @return Model|\MongoDB\Laravel\Eloquent\Model|null
     */
    public function import(array $filters): Model|\MongoDB\Laravel\Eloquent\Model|null
    {
        return $this->orderQueueRepository->create($filters);
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function addToQueueUserWise($user_id): mixed
    {
        return $this->orderQueueRepository->addToQueueSenderWise($user_id);
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public function removeFromQueueUserWise($user_id): mixed
    {
        return $this->orderQueueRepository->removeFromQueueSenderWise($user_id);
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function addToQueueOrderWise($order_id): mixed
    {
        return $this->orderQueueRepository->addToQueueOrderWise($order_id);
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function removeFromQueueOrderWise($order_id): mixed
    {
        return $this->orderQueueRepository->removeFromQueueOrderWise($order_id);
    }
}
