<?php

namespace Fintech\Transaction\Commands;

use Illuminate\Console\Command;

class TransactionCommand extends Command
{
    public $signature = 'transaction';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
