<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\MasterLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterLikeApiController extends Controller
{
    public function likeOrUnlike(Request $r)
    {
        $user_id = Auth::id();
        $data = $r->validate(['post_id' => 'required|exists:posts,id']);
        $like = MasterLike::where('user_id', $user_id)->where('post_id', $data['post_id'])->first();
        if ($like) {
            $like->delete();
            return response()->json(['status' => true, 'message' => 'Post unliked successfully!', 'liked' => false]);
        } else {
            MasterLike::create(['user_id' => $user_id, 'post_id' => $data['post_id']]);
            return response()->json(['status' => true, 'message' => 'Post liked successfully!', 'liked' => true]);
        }
    }
}
