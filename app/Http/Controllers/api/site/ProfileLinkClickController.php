<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\ProfileLinkClick;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ProfileLinkClickController extends Controller
{
    public function click(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'viewer_id' => 'required|exists:users,id',
            'profile_id' => 'required|exists:users,id',
            'platform' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first(),
            ], 422);
        }

        $viewerId = (int) $request->viewer_id;
        $profileId = (int) $request->profile_id;
        $platform = strtolower(trim($request->platform));

        // self ignore.......
        if ($viewerId === $profileId) {
            return response()->json([
                "status" => true,
                "message" => "Self click ignored",
            ]);
        }

        // allowed platforms
        $allowed = [
            "facebook",
            "instagram",
            "twitter",            
            "linkedin",
            "youtube",
            "whatsapp",
            "telegram",
            "snapchat",
        ];

        if (!in_array($platform, $allowed)) {
            return response()->json([
                "status" => false,
                "message" => "Invalid platform",
            ], 422);
        }

        // normalize x/twitter
        if ($platform === "x") {
            $platform = "twitter";
        }

        $row = ProfileLinkClick::firstOrCreate(
            [
                'viewer_id' => $viewerId,
                'profile_id' => $profileId,
                'platform' => $platform,
            ],
            [
                'click_count' => 0,
                'last_clicked_at' => null,
            ]
        );

        $row->click_count = $row->click_count + 1;
        $row->last_clicked_at = Carbon::now();
        $row->save();

        return response()->json([
            "status" => true,
            "message" => "Link click tracked",
            "data" => $row,
        ]);
    }
}
