<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscribe = [
            ['user_id' => 1, 'plan_id' => 1, 'start_date' => '2025-11-01 00:00:00', 'end_date' => '2025-11-02 00:00:00', 'status' => 1, 'payment_id' => 1, 'renewal_status' => 10],
            ['user_id' => 1, 'plan_id' => 1, 'start_date' => '2025-11-03 00:00:00', 'end_date' => '2025-11-10 00:00:00', 'status' => 1, 'payment_id' => 1, 'renewal_status' => 9],
        ];
        Subscription::insert($subscribe);
    }
}
