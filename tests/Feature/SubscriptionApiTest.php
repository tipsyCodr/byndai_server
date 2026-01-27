<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class SubscriptionApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_plans()
    {
        Plan::factory()->count(2)->create(['status' => 1]);

        $response = $this->getJson('/api/plans');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data');
    }

    public function test_can_create_plan()
    {
        $payload = [
            'name' => 'Gold Plan',
            'price' => 1000,
            'interval' => '1 month',
            'status' => 1
        ];

        $response = $this->postJson('/api/plans', $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('plans', ['name' => 'Gold Plan']);
    }

    public function test_can_create_subscription()
    {
        $plan = Plan::factory()->create(['interval' => '1 month']);

        $payload = [
            'plan_id' => $plan->id,
            'start_date' => now()->toDateString(),
            'status' => 1
        ];

        $response = $this->postJson('/api/create-subscription', $payload);
        $response->assertStatus(200);
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'plan_id' => $plan->id
        ]);
    }
}
