<?php

namespace Fintech\Transaction\Repositories\Mongodb;

use Fintech\Core\Repositories\MongodbRepository;
use Fintech\Transaction\Interfaces\PolicyRepository as InterfacesPolicyRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class PolicyRepository
 */
class PolicyRepository extends MongodbRepository implements InterfacesPolicyRepository
{
    public function __construct()
    {
        parent::__construct(config('fintech.transaction.policy_model', \Fintech\Transaction\Models\Policy::class));
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

        // Searching
        if (! empty($filters['search'])) {
            if (is_numeric($filters['search'])) {
                $query->where($this->model->getKeyName(), 'like', "%{$filters['search']}%");
            } else {
                $query->where('name', 'like', "%{$filters['search']}%");
                $query->orWhere('policy_data', 'like', "%{$filters['search']}%");
            }
        }

        // Display Trashed
        if (isset($filters['trashed']) && $filters['trashed'] === true) {
            $query->onlyTrashed();
        }

        // Handle Sorting
        $query->orderBy($filters['sort'] ?? $this->model->getKeyName(), $filters['dir'] ?? 'asc');

        // Execute Output
        return $this->executeQuery($query, $filters);

    }
}
