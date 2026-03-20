@extends('backend.layouts.app')

@section('styles')
    <style>
        .dashboard-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .welcome-text h1 {
            font-size: 1.8rem;
            margin-bottom: 0.2rem;
            font-weight: 700;
        }

        .welcome-text p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            padding: 1.2rem;
            border-radius: 1.2rem;
            display: flex;
            align-items: center;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.2rem;
        }

        .stat-info h3 {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.1rem;
        }

        .stat-info span {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .chart-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 1024px) {
            .chart-section {
                grid-template-columns: 1fr;
            }
        }

        .content-card {
            padding: 1.5rem;
            border-radius: 1.5rem;
            min-height: 350px;
        }

        .content-card h2 {
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }

        .chart-mock {
            height: 200px;
            display: flex;
            align-items: flex-end;
            gap: 0.8rem;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--glass-border);
        }

        .bar {
            background: var(--primary-gradient);
            border-radius: 4px 4px 0 0;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        .bar:hover {
            opacity: 1;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-header animate-fade">
        <div class="welcome-text">
            <h1>Welcome, <span class="gradient-text">{{ $user->user_name }}!</span></h1>
            <p>Your Byndai dashboard overview at a glance.</p>
        </div>
        <div class="header-actions">
            <button class="gradient-btn" style="display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="plus-circle" style="width: 20px;"></i>
                New Website
            </button>
        </div>
    </div>

    <div class="stats-grid animate-fade" style="animation-delay: 0.1s;">
        <!-- Total Users -->
        <div class="stat-card premium-glass">
            <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: var(--accent-primary);">
                <i data-lucide="users" style="width: 30px; height: 30px;"></i>
            </div>
            <div class="stat-info">
                <h3>Total Users</h3>
                <span>{{ number_format($stats['total_users']) }}</span>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="stat-card premium-glass">
            <div class="stat-icon" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                <i data-lucide="activity" style="width: 30px; height: 30px;"></i>
            </div>
            <div class="stat-info">
                <h3>Active Sessions</h3>
                <span>{{ $stats['active_sessions'] }}</span>
            </div>
        </div>

        <!-- Revenue -->
        <div class="stat-card premium-glass">
            <div class="stat-icon" style="background: rgba(234, 179, 8, 0.1); color: #eab308;">
                <i data-lucide="dollar-sign" style="width: 30px; height: 30px;"></i>
            </div>
            <div class="stat-info">
                <h3>Total Revenue</h3>
                <span>${{ number_format($stats['revenue'], 2) }}</span>
            </div>
        </div>
    </div>

    <div class="chart-section animate-fade" style="animation-delay: 0.2s;">
        <div class="content-card premium-glass">
            <h2>Monthly User Acquisition</h2>
            <div class="chart-mock" style="padding-bottom: 40px;">
                @php
                    $max = count($chartData) > 0 ? max(array_values($chartData)) : 1;
                    $max = $max == 0 ? 1 : $max;
                @endphp
                @foreach ($chartData as $month => $count)
                    <div
                        style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; position: relative;">
                        <div class="bar" style="height: {{ ($count / $max) * 100 }}%; width: 80%;"
                            title="{{ $month }}: {{ $count }} users">
                        </div>
                        <span
                            style="position: absolute; bottom: -30px; font-size: 0.8rem; color: var(--text-secondary);">{{ $month }}</span>
                        <span
                            style="position: absolute; top: -25px; font-size: 0.7rem; color: var(--accent-primary); font-weight: 600;">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
            <p style="margin-top: 2.5rem; color: var(--text-secondary); text-align: center;">
                User registrations over the last 6 months
            </p>
        </div>

        <div class="content-card premium-glass">
            <h2>Recent Signups</h2>
            <ul style="list-style: none;">
                @foreach ($recentUsers as $recentUser)
                    <li style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                        <div
                            style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent-secondary); margin-right: 1rem; display: flex; align-items: center; justify-content: center; font-weight: bold; color: white;">
                            {{ strtoupper(substr($recentUser->user_name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 600;">{{ $recentUser->user_name }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                {{ $recentUser->created_at?->diffForHumans() ?? 'Recently' }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
            <button class="gradient-btn" style="width: 100%; margin-top: 1rem; padding: 0.6rem; font-size: 0.9rem;">View
                All</button>
        </div>
    </div>
@endsection