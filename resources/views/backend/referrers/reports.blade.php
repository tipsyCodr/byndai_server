@extends('backend.layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700;">Referral Analytics & Reports</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">Comprehensive statistics on top referrers, revenue, discounts and coupon usage</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('admin.referrers.export-report', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="gradient-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="download"></i> Export CSV Report
        </a>
        <a href="{{ route('admin.referrers.index') }}" class="gradient-btn" style="text-decoration: none; background: rgba(255,255,255,0.1);">&larr; Back to Referrers</a>
    </div>
</div>

<div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
    <form method="GET" action="{{ route('admin.referrers.reports') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <label style="font-size: 0.9rem;">Date Range:</label>
        <input type="date" name="start_date" value="{{ $startDate }}" style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
        <input type="date" name="end_date" value="{{ $endDate }}" style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
        <button type="submit" class="gradient-btn">Apply Filter</button>
    </form>
</div>

<!-- Summary Cards Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
    <div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem;">
        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Referral Plans Sold</div>
        <div style="font-size: 1.6rem; font-weight: 700;">{{ number_format($totalSales) }}</div>
    </div>
    <div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem;">
        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Net Referral Revenue</div>
        <div style="font-size: 1.6rem; font-weight: 700; color: #34d399;">₹{{ number_format($totalRevenue, 2) }}</div>
    </div>
    <div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem;">
        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Discounts Granted</div>
        <div style="font-size: 1.6rem; font-weight: 700; color: #fbbf24;">₹{{ number_format($totalDiscounts, 2) }}</div>
    </div>
    <div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem;">
        <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Commissions Incurred</div>
        <div style="font-size: 1.6rem; font-weight: 700; color: #a855f7;">₹{{ number_format($totalCommissions, 2) }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Top Referrers Table -->
    <div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem; font-size: 1.2rem;">Top Performers</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 0.8rem; color: var(--text-secondary);">Referrer</th>
                    <th style="padding: 0.8rem; color: var(--text-secondary);">Sales</th>
                    <th style="padding: 0.8rem; color: var(--text-secondary);">Revenue</th>
                    <th style="padding: 0.8rem; color: var(--text-secondary);">Commission</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topReferrers as $top)
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <td style="padding: 0.8rem; font-weight: 600;">{{ $top->full_name }}</td>
                        <td style="padding: 0.8rem;">{{ $top->total_sales }}</td>
                        <td style="padding: 0.8rem;">₹{{ number_format($top->total_revenue ?? 0, 2) }}</td>
                        <td style="padding: 0.8rem; color: #a855f7; font-weight: 700;">₹{{ number_format($top->total_commission ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No sales in selected range.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Monthly Revenue Table -->
    <div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem; font-size: 1.2rem;">Monthly Referral Revenue ({{ date('Y') }})</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <th style="padding: 0.8rem; color: var(--text-secondary);">Month</th>
                    <th style="padding: 0.8rem; color: var(--text-secondary);">Sales</th>
                    <th style="padding: 0.8rem; color: var(--text-secondary);">Net Rev</th>
                    <th style="padding: 0.8rem; color: var(--text-secondary);">Commissions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($monthlyRevenue as $row)
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <td style="padding: 0.8rem; font-weight: 600;">{{ $row->month }}</td>
                        <td style="padding: 0.8rem;">{{ $row->sales_count }}</td>
                        <td style="padding: 0.8rem; color: #34d399;">₹{{ number_format($row->net_revenue, 2) }}</td>
                        <td style="padding: 0.8rem; color: #a855f7;">₹{{ number_format($row->total_commissions, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="padding: 1rem; text-align: center; color: var(--text-secondary);">No monthly data available.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
