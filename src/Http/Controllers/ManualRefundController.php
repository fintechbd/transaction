<?php

namespace Fintech\Transaction\Http\Controllers;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Transaction\Http\Requests\ImportManualRefundRequest;
use Fintech\Transaction\Http\Requests\IndexManualRefundRequest;
use Fintech\Transaction\Http\Requests\StoreManualRefundRequest;
use Fintech\Transaction\Http\Requests\UpdateManualRefundRequest;
use Fintech\Transaction\Http\Resources\ManualRefundCollection;
use Fintech\Transaction\Http\Resources\ManualRefundResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ManualRefundController
 *
 * @lrd:start
 * This class handle create, display, update, delete
 * operation related to ManualRefund
 *
 * @lrd:end
 */
class ManualRefundController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ManualRefund* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexManualRefundRequest $request): ManualRefundCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefundPaginate = transaction()->manualRefund()->list($inputs);

            return new ManualRefundCollection($manualRefundPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *ManualRefund* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreManualRefundRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefund = transaction()->manualRefund()->create($inputs);

            if (! $manualRefund) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.manual_refund_model'));
            }

            return response()->created([
                'message' => __('core::messages.resource.created', ['model' => 'Manual Refund']),
                'id' => $manualRefund->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *ManualRefund* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ManualRefundResource|JsonResponse
    {
        try {

            $manualRefund = transaction()->manualRefund()->find($id);

            if (! $manualRefund) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            return new ManualRefundResource($manualRefund);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *ManualRefund* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateManualRefundRequest $request, string|int $id): JsonResponse
    {
        try {

            $manualRefund = transaction()->manualRefund()->find($id);

            if (! $manualRefund) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            $inputs = $request->validated();

            if (! transaction()->manualRefund()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            return response()->updated(__('core::messages.resource.updated', ['model' => 'Manual Refund']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ManualRefund* resource using id.
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

            $manualRefund = transaction()->manualRefund()->find($id);

            if (! $manualRefund) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            if (! transaction()->manualRefund()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            return response()->deleted(__('core::messages.resource.deleted', ['model' => 'Manual Refund']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ManualRefund* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexManualRefundRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefundPaginate = transaction()->manualRefund()->export($inputs);

            return response()->exported(__('core::messages.resource.exported', ['model' => 'Manual Refund']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ManualRefund* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ManualRefundCollection|JsonResponse
     */
    public function import(ImportManualRefundRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefundPaginate = transaction()->manualRefund()->list($inputs);

            return new ManualRefundCollection($manualRefundPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
