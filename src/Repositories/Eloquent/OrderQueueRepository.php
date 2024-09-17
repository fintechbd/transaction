<?php

namespace Fintech\Transaction\Repositories\Eloquent;

use Fintech\Core\Repositories\EloquentRepository;
use Fintech\Transaction\Interfaces\OrderQueueRepository as InterfacesOrderQueueRepository;
use Fintech\Transaction\Models\OrderQueue;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class OrderQueueRepository
 */
class OrderQueueRepository extends EloquentRepository implements InterfacesOrderQueueRepository
{
    public function __construct()
    {
        parent::__construct(config('fintech.transaction.order_queue_model', OrderQueue::class));
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

        //Searching
        if (!empty($filters['search'])) {
            if (is_numeric($filters['search'])) {
                $query->where($this->model->getKeyName(), 'like', "%{$filters['search']}%");
            } else {
                $query->where('name', 'like', "%{$filters['search']}%");
                $query->orWhere('order_queue_data', 'like', "%{$filters['search']}%");
            }
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

    /**
     * @param string|int $sender_user_id
     * @return bool|string
     */
    public function addToQueueSenderWise(string|int $sender_user_id): bool|string
    {
        if(DB::statement("INSERT INTO order_queues(user_id) SELECT ? FROM DUAL WHERE NOT EXISTS (SELECT * FROM `order_queues` WHERE `user_id`=? LIMIT 1)", [$sender_user_id, $sender_user_id])) {
            return DB::getPdo()->lastInsertId();
        }

        return 0;
    }

    /**
     * @param string|int $sender_user_id
     * @return array
     */
    public function removeFromQueueSenderWise(string|int $sender_user_id): array
    {
        return DB::select("DELETE FROM `order_queues` where `user_id` ={$sender_user_id}");
    }

    /**
     * @param string|int $order_id
     * @return bool|string
     */
    public function addToQueueOrderWise(string|int $order_id): bool|string
    {
        DB::select("INSERT INTO order_queues(order_id) SELECT \"{$order_id}\" FROM DUAL WHERE NOT EXISTS (SELECT * FROM `order_queues` WHERE `order_id`={$order_id} LIMIT 1)");

        return DB::getPdo()->lastInsertId();
    }

    public function removeFromQueueOrderWise(string|int $order_id): array
    {
        return DB::select("DELETE FROM `order_queues` where `order_id`={$order_id}");
    }
}
