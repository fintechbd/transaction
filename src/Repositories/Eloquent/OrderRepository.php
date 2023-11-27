<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Core\Repositories\EloquentRepository;
use Fintech\Transaction\Interfaces\OrderRepository as InterfacesOrderRepository;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
        if (isset($filters['search']) && ! empty($filters['search'])) {
            if (is_numeric($filters['search'])) {
                $query->where($this->model->getKeyName(), 'like', "%{$filters['search']}%");
            } else {
                //$query->where('name', 'like', "%{$filters['search']}%");
                $query->orWhere($modelTable.'.order_data', 'like', "%{$filters['search']}%");
            }
        }

        if (isset($filters['user_id_not_in']) && $filters['user_id_not_in']) {
            $query->whereNotIn($modelTable.'.user_id', $filters['user_id_not_in']);
        }

        if (isset($filters['order_id']) && $filters['order_id']) {
            $query->where($modelTable.'.id', '=', $filters['order_id']);
        }

        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where($modelTable.'.user_id', '=', $filters['user_id']);
        }

        if (isset($filters['$filters']) && $filters['sender_receiver_id']) {
            $query->where($modelTable.'.sender_receiver_id', '=', $filters['sender_receiver_id']);
        }
        if (isset($filters['user_id_sender_receiver_id']) && $filters['user_id_sender_receiver_id']) {
            $query->where(function ($query) use ($filters, $modelTable) {
                $query->where($modelTable.'.user_id', $filters['user_id_sender_receiver_id']);
                $query->orWhere($modelTable.'.sender_receiver_id', $filters['user_id_sender_receiver_id']);
            });
            /*$query->where($modelTable.'.user_id', '=', $filters['user_id_sender_receiver_id']);
            $query->orWhere($modelTable.'.sender_receiver_id', '=', $filters['user_id_sender_receiver_id']);*/
        }

        if (isset($filters['service_id']) && $filters['service_id']) {
            $query->where($modelTable.'.service_id', '=', $filters['service_id']);
        }

        if (isset($filters['service_id_in']) && $filters['service_id_in']) {
            $query->whereIn($modelTable.'.service_id', $filters['service_id_in']);
        }

        if (isset($filters['transaction_form_id']) && $filters['transaction_form_id']) {
            $query->where($modelTable.'.transaction_form_id', '=', $filters['transaction_form_id']);
        }

        if (isset($filters['transaction_form_id_in']) && $filters['transaction_form_id_in']) {
            $query->whereIn($modelTable.'.transaction_form_id', $filters['transaction_form_id_in']);
        }

        if (isset($filters['transaction_form_id_not_in']) && $filters['transaction_form_id_not_in']) {
            $query->whereNotIn($modelTable.'.transaction_form_id', $filters['transaction_form_id_not_in']);
        }

        if (isset($filters['transaction_form_id_not_equal']) && $filters['transaction_form_id_not_equal']) {
            $query->where($modelTable.'.transaction_form_id', '!=', $filters['transaction_form_id_not_equal']);
        }

        if (isset($filters['amount']) && $filters['amount']) {
            $query->where($modelTable.'.amount', '=', $filters['amount']);
        }

        if (isset($filters['currency']) && $filters['currency']) {
            $query->where($modelTable.'.currency', '=', $filters['currency']);
        }

        if (isset($filters['order_start_date_time']) && $filters['order_start_date_time'] != '0000-00-00' && $filters['order_start_date_time'] != '' &&
            isset($filters['order_end_date_time']) && $filters['order_end_date_time'] != '0000-00-00' && $filters['order_end_date_time'] != ''
        ) {
            $query->whereBetween($modelTable.'.order_date', array($filters['order_start_date_time'], $filters['order_end_date_time']));
        }

        if (isset($filters['order_start_date']) && $filters['order_start_date'] != '0000-00-00' && $filters['order_start_date'] != '' &&
            isset($filters['order_end_date']) && $filters['order_end_date'] != '0000-00-00' && $filters['order_end_date'] != ''
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'.ordered_at)'), array($filters['order_start_date'], $filters['order_end_date']));
        }

        if (isset($filters['order_start_date']) && $filters['order_start_date'] != '0000-00-00' && $filters['order_start_date'] != '' &&
            empty($filters['order_end_date'])
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'.ordered_at)'), array($filters['order_start_date'], $filters['order_start_date']));
        }

        if (isset($filters['order_end_date']) && $filters['order_end_date'] != '0000-00-00' && $filters['order_end_date'] != '' &&
            empty($filters['order_start_date'])
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'.ordered_at)'), array($filters['order_end_date'], $filters['order_end_date']));
        }

        if (isset($filters['order_date']) && $filters['order_date'] != '0000-00-00' && $filters['order_date'] != '') {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'.ordered_at)'), array($filters['order_date'], $filters['order_date']));
        }

        if (isset($filters['created_at_start_date_time']) && $filters['created_at_start_date_time'] != '0000-00-00'
            && $filters['created_at_start_date_time'] != '' &&
            isset($filters['created_at_end_date_time']) && $filters['created_at_end_date_time'] != '0000-00-00'
            && $filters['created_at_end_date_time'] != ''
        ) {
            $query->whereBetween($modelTable.'.created_at', array($filters['created_at_start_date_time'], $filters['created_at_end_date_time']));
        }

        if (isset($filters['created_at_start_date']) && $filters['created_at_start_date'] != '0000-00-00' && $filters['created_at_start_date'] != '' &&
            isset($filters['created_at_end_date']) && $filters['created_at_end_date'] != '0000-00-00' && $filters['created_at_end_date'] != ''
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'..created_at)'), array($filters['created_at_start_date'], $filters['created_at_end_date']));
        }

        if (isset($filters['created_at_start_date']) && $filters['created_at_start_date'] != '0000-00-00' && $filters['created_at_start_date'] != '' &&
            empty($filters['created_at_end_date'])
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'..created_at)'), array($filters['created_at_start_date'], $filters['created_at_start_date']));
        }

        if (isset($filters['created_at_end_date']) && $filters['created_at_end_date'] != '0000-00-00' && $filters['created_at_end_date'] != '' &&
            empty($filters['created_at_start_date'])
        ) {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'..created_at)'), array($filters['created_at_end_date'], $filters['created_at_end_date']));
        }

        if (isset($filters['created_at_date']) && $filters['created_at_date'] != '0000-00-00' && $filters['created_at_date'] != '') {
            $query->whereBetween(DB::raw('DATE('.$modelTable.'..created_at)'), array($filters['created_at_date'], $filters['created_at_date']));
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->whereIn($modelTable.'.status', $filters['status']);
        }

        if (isset($filters['status_not_equal']) && $filters['status_not_equal']) {
            $query->where($modelTable.'.status', '!=', $filters['status_not_equal']);
        }

        if (isset($filters['source_country_id']) && $filters['source_country_id']) {
            $query->where($modelTable.'.source_country_id', $filters['source_country_id']);
        }

        if (isset($filters['source_country_id_array']) && !empty($filters['source_country_id_array'])) {
            $query->whereIn($modelTable.'.source_country_id', $filters['source_country_id_array']);
        }

        if (isset($filters['destination_country_id']) && !empty($filters['destination_country_id'])) {
            $query->where($modelTable.'.destination_country_id', $filters['destination_country_id']);
        }

        if (isset($filters['account_number']) && is_bool($filters['account_number'])) {
            $query->where($modelTable.'.order_data->account_number', $filters['account_number']);
        }

        if (isset($filters['bank_id']) && is_bool($filters['bank_id'])) {
            $query->where($modelTable.'.order_data->bank_id', $filters['bank_id']);
        }

        if (isset($filters['bank_branch_id']) && is_bool($filters['bank_branch_id'])) {
            $query->where($modelTable.'.order_data->bank_branch_id', $filters['bank_branch_id']);
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
