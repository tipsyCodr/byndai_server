<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\UserSubscriptionFeature;
use App\Models\PlanFeature;
use App\Models\User;
use App\Models\SubscriptionPlan;
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

    /**
     * Update BNI and JCI badges for the user
     */
    public function updateBadges(Request $request, $id)
    {
        $subscription = UserSubscription::findOrFail($id);
        $user = $subscription->user;

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $userDetail = $user->masterUser;
        if (!$userDetail) {
            return back()->with('error', 'User details not found.');
        }

        $userDetail->update([
            'jci_member' => $request->has('jci_member') ? 1 : 0,
            'bni_member' => $request->has('bni_member') ? 1 : 0,
        ]);

        return back()->with('success', 'Badges updated successfully.');
    }

    /**
     * Show create subscription form
     */
    public function create()
    {
        $users = User::orderBy('user_name')->get();
        $plans = SubscriptionPlan::where('status', 1)->get();

        return view('backend.subscriptions.create', compact('users', 'plans'));
    }

    /**
     * Store new subscription
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:subscription_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_status' => 'required|in:pending,paid,failed',
            'payment_method' => 'required|in:online,offline',
            'payment_id' => 'nullable|string',
        ]);

        // Check if user already has active subscription
        $existing = UserSubscription::where('user_id', $request->user_id)
            ->where('is_active', true)
            ->where('end_date', '>', now())
            ->first();

        if ($existing) {
            return back()->with('error', 'User already has an active subscription. Please delete it first to create a new one.');
        }

        $subscription = UserSubscription::create([
            'user_id' => $request->user_id,
            'plan_id' => $request->plan_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'payment_id' => $request->payment_id,
            'is_active' => true,
        ]);

        // Sync features for the subscription
        $plan = SubscriptionPlan::find($request->plan_id);
        $planFeatures = PlanFeature::where('plan_id', $plan->id)->get();

        foreach ($planFeatures as $pf) {
            UserSubscriptionFeature::create([
                'user_subscription_id' => $subscription->id,
                'feature_id' => $pf->feature_id,
                'used_value' => 0,
            ]);
        }

        return redirect()->route('admin.subscriptions.show', $subscription->id)
            ->with('success', 'Subscription created successfully!');
    }

    /**
     * Show edit subscription form
     */
    public function edit($id)
    {
        $subscription = UserSubscription::with(['user', 'plan'])->findOrFail($id);
        $plans = SubscriptionPlan::where('status', 1)->get();

        return view('backend.subscriptions.edit', compact('subscription', 'plans'));
    }

    /**
     * Update subscription
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_status' => 'required|in:pending,paid,failed',
            'payment_method' => 'required|in:online,offline',
            'payment_id' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $subscription = UserSubscription::findOrFail($id);

        // If plan is being changed, sync features
        $planChanged = $subscription->plan_id != $request->plan_id;

        $subscription->update([
            'plan_id' => $request->plan_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'payment_id' => $request->payment_id,
            'is_active' => $request->is_active ? true : false,
        ]);

        // If plan changed, resync features
        if ($planChanged) {
            UserSubscriptionFeature::where('user_subscription_id', $id)->delete();

            $plan = SubscriptionPlan::find($request->plan_id);
            $planFeatures = PlanFeature::where('plan_id', $plan->id)->get();

            foreach ($planFeatures as $pf) {
                UserSubscriptionFeature::create([
                    'user_subscription_id' => $subscription->id,
                    'feature_id' => $pf->feature_id,
                    'used_value' => 0,
                ]);
            }
        }

        return redirect()->route('admin.subscriptions.show', $subscription->id)
            ->with('success', 'Subscription updated successfully!');
    }

    /**
     * Delete subscription
     */
    public function destroy($id)
    {
        $subscription = UserSubscription::findOrFail($id);
        $userName = $subscription->user->user_name ?? 'Unknown User';

        // Delete related features
        UserSubscriptionFeature::where('user_subscription_id', $id)->delete();

        // Delete the subscription
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', "Subscription for {$userName} deleted successfully!");
    }
}
