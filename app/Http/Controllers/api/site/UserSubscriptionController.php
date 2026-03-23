<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use App\Models\PlanFeature;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscriptionFeature;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserSubscriptionController extends Controller
{
    // ✅ Get all subscriptions
    public function index()
    {
        $data = UserSubscription::with(['user', 'plan', 'features'])
                    ->latest()
                    ->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }



public function mySubscription(Request $request)
{
    $user = $request->user();

    $subscription = UserSubscription::with(['plan', 'features'])
        ->where('user_id', $user->id)
        ->where('is_active', 1)
        ->latest()
        ->first();

      Log::info('📦 1.Subscription Data:', [
            'subscription' => $subscription ? $subscription->toArray() : null
        ]);

    // ✅ Agar subscription nahi mila → FREE return
    if (!$subscription) {
        return response()->json([
            'status' => true,
            'data' => [
                'plan' => [
                    'name' => 'free'
                ],
                'features' => []
            ]
        ]);
    }

    // ✅ Plan features ek hi query me load  
    $planFeatures = PlanFeature::where('plan_id', $subscription->plan_id)
        ->get()
        ->keyBy('feature'); // 🔥 fast lookup

      Log::info('📊2. Plan Features:', [
        'planFeatures' => $planFeatures->toArray()
       ]);

    // ✅ Features me limit_value attach karo
    $subscription->features->transform(function ($feature) use ($planFeatures) {

        Log::info('🚨 3.Check  $feature Columns:', $feature->toArray());

        $planFeature = $planFeatures->get($feature->feature);

        return [
            'id' => $feature->id,
            'feature_id' => $feature->feature_id,
            'used_value' => $feature->used_value,
            'limit_value' => $planFeature->value ?? null, // 🔥 yahi main cheez
            'unit' => $feature->unit,
            'reset_period' => $feature->reset_period,
            'last_reset_date' => $feature->last_reset_date,
        ];
    });

    return response()->json([
        'status' => true,
        'data' => $subscription
    ]);
}

    // ✅ Create subscription
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $subscription = UserSubscription::create([
            'user_id' => $request->user_id,
            'plan_id' => $request->plan_id,
            'payment_status' => $request->payment_status,
            'payment_id' => $request->payment_id,
            'payment_method' => $request->payment_method ?? 'online',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active ?? 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Subscription created successfully',
            'data' => $subscription
        ]);
    }

    // ✅ Get single subscription
    public function show($id)
    {
        $subscription = UserSubscription::with(['user', 'plan', 'features'])
                            ->find($id);

        if (!$subscription) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $subscription
        ]);
    }

    // ✅ Update subscription
    public function update(Request $request, $id)
    {
        $subscription = UserSubscription::find($id);

        if (!$subscription) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription not found'
            ], 404);
        }

        $request->validate([
            'plan_id' => 'sometimes|exists:subscription_plans,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'payment_status' => 'sometimes|in:pending,paid,failed',
        ]);

        $subscription->update([
            'plan_id' => $request->plan_id ?? $subscription->plan_id,
            'payment_status' => $request->payment_status ?? $subscription->payment_status,
            'payment_id' => $request->payment_id ?? $subscription->payment_id,
            'payment_method' => $request->payment_method ?? $subscription->payment_method,
            'start_date' => $request->start_date ?? $subscription->start_date,
            'end_date' => $request->end_date ?? $subscription->end_date,
            'is_active' => $request->is_active ?? $subscription->is_active,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Subscription updated successfully',
            'data' => $subscription
        ]);
    }

    // ✅ Delete subscription
    public function destroy($id)
    {
        $subscription = UserSubscription::find($id);

        if (!$subscription) {
            return response()->json([
                'status' => false,
                'message' => 'Subscription not found'
            ], 404);
        }

        $subscription->delete();

        return response()->json([
            'status' => true,
            'message' => 'Subscription deleted successfully'
        ]);
    }

    // ✅ Get active subscription of user
    public function getUserActiveSubscription($userId)
    {
        $subscription = UserSubscription::with(['plan', 'features'])
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'status' => false,
                'message' => 'No active subscription'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $subscription
        ]);
    }

    public function assignFreeSubscription(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);
    
        $userId = $request->user_id;
    
        // ✅ Check already subscription hai ya nahi
        $existing = UserSubscription::where('user_id', $userId)->exists();
    
        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'User already has a subscription'
            ]);
        }
    
        // ✅ Free plan fetch karo
        $freePlan = SubscriptionPlan::where('is_free', 1)->first();
    
        if (!$freePlan) {
            return response()->json([
                'status' => false,
                'message' => 'Free plan not found'
            ]);
        }
    
        // ✅ Subscription create karo
        $subscription = UserSubscription::create([
            'user_id' => $userId,
            'plan_id' => $freePlan->id,
            'payment_id'=> null,
            'payment_status' => null,
            'payment_method' => null,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addYears(10), // long duration
            'is_active' => 1,
        ]);
    
        // ✅ Plan ke features fetch karo
        $planFeatures = $freePlan->features;
    
        // ✅ Features ko user_subscription_features me insert karo
        foreach ($planFeatures as $feature) {
            UserSubscriptionFeature::create([
                'user_subscription_id' => $subscription->id,
                'feature_id' => $feature->feature_id || 0,                
                'used_value' => 0,
                'unit' => $feature->unit,
                'reset_period' => $freePlan->type,
                'last_reset_date' => now(),
            ]);
        }
    
        return response()->json([
            'status' => true,
            'message' => 'Free subscription assigned successfully',
            'data' => $subscription->load('features', 'plan')
        ]);
    }

    public function upgradePlan(Request $request)
    {
       $request->validate([
           'user_id' => 'required|exists:users,id',
           'plan_id' => 'required|exists:subscription_plans,id',
       ]);

       $userId = $request->user_id;
       $newPlanId = $request->plan_id;

       // ✅ New plan fetch
       $plan = SubscriptionPlan::with('features')->find($newPlanId);

       if (!$plan) {
           return response()->json([
               'status' => false,
               'message' => 'Plan not found'
           ]);
       }

       // ✅ Step 1: old active subscription deactivate karo
       UserSubscription::where('user_id', $userId)
           ->where('is_active', 1)
           ->update(['is_active' => 0]);

       // ✅ Step 2: new subscription create karo
       $subscription = UserSubscription::create([
           'user_id' => $userId,
           'plan_id' => $plan->id,
           'payment_status' => 'paid',
           'payment_id' => $request->payment_id ?? null,
           'payment_method' => $request->payment_method ?? 'online',
           'start_date' => now(),
           'end_date' => now()->addMonth(), // ya dynamic karo based on plan
           'is_active' => 1,
       ]);

       // ✅ Step 3: old features delete (safety)
       UserSubscriptionFeature::where('user_subscription_id', $subscription->id)->delete();

       // ✅ Step 4: new features assign karo
       foreach ($plan->features as $feature) {
           UserSubscriptionFeature::create([
               'user_subscription_id' => $subscription->id,
               'feature_id' => $feature->feature_id || 0,
               'used_value' => 0,
               'unit' => $feature->unit,
               'reset_period' => $plan->type, // monthly/yearly
               'last_reset_date' => now(),
           ]);
       }

       return response()->json([
           'status' => true,
           'message' => 'Plan upgraded successfully 🚀',
           'data' => $subscription->load('plan', 'features')
       ]);
    }

    public function upgradeUserPlan(Request $request)
    {
       $request->validate([
           'user_id' => 'required|exists:users,id',
           'plan_id' => 'required|exists:subscription_plans,id',
       ]);

       $userId = $request->user_id;
       $newPlanId = $request->plan_id;

       \DB::beginTransaction();

       try {

           // ✅ Plan fetch with features
           $plan = SubscriptionPlan::with('features')->find($newPlanId);

           if (!$plan) {
               return response()->json([
                   'status' => false,
                   'message' => 'Plan not found'
               ]);
           }

           // ✅ Check active subscription
           $activeSub = UserSubscription::where('user_id', $userId)
               ->where('is_active', 1)
               ->first();

           // 🚫 SAME PLAN DOUBLE BUY BLOCK
           if ($activeSub && $activeSub->plan_id == $newPlanId) {
               return response()->json([
                   'status' => false,
                   'message' => 'User already subscribed to this plan'
               ]);
           }

           // ✅ Step 1: old subscription deactivate
           if ($activeSub) {
               $activeSub->update(['is_active' => 0]);
           }

           // ✅ Step 2: duration logic
           $startDate = now();

           $endDate = match ($plan->type) {
               'monthly' => now()->addMonth(),
               'yearly' => now()->addYear(),
               'weekly' => now()->addWeek(),
               default => now()->addMonth(),
           };

           // ✅ Step 3: create new subscription
           $subscription = UserSubscription::create([
               'user_id' => $userId,
               'plan_id' => $plan->id,
               'payment_status' => 'paid',
               'payment_id' => $request->payment_id ?? null,
               'payment_method' => $request->payment_method ?? 'online',
               'start_date' => $startDate,
               'end_date' => $endDate,
               'is_active' => 1,
           ]);

           // ❌ IMPORTANT: NO DELETE (tu naya row chahta hai)
           // so skip delete

           // ✅ Step 4: fresh features insert (NEW ROWS ALWAYS)
           foreach ($plan->features as $feature) {
               UserSubscriptionFeature::create([
                   'user_subscription_id' => $subscription->id,
                   'feature_id' => $feature->feature_id,
                   'used_value' => 0,
                   'unit' => $feature->unit,
                   'reset_period' => $plan->type,
                   'last_reset_date' => now(),
               ]);
           }

           \DB::commit();

           return response()->json([
               'status' => true,
               'message' => 'Plan upgraded successfully 🚀',
               'data' => $subscription->load('plan', 'features')
           ]);

       } catch (\Exception $e) {

           \DB::rollBack();

           return response()->json([
               'status' => false,
               'message' => $e->getMessage()
           ]);
       }
    }
}