<?php

namespace Fintech\Transaction\Http\Controllers;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Transaction\Http\Requests\ImportChartEntryRequest;
use Fintech\Transaction\Http\Requests\IndexChartEntryRequest;
use Fintech\Transaction\Http\Requests\StoreChartEntryRequest;
use Fintech\Transaction\Http\Requests\UpdateChartEntryRequest;
use Fintech\Transaction\Http\Resources\ChartEntryCollection;
use Fintech\Transaction\Http\Resources\ChartEntryResource;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class ChartEntryController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to ChartEntry
 *
 * @lrd:end
 */
class ChartEntryController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *ChartEntry* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexChartEntryRequest $request): ChartEntryCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartEntryPaginate = Transaction::chartEntry()->list($inputs);

            return new ChartEntryCollection($chartEntryPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *ChartEntry* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreChartEntryRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartEntry = Transaction::chartEntry()->create($inputs);

            if (! $chartEntry) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.chart_entry_model'));
            }

            return response()->created([
                'message' => __('restapi::messages.resource.created', ['model' => 'Chart Entry']),
                'id' => $chartEntry->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *ChartEntry* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): ChartEntryResource|JsonResponse
    {
        try {

            $chartEntry = Transaction::chartEntry()->find($id);

            if (! $chartEntry) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_entry_model'), $id);
            }

            return new ChartEntryResource($chartEntry);

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *ChartEntry* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateChartEntryRequest $request, string|int $id): JsonResponse
    {
        try {

            $chartEntry = Transaction::chartEntry()->find($id);

            if (! $chartEntry) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_entry_model'), $id);
            }

            $inputs = $request->validated();

            if (! Transaction::chartEntry()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.chart_entry_model'), $id);
            }

            return response()->updated(__('restapi::messages.resource.updated', ['model' => 'Chart Entry']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *ChartEntry* resource using id.
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

            $chartEntry = Transaction::chartEntry()->find($id);

            if (! $chartEntry) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_entry_model'), $id);
            }

            if (! Transaction::chartEntry()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.transaction.chart_entry_model'), $id);
            }

            return response()->deleted(__('restapi::messages.resource.deleted', ['model' => 'Chart Entry']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *ChartEntry* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $chartEntry = Transaction::chartEntry()->find($id, true);

            if (! $chartEntry) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.chart_entry_model'), $id);
            }

            if (! Transaction::chartEntry()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.transaction.chart_entry_model'), $id);
            }

            return response()->restored(__('restapi::messages.resource.restored', ['model' => 'Chart Entry']));

        } catch (ModelNotFoundException $exception) {

            return response()->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChartEntry* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexChartEntryRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartEntryPaginate = Transaction::chartEntry()->export($inputs);

            return response()->exported(__('restapi::messages.resource.exported', ['model' => 'Chart Entry']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *ChartEntry* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return ChartEntryCollection|JsonResponse
     */
    public function import(ImportChartEntryRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $chartEntryPaginate = Transaction::chartEntry()->list($inputs);

            return new ChartEntryCollection($chartEntryPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
