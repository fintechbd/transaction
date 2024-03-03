<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Core\Repositories\EloquentRepository;
use Fintech\Transaction\Interfaces\OrderRepository as InterfacesOrderRepository;
use Fintech\Transaction\Models\Order;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderRepository
 */
class OrderRepository extends EloquentRepository implements InterfacesOrderRepository
{
    public function __construct(?string $overwriteClass = null)
    {
        $className = ($overwriteClass != null)
            ? $overwriteClass
            : config('fintech.transaction.order_model', Order::class);

        parent::__construct($className);
    }

    /**
     * return a list or pagination of items from
     * filtered options
     *
     * @return Paginator|Collection
     *
     * @throws BindingResolutionException
     */
    public function list(array $filters = [])
    {
        $query = $this->model->newQuery();
        $modelTable = $this->model->getTable();

        $query->leftJoin(
            get_table('transaction.transaction_form'),
            get_table('transaction.transaction_form') . '.id', '=',
            $modelTable . '.transaction_form_id');
        $query->leftJoin(
            get_table('business.service'),
            get_table('business.service') . '.id', '=',
            $modelTable . '.service_id');
        $query->leftJoin(
            get_table('business.service_type'),
            get_table('business.service_type') . '.id', '=',
            get_table('business.service') . '.service_type_id');

        //Searching
        if (!empty($filters['search'])) {
            if (is_numeric($filters['search'])) {
                $query->where($modelTable . '.' . $this->model->getKeyName(), 'like', "%{$filters['search']}%");
                $query->where($modelTable . '.amount', 'like', "%{$filters['search']}%");
                $query->where($modelTable . '.converted_amount', 'like', "%{$filters['search']}%");
            } else {
                //$query->where('name', 'like', "%{$filters['search']}%");
                $query->orWhere($modelTable . '.order_data', 'like', "%{$filters['search']}%")
                    ->orWhere($modelTable . '.currency', 'like', "%{$filters['search']}%")
                    ->orWhere($modelTable . '.converted_currency', 'like', "%{$filters['search']}%")
                    ->orWhere($modelTable . '.order_number', 'like', "%{$filters['search']}%")
                    ->orWhere($modelTable . '.status', 'like', "%{$filters['search']}%")
                    ->orWhere(get_table('business.service') . '.service_name', 'like', "%{$filters['search']}%");
            }
        }

        if (isset($filters['user_id_not_in']) && $filters['user_id_not_in']) {
            $query->whereNotIn($modelTable . '.user_id', $filters['user_id_not_in']);
        }

        if (isset($filters['order_id']) && $filters['order_id']) {
            $query->where($modelTable . '.id', '=', $filters['order_id']);
        }

        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where($modelTable . '.user_id', '=', $filters['user_id']);
        }

        if (isset($filters['sender_receiver_id']) && $filters['sender_receiver_id']) {
            $query->where($modelTable . '.sender_receiver_id', '=', $filters['sender_receiver_id']);
        }

        if (isset($filters['user_id_sender_receiver_id']) && $filters['user_id_sender_receiver_id']) {
            $query->where(function ($query) use ($filters, $modelTable) {
                $query->where($modelTable . '.user_id', $filters['user_id_sender_receiver_id']);
                $query->orWhere($modelTable . '.sender_receiver_id', $filters['user_id_sender_receiver_id']);
            });
            /*$query->where($modelTable.'.user_id', '=', $filters['user_id_sender_receiver_id']);
            $query->orWhere($modelTable.'.sender_receiver_id', '=', $filters['user_id_sender_receiver_id']);*/
        }

        if (isset($filters['service_type_slug']) && $filters['service_type_slug']) {
            $query->where(get_table('business.service_type') . '.service_type_slug', '=', $filters['service_type_slug']);
        }

        if (isset($filters['service_slug']) && $filters['service_slug']) {
            $query->where(get_table('business.service') . '.service_slug', '=', $filters['service_slug']);
        }

