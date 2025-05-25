<?php

namespace Fintech\Transaction\Http\Controllers;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Http\Requests\DropDownRequest;
use Fintech\Core\Http\Resources\DropDownCollection;
use Fintech\Transaction\Http\Requests\ImportPolicyRequest;
use Fintech\Transaction\Http\Requests\IndexPolicyRequest;
use Fintech\Transaction\Http\Requests\StorePolicyRequest;
use Fintech\Transaction\Http\Requests\UpdatePolicyRequest;
use Fintech\Transaction\Http\Resources\PolicyCollection;
use Fintech\Transaction\Http\Resources\PolicyResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class PolicyController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to Policy
 *
 * @lrd:end
 */
class PolicyController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *Policy* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexPolicyRequest $request): PolicyCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $policyPaginate = transaction()->policy()->list($inputs);

            return new PolicyCollection($policyPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *Policy* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StorePolicyRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $policy = transaction()->policy()->create($inputs);

            if (! $policy) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.policy_model'));
            }

            return response()->created([
                'message' => __('core::messages.resource.created', ['model' => 'Policy']),
                'id' => $policy->id,
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *Policy* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): PolicyResource|JsonResponse
    {
        try {

            $policy = transaction()->policy()->find($id);

            if (! $policy) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            return new PolicyResource($policy);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *Policy* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdatePolicyRequest $request, string|int $id): JsonResponse
    {
        try {

            $policy = transaction()->policy()->find($id);

            if (! $policy) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            $inputs = $request->validated();

            if (! transaction()->policy()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            return response()->updated(__('core::messages.resource.updated', ['model' => 'Policy']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *Policy* resource using id.
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

            $policy = transaction()->policy()->find($id);

            if (! $policy) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            if (! transaction()->policy()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            return response()->deleted(__('core::messages.resource.deleted', ['model' => 'Policy']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *Policy* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $policy = transaction()->policy()->find($id, true);

            if (! $policy) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            if (! transaction()->policy()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.transaction.policy_model'), $id);
            }

            return response()->restored(__('core::messages.resource.restored', ['model' => 'Policy']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Policy* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexPolicyRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $policyPaginate = transaction()->policy()->export($inputs);

            return response()->exported(__('core::messages.resource.exported', ['model' => 'Policy']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *Policy* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return PolicyCollection|JsonResponse
     */
    public function import(ImportPolicyRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $policyPaginate = transaction()->policy()->list($inputs);

            return new PolicyCollection($policyPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * Return a dropdown list of compliances
     */
    public function dropdown(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {

            $filters = $request->all();
            $filters['paginate'] = false;

            $attribute = $filters['attribute'] ?? 'id';
            $label = $filters['label'] ?? 'name';

            unset($filters['attribute'], $filters['label']);

            $entries = transaction()->policy()->list($filters)->map(function ($entry) use ($attribute, $label) {
                return [
                    'attribute' => $entry->{$attribute},
                    'label' => $entry->{$label},
                ];
            });

            return new DropDownCollection($entries);

        } catch (Exception $exception) {
            return response()->failed($exception);
        }
    }
}
