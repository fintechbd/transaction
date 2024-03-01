<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\RedeemPointRepository;

/**
 * Class RedeemPointService
 */
class RedeemPointService
{
    /**
     * RedeemPointService constructor.
     */
    public function __construct(RedeemPointRepository $redeemPointRepository)
    {
        $this->redeemPointRepository = $redeemPointRepository;
    }

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->redeemPointRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->redeemPointRepository->create($inputs);
    }

    public function find($id, $onlyTrashed = false)
    {
        return $this->redeemPointRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->redeemPointRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->redeemPointRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->redeemPointRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->redeemPointRepository->list($filters);
    }

    public function import(array $filters)
    {
        return $this->redeemPointRepository->create($filters);
    }
}
