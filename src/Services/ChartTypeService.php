<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\ChartTypeRepository;

/**
 * Class ChartTypeService
 */
class ChartTypeService
{
    /**
     * ChartTypeService constructor.
     */
    public function __construct(private readonly ChartTypeRepository $chartTypeRepository)
    {
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

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->chartTypeRepository->list($filters);

    }

    public function import(array $filters)
    {
        return $this->chartTypeRepository->create($filters);
    }

    public function create(array $inputs = [])
    {
        return $this->chartTypeRepository->create($inputs);
    }
}
