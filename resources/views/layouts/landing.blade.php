<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bynd AI')</title>
    <meta name="description"
        content="@yield('meta_description', 'AI-powered business networking app for digital cards, lead capture, and event workflows.')">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="bg-white text-slate-900 antialiased">
    <header class="fixed inset-x-0 top-0 z-40 border-b border-white/10 bg-slate-950/75 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
            <a href="#top" class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo-white.png') }}" width='160' alt="ByndAI">

            </a>

            <nav class="hidden items-center gap-8 lg:flex">
                <a href="#features" class="text-sm text-white/80 transition hover:text-white">Features</a>
                <a href="#how-it-works" class="text-sm text-white/80 transition hover:text-white">How it works</a>
                <a href="#demo" class="text-sm text-white/80 transition hover:text-white">Demo</a>
                <a href="#faq" class="text-sm text-white/80 transition hover:text-white">FAQ</a>
                <a href="#contact" class="text-sm text-white/80 transition hover:text-white">Contact</a>
            </nav>

            <div class="flex items-center gap-4">
                <a href="https://play.google.com/store/apps/details?id=com.pathidea.byndai" target="_blank"
                    rel="noreferrer"
                    class="hidden items-center justify-center gap-3 rounded-full bg-amber-400 px-6 py-3.5 text-sm font-semibold text-slate-950 transition duration-200 hover:bg-amber-300 md:inline-flex">
                    Get Bynd AI
                </a>

                <!-- Mobile Menu Button -->
                <button type="button" id="mobile-menu-btn"
                    class="lg:hidden p-2 text-white/80 hover:text-white transition focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div id="mobile-menu"
            class="hidden lg:hidden border-t border-white/10 bg-slate-950/95 backdrop-blur-xl absolute top-full inset-x-0 w-full shadow-2xl">
            <nav class="flex flex-col gap-4 p-6">
                <a href="#features"
                    class="mobile-link block text-base font-semibold text-white/90 hover:text-white transition">Features</a>
                <a href="#how-it-works"
                    class="mobile-link block text-base font-semibold text-white/90 hover:text-white transition">How it
                    works</a>
                <a href="#demo"
                    class="mobile-link block text-base font-semibold text-white/90 hover:text-white transition">Demo</a>
                <a href="#faq"
                    class="mobile-link block text-base font-semibold text-white/90 hover:text-white transition">FAQ</a>
                <a href="#contact"
                    class="mobile-link block text-base font-semibold text-white/90 hover:text-white transition">Contact</a>

                <div class="pt-4 mt-2 border-t border-white/10">
                    <a href="https://play.google.com/store/apps/details?id=com.pathidea.byndai" target="_blank"
                        rel="noreferrer"
                        class="flex items-center justify-center w-full gap-3 rounded-full bg-amber-400 px-6 py-3.5 text-base font-semibold text-slate-950 transition duration-200 hover:bg-amber-300">
                        Get Bynd AI
                    </a>
                </div>
            </nav>
        </div>
    </header>

    @yield('content')

    <footer id="contact" class="bg-slate-950 text-white">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
            <div class="grid gap-10 md:grid-cols-2 xl:grid-cols-5">
                <div class="space-y-4 xl:col-span-1">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-200">About</p>
                    <p class="text-2xl font-semibold tracking-tight"> <img
                            src="{{ asset('assets/images/logo-white.png') }}" width='160' alt="ByndAI">
                    </p>
                    <p class="text-sm leading-7 text-slate-300">
                        AI-powered business networking built to help professionals share, capture, and grow
                        relationships more efficiently.
                    </p>
                    <p class="text-sm text-slate-400">Powered by Path Idea InfoTech</p>
                    <p class="text-sm text-slate-400">Founded February 14, 2026</p>
                </div>

                <div class="space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-200">Quick links</p>
                    <a href="#features" class="block text-sm text-slate-300 transition hover:text-white">Features</a>
                    <a href="#how-it-works" class="block text-sm text-slate-300 transition hover:text-white">How it
                        works</a>
                    <a href="#pricing" class="block text-sm text-slate-300 transition hover:text-white">Pricing</a>
                    <a href="#faq" class="block text-sm text-slate-300 transition hover:text-white">FAQ</a>
                    <a href="#contact" class="block text-sm text-slate-300 transition hover:text-white">Contact us</a>
                </div>

                <div class="space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-200">Legal</p>
                    <a href="https://byndai.in" target="_blank" rel="noreferrer"
                        class="block text-sm text-slate-300 transition hover:text-white">Privacy Policy</a>
                    <a href="https://byndai.in" target="_blank" rel="noreferrer"
                        class="block text-sm text-slate-300 transition hover:text-white">Terms of Service</a>
                    <a href="https://byndai.in" target="_blank" rel="noreferrer"
                        class="block text-sm text-slate-300 transition hover:text-white">Refund Policy</a>
                    <a href="https://byndai.in" target="_blank" rel="noreferrer"
                        class="block text-sm text-slate-300 transition hover:text-white">GDPR Compliance</a>
                </div>

                <div class="space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-200">Contact</p>
                    <a href="tel:+919039055005"
                        class="block text-sm leading-7 text-slate-300 transition hover:text-white">+91 90390 55005</a>
                    <a href="https://wa.me/919039055005" target="_blank" rel="noreferrer"
                        class="block text-sm leading-7 text-slate-300 transition hover:text-white">WhatsApp support</a>
                    <a href="mailto:info@byndai.com"
                        class="block text-sm leading-7 text-slate-300 transition hover:text-white">info@byndai.com</a>
                    <p class="text-sm leading-7 text-slate-400">
                        H Block, 2nd Floor, Farishta Complex, Rajbandha Maidan, Raipur, Chhattisgarh, India - 492001
                    </p>
                </div>

                <div class="space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-200">Connect</p>
                    <a href="https://www.linkedin.com/showcase/byndaiofficial" target="_blank" rel="noreferrer"
                        class="block text-sm text-slate-300 transition hover:text-white">LinkedIn</a>
                    <a href="https://www.instagram.com/byndaiofficial" target="_blank" rel="noreferrer"
                        class="block text-sm text-slate-300 transition hover:text-white">Instagram</a>
                    <a href="https://www.facebook.com/ByndAIOfficial" target="_blank" rel="noreferrer"
                        class="block text-sm text-slate-300 transition hover:text-white">Facebook</a>
                    <a href="https://share.google/UIXRThyhXDkJGDeVA" target="_blank" rel="noreferrer"
                        class="block text-sm text-slate-300 transition hover:text-white">Google My Business</a>

                </div>
            </div>

            <div
                class="mt-12 flex flex-col gap-4 border-t border-white/10 pt-6 text-sm text-slate-400 md:flex-row md:items-center md:justify-between">
                <p>Copyright © {{ date('Y') }} Bynd AI by Path Idea InfoTech. All rights reserved.</p>
                <p>Google Play listing live | Founder-led launch | Built for early adopters</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const menu = document.getElementById('mobile-menu');
            const links = document.querySelectorAll('.mobile-link');

            if (btn && menu) {
                btn.addEventListener('click', () => {
                    menu.classList.toggle('hidden');
                });

                // Close menu when a link is clicked
                links.forEach(link => {
                    link.addEventListener('click', () => {
                        menu.classList.add('hidden');
                    });
                });

                // Close menu when clicking outside
                document.addEventListener('click', (e) => {
                    if (!menu.contains(e.target) && !btn.contains(e.target)) {
                        menu.classList.add('hidden');
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>