<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionPlanController extends Controller
{
    // ✅ Get All Plans
    public function index()
    {
        $plans = SubscriptionPlan::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $plans
        ]);
    }

    // ✅ Create Plan....
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'price' => 'nullable|numeric',
            'regular_price' => 'nullable|numeric',
        ]);

        $plan = SubscriptionPlan::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . time(),
            'description' => $request->description,
            'type' => $request->type,
            'is_free' => $request->is_free ?? false,
            'price' => $request->price ?? 0,
            'regular_price' => $request->regular_price ?? 0,
            'currency' => $request->currency ?? 'INR',
            'status' => $request->status ?? 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Plan created successfully',
            'data' => $plan
        ]);
    }

    // ✅ Get Single Plan
    public function show($id)
    {
        $plan = SubscriptionPlan::find($id);

        if (!$plan) {
            return response()->json([
                'status' => false,
                'message' => 'Plan not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $plan
        ]);
    }

    // ✅ Update Plan
    public function update(Request $request, $id)
    {
        $plan = SubscriptionPlan::find($id);

        if (!$plan) {
            return response()->json([
                'status' => false,
                'message' => 'Plan not found'
            ], 404);
        }

        $plan->update([
            'name' => $request->name ?? $plan->name,
            'description' => $request->description ?? $plan->description,
            'type' => $request->type ?? $plan->type,
            'is_free' => $request->is_free ?? $plan->is_free,
            'price' => $request->price ?? $plan->price,
            'regular_price' => $request->regular_price ?? $plan->regular_price,
            'currency' => $request->currency ?? $plan->currency,
            'status' => $request->status ?? $plan->status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Plan updated successfully',
            'data' => $plan
        ]);
    }

    // ✅ Delete Plan
    public function destroy($id)
    {
        $plan = SubscriptionPlan::find($id);

        if (!$plan) {
            return response()->json([
                'status' => false,
                'message' => 'Plan not found'
            ], 404);
        }

        $plan->delete();

        return response()->json([
            'status' => true,
            'message' => 'Plan deleted successfully'
        ]);
    }

    // ✅ Get Plan with Features
    public function getPlanWithFeatures($id)
    {
       $plan = SubscriptionPlan::with('features')->find($id);
   
       if (!$plan) {
           return response()->json([
               'status' => false,
               'message' => 'Plan not found'
           ], 404);
       }
   
       return response()->json([
           'status' => true,
           'data' => $plan
       ]);
    }
}