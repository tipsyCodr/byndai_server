<?php

namespace Tests\Feature;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ChatApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_create_chat()
    {
        $otherUser = User::factory()->create();

        $response = $this->postJson("/api/chat/create-chat/{$otherUser->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('chats', [
            'sender_id' => min($this->user->id, $otherUser->id),
            'receiver_id' => max($this->user->id, $otherUser->id),
        ]);
    }

    public function test_can_send_message()
    {
        $otherUser = User::factory()->create();
        $chat = Chat::factory()->create([
            'sender_id' => min($this->user->id, $otherUser->id),
            'receiver_id' => max($this->user->id, $otherUser->id),
        ]);

        $response = $this->postJson('/api/chat/send-message', [
            'chat_id' => $chat->id,
            'message' => 'Hello there!'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('chat_messages', [
            'chat_id' => $chat->id,
            'sender_id' => $this->user->id,
            'message' => 'Hello there!'
        ]);
    }

    public function test_can_get_messages()
    {
        $chat = Chat::factory()->create();
        ChatMessage::factory()->count(3)->create(['chat_id' => $chat->id]);

        $response = $this->getJson("/api/chat/get-message/{$chat->id}");

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'messages');
    }

    public function test_can_mark_messages_as_read()
    {
        $otherUser = User::factory()->create();
        $chat = Chat::factory()->create();
        ChatMessage::factory()->create([
            'chat_id' => $chat->id,
            'sender_id' => $otherUser->id,
            'is_read' => 0
        ]);

        $response = $this->postJson("/api/chat/mark-read/{$chat->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('chat_messages', [
            'chat_id' => $chat->id,
            'is_read' => 1
        ]);
    }

    public function test_can_toggle_like_on_message()
    {
        $message = ChatMessage::factory()->create();

        $response = $this->postJson('/api/chat/like-message', [
            'message_id' => $message->id,
            'emoji' => '👍'
        ]);

        $response->assertStatus(200)->assertJson(['liked' => true]);
        $this->assertDatabaseHas('message_likes', ['message_id' => $message->id, 'user_id' => $this->user->id]);
    }

    public function test_can_delete_message()
    {
        $message = ChatMessage::factory()->create(['sender_id' => $this->user->id]);

        $response = $this->postJson("/api/chat/delete-message/{$message->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('chat_messages', ['id' => $message->id, 'is_deleted' => 1]);
    }
}
