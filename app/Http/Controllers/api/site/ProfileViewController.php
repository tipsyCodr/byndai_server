<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\ProfileView;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ProfileViewController extends Controller
{
    // जब profile open हो
    public function startView(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'viewer_user_id' => 'required|exists:users,id',
            'profile_user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first(),
            ], 422);
        }

        $viewerId = (int) $request->viewer_user_id;
        $profileId = (int) $request->profile_user_id;

        // खुद का profile view ना गिने
        if ($viewerId === $profileId) {
            return response()->json([
                "status" => true,
                "message" => "Self view ignored",
            ]);
        }

        $row = ProfileView::firstOrCreate(
            [
                'viewer_user_id' => $viewerId,
                'profile_user_id' => $profileId,
            ],
            [
                'view_count' => 0,
                'total_time_seconds' => 0,
                'last_viewed_at' => null,
            ]
        );

        // view count बढ़ाओ
        $row->view_count = $row->view_count + 1;
        $row->last_viewed_at = Carbon::now();
        $row->save();

        return response()->json([
            "status" => true,
            "message" => "Profile view started",
            "data" => $row,
        ]);
    }

    // जब profile बंद हो / back हो
    public function endView(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'viewer_user_id' => 'required|exists:users,id',
            'profile_user_id' => 'required|exists:users,id',
            'duration_seconds' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => $validator->errors()->first(),
            ], 422);
        }

        $viewerId = (int) $request->viewer_user_id;
        $profileId = (int) $request->profile_user_id;

        if ($viewerId === $profileId) {
            return response()->json([
                "status" => true,
                "message" => "Self view ignored",
            ]);
        }

        $duration = (int) $request->duration_seconds;

        $row = ProfileView::where('viewer_user_id', $viewerId)
            ->where('profile_user_id', $profileId)
            ->first();

        if (!$row) {
            return response()->json([
                "status" => false,
                "message" => "No view session found",
            ], 404);
        }

        $row->total_time_seconds = $row->total_time_seconds + $duration;
        $row->save();

        return response()->json([
            "status" => true,
            "message" => "Profile view ended",
            "data" => $row,
        ]);
    }
}
