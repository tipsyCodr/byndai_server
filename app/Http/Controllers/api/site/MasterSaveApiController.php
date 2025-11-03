<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\MasterSave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterSaveApiController extends Controller
{
    public function saveOrUnsave(Request $r)
    {
        $user_id = Auth::id();
        $data = $r->validate(['post_id' => 'required|exists:posts,id']);
        $saved_post = MasterSave::where('user_id', $user_id)->where('post_id', $data['post_id'])->first();
        if ($saved_post) {
            $saved_post->delete();
            return response()->json(['status' => true, 'message' => 'Post deleted successfully!', 'saved' => false]);
        } else {
            MasterSave::create(['user_id' => $user_id, 'post_id' => $data['post_id']]);
            return response()->json(['status' => true, 'message' => 'Post saved successfully!', 'saved' => true]);
        }
    }
}
