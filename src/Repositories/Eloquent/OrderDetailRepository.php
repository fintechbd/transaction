<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Core\Repositories\EloquentRepository;
use Fintech\Transaction\Interfaces\OrderDetailRepository as InterfacesOrderDetailRepository;
use Fintech\Transaction\Models\OrderDetail;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class OrderDetailRepository
 */
class OrderDetailRepository extends EloquentRepository implements InterfacesOrderDetailRepository
{
    public function __construct()
    {
        parent::__construct(config('fintech.transaction.order_detail_model', OrderDetail::class));
    }

    /**
     * return a list or pagination of items from
     * filtered options
     *
     * @return Paginator|Collection|int|float|mixed
     */
    public function list(array $filters = []): mixed
    {
        $query = $this->model->newQuery();

        // Searching
        if (! empty($filters['search'])) {
            $query->where(function ($query) use ($filters) {
                $query->where($this->model->getKeyName(), 'like', "%{$filters['search']}%")
                    ->orWhere('order_detail_number', 'like', "%{$filters['search']}%")
                    ->orWhere('order_detail_response_id', 'like', "%{$filters['search']}%")
                    ->orWhere('order_detail_data', 'like', "%{$filters['search']}%");
            });
        }

        if (! empty($filters['id_not_in'])) {
            $query->whereNotIn($this->model->getKeyName(), (array) $filters['id_not_in']);
        }

        if (! empty($filters['id_in'])) {
            $query->whereIn($this->model->getKeyName(), (array) $filters['id_in']);
        }

        if (! empty($filters['order_id'])) {
            $query->where('order_id', '=', $filters['order_id']);
        }

        if (! empty($filters['source_country_id'])) {
            $query->where('source_country_id', '=', $filters['source_country_id']);
        }

        if (! empty($filters['destination_country_id'])) {
            $query->where('destination_country_id', '=', $filters['destination_country_id']);
        }

        if (! empty($filters['order_detail_parent_id'])) {
            $query->where('order_detail_parent_id', '=', $filters['order_detail_parent_id']);
        }

        if (! empty($filters['sender_receiver_id'])) {
            $query->where('sender_receiver_id', '=', $filters['sender_receiver_id']);
        }

        if (! empty($filters['user_id'])) {
            $query->where('user_id', '=', $filters['user_id']);
        }

        if (! empty($filters['service_id'])) {
            $query->where('service_id', '=', $filters['service_id']);
        }

        if (! empty($filters['transaction_form_id'])) {
            $query->where('transaction_form_id', '=', $filters['transaction_form_id']);
        }

        if (! empty($filters['order_detail_currency'])) {
            $query->where('order_detail_currency', '=', $filters['order_detail_currency']);
        }

        if (! empty($filters['converted_currency'])) {
            $query->where('converted_currency', '=', $filters['converted_currency']);
        }

        // Display Trashed
        if (isset($filters['trashed']) && $filters['trashed'] === true) {
            $query->onlyTrashed();
        }

        // Handle Sorting
        $query->orderBy($filters['sort'] ?? $this->model->getKeyName(), $filters['dir'] ?? 'asc');

        if (isset($filters['get_order_detail_amount_sum']) && $filters['get_order_detail_amount_sum'] === true) {
            return $query->sum('order_detail_amount');
        }

        if (isset($filters['get_converted_amount_sum']) && $filters['get_converted_amount_sum'] === true) {
            return $query->sum('converted_amount');
        }

        // Execute Output
        return $this->executeQuery($query, $filters);

    }
}
