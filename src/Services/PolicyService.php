<?php

namespace Fintech\Transaction\Services;


use Fintech\Transaction\Interfaces\PolicyRepository;

/**
 * Class PolicyService
 * @package Fintech\Transaction\Services
 *
 */
class PolicyService
{
    /**
     * PolicyService constructor.
     * @param PolicyRepository $policyRepository
     */
    public function __construct(private readonly PolicyRepository $policyRepository) { }

    /**
     * @param array $filters
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->policyRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->policyRepository->create($inputs);
    }

    public function find($id, $onlyTrashed = false)
    {
        return $this->policyRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->policyRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->policyRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->policyRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->policyRepository->list($filters);
    }

    public function import(array $filters)
    {
        return $this->policyRepository->create($filters);
    }
}
