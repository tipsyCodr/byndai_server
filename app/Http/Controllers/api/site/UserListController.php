<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Connection;

class UserListController extends Controller
{
    public function listUsers(Request $request)
    {
        $userId = $request->userId;

        if (!$userId) {
            return response()->json([
                "status" => false,
                "message" => "userId is required"
            ], 400);
        }

        // ============================
        // 1) Get friend ids (both side)
        // ============================

        // where user is sender
        $friends1 = Connection::where("user_id", $userId)->pluck("friend_id")->toArray();

        // where user is receiver
        $friends2 = Connection::where("friend_id", $userId)->pluck("user_id")->toArray();

        $friendIds = array_unique(array_merge($friends1, $friends2));

        // ============================
        // 2) Query Users (exclude self + friends)
        // ============================

        $users = User::where("id", "!=", $userId)
            ->whereNotIn("id", $friendIds)
            ->with([
                "masterUser" => function ($q) {
                    $q->select(
                        "emp_id",
                        "user_id",
                        "fname",
                        "address",
                        "state_id",
                        "profile_pic",
                        "profile_logo"
                    );
                },
                "businessInfo" => function ($q) {
                    $q->select(
                        "id",
                        "user_id",
                        "legal_name",
                        "industry_id",
                        "description",
                        "gst_no"
                    );
                },
                "masterUser.state:id,state_name",
                "businessInfo.industry:id,title"
            ])
            ->select("id", "user_name")
            ->orderByDesc("id")
            ->paginate(10);

        // ============================
        // 3) Response format
        // ============================

        $formatted = collect($users->items())->map(function ($u) {

            return [
                "id" => $u->id,
                "user_name" => $u->user_name,

                "fname" => $u->masterUser?->fname,
                "address" => $u->masterUser?->address,

                "state" => $u->masterUser?->state ? [
                    "id" => $u->masterUser?->state?->id,
                    "state_name" => $u->masterUser?->state?->state_name,
                ] : null,

                "profile_pic" => $u->masterUser?->profile_pic,
                "profile_logo" => $u->masterUser?->profile_logo,

                "business" => $u->businessInfo ? [
                    "legal_name" => $u->businessInfo?->legal_name,
                    "description" => $u->businessInfo?->description,
                    "gst_no" => $u->businessInfo?->gst_no,

                    "industry" => $u->businessInfo?->industry ? [
                        "id" => $u->businessInfo?->industry?->id,
                        "title" => $u->businessInfo?->industry?->title,
                    ] : null,
                ] : null,
            ];
        });

        return response()->json([
            "status" => true,
            "message" => "Users list fetched successfully",
            "data" => [
                "users" => $formatted,
                "pagination" => [
                    "current_page" => $users->currentPage(),
                    "per_page" => $users->perPage(),
                    "total" => $users->total(),
                    "last_page" => $users->lastPage(),
                    "has_next" => $users->hasMorePages(),
                ]
            ]
        ]);
    }
}
