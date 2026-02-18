<?php

namespace App\Http\Controllers\api\site;

use App\Http\Controllers\Controller;
use App\Models\ProfileLinkClick;
use App\Models\ProfileView;
use Illuminate\Http\Request;

class UserAnalyticsController extends Controller
{
    public function userAnalytics(Request $request)
    {
        $userId = $request->userId;

        if (! $userId) {
            return response()->json([
                'status' => false,
                'message' => 'userId is required',
            ], 400);
        }

        // ✅ pagination inputs
        $perPage = 10;
        $viewersPage = (int) ($request->viewers_page ?? 1);
        $clickersPage = (int) ($request->clickers_page ?? 1);

        if ($viewersPage < 1) {
            $viewersPage = 1;
        }
        if ($clickersPage < 1) {
            $clickersPage = 1;
        }

        // ---------------------------
        // 1) PROFILE VIEWS ANALYTICS
        // ---------------------------

        $totalViews = ProfileView::where('profile_user_id', $userId)->sum('view_count');
        $totalTimeSeconds = ProfileView::where('profile_user_id', $userId)->sum('total_time_seconds');

        // ✅ viewers pagination
        $viewersPaginated = ProfileView::where('profile_user_id', $userId)
            ->with([
                'viewer:id,user_name',
                'viewer.masterUser:emp_id,user_id,fname',
            ])
            ->orderByDesc('last_viewed_at')
            ->paginate($perPage, ['*'], 'viewers_page', $viewersPage);

        $viewers = collect($viewersPaginated->items())->map(function ($row) {
            return [
                'viewer_user_id' => $row->viewer_user_id,
                'viewer_user_name' => $row->viewer?->user_name,
                'viewer_fname' => $row->viewer?->masterUser?->fname,
                'view_count' => (int) $row->view_count,
                'total_time_seconds' => (int) $row->total_time_seconds,
                'last_viewed_at' => $row->last_viewed_at,
            ];
        });

        // ---------------------------
        // 2) PLATFORM CLICKS ANALYTICS
        // ---------------------------

        // ✅ FIXED PLATFORM LIST (always show)
        $allPlatforms = [
            'instagram',
            'facebook',
            'youtube',
            'whatsapp',
            'twitter',
            'telegram',
            'snapchat',
            'linkedin',
        ];

        // DB totals
        $dbPlatformTotals = ProfileLinkClick::where('profile_id', $userId)
            ->selectRaw('platform, SUM(click_count) as total_clicks, COUNT(DISTINCT viewer_id) as unique_users')
            ->groupBy('platform')
            ->get()
            ->keyBy('platform');

        // ✅ Final totals (with 0 values for missing platforms)
        $platformTotals = collect($allPlatforms)->map(function ($platform) use ($dbPlatformTotals) {
            return [
                'platform' => $platform,
                'total_clicks' => (string) ($dbPlatformTotals[$platform]->total_clicks ?? 0),
                'unique_users' => (int) ($dbPlatformTotals[$platform]->unique_users ?? 0),
            ];
        });

        // ✅ clickers pagination
        $clickersPaginated = ProfileLinkClick::where('profile_id', $userId)
            ->with([
                'viewer:id,user_name',
                'viewer.masterUser:emp_id,user_id,fname',
            ])
            ->orderByDesc('last_clicked_at')
            ->paginate($perPage, ['*'], 'clickers_page', $clickersPage);

        $clickers = collect($clickersPaginated->items())->map(function ($row) {
            return [
                'viewer_id' => $row->viewer_id,
                'viewer_user_name' => $row->viewer?->user_name,
                'viewer_fname' => $row->viewer?->masterUser?->fname,
                'platform' => $row->platform,
                'click_count' => (int) $row->click_count,
                'last_clicked_at' => $row->last_clicked_at,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'User analytics fetched successfully',
            'data' => [
                'profile_views' => [
                    'total_view_count' => (int) $totalViews,
                    'total_time_seconds' => (int) $totalTimeSeconds,

                    // ✅ viewers list
                    'viewers' => $viewers,

                    // ✅ viewers pagination info
                    'pagination' => [
                        'current_page' => $viewersPaginated->currentPage(),
                        'last_page' => $viewersPaginated->lastPage(),
                        'per_page' => $viewersPaginated->perPage(),
                        'total' => $viewersPaginated->total(),
                        'has_next' => $viewersPaginated->hasMorePages(),
                        'has_prev' => $viewersPaginated->currentPage() > 1,
                        'next_page' => $viewersPaginated->hasMorePages()
                            ? $viewersPaginated->currentPage() + 1
                            : null,
                        'prev_page' => $viewersPaginated->currentPage() > 1
                            ? $viewersPaginated->currentPage() - 1
                            : null,
                    ],
                ],

                'platform_clicks' => [
                    'platform_totals' => $platformTotals,

                    // ✅ clickers list
                    'clickers' => $clickers,

                    // ✅ clickers pagination info
                    'pagination' => [
                        'current_page' => $clickersPaginated->currentPage(),
                        'last_page' => $clickersPaginated->lastPage(),
                        'per_page' => $clickersPaginated->perPage(),
                        'total' => $clickersPaginated->total(),
                        'has_next' => $clickersPaginated->hasMorePages(),
                        'has_prev' => $clickersPaginated->currentPage() > 1,
                        'next_page' => $clickersPaginated->hasMorePages()
                            ? $clickersPaginated->currentPage() + 1
                            : null,
                        'prev_page' => $clickersPaginated->currentPage() > 1
                            ? $clickersPaginated->currentPage() - 1
                            : null,
                    ],
                ],
            ],
        ]);
    }
}
