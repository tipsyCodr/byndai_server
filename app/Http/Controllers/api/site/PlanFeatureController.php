<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlanFeature;

class PlanFeatureController extends Controller
{
    // ✅ Get all features
    public function index()
    {
        $features = PlanFeature::with('plan')->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $features
        ]);
    }

    // ✅ Create feature
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'feature' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        $feature = PlanFeature::create([
            'plan_id' => $request->plan_id,
            'feature' => $request->feature,
            'value' => $request->value,
            'unit' => $request->unit
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Feature created successfully',
            'data' => $feature
        ]);
    }

    // ✅ Get single feature
    public function show($id)
    {
        $feature = PlanFeature::with('plan')->find($id);

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
        $feature = PlanFeature::find($id);

        if (!$feature) {
            return response()->json([
                'status' => false,
                'message' => 'Feature not found'
            ], 404);
        }

        $feature->update([
            'plan_id' => $request->plan_id ?? $feature->plan_id,
            'feature' => $request->feature ?? $feature->feature,
            'value' => $request->value ?? $feature->value,
            'unit' => $request->unit ?? $feature->unit,
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
        $feature = PlanFeature::find($id);

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
}