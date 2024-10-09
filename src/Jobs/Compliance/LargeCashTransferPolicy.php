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

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->setPriority(RiskProfile::High);

        $order_amount_sum = Transaction::order()->list([
            'created_at_start_date' => now()->format('Y-m-d'),
            'created_at_end_date' => now()->subHours(24)->format('Y-m-d'),
            'user_id' => $this->order->user_id,
            'currency' => $this->order->currency,
            'sum_amount' => true
        ])['total'];

        if ($order_amount_sum >= 100) {
            $this->riskProfile = RiskProfile::High;
        } elseif ($order_amount_sum >= 20) {
            $this->riskProfile = RiskProfile::Moderate;
        } else {
            $this->riskProfile = RiskProfile::Low;
        }

        $this->updateComplianceReport();
    }
}
