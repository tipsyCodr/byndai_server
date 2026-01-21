<?php

namespace Database\Factories;

use App\Models\MasterIndustry;
use Illuminate\Database\Eloquent\Factories\Factory;

class MasterIndustryFactory extends Factory
{
    protected $model = MasterIndustry::class;

    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'status' => 1,
        ];
    }
}
