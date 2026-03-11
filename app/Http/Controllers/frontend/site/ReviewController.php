<?php

namespace App\Http\Controllers\frontend\site;

use App\Http\Controllers\Controller;
use App\Models\UserReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'rating'     => 'required|integer|min:1|max:5',
            'message'    => 'nullable|string|max:1000',
        ]);

        $fromUserId = auth()->id() ?? null;

        // Prevent duplicate review from same logged-in user
        if ($fromUserId) {
            $exists = UserReview::where('to_user_id', $request->to_user_id)
                ->where('from_user_id', $fromUserId)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already submitted a review.',
                ], 409);
            }
        }

        UserReview::create([
            'to_user_id'   => $request->to_user_id,
            'from_user_id' => $fromUserId,
            'rating'       => $request->rating,
            'message'      => $request->message,
        ]);

        $avg   = UserReview::where('to_user_id', $request->to_user_id)->avg('rating');
        $count = UserReview::where('to_user_id', $request->to_user_id)->count();

        return response()->json([
            'success'        => true,
            'message'        => 'Thank you for your review!',
            'average_rating' => round($avg, 1),
            'review_count'   => $count,
        ]);
    }
}
