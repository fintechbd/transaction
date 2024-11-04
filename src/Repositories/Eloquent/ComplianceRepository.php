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
        $query->leftJoin('users', 'compliances.user_id', '=', 'users.id');
        $query->leftJoin('countries as source_country', 'orders.source_country_id', '=', 'source_country.id');
        $query->leftJoin('countries as destination_country', 'orders.destination_country_id', '=', 'destination_country.id');

        //Searching
        if (! empty($filters['search'])) {
            $query->where(function ($query) use ($filters) {
                return $query->where('compliances.name', 'like', "%{$filters['search']}%")
                    ->orWhere('compliances.code', 'like', "%{$filters['search']}%")
                    ->orWhere('compliances.remarks', 'like', "%{$filters['search']}%")
                    ->orWhere('orders.order_number', 'like', "%{$filters['search']}%")
                    ->orWhere('orders.order_data->purchase_number', 'like', "%{$filters['search']}%")
                    ->orWhere('orders.order_data->accept_number', 'like', "%{$filters['search']}%")
                    ->orWhere('orders.order_data->reject_number', 'like', "%{$filters['search']}%");
            });
        }

        if (! empty($filters['code'])) {
            $query->where('compliances.code', $filters['code']);
        }

        if (! empty($filters['priority'])) {
            $query->where('compliances.priority', $filters['priority']);
        }

        if (! empty($filters['risk'])) {
            $query->where('compliances.risk', $filters['risk']);
        }

        if (! empty($filters['order_status'])) {
            $query->where('orders.status', $filters['order_status']);
        }

        if (! empty($filters['order_number'])) {
            $query->where(function ($query) use ($filters) {
                return $query->where('orders.order_number', $filters['order_number'])
                    ->orWhere('orders.order_data->purchase_number', $filters['order_number'])
                    ->orWhere('orders.order_data->accept_number', $filters['order_number'])
                    ->orWhere('orders.order_data->reject_number', $filters['order_number']);
            });
        }

        if (! empty($filters['user_id'])) {
            $query->where('compliances.user_id', $filters['user_id']);
        }

        if (! empty($filters['source_country_id'])) {
            $query->where('orders.source_country_id', $filters['source_country_id']);
        }

        if (! empty($filters['destination_country_id'])) {
            $query->where('orders.destination_country_id', $filters['destination_country_id']);
        }

        //Display Trashed
        if (isset($filters['trashed']) && $filters['trashed'] === true) {
            $query->onlyTrashed();
        }

        //Handle Sorting
        $query->orderBy($filters['sort'] ?? $this->model->getKeyName(), $filters['dir'] ?? 'asc');

        $query->select([
            'compliances.*',
            'orders.description as description',
            'users.name as user_name',
            'users.mobile as user_mobile',
            'orders.order_number',
            'orders.order_data',
            'source_country.name as source_country_name',
            'destination_country.name as destination_country_name',
            'orders.status as order_status',
        ]);

        //Execute Output
        return $this->executeQuery($query, $filters);

    }
}
