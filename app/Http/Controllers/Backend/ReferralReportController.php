<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Referrer;
use App\Models\ReferralSale;
use App\Models\ReferralCommission;
use App\Models\ReferralCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferralReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        // 1. Top Referrers
        $topReferrers = Referrer::with(['user', 'coupon'])
            ->withCount(['sales as total_sales' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['sales as total_revenue' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }], 'final_amount')
            ->withSum(['sales as total_commission' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }], 'commission_amount')
            ->orderBy('total_sales', 'desc')
            ->limit(10)
            ->get();

        // 2. Revenue by Month (Current Year)
        $monthlyRevenue = ReferralSale::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw("COUNT(*) as sales_count"),
            DB::raw("SUM(original_price) as gross_revenue"),
            DB::raw("SUM(discount_amount) as total_discounts"),
            DB::raw("SUM(final_amount) as net_revenue"),
            DB::raw("SUM(commission_amount) as total_commissions")
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->get();

        // 3. Coupon Usage Stats
        $couponStats = ReferralCoupon::with(['referrer'])
            ->withCount(['sales as total_uses' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['sales as revenue_generated' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }], 'final_amount')
            ->orderBy('total_uses', 'desc')
            ->paginate(15);

        // Summary Cards
        $totalSales = ReferralSale::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalRevenue = ReferralSale::whereBetween('created_at', [$startDate, $endDate])->sum('final_amount');
        $totalDiscounts = ReferralSale::whereBetween('created_at', [$startDate, $endDate])->sum('discount_amount');
        $totalCommissions = ReferralSale::whereBetween('created_at', [$startDate, $endDate])->sum('commission_amount');

        return view('backend.referrers.reports', compact(
            'topReferrers',
            'monthlyRevenue',
            'couponStats',
            'startDate',
            'endDate',
            'totalSales',
            'totalRevenue',
            'totalDiscounts',
            'totalCommissions'
        ));
    }

    public function exportReport(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $sales = ReferralSale::with(['referrer', 'user', 'plan', 'coupon'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "referral_report_{$startDate}_to_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Sale ID', 'Referrer Name', 'Coupon Code', 'Customer Name', 'Customer Email', 'Plan Name', 'Original Price', 'Discount', 'Final Amount', 'Commission', 'Date']);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->id,
                    $sale->referrer?->full_name ?? 'N/A',
                    $sale->coupon?->code ?? 'N/A',
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
