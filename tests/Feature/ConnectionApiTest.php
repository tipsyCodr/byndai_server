<?php

namespace Tests\Feature;

use App\Models\Connection;
use App\Models\ConnectionRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ConnectionApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_send_connection_request()
    {
        $otherUser = User::factory()->create();

        $response = $this->postJson('/api/connection-create', [
            'receiver_id' => $otherUser->id
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('connection_requests', [
            'sender_id' => $this->user->id,
            'receiver_id' => $otherUser->id,
            'status' => 0
        ]);
    }

    public function test_can_list_connection_requests()
    {
        $sender = User::factory()->create();
        ConnectionRequest::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $this->user->id,
            'status' => 0
        ]);

        $response = $this->getJson('/api/connection-request');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data.received_requests');
    }

    public function test_can_accept_connection_request()
    {
        $sender = User::factory()->create();
        $request = ConnectionRequest::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $this->user->id,
            'status' => 0
        ]);

        $response = $this->postJson("/api/connection-accept/{$request->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('connection_requests', ['id' => $request->id, 'status' => 2]);
        $this->assertDatabaseHas('connections', ['user_id' => $this->user->id, 'friend_id' => $sender->id]);
    }

    public function test_can_reject_connection_request()
    {
        $sender = User::factory()->create();
        $request = ConnectionRequest::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $this->user->id,
            'status' => 0
        ]);

        $response = $this->postJson("/api/connection-reject/{$request->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('connection_requests', ['id' => $request->id, 'status' => 1]);
    }

    public function test_can_list_connections()
    {
        $friend = User::factory()->create();
        Connection::factory()->create([
            'user_id' => $this->user->id,
            'friend_id' => $friend->id
        ]);

        $response = $this->getJson('/api/connection-show');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    public function test_can_remove_connection()
    {
        $friend = User::factory()->create();
        $connection = Connection::factory()->create([
            'user_id' => $this->user->id,
            'friend_id' => $friend->id
        ]);

        $response = $this->postJson("/api/connection-remove/{$connection->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('connections', ['id' => $connection->id]);
    }
}
