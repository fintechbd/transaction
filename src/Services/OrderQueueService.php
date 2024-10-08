<?php

namespace Fintech\Transaction\Services;

use Fintech\Core\Abstracts\BaseModel;
use Fintech\Transaction\Interfaces\OrderQueueRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Class OrderQueueService
 */
class OrderQueueService
{
    use \Fintech\Core\Traits\HasFindWhereSearch;

    /**
     * OrderQueueService constructor.
     */
    public function __construct(private readonly OrderQueueRepository $orderQueueRepository) {}

    public function find($id, bool $onlyTrashed = false): ?BaseModel
    {
        return $this->orderQueueRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = []): ?BaseModel
    {
        return $this->orderQueueRepository->update($id, $inputs);
    }

    public function destroy($id): mixed
    {
        return $this->orderQueueRepository->delete($id);
    }

    public function restore($id): mixed
    {
        return $this->orderQueueRepository->restore($id);
    }

    public function export(array $filters): Paginator|Collection
    {
        return $this->orderQueueRepository->list($filters);
    }

    public function list(array $filters = []): Collection|Paginator
    {
        return $this->orderQueueRepository->list($filters);

    }

    public function import(array $filters): ?BaseModel
    {
        return $this->orderQueueRepository->create($filters);
    }

    public function create(array $inputs = []): ?BaseModel
    {
        return $this->orderQueueRepository->create($inputs);
    }

    public function addToQueueUserWise($user_id): mixed
    {
        return $this->orderQueueRepository->addToQueueSenderWise($user_id);
    }

    public function removeFromQueueUserWise($user_id): mixed
    {
        return $this->orderQueueRepository->removeFromQueueSenderWise($user_id);
    }

    public function addToQueueOrderWise($order_id): mixed
    {
        return $this->orderQueueRepository->addToQueueOrderWise($order_id);
    }

    public function removeFromQueueOrderWise($order_id): mixed
    {
        return $this->orderQueueRepository->removeFromQueueOrderWise($order_id);
    }
}
