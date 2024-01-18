<?php

namespace Fintech\Transaction\Http\Resources\Charts;

use Fintech\Core\Supports\Currency;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserAccountUsageResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($entry) {
            return [
                'label' => $entry->currency,
                'data' => $entry->total,
                'color' => Currency::get($entry->currency)['color'] ?? '#000000',
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
                'type' => [
                    'receive' => 'Received',
                    'transfer' => 'Transferred',
                ],
                'duration' => [
                    '1' => 'Today',
                    '7' => 'Last 7 Days',
                    '15' => 'Last 15 Days',
                    '30' => 'Last 30 Days',
                ],
            ],
            'query' => $request->all(),
        ];
    }
}
