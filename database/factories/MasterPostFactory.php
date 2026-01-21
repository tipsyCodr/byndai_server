<?php

namespace Database\Factories;

use App\Models\MasterIndustry;
use App\Models\MasterPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MasterPostFactory extends Factory
{
    protected $model = MasterPost::class;

    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'indus_type' => MasterIndustry::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'slug' => fake()->slug(),
            'status' => 1,
        ];
    }
}
