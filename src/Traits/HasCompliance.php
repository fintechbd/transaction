<?php

namespace Fintech\Transaction\Traits;

trait HasCompliance
{
    /**
     * Create a new job instance.
     */
    public function __construct($orderId)
    {
        $this->order = transaction()->order()->find($orderId);
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
