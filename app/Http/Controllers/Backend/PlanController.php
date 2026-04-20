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
        $allFeatures = Feature::orderBy('feature')->get();

        return view('backend.plans.index', compact('plans', 'allFeatures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'price' => 'required|numeric|min:0',
            'is_free' => 'required|boolean',
            'status' => 'required|in:0,1',
            'currency' => 'nullable|in:INR',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*.enabled' => 'nullable|boolean',
            'features.*.value' => 'nullable|string|max:100',
            'features.*.unit' => 'nullable|string|max:50',
        ]);

        $data = $request->only(['name', 'type', 'price', 'is_free', 'status', 'currency', 'description']);
        $data['currency'] = $data['currency'] ?: 'INR';
        $data['price'] = (bool) $data['is_free'] ? 0 : $data['price'];
        $data['slug'] = Str::slug($request->name) . '-' . uniqid();

        $plan = SubscriptionPlan::create($data);

        $this->syncFeatureQuotas($plan, $request->input('features', []));

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
            'type' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'price' => 'required|numeric|min:0',
            'is_free' => 'required|boolean',
            'status' => 'required|in:0,1',
            'currency' => 'nullable|in:INR',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
            'features.*.enabled' => 'nullable|boolean',
            'features.*.value' => 'nullable|string|max:100',
            'features.*.unit' => 'nullable|string|max:50',
        ]);

        $data = $request->only(['name', 'type', 'price', 'is_free', 'status', 'currency', 'description']);
        $data['currency'] = $data['currency'] ?: 'INR';
        $data['price'] = (bool) $data['is_free'] ? 0 : $data['price'];

        $plan->update($data);
        $this->syncFeatureQuotas($plan, $request->input('features', []));

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

    private function syncFeatureQuotas(SubscriptionPlan $plan, array $features): void
    {
        foreach ($features as $featureId => $featureData) {
            if (! Feature::whereKey($featureId)->exists()) {
                continue;
            }

            $enabled = (bool) ($featureData['enabled'] ?? false);
            $value = trim((string) ($featureData['value'] ?? ''));
            $unit = trim((string) ($featureData['unit'] ?? ''));

            if (! $enabled) {
                PlanFeature::where('plan_id', $plan->id)
                    ->where('feature_id', $featureId)
                    ->delete();
                continue;
            }

            PlanFeature::updateOrCreate(
                ['plan_id' => $plan->id, 'feature_id' => $featureId],
                [
                    'value' => $value !== '' ? $value : '-1',
                    'unit' => $unit !== '' ? $unit : null,
                ]
            );
        }
    }
}
