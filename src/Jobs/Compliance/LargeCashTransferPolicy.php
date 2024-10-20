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

    private $highThreshold = 10_000;

    private $moderateThreshold = 5_000;

    private $orderSumAmount;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->setPriority(RiskProfile::High);

        $this->orderSumAmount = floatval(Transaction::order()->findWhere([
            'created_at_start_date' => now()->subHours(24)->format('Y-m-d'),
            'created_at_end_date' => now()->format('Y-m-d'),
            'transaction_form_id' => Transaction::transactionForm()->findWhere(['code' => 'money_transfer'])->getKey(),
            'user_id' => $this->order->user_id,
            'currency' => $this->order->currency,
            'sum_amount' => true,
        ])?->total ?? '0');

        if ($this->orderSumAmount >= $this->highThreshold) {
            $this->riskProfile = RiskProfile::High;
            $this->remarks = \currency($this->orderSumAmount, $this->order->currency).' amount transferred in last 24 hours has crossed the '.\currency($this->highThreshold, $this->order->currency).' threshold limit.';
        } elseif ($this->orderSumAmount >= $this->moderateThreshold) {
            $this->riskProfile = RiskProfile::Moderate;
            $this->remarks = \currency($this->orderSumAmount, $this->order->currency).' amount transferred in last 24 hours has crossed the '.\currency($this->moderateThreshold, $this->order->currency).' threshold limit.';
        } else {
            $this->riskProfile = RiskProfile::Low;
            $this->remarks = \currency($this->orderSumAmount, $this->order->currency).' amount transferred in last 24 hours is below the '.\currency($this->moderateThreshold, $this->order->currency).' threshold limit.';
        }

        $this->updateComplianceReport();
    }
}
