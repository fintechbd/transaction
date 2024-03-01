<?php

namespace Fintech\Transaction\Services;


use Fintech\Transaction\Interfaces\RewardPointRepository;

/**
 * Class RewardPointService
 * @package Fintech\Transaction\Services
 *
 */
class RewardPointService
{
    /**
     * RewardPointService constructor.
     * @param RewardPointRepository $rewardPointRepository
     */
    public function __construct(RewardPointRepository $rewardPointRepository) {
        $this->rewardPointRepository = $rewardPointRepository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->rewardPointRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->rewardPointRepository->create($inputs);
    }

    public function find($id, $onlyTrashed = false)
    {
        return $this->rewardPointRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->rewardPointRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->rewardPointRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->rewardPointRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->rewardPointRepository->list($filters);
    }

    public function import(array $filters)
    {
        return $this->rewardPointRepository->create($filters);
    }
}
