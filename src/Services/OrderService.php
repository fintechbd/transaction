<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\OrderRepository;

/**
 * Class OrderService
 */
class OrderService
{
    /**
     * OrderService constructor.
     */
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->orderRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->orderRepository->create($inputs);
    }

    public function find($id, $onlyTrashed = false)
    {
        return $this->orderRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->orderRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->orderRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->orderRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->orderRepository->list($filters);
    }

    public function import(array $filters)
    {
        return $this->orderRepository->create($filters);
    }
}
