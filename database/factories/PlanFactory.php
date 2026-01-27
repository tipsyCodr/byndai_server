<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word() . ' Plan',
            'price' => fake()->randomFloat(2, 99, 999),
            'interval' => '1 month',
            'duration_days' => 30,
            'features' => json_encode(['feature1', 'feature2']),
            'description' => fake()->sentence(),
            'status' => 1,
        ];
    }
}
