<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Core\Repositories\EloquentRepository;
use Fintech\Transaction\Interfaces\ComplianceRepository as InterfacesComplianceRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class ComplianceRepository
 */
class ComplianceRepository extends EloquentRepository implements InterfacesComplianceRepository
{
    public function __construct()
    {
        parent::__construct(config('fintech.transaction.compliance_model', \Fintech\Transaction\Models\Compliance::class));
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

        $query->leftJoin('orders', 'compliances.order_id', '=', 'orders.id');

        //Searching
        if (! empty($filters['search'])) {
            $query->where(function ($query) use ($filters) {
                $query->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('code', 'like', "%{$filters['search']}%")
                    ->orWhere('remarks', 'like', "%{$filters['search']}%");
            });
        }

        if (! empty($filters['code'])) {
            $query->where('code', $filters['code']);
        }

        if (! empty($filters['order_number'])) {
            $query->where('order_number', $filters['order_number']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('order_number', $filters['user_id']);
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
