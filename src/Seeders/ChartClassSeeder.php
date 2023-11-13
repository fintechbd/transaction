<?php

namespace Fintech\Transaction\Seeders;

use Illuminate\Database\Seeder;
use Fintech\Transaction\Facades\Transaction;

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
        return array(
            array(
                'id' => '1',
                'name' => 'Asset',
                'code' => '1',
                'chart_class_data' => array()
            ),
            array(
                'id' => '2',
                'name' => 'Liability',
                'code' => '2',
                'chart_class_data' => array()
            ),
            array(
                'id' => '3',
                'name' => 'Equity',
                'code' => '3',
                'chart_class_data' => array()
            ),
            array(
                'id' => '4',
                'name' => 'Revenue',
                'code' => '4',
                'chart_class_data' => array()
            ),
            array(
                'id' => '5',
                'name' => 'Expense',
                'code' => '5',
                'chart_class_data' => array()
            ),
            array(
                'id' => '6',
                'name' => 'Other',
                'code' => '6',
                'chart_class_data' => array()
            ),
        );
    }
}
