<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\ProductClick;
use Illuminate\Http\Request;

class ProductClickController extends Controller
{
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

    public function getProfileProductClicks(Request $request)
    {
        $request->validate([
            'profile_user_id' => 'required|integer',
        ]);

        $profileId = $request->profile_user_id;

        $clicks = ProductClick::with([
            'product.productImage',   // product + images
            'viewerDetail',            // viewer details
        ])
            ->where('profile_id', $profileId)
            ->latest()
            ->get();

        $data = $clicks->map(function ($item) {

            return [
                'product_id' => $item->product_id,
                'click_count' => $item->click_count,
                'last_clicked_at' => $item->last_clicked_at,

                // 👉 Product Data
                'product_name' => $item->product->product_name ?? null,
                'product_price' => $item->product->product_price ?? null,
                'product_image' => optional($item->product->productImage->first())->image ?? null,

                // 👉 Viewer Data
                'viewer_id' => $item->viewer_id,
                'viewer_name' => optional($item->viewerDetail)->fname,
                'viewer_mobile' => optional($item->viewerDetail)->mobile_one,
                'viewer_profile_pic' => optional($item->viewerDetail)->profile_pic,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Profile Product Clicks fetched successfully',
            'data' => $data,
        ]);
    }
}
