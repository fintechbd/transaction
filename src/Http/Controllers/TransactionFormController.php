<?php

namespace Fintech\Transaction\Http\Controllers;
use Exception;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\Transaction\Facades\Transaction;
use Fintech\Transaction\Http\Resources\TransactionFormResource;
use Fintech\Transaction\Http\Resources\TransactionFormCollection;
use Fintech\Transaction\Http\Requests\ImportTransactionFormRequest;
use Fintech\Transaction\Http\Requests\StoreTransactionFormRequest;
use Fintech\Transaction\Http\Requests\UpdateTransactionFormRequest;
use Fintech\Transaction\Http\Requests\IndexTransactionFormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class TransactionFormController
 * @package Fintech\Transaction\Http\Controllers
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to TransactionForm
 * @lrd:end
 *
 */

class TransactionFormController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *TransactionForm* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     * @lrd:end
     *
     * @param IndexTransactionFormRequest $request
     * @return TransactionFormCollection|JsonResponse
     */
    public function index(IndexTransactionFormRequest $request): TransactionFormCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $transactionFormPaginate = Transaction::transactionForm()->list($inputs);

            return new TransactionFormCollection($transactionFormPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *TransactionForm* resource in storage.
     * @lrd:end
     *
     * @param StoreTransactionFormRequest $request
     * @return JsonResponse
     * @throws StoreOperationException
     */
    public function store(StoreTransactionFormRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $transactionForm = Transaction::transactionForm()->create($inputs);

            if (!$transactionForm) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.transaction_form_model'));
            }

            return $this->created([
                'message' => __('core::messages.resource.created', ['model' => 'Transaction Form']),
                'id' => $transactionForm->id
             ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *TransactionForm* resource found by id.
     * @lrd:end
     *
     * @param string|int $id
     * @return TransactionFormResource|JsonResponse
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): TransactionFormResource|JsonResponse
    {
        try {

            $transactionForm = Transaction::transactionForm()->find($id);

            if (!$transactionForm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            return new TransactionFormResource($transactionForm);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *TransactionForm* resource using id.
     * @lrd:end
     *
     * @param UpdateTransactionFormRequest $request
     * @param string|int $id
     * @return JsonResponse
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateTransactionFormRequest $request, string|int $id): JsonResponse
    {
        try {

            $transactionForm = Transaction::transactionForm()->find($id);

            if (!$transactionForm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            $inputs = $request->validated();

            if (!Transaction::transactionForm()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            return $this->updated(__('core::messages.resource.updated', ['model' => 'Transaction Form']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *TransactionForm* resource using id.
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

            $transactionForm = Transaction::transactionForm()->find($id);

            if (!$transactionForm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            if (!Transaction::transactionForm()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            return $this->deleted(__('core::messages.resource.deleted', ['model' => 'Transaction Form']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *TransactionForm* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     * @lrd:end
     *
     * @param string|int $id
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $transactionForm = Transaction::transactionForm()->find($id, true);

            if (!$transactionForm) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            if (!Transaction::transactionForm()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.transaction.transaction_form_model'), $id);
            }

            return $this->restored(__('core::messages.resource.restored', ['model' => 'Transaction Form']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *TransactionForm* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param IndexTransactionFormRequest $request
     * @return JsonResponse
     */
    public function export(IndexTransactionFormRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $transactionFormPaginate = Transaction::transactionForm()->export($inputs);

            return $this->exported(__('core::messages.resource.exported', ['model' => 'Transaction Form']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *TransactionForm* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @param ImportTransactionFormRequest $request
     * @return TransactionFormCollection|JsonResponse
     */
    public function import(ImportTransactionFormRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $transactionFormPaginate = Transaction::transactionForm()->list($inputs);

            return new TransactionFormCollection($transactionFormPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
