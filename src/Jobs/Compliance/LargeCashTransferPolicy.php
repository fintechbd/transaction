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

class LargeCashTransferPolicy implements ShouldBeUnique, ShouldQueue
{
    use Batchable, Dispatchable, HasCompliance, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->setPriority(RiskProfile::Low);
        if ($this->order->amount >= 100) {
            $this->riskProfile = RiskProfile::High;
        } elseif ($this->order->amount >= 20) {
            $this->riskProfile = RiskProfile::Moderate;
        } else {
            $this->riskProfile = RiskProfile::Low;
        }

        $this->updateComplianceReport();
    }
}
