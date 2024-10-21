<?php

namespace Fintech\Transaction\Traits;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Support\Str;

trait HasCompliance
{
    /**
     * Create a new job instance.
     */
    public function __construct($orderId)
    {
        $this->order = Transaction::order()->find($orderId);
        $this->riskProfile = $this->order->risk_profile;

        $this->resolvePolicyName();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->enabled) {

            $this->check();

            $this->updateComplianceReport();
        }
    }
}
