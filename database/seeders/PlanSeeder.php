<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            ['name' => 'Pro', 'price' => 500, 'interval' => '3 Month', 'duration_days' => '90', 'features' => 'Test', 'description' => 'Test', 'status' => 1],
            ['name' => 'Plus', 'price' => 1000, 'interval' => '6 Month', 'duration_days' => '180', 'features' => 'Test', 'description' => 'Test', 'status' => 1],
            ['name' => 'Go', 'price' => 2000, 'interval' => '1 Years', 'duration_days' => '365', 'features' => 'Test', 'description' => 'Test', 'status' => 1],
        ];
        Plan::insert($plans);
    }
}
