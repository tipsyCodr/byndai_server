<?php

namespace App\Http\Controllers\Referrer;

use App\Http\Controllers\Controller;
use App\Models\ReferralSale;
use App\Models\ReferralCommission;
use App\Models\CommissionPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReferrerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $referrer = Auth::user()->referrer;
        $coupon = $referrer->coupon;

        $query = ReferralSale::with(['user', 'plan', 'transaction'])
            ->where('referrer_id', $referrer->id);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $sales = (clone $query)->orderBy('created_at', 'desc')->paginate(15);

        // Stats
        $totalUses = (clone $query)->count();
        $totalPlansSold = (clone $query)->count();
        $totalRevenue = (clone $query)->sum('final_amount');
        $totalCommissionEarned = (clone $query)->sum('commission_amount');

        $pendingCommission = ReferralCommission::where('referrer_id', $referrer->id)
            ->where('status', 'pending')
            ->sum('amount');

        $paidCommission = ReferralCommission::where('referrer_id', $referrer->id)
            ->where('status', 'paid')
            ->sum('amount');

        // Monthly Earnings Chart (Last 6 Months)
        $monthlyEarnings = ReferralSale::where('referrer_id', $referrer->id)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("SUM(commission_amount) as total_commission"),
                DB::raw("COUNT(*) as sales_count")
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->limit(6)
            ->get();

        $commissionHistory = ReferralCommission::with('sale.plan')
            ->where('referrer_id', $referrer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'commission_page');

        return view('referrer.dashboard', compact(
            'referrer',
            'coupon',
            'sales',
            'totalUses',
            'totalPlansSold',
            'totalRevenue',
            'totalCommissionEarned',
            'pendingCommission',
            'paidCommission',
            'monthlyEarnings',
            'commissionHistory'
        ));
    }

    public function profile()
    {
        $referrer = Auth::user()->referrer;
        return view('referrer.profile', compact('referrer'));
    }

    public function exportCsv(Request $request)
    {
        $referrer = Auth::user()->referrer;
        $sales = ReferralSale::with(['user', 'plan'])
            ->where('referrer_id', $referrer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'referral_sales_' . date('Y_m_d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Customer Name', 'Customer Email', 'Plan Purchased', 'Original Price (INR)', 'Discount (INR)', 'Final Amount (INR)', 'Commission Earned (INR)', 'Date']);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->id,
                    $sale->user?->user_name ?? 'N/A',
                    $sale->user?->email ?? 'N/A',
                    $sale->plan?->name ?? 'N/A',
                    $sale->original_price,
                    $sale->discount_amount,
                    $sale->final_amount,
                    $sale->commission_amount,
                    $sale->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
