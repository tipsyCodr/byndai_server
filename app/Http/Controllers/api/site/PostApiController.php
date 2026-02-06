<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Post;
use App\Models\PostMedia;
use App\Models\PostReaction;
use App\Models\PostComment;
use App\Models\PostSave;
use App\Models\PostShare;
use App\Models\PostView;
use App\Models\PostReport;
use App\Models\MasterUserDetail;
use Illuminate\Support\Facades\Auth;

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
          if (!$user) {
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

                  $fileName = time() . "_" . uniqid() . "." . $file->getClientOriginalExtension();  

                  // save in storage/app/public/uploads/posts
                  $path = $file->storeAs("uploads/posts", $fileName, "public");  

                  PostMedia::create([
                      'post_id' => $post->id,
                      'media_type' => 'image',
                      'media_url' => "/storage/" . $path,
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

                  $fileName = time() . "_" . uniqid() . "." . $file->getClientOriginalExtension();  

                  $path = $file->storeAs("uploads/posts", $fileName, "public");  

                  PostMedia::create([
                      'post_id' => $post->id,
                      'media_type' => 'video',
                      'media_url' => "/storage/" . $path,
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
    public function updatePost(Request $request, $post_id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:150',
            'description' => 'nullable|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $userId = $request->user()->id;

            $post = Post::where('id', $post_id)
                ->where('user_id', $userId)
                ->first();

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found or unauthorized',
                ], 404);
            }

            $post->update([
                'title' => $request->title ?? $post->title,
                'description' => $request->description ?? $post->description,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Post updated successfully',
                'data' => $this->postFullResponse($post_id, $userId),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update post: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 3) DELETE POST
    // =========================
    public function deletePost(Request $request, $post_id)
    {
        try {
            $userId = $request->user()->id;

            $post = Post::where('id', $post_id)
                ->where('user_id', $userId)
                ->first();

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found or unauthorized',
                ], 404);
            }

            $post->update(['status' => 'deleted']);

            return response()->json([
                'status' => true,
                'message' => 'Post deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete post: ' . $e->getMessage(),
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
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch feed: ' . $e->getMessage(),
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

            if (!$post) {
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
                'message' => 'Failed to fetch post detail: ' . $e->getMessage(),
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

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ], 404);
        }

        // Remove reaction if type is none
        if ($request->reaction_type === "none") {
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
                'reaction' => $request->reaction
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
            'message' => 'Failed to react: ' . $e->getMessage(),
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

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            // if reply parent exists
            if ($request->parent_id) {
                $parent = PostComment::where('id', $request->parent_id)
                    ->where('post_id', $request->post_id)
                    ->first();

                if (!$parent) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Parent comment not found',
                    ], 404);
                }
            }

            $comment = PostComment::create([
                'post_id' => $request->post_id,
                'user_id' => $userId,
                'comment' => $request->comment,
                'parent_id' => $request->parent_id ?? null,
            ]);

            $this->updatePostCounts($request->post_id);

            return response()->json([
                'status' => true,
                'message' => 'Comment added successfully',
                'data' => $comment,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to add comment: ' . $e->getMessage(),
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

            if (!$comment) {
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
                'message' => 'Failed to delete comment: ' . $e->getMessage(),
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

            if (!$post) {
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
                $msg = "Post unsaved";
            } else {
                PostSave::create([
                    'post_id' => $postId,
                    'user_id' => $userId,
                ]);
                $msg = "Post saved";
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
                'message' => 'Failed to save post: ' . $e->getMessage(),
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
                'share_type' => $request->share_type ?? "internal",
            ]);

            $this->updatePostCounts($request->post_id);

            return response()->json([
                'status' => true,
                'message' => 'Post shared',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to share: ' . $e->getMessage(),
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

            if (!$exists) {
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
                'message' => 'Failed to count view: ' . $e->getMessage(),
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
                'message' => 'Failed to report: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // 13) User Active Status Post List
    // =========================

    public function userPosts(Request $request, $user_id)
    {
    try {
        $viewerUserId = $request->user()->id;

        $posts = Post::where('user_id', $user_id)
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $data = [];

        foreach ($posts->items() as $post) {
            $data[] = $this->postFullResponse($post->id, $viewerUserId);
        }

        return response()->json([
            'status' => true,
            'message' => 'User posts fetched successfully',
            'data' => $data,
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'next_page_url' => $posts->nextPageUrl(),
                'prev_page_url' => $posts->previousPageUrl(),
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to fetch user posts: ' . $e->getMessage(),
        ], 500);
    }
    }

    // =========================
    // 13) All User Post List
    // =========================

    public function listAllPosts(Request $request)
    {
    try {
        $viewerUserId = $request->user()->id;

        $posts = Post::with([
                'media',
                'user',
                'userDetail'
            ])
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $data = [];

        foreach ($posts->items() as $post) {

            // counts
            $likeCount = PostReaction::where('post_id', $post->id)->where('reaction_type', 'like')->count();
            $dislikeCount = PostReaction::where('post_id', $post->id)->where('reaction_type', 'dislike')->count();
            $commentCount = PostComment::where('post_id', $post->id)->count();
            $saveCount = PostSave::where('post_id', $post->id)->count();
            $shareCount = PostShare::where('post_id', $post->id)->count();
            $viewCount = PostView::where('post_id', $post->id)->count();

            // my states
            $myReaction = PostReaction::where('post_id', $post->id)
                ->where('user_id', $viewerUserId)
                ->value('reaction_type');

            $isSaved = PostSave::where('post_id', $post->id)
                ->where('user_id', $viewerUserId)
                ->exists();

            $data[] = [
                'id' => $post->id,
                'user_id' => $post->user_id,

                // user info
                'user' => [
                    'user_name' => $post->user ? $post->user->user_name : null,
                    'fname' => $post->userDetail ? $post->userDetail->fname : null,
                ],

                'title' => $post->title,
                'description' => $post->description,
                'status' => $post->status,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,

                // media (images/videos links)
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
                    'reaction' => $myReaction ?? "none",
                    'saved' => $isSaved,
                ],
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'All posts fetched successfully',
            'data' => $data,
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'next_page_url' => $posts->nextPageUrl(),
                'prev_page_url' => $posts->previousPageUrl(),
            ]
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to fetch posts: ' . $e->getMessage(),
        ], 500);
    }
    }



    // =========================
    // Helper: Full Post Response
    // =========================
   

    private function postFullResponse($postId, $viewerUserId)
   {
    $post = Post::with(['media'])->where('id', $postId)->first();
    if (!$post) return null;

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
            'reaction' => $myReaction ?? "none",
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

}
