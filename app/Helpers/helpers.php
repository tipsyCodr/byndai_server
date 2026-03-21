<?php

use Illuminate\Support\Facades\Auth;
use App\Models\MasterUserDetail;
use App\Models\UserSubscription;
use App\Models\PlanFeature;
use App\Models\UserSubscriptionFeature;

if (!function_exists('current_user')) {
    function current_user()
    {
        return Auth::user();
    }
}

if (!function_exists('current_user_id')) {
    function current_user_id()
    {
        return Auth::id();
    }
}

if (!function_exists('current_user_master')) {
    function current_user_master()
    {
        $user = Auth::user();
        if (!$user) return null;

        return MasterUserDetail::where('user_id', $user->id)->first();
    }
}

if (!function_exists('checkUserFeatureLimit')) {

    function checkUserFeatureLimit($featureId)
    {
        try {
            // ✅ 1. Auth User
            $user = auth()->user();
            if (!$user) {
                return [
                    'status' => false,
                    'message' => 'User not authenticated'
                ];
            }

            $userId = $user->id;

            // ✅ 2. Active Subscription
            $subscription = UserSubscription::where('user_id', $userId)
                ->where('is_active', 1)
                ->first();

            if (!$subscription) {
                return [
                    'status' => false,
                    'message' => 'No active subscription'
                ];
            }

            $planId = $subscription->plan_id;
            $subscriptionId = $subscription->id;

            // ✅ 3. Get all plan features
            $planFeatures = PlanFeature::where('plan_id', $planId)->get();

            if ($planFeatures->isEmpty()) {
                return [
                    'status' => false,
                    'message' => 'No features found in plan'
                ];
            }

            // ✅ 4. Sync missing features into user_subscription_features
            // foreach ($planFeatures as $pf) {

            //     $exists = UserSubscriptionFeature::where('user_subscription_id', $subscriptionId)
            //         ->where('feature_id', $pf->feature_id)
            //         ->exists();

            //     if (!$exists) {
            //         UserSubscriptionFeature::create([
            //             'user_subscription_id' => $subscriptionId,
            //             'feature_id' => $pf->feature_id,
            //             'used_value' => 0,
            //             'unit' => $pf->unit ?? null,
            //         ]);
            //     }
            // }

            // ✅ 4. Sync missing features into user_subscription_features
           foreach ($planFeatures as $pf) {
           
               $exists = UserSubscriptionFeature::where('user_subscription_id', $subscriptionId)
                   ->where('feature_id', $pf->feature_id)
                   ->exists();
           
               if (!$exists) {
                   UserSubscriptionFeature::create([
                       'user_subscription_id' => $subscriptionId,
                       'feature_id' => $pf->feature_id,
                       'used_value' => 0,
                       'unit' => $pf->unit ?? null,
           
                       // ✅ NEW FIX
                       'reset_period' => $pf->reset_period ?? null,
                       'last_reset_date' => now(),
                   ]);
               }
           }

            // ✅ 5. Get current feature (user side)
            $userFeature = UserSubscriptionFeature::where('user_subscription_id', $subscriptionId)
                ->where('feature_id', $featureId)
                ->first();

            if (!$userFeature) {
                return [
                    'status' => false,
                    'message' => 'Feature not assigned to user'
                ];
            }

            // ✅ 6. Get plan feature limit
            $planFeature = $planFeatures->where('feature_id', $featureId)->first();

            if (!$planFeature) {
                return [
                    'status' => false,
                    'message' => 'Feature not found in plan'
                ];
            }

            $limit = $planFeature->value; // plan limit
            $used = $userFeature->used_value;

            // ✅ 7. Unlimited check
            if ($limit === 'unlimited' || $limit === null) {
                return [
                    'status' => true,
                    'is_exceeded' => false,
                    'plan_id' => $planId,
                    'feature_id' => $featureId,
                    'limit' => $limit,
                    'used_value' => $used
                ];
            }

            // ✅ 8. Compare usage
            if ($used >= (int)$limit) {
                return [
                    'status' => true,
                    'is_exceeded' => true, // ❌ limit reached
                    'plan_id' => $planId,
                    'feature_id' => $featureId,
                    'limit' => (int)$limit,
                    'used_value' => $used
                ];
            }

            return [
                'status' => true,
                'is_exceeded' => false, // ✅ still allowed
                'plan_id' => $planId,
                'feature_id' => $featureId,
                'limit' => (int)$limit,
                'used_value' => $used
            ];

        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

if (!function_exists('incrementFeatureUsage')) {

    function incrementFeatureUsage($featureId, $incrementBy = 1)
    {
        try {
            // ✅ Auth user
            $user = auth()->user();
            if (!$user) return false;

            // ✅ Active subscription
            $subscription = UserSubscription::where('user_id', $user->id)
                ->where('is_active', 1)
                ->first();

            if (!$subscription) return false;

            // ✅ User feature row
            $userFeature = UserSubscriptionFeature::where('user_subscription_id', $subscription->id)
                ->where('feature_id', $featureId)
                ->first();

            if (!$userFeature) return false;

            // ✅ Increment usage
            $userFeature->increment('used_value', $incrementBy);

            return true;

        } catch (\Exception $e) {
            \Log::error('Feature usage increment error: ' . $e->getMessage());
            return false;
        }
    }
}