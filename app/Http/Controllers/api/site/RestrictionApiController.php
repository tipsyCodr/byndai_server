<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Restriction;
use Illuminate\Http\Request;

class RestrictionApiController extends Controller
{
    public function getRestrictionData()
    {
        $restriction = Restriction::with('plan')->latest()->get();
        return response()->json(['status' => true, 'message' => 'Restrictions fetched successfully!', 'data' => $restriction]);
    }

    public function createRestriction(Request $r)
    {
        $data = $r->validate(['plan_id' => 'nullable|exists:plans', 'title' => 'required|string|max:255', 'slug' => 'required|string|max:255|unique:restrictions,slug', 'status' => 'required|in:0,1',]);
        $restriction = Restriction::create($data);
        return response()->json(['status' => true, 'message' => 'Restriction created successfully!', 'data' => $restriction]);
    }

    public function getRestriction($id)
    {
        $restriction = Restriction::with('plan')->find($id);
        if (!$restriction) {
            return response()->json(['status' => false, 'message' => 'Restriction not found!'], 404);
        }
        return response()->json(['status' => true, 'message' => 'Restriction fetched successfully!', 'data' => $restriction]);
    }

    public function updateRestriction(Request $r, $id)
    {
        $data = $r->validate(['plan_id' => 'nullable|exists:plans', 'title' => 'required|string|max:255', 'slug' => 'required|string|max:255|unique:restrictions,slug,' . $id, 'status' => 'required|in:0,1',]);
        $restriction = Restriction::findOrFail($id);
        $restriction->update($data);
        return response()->json(['status' => true, 'message' => 'Restriction updated successfully!', 'data' => $restriction]);
    }
}
