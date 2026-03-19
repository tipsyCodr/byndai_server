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
        /* Embedding essential styles for immediate experience */
        :root {
            --bg-primary: #0f172a;
            --bg-secondary: rgba(30, 41, 59, 0.7);
            --accent-primary: #6366f1;
            --accent-secondary: #a855f7;
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
            overflow-x: hidden;
        }
        .premium-glass {
            background: var(--bg-secondary);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
        }
    </style>
    @yield('styles')
</head>
<body>
    @guest
        @yield('content')
    @else
        <div class="dashboard-wrapper" style="display: flex; min-height: 100vh;">
            <!-- Simple Premium Sidebar -->
            <aside class="sidebar premium-glass" style="width: 250px; border-radius: 0; position: sticky; top: 0; height: 100vh; padding: 2rem;">
                <div class="logo-area" style="margin-bottom: 3rem; text-align: center;">
                    <h2 class="gradient-text" style="font-size: 2rem; font-weight: 700; letter-spacing: -1px;">GRID</h2>
                </div>
                
                <nav>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 1rem;">
                            <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; color: var(--text-primary); text-decoration: none; padding: 0.8rem 1rem; border-radius: 0.8rem; background: rgba(255,255,255,0.05);">
                                <i data-lucide="layout-dashboard" style="margin-right: 1rem; width: 20px;"></i>
                                Dashboard
                            </a>
                        </li>
                        <!-- Mock nav items -->
                        <li style="margin-bottom: 1rem;">
                            <a href="#" style="display: flex; align-items: center; color: var(--text-secondary); text-decoration: none; padding: 0.8rem 1rem; opacity: 0.7;">
                                <i data-lucide="users" style="margin-right: 1rem; width: 20px;"></i>
                                Users
                            </a>
                        </li>
                        <li style="margin-bottom: 1rem;">
                            <a href="#" style="display: flex; align-items: center; color: var(--text-secondary); text-decoration: none; padding: 0.8rem 1rem; opacity: 0.7;">
                                <i data-lucide="settings" style="margin-right: 1rem; width: 20px;"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="logout-area" style="position: absolute; bottom: 2rem; width: calc(100% - 4rem);">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); width: 100%; border-radius: 0.8rem; padding: 0.8rem; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="log-out" style="margin-right: 0.5rem; width: 18px;"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main style="flex: 1; padding: 3rem; position: relative;">
                @yield('content')
            </main>
        </div>
    @endguest

    <script>
        lucide.createIcons();
    </script>
    @yield('scripts')
</body>
</html>
