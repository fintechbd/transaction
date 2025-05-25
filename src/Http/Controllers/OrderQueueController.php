<?php

namespace Fintech\Transaction\Http\Controllers;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Transaction\Http\Requests\IndexOrderQueueRequest;
use Fintech\Transaction\Http\Resources\OrderQueueCollection;
use Fintech\Transaction\Http\Resources\OrderQueueResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class OrderQueueController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to OrderQueue
 *
 * @lrd:end
 */
class OrderQueueController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *OrderQueue* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexOrderQueueRequest $request): OrderQueueCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderQueuePaginate = transaction()->orderQueue()->list($inputs);

            return new OrderQueueCollection($orderQueuePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *OrderQueue* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): OrderQueueResource|JsonResponse
    {
        try {

            $orderQueue = transaction()->orderQueue()->find($id);

            if (! $orderQueue) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_queue_model'), $id);
            }

            return new OrderQueueResource($orderQueue);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    //    /**
    //     * @lrd:start
    //     * Update a specified *OrderQueue* resource using id.
    //     * @lrd:end
    //     *
    //     * @param UpdateOrderQueueRequest $request
    //     * @param string|int $id
    //     * @return JsonResponse
    //     * @throws ModelNotFoundException
    //     * @throws UpdateOperationException
    //     */
    //    public function update(UpdateOrderQueueRequest $request, string|int $id): JsonResponse
    //    {
    //        try {
    //
    //            $orderQueue =transaction()->orderQueue()->find($id);
    //
    //            if (!$orderQueue) {
    //                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_queue_model'), $id);
    //            }
    //
    //            $inputs = $request->validated();
    //
    //            if (!Transaction::orderQueue()->update($id, $inputs)) {
    //
    //                throw (new UpdateOperationException)->setModel(config('fintech.transaction.order_queue_model'), $id);
    //            }
    //
    //            return response()->updated(__('core::messages.resource.updated', ['model' => 'Order Queue']));
    //
    //        } catch (ModelNotFoundException $exception) {
    //
    //            return response()->notfound($exception);
    //
    //        } catch (Exception $exception) {
    //
    //            return response()->failed($exception);
    //        }
    //    }

    /**
     * @lrd:start
     * Soft delete a specified *OrderQueue* resource using id.
     *
     * @lrd:end
     *
     * @return JsonResponse
     *
     * @throws ModelNotFoundException
     * @throws DeleteOperationException
     */
    public function destroy(string|int $id)
    {
        try {

            $orderQueue = transaction()->orderQueue()->find($id);

            if (! $orderQueue) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_queue_model'), $id);
            }

            if (! transaction()->orderQueue()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.transaction.order_queue_model'), $id);
            }

            return response()->deleted(__('core::messages.resource.deleted', ['model' => 'Order Queue']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
