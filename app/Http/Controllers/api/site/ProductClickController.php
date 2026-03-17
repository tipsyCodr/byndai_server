<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\ProductClick;
use Illuminate\Http\Request;

class ProductClickController extends Controller
{
    // ✅ STORE / UPDATE CLICK
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'product_id' => 'required|integer',
    //         'viewer_user_id' => 'required|integer',
    //         'profile_user_id' => 'required|integer',
    //     ]);

    //     $productId = $request->product_id;
    //     $viewerId = $request->viewer_user_id;
    //     $profileId = $request->profile_user_id;

    //     // 👉 Check existing row
    //     $click = ProductClick::where('product_id', $productId)
    //         ->where('viewer_id', $viewerId)
    //         ->where('profile_id', $profileId)
    //         ->first();

    //     if ($click) {
    //         // ✅ Row exists → increment
    //         $click->increment('click_count');
    //         $click->update([
    //             'last_clicked_at' => now(),
    //         ]);
    //     } else {
    //         // ✅ New row create
    //         $click = ProductClick::create([
    //             'product_id' => $productId,
    //             'viewer_id' => $viewerId,
    //             'profile_id' => $profileId,
    //             'click_count' => 1,
    //             'last_clicked_at' => now(),
    //         ]);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Click tracked successfully',
    //         'data' => $click,
    //     ]);
    // }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'viewer_user_id' => 'required|integer',
            'profile_user_id' => 'required|integer',
        ]);

        $productId = $request->product_id;
        $viewerId = $request->viewer_user_id;
        $profileId = $request->profile_user_id;

        // ✅ Find existing row INCLUDING product_id
        $click = ProductClick::where([
            'product_id' => $productId,
            'viewer_id' => $viewerId,
            'profile_id' => $profileId,
        ])->first();

        if ($click) {
            // ✅ Row exists → increment
            $click->increment('click_count');
            $click->update([
                'last_clicked_at' => now(),
            ]);
        } else {
            // ✅ New row create
            $click = ProductClick::create([
                'product_id' => $productId,
                'viewer_id' => $viewerId,
                'profile_id' => $profileId,
                'click_count' => 1,
                'last_clicked_at' => now(),
            ]);
        }

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

        // 👉 Optional filters.......
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
