<?php

namespace Fintech\Transaction\Services;


use Fintech\Transaction\Interfaces\ComplianceRepository;

/**
 * Class ComplianceService
 * @package Fintech\Transaction\Services
 *
 */
class ComplianceService
{
    /**
     * ComplianceService constructor.
     * @param ComplianceRepository $complianceRepository
     */
    public function __construct(private readonly ComplianceRepository $complianceRepository) { }

    /**
     * @param array $filters
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->complianceRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->complianceRepository->create($inputs);
    }

    public function find($id, $onlyTrashed = false)
    {
        return $this->complianceRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->complianceRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->complianceRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->complianceRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->complianceRepository->list($filters);
    }

    public function import(array $filters)
    {
        return $this->complianceRepository->create($filters);
    }
}
