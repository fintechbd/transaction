<?php

namespace Fintech\Transaction\Http\Controllers;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Transaction\Http\Requests\ImportChartTypeRequest;
use Fintech\Transaction\Http\Requests\IndexChartTypeRequest;
use Fintech\Transaction\Http\Requests\StoreChartTypeRequest;
use Fintech\Transaction\Http\Requests\UpdateChartTypeRequest;
use Fintech\Transaction\Http\Resources\ChartTypeCollection;
use Fintech\Transaction\Http\Resources\ChartTypeResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ChartTypeController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ChartType
 *
 * @lrd:end
 */
class ChartTypeController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ChartType* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexChartTypeRequest $request): ChartTypeCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartTypePaginate = transaction()->chartType()->list($inputs);

            return new ChartTypeCollection($chartTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *ChartType* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreChartTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartType = transaction()->chartType()->create($inputs);

            if (! $chartType) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.chart_type_model'));
            }

            return response()->created([
                'message' => __('core::messages.resource.created', ['model' => 'Chart Type']),
                'id' => $chartType->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *ChartType* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ChartTypeResource|JsonResponse
    {
        try {

            $chartType = transaction()->chartType()->find($id);

            if (! $chartType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            return new ChartTypeResource($chartType);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *ChartType* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateChartTypeRequest $request, string|int $id): JsonResponse
    {
        try {

            $chartType = transaction()->chartType()->find($id);

            if (! $chartType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            $inputs = $request->validated();

            if (!transaction()->chartType()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            return response()->updated(__('core::messages.resource.updated', ['model' => 'Chart Type']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ChartType* resource using id.
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

            $chartType = transaction()->chartType()->find($id);

            if (! $chartType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            if (!transaction()->chartType()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            return response()->deleted(__('core::messages.resource.deleted', ['model' => 'Chart Type']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ChartType* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $chartType = transaction()->chartType()->find($id, true);

            if (! $chartType) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            if (!transaction()->chartType()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.transaction.chart_type_model'), $id);
            }

            return response()->restored(__('core::messages.resource.restored', ['model' => 'Chart Type']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChartType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexChartTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartTypePaginate = transaction()->chartType()->export($inputs);

            return response()->exported(__('core::messages.resource.exported', ['model' => 'Chart Type']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChartType* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ChartTypeCollection|JsonResponse
     */
    public function import(ImportChartTypeRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartTypePaginate = transaction()->chartType()->list($inputs);

            return new ChartTypeCollection($chartTypePaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
