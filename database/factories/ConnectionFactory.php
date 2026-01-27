<?php

namespace Database\Factories;

use App\Models\Connection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectionFactory extends Factory
{
    protected $model = Connection::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'friend_id' => User::factory(),
            'connected_at' => now(),
        ];
    }
}
