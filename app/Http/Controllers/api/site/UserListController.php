<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\User;
use Illuminate\Http\Request;

class UserListController extends Controller
{
    // public function listUsers(Request $request)
    // {
    //     $userId = $request->userId;

    //     if (! $userId) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'userId is required',
    //         ], 400);
    //     }

    //     // ======================================
    //     // 1️⃣ Get friend ids (both direction)
    //     // ======================================

    //     $friends1 = Connection::where('user_id', $userId)
    //         ->pluck('friend_id')
    //         ->toArray();

    //     $friends2 = Connection::where('friend_id', $userId)
    //         ->pluck('user_id')
    //         ->toArray();

    //     $friendIds = array_unique(array_merge($friends1, $friends2));

    //     // ======================================
    //     // 2️⃣ Fetch Users (exclude self + friends)
    //     // ======================================

    //     $users = User::where('id', '!=', $userId)
    //         ->whereNotIn('id', $friendIds)

    //         // 🔥 Proper friend count query
    //         ->select('users.id', 'users.user_name')
    //         ->selectRaw('
    //         (
    //             SELECT COUNT(*)
    //             FROM connections
    //             WHERE connections.user_id = users.id
    //                OR connections.friend_id = users.id
    //         ) as friend_count
    //     ')

    //         ->with([
    //             'masterUser:emp_id,user_id,fname,address,state_id,profile_pic,profile_logo',
    //             'masterUser.state:id,state_name',
    //             'businessInfo:id,user_id,legal_name,industry_id,description,gst_no',
    //             'businessInfo.industry:id,title',
    //         ])

    //         ->orderByDesc('users.id')
    //         ->paginate(10);

    //     // ======================================
    //     // 3️⃣ Format response
    //     // ======================================

    //     $formatted = collect($users->items())->map(function ($u) {

    //         return [
    //             'id' => $u->id,
    //             'user_name' => $u->user_name,
    //             'friend_count' => (int) $u->friend_count,

    //             'fname' => $u->masterUser?->fname,
    //             'address' => $u->masterUser?->address,

    //             'state' => $u->masterUser?->state ? [
    //                 'id' => $u->masterUser?->state?->id,
    //                 'state_name' => $u->masterUser?->state?->state_name,
    //             ] : null,

    //             'profile_pic' => $u->masterUser?->profile_pic,
    //             'profile_logo' => $u->masterUser?->profile_logo,

    //             'business' => $u->businessInfo ? [
    //                 'legal_name' => $u->businessInfo?->legal_name,
    //                 'description' => $u->businessInfo?->description,
    //                 'gst_no' => $u->businessInfo?->gst_no,
    //                 'industry' => $u->businessInfo?->industry ? [
    //                     'id' => $u->businessInfo?->industry?->id,
    //                     'title' => $u->businessInfo?->industry?->title,
    //                 ] : null,
    //             ] : null,
    //         ];
    //     });

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Users list fetched successfully',
    //         'data' => [
    //             'users' => $formatted,
    //             'pagination' => [
    //                 'current_page' => $users->currentPage(),
    //                 'per_page' => $users->perPage(),
    //                 'total' => $users->total(),
    //                 'last_page' => $users->lastPage(),
    //                 'has_next' => $users->hasMorePages(),
    //             ],
    //         ],
    //     ]);
    // }

    public function listUsers(Request $request)
    {
        $userId = $request->userId;
        $search = $request->search; // 👈 NEW

        if (! $userId) {
            return response()->json([
                'status' => false,
                'message' => 'userId is required',
            ], 400);
        }

        // ======================================
        // 1️⃣ Get friend ids (both direction)
        // ======================================

        $friends1 = Connection::where('user_id', $userId)
            ->pluck('friend_id')
            ->toArray();

        $friends2 = Connection::where('friend_id', $userId)
            ->pluck('user_id')
            ->toArray();

        $friendIds = array_unique(array_merge($friends1, $friends2));

        // ======================================
        // 2️⃣ Base Query
        // ======================================

        $query = User::where('id', '!=', $userId)
            ->whereNotIn('id', $friendIds)

            ->select('users.id', 'users.user_name')
            ->selectRaw('
            (
                SELECT COUNT(*)
                FROM connections
                WHERE connections.user_id = users.id
                   OR connections.friend_id = users.id
            ) as friend_count
        ')

            ->with([
                'masterUser:emp_id,user_id,fname,address,state_id,profile_pic,profile_logo',
                'masterUser.state:id,state_name',
                'businessInfo:id,user_id,legal_name,industry_id,description,gst_no',
                'businessInfo.industry:id,title',
            ]);

        // ======================================
        // 🔎 3️⃣ Search Functionality Added
        // ======================================

        if ($search) {
            $query->where(function ($q) use ($search) {

                $q->where('users.user_name', 'LIKE', "%{$search}%")
                    ->orWhereHas('masterUser', function ($mq) use ($search) {
                        $mq->where('fname', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('businessInfo', function ($bq) use ($search) {
                        $bq->where('legal_name', 'LIKE', "%{$search}%")
                            ->orWhere('description', 'LIKE', "%{$search}%");
                    });

            });
        }

        // ======================================
        // 4️⃣ Pagination
        // ======================================

        $users = $query->orderByDesc('users.id')->paginate(10);

        // ======================================
        // 5️⃣ Format response
        // ======================================

        $formatted = collect($users->items())->map(function ($u) {

            return [
                'id' => $u->id,
                'user_name' => $u->user_name,
                'friend_count' => (int) $u->friend_count,

                'fname' => $u->masterUser?->fname,
                'address' => $u->masterUser?->address,

                'state' => $u->masterUser?->state ? [
                    'id' => $u->masterUser?->state?->id,
                    'state_name' => $u->masterUser?->state?->state_name,
                ] : null,

                'profile_pic' => $u->masterUser?->profile_pic,
                'profile_logo' => $u->masterUser?->profile_logo,

                'business' => $u->businessInfo ? [
                    'legal_name' => $u->businessInfo?->legal_name,
                    'description' => $u->businessInfo?->description,
                    'gst_no' => $u->businessInfo?->gst_no,
                    'industry' => $u->businessInfo?->industry ? [
                        'id' => $u->businessInfo?->industry?->id,
                        'title' => $u->businessInfo?->industry?->title,
                    ] : null,
                ] : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Users list fetched successfully',
            'data' => [
                'users' => $formatted,
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                    'has_next' => $users->hasMorePages(),
                ],
            ],
        ]);
    }
}
