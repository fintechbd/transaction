<?php

namespace Fintech\Transaction\Traits;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Transaction\Facades\Transaction;

trait HasCompliance
{
    public $order;

    public RiskProfile $riskProfile;

    /**
     * Create a new job instance.
     */
    public function __construct($orderId)
    {
        $this->order = Transaction::order()->find($orderId);

        $this->riskProfile = $this->order->risk_profile;
    }
}
