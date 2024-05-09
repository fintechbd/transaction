<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Core\Repositories\EloquentRepository;
use Fintech\Transaction\Interfaces\UserAccountRepository as InterfacesUserAccountRepository;
use Fintech\Transaction\Models\UserAccount;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class UserAccountRepository
 */
class UserAccountRepository extends EloquentRepository implements InterfacesUserAccountRepository
{
    public function __construct()
    {
        parent::__construct(config('fintech.transaction.user_account_model', UserAccount::class));
    }

    /**
     * return a list or pagination of items from
     * filtered options
     *
     * @return Paginator|Collection
     */
    public function list(array $filters = [])
    {
        $query = $this->model->newQuery();

        //Searching
        if (!empty($filters['search'])) {
            if (is_numeric($filters['search'])) {
                $query->where($this->model->getKeyName(), 'like', "%{$filters['search']}%");
            } else {
                $query->where('name', 'like', "%{$filters['search']}%");
                $query->orWhere('user_account_data', 'like', "%{$filters['search']}%");
            }
        }

        if (!empty($filters['currency'])) {
            $query->where('user_account_data->currency', '=', $filters['currency']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', '=', $filters['user_id']);
        }

        if (!empty($filters['country_id'])) {
            $query->where('country_id', '=', $filters['country_id']);
        }

        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        }

        //Display Trashed
        if (isset($filters['trashed']) && $filters['trashed'] === true) {
            $query->onlyTrashed();
        }

        //Handle Sorting
        $query->orderBy($filters['sort'] ?? $this->model->getKeyName(), $filters['dir'] ?? 'asc');

        //Execute Output
        return $this->executeQuery($query, $filters);

    }
}
