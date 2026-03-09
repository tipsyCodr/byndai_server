<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\MasterIndustry;
use App\Models\MasterUserDetail;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostMedia;
use App\Models\PostReaction;
use App\Models\PostReport;
use App\Models\PostSave;
use App\Models\PostShare;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostApiController extends Controller
{
    // =========================
    // 1) CREATE POST (with media)
    // =========================

    public function createPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:5000',
            'industry_id' => 'required|integer',

            // separate images/videos
            'images' => 'nullable|array|max:5',
            'images.*' => 'file|mimes:jpg,jpeg,png,webp|max:10240', // 10MB each

            'videos' => 'nullable|array|max:2',
            'videos.*' => 'file|mimes:mp4,mov,avi,mkv,webm|max:51200', // 50MB each
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user(); // sanctum user
            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $userId = $user->id;

            DB::beginTransaction();

            // 1) create post
            $post = Post::create([
                'user_id' => $userId,
                'title' => $request->title ?? null,
                'description' => $request->description ?? null,
                'industry_id' => $request->industry_id,
                'status' => 'active',
            ]);

            $position = 1;

            // 2) store images
            if ($request->hasFile('images')) {

                $imageFiles = $request->file('images');

                if (count($imageFiles) > 5) {
                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => 'Maximum 5 images allowed',
                    ], 422);
                }

                foreach ($imageFiles as $file) {

                    $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

                    // save in storage/app/public/uploads/posts
                    $path = $file->storeAs('uploads/posts', $fileName, 'public');

                    PostMedia::create([
                        'post_id' => $post->id,
                        'media_type' => 'image',
                        'media_url' => '/storage/'.$path,
                        'thumbnail_url' => null,
                        'position' => $position++,
                    ]);
                }
            }

            // 3) store videos
            if ($request->hasFile('videos')) {

                $videoFiles = $request->file('videos');

                if (count($videoFiles) > 2) {
                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => 'Maximum 2 videos allowed',
                    ], 422);
                }

                foreach ($videoFiles as $file) {

                    $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();

                    $path = $file->storeAs('uploads/posts', $fileName, 'public');

                    PostMedia::create([
                        'post_id' => $post->id,
                        'media_type' => 'video',
                        'media_url' => '/storage/'.$path,
                        'thumbnail_url' => null,
                        'position' => $position++,
                    ]);
                }
            }

            DB::commit();

            // return full response
            return response()->json([
                'status' => true,
                'message' => 'Post created successfully',
                'data' => $this->postFullResponse($post->id, $userId),
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to create post',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 2) UPDATE POST
    // =========================
    // public function updatePost(Request $request, $post_id)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'title' => 'nullable|string|max:150',
    //         'description' => 'nullable|string|max:5000',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => $validator->errors()->first(),
    //         ], 422);
    //     }

    //     try {
    //         $userId = $request->user()->id;

    //         $post = Post::where('id', $post_id)
    //             ->where('user_id', $userId)
    //             ->first();

    //         if (! $post) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Post not found or unauthorized',
    //             ], 404);
    //         }

    //         $post->update([
    //             'title' => $request->title ?? $post->title,
    //             'description' => $request->description ?? $post->description,
    //         ]);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Post updated successfully',
    //             'data' => $this->postFullResponse($post_id, $userId),
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to update post: '.$e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function updatePost(Request $request, $post_id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:5000',
            'industry_id' => 'nullable|integer|exists:master_industries,id',

            // images (new files)
            'new_images' => 'nullable|array|max:5',
            'new_images.*' => 'file|mimes:jpg,jpeg,png,webp|max:10240', // 10MB each

            // IDs of existing images to keep
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'integer|exists:post_media,id,post_id,'.$post_id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId = $request->user()->id;

            // Find the post belonging to the user
            $post = Post::where('id', $post_id)
                ->where('user_id', $userId)
                ->first();

            if (! $post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found or unauthorized',
                ], 404);
            }

            DB::beginTransaction();

            // 1. Update basic post data
            $post->update([
                'title' => $request->title ?? $post->title,
                'description' => $request->description ?? $post->description,
                'industry_id' => $request->industry_id ?? $post->industry_id,
            ]);

            // 2. Handle existing media (images) – delete those not in the "keep" list
            $keepMediaIds = $request->input('existing_images', []);
            // Delete all media belonging to this post that are NOT in $keepMediaIds
            PostMedia::where('post_id', $post->id)
                ->whereNotIn('id', $keepMediaIds)
                ->delete();

            // 3. Handle new images
            $position = $post->media()->max('position') ?? 0; // start after highest position

            if ($request->hasFile('new_images')) {
                $newImages = $request->file('new_images');

                // Optional: enforce max count (including kept ones)
                $totalAfterAdd = count($keepMediaIds) + count($newImages);
                if ($totalAfterAdd > 5) {
                    DB::rollBack();

                    return response()->json([
                        'status' => false,
                        'message' => 'Maximum 5 images allowed in total',
                    ], 422);
                }

                foreach ($newImages as $file) {
                    $position++;

                    $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                    $path = $file->storeAs('uploads/posts', $fileName, 'public');

                    PostMedia::create([
                        'post_id' => $post->id,
                        'media_type' => 'image',
                        'media_url' => '/storage/'.$path,
                        'thumbnail_url' => null,
                        'position' => $position,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Post updated successfully',
                'data' => $this->postFullResponse($post->id, $userId),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to update post: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 3) DELETE POST
    // =========================
    // public function deletePost(Request $request, $post_id)
    // {
    //     try {
    //         $userId = $request->user()->id;

    //         $post = Post::where('id', $post_id)
    //             ->where('user_id', $userId)
    //             ->first();

    //         if (! $post) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Post not found or unauthorized',
    //             ], 404);
    //         }

    //         $post->update(['status' => 'deleted']);

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Post deleted successfully',
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to delete post: '.$e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function deletePost(Request $request, $post_id)
    {
        try {
            $userId = $request->user()->id;

            // Find the post belonging to the user
            $post = Post::where('id', $post_id)
                ->where('user_id', $userId)
                ->first();

            if (! $post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found or unauthorized',
                ], 404);
            }

            DB::beginTransaction();

            // 1. Delete media files from storage and media records
            foreach ($post->media as $media) {
                // Extract relative path from media_url
                // Example: "/storage/uploads/posts/filename.jpg" -> "uploads/posts/filename.jpg"
                $relativePath = null;
                if (! empty($media->media_url)) {
                    // Remove leading "/storage/" if present
                    if (str_starts_with($media->media_url, '/storage/')) {
                        $relativePath = substr($media->media_url, 9); // remove '/storage/'
                    } else {
                        $relativePath = $media->media_url; // assume already relative
                    }

                    // Delete the file if it exists
                    if (Storage::disk('public')->exists($relativePath)) {
                        Storage::disk('public')->delete($relativePath);
                    }
                }

                // Delete thumbnail if exists
                if (! empty($media->thumbnail_url)) {
                    $thumbPath = null;
                    if (str_starts_with($media->thumbnail_url, '/storage/')) {
                        $thumbPath = substr($media->thumbnail_url, 9);
                    } else {
                        $thumbPath = $media->thumbnail_url;
                    }
                    if (Storage::disk('public')->exists($thumbPath)) {
                        Storage::disk('public')->delete($thumbPath);
                    }
                }

                // Delete the media record
                $media->delete();
            }

            // 2. Delete all reactions
            $post->reactions()->delete();

            // 3. Delete all comments (including replies – they all belong to the post)
            $post->comments()->delete();

            // 4. Delete all saves
            $post->saves()->delete();

            // 5. Delete all views
            $post->views()->delete();

            // 6. Delete all reports
            $post->reports()->delete();

            // 7. Finally delete the post itself
            $post->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Post and all associated data deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete post: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 4) FEED LIST (pagination)
    // =========================
    public function feed(Request $request)
    {
        try {
            $userId = $request->user()->id;

            $posts = Post::where('status', 'active')
                ->orderBy('id', 'desc')
                ->paginate(10);

            $data = [];

            foreach ($posts->items() as $post) {
                $data[] = $this->postFullResponse($post->id, $userId);
            }

            return response()->json([
                'status' => true,
                'message' => 'Feed fetched successfully',
                'data' => $data,
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'next_page_url' => $posts->nextPageUrl(),
                    'prev_page_url' => $posts->previousPageUrl(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch feed: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 5) SINGLE POST DETAIL
    // =========================
    public function postDetail(Request $request, $post_id)
    {
        try {
            $userId = $request->user()->id;

            $post = Post::where('id', $post_id)
                ->where('status', 'active')
                ->first();

            if (! $post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Post detail fetched successfully',
                'data' => $this->postFullResponse($post_id, $userId),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch post detail: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 6) REACT (LIKE/DISLIKE)
    // =========================

    public function reactPost(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer',
            'reaction_type' => 'required|in:like,dislike,none',
            'reaction' => 'required|in:like,dislike,none',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId = $request->user()->id;
            $postId = $request->post_id;

            // Check if post exists and is active
            $post = Post::where('id', $postId)
                ->where('status', 'active')
                ->first();

            if (! $post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            // Remove reaction if type is none
            if ($request->reaction_type === 'none') {
                PostReaction::where('post_id', $postId)
                    ->where('user_id', $userId)
                    ->delete();

                $this->updatePostCounts($postId);

                return response()->json([
                    'status' => true,
                    'message' => 'Reaction removed',
                    'data' => $this->postFullResponse($postId, $userId),
                ], 200);
            }

            // Update or create reaction correctly
            PostReaction::updateOrCreate(
                ['post_id' => $postId, 'user_id' => $userId], // Find record
                [
                    'reaction_type' => $request->reaction_type, // Update/Insert values
                    'reaction' => $request->reaction,
                ]
            );

            $this->updatePostCounts($postId);

            return response()->json([
                'status' => true,
                'message' => 'Reaction updated',
                'data' => $this->postFullResponse($postId, $userId),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to react: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 7) ADD COMMENT / REPLY
    // =========================

    public function addComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer',
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId = $request->user()->id;

            $post = Post::where('id', $request->post_id)
                ->where('status', 'active')
                ->first();

            if (! $post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            // Reply validation
            if ($request->parent_id) {
                $parent = PostComment::where('id', $request->parent_id)
                    ->where('post_id', $request->post_id)
                    ->first();

                if (! $parent) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Parent comment not found',
                    ], 404);
                }
            }

            $newComment = PostComment::create([
                'post_id' => $request->post_id,
                'user_id' => $userId,
                'comment' => $request->comment,
                'parent_id' => $request->parent_id ?? null,
            ]);

            $this->updatePostCounts($request->post_id);

            // ✅ load user + fname
            $newComment = PostComment::with([
                'user:id,user_name',
                'user.masterUser:emp_id,user_id,fname',
            ])->find($newComment->id);

            return response()->json([
                'status' => true,
                'message' => $request->parent_id ? 'Reply added successfully' : 'Comment added successfully',
                'data' => $newComment,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to add comment: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 8) DELETE COMMENT
    // =========================
    public function deleteComment(Request $request, $comment_id)
    {
        try {
            $userId = $request->user()->id;

            $comment = PostComment::where('id', $comment_id)
                ->where('user_id', $userId)
                ->first();

            if (! $comment) {
                return response()->json([
                    'status' => false,
                    'message' => 'Comment not found or unauthorized',
                ], 404);
            }

            $comment->delete();

            $this->updatePostCounts($postId);

            return response()->json([
                'status' => true,
                'message' => 'Comment deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete comment: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 9) SAVE / UNSAVE
    // =========================
    public function toggleSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId = $request->user()->id;
            $postId = $request->post_id;

            $post = Post::where('id', $postId)
                ->where('status', 'active')
                ->first();

            if (! $post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            $exists = PostSave::where('post_id', $postId)
                ->where('user_id', $userId)
                ->first();

            if ($exists) {
                $exists->delete();
                $msg = 'Post unsaved';
            } else {
                PostSave::create([
                    'post_id' => $postId,
                    'user_id' => $userId,
                ]);
                $msg = 'Post saved';
            }

            $this->updatePostCounts($postId);

            return response()->json([
                'status' => true,
                'message' => $msg,
                'data' => $this->postFullResponse($postId, $userId),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to save post: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 10) SHARE POST
    // =========================
    public function sharePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer',
            'share_type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId = $request->user()->id;

            PostShare::create([
                'post_id' => $request->post_id,
                'user_id' => $userId,
                'share_type' => $request->share_type ?? 'internal',
            ]);

            $this->updatePostCounts($request->post_id);

            return response()->json([
                'status' => true,
                'message' => 'Post shared',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to share: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 11) VIEW POST
    // =========================
    public function viewPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId = $request->user()->id;
            $postId = $request->post_id;

            $ip = $request->ip();

            // prevent spam view: per user per post 1 record
            $exists = PostView::where('post_id', $postId)
                ->where('user_id', $userId)
                ->first();

            if (! $exists) {
                PostView::create([
                    'post_id' => $postId,
                    'user_id' => $userId,
                    'ip_address' => $ip,
                ]);

                $this->updatePostCounts($postId);
            }

            return response()->json([
                'status' => true,
                'message' => 'View counted',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to count view: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 12) REPORT POST
    // =========================
    public function reportPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer',
            'reason' => 'required|string|max:150',
            'message' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId = $request->user()->id;

            PostReport::create([
                'post_id' => $request->post_id,
                'user_id' => $userId,
                'reason' => $request->reason,
                'message' => $request->message ?? null,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Post reported successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to report: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 13) User Active Status Post List
    // =========================

    // public function userPosts(Request $request, $user_id)
    // {
    //     try {
    //         $viewer = $request->user();
    //         $viewerUserId = $viewer->id;

    //         $posts = Post::with([
    //             'user.masterUser',
    //             'media',
    //             'industry',
    //         ])
    //             ->where('user_id', $user_id)
    //             ->where('status', 'active')
    //             ->orderBy('id', 'desc')
    //             ->paginate(10);

    //         $formattedPosts = $posts->getCollection()->map(function ($post) use ($viewerUserId) {
    //             return [
    //                 'id' => $post->id,
    //                 'user_id' => $post->user_id,

    //                 'user' => [
    //                     'user_name' => $post->user->user_name ?? null,
    //                     'fname' => $post->user->masterUser->fname ?? null,
    //                     'profile_pic' => $post->user->masterUser->profile_pic ?? null,
    //                 ],

    //                 'title' => $post->title,
    //                 'description' => $post->description,

    //                 'industry_id' => $post->industry_id,
    //                 'industry_title' => $post->industry->title ?? null,
    //                 'industry_icon' => $post->industry ? $post->industry->icons : null,

    //                 'status' => $post->status,
    //                 'created_at' => $post->created_at,
    //                 'updated_at' => $post->updated_at,

    //                 'media' => $post->media,

    //                 'counts' => [
    //                     'likes' => $post->reactions()->where('reaction_type', 'like')->count(),
    //                     'dislikes' => $post->reactions()->where('reaction_type', 'dislike')->count(),
    //                     'comments' => $post->comments()->count(),
    //                     'saves' => $post->saves()->count(),
    //                     'shares' => $post->shares()->count(),
    //                     'views' => $post->views()->count(),
    //                 ],

    //                 'my' => [
    //                     'reaction' => optional(
    //                         $post->reactions()
    //                             ->where('user_id', $viewerUserId)
    //                             ->first()
    //                     )->type ?? 'none',

    //                     'saved' => $post->saves()
    //                         ->where('user_id', $viewerUserId)
    //                         ->exists(),
    //                 ],
    //             ];
    //         });

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'User posts fetched successfully',
    //             'data' => $formattedPosts,
    //             'pagination' => [
    //                 'current_page' => $posts->currentPage(),
    //                 'last_page' => $posts->lastPage(),
    //                 'per_page' => $posts->perPage(),
    //                 'total' => $posts->total(),
    //                 'next_page_url' => $posts->nextPageUrl(),
    //                 'prev_page_url' => $posts->previousPageUrl(),
    //             ],
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to fetch user posts: '.$e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function userPosts(Request $request, $user_id)
    {
        try {
            $viewer = $request->user();
            $viewerUserId = $viewer->id;

            $search = $request->query('search');
            $industryId = $request->query('industry_id');
            $perPage = $request->query('per_page', 10);

            $query = Post::with([
                'user.masterUser',
                'media',
                'industry',
            ])
                ->where('user_id', $user_id)
                ->where('status', 'active');

            // ================= INDUSTRY FILTER =================
            if (! empty($industryId)) {
                $query->where('industry_id', $industryId);
            }

            // ================= SEARCH FILTER =================
            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhereHas('industry', function ($iq) use ($search) {
                            $iq->where('title', 'LIKE', "%{$search}%");
                        });
                });
            }

            $posts = $query->latest()->paginate($perPage);

            $formattedPosts = $posts->getCollection()->map(function ($post) use ($viewerUserId) {

                return [
                    'id' => $post->id,
                    'user_id' => $post->user_id,

                    'user' => [
                        'user_name' => $post->user->user_name ?? null,
                        'fname' => $post->user->masterUser->fname ?? null,
                        'profile_pic' => $post->user->masterUser->profile_pic ?? null,
                    ],

                    'title' => $post->title,
                    'description' => $post->description,

                    'industry_id' => $post->industry_id,
                    'industry_title' => $post->industry->title ?? null,
                    'industry_icon' => $post->industry ? $post->industry->icons : null,

                    'status' => $post->status,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,

                    'media' => $post->media,

                    'counts' => [
                        'likes' => $post->reactions()->where('reaction_type', 'like')->count(),
                        'dislikes' => $post->reactions()->where('reaction_type', 'dislike')->count(),
                        'comments' => $post->comments()->count(),
                        'saves' => $post->saves()->count(),
                        'shares' => $post->shares()->count(),
                        'views' => $post->views()->count(),
                    ],

                    'my' => [
                        'reaction' => optional(
                            $post->reactions()
                                ->where('user_id', $viewerUserId)
                                ->first()
                        )->reaction_type ?? 'none',

                        'saved' => $post->saves()
                            ->where('user_id', $viewerUserId)
                            ->exists(),
                    ],
                ];
            });

            return response()->json([
                'status' => true,
                'message' => 'User posts fetched successfully',
                'data' => $formattedPosts,
                'pagination' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'next_page_url' => $posts->nextPageUrl(),
                    'prev_page_url' => $posts->previousPageUrl(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch user posts: '.$e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 13)  Particular User Post List
    // =========================

    // public function listAllPosts(Request $request)
    // {
    //     try {
    //         $viewerUserId = $request->user()->id;

    //         $posts = Post::with([
    //             'media',
    //             'user',
    //             'userDetail',
    //             'industry',
    //         ])
    //             ->where('status', 'active')
    //             ->orderBy('id', 'desc')
    //             ->paginate(10);

    //         $data = [];

    //         foreach ($posts->items() as $post) {

    //             // counts
    //             $likeCount = PostReaction::where('post_id', $post->id)->where('reaction_type', 'like')->count();
    //             $dislikeCount = PostReaction::where('post_id', $post->id)->where('reaction_type', 'dislike')->count();
    //             $commentCount = PostComment::where('post_id', $post->id)->count();
    //             $saveCount = PostSave::where('post_id', $post->id)->count();
    //             $shareCount = PostShare::where('post_id', $post->id)->count();
    //             $viewCount = PostView::where('post_id', $post->id)->count();

    //             // my states
    //             $myReaction = PostReaction::where('post_id', $post->id)
    //                 ->where('user_id', $viewerUserId)
    //                 ->value('reaction_type');

    //             $isSaved = PostSave::where('post_id', $post->id)
    //                 ->where('user_id', $viewerUserId)
    //                 ->exists();

    //             $data[] = [
    //                 'id' => $post->id,
    //                 'user_id' => $post->user_id,

    //                 // user info
    //                 'user' => [
    //                     'user_name' => $post->user ? $post->user->user_name : null,
    //                     'fname' => $post->userDetail ? $post->userDetail->fname : null,
    //                     'profile_pic' => $post->userDetail ? $post->userDetail->profile_pic : null,
    //                 ],

    //                 'title' => $post->title,
    //                 'description' => $post->description,
    //                 'industry_id' => $post->industry_id,
    //                 'industry_title' => $post->industry ? $post->industry->title : null,
    //                 'industry_icon' => $post->industry ? $post->industry->icons : null,
    //                 'status' => $post->status,
    //                 'created_at' => $post->created_at,
    //                 'updated_at' => $post->updated_at,

    //                 // media (images/videos links)
    //                 'media' => $post->media,

    //                 'counts' => [
    //                     'likes' => $likeCount,
    //                     'dislikes' => $dislikeCount,
    //                     'comments' => $commentCount,
    //                     'saves' => $saveCount,
    //                     'shares' => $shareCount,
    //                     'views' => $viewCount,
    //                 ],

    //                 'my' => [
    //                     'reaction' => $myReaction ?? 'none',
    //                     'saved' => $isSaved,
    //                 ],
    //             ];
    //         }

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'All posts fetched successfully',
    //             'data' => $data,
    //             'pagination' => [
    //                 'current_page' => $posts->currentPage(),
    //                 'last_page' => $posts->lastPage(),
    //                 'per_page' => $posts->perPage(),
    //                 'total' => $posts->total(),
    //                 'next_page_url' => $posts->nextPageUrl(),
    //                 'prev_page_url' => $posts->previousPageUrl(),
    //             ],
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to fetch posts: ',
    //         ], 500);
    //     }
    // }

    public function listAllPosts(Request $request)
    {
        $user = auth()->user();

        $search = $request->query('search');
        $industryId = $request->query('industry_id');
        $perPage = $request->query('per_page', 10);

        $query = Post::with([
            'user.masterUser',
            'media',
            'industry',
        ])
            ->where('status', 'active');

        // ================= INDUSTRY FILTER =================
        if (! empty($industryId)) {
            $query->where('industry_id', $industryId);
        }

        // ================= SEARCH FILTER =================
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('user_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user.masterUser', function ($mq) use ($search) {
                        $mq->where('fname', 'LIKE', "%{$search}%");
                    });
            });
        }

        $posts = $query->latest()->paginate($perPage);

        $formattedPosts = $posts->getCollection()->map(function ($post) use ($user) {

            return [
                'id' => $post->id,
                'user_id' => $post->user_id,

                'user' => [
                    'user_name' => $post->user->user_name ?? null,
                    'fname' => $post->user->masterUser->fname ?? null,
                    'profile_pic' => $post->user->masterUser->profile_pic ?? null,
                ],

                'title' => $post->title,
                'description' => $post->description,

                'industry_id' => $post->industry_id,
                'industry_title' => $post->industry->title ?? null,
                'industry_icon' => $post->industry ? $post->industry->icons : null,

                'status' => $post->status,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,

                'media' => $post->media,

                'counts' => [
                    'likes' => $post->reactions()->where('reaction_type', 'like')->count(),
                    'dislikes' => $post->reactions()->where('reaction_type', 'dislike')->count(),
                    'comments' => $post->comments()->count(),
                    'saves' => $post->saves()->count(),
                    'shares' => $post->shares()->count(),
                    'views' => $post->views()->count(),
                ],

                'my' => [
                    'reaction' => optional(
                        $post->reactions()
                            ->where('user_id', $user->id)
                            ->first()
                    )->type ?? 'none',

                    'saved' => $post->saves()
                        ->where('user_id', $user->id)
                        ->exists(),
                ],
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'All posts fetched successfully',
            'data' => $formattedPosts,
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'next_page_url' => $posts->nextPageUrl(),
                'prev_page_url' => $posts->previousPageUrl(),
            ],
        ]);
    }

    // =========================
    // 14) All User Post List second
    // =========================
    public function allPostList()
    {
        return response()->json(['hit' => 'i am here']);
    }

    // =========================
    // 14)  Post Comment List
    // =========================

    public function getPostComments($postId, Request $request)
    {
        try {

            $post = Post::where('id', $postId)
                ->where('status', 'active')
                ->first();

            if (! $post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            $comments = PostComment::with([
                'user:id,user_name',
                'user.masterUser:emp_id,user_id,fname',
            ])
                ->where('post_id', $postId)
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($c) {

                    return [
                        'id' => $c->id,
                        'post_id' => $c->post_id,
                        'user_id' => $c->user_id,
                        'comment' => $c->comment,
                        'parent_id' => $c->parent_id,

                        'created_at' => $c->created_at,
                        'created_at_human' => $c->created_at
                            ? $c->created_at->diffForHumans()
                            : null,

                        'user' => [
                            'id' => $c->user?->id,
                            'user_name' => $c->user?->user_name,
                            'fname' => $c->user?->masterUser?->fname,
                        ],
                    ];
                });

            return response()->json([
                'status' => true,
                'message' => 'Post comments fetched successfully',
                'data' => $comments,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch comments: '.$e->getMessage(),
            ], 500);
        }
    }

    // ===================================
    // Get industries data
    // ===================================

    public function getIndustriesData()
    {
        try {
            $industries = MasterIndustry::orderBy('id', 'desc')->get();

            return response()->json([
                'status' => true,
                'message' => 'Industries fetched successfully',
                'data' => $industries,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // ================================================
    // ocr gpt img
    // ================================================

    //     public function gptOcr(Request $request)
    //     {
    //         $request->validate([
    //             'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
    //         ]);

    //         // ✅ Image to base64
    //         $imageFile = $request->file('image');
    //         $imageData = base64_encode(file_get_contents($imageFile->getRealPath()));
    //         $mimeType = $imageFile->getMimeType();

    //         // ✅ OpenAI API Key (ENV)
    //         $apiKey = env('OPENAI_API_SECRET_KEY');

    //         if (! $apiKey) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'OPENAI_API_SECRET_KEY missing in .env',
    //             ], 500);
    //         }

    //         // ✅ GPT Prompt
    //         $systemPrompt = '
    // You are an OCR + Business Card Data Extraction assistant.
    // Extract details from the image and return ONLY valid JSON.
    // Do not add explanation, do not add markdown, do not add extra text.

    // Return JSON with EXACT keys:
    // name, company_name, email, email2, website, address,
    // mobile_no_1, mobile_no_2, mobile_no_3

    // Rules:
    // - If any field is not found, return null.
    // - Only return ONE JSON object.
    // - For phone numbers: return only digits (remove +91, spaces, dashes).
    // - Website: return without http/https if possible.
    // - If there are multiple emails, put first in email, second in email2.
    // - If there are more than 3 phone numbers, keep first 3 only.
    // ';

    //         $response = Http::withToken($apiKey)
    //             ->timeout(90)
    //             ->post('https://api.openai.com/v1/chat/completions', [
    //                 'model' => 'gpt-4o-mini',
    //                 'messages' => [
    //                     [
    //                         'role' => 'system',
    //                         'content' => $systemPrompt,
    //                     ],
    //                     [
    //                         'role' => 'user',
    //                         'content' => [
    //                             [
    //                                 'type' => 'text',
    //                                 'text' => 'Extract business card details from this image and return JSON only.',
    //                             ],
    //                             [
    //                                 'type' => 'image_url',
    //                                 'image_url' => [
    //                                     'url' => "data:$mimeType;base64,$imageData",
    //                                 ],
    //                             ],
    //                         ],
    //                     ],
    //                 ],
    //                 'temperature' => 0,
    //             ]);

    //         if ($response->failed()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'OpenAI API Error',
    //                 'error' => $response->json(),
    //             ], 500);
    //         }

    //         $result = $response->json();
    //         $content = $result['choices'][0]['message']['content'] ?? '';

    //         // ✅ JSON decode
    //         $decoded = json_decode($content, true);

    //         // Agar GPT ne JSON sahi na diya
    //         if (! $decoded || ! is_array($decoded)) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Invalid JSON returned by GPT',
    //                 'raw_text' => $content,
    //                 'raw' => $result,
    //             ], 500);
    //         }

    //         // ✅ Fix missing keys (always return all)
    //         $final = [
    //             'id' => (string) rand(1, 999999), // optional id generate
    //             'name' => $decoded['name'] ?? null,
    //             'company_name' => $decoded['company_name'] ?? null,
    //             'email' => $decoded['email'] ?? null,
    //             'email2' => $decoded['email2'] ?? null,
    //             'website' => $decoded['website'] ?? null,
    //             'address' => $decoded['address'] ?? null,
    //             'mobile_no_1' => $decoded['mobile_no_1'] ?? null,
    //             'mobile_no_2' => $decoded['mobile_no_2'] ?? null,
    //             'mobile_no_3' => $decoded['mobile_no_3'] ?? null,
    //         ];

    //         return response()->json([
    //             'success' => true,
    //             'data' => $final,
    //             'raw' => $result, // optional debug (remove in production)

    //         ]);
    //     }

    public function gptOcr(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',

            // ✅ New optional fields (frontend se aa sakte)
            'user_id' => 'nullable|integer',
            'category' => 'nullable|string|max:255',

            // Flutter base64 images
            'selfy_images' => 'nullable|array',
            'selfy_images.*' => 'nullable|string',

            // Flutter base64 video
            'selfy_video' => 'nullable|string',
            'selfy_video_filename' => 'nullable|string|max:255',
        ]);

        // ✅ Image to base64 (business card image)
        $imageFile = $request->file('image');
        $imageData = base64_encode(file_get_contents($imageFile->getRealPath()));
        $mimeType = $imageFile->getMimeType();

        // ✅ OpenAI API Key (ENV)
        $apiKey = env('OPENAI_API_SECRET_KEY');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'OPENAI_API_SECRET_KEY missing in .env',
            ], 500);
        }

        // ✅ GPT Prompt
        $systemPrompt = '
You are an OCR + Business Card Data Extraction assistant.
Extract details from the image and return ONLY valid JSON.
Do not add explanation, do not add markdown, do not add extra text.

Return JSON with EXACT keys:
name, company_name, email, email2, website, address,
mobile_no_1, mobile_no_2, mobile_no_3

Rules:
- If any field is not found, return null.
- Only return ONE JSON object.
- For phone numbers: return only digits (remove +91, spaces, dashes).
- Website: return without http/https if possible.
- If there are multiple emails, put first in email, second in email2.
- If there are more than 3 phone numbers, keep first 3 only.
';

        $response = Http::withToken($apiKey)
            ->timeout(90)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Extract business card details from this image and return JSON only.',
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:$mimeType;base64,$imageData",
                                ],
                            ],
                        ],
                    ],
                ],
                'temperature' => 0,
            ]);

        if ($response->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'OpenAI API Error',
                'error' => $response->json(),
            ], 500);
        }

        $result = $response->json();
        $content = $result['choices'][0]['message']['content'] ?? '';

        // ✅ JSON decode
        $decoded = json_decode($content, true);

        // Agar GPT ne JSON sahi na diya
        if (! $decoded || ! is_array($decoded)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid JSON returned by GPT',
                'raw_text' => $content,
                'raw' => $result,
            ], 500);
        }

        // ✅ Fix missing keys (always return all)
        $final = [
            'id' => (string) rand(1, 999999),
            'name' => $decoded['name'] ?? null,
            'company_name' => $decoded['company_name'] ?? null,
            'email' => $decoded['email'] ?? null,
            'email2' => $decoded['email2'] ?? null,
            'website' => $decoded['website'] ?? null,
            'address' => $decoded['address'] ?? null,
            'mobile_no_1' => $decoded['mobile_no_1'] ?? null,
            'mobile_no_2' => $decoded['mobile_no_2'] ?? null,
            'mobile_no_3' => $decoded['mobile_no_3'] ?? null,
        ];

        // ============================================================
        // ✅ NEW PART: Save Lead with user_id, category, selfy images/video
        // ============================================================

        try {
            // 🔥 You must import Lead model:
            // use App\Models\Lead;

            $lead = new \App\Models\Lead;

            // OCR extracted data
            $lead->name = $final['name'];
            $lead->company_name = $final['company_name'];
            $lead->email = $final['email'];
            $lead->email2 = $final['email2'];
            $lead->website = $final['website'];
            $lead->address = $final['address'];
            $lead->mobile_no_1 = $final['mobile_no_1'];
            $lead->mobile_no_2 = $final['mobile_no_2'];
            $lead->mobile_no_3 = $final['mobile_no_3'];

            // ✅ New fields
            $lead->user_id = $request->user_id ?? null;
            $lead->category = $request->category ?? null;

            // ✅ Folder path (same as lead_image folder)
            $folderPath = public_path('uploads/leads');

            if (! file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            // =========================
            // ✅ Save selfy images
            // =========================
            $selfyImages = $request->selfy_images ?? [];

            // helper function
            $saveBase64Image = function ($base64, $prefix) use ($folderPath) {
                if (! $base64) {
                    return null;
                }

                // remove data:image/...;base64,
                if (str_contains($base64, 'base64,')) {
                    $base64 = explode('base64,', $base64)[1];
                }

                $imageData = base64_decode($base64);

                if (! $imageData) {
                    return null;
                }

                $fileName = $prefix.'_'.time().'_'.rand(1000, 9999).'.jpg';
                file_put_contents($folderPath.'/'.$fileName, $imageData);

                return 'uploads/leads/'.$fileName;
            };

            $lead->selfy_image_1 = isset($selfyImages[0])
                ? $saveBase64Image($selfyImages[0], 'selfy_1')
                : null;

            $lead->selfy_image_2 = isset($selfyImages[1])
                ? $saveBase64Image($selfyImages[1], 'selfy_2')
                : null;

            // =========================
            // ✅ Save selfy video
            // =========================
            $selfyVideo = $request->selfy_video;

            if ($selfyVideo) {
                if (str_contains($selfyVideo, 'base64,')) {
                    $selfyVideo = explode('base64,', $selfyVideo)[1];
                }

                $videoData = base64_decode($selfyVideo);

                if ($videoData) {
                    $videoFileName = $request->selfy_video_filename
                        ?? ('selfy_video_'.time().'_'.rand(1000, 9999).'.mp4');

                    file_put_contents($folderPath.'/'.$videoFileName, $videoData);

                    $lead->selfy_video = 'uploads/leads/'.$videoFileName;
                }
            }

            // ✅ Save lead
            $lead->save();

            return response()->json([
                'success' => true,
                'message' => 'OCR Done + Lead Saved Successfully',
                'data' => $final,
                'lead_saved' => true,
                'lead_id' => $lead->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'OCR Done BUT lead save failed',
                'data' => $final,
                'lead_saved' => false,
                'save_error' => $e->getMessage(),
            ], 200);
        }
    }

    // =========================
    // Helper: Full Post Response
    // =========================

    private function postFullResponse($postId, $viewerUserId)
    {
        $post = Post::with(['media'])->where('id', $postId)->first();
        if (! $post) {
            return null;
        }

        // Fetch User
        $postUser = User::where('id', $post->user_id)->first();

        // Fetch User Detail by user_id
        $userDetail = MasterUserDetail::where('user_id', $post->user_id)->first();

        $fname = $userDetail ? $userDetail->fname : null;

        // counts
        $likeCount = PostReaction::where('post_id', $postId)->where('reaction_type', 'like')->count();
        $dislikeCount = PostReaction::where('post_id', $postId)->where('reaction_type', 'dislike')->count();
        $commentCount = PostComment::where('post_id', $postId)->count();
        $saveCount = PostSave::where('post_id', $postId)->count();
        $shareCount = PostShare::where('post_id', $postId)->count();
        $viewCount = PostView::where('post_id', $postId)->count();

        // my states
        $myReaction = PostReaction::where('post_id', $postId)
            ->where('user_id', $viewerUserId)
            ->value('reaction_type');

        $isSaved = PostSave::where('post_id', $postId)
            ->where('user_id', $viewerUserId)
            ->exists();

        return [
            'id' => $post->id,
            'user_id' => $post->user_id,

            // from users table
            'user_name' => $postUser ? $postUser->user_name : null,

            // from master_user_details table
            'fname' => $fname,

            'title' => $post->title,
            'description' => $post->description,
            'industry_id' => $post->industry_id,
            'status' => $post->status,

            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,

            'media' => $post->media,

            'counts' => [
                'likes' => $likeCount,
                'dislikes' => $dislikeCount,
                'comments' => $commentCount,
                'saves' => $saveCount,
                'shares' => $shareCount,
                'views' => $viewCount,
            ],

            'my' => [
                'reaction' => $myReaction ?? 'none',
                'saved' => $isSaved,
            ],
        ];
    }

    private function updatePostCounts($postId)
    {
        Post::where('id', $postId)->update([
            'like_count' => PostReaction::where('post_id', $postId)->where('reaction_type', 'like')->count(),
            'dislike_count' => PostReaction::where('post_id', $postId)->where('reaction_type', 'dislike')->count(),
            'comment_count' => PostComment::where('post_id', $postId)->count(),
            'save_count' => PostSave::where('post_id', $postId)->count(),
            'share_count' => PostShare::where('post_id', $postId)->count(),
            'view_count' => PostView::where('post_id', $postId)->count(),
        ]);
    }

    // public function getUserDetails($id)
    // {
    //     $user = User::with([
    //         'masterUser',
    //         'masterUser.country',
    //         'masterUser.state',
    //         'masterUser.district',
    //     ])->find($id);

    //     if (! $user) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not found',
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'User details fetched successfully',
    //         'data' => $user,
    //     ], 200);
    // }

    public function getUserDetails($id)
    {
        $user = User::with([
            'masterUser',
            'masterUser.country',
            'masterUser.state',
            'masterUser.district',
            'businessInfo', // <-- ye add kiya
            'businessInfo.industry', // <-- agar industry details bhi chahiye
        ])->find($id);

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'User details fetched successfully',
            'data' => $user,
        ], 200);
    }
}
