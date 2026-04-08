<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Grid</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="{{ asset('css/backend.css') }}">
    <!-- Fallback if asset is not built/linked correctly -->
    <style>
        :root {
            --bg-primary: #0f172a;
            --bg-secondary: rgba(30, 41, 59, 0.7);
            /* Centralized Primary Accent */
            --accent-primary: #6366f1;
            --accent-secondary: #a855f7;
            --primary-gradient: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));

            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.4);
            --sidebar-width: 240px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top right, #1e1b4b, #0f172a);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .premium-glass {
            background: var(--bg-secondary);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
        }

        .gradient-text {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gradient-btn {
            background: var(--primary-gradient);
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .gradient-btn:hover {
            opacity: 0.9;
        }

        .dashboard-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            border-radius: 0;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            padding: 1.5rem;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        /* Mobile Adjustments */
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1100;
            background: var(--bg-secondary);
            border: 1px solid var(--glass-border);
            color: white;
            padding: 0.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1.5rem;
                padding-top: 4rem;
            }

            .mobile-toggle {
                display: block;
            }
        }

        .nav-link {
            display: flex;
            align-items: center;
            color: var(--text-secondary);
            text-decoration: none;
            padding: 0.7rem 1rem;
            border-radius: 0.8rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
        }

        .nav-link.active i {
            color: var(--accent-primary);
        }
    </style>
    @yield('styles')
</head>

<body>
    @guest
        @yield('content')
    @else
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i data-lucide="menu"></i>
        </button>

        <div class="dashboard-wrapper">
            <!-- Sidebar -->
            <aside class="sidebar premium-glass" id="sidebar">
                <div class="logo-area" style="margin-bottom: 2rem; text-align: center;">
                    <img src="{{ asset('assets/images/logo-white.png') }}" style="width: 80%;" alt="Bynd AI Logo">
                </div>

                <nav>
                    <ul style="list-style: none;">
                        <li>
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i data-lucide="layout-dashboard" style="margin-right: 1rem; width: 18px;"></i>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.notifications.index') }}"
                                class="nav-link {{ request()->routeIs('admin.notifications.index') ? 'active' : '' }}">
                                <i data-lucide="bell" style="margin-right: 1rem; width: 18px;"></i>
                                Notifications
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link">
                                <i data-lucide="users" style="margin-right: 1rem; width: 18px;"></i>
                                Users
                            </a>
                        </li>
                        <li style="margin-top: 1.5rem; margin-bottom: 0.5rem;">
                            <span style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-secondary); letter-spacing: 0.05em; padding-left: 1rem;">Subscription Mgmt</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.plans.index') }}" class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                                <i data-lucide="package" style="margin-right: 1rem; width: 18px;"></i>
                                Subscription Plans
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.features.index') }}" class="nav-link {{ request()->routeIs('admin.features.*') ? 'active' : '' }}">
                                <i data-lucide="list-checks" style="margin-right: 1rem; width: 18px;"></i>
                                Master Features
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.subscriptions.index') }}" class="nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                                <i data-lucide="credit-card" style="margin-right: 1rem; width: 18px;"></i>
                                User Subscriptions
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link">
                                <i data-lucide="settings" style="margin-right: 1rem; width: 18px;"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="logout-area" style="position: absolute; bottom: 1.5rem; width: calc(100% - 3rem);">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); width: 100%; border-radius: 0.8rem; padding: 0.7rem; cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 500;">
                            <i data-lucide="log-out" style="margin-right: 0.5rem; width: 18px;"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="main-content">
                @yield('content')
            </main>
        </div>
    @endguest

    <script>
        lucide.createIcons();

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            if (window.innerWidth <= 768 && sidebar && !sidebar.contains(event.target) && !toggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>
    @yield('scripts')
</body>

</html>