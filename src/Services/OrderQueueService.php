<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\OrderQueueRepository;

/**
 * Class OrderQueueService
 */
class OrderQueueService
{
    /**
     * OrderQueueService constructor.
     */
    public function __construct(OrderQueueRepository $orderQueueRepository)
    {
        $this->orderQueueRepository = $orderQueueRepository;
    }

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->orderQueueRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->orderQueueRepository->create($inputs);
    }

    public function find($id, $onlyTrashed = false)
    {
        return $this->orderQueueRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->orderQueueRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->orderQueueRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->orderQueueRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->orderQueueRepository->list($filters);
    }

    public function import(array $filters)
    {
        return $this->orderQueueRepository->create($filters);
    }
}
