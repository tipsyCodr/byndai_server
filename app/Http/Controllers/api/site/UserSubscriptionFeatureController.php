<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserSubscriptionFeature;

class UserSubscriptionFeatureController extends Controller
{
    // ✅ Get all features
    public function index()
    {
        $data = UserSubscriptionFeature::with('subscription')->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    // ✅ Create feature
    public function store(Request $request)
    {
        $request->validate([
            'user_subscription_id' => 'required|exists:user_subscriptions,id',
            'feature' => 'required|string|max:255',
            
        ]);

        $feature = UserSubscriptionFeature::create([
            'user_subscription_id' => $request->user_subscription_id,
            'feature' => $request->feature,            
            'used_value' => $request->used_value ?? 0,
            'unit' => $request->unit,
            'reset_period' => $request->reset_period,
            'last_reset_date' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User feature created successfully',
            'data' => $feature
        ]);
    }

    // ✅ Get single feature
    public function show($id)
    {
        $feature = UserSubscriptionFeature::with('subscription')->find($id);

        if (!$feature) {
            return response()->json([
                'status' => false,
                'message' => 'Feature not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $feature
        ]);
    }

    // ✅ Update feature
    public function update(Request $request, $id)
    {
        $feature = UserSubscriptionFeature::find($id);

        if (!$feature) {
            return response()->json([
                'status' => false,
                'message' => 'Feature not found'
            ], 404);
        }

        $feature->update([            
            'used_value' => $request->used_value ?? $feature->used_value,
            'unit' => $request->unit ?? $feature->unit,
            'reset_period' => $request->reset_period ?? $feature->reset_period,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Feature updated successfully',
            'data' => $feature
        ]);
    }

    // ✅ Delete feature
    public function destroy($id)
    {
        $feature = UserSubscriptionFeature::find($id);

        if (!$feature) {
            return response()->json([
                'status' => false,
                'message' => 'Feature not found'
            ], 404);
        }

        $feature->delete();

        return response()->json([
            'status' => true,
            'message' => 'Feature deleted successfully'
        ]);
    }

    // 🔥 Get features by subscription
    public function getBySubscription($subscriptionId)
    {
        $features = UserSubscriptionFeature::where('user_subscription_id', $subscriptionId)->get();

        return response()->json([
            'status' => true,
            'data' => $features
        ]);
    }

    // 🔥 Increment usage (MOST IMPORTANT)
    public function incrementUsage($id)
{
    $feature = UserSubscriptionFeature::with('subscription')->find($id);

    if (!$feature) {
        return response()->json([
            'status' => false,
            'message' => 'Feature not found'
        ], 404);
    }

    // get plan feature limit
    $planFeature = PlanFeature::where('plan_id', $feature->subscription->plan_id)
        ->where('feature', $feature->feature)
        ->first();

    if (!$planFeature) {
        return response()->json([
            'status' => false,
            'message' => 'Plan feature not found'
        ]);
    }

    if ($feature->used_value >= $planFeature->value) {
        return response()->json([
            'status' => false,
            'message' => 'Limit exhausted'
        ]);
    }

    $feature->increment('used_value');

    return response()->json([
        'status' => true,
        'message' => 'Usage incremented',
        'data' => $feature
    ]);
}

    public function checkFeature(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'feature' => 'required|string'
    ]);

    // get active subscription
    $subscription = UserSubscription::where('user_id', $request->user_id)
        ->where('is_active', 1)
        ->latest()
        ->first();

    if (!$subscription) {
        return response()->json([
            'status' => false,
            'message' => 'No active subscription'
        ]);
    }

    // get plan feature (MASTER)
    $planFeature = PlanFeature::where('plan_id', $subscription->plan_id)
        ->where('feature', $request->feature)
        ->first();

    if (!$planFeature) {
        return response()->json([
            'status' => false,
            'message' => 'Feature not available in plan'
        ]);
    }

    // get user usage
    $userFeature = UserSubscriptionFeature::firstOrCreate(
        [
            'user_subscription_id' => $subscription->id,
            'feature' => $request->feature
        ],
        [
            'used_value' => 0
        ]
    );

    return response()->json([
        'status' => true,
        'data' => [
            'limit' => $planFeature->value,
            'used' => $userFeature->used_value,
            'remaining' => $planFeature->value - $userFeature->used_value
        ]
    ]);
}
}