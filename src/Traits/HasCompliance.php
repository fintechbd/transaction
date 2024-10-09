<?php

namespace Fintech\Transaction\Traits;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Support\Str;

trait HasCompliance
{
    public $tries = 1;

    public $order;

    protected $priority;

    protected string $title;

    protected $riskProfile;

    /**
     * Create a new job instance.
     */
    public function __construct($orderId)
    {
        $this->resolvePolicyName();

        $this->order = Transaction::order()->find($orderId);

        $this->riskProfile = $this->order->risk_profile;
    }

    public function resolvePolicyName(): void
    {
        $this->title = trim(preg_replace('([A-Z])', ' $0', class_basename($this)));
    }

    private function updateComplianceReport(): void
    {
        $order_data = $this->order->order_data;

        $timeline = $this->order->timeline;

        $report = [
            'name' => $this->title,
            'score' => $this->getScore(),
            'risk' => $this->riskProfile->value,
            'priority' => $this->priority,
            'timestamp' => now(),
        ];

        $order_data['compliance_data'][] = $report;

        $timeline[] = [
            'message' => ucfirst($this->title) . ' compliance policy verification completed with risk level (' . $this->riskProfile->value . ').',
            'flag' => 'info',
            'timestamp' => now(),
        ];

        Transaction::order()->update($this->order->getKey(), ['order_data' => $order_data, 'timeline' => $timeline]);
    }

    private function getScore(): int
    {
        return match ($this->riskProfile) {
            RiskProfile::High => mt_rand(67, 100),
            RiskProfile::Moderate => mt_rand(34, 66),
            default => mt_rand(15, 33),
        };
    }

    public function setPriority(RiskProfile $priority): void
    {
        $this->priority = $priority;
    }

    public function failed(?\Throwable $exception): void
    {
        $order_data = $this->order->order_data;

        $timeline = $this->order->timeline;

        $this->riskProfile = RiskProfile::High;

        $report = [
            'name' => $this->title,
            'score' => $this->getScore(),
            'risk' => $this->riskProfile->value,
            'priority' => $this->priority,
            'timestamp' => now(),
        ];

        $order_data['compliance_data'][] = $report;

        $timeline[] = [
            'message' => ucfirst($this->title) . ' verification reported a error: ' . $exception->getMessage(),
            'flag' => 'error',
            'timestamp' => now(),
        ];

        Transaction::order()->update($this->order->getKey(), ['order_data' => $order_data, 'timeline' => $timeline]);
    }

    public function uniqueId(): string
    {
        return Str::slug(get_class($this) . '-' . $this->order->getKey());
    }
}
