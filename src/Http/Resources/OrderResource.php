<?php

namespace Fintech\Transaction\Http\Resources;

use Fintech\Core\Enums\RequestPlatform;
use Fintech\Core\Facades\Core;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->getKey(),
            'description' => $this->description ?? null,
            'source_country_id' => $this->source_country_id ?? null,
            'source_country_name' => null,
            'destination_country_id' => $this->destination_country_id ?? null,
            'destination_country_name' => null,
            'parent_id' => $this->parent_id ?? null,
            'sender_receiver_id' => $this->sender_receiver_id ?? null,
            'sender_receiver_name' => null,
            'user_id' => $this->user_id ?? null,
            'user_name' => null,
            'service_id' => $this->service_id ?? null,
            'service_name' => null,
            'service_type' => null,
            'transaction_form_id' => $this->transaction_form_id ?? null,
            'transaction_form_name' => $this->transaction_form_name ?? null,
            'ordered_at' => $this->ordered_at ?? null,
            'amount' => $this->amount ?? null,
            'amount_formatted' => (string) \currency($this->amount ?? null, $this->currency),
            'currency' => $this->currency ?? null,
            'converted_amount' => $this->converted_amount ?? null,
            'converted_amount_formatted' => (string) \currency($this->converted_amount ?? null, $this->converted_currency),
            'converted_currency' => $this->converted_currency ?? null,

            'charge_amount' => $this->order_data['service_stat_data']['charge_amount'] ?? null,
            'charge_amount_formatted' => (string) \currency($this->order_data['service_stat_data']['charge_amount'] ?? null, $this->currency ?? null),

            'discount_amount' => $this->order_data['service_stat_data']['discount_amount'] ?? null,
            'discount_amount_formatted' => (string) \currency($this->order_data['service_stat_data']['discount_amount'] ?? null, $this->currency ?? null),

            'commission_amount' => $this->order_data['service_stat_data']['commission_amount'] ?? null,
            'commission_amount_formatted' => (string) \currency($this->order_data['service_stat_data']['commission_amount'] ?? null, $this->currency ?? null),

            'cost_amount' => $this->order_data['service_stat_data']['cost_amount'] ?? null,
            'cost_amount_formatted' => (string) \currency($this->order_data['service_stat_data']['cost_amount'] ?? null, $this->currency ?? null),

            'interac_charge' => $this->order_data['service_stat_data']['interac_charge_amount'] ?? null,
            'interac_charge_formatted' => (string) \currency($this->order_data['service_stat_data']['interac_charge_amount'] ?? null, $this->currency ?? null),

            'total_amount' => $this->order_data['service_stat_data']['total_amount'] ?? null,
            'total_amount_formatted' => (string) \currency($this->order_data['service_stat_data']['total_amount'] ?? null, $this->currency ?? null),
            'order_number' => $this->order_number ?? null,
            'risk_profile' => $this->risk_profile ?? null,
            'notes' => $this->notes ?? null,
            'is_refunded' => $this->is_refunded ?? null,
            'order_data' => $this->order_data ?? null,
            'status' => $this->status ?? null,
            'request_platform' => RequestPlatform::tryFrom($this->order_data['request_from']),
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
        ];

        if (Core::packageExists('MetaData')) {
            $data['source_country_name'] = $this->sourceCountry?->name ?? null;
            $data['destination_country_name'] = $this->destinationCountry?->name ?? null;
        }

        if (Core::packageExists('Auth')) {
            $data['sender_receiver_name'] = $this->senderReceiver?->name ?? null;
            $data['user_name'] = $this->user?->name ?? null;
        }

        if (Core::packageExists('Business')) {
            $data['service_name'] = $this->service?->name ?? null;
            $data['service_type'] = $this->service->serviceType?->all_parent_list ?? null;
        }

        return $data;
    }
}
