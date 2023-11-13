<?php

namespace Fintech\Transaction\Services;

use Fintech\Transaction\Interfaces\UserAccountRepository;

/**
 * Class UserAccountService
 */
class UserAccountService
{
    /**
     * UserAccountService constructor.
     */
    public function __construct(UserAccountRepository $userAccountRepository)
    {
        $this->userAccountRepository = $userAccountRepository;
    }

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->userAccountRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->userAccountRepository->create($inputs);
    }

    public function find($id, $onlyTrashed = false)
    {
        return $this->userAccountRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->userAccountRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->userAccountRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->userAccountRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->userAccountRepository->list($filters);
    }

    public function import(array $filters)
    {
        return $this->userAccountRepository->create($filters);
    }
}
