<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Referrer;
use App\Models\ReferralCoupon;
use App\Models\SubscriptionPlan;
use App\Models\Transaction;
use App\Models\ReferralSale;
use App\Models\ReferralCommission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DummyReferrerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Referrer role exists
        Role::firstOrCreate(['name' => 'Referrer', 'guard_name' => 'web']);

        // 2. Create or find Referrer User
        $user = User::firstOrCreate(
            ['email' => 'referrer@example.com'],
            [
                'user_name' => 'Demo Referrer',
                'user_mobile' => '9876543210',
                'password' => Hash::make('password'),
                'status' => '1',
            ]
        );
        $user->assignRole('Referrer');

        // 3. Create or find approved Referrer Profile
        $referrer = Referrer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => 'Demo Referrer Account',
                'address' => '123 Business Park, Suite 400',
                'status' => 'approved',
                'is_active' => true,
            ]
        );

        // 4. Create Referral Coupon
        $coupon = ReferralCoupon::firstOrCreate(
            ['referrer_id' => $referrer->id],
            [
                'code' => 'DEMO100',
                'discount_type' => 'percentage',
                'discount_value' => 10.00,
                'commission_type' => 'percentage',
                'commission_value' => 15.00,
                'is_active' => true,
            ]
        );

        // 5. Ensure a Subscription Plan exists
        $plan = SubscriptionPlan::firstOrCreate(
            ['slug' => 'pro-monthly'],
            [
                'name' => 'Pro Monthly Plan',
                'price' => 2000.00,
                'type' => 'monthly',
                'status' => 1,
                'is_free' => 0,
            ]
        );

        // 6. Create Customer User
        $customer = User::firstOrCreate(
            ['email' => 'dummycustomer@example.com'],
            [
                'user_name' => 'Dummy Customer',
                'user_mobile' => '9123456789',
                'password' => Hash::make('password'),
                'status' => '1',
            ]
        );

        // 7. Create 3 Dummy Transactions & Referral Sales with Pending Commissions
        for ($i = 1; $i <= 3; $i++) {
            $transaction = Transaction::create([
                'user_id' => $customer->id,
                'plan_id' => $plan->id,
                'payment_gateway' => 'razorpay',
                'payment_method' => 'online',
                'gateway_order_id' => 'order_demo_' . $i . '_' . time(),
                'gateway_payment_id' => 'pay_demo_' . $i . '_' . time(),
                'amount' => 1800.00,
                'currency' => 'INR',
                'coupon_id' => $coupon->id,
                'referrer_id' => $referrer->id,
                'discount_amount' => 200.00,
                'status' => 'success',
            ]);

            $sale = ReferralSale::create([
                'referrer_id' => $referrer->id,
                'coupon_id' => $coupon->id,
                'user_id' => $customer->id,
                'subscription_plan_id' => $plan->id,
                'transaction_id' => $transaction->id,
                'original_price' => 2000.00,
                'discount_amount' => 200.00,
                'final_amount' => 1800.00,
                'payment_status' => 'paid',
                'commission_amount' => 270.00, // 15% of 1800
                'created_at' => now()->subDays($i * 2),
            ]);

            ReferralCommission::create([
                'referral_sale_id' => $sale->id,
                'referrer_id' => $referrer->id,
                'amount' => 270.00,
                'status' => 'pending',
                'created_at' => now()->subDays($i * 2),
            ]);
        }
    }
}
