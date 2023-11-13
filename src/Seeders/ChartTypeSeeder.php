<?php

namespace Fintech\Transaction\Seeders;

use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Seeder;

class ChartTypeSeeder extends Seeder
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
                Transaction::chartType()->create($entry);
            }
        }
    }

    private function data()
    {
        return [];
    }
}
