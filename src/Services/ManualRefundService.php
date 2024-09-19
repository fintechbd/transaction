<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\ManualRefundRepository;

/**
 * Class ManualRefundService
 */
class ManualRefundService extends \Fintech\Core\Abstracts\Service
{
    /**
     * ManualRefundService constructor.
     */
    public function __construct(ManualRefundRepository $manualRefundRepository)
    {
        $this->manualRefundRepository = $manualRefundRepository;
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

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->manualRefundRepository->list($filters);

    }

    public function import(array $filters)
    {
        return $this->manualRefundRepository->create($filters);
    }

    public function create(array $inputs = [])
    {
        return $this->manualRefundRepository->create($inputs);
    }
}
