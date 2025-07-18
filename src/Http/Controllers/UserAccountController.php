<?php

namespace Fintech\Transaction\Http\Controllers;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Transaction\Http\Requests\ImportUserAccountRequest;
use Fintech\Transaction\Http\Requests\IndexUserAccountRequest;
use Fintech\Transaction\Http\Requests\StoreUserAccountRequest;
use Fintech\Transaction\Http\Requests\UpdateUserAccountRequest;
use Fintech\Transaction\Http\Resources\UserAccountCollection;
use Fintech\Transaction\Http\Resources\UserAccountResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class UserAccountController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to UserAccount
 *
 * @lrd:end
 */
class UserAccountController extends Controller
{
    /**
     * @lrd:start
     * Return a listing of the *UserAccount* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexUserAccountRequest $request): UserAccountCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $userAccountPaginate = transaction()->userAccount()->list($inputs);

            return new UserAccountCollection($userAccountPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a new *UserAccount* resource in storage.
     *
     * @lrd:end
     */
    public function store(StoreUserAccountRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $userAccount = transaction()->userAccount()->create($inputs);

            if (! $userAccount) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.user_account_model'));
            }

            return response()->created([
                'message' => __('core::messages.resource.created', ['model' => 'User Account']),
                'id' => $userAccount->getKey(),
            ]);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Return a specified *UserAccount* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): UserAccountResource|JsonResponse
    {
        try {

            $userAccount = transaction()->userAccount()->find($id);

            if (! $userAccount) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            return new UserAccountResource($userAccount);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *UserAccount* resource using id.
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

            $userAccount = transaction()->userAccount()->find($id);

            if (! $userAccount) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            if (! transaction()->userAccount()->destroy($id)) {

                throw (new DeleteOperationException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            return response()->deleted(__('core::messages.resource.deleted', ['model' => 'User Account']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Restore the specified *UserAccount* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $userAccount = transaction()->userAccount()->find($id, true);

            if (! $userAccount) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            if (! transaction()->userAccount()->restore($id)) {

                throw (new RestoreOperationException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            return response()->restored(__('core::messages.resource.restored', ['model' => 'User Account']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *UserAccount* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexUserAccountRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $userAccountPaginate = transaction()->userAccount()->export($inputs);

            return response()->exported(__('core::messages.resource.exported', ['model' => 'User Account']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *UserAccount* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return UserAccountCollection|JsonResponse
     */
    public function import(ImportUserAccountRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $userAccountPaginate = transaction()->userAccount()->list($inputs);

            return new UserAccountCollection($userAccountPaginate);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified user account active or inactive.
     *
     * @lrd:end
     *
     * N.B after toggle update actions follow
     *
     * @throws ModelNotFoundException
     *
     * @see \Fintech\MetaData\Observers\CountryObserver
     */
    public function toggle(string|int $id): JsonResponse
    {
        try {

            $userAccount = transaction()->userAccount()->find($id);

            if (! $userAccount) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            if (! transaction()->userAccount()->update($id, ['enabled' => ! $userAccount->enabled])) {
                throw (new UpdateOperationException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            return response()->updated(__('metadata::messages.user_account.status_changed', ['status' => ($userAccount->enabled) ? 'Inactive' : 'Active']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    /**
     * @lrd:start
     * Update a specified *UserAccount* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateUserAccountRequest $request, string|int $id): JsonResponse
    {
        try {

            $userAccount = transaction()->userAccount()->find($id);

            if (! $userAccount) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            $inputs = $request->validated();

            if (! transaction()->userAccount()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.user_account_model'), $id);
            }

            return response()->updated(__('core::messages.resource.updated', ['model' => 'User Account']));

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
