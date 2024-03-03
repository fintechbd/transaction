<?php

namespace Fintech\Transaction\Http\Controllers;

use Exception;
use Fintech\Core\Exceptions\DeleteOperationException;
use Fintech\Core\Exceptions\RestoreOperationException;
use Fintech\Core\Exceptions\StoreOperationException;
use Fintech\Core\Exceptions\UpdateOperationException;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\Transaction\Facades\Transaction;
use Fintech\Transaction\Http\Requests\ImportRewardPointRequest;
use Fintech\Transaction\Http\Requests\IndexRewardPointRequest;
use Fintech\Transaction\Http\Requests\StoreRewardPointRequest;
use Fintech\Transaction\Http\Requests\UpdateRewardPointRequest;
use Fintech\Transaction\Http\Resources\RewardPointCollection;
use Fintech\Transaction\Http\Resources\RewardPointResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

/**
 * Class RewardPointController
 *
 * @lrd:start
 * This class handle create, display, update, delete & restore
 * operation related to RewardPoint
 *
 * @lrd:end
 */
class RewardPointController extends Controller
{
    use ApiResponseTrait;

    /**
     * @lrd:start
     * Return a listing of the *RewardPoint* resource as collection.
     *
     * *```paginate=false``` returns all resource as list not pagination*
     *
     * @lrd:end
     */
    public function index(IndexRewardPointRequest $request): RewardPointCollection|JsonResponse
    {
        try {
            $inputs = $request->validated();

            $rewardPointPaginate = Transaction::rewardPoint()->list($inputs);

            return new RewardPointCollection($rewardPointPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a new *RewardPoint* resource in storage.
     *
     * @lrd:end
     *
     * @throws StoreOperationException
     */
    public function store(StoreRewardPointRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $rewardPoint = Transaction::rewardPoint()->create($inputs);

            if (! $rewardPoint) {
                throw (new StoreOperationException)->setModel(config('fintech.transaction.reward_point_model'));
            }

            return $this->created([
                'message' => __('core::messages.resource.created', ['model' => 'Reward Point']),
                'id' => $rewardPoint->id,
            ]);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Return a specified *RewardPoint* resource found by id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     */
    public function show(string|int $id): RewardPointResource|JsonResponse
    {
        try {

            $rewardPoint = Transaction::rewardPoint()->find($id);

            if (! $rewardPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.reward_point_model'), $id);
            }

            return new RewardPointResource($rewardPoint);

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Update a specified *RewardPoint* resource using id.
     *
     * @lrd:end
     *
     * @throws ModelNotFoundException
     * @throws UpdateOperationException
     */
    public function update(UpdateRewardPointRequest $request, string|int $id): JsonResponse
    {
        try {

            $rewardPoint = Transaction::rewardPoint()->find($id);

            if (! $rewardPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.reward_point_model'), $id);
            }

            $inputs = $request->validated();

            if (! Transaction::rewardPoint()->update($id, $inputs)) {

                throw (new UpdateOperationException)->setModel(config('fintech.transaction.reward_point_model'), $id);
            }

            return $this->updated(__('core::messages.resource.updated', ['model' => 'Reward Point']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Soft delete a specified *RewardPoint* resource using id.
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

            $rewardPoint = Transaction::rewardPoint()->find($id);

            if (! $rewardPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.reward_point_model'), $id);
            }

            if (! Transaction::rewardPoint()->destroy($id)) {

                throw (new DeleteOperationException())->setModel(config('fintech.transaction.reward_point_model'), $id);
            }

            return $this->deleted(__('core::messages.resource.deleted', ['model' => 'Reward Point']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Restore the specified *RewardPoint* resource from trash.
     * ** ```Soft Delete``` needs to enabled to use this feature**
     *
     * @lrd:end
     *
     * @return JsonResponse
     */
    public function restore(string|int $id)
    {
        try {

            $rewardPoint = Transaction::rewardPoint()->find($id, true);

            if (! $rewardPoint) {
                throw (new ModelNotFoundException)->setModel(config('fintech.transaction.reward_point_model'), $id);
            }

            if (! Transaction::rewardPoint()->restore($id)) {

                throw (new RestoreOperationException())->setModel(config('fintech.transaction.reward_point_model'), $id);
            }

            return $this->restored(__('core::messages.resource.restored', ['model' => 'Reward Point']));

        } catch (ModelNotFoundException $exception) {

            return $this->notfound($exception->getMessage());

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *RewardPoint* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     */
    public function export(IndexRewardPointRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $rewardPointPaginate = Transaction::rewardPoint()->export($inputs);

            return $this->exported(__('core::messages.resource.exported', ['model' => 'Reward Point']));

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }

    /**
     * @lrd:start
     * Create a exportable list of the *RewardPoint* resource as document.
     * After export job is done system will fire  export completed event
     *
     * @lrd:end
     *
     * @return RewardPointCollection|JsonResponse
     */
    public function import(ImportRewardPointRequest $request): JsonResponse
    {
        try {
            $inputs = $request->validated();

            $rewardPointPaginate = Transaction::rewardPoint()->list($inputs);

            return new RewardPointCollection($rewardPointPaginate);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
