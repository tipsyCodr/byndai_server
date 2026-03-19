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
        
        // Mocking some dashboard data (metrics, stats)
        $stats = [
            'total_users' => User::count(),
            'new_signups' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'active_sessions' => 124, // Mock
            'revenue' => 1450.50, // Mock
        ];

        return view('backend.dashboard', compact('user', 'stats'));
    }
}
