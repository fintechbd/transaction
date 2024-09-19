<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\ChartEntryRepository;

/**
 * Class ChartEntryService
 */
class ChartEntryService
{
    use \Fintech\Core\Traits\HasFindWhereSearch;

    /**
     * ChartEntryService constructor.
     */
    public function __construct(private readonly ChartEntryRepository $chartEntryRepository) {}

    public function find($id, $onlyTrashed = false)
    {
        return $this->chartEntryRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->chartEntryRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->chartEntryRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->chartEntryRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->chartEntryRepository->list($filters);
    }

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->chartEntryRepository->list($filters);

    }

    public function import(array $filters)
    {
        return $this->chartEntryRepository->create($filters);
    }

    public function create(array $inputs = [])
    {
        return $this->chartEntryRepository->create($inputs);
    }
}
