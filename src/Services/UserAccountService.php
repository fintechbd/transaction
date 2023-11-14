<?php

namespace Fintech\Transaction\Services;

use Fintech\MetaData\Facades\MetaData;
use Fintech\Transaction\Interfaces\UserAccountRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        $country = MetaData::country()->find($inputs['country_id']);

        if (! $country) {
            throw (new ModelNotFoundException())->setModel(config('fintech.metadata.country_model', \Fintech\MetaData\Models\Country::class), $inputs['present_country_id']);
        }

        $inputs['user_account_data'] = [
            'currency' => $country->currency,
            'currency_name' => $country->currency_name,
            'currency_symbol' => $country->currency_symbol,
            'deposit_amount' => 0,
            'available_amount' => 0,
            'spent_amount' => 0,
        ];

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
