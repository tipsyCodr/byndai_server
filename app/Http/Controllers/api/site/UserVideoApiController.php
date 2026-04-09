<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\UserVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserVideoApiController extends Controller
{
    /**
     * Get all videos for a specific user
     */
    public function index($user_id)
    {
        try {
            $videos = UserVideo::where('user_id', $user_id)
                ->where('status', 1)
                ->latest()
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Videos fetched successfully',
                'data' => $videos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add a new video
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'video_url' => [
                    'required',
                    'string',
                    'url',
                    'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/'
                ],
                'status' => 'nullable|in:0,1'
            ], [
                'video_url.regex' => 'Please provide a valid YouTube or YouTube Shorts link.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            // --- QUOTA CHECK START ---
            $featureId = getFeatureId('product_short_videos');
            if (!$featureId) {
                return response()->json([
                    'status' => false,
                    'message' => 'Feature configuration error (product_short_videos not found)'
                ], 500);
            }

            $check = checkUserFeatureLimit($featureId);
            if (!$check['status']) {
                return response()->json([
                    'status' => false,
                    'message' => $check['message']
                ], 403);
            }

            if ($check['is_exceeded'] === true) {
                return response()->json([
                    'status' => false,
                    'message' => 'Your product video limit has been exceeded for this plan.',
                    'data' => $check
                ], 403);
            }
            // --- QUOTA CHECK END ---

            // Optional: Extract thumbnail or video ID logic could go here
            
            $video = UserVideo::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'video_url' => $request->video_url,
                'video_type' => 'youtube',
                'status' => $request->status ?? 1
            ]);

            // Increment usage
            incrementFeatureUsage($featureId);

            return response()->json([
                'status' => true,
                'message' => 'Video added successfully',
                'data' => $video
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a video
     */
    public function delete($id)
    {
        try {
            $user = Auth::user();

            $video = UserVideo::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$video) {
                return response()->json([
                    'status' => false,
                    'message' => 'Video not found or access denied'
                ], 404);
            }

            $video->delete();

            return response()->json([
                'status' => true,
                'message' => 'Video deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
