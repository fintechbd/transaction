<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Core\Repositories\EloquentRepository;
use Fintech\Transaction\Interfaces\OrderDetailRepository as InterfacesOrderDetailRepository;
use Fintech\Transaction\Models\OrderDetail;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Class OrderDetailRepository
 */
class OrderDetailRepository extends EloquentRepository implements InterfacesOrderDetailRepository
{
    public function __construct()
    {
        $model = app(config('fintech.transaction.order_detail_model', OrderDetail::class));

        if (! $model instanceof Model) {
            throw new InvalidArgumentException("Eloquent repository require model class to be `Illuminate\Database\Eloquent\Model` instance.");
        }

        $this->model = $model;
    }

    /**
     * return a list or pagination of items from
     * filtered options
     * @param array $filters
     * @return Builder[]|Paginator|Collection|int|float|mixed
     */
    public function list(array $filters = []): mixed
    {
        $query = $this->model->newQuery();

        //Searching
        if (! empty($filters['search'])) {
            if (is_numeric($filters['search'])) {
                $query->where($this->model->getKeyName(), 'like', "%{$filters['search']}%");
            } else {
                $query->where('order_detail_number', 'like', "%{$filters['search']}%");
                $query->orWhere('order_detail_response_id', 'like', "%{$filters['search']}%");
                $query->orWhere('order_detail_data', 'like', "%{$filters['search']}%");
            }
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

        //Display Trashed
        if (isset($filters['trashed']) && $filters['trashed'] === true) {
            $query->onlyTrashed();
        }

        //Handle Sorting
        $query->orderBy($filters['sort'] ?? $this->model->getKeyName(), $filters['dir'] ?? 'asc');

        if (isset($filters['get_order_detail_amount_sum']) && $filters['get_order_detail_amount_sum'] === true) {
            return $query->sum('converted_amount');
        }

        //Execute Output
        return $this->executeQuery($query, $filters);

    }
}
