<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\ChartClassRepository;

/**
 * Class ChartClassService
 */
class ChartClassService
{
    use \Fintech\Core\Traits\HasFindWhereSearch;

    /**
     * ChartClassService constructor.
     */
    public function __construct(private readonly ChartClassRepository $chartClassRepository) {}

    public function find($id, $onlyTrashed = false)
    {
        return $this->chartClassRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->chartClassRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->chartClassRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->chartClassRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->chartClassRepository->list($filters);
    }

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->chartClassRepository->list($filters);

    }

    public function import(array $filters)
    {
        return $this->chartClassRepository->create($filters);
    }

    public function create(array $inputs = [])
    {
        return $this->chartClassRepository->create($inputs);
    }
}
