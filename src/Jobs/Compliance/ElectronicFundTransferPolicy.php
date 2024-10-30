<?php

namespace Fintech\Transaction\Jobs\Compliance;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Transaction\Facades\Transaction;
use Fintech\Transaction\Jobs\Compliance;
use Fintech\Transaction\Traits\HasCompliance;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ElectronicFundTransferPolicy extends Compliance implements ShouldQueue
{
    use Batchable, Dispatchable, HasCompliance, InteractsWithQueue, Queueable, SerializesModels;

    protected $priority = RiskProfile::High;

    protected $enabled = true;

    private $highThreshold = 10_000;

    private $moderateThreshold = 5_000;

    protected $code = 'CP003';

    /**
     * Execute the job.
     */
    public function check(): void
    {
        if (($this->order->currency != $this->order->converted_currency)
            || ($this->order->source_country_id != $this->order->destination_country_id)) {

            $currency = $this->order->currency;

            $orderSumAmount = floatval(Transaction::order()->findWhere([
                'created_at_start_date' => now()->subHours(24)->format('Y-m-d'),
                'created_at_end_date' => now()->format('Y-m-d'),
                'transaction_form_id' => Transaction::transactionForm()->findWhere(['code' => 'money_transfer'])->getKey(),
                'user_id' => $this->order->user_id,
                'currency' => $currency,
                'converted_currency_not_in' => [$currency],
                'sum_amount' => true,
            ])?->total ?? '0');

            $amountFormatted = \currency($orderSumAmount, $currency);

            if ($orderSumAmount >= $this->highThreshold) {
                $this->high("{$amountFormatted} electronic fund transferred in last 24 hours has crossed the ".\currency($this->highThreshold, $currency).' threshold limit.');
            } elseif ($orderSumAmount >= $this->moderateThreshold) {
                $this->moderate("{$amountFormatted} electronic fund transferred in last 24 hours has crossed the ".\currency($this->moderateThreshold, $currency).' threshold limit.');
            } else {
                $this->low("{$amountFormatted} electronic fund transferred in last 24 hours is below the ".\currency($this->moderateThreshold, $currency).' threshold limit.');
            }
        }
    }
}
