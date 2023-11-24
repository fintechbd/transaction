<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Core\Repositories\EloquentRepository;
use Fintech\Transaction\Interfaces\OrderRepository as InterfacesOrderRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Class OrderRepository
 */
class OrderRepository extends EloquentRepository implements InterfacesOrderRepository
{
    public function __construct()
    {
        $model = app(config('fintech.transaction.order_model', \Fintech\Transaction\Models\Order::class));

        if (! $model instanceof Model) {
            throw new InvalidArgumentException("Eloquent repository require model class to be `Illuminate\Database\Eloquent\Model` instance.");
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
        $modelTable = $this->model->getTable();
        //Searching
        if (! empty($filters['search'])) {
            if (is_numeric($filters['search'])) {
                $query->where($this->model->getKeyName(), 'like', "%{$filters['search']}%");
            } else {
                //$query->where('name', 'like', "%{$filters['search']}%");
                $query->orWhere($modelTable.'.order_data', 'like', "%{$filters['search']}%");
            }
        }

        if (isset($filter['user_id_not_in']) && $filter['user_id_not_in']) {
            $query->whereNotIn($modelTable.'.user_id', $filter['user_id_not_in']);
        }

        if (isset($filter['order_id']) && $filter['order_id']) {
            $query->where($modelTable.'.id', '=', $filter['order_id']);
        }

        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where($modelTable.'.user_id', '=', $filters['user_id']);
        }

        if (isset($filter['$filters']) && $filters['sender_receiver_id']) {
            $query->where($modelTable.'.sender_receiver_id', '=', $filters['sender_receiver_id']);
        }
        if (isset($filter['user_id_sender_receiver_id']) && $filter['user_id_sender_receiver_id']) {
            $query->where(function ($query) use ($filter, $modelTable) {
                $query->where($modelTable.'.user_id', $filter['user_id_sender_receiver_id']);
                $query->orWhere($modelTable.'.sender_receiver_id', $filter['user_id_sender_receiver_id']);
            });
            /*$query->where($modelTable.'.user_id', '=', $filter['user_id_sender_receiver_id']);
            $query->orWhere($modelTable.'.sender_receiver_id', '=', $filter['user_id_sender_receiver_id']);*/
        }

        if (isset($filter['service_id']) && $filter['service_id']) {
            $query->where($modelTable.'.service_id', '=', $filter['service_id']);
        }
        if (isset($filter['service_id_in']) && $filter['service_id_in']) {
            $query->whereIn($modelTable.'.service_id', $filter['service_id_in']);
        }

        if (isset($filter['transaction_form_id']) && $filter['transaction_form_id']) {
            $query->where($modelTable.'.transaction_form_id', '=', $filter['transaction_form_id']);
        }

        if (isset($filter['transaction_form_id_in']) && $filter['transaction_form_id_in']) {
            $query->whereIn($modelTable.'.transaction_form_id', $filter['transaction_form_id_in']);
        }

        if (isset($filter['transaction_form_id_not_in']) && $filter['transaction_form_id_not_in']) {
            $query->whereNotIn($modelTable.'.transaction_form_id', $filter['transaction_form_id_not_in']);
        }

        if (isset($filter['transaction_form_id_not_equal']) && $filter['transaction_form_id_not_equal']) {
            $query->where($modelTable.'.transaction_form_id', '!=', $filter['transaction_form_id_not_equal']);
        }

        if (isset($filter['amount']) && $filter['amount']) {
            $query->where($modelTable.'.amount', '=', $filter['amount']);
        }

        if (isset($filter['order_start_date_time']) && $filter['order_start_date_time'] != '0000-00-00' && $filter['order_start_date_time'] != '' &&
            isset($filter['order_end_date_time']) && $filter['order_end_date_time'] != '0000-00-00' && $filter['order_end_date_time'] != ''
        ) {
            $query->whereBetween($modelTable.'.order_date', [$filter['order_start_date_time'], $filter['order_end_date_time']]);
        }

        if (isset($filter['order_start_date']) && $filter['order_start_date'] != '0000-00-00' && $filter['order_start_date'] != '' &&
            isset($filter['order_end_date']) && $filter['order_end_date'] != '0000-00-00' && $filter['order_end_date'] != ''
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'.ordered_at)'), [$filter['order_start_date'], $filter['order_end_date']]);
        }

        if (isset($filter['order_start_date']) && $filter['order_start_date'] != '0000-00-00' && $filter['order_start_date'] != '' &&
            empty($filter['order_end_date'])
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'.ordered_at)'), [$filter['order_start_date'], $filter['order_start_date']]);
        }

        if (isset($filter['order_end_date']) && $filter['order_end_date'] != '0000-00-00' && $filter['order_end_date'] != '' &&
            empty($filter['order_start_date'])
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'.ordered_at)'), [$filter['order_end_date'], $filter['order_end_date']]);
        }

        if (isset($filter['order_date']) && $filter['order_date'] != '0000-00-00' && $filter['order_date'] != '') {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'.ordered_at)'), [$filter['order_date'], $filter['order_date']]);
        }

        if (isset($filter['created_at_start_date_time']) && $filter['created_at_start_date_time'] != '0000-00-00'
            && $filter['created_at_start_date_time'] != '' &&
            isset($filter['created_at_end_date_time']) && $filter['created_at_end_date_time'] != '0000-00-00'
            && $filter['created_at_end_date_time'] != ''
        ) {
            $query->whereBetween($modelTable.'.created_at', [$filter['created_at_start_date_time'], $filter['created_at_end_date_time']]);
        }

        if (isset($filter['created_at_start_date']) && $filter['created_at_start_date'] != '0000-00-00' && $filter['created_at_start_date'] != '' &&
            isset($filter['created_at_end_date']) && $filter['created_at_end_date'] != '0000-00-00' && $filter['created_at_end_date'] != ''
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'..created_at)'), [$filter['created_at_start_date'], $filter['created_at_end_date']]);
        }

        if (isset($filter['created_at_start_date']) && $filter['created_at_start_date'] != '0000-00-00' && $filter['created_at_start_date'] != '' &&
            empty($filter['created_at_end_date'])
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'..created_at)'), [$filter['created_at_start_date'], $filter['created_at_start_date']]);
        }

        if (isset($filter['created_at_end_date']) && $filter['created_at_end_date'] != '0000-00-00' && $filter['created_at_end_date'] != '' &&
            empty($filter['created_at_start_date'])
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'..created_at)'), [$filter['created_at_end_date'], $filter['created_at_end_date']]);
        }

        if (isset($filter['created_at_date']) && $filter['created_at_date'] != '0000-00-00' && $filter['created_at_date'] != '') {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'..created_at)'), [$filter['created_at_date'], $filter['created_at_date']]);
        }

        if (isset($filter['status']) && $filter['status']) {
            $query->whereIn($modelTable.'.status', $filter['status']);
        }

        if (isset($filter['status_not_equal']) && $filter['status_not_equal']) {
            $query->where($modelTable.'.status', '!=', $filter['status_not_equal']);
        }

        if (isset($filter['source_country_id']) && $filter['source_country_id']) {
            $query->whereIn($modelTable.'.source_country_id', $filter['source_country_id']);
        }

        if (! empty($filter['source_country_id_array'])) {
            $query->whereIn($modelTable.'.source_country_id', $filter['source_country_id_array']);
        }

        if (! empty($filter['destination_country_id'])) {
            $query->where($modelTable.'.destination_country_id', $filter['destination_country_id']);
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
