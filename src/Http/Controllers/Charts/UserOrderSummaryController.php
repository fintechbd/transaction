<?php

namespace Fintech\Transaction\Http\Controllers\Charts;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Facades\Core;
use Fintech\Transaction\Facades\Transaction;
use Fintech\Transaction\Http\Requests\Charts\UserOrderSummaryRequest;
use Fintech\Transaction\Http\Resources\Charts\UserOrderSummaryCollection;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class UserOrderSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UserOrderSummaryRequest $request): UserOrderSummaryCollection
    {
        try {
            $orders = collect();
            $userId = $request->input('user_id', auth()->id());
            $input = $request->validated();

            $input['enabled'] = true;
            $input['paginate'] = false;
            $input['user_id'] = $userId;
            $input['role_id'] = $request->input('role_id', auth()->user()?->roles?->first()?->getKey() ?? config('fintech.auth.customer_roles', [7])[0]);

            if ($request->filled('reload') && $request->boolean('reload')) {
                $input['destination_country_id'] = $input['source_country_id'];
            }

            $serviceTypes = collect();

            if (Core::packageExists('Business', true)) {

                if ($request->filled('service_type_parent_slug')) {
                    $serviceType = Business::serviceType()->findWhere(['service_type_slug' => $input['service_type_parent_slug'], 'get' => ['service_types.id']]);
                    $input['service_type_parent_id'] = $serviceType->id;
                } elseif ($request->filled('service_type_parent_id')) {
                    $input['service_type_parent_id'] = $request->input('service_type_parent_id');
                } else {
                    $input['service_type_parent_id_is_null'] = true;
                }
                $orders = Transaction::order()->list([
                    'parent_id_is_null' => true,
                    'user_id' => $userId,
                    'sort' => 'currency',
                    'dir' => 'asc',
                    'paginate' => false,
                    'sum_amount_count_order_group_by_service_type' => true,
                ]);

                if ($orders->isNotEmpty()) {

                    $serviceTypeCacheData = Cache::remember('serviceTypeCacheData', DAY, function () {
                        $serviceTypes = [];
                        foreach (\Fintech\Business\Facades\Business::serviceType(['paginate' => false]) as $serviceType) {
                            $serviceTypes[$serviceType->getKey()] = $serviceType->service_type_parent_id;
                        }

                        return $serviceTypes;
                    });

                    $orders = $orders->map(function ($order) use (&$serviceTypeCacheData) {
                        $order->service_type_parents = array_filter(
                            $this->getServiceTypeParentList($order->service_type_id, $serviceTypeCacheData),
                            fn ($item) => $item != null);

                        return $order;
                    });

                    $serviceTypes = \Fintech\Business\Facades\Business::serviceType()
                        ->available($input)
                        ->each(function ($item) use ($orders) {
                            $groupedOrders = [];
                            foreach ($orders as $order) {
                                if (in_array($item->id, $order->service_type_parents)) {
                                    $groupedOrders[$order->currency] = $order;
                                }
                            }
                            $item->orders = $groupedOrders;

                            return $item;
                        });
                }
            }

            return new UserOrderSummaryCollection($serviceTypes);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }

    private function getServiceTypeParentList($id, &$serviceTypeCacheData = [])
    {
        if (isset($serviceTypeCacheData[$id])) {
            if ($serviceTypeCacheData[$id]) {
                return [$id, ...$this->getServiceTypeParentList($serviceTypeCacheData[$id], $serviceTypeCacheData)];
            }
        }

        return [$id, $serviceTypeCacheData[$id] ?? null];
    }
}
