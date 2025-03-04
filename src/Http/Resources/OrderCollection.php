<?php

namespace Fintech\Transaction\Http\Resources;

use Fintech\Core\Enums\RequestPlatform;
use Fintech\Core\Enums\Transaction\OrderType;
use Fintech\Core\Facades\Core;
use Fintech\Core\Supports\Constant;
use Fintech\Transaction\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class OrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($order) {

            /**
             * @var Order $order
             */

            $order_data = $order->order_data ?? [];

            Arr::forget($order_data, $this->hidden);

            $order_data['current_amount'] = $order->current_amount_formatted;
            $order_data['sending_amount'] = (string)\currency($order_data['sending_amount'] ?? null, $order->currency);
            $order_data['previous_amount'] = $order->previous_amount_formatted;

            return [
                'id' => $order->getKey(),
                'description' => $order->description ?? null,
                'source_country_name' => $order->sourceCountry?->name ?? null,
                'destination_country_name' => $order->destinationCountry?->name ?? null,
                'sender_receiver_name' => $order->senderReceiver?->name ?? null,
                'user_name' => $order->user?->name ?? null,
                'service_name' => $order->service?->service_name ?? null,
                'service_type' => $order->service->serviceType?->all_parent_list ?? null,
                'transaction_form_name' => $order->transaction_form_name ?? null,
                'ordered_at' => $order->ordered_at ?? null,
                'currency' => $order->currency ?? null,
                'amount' => (string)($order->amount ?? null),
                'amount_formatted' => $order->amount_formatted,

                'converted_currency' => $order->converted_currency ?? null,
                'converted_amount' => (string)($order->converted_amount ?? null),
                'converted_amount_formatted' => $order->converted_amount_formatted ?? null,

                'charge_amount' => $order->charge_amount ?? null,
                'charge_amount_formatted' => $order->charge_amount_formatted ?? null,

                'discount_amount' => $order->discount_amount ?? null,
                'discount_amount_formatted' => $order->discount_amount_formatted ?? null,

                'commission_amount' => $order->commission_amount ?? null,
                'commission_amount_formatted' => $order->commission_amount_formatted ?? null,

                'cost_amount' => $order->cost_amount ?? null,
                'cost_amount_formatted' => $order->cost_amount_formatted ?? null,

                'interac_charge' => $order->interac_charge_amount ?? null,
                'interac_charge_formatted' => $order->interac_charge_amount_formatted ?? null,

                'total_amount' => $order->total_amount ?? null,
                'total_amount_formatted' => $order->total_amount_formatted ?? null,

                'order_number' => $order->order_number ?? null,
                'risk_profile' => $order->risk_profile->value,
                'notes' => $order->notes ?? null,
                'order_data' => $order_data,
                'order_type' => $order->order_data['order_type'] ?? 'transaction',
                'status' => $order->status ?? null,
                'request_platform' => RequestPlatform::tryFrom($order->order_data['request_from']),
                'created_at' => $order->created_at ?? null,
                'updated_at' => $order->updated_at ?? null,
                'service_logo_png' => $order->service?->getFirstMediaUrl('logo_png') ?? null,
                'service_logo_svg' => $order->service?->getFirstMediaUrl('logo_svg') ?? null,
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
                'order_type' => OrderType::values(),
            ],
            'query' => $request->all(),
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
