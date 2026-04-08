<?php

namespace App\Console\Commands;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\UserSubscriptionFeature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UserUpgradePlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:upgrade {user : The ID of the user} {plan : The ID of the plan} {--method=manual : Payment method} {--payment_id= : Payment ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually upgrade a user to a specific subscription plan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user');
        $planId = $this->argument('plan');
        $method = $this->option('method');
        $paymentId = $this->option('payment_id');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $plan = SubscriptionPlan::with('features')->find($planId);
        if (!$plan) {
            $this->error("Subscription plan with ID {$planId} not found.");
            return 1;
        }

        $this->info("Upgrading User: {$user->user_name} (ID: {$userId}) to Plan: {$plan->name} (ID: {$planId})");

        try {
            DB::beginTransaction();

            // ✅ Step 1: Deactivate old active subscriptions
            UserSubscription::where('user_id', $userId)
                ->where('is_active', 1)
                ->update(['is_active' => 0]);

            // ✅ Step 2: Create new subscription
            $subscription = UserSubscription::create([
                'user_id' => $userId,
                'plan_id' => $plan->id,
                'payment_status' => 'paid',
                'payment_id' => $paymentId,
                'payment_method' => $method,
                'start_date' => now(),
                'end_date' => now()->addMonth(), // Default to 1 month, you can customize this based on $plan->type
                'is_active' => 1,
            ]);

            // ✅ Step 3: Assign new features
            foreach ($plan->features as $feature) {
                UserSubscriptionFeature::create([
                    'user_subscription_id' => $subscription->id,
                    'feature_id' => $feature->feature_id ?? 0,
                    'used_value' => 0,
                    'unit' => $feature->unit,
                    'reset_period' => $plan->type, // e.g., monthly/yearly
                    'last_reset_date' => now(),
                ]);
            }

            DB::commit();

            $this->info('Plan upgraded successfully! 🚀');
            $this->table(
                ['Subscription ID', 'Plan', 'End Date'],
                [[$subscription->id, $plan->name, $subscription->end_date->toDateString()]]
            );

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to upgrade plan: ' . $e->getMessage());
            return 1;
        }
    }
}
