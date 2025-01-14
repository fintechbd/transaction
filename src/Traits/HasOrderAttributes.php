<?php

namespace Fintech\Transaction\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property-read string $amount_formatted
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
            get: fn (mixed $value, array $attributes) => (string) \currency($attributes['amount'], $attributes['currency'])
        );
    }

    public function convertedAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (string) \currency($attributes['converted_amount'], $attributes['converted_currency'])
        );
    }

    public function chargeAmount(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['charge_amount'] ?? null
        );
    }

    public function chargeAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (string) \currency($attributes['order_data']['service_stat_data']['charge_amount'] ?? null, $attributes['currency'])
        );
    }

    public function discountAmount(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['discount_amount'] ?? null
        );
    }

    public function discountAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (string) \currency($attributes['order_data']['service_stat_data']['discount_amount'] ?? null, $attributes['currency'])
        );
    }

    public function commissionAmount(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['commission_amount'] ?? null
        );
    }

    public function commissionAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (string) \currency($attributes['order_data']['service_stat_data']['commission_amount'] ?? null, $attributes['currency'])
        );
    }

    public function costAmount(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['cost_amount'] ?? null
        );
    }

    public function costAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (string) \currency($attributes['order_data']['service_stat_data']['cost_amount'] ?? null, $attributes['currency'])
        );
    }

    public function totalAmount(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['total_amount'] ?? null
        );
    }

    public function totalAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (string) \currency($attributes['order_data']['service_stat_data']['total_amount'] ?? null, $attributes['currency'])
        );
    }

    public function interacCharge(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['order_data']['service_stat_data']['interac_charge_amount'] ?? null
        );
    }

    public function interacChargeFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (string) \currency($attributes['order_data']['service_stat_data']['interac_charge_amount'] ?? null, $attributes['currency'])
        );
    }

    public function previousAmount(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['order_data']['previous_amount'] ?? null
        );
    }

    public function previousAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (string) \currency($attributes['order_data']['previous_amount'] ?? null, $attributes['currency'])
        );
    }

    public function currentAmount(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['order_data']['current_amount'] ?? null
        );
    }

    public function currentAmountFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => (string) \currency($attributes['order_data']['current_amount'] ?? null, $attributes['currency'])
        );
    }

    public function commonAttributes(): array
    {
        return [
            'amount' => (string) $this->amount,
        ];

    }
}
