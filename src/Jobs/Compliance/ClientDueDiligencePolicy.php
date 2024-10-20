<?php

namespace Fintech\Transaction\Jobs\Compliance;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Transaction\Traits\HasCompliance;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClientDueDiligencePolicy implements ShouldQueue
{
    use Batchable, Dispatchable, HasCompliance, InteractsWithQueue, Queueable, SerializesModels;

    private $highThreshold = 1_000;

    private $moderateThreshold = 700;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->setPriority(RiskProfile::Moderate);

        $currency = $this->order->currency;

        $amountFormatted = \currency($this->order->amount, $currency);

        $ekyc = $this->order->user?->profile?->profile_data['ekyc'] ?? [];

        $kycStatus = ($ekyc['status'] ?? 'rejected');

        if ($kycStatus != 'accepted') {
            if ($this->order->amount >= $this->highThreshold) {
                $this->riskProfile = RiskProfile::High;
                $this->remarks = "{$amountFormatted} amount transferred without client due diligence has crossed the ".\currency($this->highThreshold, $currency).' threshold limit.';
            } elseif ($this->order->amount >= $this->moderateThreshold) {
                $this->riskProfile = RiskProfile::Moderate;
                $this->remarks = "{$amountFormatted} amount transferred without client due diligence has crossed the ".\currency($this->moderateThreshold, $currency).' threshold limit.';
            } else {
                $this->riskProfile = RiskProfile::Low;
                $this->remarks = "{$amountFormatted} amount transferred without client due diligence is below the ".\currency($this->moderateThreshold, $currency).' threshold limit.';
            }
        } else {
            $this->riskProfile = RiskProfile::Low;
            $this->remarks = "{$amountFormatted} amount transferred with client due diligence is approved.";
        }

        $this->updateComplianceReport();
    }
}
