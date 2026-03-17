<?php

namespace App\Http\Controllers\Api\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductClickController extends Controller
{
    // ✅ STORE / UPDATE CLICK
    public function store(Request $request)
    {
        $request->validate([
            'viewer_user_id' => 'required|integer',
            'profile_user_id' => 'required|integer',
        ]);

        $viewerId = $request->viewer_user_id;
        $profileId = $request->profile_user_id;

        // 👉 Find or Create
        $click = ProductClick::firstOrCreate([
            'viewer_id' => $viewerId,
            'profile_id' => $profileId,
        ]);

        // 👉 Increment count
        $click->increment('click_count');

        // 👉 Update last clicked time
        $click->update([
            'last_clicked_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Click tracked successfully',
            'data' => $click,
        ]);
    }

    // ✅ GET DATA
    public function index(Request $request)
    {
        $query = ProductClick::query();

        // 👉 Optional filters
        if ($request->viewer_user_id) {
            $query->where('viewer_id', $request->viewer_user_id);
        }

        if ($request->profile_user_id) {
            $query->where('profile_id', $request->profile_user_id);
        }

        $data = $query->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
}
