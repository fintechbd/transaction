<?php

namespace Fintech\Transaction\Commands;

use Fintech\Core\Traits\HasCoreSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetUserBalanceCommand extends Command
{
    use HasCoreSetting;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:reset-user-balance';
    private string $module = 'Transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset All orders and set balance to 0';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->infoMessage('Reset User Account Balance', 'RUNNING');

        $this->task('Clean Order Table', function () {
            DB::table('orders')->truncate();
        });

        $this->task('Clean Order Detail Table', function () {
            DB::table('order_details')->truncate();
        });

        $this->task('Update User Account Table', function () {
            DB::table('user_accounts')->update([
                'user_account_data->spent_amount' => 0,
                'user_account_data->deposit_amount' => 0,
                'user_account_data->available_amount' => 0
            ]);
        });
    }
}
