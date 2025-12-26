<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CustomerMaster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class GridApiAlignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_phone_exists_response()
    {
        $user = User::factory()->create(['user_mobile' => '1234567890']);

        $response = $this->postJson('/api/login/phone/check', ['phone_no' => '1234567890']);

        $response->assertStatus(200)
                 ->assertJson(['exists' => true]);
    }

    public function test_check_phone_not_exists_response()
    {
        $response = $this->postJson('/api/login/phone/check', ['phone_no' => '0000000000']);

        $response->assertStatus(404)
                 ->assertJson(['exists' => false]);
    }

    public function test_customer_master_create_payload_alignment()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $payload = [
            'full_name' => 'John Doe',
            'company_name' => 'Test Corp',
            'designation' => 'Manager',
            'phone' => '1234567890',
            'email' => 'john@example.com',
            'address' => '123 Street',
            'notes' => 'Some notes',
        ];

        $response = $this->postJson('/api/customer-master/create', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.desg', 'Manager')
                 ->assertJsonPath('data.mobile_no', '1234567890');
    }

    public function test_customer_master_index_response_fields()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        CustomerMaster::create([
            'user_id' => $user->id,
            'full_name' => 'Jane Doe',
            'company_name' => 'Jane Corp',
            'desg' => 'CEO',
            'mobile_no' => '9876543210',
            'email' => 'jane@example.com',
            'address' => '456 Avenue',
            'date' => now()->toDateString(),
        ]);

        $response = $this->postJson('/api/customer-master/index');

        $response->assertStatus(200)
                 ->assertJsonFragment(['designation' => 'CEO', 'phone' => '9876543210']);
    }

    public function test_profile_card_returns_current_user_data()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        
        // Create master user detail
        \App\Models\MasterUserDetail::create([
            'user_id' => $user->id,
            'fname' => 'Alice',
            'mobile_one' => $user->user_mobile,
            'email_one' => $user->email,
        ]);

        $response = $this->getJson('/api/profile-card');

        $response->assertStatus(200)
                 ->assertJsonPath('data.fname', 'Alice');
    }
}
