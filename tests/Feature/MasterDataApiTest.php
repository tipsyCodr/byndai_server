<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MasterCountry;
use App\Models\MasterState;
use App\Models\MasterDistrict;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class MasterDataApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_can_get_countries()
    {
        MasterCountry::create(['ctry_name' => 'India']);

        $response = $this->getJson('/api/master-country');

        $response->assertStatus(200)
                 ->assertJsonFragment(['ctry_name' => 'India']);
    }

    public function test_can_get_states()
    {
        $country = MasterCountry::create(['ctry_name' => 'India']);
        MasterState::create(['state_name' => 'Goa', 'c_id' => $country->id]);

        $response = $this->postJson('/api/master-state', ['country_id' => $country->id]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['state_name' => 'Goa']);
    }

    public function test_can_get_districts()
    {
        $state = MasterState::create(['state_name' => 'Goa', 'c_id' => 1]);
        MasterDistrict::create(['district_name' => 'North Goa', 's_id' => $state->id]);

        $response = $this->postJson('/api/master-district', ['state_id' => $state->id]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['district_name' => 'North Goa']);
    }
}
