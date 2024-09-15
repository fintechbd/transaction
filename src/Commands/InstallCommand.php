<?php

namespace Fintech\Transaction\Commands;

use Fintech\Core\Traits\HasCoreSettingTrait;
use Fintech\Transaction\Seeders\TransactionFormSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallCommand extends Command
{
    use HasCoreSettingTrait;

    public $signature = 'transaction:install';

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

    private string $module = 'Transaction';

    public function handle(): int
    {
        $this->infoMessage('Module Installation', 'RUNNING');

        $this->task('Module Installation', function () {
            $this->addSettings();

            $this->addUtilityOptions();

        });

        return self::SUCCESS;
    }

    private function addUtilityOptions(): void
    {
        $seeders = [
            TransactionFormSeeder::class => 'transaction form',
        ];

        foreach ($seeders as $class => $label) {
            $this->task("Populating {$label} data", function () use ($class) {
                Artisan::call('db:seed --class='.addslashes($class).' --quiet');
            });
        }
    }
}
