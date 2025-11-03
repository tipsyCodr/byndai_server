<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\MasterPost;
use App\Models\MasterPostImage;
use App\Models\MasterSave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterPostApiController extends Controller
{
    public function showPost(Request $r)
    {
        $data_provider = MasterPost::getData($r);
        return response()->json(['status' => true, 'data' => $data_provider]);
    }
    public function showPostByIndustry($indus_type)
    {
        $data_provider = MasterPost::with(['industry', 'createdBy'])->where('indus_type', $indus_type)->orderBy('id', 'DESC')->get();
        return response()->json(['status' => true, 'data' => $data_provider]);
    }
    public function postImage(Request $r, $title, $post_id)
    {
        if ($r->hasFile('post_img')) {
            $files = $r->file('post_img');
            if (!is_array($files)) {
                $files = [$files];
            }
            foreach ($files as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = public_path('assets/uploads/post/' . $title . '/');
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $file->move($path, $fileName);
                $extension = strtolower($file->getClientOriginalExtension());
                $type = in_array($extension, ['mp4', 'mov', 'avi', 'mkv']) ? 1 : 0;
                MasterPostImage::create(['post_id' => $post_id, 'post_img' => $fileName, 'type' => $type]);
            }
        }
    }
    public function createPost(Request $r)
    {
        $user = Auth::user();
        $data = $r->validate(['indus_type' => 'required', 'title' => 'required|string|max:100', 'content' => 'nullable|string', 'slug' => 'nullable', 'post_img' => 'nullable', 'is_audience' => 'nullable', 'status' => 'nullable|integer|in:0,1', 'created_by' => 'nullable|exists:users,id']);
        $data['created_by'] = $user->id;
        $post = MasterPost::create($data);
        $post_id = $post->id;
        if ($r->hasFile('post_img')) {
            $this->postImage($r, $r->title, $post_id);
        }
        return response()->json(['message' => 'Post created successfully!', 'post_id' => $post_id]);
    }
    public function deletePost($pid)
    {
        $event = MasterPost::findOrFail($pid);
        MasterPostImage::where('post_id', $pid)->delete();
        $event->delete();
        return response()->json(['message' => 'Post deleted successfully!']);
    }
    public function toggleSavePost($post_id)
    {
        $user = Auth::user();
        $saved = MasterSave::where('user_id', $user->id)->where('post_id', $post_id)->first();
        if ($saved) {
            $saved->delete();
            return response()->json(['status' => true, 'message' => 'Post unsaved successfully!']);
        } else {
            $save = MasterSave::create(['user_id' => $user->id, 'post_id' => $post_id]);
            return response()->json(['status' => true, 'message' => 'Post saved successfully!', 'data' => $save]);
        }
    }
}
