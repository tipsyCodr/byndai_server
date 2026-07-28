@extends('backend.layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700;">All Referral Sales</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">Track all subscription sales generated through referral coupon codes</p>
    </div>
    <a href="{{ route('admin.referrers.index') }}" class="gradient-btn" style="text-decoration: none;">&larr; Back to Referrers</a>
</div>

<div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
    <form method="GET" action="{{ route('admin.referrers.sales') }}" style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <select name="referrer_id" style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.8rem; padding: 0.6rem 1rem; color: white;">
            <option value="">All Referrers</option>
            @foreach($referrers as $ref)
                <option value="{{ $ref->id }}" {{ request('referrer_id') == $ref->id ? 'selected' : '' }}>{{ $ref->full_name }} ({{ $ref->coupon?->code }})</option>
            @endforeach
        </select>

        <input type="date" name="start_date" value="{{ request('start_date') }}" style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.8rem; padding: 0.6rem 1rem; color: white;">
        <input type="date" name="end_date" value="{{ request('end_date') }}" style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.8rem; padding: 0.6rem 1rem; color: white;">

        <button type="submit" class="gradient-btn">Filter Sales</button>
    </form>
</div>

<div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <th style="padding: 1rem; color: var(--text-secondary);">ID</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Date</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Referrer</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Coupon</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Customer</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Plan</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Original</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Discount</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Final Paid</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Commission</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $sale)
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 1rem;">#{{ $sale->id }}</td>
                    <td style="padding: 1rem;">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                    <td style="padding: 1rem; font-weight: 600;">{{ $sale->referrer?->full_name ?? 'N/A' }}</td>
                    <td style="padding: 1rem;">
                        <span style="background: rgba(168, 85, 247, 0.2); color: #c084fc; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 0.4rem; font-family: monospace;">
                            {{ $sale->coupon?->code ?? 'N/A' }}
                        </span>
                    </td>
                    <td style="padding: 1rem;">{{ $sale->user?->user_name }}</td>
                    <td style="padding: 1rem;">{{ $sale->plan?->name }}</td>
                    <td style="padding: 1rem;">₹{{ number_format($sale->original_price, 2) }}</td>
                    <td style="padding: 1rem;">₹{{ number_format($sale->discount_amount, 2) }}</td>
                    <td style="padding: 1rem; font-weight: 700;">₹{{ number_format($sale->final_amount, 2) }}</td>
                    <td style="padding: 1rem; color: #a855f7; font-weight: 700;">+₹{{ number_format($sale->commission_amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="padding: 2rem; text-align: center; color: var(--text-secondary);">No referral sales found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">
        {{ $sales->links() }}
    </div>
</div>
@endsection
