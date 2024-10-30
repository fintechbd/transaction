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

class StructuringDetectionPolicy extends Compliance implements ShouldQueue
{
    use Batchable, Dispatchable, HasCompliance, InteractsWithQueue, Queueable, SerializesModels;

    protected $priority = RiskProfile::High;

    protected $enabled = true;

    private $threshold = 10_000;

    private $radius = 75;

    private $highThreshold = 5;

    private $moderateThreshold = 2;

    protected $code = 'CP006';

    /**
     * Execute the job.
     */
    public function check(): void
    {
        $currency = $this->order->currency;

        $orderCount = floatval(Transaction::order()->findWhere([
            'created_at_start_date' => now()->subHours(24)->format('Y-m-d'),
            'created_at_end_date' => now()->format('Y-m-d'),
            'transaction_form_id' => Transaction::transactionForm()->findWhere(['code' => 'money_transfer'])->getKey(),
            'user_id' => $this->order->user_id,
            'currency' => $currency,
            'count_order' => true,
            'above_amount' => calculate_flat_percent($this->threshold, "{$this->radius}%"),
        ])?->total ?? '0');

        $thresholdFormatted = \currency($this->threshold, $currency);

        if ($orderCount >= $this->highThreshold) {
            $this->high("{$orderCount} orders with amount near to {$thresholdFormatted} in last 24 hours has crossed the {$this->highThreshold} order threshold limit.");
        } elseif ($orderCount >= $this->moderateThreshold) {
            $this->moderate("{$orderCount} orders with amount near to {$thresholdFormatted} in last 24 hours has crossed the {$this->moderateThreshold} order threshold limit.");
        } else {
            $this->low("{$orderCount} orders with amount near to {$thresholdFormatted} in last 24 hours is below the {$this->moderateThreshold} order threshold limit.");
        }
    }
}
