<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\TransactionFormRepository;

/**
 * Class TransactionFormService
 */
class TransactionFormService
{
    /**
     * TransactionFormService constructor.
     */
    public function __construct(private readonly TransactionFormRepository $transactionFormRepository) {}

    public function find($id, $onlyTrashed = false)
    {
        return $this->transactionFormRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->transactionFormRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->transactionFormRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->transactionFormRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->transactionFormRepository->list($filters);
    }

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->transactionFormRepository->list($filters);

    }

    public function import(array $filters)
    {
        return $this->transactionFormRepository->create($filters);
    }

    public function create(array $inputs = [])
    {
        return $this->transactionFormRepository->create($inputs);
    }
}
