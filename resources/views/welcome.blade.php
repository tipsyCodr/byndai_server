<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ByndAI - Intelligent Contact Management</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Outfit:300,400,500,600,700" rel="stylesheet" />
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .gradient-text {
            background: linear-gradient(135deg, #1d213e 0%, #1ea9ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-bg {
            background-image: radial-gradient(circle at 50% 50%, rgba(30, 169, 255, 0.1) 0%, transparent 50%);
        }
    </style>
</head>

<body class="bg-[#FDFDFC] text-[#1b1b18] transition-colors duration-300">
    <div class="relative overflow-hidden min-h-screen">
        <!-- Background Decoration -->
        <div class="absolute top-0 left-0 w-full h-full hero-bg pointer-events-none"></div>

        <!-- Navbar -->
        <nav class="relative z-10 max-w-7xl mx-auto px-6 py-8 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <img src="{{ asset('assets/images/logo.png') }}" alt="ByndAI Logo" class="h-12 w-auto">
            </div>
            <div class="hidden md:flex items-center gap-8 font-medium">
                <a href="#features" class="hover:text-[#1ea9ff] transition-colors">Features</a>
                {{-- @if (Route::has('login'))
                @auth
                <a href="{{ url('/dashboard') }}"
                    class="px-6 py-2 bg-[#1b1b18] text-white rounded-full hover:scale-105 transition-transform">Dashboard</a>
                @else
                <a href="{{ route('login') }}" class="hover:text-[#f53003] transition-colors">Log in</a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}"
                    class="px-6 py-2 bg-[#1b1b18] text-white rounded-full hover:scale-105 transition-transform shadow-lg">Get
                    Started</a>
                @endif
                @endauth
                @endif --}}
            </div>
        </nav>

        <!-- Hero Section -->
        <main
            class="relative z-10 max-w-7xl mx-auto px-6 pt-20 pb-32 text-center md:text-left flex flex-col md:flex-row items-center gap-16">
            <div class="flex-1 space-y-8">
                <div
                    class="inline-block px-4 py-1 rounded-full bg-[#1ea9ff]/10 text-[#1ea9ff] text-sm font-semibold tracking-wider uppercase">
                    AI-Powered Productivity
                </div>
                <h1 class="text-6xl md:text-7xl font-extrabold tracking-tight leading-[1.1]">
                    Streamline Your <br />
                    <span class="gradient-text">Connections</span>
                </h1>
                <p class="text-xl text-[#706f6c] max-w-xl leading-relaxed">
                    ByndAI transforms business cards into digital assets instantly. Capture, organize, and grow your
                    professional network with on-device artificial intelligence.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 pt-4">
                    <a href="https://play.google.com/store/apps/details?id=com.pathidea.byndai&pcampaignid=web_share"
                        target="_blank" rel="noopener noreferrer"
                        class="hover:-translate-y-1 transition-transform inline-block">
                        <img src="{{ asset('assets/images/google-badge.svg') }}" alt="Google Play" class="h-14 w-auto">
                    </a>
                    <a href="#features"
                        class="px-8 py-4 glass rounded-2xl font-bold text-lg hover:bg-white/10 transition-all flex items-center justify-center gap-2 border border-[#3e3e3a]">
                        Learn More
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Hero Image Placeholder -->
            <div class="flex-1 relative w-full max-w-md md:max-w-none">
                <div
                    class="aspect-square rounded-[3rem] bg-gradient-to-br from-[#1d213e] to-[#1ea9ff]/20 p-4 shadow-2xl relative overflow-hidden group">
                    <img src="{{ asset('assets/images/banner/banner.png') }}" alt="ByndAI App"
                        class="w-full h-full object-cover rounded-[2.5rem] opacity-90 group-hover:scale-105 transition-duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex items-end p-12">
                        <div class="glass p-6 rounded-3xl w-full">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-[#1ea9ff] flex items-center justify-center">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white"
                                        stroke-width="2">
                                        <path d="M12 2v20M2 12h20" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white">Smart Scan</p>
                                    <p class="text-sm text-white/70">Card detected & verified</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Features Section -->
        <section id="features"
            class="relative z-10 max-w-7xl mx-auto px-6 py-32 border-t border-[#19140035]">
            <div class="text-center space-y-4 mb-20">
                <h2 class="text-4xl font-bold tracking-tight">Powerful Capabilities</h2>
                <p class="text-[#706f6c] max-w-2xl mx-auto italic text-lg">
                    Designed for professionals who value speed and privacy.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div
                    class="p-8 rounded-[2.5rem] glass hover:bg-[#1ea9ff]/5 transition-all duration-300 border border-transparent hover:border-[#1ea9ff]/20">
                    <div
                        class="w-14 h-14 bg-[#1ea9ff]/10 text-[#1ea9ff] rounded-2xl flex items-center justify-center mb-6">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <path d="m21 15-5-5L5 21" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">AI-Powered OCR</h3>
                    <p class="text-[#706f6c] leading-relaxed">
                        Advanced text recognition extracts contact details from business cards in seconds with
                        unparalleled accuracy.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div
                    class="p-8 rounded-[2.5rem] glass hover:bg-[#1ea9ff]/5 transition-all duration-300 border border-transparent hover:border-[#1ea9ff]/20">
                    <div
                        class="w-14 h-14 bg-[#1ea9ff]/10 text-[#1ea9ff] rounded-2xl flex items-center justify-center mb-6">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Seamless Integration</h3>
                    <p class="text-[#706f6c] leading-relaxed">
                        Save extracted information directly to your device contacts or manage them within our intuitive
                        interface.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div
                    class="p-8 rounded-[2.5rem] glass hover:bg-[#1ea9ff]/5 transition-all duration-300 border border-transparent hover:border-[#1ea9ff]/20">
                    <div
                        class="w-14 h-14 bg-[#1ea9ff]/10 text-[#1ea9ff] rounded-2xl flex items-center justify-center mb-6">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Privacy First</h3>
                    <p class="text-[#706f6c] leading-relaxed">
                        All processing is done locally on your device. We never upload your images or contact data to
                        our servers.
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer
            class="relative z-10 max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row justify-between items-center gap-6 border-t border-[#19140035]">
            <div class="flex items-center gap-2 opacity-80">
                <img src="{{ asset('assets/images/logo.png') }}" alt="ByndAI Logo" class="h-8 w-auto">
                <span class="text-sm text-[#706f6c]">© {{ date('Y') }} ByndAI.</span>
            </div>
            <div class="flex gap-8 text-sm font-medium text-[#706f6c]">
                <a href="{{ url('/privacy') }}" class="hover:text-[#1ea9ff] transition-colors">Privacy Policy</a>
                <a href="{{ url('/terms') }}" class="hover:text-[#1ea9ff] transition-colors">Terms of Service</a>
                <a href="mailto:info@pathideainfotech.com" class="hover:text-[#1ea9ff] transition-colors">Contact</a>
            </div>
        </footer>
    </div>
</body>

</html>