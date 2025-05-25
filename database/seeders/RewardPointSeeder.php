<?php

namespace Fintech\Transaction\Seeders;

use Illuminate\Database\Seeder;

class RewardPointSeeder extends Seeder
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
                transaction()->rewardPoint()->create($entry);
            }
        }
    }

    private function data()
    {
        return [];
    }
}
