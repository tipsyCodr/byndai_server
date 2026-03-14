<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\UserReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReviewApiController extends Controller
{
    /**
     * ✅ Add Review
     */
    public function store(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'nullable|string|max:1000',
        ]);

        $fromUserId = Auth::id();

        if (! $fromUserId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // ❌ User cannot review himself
        if ((int) $fromUserId === (int) $request->to_user_id) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot review yourself',
            ], 400);
        }

        // ❌ One user can review same user only once
        $already = UserReview::where('to_user_id', $request->to_user_id)
            ->where('from_user_id', $fromUserId)
            ->first();

        if ($already) {
            return response()->json([
                'status' => false,
                'message' => 'You already reviewed this user',
            ], 400);
        }

        $review = UserReview::create([
            'to_user_id' => $request->to_user_id,
            'from_user_id' => $fromUserId,
            'rating' => $request->rating,
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Review added successfully',
            'data' => $review,
        ], 200);
    }

    /**
     * ✅ Get Reviews of a User (to_user_id)
     */
    public function getUserReviews($userId)
    {
        $reviews = UserReview::with([
            'fromUser:id,user_name,email',
        ])
            ->where('to_user_id', $userId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'User reviews loaded',
            'data' => $reviews,
        ], 200);
    }

    /**
     * ✅ Get Average Rating of a User
     */
    public function getUserAverageRating($userId)
    {
        $avg = UserReview::where('to_user_id', $userId)->avg('rating');
        $count = UserReview::where('to_user_id', $userId)->count();

        return response()->json([
            'status' => true,
            'message' => 'User average rating loaded',
            'data' => [
                'user_id' => (int) $userId,
                'average_rating' => round($avg ?? 0, 1),
                'total_reviews' => $count,
            ],
        ], 200);
    }

    /**
     * ✅ My Given Reviews (Logged in user)
     */
    public function myGivenReviews()
    {
        $fromUserId = Auth::id();

        if (! $fromUserId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $reviews = UserReview::with([
            'toUser:id,user_name,email',
        ])
            ->where('from_user_id', $fromUserId)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'message' => 'My given reviews loaded',
            'data' => $reviews,
        ], 200);
    }

    /**
     * ✅ Delete Review (Only owner can delete)
     */
    public function delete($id)
    {
        $fromUserId = Auth::id();

        if (! $fromUserId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $review = UserReview::find($id);

        if (! $review) {
            return response()->json([
                'status' => false,
                'message' => 'Review not found',
            ], 404);
        }

        if ((int) $review->from_user_id !== (int) $fromUserId) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $review->delete();

        return response()->json([
            'status' => true,
            'message' => 'Review deleted successfully',
        ], 200);
    }

    public function findReview(Request $request)
    {
        $request->validate([
            'to_user_id' => 'required|integer',
            'from_user_id' => 'required|integer',
        ]);

        $review = UserReview::where('to_user_id', $request->to_user_id)
            ->where('from_user_id', $request->from_user_id)
            ->first();

        if ($review) {
            return response()->json([
                'status' => true,
                'message' => 'Review found',
                'data' => $review,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Review not found',
            'data' => null,
        ]);
    }
}
