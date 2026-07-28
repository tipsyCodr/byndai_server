@extends('backend.layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700;">Referrer Management</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">Review applications, manage codes and monitor affiliate performance</p>
    </div>
    <div style="display: flex; gap: 0.75rem;">
        <a href="{{ route('admin.referrers.sales') }}" class="gradient-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
            <i data-lucide="shopping-cart"></i> All Sales
        </a>
        <a href="{{ route('admin.referrers.payouts') }}" class="gradient-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; background: linear-gradient(135deg, #10b981, #059669);">
            <i data-lucide="credit-card"></i> Payouts
        </a>
        <a href="{{ route('admin.referrers.reports') }}" class="gradient-btn" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; background: linear-gradient(135deg, #f59e0b, #d97706);">
            <i data-lucide="bar-chart-2"></i> Reports
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #6ee7b7; padding: 0.75rem 1rem; border-radius: 0.8rem; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

<div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
    <form method="GET" action="{{ route('admin.referrers.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Name, Email, Mobile or Coupon Code..." style="flex: 1; min-width: 250px; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.8rem; padding: 0.6rem 1rem; color: white;">
        
        <select name="status" style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.8rem; padding: 0.6rem 1rem; color: white;">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>

        <button type="submit" class="gradient-btn">Search & Filter</button>
    </form>
</div>

<div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <th style="padding: 1rem; color: var(--text-secondary);">Referrer</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Contact</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Coupon Code</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Status</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Active</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Sales</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Earned</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($referrers as $ref)
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 1rem; font-weight: 600;">
                        <a href="{{ route('admin.referrers.show', $ref->id) }}" style="color: white; text-decoration: none;">
                            {{ $ref->full_name }}
                        </a>
                    </td>
                    <td style="padding: 1rem;">
                        <div>{{ $ref->user?->email }}</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">{{ $ref->user?->user_mobile }}</div>
                    </td>
                    <td style="padding: 1rem;">
                        <span style="background: rgba(168, 85, 247, 0.2); color: #c084fc; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 0.4rem; font-family: monospace;">
                            {{ $ref->coupon?->code ?? 'N/A' }}
                        </span>
                    </td>
                    <td style="padding: 1rem;">
                        @if($ref->status === 'approved')
                            <span style="background: rgba(16, 185, 129, 0.2); color: #34d399; padding: 0.2rem 0.6rem; border-radius: 0.4rem; font-size: 0.8rem; font-weight: 600;">Approved</span>
                        @elseif($ref->status === 'pending')
                            <span style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; padding: 0.2rem 0.6rem; border-radius: 0.4rem; font-size: 0.8rem; font-weight: 600;">Pending</span>
                        @else
                            <span style="background: rgba(239, 68, 68, 0.2); color: #f87171; padding: 0.2rem 0.6rem; border-radius: 0.4rem; font-size: 0.8rem; font-weight: 600;">Rejected</span>
                        @endif
                    </td>
                    <td style="padding: 1rem;">
                        <form action="{{ route('admin.referrers.toggle-active', $ref->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="background: transparent; border: none; cursor: pointer; color: {{ $ref->is_active ? '#34d399' : '#f87171' }}; font-weight: 600;">
                                {{ $ref->is_active ? 'Enabled' : 'Disabled' }}
                            </button>
                        </form>
                    </td>
                    <td style="padding: 1rem;">{{ $ref->sales->count() }}</td>
                    <td style="padding: 1rem; color: #a855f7; font-weight: 600;">₹{{ number_format($ref->sales->sum('commission_amount'), 2) }}</td>
                    <td style="padding: 1rem;">
                        <a href="{{ route('admin.referrers.show', $ref->id) }}" class="gradient-btn" style="padding: 0.4rem 0.8rem; font-size: 0.8rem; text-decoration: none;">View / Manage</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding: 2rem; text-align: center; color: var(--text-secondary);">No referrers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">
        {{ $referrers->links() }}
    </div>
</div>
@endsection
