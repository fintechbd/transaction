<?php

namespace Fintech\Transaction\Http\Controllers\Charts;

use Exception;
use Fintech\Core\Traits\ApiResponseTrait;
use Fintech\Transaction\Facades\Transaction;
use Fintech\Transaction\Http\Requests\Charts\UserAccountUsageRequest;
use Fintech\Transaction\Http\Resources\Charts\UserAccountUsageResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class UserAccountUsagePieChartController extends Controller
{
    use ApiResponseTrait;

    /**
     *
     * @param UserAccountUsageRequest $request
     * @return UserAccountUsageResource|JsonResponse
     */
    public function __invoke(UserAccountUsageRequest $request): UserAccountUsageResource|JsonResponse
    {
        try {
            $inputs = $request->validated();
            $inputs['paginate'] = false;

            $ordersSum = Transaction::order()->list($inputs);

            return new UserAccountUsageResource($data);

        } catch (Exception $exception) {

            return $this->failed($exception->getMessage());
        }
    }
}
