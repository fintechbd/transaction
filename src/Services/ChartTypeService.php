<?php

namespace Fintech\Transaction\Services;


use Fintech\Transaction\Interfaces\ChartTypeRepository;

/**
 * Class ChartTypeService
 * @package Fintech\Transaction\Services
 *
 */
class ChartTypeService
{
    /**
     * ChartTypeService constructor.
     * @param ChartTypeRepository $chartTypeRepository
     */
    public function __construct(ChartTypeRepository $chartTypeRepository) {
        $this->chartTypeRepository = $chartTypeRepository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->chartTypeRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->chartTypeRepository->create($inputs);
    }

    public function find($id, $onlyTrashed = false)
    {
        return $this->chartTypeRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->chartTypeRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->chartTypeRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->chartTypeRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->chartTypeRepository->list($filters);
    }

    public function import(array $filters)
    {
        return $this->chartTypeRepository->create($filters);
    }
}
