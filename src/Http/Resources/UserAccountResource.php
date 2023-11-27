<?php

namespace Fintech\Transaction\Http\Resources;

use Fintech\Core\Facades\Core;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->getKey(),
            'name' => $this->name,
            'account_no' => $this->account_no,
            'user_id' => $this->user_id,
            'user_name' => null,
            'country_id' => $this->country_id,
            'country_name' => null,
            'logo_svg' => null,
            'logo_png' => null,
            'user_account_data' => $this->user_account_data ?? [],
            'enabled' => $this->enabled,
            'links' => $this->links,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (Core::packageExists('Auth')) {
            $data['user_name'] = $this->user?->name ?? null;
        }
        if (Core::packageExists('MetaData')) {
            $data['country_name'] = $this->country?->name ?? null;
            $data['logo_svg'] = $user_account->country?->getFirstMediaUrl('logo_svg') ?? null;
            $data['logo_png'] = $user_account->country?->getFirstMediaUrl('logo_png') ?? null;
        }

        return $data;
    }
}
