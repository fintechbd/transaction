<?php

namespace Fintech\Transaction\Http\Controllers;
use Exception;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\Transaction\Facades\Transaction;
use Fintech\Transaction\Http\Resources\RedeemPointResource;
use Fintech\Transaction\Http\Resources\RedeemPointCollection;
use Fintech\Transaction\Http\Requests\ImportRedeemPointRequest;
use Fintech\Transaction\Http\Requests\StoreRedeemPointRequest;
use Fintech\Transaction\Http\Requests\UpdateRedeemPointRequest;
use Fintech\Transaction\Http\Requests\IndexRedeemPointRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class RedeemPointController
 * @package Fintech\Transaction\Http\Controllers
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to RedeemPoint
 * @lrd:end
 *
 */

class RedeemPointController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *RedeemPoint* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexRedeemPointRequest $request
     * @return RedeemPointCollection|JsonResponse
     */
    public function index(IndexRedeemPointRequest $request): RedeemPointCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $redeemPointPaginate = Transaction::redeemPoint()->list($inputs);

            return new RedeemPointCollection($redeemPointPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *RedeemPoint* resource in storage.
     * @lrd:end
     *
     * @param StoreRedeemPointRequest $request
     * @return JsonResponse
     * @throws StoreOperationException
     */
    public function store(StoreRedeemPointRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $redeemPoint = Transaction::redeemPoint()->create($inputs);

            if (!$redeemPoint) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.redeem_point_model'));
            }

            return $this->created([
                'message' => __('core::messages.resource.created', ['model' => 'Redeem Point']),
                'id' => $redeemPoint->id
             ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *RedeemPoint* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return RedeemPointResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): RedeemPointResource|JsonResponse
    {
        try {

            $redeemPoint = Transaction::redeemPoint()->find($id);

            if (!$redeemPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            return new RedeemPointResource($redeemPoint);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *RedeemPoint* resource using id.
     * @lrd:end
     *
     * @param UpdateRedeemPointRequest $request
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateRedeemPointRequest $request, string|int $id): JsonResponse
    {
        try {

            $redeemPoint = Transaction::redeemPoint()->find($id);

            if (!$redeemPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            $inputs = $request->validated();

            if (!Transaction::redeemPoint()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            return $this->updated(__('core::messages.resource.updated', ['model' => 'Redeem Point']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *RedeemPoint* resource using id.
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

            $redeemPoint = Transaction::redeemPoint()->find($id);

            if (!$redeemPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            if (!Transaction::redeemPoint()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            return $this->deleted(__('core::messages.resource.deleted', ['model' => 'Redeem Point']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *RedeemPoint* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $redeemPoint = Transaction::redeemPoint()->find($id, true);

            if (!$redeemPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            if (!Transaction::redeemPoint()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            return $this->restored(__('core::messages.resource.restored', ['model' => 'Redeem Point']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *RedeemPoint* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param IndexRedeemPointRequest $request
     * @return JsonResponse
     */
    public function export(IndexRedeemPointRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $redeemPointPaginate = Transaction::redeemPoint()->export($inputs);

            return $this->exported(__('core::messages.resource.exported', ['model' => 'Redeem Point']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *RedeemPoint* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param ImportRedeemPointRequest $request
     * @return RedeemPointCollection|JsonResponse
     */
    public function import(ImportRedeemPointRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $redeemPointPaginate = Transaction::redeemPoint()->list($inputs);

            return new RedeemPointCollection($redeemPointPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
