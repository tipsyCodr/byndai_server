<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatMessageFactory extends Factory
{
    protected $model = ChatMessage::class;

    public function definition(): array
    {
        return [
            'chat_id' => Chat::factory(),
            'sender_id' => User::factory(),
            'message' => fake()->sentence(),
            'is_read' => 0,
            'is_delivered' => 1,
            'is_deleted' => 0,
        ];
    }
}
