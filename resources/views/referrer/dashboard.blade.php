<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referrer Dashboard | Bynd AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg-primary: #0f172a;
            --bg-secondary: rgba(30, 41, 59, 0.7);
            --accent-primary: #6366f1;
            --accent-secondary: #a855f7;
            --primary-gradient: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.4);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 2rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .header h1 { font-size: 1.8rem; font-weight: 700; }
        .header-actions { display: flex; gap: 1rem; align-items: center; }
        .btn {
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 0.8rem;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }
        .btn:hover { background: rgba(255, 255, 255, 0.1); }
        .btn-gradient {
            background: var(--primary-gradient);
            border: none;
        }
        .btn-gradient:hover { opacity: 0.9; }

        .coupon-banner {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(168, 85, 247, 0.2));
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(12px);
            border-radius: 1rem;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .coupon-box {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(15, 23, 42, 0.8);
            border: 1px dashed var(--accent-primary);
            padding: 0.6rem 1.2rem;
            border-radius: 0.8rem;
        }
        .coupon-code { font-size: 1.4rem; font-weight: 700; letter-spacing: 2px; color: #a855f7; }

        .grid-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--bg-secondary);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            padding: 1.5rem;
            border-radius: 1rem;
        }
        .stat-label { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem; }
        .stat-value { font-size: 1.6rem; font-weight: 700; }

        .section-card {
            background: var(--bg-secondary);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }
        .section-title { font-size: 1.1rem; font-weight: 600; }

        .filter-form {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .filter-form input[type="date"] {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--glass-border);
            border-radius: 0.6rem;
            padding: 0.5rem 0.8rem;
            color: white;
            font-size: 0.85rem;
        }

        table { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem; }
        th { background: rgba(15, 23, 42, 0.5); color: var(--text-secondary); padding: 0.8rem 1rem; font-weight: 600; }
        td { padding: 0.8rem 1rem; border-bottom: 1px solid var(--glass-border); }
        tr:last-child td { border-bottom: none; }

        .badge {
            padding: 0.25rem 0.6rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success { background: rgba(16, 185, 129, 0.2); color: #34d399; }
        .badge-warning { background: rgba(245, 158, 11, 0.2); color: #fbbf24; }

        @media (max-width: 768px) { body { padding: 1rem; } }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Referrer Dashboard</h1>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Welcome back, {{ $referrer->full_name }}</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('referrer.profile') }}" class="btn"><i data-lucide="user"></i> Profile</a>
            <a href="{{ route('referrer.export-csv') }}" class="btn"><i data-lucide="download"></i> Export CSV</a>
            <form action="{{ route('referrer.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn" style="background: rgba(239, 68, 68, 0.2); color: #ef4444; border-color: rgba(239, 68, 68, 0.3);">
                    <i data-lucide="log-out"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Coupon Code Card -->
    <div class="coupon-banner">
        <div>
            <h3 style="margin-bottom: 0.3rem;">Your Unique Referral Coupon</h3>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">
                Share code with users to offer a <strong>{{ $coupon?->discount_value }}{{ $coupon?->discount_type === 'percentage' ? '%' : ' INR' }}</strong> discount!
            </p>
        </div>
        <div class="coupon-box">
            <span class="coupon-code" id="couponCode">{{ $coupon?->code ?? 'N/A' }}</span>
            <button class="btn btn-gradient" onclick="copyCoupon()">
                <i data-lucide="copy"></i> Copy Code
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid-stats">
        <div class="stat-card">
            <div class="stat-label">Total Code Uses</div>
            <div class="stat-value">{{ number_format($totalUses) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Plans Sold</div>
            <div class="stat-value">{{ number_format($totalPlansSold) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Revenue Generated</div>
            <div class="stat-value">₹{{ number_format($totalRevenue, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Commission</div>
            <div class="stat-value" style="color: #a855f7;">₹{{ number_format($totalCommissionEarned, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending Commission</div>
            <div class="stat-value" style="color: #fbbf24;">₹{{ number_format($pendingCommission, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Paid Commission</div>
            <div class="stat-value" style="color: #34d399;">₹{{ number_format($paidCommission, 2) }}</div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="section-card">
        <div class="section-title">Monthly Earnings Overview</div>
        <div style="height: 250px; margin-top: 1rem;">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>

    <!-- Recent Sales Table -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title">Recent Referral Purchases</div>
            <form class="filter-form" method="GET" action="{{ route('referrer.dashboard') }}">
                <input type="date" name="start_date" value="{{ request('start_date') }}">
                <input type="date" name="end_date" value="{{ request('end_date') }}">
                <button type="submit" class="btn"><i data-lucide="filter"></i> Filter</button>
            </form>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Plan</th>
                        <th>Original</th>
                        <th>Discount</th>
                        <th>Final Paid</th>
                        <th>Commission</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $sale->user?->user_name ?? 'Customer' }}</td>
                            <td>{{ $sale->plan?->name ?? 'Plan' }}</td>
                            <td>₹{{ number_format($sale->original_price, 2) }}</td>
                            <td>₹{{ number_format($sale->discount_amount, 2) }}</td>
                            <td>₹{{ number_format($sale->final_amount, 2) }}</td>
                            <td style="color: #a855f7; font-weight: 600;">+₹{{ number_format($sale->commission_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No referral sales recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1rem;">
            {{ $sales->links() }}
        </div>
    </div>

    <script>
        lucide.createIcons();

        function copyCoupon() {
            const code = document.getElementById('couponCode').innerText;
            navigator.clipboard.writeText(code);
            alert('Referral coupon code copied to clipboard!');
        }

        // Render Chart
        const months = {!! json_encode($monthlyEarnings->pluck('month')) !!};
        const commissions = {!! json_encode($monthlyEarnings->pluck('total_commission')) !!};

        const ctx = document.getElementById('earningsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months.length ? months : ['No Data'],
                datasets: [{
                    label: 'Commission (INR)',
                    data: commissions.length ? commissions : [0],
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    </script>
</body>
</html>
