<?php

namespace Fintech\Transaction\Jobs;

use Fintech\Core\Abstracts\BaseModel;
use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Transaction\Facades\Transaction;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

abstract class Compliance
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    /**
     * @var BaseModel|null
     */
    public $order;

    /**
     * @var string|null
     */
    public $remarks;

    /**
     * @var RiskProfile::case|null
     */
    protected $priority;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var RiskProfile|null
     */
    protected $riskProfile;

    /**
     * @var bool
     */
    protected $enabled = true;

    public function resolvePolicyName(): void
    {
        $this->title = preg_replace('/([A-Z])/', ' $0', class_basename($this));

        $this->title = trim(preg_replace('/(.+)\sPolicy$/', '$1', $this->title));

    }

    protected function updateComplianceReport(): void
    {
        $order_data = $this->order->order_data;

        $timeline = $this->order->timeline;

        $report = [
            'name' => $this->title,
            'score' => $this->getScore(),
            'risk' => $this->riskProfile?->value ?? 'red',
            'priority' => $this->priority?->value ?? 'red',
            'remarks' => $this->remarks,
            'timestamp' => now(),
        ];

        $order_data['compliance_data'][] = $report;

        $timeline[] = [
            'message' => ucfirst($this->title)." compliance policy verification completed with risk level ({$this->riskProfile->value}).",
            'flag' => (($this->riskProfile?->value ?? 'red') == 'green') ? 'info' : 'warn',
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
            'risk' => $this->riskProfile?->value ?? 'red',
            'priority' => $this->priority?->value ?? 'red',
            'remarks' => 'Internal Server Error: '.$exception->getMessage(),
            'timestamp' => now(),
        ];

        $order_data['compliance_data'][] = $report;

        $timeline[] = [
            'message' => ucfirst($this->title).' verification reported a error: '.$exception->getMessage(),
            'flag' => 'error',
            'timestamp' => now(),
        ];

        Transaction::order()->update($this->order->getKey(), ['order_data' => $order_data, 'timeline' => $timeline]);
    }

    public function uniqueId(): string
    {
        return Str::slug(get_class($this).'-'.$this->order->getKey());
    }

    private function setReport(RiskProfile $riskProfile, string $remarks): void
    {
        $this->riskProfile = $riskProfile;
        $this->remarks = $remarks;
    }

    protected function high(string $remarks): void
    {
        $this->setReport(RiskProfile::High, $remarks);
    }

    protected function low(string $remarks): void
    {
        $this->setReport(RiskProfile::Low, $remarks);
    }

    protected function moderate(string $remarks): void
    {
        $this->setReport(RiskProfile::Moderate, $remarks);
    }

    public function check() {}
}