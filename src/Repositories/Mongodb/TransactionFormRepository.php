<?php

namespace Fintech\Transaction\Repositories\Mongodb;

use Fintech\Core\Repositories\MongodbRepository;
use Fintech\Transaction\Interfaces\TransactionFormRepository as InterfacesTransactionFormRepository;
use Fintech\Transaction\Models\TransactionForm;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Class TransactionFormRepository
 */
class TransactionFormRepository extends MongodbRepository implements InterfacesTransactionFormRepository
{
    public function __construct()
    {
        $model = app(config('fintech.transaction.transaction_form_model', TransactionForm::class));

        if (! $model instanceof Model) {
            throw new InvalidArgumentException("Mongodb repository require model class to be `MongoDB\Laravel\Eloquent\Model` instance.");
        }

        $this->model = $model;
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
        if (isset($filters['search']) && ! empty($filters['search'])) {
            if (is_numeric($filters['search'])) {
                $query->where($this->model->getKeyName(), 'like', "%{$filters['search']}%");
            } else {
                $query->where('name', 'like', "%{$filters['search']}%");
                $query->orWhere('transaction_form_data', 'like', "%{$filters['search']}%");
            }
        }

        //Display Trashed
        if (isset($filters['trashed']) && ! empty($filters['trashed'])) {
            $query->onlyTrashed();
        }

        //Handle Sorting
        $query->orderBy($filters['sort'] ?? $this->model->getKeyName(), $filters['dir'] ?? 'asc');

        //Execute Output
        return $this->executeQuery($query, $filters);

    }
}
