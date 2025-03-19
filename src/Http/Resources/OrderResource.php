<?php

namespace Fintech\Transaction\Http\Resources;

use Fintech\Core\Enums\RequestPlatform;
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
            'ordered_at_formatted' => $this->ordered_at?->format("j<\s\u\p>S</\s\u\p> F, Y h:ia") ?? null,
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
            'status_label' => $this->status ? $this->status->label() : null,
            'request_platform' => RequestPlatform::tryFrom($this->order_data['request_from']),
            'created_at' => $this->created_at ?? null,
            'updated_at' => $this->updated_at ?? null,
            'service_logo_png' => $this->service?->getFirstMediaUrl('logo_png') ?? null,
            'service_logo_svg' => $this->service?->getFirstMediaUrl('logo_svg') ?? null,
        ];
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
