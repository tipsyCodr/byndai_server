<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\UserSubscriptionFeature;
use App\Models\PlanFeature;
use Illuminate\Http\Request;

class SubscriptionManagementController extends Controller
{
    /**
     * Display all user subscriptions
     */
    public function index(Request $request)
    {
        $query = UserSubscription::with(['user', 'plan'])
            ->orderBy('created_at', 'desc');

        if ($request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('user_name', 'like', "%$search%")
                  ->orWhere('id', 'like', "%$search%");
            });
        }

        $subscriptions = $query->paginate(15);
        
        return view('backend.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show features usage for a specific subscription
     */
    public function show($id)
    {
        $subscription = UserSubscription::with(['user', 'plan', 'features.feature'])->findOrFail($id);
        
        // Get the plan's master features to know what the limits are
        $planFeatures = PlanFeature::where('plan_id', $subscription->plan_id)->get()->keyBy('feature_id');
        
        return view('backend.subscriptions.show', compact('subscription', 'planFeatures'));
    }

    /**
     * Update feature usage manually
     */
    public function updateUsage(Request $request, $id)
    {
        $request->validate([
            'feature_id' => 'required|exists:features,id',
            'used_value' => 'required|integer|min:0',
        ]);

        $usf = UserSubscriptionFeature::where('user_subscription_id', $id)
            ->where('feature_id', $request->feature_id)
            ->first();

        if (!$usf) {
            // Create if doesn't exist (though it should usually be synced on subscribe)
            UserSubscriptionFeature::create([
                'user_subscription_id' => $id,
                'feature_id' => $request->feature_id,
                'used_value' => $request->used_value,
            ]);
        } else {
            $usf->update([
                'used_value' => $request->used_value
            ]);
        }

        return back()->with('success', 'User usage updated successfully.');
    }
}
