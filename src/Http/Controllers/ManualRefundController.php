<?php

namespace Fintech\Transaction\Http\Controllers;
use Exception;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\Transaction\Facades\Transaction;
use Fintech\Transaction\Http\Resources\ManualRefundResource;
use Fintech\Transaction\Http\Resources\ManualRefundCollection;
use Fintech\Transaction\Http\Requests\ImportManualRefundRequest;
use Fintech\Transaction\Http\Requests\StoreManualRefundRequest;
use Fintech\Transaction\Http\Requests\UpdateManualRefundRequest;
use Fintech\Transaction\Http\Requests\IndexManualRefundRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ManualRefundController
 * @package Fintech\Transaction\Http\Controllers
 *
 * @lrd:start
 * This class handle create, display, update, delete
 * operation related to ManualRefund
 * @lrd:end
 *
 */

class ManualRefundController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *ManualRefund* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexManualRefundRequest $request
     * @return ManualRefundCollection|JsonResponse
     */
    public function index(IndexManualRefundRequest $request): ManualRefundCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefundPaginate = Transaction::manualRefund()->list($inputs);

            return new ManualRefundCollection($manualRefundPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *ManualRefund* resource in storage.
     * @lrd:end
     *
     * @param StoreManualRefundRequest $request
     * @return JsonResponse
     * @throws StoreOperationException
     */
    public function store(StoreManualRefundRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefund = Transaction::manualRefund()->create($inputs);

            if (!$manualRefund) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.manual_refund_model'));
            }

            return $this->created([
                'message' => __('core::messages.resource.created', ['model' => 'Manual Refund']),
                'id' => $manualRefund->id
             ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *ManualRefund* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return ManualRefundResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ManualRefundResource|JsonResponse
    {
        try {

            $manualRefund = Transaction::manualRefund()->find($id);

            if (!$manualRefund) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            return new ManualRefundResource($manualRefund);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *ManualRefund* resource using id.
     * @lrd:end
     *
     * @param UpdateManualRefundRequest $request
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateManualRefundRequest $request, string|int $id): JsonResponse
    {
        try {

            $manualRefund = Transaction::manualRefund()->find($id);

            if (!$manualRefund) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            $inputs = $request->validated();

            if (!Transaction::manualRefund()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            return $this->updated(__('core::messages.resource.updated', ['model' => 'Manual Refund']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ManualRefund* resource using id.
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws DeleteOperationException
     */
    public function destroy(string|int $id)
    {
        try {

            $manualRefund = Transaction::manualRefund()->find($id);

            if (!$manualRefund) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            if (!Transaction::manualRefund()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.transaction.manual_refund_model'), $id);
            }

            return $this->deleted(__('core::messages.resource.deleted', ['model' => 'Manual Refund']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ManualRefund* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param IndexManualRefundRequest $request
     * @return JsonResponse
     */
    public function export(IndexManualRefundRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefundPaginate = Transaction::manualRefund()->export($inputs);

            return $this->exported(__('core::messages.resource.exported', ['model' => 'Manual Refund']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ManualRefund* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param ImportManualRefundRequest $request
     * @return ManualRefundCollection|JsonResponse
     */
    public function import(ImportManualRefundRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $manualRefundPaginate = Transaction::manualRefund()->list($inputs);

            return new ManualRefundCollection($manualRefundPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
