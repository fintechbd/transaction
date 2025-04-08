<?php

namespace Fintech\Transaction\Http\Resources\Charts;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserOrderSummaryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'service_type_logo_svg' => $item->getFirstMediaUrl('logo_svg') ?? null,
                'service_type_logo_png' => $item->getFirstMediaUrl('logo_png') ?? null,
                'service_type_name' => $item->service_type_name,
                'order_count' => $item->order_count ?? 0,
                'orders' => $item->orders ?? [],
            ];
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
                'filter' => []
            ],
            'query' => $request->all(),
        ];
    }
}
