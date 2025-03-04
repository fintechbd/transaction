<?php

namespace Fintech\Transaction\Http\Resources;

use Fintech\Core\Enums\RequestPlatform;
use Fintech\Core\Facades\Core;
use Fintech\Transaction\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

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
        /**
         * @var Order $this
         */
        $order_data = $this->order_data ?? [];

        Arr::forget($order_data, $this->hidden);

        $order_data['current_amount'] = $this->current_amount_formatted;
        $order_data['sending_amount'] = (string) \currency($order_data['sending_amount'] ?? null, $this->currency);
        $order_data['previous_amount'] = $this->previous_amount_formatted;

        return [
            'id' => $this->getKey(),
            'description' => $this->description ?? null,
            'source_country_name' => $this->sourceCountry?->name ?? null,
            'destination_country_name' => $this->destinationCountry?->name ?? null,
            'sender_receiver_name' => $this->senderReceiver?->name ?? null,
            'user_name' => $this->user?->name ?? null,
            'service_name' => $this->service?->service_name ?? null,
            'service_type' => $this->service->serviceType?->all_parent_list ?? null,
            'transaction_form_name' => $this->transaction_form_name ?? null,
            'ordered_at' => $this->ordered_at ?? null,
            'currency' => $this->currency ?? null,
            'amount' => (string) ($this->amount ?? null),
            'amount_formatted' => $this->amount_formatted,

            'converted_currency' => $this->converted_currency ?? null,
            'converted_amount' => (string) ($this->converted_amount ?? null),
            'converted_amount_formatted' => $this->converted_amount_formatted ?? null,

            'charge_amount' => $this->charge_amount ?? null,
            'charge_amount_formatted' => $this->charge_amount_formatted ?? null,

            'discount_amount' => $this->discount_amount ?? null,
            'discount_amount_formatted' => $this->discount_amount_formatted ?? null,

            'commission_amount' => $this->commission_amount ?? null,
            'commission_amount_formatted' => $this->commission_amount_formatted ?? null,

            'cost_amount' => $this->cost_amount ?? null,
            'cost_amount_formatted' => $this->cost_amount_formatted ?? null,

            'interac_charge' => $this->interac_charge_amount ?? null,
            'interac_charge_formatted' => $this->interac_charge_amount_formatted ?? null,

            'total_amount' => $this->total_amount ?? null,
            'total_amount_formatted' => $this->total_amount_formatted ?? null,

            'order_number' => $this->order_number ?? null,
            'risk_profile' => $this->risk_profile->value,
            'notes' => $this->notes ?? null,
            'order_data' => $order_data,
            'order_type' => $this->order_data['order_type'] ?? 'transaction',
            'status' => $this->status ?? null,
            'request_platform' => RequestPlatform::tryFrom($this->order_data['request_from']),
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
            'service_logo_png' => $this->service?->getFirstMediaUrl('logo_png') ?? null,
            'service_logo_svg' => $this->service?->getFirstMediaUrl('logo_svg') ?? null,
        ];
    }

    public function oldToArray($request)
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

    private $hidden = [
        'role_id',
        'is_reload',
        'is_reverse',
        'service_stat_data',
        'assign_order',
        'fund_source',
        'remittance_purpose',
        'beneficiary_type_id',
        'compliance_data',
        'vendor_data',
        'system_notification_variable_failed',
        'system_notification_variable_success',
        'wallet_id',
        'bank_id',
        'cash_pickup_id',
        'bank_branch_id',
        'created_at',
        'created_by',
        'beneficiary_id',
        'beneficiary_data.reference_no',
        'beneficiary_data.sender_information.profile',
        'beneficiary_data.sender_information.fcm_token',
        'beneficiary_data.sender_information.language',
        'beneficiary_data.sender_information.currency',
        'beneficiary_data.sender_information.profile',
        'beneficiary_data.receiver_information.city_id',
        'beneficiary_data.receiver_information.state_id',
        'beneficiary_data.receiver_information.country_id',
        'beneficiary_data.receiver_information.city_data',
        'beneficiary_data.receiver_information.state_data',
        'beneficiary_data.receiver_information.country_data',
        'beneficiary_data.receiver_information.relation_data',
        'beneficiary_data.receiver_information.beneficiary_type_id',
        'beneficiary_data.bank_information.bank_data',
        'beneficiary_data.bank_information.vendor_code',
        'beneficiary_data.branch_information.branch_data',
        'beneficiary_data.branch_information.vendor_code',
        'beneficiary_data.wallet_information.vendor_code',
        'beneficiary_data.wallet_information.bank_data',
        'beneficiary_data.cash_pickup_information.vendor_code',
        'beneficiary_data.cash_pickup_information.bank_data',

    ];
}
