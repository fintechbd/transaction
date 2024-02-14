<?php

namespace Fintech\Transaction\Services;


use Fintech\Transaction\Interfaces\ManualRefundRepository;

/**
 * Class ManualRefundService
 * @package Fintech\Transaction\Services
 *
 */
class ManualRefundService
{
    /**
     * ManualRefundService constructor.
     * @param ManualRefundRepository $manualRefundRepository
     */
    public function __construct(ManualRefundRepository $manualRefundRepository) {
        $this->manualRefundRepository = $manualRefundRepository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->manualRefundRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->manualRefundRepository->create($inputs);
    }

    public function find($id, $onlyTrashed = false)
    {
        return $this->manualRefundRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->manualRefundRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->manualRefundRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->manualRefundRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->manualRefundRepository->list($filters);
    }

    public function import(array $filters)
    {
        return $this->manualRefundRepository->create($filters);
    }
}
