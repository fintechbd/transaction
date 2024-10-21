<?php

namespace Fintech\Transaction\Jobs\Compliance;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Transaction\Facades\Transaction;
use Fintech\Transaction\Jobs\Compliance;
use Fintech\Transaction\Traits\HasCompliance;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DormantAccountActivityPolicy extends Compliance implements ShouldQueue
{
    use Batchable, Dispatchable, HasCompliance, InteractsWithQueue, Queueable, SerializesModels;

    protected $priority = RiskProfile::Moderate;

    protected $enabled = true;

    private $highThreshold = 3;

    private $moderateThreshold = 1;

    /**
     * Execute the job.
     */
    public function check(): void
    {
        $currency = $this->order->currency;

        $orderCount = intval(Transaction::order()->findWhere([
            'created_at_start_date' => now()->subMonths(3)->format('Y-m-d'),
            'created_at_end_date' => now()->format('Y-m-d'),
            'user_id' => $this->order->user_id,
            'currency' => $currency,
            'count_order' => true,
        ])?->total ?? '0');

        $serviceName = $this->order?->service?->service_name ?? 'N/A';

        if ($orderCount >= $this->highThreshold) {
            $this->high("{$orderCount} new orders on {$serviceName} in last 7 days has crossed the {$this->highThreshold} order threshold limit.");
        } elseif ($orderCount >= $this->moderateThreshold) {
            $this->moderate("{$orderCount} new orders on {$serviceName} in last 7 days has crossed the {$this->moderateThreshold} order threshold limit.");
        } else {
            $this->low("{$orderCount} new orders on {$serviceName} in last 7 days is below the {$this->moderateThreshold} order threshold limit.");
        }
    }
}
