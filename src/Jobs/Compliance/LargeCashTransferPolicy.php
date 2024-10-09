<?php

namespace Fintech\Transaction\Jobs\Compliance;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Transaction\Traits\HasCompliance;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LargeCashTransferPolicy implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasCompliance;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->order->amount >= 10_000) {
            $this->riskProfile(RiskProfile::High);
        } elseif ($this->order->amount >= 5_000) {
            $this->riskProfile(RiskProfile::Moderate);
        } else {
            $this->riskProfile(RiskProfile::Low);
        }

        $this->updateComplianceReport();
    }
}