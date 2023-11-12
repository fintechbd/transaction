<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\OrderDetailRepository;

/**
 * Class OrderDetailService
 */
class OrderDetailService
{
    /**
     * OrderDetailService constructor.
     */
    public function __construct(OrderDetailRepository $orderDetailRepository)
    {
        $this->orderDetailRepository = $orderDetailRepository;
    }

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->orderDetailRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->orderDetailRepository->create($inputs);
    }

    public function find($id, $onlyTrashed = false)
    {
        return $this->orderDetailRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->orderDetailRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->orderDetailRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->orderDetailRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->orderDetailRepository->list($filters);
    }

    public function import(array $filters)
    {
        return $this->orderDetailRepository->create($filters);
    }
}
