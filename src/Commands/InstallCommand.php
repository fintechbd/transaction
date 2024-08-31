<?php

namespace Fintech\Transaction\Commands;

use Fintech\Core\Traits\HasCoreSettingTrait;
use Illuminate\Console\Command;

class InstallCommand extends Command
{
    public $signature = 'transaction:install';

    use HasCoreSettingTrait;

    public $description = 'Configure the system for the `fintech/transaction` module';

    private array $settings = [
        [
            'package' => 'transaction',
            'label' => 'Transaction Delay Time',
            'description' => 'Transaction Delay Time',
            'key' => 'delay_time',
            'type' => 'integer',
            'value' => '15',
        ],
        [
            'package' => 'transaction',
            'label' => 'Transaction Minimum Balance',
            'description' => 'Transaction Minimum Balance',
            'key' => 'minimum_balance',
            'type' => 'float',
            'value' => '0.00',
        ],
    ];

    public function handle(): int
    {
        try {

            $this->addOverwriteSetting();

            return self::SUCCESS;

        } catch (\Exception $e) {

            $this->components->twoColumnDetail($e->getMessage(), '<fg=red;options=bold>ERROR</>');

            return self::FAILURE;
        }
    }
}
