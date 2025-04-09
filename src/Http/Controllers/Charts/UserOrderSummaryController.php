<?php

namespace Fintech\Transaction\Http\Controllers\Charts;

use Exception;
use Fintech\Business\Facades\Business;
use Fintech\Core\Facades\Core;
use Fintech\Transaction\Http\Requests\Charts\UserOrderSummaryRequest;
use Fintech\Transaction\Http\Resources\Charts\UserOrderSummaryCollection;
use Illuminate\Routing\Controller;

class UserOrderSummaryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UserOrderSummaryRequest $request): UserOrderSummaryCollection
    {
        try {
            $orders = collect();

            $input = $request->validated();

            $input['enabled'] = true;
            $input['paginate'] = false;
            $input['user_id'] = $request->input('user_id', auth()->id());
            $input['role_id'] = $request->input('role_id', auth()->user()?->roles?->first()?->getKey() ?? config('fintech.auth.customer_roles', [7])[0]);

            if ($request->filled('reload') && $request->boolean('reload')) {
                $input['destination_country_id'] = $input['source_country_id'];
            }

            if (Core::packageExists('Business', true)) {
                if ($request->filled('service_type_parent_slug')) {
                    $serviceType = Business::serviceType()->findWhere(['service_type_slug' => $input['service_type_parent_slug'], 'get' => ['service_types.id']]);
                    $input['service_type_parent_id'] = $serviceType->id;
                } elseif ($request->filled('service_type_parent_id')) {
                    $input['service_type_parent_id'] = $request->input('service_type_parent_id');
                } else {
                    $input['service_type_parent_id_is_null'] = true;
                }

                $orders = \Fintech\Business\Facades\Business::serviceType()
                    ->available($input)
                    ->map(function ($item) {
                        $item->order_count = mt_rand(10, 50);
                        $item->orders = [
                            ['currency' => 'CAD', 'total_order' => mt_rand(10, 30), 'total_amount' => 500, 'total_amount_formated' => (string) \currency(500, 'CAD')],
//                            ['currency' => 'AED', 'total_order' => mt_rand(10, 30), 'total_amount' => 1500, 'total_amount_formated' => (string) \currency(1500, 'AED')],
//                            ['currency' => 'BDT', 'total_order' => mt_rand(10, 30), 'total_amount' => 5000, 'total_amount_formated' => (string) \currency(5000, 'BDT')],
                        ];

                        return $item;
                    });

                //            $orderSummary = Transaction::order()->list([
                //                'parent_id_is_null' => true,
                //                'user_id' => $userId,
                //                'sort' => 'currency',
                //                'dir' => 'asc',
                //                'paginate' => false,
                //                'sum_amount_count_order_group_by_service_type' => true,
                //            ]);
            }

            return new UserOrderSummaryCollection($orders);

        } catch (Exception $exception) {

            return response()->failed($exception);
        }
    }
}
