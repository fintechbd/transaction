<?php

namespace Fintech\Transaction\Traits;

use Fintech\Core\Enums\Reload\DepositStatus;
use Fintech\Core\Enums\RequestPlatform;
use Fintech\Core\Enums\Transaction\OrderStatus;
use Fintech\Core\Facades\Core;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property int $id
 * @property OrderStatus|DepositStatus|null $status
 * @property OrderStatus|DepositStatus|null $platform
 * @property string|null $order_number
 * @property string|null $notes
 * @property string|null transaction_form_name,
 * @property \DateTime|null $ordered_at
 * @property float $amount
 * @property string|null $currency
 * @property-read string $amount_formatted
 * @property string $converted_currency
 * @property float $converted_amount
 * @property-read string $converted_amount_formatted
 * @property-read string $charge_amount
 * @property-read string $charge_amount_formatted
 * @property-read string $discount_amount
 * @property-read string $discount_amount_formatted
 * @property-read string $commission_amount
 * @property-read string $commission_amount_formatted
 * @property-read string $cost_amount
 * @property-read string $cost_amount_formatted
 * @property-read string $total_amount
 * @property-read string $total_amount_formatted
 * @property-read string $previous_amount
 * @property-read string $previous_amount_formatted
 * @property-read string $current_amount
 * @property-read string $current_amount_formatted
 * @property-read string $interac_charge
 * @property-read string $interac_charge_formatted
 */
trait HasOrderAttributes
{
    public function amountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) \currency($this->amount, $this->currency)
        );
    }

    public function convertedAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) \currency($this->converted_amount, $this->converted_currency)
        );
    }

    public function chargeAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_data['service_stat_data']['charge_amount'] ?? null
        );
    }

    public function chargeAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) \currency($this->charge_amount, $this->currency)
        );
    }

    public function discountAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_data['service_stat_data']['discount_amount'] ?? null
        );
    }

    public function discountAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) \currency($this->discount_amount, $this->currency)
        );
    }

    public function commissionAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_data['service_stat_data']['commission_amount'] ?? null
        );
    }

    public function commissionAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) \currency($this->commission_amount, $this->currency)
        );
    }

    public function costAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_data['service_stat_data']['cost_amount'] ?? null
        );
    }

    public function costAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) \currency($this->cost_amount, $this->currency)
        );
    }

    public function totalAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_data['service_stat_data']['total_amount'] ?? null
        );
    }

    public function totalAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) \currency($this->total_amount, $this->currency)
        );
    }

    public function interacCharge(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_data['service_stat_data']['interac_charge_amount'] ?? null
        );
    }

    public function interacChargeFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) \currency($this->interac_charge, $this->currency)
        );
    }

    public function previousAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_data['previous_amount'] ?? null
        );
    }

    public function previousAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) \currency($this->previous_amount, $this->currency)
        );
    }

    public function currentAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_data['current_amount'] ?? null
        );
    }

    public function currentAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => (string) \currency($this->current_amount, $this->currency)
        );
    }

    public function platform(): Attribute
    {
        return Attribute::make(
            get: fn () => RequestPlatform::tryFrom($this->order_data['request_from'] ?? '')
        );
    }

    public function commonAttributes(...$ignore): array
    {
        $data = [
            'id' => $this->getKey(),
            'currency' => $this->currency,
            'amount' => (string) $this->amount,
            'amount_formatted' => $this->amount_formatted,
            'converted_currency' => $this->converted_currency,
            'converted_amount' => (string) $this->converted_amount,
            'converted_amount_formatted' => $this->converted_amount_formatted,
            'charge_amount' => $this->charge_amount,
            'charge_amount_formatted' => $this->charge_amount_formatted,
            'discount_amount' => $this->discount_amount,
            'discount_amount_formatted' => $this->discount_amount_formatted,
            'commission_amount' => $this->commission_amount,
            'commission_amount_formatted' => $this->commission_amount_formatted,
            'cost_amount' => $this->cost_amount,
            'cost_amount_formatted' => $this->cost_amount_formatted,
            'interac_charge' => $this->interac_charge,
            'interac_charge_formatted' => $this->interac_charge_formatted,
            'total_amount' => $this->total_amount,
            'total_amount_formatted' => $this->total_amount_formatted,
            'previous_amount' => $this->previous_amount,
            'previous_amount_formatted' => $this->previous_amount_formatted,
            'current_amount' => $this->current_amount,
            'current_amount_formatted' => $this->current_amount_formatted,
            'ordered_at' => $this->ordered_at,
            'order_number' => $this->order_number,
            'notes' => $this->notes,
            'status' => $this->status,
            'transaction_form_name' => $this->transaction_form_name ?? null,
            'timeline' => $this->timeline ?? [],
            'risk_profile' => $this->risk_profile ?? null,
            'platform' => $this->platform ?? [],
        ];

        if (Core::packageExists('MetaData')) {
            $data['source_country_name'] = $this->sourceCountry?->name ?? null;
            $data['destination_country_name'] = $this->destinationCountry?->name ?? null;
        } else {
            $data['source_country_name'] = null;
            $data['destination_country_name'] = null;
        }
        if (Core::packageExists('Auth')) {
            $data['sender_receiver_name'] = $this->senderReceiver?->name ?? null;
            $data['user_name'] = $this->user?->name ?? null;
            $data['user_risk_profile'] = $this->user?->risk_profile ?? new \stdClass;
        } else {
            $data['sender_receiver_name'] = null;
            $data['user_name'] = null;
            $data['user_risk_profile'] = null;
        }
        if (Core::packageExists('Business')) {
            $data['service_name'] = $this->service?->service_name ?? null;
            $data['service_type'] = $this->service->serviceType?->all_parent_list ?? null;
            $data['service_vendor_name'] = $item->serviceVendor?->service_vendor_name ?? null;
        } else {
            $data['service_name'] = null;
            $data['service_type'] = null;
            $data['service_vendor_name'] = null;
        }

        foreach ($ignore as $key) {
            unset($data[$key]);
        }

        return $data;

    }
}
