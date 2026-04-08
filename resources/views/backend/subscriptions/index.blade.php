@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">User Subscriptions</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Monitor and manage active plans for your community</p>
        </div>
        <form action="{{ route('admin.subscriptions.index') }}" method="GET" style="display: flex; gap: 1rem;">
            <input type="text" name="search" class="form-control" placeholder="Search User or ID..." value="{{ request('search') }}" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1.5rem; border-radius: 0.8rem; color: white; width: 300px;">
            <button type="submit" class="gradient-btn" style="padding: 0 1.5rem; border-radius: 0.8rem;">
                <i data-lucide="search" style="width: 20px;"></i>
            </button>
        </form>
    </div>

    <div class="premium-glass" style="padding: 0; overflow: hidden; border-radius: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.05);">
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">User</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Current Plan</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Status</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Validity</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                <tr style="border-bottom: 1px solid var(--glass-border); transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem 2rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white;">
                                {{ substr($sub->user->user_name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $sub->user->user_name ?? 'Unknown User' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">ID: {{ $sub->user_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1.5rem 2rem;">
                        <span style="font-weight: 600;">{{ $sub->plan->name ?? 'N/A' }}</span>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ ucfirst($sub->plan->type ?? '') }}</div>
                    </td>
                    <td style="padding: 1.5rem 2rem;">
                        @if($sub->isActive())
                            <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 0.4rem 0.8rem; border-radius: 0.5rem; font-size: 0.75rem;">ACTIVE</span>
                        @else
                            <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #f87171; padding: 0.4rem 0.8rem; border-radius: 0.5rem; font-size: 0.75rem;">EXPIRED</span>
                        @endif
                    </td>
                    <td style="padding: 1.5rem 2rem;">
                        <div style="font-size: 0.85rem; color: var(--text-primary);">Till: {{ $sub->end_date ? $sub->end_date->format('d M, Y') : 'N/A' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $sub->start_date ? $sub->start_date->format('d M, Y') : '' }}</div>
                    </td>
                    <td style="padding: 1.5rem 2rem; text-align: right;">
                        <a href="{{ route('admin.subscriptions.show', $sub->id) }}" class="gradient-btn" style="padding: 0.6rem 1.2rem; border-radius: 0.6rem; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                            <i data-lucide="bar-chart-3" style="width: 14px;"></i>
                            Usage Stats
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 4rem; text-align: center; color: var(--text-secondary);">
                        No subscriptions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div style="padding: 2rem;">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>
@endsection
