<?php

namespace Fintech\Transaction\Http\Controllers;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Transaction\Http\Requests\ImportRedeemPointRequest;
use Fintech\Transaction\Http\Requests\IndexRedeemPointRequest;
use Fintech\Transaction\Http\Requests\StoreRedeemPointRequest;
use Fintech\Transaction\Http\Requests\UpdateRedeemPointRequest;
use Fintech\Transaction\Http\Resources\RedeemPointCollection;
use Fintech\Transaction\Http\Resources\RedeemPointResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class RedeemPointController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to RedeemPoint
 *
 * @lrd:end
 */
class RedeemPointController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *RedeemPoint* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexRedeemPointRequest $request): RedeemPointCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $redeemPointPaginate = transaction()->redeemPoint()->list($inputs);

            return new RedeemPointCollection($redeemPointPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *RedeemPoint* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreRedeemPointRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $redeemPoint = transaction()->redeemPoint()->create($inputs);

            if (! $redeemPoint) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.redeem_point_model'));
            }

            return response()->created([
                'message' => __('core::messages.resource.created', ['model' => 'Redeem Point']),
                'id' => $redeemPoint->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *RedeemPoint* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): RedeemPointResource|JsonResponse
    {
        try {

            $redeemPoint = transaction()->redeemPoint()->find($id);

            if (! $redeemPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            return new RedeemPointResource($redeemPoint);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *RedeemPoint* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateRedeemPointRequest $request, string|int $id): JsonResponse
    {
        try {

            $redeemPoint = transaction()->redeemPoint()->find($id);

            if (! $redeemPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            $inputs = $request->validated();

            if (! transaction()->redeemPoint()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            return response()->updated(__('core::messages.resource.updated', ['model' => 'Redeem Point']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *RedeemPoint* resource using id.
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

            $redeemPoint = transaction()->redeemPoint()->find($id);

            if (! $redeemPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            if (! transaction()->redeemPoint()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            return response()->deleted(__('core::messages.resource.deleted', ['model' => 'Redeem Point']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *RedeemPoint* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $redeemPoint = transaction()->redeemPoint()->find($id, true);

            if (! $redeemPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            if (! transaction()->redeemPoint()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.transaction.redeem_point_model'), $id);
            }

            return response()->restored(__('core::messages.resource.restored', ['model' => 'Redeem Point']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *RedeemPoint* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexRedeemPointRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $redeemPointPaginate = transaction()->redeemPoint()->export($inputs);

            return response()->exported(__('core::messages.resource.exported', ['model' => 'Redeem Point']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *RedeemPoint* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return RedeemPointCollection|JsonResponse
     */
    public function import(ImportRedeemPointRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $redeemPointPaginate = transaction()->redeemPoint()->list($inputs);

            return new RedeemPointCollection($redeemPointPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
