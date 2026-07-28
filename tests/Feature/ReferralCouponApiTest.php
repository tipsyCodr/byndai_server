<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Referrer;
use App\Models\ReferralCoupon;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Models\ReferralSale;
use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferralCouponApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected SubscriptionPlan $plan;
    protected Referrer $referrer;
    protected ReferralCoupon $coupon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $referrerUser = User::factory()->create();
        $this->referrer = Referrer::create([
            'user_id' => $referrerUser->id,
            'full_name' => 'John Referrer',
            'status' => 'approved',
            'is_active' => true,
        ]);

        $this->coupon = ReferralCoupon::create([
            'referrer_id' => $this->referrer->id,
            'code' => 'SAVE10',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'commission_type' => 'percentage',
            'commission_value' => 10.00,
            'is_active' => true,
        ]);

        $this->plan = SubscriptionPlan::create([
            'name' => 'Pro Plan',
            'slug' => 'pro-plan',
            'price' => 100.00,
            'type' => 'monthly',
            'status' => 1,
            'is_free' => 0,
        ]);
    }

    public function test_can_apply_valid_coupon()
    {
        $response = $this->actingAs($this->user)->postJson('/api/apply-coupon', [
            'coupon_code' => 'SAVE10',
            'plan_id' => $this->plan->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'coupon_applied' => true,
                'coupon_validation_status' => 'valid',
                'coupon_code' => 'SAVE10',
                'original_price' => 100.00,
                'discount_amount' => 10.00,
                'final_amount' => 90.00,
                'payable_amount' => 90.00,
                'referrer_name' => 'John Referrer',
            ]);
    }

    public function test_cannot_apply_invalid_coupon()
    {
        $response = $this->actingAs($this->user)->postJson('/api/apply-coupon', [
            'coupon_code' => 'INVALIDCODE',
            'plan_id' => $this->plan->id,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'coupon_applied' => false,
                'coupon_validation_status' => 'invalid',
            ]);
    }

    public function test_validate_coupon_unapproved_referrer()
    {
        $this->referrer->update(['status' => 'pending']);

        $response = $this->actingAs($this->user)->postJson('/api/apply-coupon', [
            'coupon_code' => 'SAVE10',
            'plan_id' => $this->plan->id,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'status' => false,
                'coupon_applied' => false,
                'coupon_validation_status' => 'invalid',
            ]);
    }

    public function test_existing_user_can_register_as_referrer()
    {
        $existingUser = User::factory()->create([
            'email' => 'normaluser@example.com',
            'user_mobile' => '9988776655',
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('aadhaar.jpg');

        $response = $this->postJson('/api/referrer/register', [
            'full_name' => 'Existing User',
            'email' => 'normaluser@example.com',
            'user_mobile' => '9988776655',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'address' => '123 Test Street',
            'aadhaar_photo' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);

        $this->assertDatabaseHas('referrers', [
            'user_id' => $existingUser->id,
            'status' => 'pending',
        ]);
    }

    public function test_auto_approve_setting_approves_registration()
    {
        \App\Models\ReferralSetting::set('auto_approve_referrers', '1');

        $file = \Illuminate\Http\UploadedFile::fake()->image('aadhaar.jpg');

        $response = $this->postJson('/api/referrer/register', [
            'full_name' => 'Auto Approved Referrer',
            'email' => 'autoapproved@example.com',
            'user_mobile' => '1122334455',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'address' => '456 Test Street',
            'aadhaar_photo' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
            ]);

        $this->assertDatabaseHas('referrers', [
            'full_name' => 'Auto Approved Referrer',
            'status' => 'approved',
        ]);
    }

    public function test_admin_can_reset_referrer_password()
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post("/admin/referrers/{$this->referrer->id}/reset-password", [
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newsecret123', $this->referrer->fresh()->user->password));
    }
}
