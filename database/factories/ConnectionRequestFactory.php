<?php

namespace Database\Factories;

use App\Models\ConnectionRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConnectionRequestFactory extends Factory
{
    protected $model = ConnectionRequest::class;

    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'request_type' => 'friend_request',
            'status' => 0,
            'requested_at' => now(),
        ];
    }
}
