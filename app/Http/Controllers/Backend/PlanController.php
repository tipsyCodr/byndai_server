<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Feature;
use App\Models\PlanFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = SubscriptionPlan::with('features.feature')->orderBy('price')->get();
        return view('backend.plans.index', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:monthly,yearly,lifetime',
            'price' => 'required|numeric|min:0',
            'is_free' => 'required|boolean',
            'status' => 'required|in:0,1',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name) . '-' . uniqid();

        SubscriptionPlan::create($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    /**
     * Show details and features of a plan
     */
    public function show($id)
    {
        $plan = SubscriptionPlan::with('features.feature')->findOrFail($id);
        $allFeatures = Feature::all();
        return view('backend.plans.show', compact('plan', 'allFeatures'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:monthly,yearly,lifetime',
            'price' => 'required|numeric|min:0',
            'is_free' => 'required|boolean',
            'status' => 'required|in:0,1',
        ]);

        $plan->update($request->all());

        return redirect()->route('admin.plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        
        // Check for active subscriptions before deleting?
        // For now, we'll allow it but warning in UI
        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    /**
     * Manage features for a plan
     */
    public function addFeature(Request $request, $id)
    {
        $request->validate([
            'feature_id' => 'required|exists:features,id',
            'value' => 'required|string',
            'unit' => 'nullable|string',
        ]);

        PlanFeature::updateOrCreate(
            ['plan_id' => $id, 'feature_id' => $request->feature_id],
            ['value' => $request->value, 'unit' => $request->unit]
        );

        return back()->with('success', 'Feature added/updated for this plan.');
    }

    public function removeFeature($id, $featureId)
    {
        PlanFeature::where('plan_id', $id)->where('feature_id', $featureId)->delete();
        return back()->with('success', 'Feature removed from plan.');
    }
}
