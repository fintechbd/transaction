<?php

namespace Fintech\Transaction\Http\Resources\Charts;

use Fintech\Core\Supports\Constant;
use Fintech\Core\Supports\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserOrderSummaryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $userAccounts = transaction()->userAccount([
            'user_id' => $request->input('user_id', auth()->id()),
            'paginate' => false,
            'get' => ['user_account_data'],
        ]);

        return $this->collection->map(function ($item) use ($userAccounts) {
            $data = [
                'service_type_logo_svg' => $item->logo_svg ?? null,
                'service_type_logo_png' => $item->logo_png ?? null,
                'service_type_name' => $item->service_type_name,
                'order_count' => 0,
                'orders' => [],
            ];

            foreach ($userAccounts as $userAccount) {
                $currency = Currency::config($userAccount->user_account_data['currency'] ?? config('fintech.core.default_currency_code', 'USD'));
                $data['orders'][$currency['code']] = [
                    'currency' => $currency['code'],
                    'currency_name' => $currency['name'],
                    'currency_symbol' => $currency['symbol'],
                    'total_order' => 0,
                    'total_amount' => 0,
                    'total_amount_formatted' => (string) \currency(0, $currency['code']),
                ];
            }

            foreach ($item->orders as $order) {
                $data['order_count'] += ($order->count_order ?? 0);
                $data['orders'][$order->currency]['total_order'] += ($order->count_order ?? 0);
                $data['orders'][$order->currency]['total_amount'] += ($order->sum_amount ?? 0);
                $data['orders'][$order->currency]['total_amount_formatted'] = (string) \currency($data['orders'][$order->currency]['total_amount'], $order->currency);
            }

            $data['orders'] = array_values($data['orders']);

            return $data;
        })->toArray();
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'options' => [
                'dir' => Constant::SORT_DIRECTIONS,
                'sort' => ['count', 'status'],
                'filter' => [],
            ],
            'query' => $request->all(),
        ];
    }
}
