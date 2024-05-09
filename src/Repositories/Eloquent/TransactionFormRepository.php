<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Core\Repositories\EloquentRepository;
use Fintech\Transaction\Interfaces\TransactionFormRepository as InterfacesTransactionFormRepository;
use Fintech\Transaction\Models\TransactionForm;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TransactionFormRepository
 */
class TransactionFormRepository extends EloquentRepository implements InterfacesTransactionFormRepository
{
    public function __construct()
    {
        parent::__construct(config('fintech.transaction.transaction_form_model', TransactionForm::class));
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
        $modelTable = $this->model->getTable();

        //Searching
        if (! empty($filters['search'])) {
            if (is_numeric($filters['search'])) {
                $query->where($this->model->getKeyName(), 'like', "%{$filters['search']}%");
            } else {
                $query->where($modelTable.'.name', 'like', "%{$filters['search']}%");
                $query->orWhere($modelTable.'.transaction_form_data', 'like', "%{$filters['search']}%");
            }
        }

        if (! empty($filters['code'])) {
            $query->where($modelTable.'.code', $filters['code']);
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
