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
        $this->policyModel = transaction()->policy()->findWhere(['code' => $this->code]);
        $this->enabled = $this->policyModel->enabled === true;
        $this->priority = $this->policyModel->priority;
        $this->resolvePolicyName();
    }

    private function setOptions(): void
    {
        foreach ($this->policyModel->policy_data ?? [] as $parameter => $value) {
            if (property_exists($this, $parameter)) {
                $this->{$parameter} = $value;
            }
        }
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->setOptions();

        if ($this->enabled) {

            $this->check();

            $this->updateComplianceReport();
        }
    }
}
