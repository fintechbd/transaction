<?php

namespace Fintech\Transaction\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property float $amount
 * @property string $currency
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
            get: fn(mixed $value, array $attributes) => (string)\currency($attributes['amount'], $attributes['currency'])
        );
    }

    public function convertedAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (string)\currency($attributes['converted_amount'], $attributes['converted_currency'])
        );
    }

    public function chargeAmount(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['charge_amount'] ?? null
        );
    }

    public function chargeAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (string)\currency($attributes['order_data']['service_stat_data']['charge_amount'] ?? null, $attributes['currency'])
        );
    }

    public function discountAmount(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['discount_amount'] ?? null
        );
    }

    public function discountAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (string)\currency($attributes['order_data']['service_stat_data']['discount_amount'] ?? null, $attributes['currency'])
        );
    }

    public function commissionAmount(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['commission_amount'] ?? null
        );
    }

    public function commissionAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (string)\currency($attributes['order_data']['service_stat_data']['commission_amount'] ?? null, $attributes['currency'])
        );
    }

    public function costAmount(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['cost_amount'] ?? null
        );
    }

    public function costAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (string)\currency($attributes['order_data']['service_stat_data']['cost_amount'] ?? null, $attributes['currency'])
        );
    }

    public function totalAmount(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['total_amount'] ?? null
        );
    }

    public function totalAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (string)\currency($attributes['order_data']['service_stat_data']['total_amount'] ?? null, $attributes['currency'])
        );
    }

    public function interacCharge(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['interac_charge_amount'] ?? null
        );
    }

    public function interacChargeFormatted(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (string)\currency($attributes['order_data']['service_stat_data']['interac_charge_amount'] ?? null, $attributes['currency'])
        );
    }

    public function previousAmount(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['order_data']['previous_amount'] ?? null
        );
    }

    public function previousAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (string)\currency($attributes['order_data']['previous_amount'] ?? null, $attributes['currency'])
        );
    }

    public function currentAmount(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => $attributes['order_data']['current_amount'] ?? null
        );
    }

    public function currentAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (string)\currency($attributes['order_data']['current_amount'] ?? null, $attributes['currency'])
        );
    }

    public function commonAttributes(): array
    {
        return [
            'currency' => $this->currency,
            'amount' => (string)$this->amount,
            'amount_formatted' => $this->amount_formatted,

            'converted_currency' => $this->converted_currency,
            'converted_amount' => (string)$this->converted_amount,
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
        ];

    }
}
