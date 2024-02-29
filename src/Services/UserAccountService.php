<?php

namespace Fintech\Transaction\Services;

use Fintech\MetaData\Facades\MetaData;
use Fintech\MetaData\Models\Country;
use Fintech\Transaction\Interfaces\UserAccountRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

/**
 * Class UserAccountService
 */
class UserAccountService
{
    /**
     * UserAccountService constructor.
     */
    public function __construct(private readonly UserAccountRepository $userAccountRepository)
    {
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

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->userAccountRepository->list($filters);

    }

    public function import(array $filters)
    {
        return $this->userAccountRepository->create($filters);
    }

    public function create(array $inputs = [])
    {
        $country = MetaData::country()->find($inputs['country_id']);

        if (! $country) {
            throw (new ModelNotFoundException())->setModel(config('fintech.metadata.country_model', Country::class), $inputs['present_country_id']);
        }

        $inputs['account_no'] = $this->guessNextAccountNumber($country->getKey());
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

    public function guessNextAccountNumber($country_id): string
    {
        $accounts = $this->list(['country_id' => $country_id, 'sort' => 'id', 'dir' => 'desc', 'limit' => 1]);

        if ($accounts->isEmpty()) {
            return $this->formatAccountNumber($country_id, '1');
        }

        $lastEntry = $accounts->first();

        $account_info = [];

        preg_match('/^(\d{3})(\d{8})$/', $lastEntry->account_no, $account_info);
        //@TODO: worst case senior
        if (empty($account_info[2])) {

        }

        $newEntry = intval($account_info[2]) + 1;

        return $this->formatAccountNumber($country_id, $newEntry);

    }

    private function formatAccountNumber($county_id, $entry_number): string
    {
        return Str::padLeft($county_id, 3, '0').Str::padLeft($entry_number, 8, '0');
    }
}
