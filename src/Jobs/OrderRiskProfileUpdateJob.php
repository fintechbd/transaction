<?php

namespace Fintech\Transaction\Jobs;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Core\Enums\Transaction\OrderStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderRiskProfileUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Fintech\Core\Abstracts\BaseModel
     */
    public $transaction;

    /**
     * Create a new job instance.
     */
    public function __construct($transactionId)
    {
        $this->transaction = transaction()->order()->find($transactionId);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->transaction) {

            $order_data = $this->transaction->order_data;

            $verdicts = $order_data['compliance_data'] ?? [];

            $policies = count($verdicts);

            $riskValues = 0;

            foreach ($verdicts as $verdict) {
                $riskValues[] = match (RiskProfile::from($verdict->level)) {
                    RiskProfile::High => mt_rand(67, 100),
                    RiskProfile::Moderate => mt_rand(34, 66),
                    RiskProfile::Low => mt_rand(15, 33),
                    default => 10
                };
            }

            $order_data['risk_profile_values'] = $riskValues;
            $order_data['risk_profile_avg'] = array_sum($riskValues) / $policies;

            $riskProfile = $this->transaction->risk_profile;

            if ($order_data['risk_profile_avg'] >= 67) {
                $riskProfile = RiskProfile::High;
            } elseif ($order_data['risk_profile_avg'] >= 34) {
                $riskProfile = RiskProfile::Moderate;
            }

            transaction()->order()->update($this->transaction->getKey(), [
                'status' => OrderStatus::Processing,
                'risk_profile' => $riskProfile,
                'order_data' => $order_data,
            ]);
        }
    }
}
