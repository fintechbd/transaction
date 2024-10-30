<?php

namespace Fintech\Transaction\Jobs\Compliance;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Transaction\Jobs\Compliance;
use Fintech\Transaction\Traits\HasCompliance;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClientDueDiligencePolicy extends Compliance implements ShouldQueue
{
    use Batchable, Dispatchable, HasCompliance, InteractsWithQueue, Queueable, SerializesModels;

    protected $priority = RiskProfile::Moderate;

    protected $enabled = false;

    private $highThreshold = 1_000;

    private $moderateThreshold = 700;

    protected $code = 'CP005';

    /**
     * Execute the job.
     */
    public function check(): void
    {

        $ekyc = $this->order->user?->profile?->user_profile_data['ekyc'] ?? ['status' => 'rejected'];

        $kycStatus = ($ekyc['status'] ?? 'rejected');

        if ($kycStatus != 'accepted') {

            $currency = $this->order->currency;

            $amountFormatted = \currency($this->order->amount, $currency);

            if ($this->order->amount >= $this->highThreshold) {
                $this->high("{$amountFormatted} amount transferred without client due diligence has crossed the ".\currency($this->highThreshold, $currency).' threshold limit.');
            } elseif ($this->order->amount >= $this->moderateThreshold) {
                $this->moderate("{$amountFormatted} amount transferred without client due diligence has crossed the ".\currency($this->moderateThreshold, $currency).' threshold limit.');
            } else {
                $this->low("{$amountFormatted} amount transferred without client due diligence is below the ".\currency($this->moderateThreshold, $currency).' threshold limit.');
            }
        }
    }
}
