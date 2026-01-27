<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'product_name' => fake()->words(3, true),
            'product_price' => fake()->randomFloat(2, 10, 1000),
            'description' => fake()->sentence(),
            'status' => 1,
        ];
    }
}
