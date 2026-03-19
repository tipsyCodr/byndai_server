@extends('backend.layouts.app')

@section('styles')
    <style>
        .dashboard-header {
            margin-bottom: 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .welcome-text h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        .welcome-text p {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }
        .stat-card {
            padding: 2rem;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .stat-card:hover {
            transform: scale(1.05);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
        }
        .stat-info h3 {
            font-size: 0.9rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.2rem;
        }
        .stat-info span {
            font-size: 1.8rem;
            font-weight: 700;
        }
        .chart-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .content-card {
            padding: 2.5rem;
            border-radius: 2rem;
            height: 400px;
        }
        .content-card h2 {
            margin-bottom: 2rem;
            font-size: 1.5rem;
        }
        /* Fancy chart mock */
        .chart-mock {
            height: 250px;
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--glass-border);
        }
        .bar {
            flex: 1;
            background: linear-gradient(to top, var(--accent-primary), var(--accent-secondary));
            border-radius: 4px 4px 0 0;
            opacity: 0.7;
            transition: opacity 0.3s;
        }
        .bar:hover { opacity: 1; }
    </style>
@endsection

@section('content')
<div class="dashboard-header animate-fade">
    <div class="welcome-text">
        <h1>Welcome, <span class="gradient-text">{{ $user->user_name }}!</span></h1>
        <p>Your Grid dashboard overview at a glance.</p>
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
        <h2>Growth Analytics</h2>
        <div class="chart-mock">
            <div class="bar" style="height: 40%;"></div>
            <div class="bar" style="height: 65%;"></div>
            <div class="bar" style="height: 45%;"></div>
            <div class="bar" style="height: 85%;"></div>
            <div class="bar" style="height: 70%;"></div>
            <div class="bar" style="height: 90%;"></div>
            <div class="bar" style="height: 60%;"></div>
            <div class="bar" style="height: 100%;"></div>
            <div class="bar" style="height: 75%;"></div>
        </div>
        <p style="margin-top: 1rem; color: var(--text-secondary); text-align: center;">Monthly user acquisitions overview</p>
    </div>

    <div class="content-card premium-glass">
        <h2>Recent Signups</h2>
        <ul style="list-style: none;">
            <li style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent-secondary); margin-right: 1rem;"></div>
                <div>
                    <div style="font-weight: 600;">David Miller</div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">2 hours ago</div>
                </div>
            </li>
            <li style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--accent-primary); margin-right: 1rem;"></div>
                <div>
                    <div style="font-weight: 600;">Sarah Johnson</div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">4 hours ago</div>
                </div>
            </li>
            <li style="display: flex; align-items: center; margin-bottom: 1.5rem;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: #22c55e; margin-right: 1rem;"></div>
                <div>
                    <div style="font-weight: 600;">Michael Chen</div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">Yesterday</div>
                </div>
            </li>
        </ul>
        <button class="gradient-btn" style="width: 100%; margin-top: 1rem; padding: 0.6rem; font-size: 0.9rem;">View All</button>
    </div>
</div>
@endsection
