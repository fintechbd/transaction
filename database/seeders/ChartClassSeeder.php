<?php

namespace Fintech\Transaction\Seeders;

use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Seeder;

class ChartClassSeeder extends Seeder
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
                Transaction::chartClass()->create($entry);
            }
        }
    }

    private function data()
    {
        return [
            [
                'id' => '1',
                'name' => 'Asset',
                'code' => '1',
                'chart_class_data' => [],
            ],
            [
                'id' => '2',
                'name' => 'Liability',
                'code' => '2',
                'chart_class_data' => [],
            ],
            [
                'id' => '3',
                'name' => 'Equity',
                'code' => '3',
                'chart_class_data' => [],
            ],
            [
                'id' => '4',
                'name' => 'Revenue',
                'code' => '4',
                'chart_class_data' => [],
            ],
            [
                'id' => '5',
                'name' => 'Expense',
                'code' => '5',
                'chart_class_data' => [],
            ],
            [
                'id' => '6',
                'name' => 'Other',
                'code' => '6',
                'chart_class_data' => [],
            ],
        ];
    }
}
