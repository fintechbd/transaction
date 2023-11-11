<?php

namespace Fintech\Transaction\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    public $signature = 'transaction:install';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
