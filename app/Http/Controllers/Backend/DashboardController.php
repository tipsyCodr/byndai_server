<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Calculate Monthly Acquisition Data (Last 6 Months)
        $monthlyAcquisition = User::selectRaw('COUNT(*) as count, MONTH(created_at) as month, YEAR(created_at) as year')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get()
            ->mapWithKeys(function ($item) {
                $monthName = \Carbon\Carbon::createFromDate($item->year, $item->month, 1)->format('M');
                return [$monthName => $item->count];
            });

        // Ensure we show last 6 months even if count is 0
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('M');
            $chartData[$month] = $monthlyAcquisition[$month] ?? 0;
        }

        // Real dashboard data
        $stats = [
            'total_users' => User::count(),
            'new_signups' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'active_sessions' => \Illuminate\Support\Facades\DB::table('sessions')->count(),
            'revenue' => \Illuminate\Support\Facades\DB::table('payments')->where('status', 1)->sum('amount'),
        ];

        $recentUsers = User::latest()->limit(3)->get();

        return view('backend.dashboard', compact('user', 'stats', 'chartData', 'recentUsers'));
    }
}