        if (isset($filters['service_id']) && $filters['service_id']) {
            $query->where($modelTable . '.service_id', '=', $filters['service_id']);
        }

        if (isset($filters['service_id_in']) && $filters['service_id_in']) {
            $query->whereIn($modelTable . '.service_id', $filters['service_id_in']);
        }

        if (isset($filters['transaction_form_code']) && $filters['transaction_form_code']) {
            $query->where(get_table('transaction.transaction_form') . '.code', '=', $filters['transaction_form_code']);
        }

        if (isset($filters['transaction_form_id']) && $filters['transaction_form_id']) {
            $query->where($modelTable . '.transaction_form_id', '=', $filters['transaction_form_id']);
        }

        if (isset($filters['transaction_form_id_in']) && $filters['transaction_form_id_in']) {
            $query->whereIn($modelTable . '.transaction_form_id', $filters['transaction_form_id_in']);
        }

        if (isset($filters['transaction_form_id_not_in']) && $filters['transaction_form_id_not_in']) {
            $query->whereNotIn($modelTable . '.transaction_form_id', $filters['transaction_form_id_not_in']);
        }

        if (isset($filters['transaction_form_id_not_equal']) && $filters['transaction_form_id_not_equal']) {
            $query->where($modelTable . '.transaction_form_id', '!=', $filters['transaction_form_id_not_equal']);
        }

        if (isset($filters['amount']) && $filters['amount']) {
            $query->where($modelTable . '.amount', '=', $filters['amount']);
        }

        if (isset($filters['currency']) && $filters['currency']) {
            $query->where($modelTable . '.currency', '=', $filters['currency']);
        }

        if (isset($filters['order_start_date_time']) && $filters['order_start_date_time'] != '0000-00-00' && $filters['order_start_date_time'] != '' &&
            isset($filters['order_end_date_time']) && $filters['order_end_date_time'] != '0000-00-00' && $filters['order_end_date_time'] != ''
        ) {
            $query->whereBetween($modelTable . '.order_date', [$filters['order_start_date_time'], $filters['order_end_date_time']]);
        }

        if (isset($filters['order_start_date']) && $filters['order_start_date'] != '0000-00-00' && $filters['order_start_date'] != '' &&
            isset($filters['order_end_date']) && $filters['order_end_date'] != '0000-00-00' && $filters['order_end_date'] != ''
        ) {
            $query->whereBetween(DB::raw('DATE(' . $modelTable . '.ordered_at)'), [$filters['order_start_date'], $filters['order_end_date']]);
        }

        if (isset($filters['order_start_date']) && $filters['order_start_date'] != '0000-00-00' && $filters['order_start_date'] != '' &&
            empty($filters['order_end_date'])
        ) {
            $query->whereBetween(DB::raw('DATE(' . $modelTable . '.ordered_at)'), [$filters['order_start_date'], $filters['order_start_date']]);
        }

        if (isset($filters['order_end_date']) && $filters['order_end_date'] != '0000-00-00' && $filters['order_end_date'] != '' &&
            empty($filters['order_start_date'])
        ) {
            $query->whereBetween(DB::raw('DATE(' . $modelTable . '.ordered_at)'), [$filters['order_end_date'], $filters['order_end_date']]);
        }

        if (isset($filters['order_date']) && $filters['order_date'] != '0000-00-00' && $filters['order_date'] != '') {
            $query->whereBetween(DB::raw('DATE(' . $modelTable . '.ordered_at)'), [$filters['order_date'], $filters['order_date']]);
        }

        if (isset($filters['created_at_start_date_time']) && $filters['created_at_start_date_time'] != '0000-00-00'
            && $filters['created_at_start_date_time'] != '' &&
            isset($filters['created_at_end_date_time']) && $filters['created_at_end_date_time'] != '0000-00-00'
            && $filters['created_at_end_date_time'] != ''
        ) {
            $query->whereBetween($modelTable . '.created_at', [$filters['created_at_start_date_time'], $filters['created_at_end_date_time']]);
        }

