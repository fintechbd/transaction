<?php

namespace Fintech\Transaction\Http\Controllers;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Transaction\Http\Requests\ImportOrderDetailRequest;
use Fintech\Transaction\Http\Requests\IndexOrderDetailRequest;
use Fintech\Transaction\Http\Requests\StoreOrderDetailRequest;
use Fintech\Transaction\Http\Requests\UpdateOrderDetailRequest;
use Fintech\Transaction\Http\Resources\OrderDetailCollection;
use Fintech\Transaction\Http\Resources\OrderDetailResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class OrderDetailController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to OrderDetail
 *
 * @lrd:end
 */
class OrderDetailController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *OrderDetail* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexOrderDetailRequest $request): OrderDetailCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderDetailPaginate = transaction()->orderDetail()->list($inputs);

            return new OrderDetailCollection($orderDetailPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *OrderDetail* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreOrderDetailRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderDetail = transaction()->orderDetail()->create($inputs);

            if (! $orderDetail) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.order_detail_model'));
            }

            return response()->created([
                'message' => __('core::messages.resource.created', ['model' => 'Order Detail']),
                'id' => $orderDetail->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *OrderDetail* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): OrderDetailResource|JsonResponse
    {
        try {

            $orderDetail = transaction()->orderDetail()->find($id);

            if (! $orderDetail) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            return new OrderDetailResource($orderDetail);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *OrderDetail* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateOrderDetailRequest $request, string|int $id): JsonResponse
    {
        try {

            $orderDetail = transaction()->orderDetail()->find($id);

            if (! $orderDetail) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            $inputs = $request->validated();

            if (!transaction()->orderDetail()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            return response()->updated(__('core::messages.resource.updated', ['model' => 'Order Detail']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *OrderDetail* resource using id.
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

            $orderDetail = transaction()->orderDetail()->find($id);

            if (! $orderDetail) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            if (!transaction()->orderDetail()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            return response()->deleted(__('core::messages.resource.deleted', ['model' => 'Order Detail']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *OrderDetail* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $orderDetail = transaction()->orderDetail()->find($id, true);

            if (! $orderDetail) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            if (!transaction()->orderDetail()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.transaction.order_detail_model'), $id);
            }

            return response()->restored(__('core::messages.resource.restored', ['model' => 'Order Detail']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *OrderDetail* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexOrderDetailRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderDetailPaginate = transaction()->orderDetail()->export($inputs);

            return response()->exported(__('core::messages.resource.exported', ['model' => 'Order Detail']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *OrderDetail* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return OrderDetailCollection|JsonResponse
     */
    public function import(ImportOrderDetailRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $orderDetailPaginate = transaction()->orderDetail()->list($inputs);

            return new OrderDetailCollection($orderDetailPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
