<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Core\Facades\Core;
use Fintech\Core\Repositories\EloquentRepository;
use Fintech\MetaData\Facades\MetaData;
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
        $query->leftJoin('transaction_forms', 'transaction_forms.id', '=', 'orders.transaction_form_id');
        $query->leftJoin('services', 'services.id', '=', 'orders.service_id');
        $query->leftJoin('service_types', 'service_types.id', '=', 'services.service_type_id');

        //Searching
        if (! empty($filters['search'])) {
            if (is_numeric($filters['search'])) {
                $query->where(function ($query) use ($filters) {
                    $query->where('orders.'.$this->model->getKeyName(), 'like', "%{$filters['search']}%");
                    $query->orWhere('orders.amount', 'like', "%{$filters['search']}%");
                    $query->orWhere('orders.converted_amount', 'like', "%{$filters['search']}%");
                });
            } else {
                $query->where(function ($query) use ($filters) {
                    $query->where('orders.order_data', 'like', "%{$filters['search']}%");
                    $query->orWhere('orders.currency', 'like', "%{$filters['search']}%");
                    $query->orWhere('orders.converted_currency', 'like', "%{$filters['search']}%");
                    $query->orWhere('orders.order_number', 'like', "%{$filters['search']}%");
                    $query->orWhere('orders.status', 'like', "%{$filters['search']}%");
                    $query->orWhere('services.service_name', 'like', "%{$filters['search']}%");
                });
            }
        }

        if (isset($filters['user_id_not_in']) && $filters['user_id_not_in']) {
            $query->whereNotIn('orders.user_id', $filters['user_id_not_in']);
        }

        if (isset($filters['order_id']) && $filters['order_id']) {
            $query->where('orders.id', '=', $filters['order_id']);
        }

        if (! empty($filters['id_not_in'])) {
            $query->whereNotIn($this->model->getTable().'.'.$this->model->getKeyName(), (array) $filters['id_not_in']);
        }

        if (! empty($filters['id_in'])) {
            $query->whereIn($this->model->getTable().'.'.$this->model->getKeyName(), (array) $filters['id_in']);
        }

        if (isset($filters['parent_id']) && $filters['parent_id']) {
            $query->where('orders.parent_id', '=', $filters['parent_id']);
        }

        if (isset($filters['user_id']) && $filters['user_id']) {
            $query->where('orders.user_id', '=', $filters['user_id']);
        }

        if (isset($filters['sender_receiver_id']) && $filters['sender_receiver_id']) {
            $query->where('orders.sender_receiver_id', '=', $filters['sender_receiver_id']);
        }

        if (isset($filters['user_id_sender_receiver_id']) && $filters['user_id_sender_receiver_id']) {
            $query->where(function ($query) use ($filters) {
                $query->where('orders.user_id', $filters['user_id_sender_receiver_id']);
                $query->orWhere('orders.sender_receiver_id', $filters['user_id_sender_receiver_id']);
            });
            /*$query->where($modelTable.'.user_id', '=', $filters['user_id_sender_receiver_id']);
            $query->orWhere($modelTable.'.sender_receiver_id', '=', $filters['user_id_sender_receiver_id']);*/
        }

        if (isset($filters['service_type_slug']) && $filters['service_type_slug']) {
            $query->where('service_types.service_type_slug', '=', $filters['service_type_slug']);
        }

        if (isset($filters['service_slug']) && $filters['service_slug']) {
            $query->where('services.service_slug', '=', $filters['service_slug']);
        }

        if (isset($filters['service_id']) && $filters['service_id']) {
            $query->where('orders.service_id', '=', $filters['service_id']);
        }

        if (! empty($filters['transaction_id'])) {
            $query->where('orders.order_number', '=', $filters['transaction_id'])
                ->orWhere('orders.order_data->purchase_number', '=', $filters['transaction_id'])
                ->orWhere('orders.order_data->accepted_number', '=', $filters['transaction_id'])
                ->orWhere('orders.order_data->rejected_number', '=', $filters['transaction_id']);
        }

        if (isset($filters['purchase_number']) && $filters['purchase_number']) {
            $query->where('orders.order_data->purchase_number', '=', $filters['purchase_number']);
        }

        if (isset($filters['service_id_in']) && $filters['service_id_in']) {
            $query->whereIn('orders.service_id', $filters['service_id_in']);
        }

        if (isset($filters['transaction_form_code']) && $filters['transaction_form_code']) {
            $query->where('transaction_forms.code', '=', $filters['transaction_form_code']);
        }

        if (isset($filters['transaction_form_id']) && $filters['transaction_form_id']) {
            $query->where('orders.transaction_form_id', '=', $filters['transaction_form_id']);
        }

        if (isset($filters['transaction_form_id_in']) && $filters['transaction_form_id_in']) {
            $query->whereIn('orders.transaction_form_id', $filters['transaction_form_id_in']);
        }

        if (isset($filters['transaction_form_id_not_in']) && $filters['transaction_form_id_not_in']) {
            $query->whereNotIn('orders.transaction_form_id', $filters['transaction_form_id_not_in']);
        }

        if (isset($filters['transaction_form_id_not_equal']) && $filters['transaction_form_id_not_equal']) {
            $query->where('orders.transaction_form_id', '!=', $filters['transaction_form_id_not_equal']);
        }

        if (isset($filters['amount']) && $filters['amount']) {
            $query->where('orders.amount', '=', $filters['amount']);
        }

        if (isset($filters['currency']) && $filters['currency']) {
            $query->where('orders.currency', '=', $filters['currency']);
        }

        if (isset($filters['converted_currency_not_in']) && $filters['converted_currency_not_in']) {
            $query->whereNotIn('orders.converted_currency', $filters['converted_currency_not_in']);
        }

        if (isset($filters['order_start_date_time']) && $filters['order_start_date_time'] != '0000-00-00' && $filters['order_start_date_time'] != '' &&
            isset($filters['order_end_date_time']) && $filters['order_end_date_time'] != '0000-00-00' && $filters['order_end_date_time'] != ''
        ) {
            $query->whereBetween('orders.ordered_at', [$filters['order_start_date_time'], $filters['order_end_date_time']]);
        }

        if (isset($filters['order_start_date']) && $filters['order_start_date'] != '0000-00-00' && $filters['order_start_date'] != '' &&
            isset($filters['order_end_date']) && $filters['order_end_date'] != '0000-00-00' && $filters['order_end_date'] != ''
        ) {
            $query->whereBetween(DB::raw('DATE('.'orders.ordered_at)'), [$filters['order_start_date'], $filters['order_end_date']]);
        }

        if (isset($filters['order_start_date']) && $filters['order_start_date'] != '0000-00-00' && $filters['order_start_date'] != '' &&
            empty($filters['order_end_date'])
        ) {
            $query->whereBetween(DB::raw('DATE('.'orders.ordered_at)'), [$filters['order_start_date'], $filters['order_start_date']]);
        }

        if (isset($filters['order_end_date']) && $filters['order_end_date'] != '0000-00-00' && $filters['order_end_date'] != '' &&
            empty($filters['order_start_date'])
        ) {
            $query->whereBetween(DB::raw('DATE('.'orders.ordered_at)'), [$filters['order_end_date'], $filters['order_end_date']]);
        }

        if (isset($filters['order_date']) && $filters['order_date'] != '0000-00-00' && $filters['order_date'] != '') {
            $query->whereBetween(DB::raw('DATE('.'orders.ordered_at)'), [$filters['order_date'], $filters['order_date']]);
        }

        if (isset($filters['created_at_start_date_time']) && $filters['created_at_start_date_time'] != '0000-00-00'
            && $filters['created_at_start_date_time'] != '' &&
            isset($filters['created_at_end_date_time']) && $filters['created_at_end_date_time'] != '0000-00-00'
            && $filters['created_at_end_date_time'] != ''
        ) {
            $query->whereBetween('orders.created_at', [$filters['created_at_start_date_time'], $filters['created_at_end_date_time']]);
        }

        if (isset($filters['created_at_start_date']) && $filters['created_at_start_date'] != '0000-00-00' && $filters['created_at_start_date'] != '' &&
            isset($filters['created_at_end_date']) && $filters['created_at_end_date'] != '0000-00-00' && $filters['created_at_end_date'] != ''
        ) {
            $query->whereBetween(DB::raw('DATE('.'orders.created_at)'), [$filters['created_at_start_date'], $filters['created_at_end_date']]);
        }

        //@TODO using between for better result
        if ((isset($filters['created_at_start_date']) && $filters['created_at_start_date'] != '0000-00-00' && $filters['created_at_start_date'] != '')
            && empty($filters['created_at_end_date'])) {
            $query->whereBetween(DB::raw('DATE('.'orders.created_at)'), [$filters['created_at_start_date'], now()->format('Y-m-d')]);
        }

        if (isset($filters['created_at_end_date']) && $filters['created_at_end_date'] != '0000-00-00' && $filters['created_at_end_date'] != ''
            && empty($filters['created_at_start_date'])
        ) {
            $query->where(DB::raw('DATE('.'orders.created_at)'), '<=', $filters['created_at_end_date']);
        }

        if (isset($filters['created_at_date']) && $filters['created_at_date'] != '0000-00-00' && $filters['created_at_date'] != '') {
            $query->where(DB::raw('DATE('.'orders.created_at)'), '=', $filters['created_at_date']);
        }

        if (isset($filters['status']) && $filters['status']) {
            $query->whereIn('orders.status', (array) $filters['status']);
        }

        if (isset($filters['status_not_equal']) && $filters['status_not_equal']) {
            $query->where('orders.status', '!=', $filters['status_not_equal']);
        }

        if (isset($filters['source_country_id']) && $filters['source_country_id']) {
            $query->where('orders.source_country_id', $filters['source_country_id']);
        }

        if (! empty($filters['source_country_id_array'])) {
            $query->whereIn('orders.source_country_id', $filters['source_country_id_array']);
        }

        if (! empty($filters['destination_country_id'])) {
            $query->where('orders.destination_country_id', $filters['destination_country_id']);
        }

        if (! empty($filters['creator_id'])) {
            $query->where(function ($query) use ($filters) {
                return $query->where('orders.creator_id', $filters['creator_id'])
                    ->orWhere('orders.user_id', $filters['creator_id']);
            });
        }

        if (! empty($filters['destination_country_id_array'])) {
            $query->where('orders.destination_country_id', $filters['destination_country_id_array']);
        }

        if (isset($filters['account_number']) && is_bool($filters['account_number'])) {
            $query->where('orders.order_data->account_number', $filters['account_number']);
        }

        if (isset($filters['bank_id']) && is_bool($filters['bank_id'])) {
            $query->where('orders.order_data->bank_id', $filters['bank_id']);
        }

        if (isset($filters['bank_branch_id']) && is_bool($filters['bank_branch_id'])) {
            $query->where('orders.order_data->bank_branch_id', $filters['bank_branch_id']);
        }

        if (isset($filters['order_number'])) {
            $query->where('orders.order_number', '=', $filters['order_number']);
        }

        //Display Trashed
        if (isset($filters['trashed']) && $filters['trashed'] === true) {
            $query->onlyTrashed();
        }

        //Handle Sorting
        $query->orderBy($filters['sort'] ?? 'orders.'.$this->model->getKeyName(), $filters['dir'] ?? 'asc');

        if (isset($filters['sum_converted_amount']) && $filters['sum_converted_amount'] === true) {
            $query->selectRaw('SUM(`orders`.`converted_amount`) as `total`, `orders`.`converted_currency` as `currency`')
                ->groupBy('orders.converted_currency');
        } elseif (isset($filters['sum_amount']) && $filters['sum_amount'] === true) {
            $query->selectRaw('SUM(`orders`.`amount`) as `total`, `orders`.`currency` as `currency`')
                ->groupBy('orders.currency');
        } else {
            $query->select('orders.*', DB::raw('transaction_forms.name AS transaction_form_name'));
        }

        //Execute Output
        return $this->executeQuery($query, $filters);

    }

    public function create(array $attributes = []): mixed
    {
        if (Core::packageExists('MetaData')) {
            if (! empty($attributes['order_data']['fund_source'])) {
                $attributes['order_data']['fund_source_name'] = MetaData::fundSource()
                    ->find($attributes['order_data']['fund_source'])->name ?? null;
            }

            if (! empty($attributes['order_data']['remittance_purpose'])) {
                $attributes['order_data']['remittance_purpose_name'] = MetaData::remittancePurpose()
                    ->find($attributes['order_data']['remittance_purpose'])->name ?? null;
            }
        }

        return parent::create($attributes);
    }
}