        if (isset($filters['created_at_start_date']) && $filters['created_at_start_date'] != '0000-00-00' && $filters['created_at_start_date'] != '' &&
            isset($filters['created_at_end_date']) && $filters['created_at_end_date'] != '0000-00-00' && $filters['created_at_end_date'] != ''
        ) {
            $query->whereBetween(DB::raw('DATE(' . $modelTable . '.created_at)'), [$filters['created_at_start_date'], $filters['created_at_end_date']]);
        }

        //@TODO using between for better result
        if ((isset($filters['created_at_start_date']) && $filters['created_at_start_date'] != '0000-00-00' && $filters['created_at_start_date'] != '')
            && empty($filters['created_at_end_date'])) {
            $query->whereBetween(DB::raw('DATE(' . $modelTable . '.created_at)'), [$filters['created_at_start_date'], now()->format('Y-m-d')]);
        }

        if (isset($filters['created_at_end_date']) && $filters['created_at_end_date'] != '0000-00-00' && $filters['created_at_end_date'] != ''
            && empty($filters['created_at_start_date'])
        ) {
            $query->where(DB::raw('DATE(' . $modelTable . '.created_at)'), '<=', $filters['created_at_end_date']);
        }

        if (isset($filters['created_at_date']) && $filters['created_at_date'] != '0000-00-00' && $filters['created_at_date'] != '') {
            $query->where(DB::raw('DATE(' . $modelTable . '.created_at)'), '=', $filters['created_at_date']);
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->whereIn($modelTable . '.status', $filters['status']);
        }

        if (isset($filters['status_not_equal']) && $filters['status_not_equal']) {
            $query->where($modelTable . '.status', '!=', $filters['status_not_equal']);
        }

        if (isset($filters['source_country_id']) && $filters['source_country_id']) {
            $query->where($modelTable . '.source_country_id', $filters['source_country_id']);
        }

        if (!empty($filters['source_country_id_array'])) {
            $query->whereIn($modelTable . '.source_country_id', $filters['source_country_id_array']);
        }

        if (!empty($filters['destination_country_id'])) {
            $query->where($modelTable . '.destination_country_id', $filters['destination_country_id']);
        }

        if (!empty($filters['destination_country_id_array'])) {
            $query->where($modelTable . '.destination_country_id', $filters['destination_country_id_array']);
        }

        if (isset($filters['account_number']) && is_bool($filters['account_number'])) {
            $query->where($modelTable . '.order_data->account_number', $filters['account_number']);
        }

        if (isset($filters['bank_id']) && is_bool($filters['bank_id'])) {
            $query->where($modelTable . '.order_data->bank_id', $filters['bank_id']);
        }

        if (isset($filters['bank_branch_id']) && is_bool($filters['bank_branch_id'])) {
            $query->where($modelTable . '.order_data->bank_branch_id', $filters['bank_branch_id']);
        }

        //Display Trashed
        if (isset($filters['trashed']) && $filters['trashed'] === true) {
            $query->onlyTrashed();
        }

        //Handle Sorting
        $query->orderBy($filters['sort'] ?? $this->model->getKeyName(), $filters['dir'] ?? 'asc');

        if (isset($filters['sum_converted_amount']) && $filters['sum_converted_amount'] === true) {
            $query->selectRaw("SUM(`{$modelTable}`.`converted_amount`) as `total`, `{$modelTable}`.`converted_currency` as `currency`")
                ->groupBy("{$modelTable}.converted_currency");
        } else {
            $query->select($modelTable . '.*', DB::raw(get_table('transaction.transaction_form') . '.name AS transaction_form_name'));
        }

        //Execute Output
        return $this->executeQuery($query, $filters);

    }
}
