<?php

namespace seeders;

use Fintech\Transaction\Facades\Transaction;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
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
                Transaction::order()->create($entry);
            }
        }
    }

    private function data()
    {
        return [];
    }
}
