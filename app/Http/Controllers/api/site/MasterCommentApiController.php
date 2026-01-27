<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\MasterComment;
use App\Models\MasterCommentLike;
use App\Models\MasterPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterCommentApiController extends Controller
{
    public function createComments(Request $r)
    {
        $data = $r->validate([
            'post_id'   => 'required|exists:master_posts,id',
            'comment'   => 'required|string',
            'parent_id' => 'nullable|exists:master_comments,id'
        ]);
        $data['user_id'] = Auth::id();
        $post = MasterPost::find($data['post_id']);
        $post->increment('comments');
        $comment = MasterComment::create($data);
        return response()->json(['status'  => true, 'message' => 'Comment added successfully!', 'data'    => $comment]);
    }
    public function replyComment(Request $r)
    {
        $data = $r->validate(['post_id' => 'required|exists:master_posts,id', 'comment' => 'required|string', 'parent_id' => 'required|exists:master_comments,id']);
        $data['user_id'] = Auth::id();
        $reply = MasterComment::create($data);
        return response()->json(['status' => true, 'message' => 'Reply added successfully!', 'data' => $reply]);
    }
    public function likeOrUnlikeComment(Request $r)
    {
        $user_id = Auth::id();
        $data = $r->validate(['comment_id' => 'required|exists:master_comments,id']);
        $existing_like = MasterCommentLike::where('user_id', $user_id)->where('comment_id', $data['comment_id'])->first();
        if ($existing_like) {
            $existing_like->delete();
            return response()->json(['status' => true, 'message' => 'Comment unliked successfully!', 'liked' => false]);
        } else {
            MasterCommentLike::create(['user_id' => $user_id, 'comment_id' => $data['comment_id']]);
            return response()->json(['status' => true, 'message' => 'Comment liked successfully!', 'liked' => true]);
        }
    }
    public function getComments($post_id)
    {
        $comments = MasterComment::where('post_id', $post_id)->whereNull('parent_id')->with(['user', 'likes', 'replies'])->withCount('likes')->latest()->get();
        return response()->json(['status' => true, 'data' => $comments]);
    }
    public function deleteComments($c_id)
    {
        $comment = MasterComment::find($c_id);
        if (!$comment) {
            return response()->json(['status' => false, 'message' => 'Comment not found!'], 404);
        }
        MasterComment::where('parent_id', $comment->id)->delete();
        $comment->delete();
        return response()->json(['status' => true, 'message' => 'Comment deleted successfully!']);
    }
}
