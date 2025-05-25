<?php

namespace Fintech\Transaction\Seeders;

use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
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
                transaction()->policy()->create($entry);
            }
        }
    }

    private function data()
    {
        return [
            [
                'name' => 'Large Cash Transaction Policy',
                'code' => 'CP001',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'Large Virtual Cash Transaction Policy',
                'code' => 'CP002',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'Electronic Funds Transfer Policy',
                'code' => 'CP003',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'Suspicious Transaction Policy',
                'code' => 'CP004',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'Client Due Diligence Policy',
                'code' => 'CP005',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'Structuring Detection Policy',
                'code' => 'CP006',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'High-Risk Countries Policy',
                'code' => 'CP007',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'HIO(Head of International Organization) Detection Policy',
                'code' => 'CP008',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'PEP(Politically Exposed Person) Detection Policy',
                'code' => 'CP009',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'Account Velocity Policy',
                'code' => 'CP010',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'New Product Usage Policy',
                'code' => 'CP011',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'Dormant Account Activity Policy',
                'code' => 'CP012',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'Third-Party Transactions Policy',
                'code' => 'CP013',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
            [
                'name' => 'Virtual Currency Travel Rule Policy',
                'code' => 'CP014',
                'enabled' => true,
                'risk' => 'green',
                'priority' => 'green',
                'policy_data' => [],
            ],
        ];
    }
}
