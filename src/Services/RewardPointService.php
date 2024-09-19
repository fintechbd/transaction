<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\RewardPointRepository;

/**
 * Class RewardPointService
 */
class RewardPointService extends \Fintech\Core\Abstracts\Service
{
    /**
     * RewardPointService constructor.
     */
    public function __construct(RewardPointRepository $rewardPointRepository)
    {
        $this->rewardPointRepository = $rewardPointRepository;
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

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->rewardPointRepository->list($filters);

    }

    public function import(array $filters)
    {
        return $this->rewardPointRepository->create($filters);
    }

    public function create(array $inputs = [])
    {
        return $this->rewardPointRepository->create($inputs);
    }
}
