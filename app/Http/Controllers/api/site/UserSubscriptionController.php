<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use App\Models\PlanFeature;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscriptionFeature;

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

    // ✅ Features me limit_value attach karo
    $subscription->features->transform(function ($feature) use ($planFeatures) {

        $planFeature = $planFeatures->get($feature->feature);

        return [
            'id' => $feature->id,
            'feature' => $feature->feature,
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

    public function assignFreePlanToAllUsers()
{
    // ✅ Free plan nikalo
    $freePlan = SubscriptionPlan::where('is_free', 1)->first();

    if (!$freePlan) {
        return response()->json([
            'status' => false,
            'message' => 'Free plan not found'
        ]);
    }

    // ✅ Plan ke features load karo
    $planFeatures = PlanFeature::where('plan_id', $freePlan->id)->get();

    // ✅ Un users ko nikalo jinke paas koi subscription nahi hai
    $users = User::whereDoesntHave('subscriptions')->get();

    $createdCount = 0;

    foreach ($users as $user) {

        // ✅ Subscription create karo
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $freePlan->id,
            'payment_status' => 'paid',
            'payment_method' => 'system',
            'start_date' => now(),
            'end_date' => now()->addMonth(), // 🔥 monthly free plan
            'is_active' => 1,
        ]);

        // ✅ Features assign karo
        foreach ($planFeatures as $feature) {
            UserSubscriptionFeature::create([
                'user_subscription_id' => $subscription->id,
                'feature' => $feature->feature,
                'limit_value' => $feature->value,
                'used_value' => 0,
                'unit' => $feature->unit,
                'reset_period' => 'monthly',
                'last_reset_date' => now(),
            ]);
        }

        $createdCount++;
    }

    return response()->json([
        'status' => true,
        'message' => 'Free plan assigned successfully',
        'total_users_updated' => $createdCount
    ]);
}
}