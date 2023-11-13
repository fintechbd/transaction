<?php

namespace Fintech\Transaction\Http\Resources;

use Fintech\Core\Supports\Constant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserAccountCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($user_account) {
            return [
                'id' => $user_account->getKey(),
                'user_id' => $user_account->user_id,
                'user_name' => $user_account->user->name,
                'name' => $user_account->name,
                'chart_class_data' => $user_account->chart_class_data,
                'links' => $user_account->links,
                'created_at' => $user_account->created_at,
                'updated_at' => $user_account->updated_at,
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
                'per_page' => Constant::PAGINATE_LENGTHS,
                'sort' => ['id', 'name', 'created_at', 'updated_at'],
            ],
            'query' => $request->all(),
        ];
    }
}
