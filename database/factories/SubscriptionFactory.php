<?php

namespace Database\Factories;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'amount' => 999,
            'status' => 1,
            'renewal_status' => 10,
        ];
    }
}
