<?php

namespace Fintech\Transaction\Jobs\Compliance;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Transaction\Facades\Transaction;
use Fintech\Transaction\Traits\HasCompliance;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LargeCashTransferPolicy implements ShouldBeUnique, ShouldQueue
{
    use Batchable, Dispatchable, HasCompliance, InteractsWithQueue, Queueable, SerializesModels;

    private $highThreshold = 100;

    private $moderateThreshold = 50;

    private $lowThreshold = 20;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->setPriority(RiskProfile::High);

        $order_amount_sum = floatval(Transaction::order()->findWhere([
            'created_at_start_date' => now()->format('Y-m-d'),
            'created_at_end_date' => now()->subHours(24)->format('Y-m-d'),
            'user_id' => $this->order->user_id,
            'currency' => $this->order->currency,
            'sum_amount' => true,
        ])?->total ?? '0');

        if ($order_amount_sum >= $this->highThreshold) {
            $this->riskProfile = RiskProfile::High;
            $this->remarks = \currency($order_amount_sum, $this->order->currency).' amount transferred in last 24 hours has crossed the '.\currency($this->highThreshold, $this->order->currency).' threshold limit.';
        } elseif ($order_amount_sum >= $this->moderateThreshold) {
            $this->riskProfile = RiskProfile::Moderate;
            $this->remarks = \currency($order_amount_sum, $this->order->currency).' amount transferred in last 24 hours has crossed the '.\currency($this->moderateThreshold, $this->order->currency).' threshold limit.';
        } else {
            $this->riskProfile = RiskProfile::Low;
            $this->remarks = \currency($order_amount_sum, $this->order->currency).' amount transferred in last 24 hours is below the '.\currency($this->lowThreshold, $this->order->currency).' threshold limit.';
        }

        $this->updateComplianceReport();
    }
}
