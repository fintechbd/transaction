<?php

namespace Fintech\Transaction\Seeders;

use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Seeder;

class TransactionFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = $this->data();

        foreach (array_chunk($data, 200) as $block) {
            set_time_limit(2100);
            foreach ($block as $entry) {
                Transaction::transactionForm()->create($entry);
            }
        }
    }

    private function data()
    {
        return [
            [
                'id' => 1,
                'name' => 'Master Point Transfer',
                'code' => 'master_point_transfer',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 2,
                'name' => 'Point Transfer',
                'code' => 'point_transfer',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 3,
                'name' => 'Point Assign',
                'code' => 'point_assign',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 4,
                'name' => 'Point Reload',
                'code' => 'point_reload',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 5,
                'name' => 'BD Top up',
                'code' => 'bangladesh_top_up',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 6,
                'name' => 'Money Transfer',
                'code' => 'money_transfer',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 7,
                'name' => 'BD MB Reload',
                'code' => 'bangladesh_mb_reload',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 8,
                'name' => 'International Top up',
                'code' => 'international_top_up',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 9,
                'name' => 'Singapore Top up',
                'code' => 'singapore_top_up',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 10,
                'name' => 'Wallet Transfer',
                'code' => 'wallet_transfer',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 11,
                'name' => 'E-Reload Transfer',
                'code' => 'e_reload_transfer',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 12,
                'name' => 'Malaysia Top Up',
                'code' => 'malaysia_top_up',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 13,
                'name' => 'Nium Transfer',
                'code' => 'nium_transfer',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 14,
                'name' => 'Manual Refund',
                'code' => 'manual_refund',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 15,
                'name' => 'Malaysia Bill Payment',
                'code' => 'malaysia_bill_payment',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
            [
                'id' => 16,
                'name' => 'Salary Advance',
                'code' => 'salary_advance',
                'enabled' => true,
                'transaction_form_data' => [],
            ],
        ];
    }
}
