<?php

namespace Fintech\Transaction\Services;


use Fintech\Transaction\Interfaces\ChartEntryRepository;

/**
 * Class ChartEntryService
 * @package Fintech\Transaction\Services
 *
 */
class ChartEntryService
{
    /**
     * ChartEntryService constructor.
     * @param ChartEntryRepository $chartEntryRepository
     */
    public function __construct(ChartEntryRepository $chartEntryRepository) {
        $this->chartEntryRepository = $chartEntryRepository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->chartEntryRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->chartEntryRepository->create($inputs);
    }

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

    public function import(array $filters)
    {
        return $this->chartEntryRepository->create($filters);
    }
}
