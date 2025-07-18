<?php

namespace Fintech\Transaction\Http\Controllers;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Transaction\Http\Requests\ImportChartClassRequest;
use Fintech\Transaction\Http\Requests\IndexChartClassRequest;
use Fintech\Transaction\Http\Requests\StoreChartClassRequest;
use Fintech\Transaction\Http\Requests\UpdateChartClassRequest;
use Fintech\Transaction\Http\Resources\ChartClassCollection;
use Fintech\Transaction\Http\Resources\ChartClassResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ChartClassController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ChartClass
 *
 * @lrd:end
 */
class ChartClassController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ChartClass* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexChartClassRequest $request): ChartClassCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartClassPaginate = transaction()->chartClass()->list($inputs);

            return new ChartClassCollection($chartClassPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *ChartClass* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreChartClassRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartClass = transaction()->chartClass()->create($inputs);

            if (! $chartClass) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.chart_class_model'));
            }

            return response()->created([
                'message' => __('core::messages.resource.created', ['model' => 'Chart Class']),
                'id' => $chartClass->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *ChartClass* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ChartClassResource|JsonResponse
    {
        try {

            $chartClass = transaction()->chartClass()->find($id);

            if (! $chartClass) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            return new ChartClassResource($chartClass);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *ChartClass* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateChartClassRequest $request, string|int $id): JsonResponse
    {
        try {

            $chartClass = transaction()->chartClass()->find($id);

            if (! $chartClass) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            $inputs = $request->validated();

            if (! transaction()->chartClass()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            return response()->updated(__('core::messages.resource.updated', ['model' => 'Chart Class']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ChartClass* resource using id.
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

            $chartClass = transaction()->chartClass()->find($id);

            if (! $chartClass) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            if (! transaction()->chartClass()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            return response()->deleted(__('core::messages.resource.deleted', ['model' => 'Chart Class']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ChartClass* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $chartClass = transaction()->chartClass()->find($id, true);

            if (! $chartClass) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            if (! transaction()->chartClass()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.transaction.chart_class_model'), $id);
            }

            return response()->restored(__('core::messages.resource.restored', ['model' => 'Chart Class']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChartClass* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexChartClassRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartClassPaginate = transaction()->chartClass()->export($inputs);

            return response()->exported(__('core::messages.resource.exported', ['model' => 'Chart Class']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChartClass* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ChartClassCollection|JsonResponse
     */
    public function import(ImportChartClassRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartClassPaginate = transaction()->chartClass()->list($inputs);

            return new ChartClassCollection($chartClassPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
