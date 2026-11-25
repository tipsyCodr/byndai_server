<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms of Service - {{ config('app.name', 'Bynd AI') }}</title>
    <meta name="description" content="Read the terms and conditions governing your use of Bynd AI's mobile application and on-device networking tools.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CDN & Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-sky-500/20 selection:text-sky-900">

    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 transition-opacity hover:opacity-90">
                <img src="{{ asset('assets/images/logo.png') }}" class="h-9 w-auto" alt="Bynd AI" onerror="this.src='{{ asset('assets/images/logo-white.png') }}'; this.classList.add('invert');">
            </a>

            <div class="flex items-center gap-4">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors py-2 px-3 rounded-lg hover:bg-slate-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7" />
                    </svg>
                    <span>Back to Home</span>
                </a>

                <a href="{{ url('/privacy') }}"
                    class="hidden sm:inline-flex text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors py-2 px-3 rounded-lg hover:bg-slate-100">
                    Privacy Policy
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Banner -->
    <div class="relative overflow-hidden bg-gradient-to-b from-sky-50 via-white to-slate-50 border-b border-slate-200/70 pt-12 pb-16">
        <div class="absolute inset-0 pointer-events-none opacity-40">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-sky-200/50 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 right-0 w-80 h-80 bg-indigo-200/40 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sky-600 mb-3">
                <span class="inline-block w-2 h-2 rounded-full bg-sky-500"></span>
                <span>Terms & Agreement</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-950 tracking-tight">
                Terms of Service
            </h1>
            <p class="mt-3 text-base sm:text-lg text-slate-600 max-w-2xl">
                Please read these terms carefully before accessing or using the Bynd AI mobile application.
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-3 sm:gap-6 text-xs sm:text-sm text-slate-500">
                <span class="inline-flex items-center gap-1.5 bg-white border border-slate-200 shadow-2xs px-3 py-1.5 rounded-full text-slate-700 font-medium">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Last Updated: February 11, 2026
                </span>
                <span class="inline-flex items-center gap-1.5 bg-sky-50 border border-sky-200 px-3 py-1.5 rounded-full text-sky-800 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                    Legally Binding
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-5xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10 sm:py-14">

        <!-- Terms Highlights Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 mb-3 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 text-sm mb-1">Lawful Use</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Use ByndAI exclusively for legitimate personal or business networking in accordance with all applicable laws.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mb-3 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 text-sm mb-1">Data Ownership</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    You maintain complete custody and responsibility for any business cards and contact details scanned on your device.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-3 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 text-sm mb-1">IP Protection</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    All app designs, code, brand marks, and underlying OCR integrations are protected under intellectual property laws.
                </p>
            </div>
        </div>

        <!-- Document Body Card -->
        <article class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-10 lg:p-12 space-y-10 text-slate-700 leading-relaxed">

            <!-- Intro -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-sm sm:text-base text-slate-700">
                <p class="leading-relaxed">
                    Welcome to <strong>ByndAI</strong>. By accessing or using our mobile application, you agree to be bound by these Terms of Service. Please read them carefully before using our software.
                </p>
            </div>

            <!-- Section 1 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">1</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Use of Service</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    ByndAI provides on-device contact extraction and management tools. You agree to use the application only for lawful purposes and strictly in accordance with these Terms. You shall not use ByndAI in any manner that could disable, overburden, damage, or impair the application or interfere with any other party's use.
                </p>
            </section>

            <hr class="border-slate-200">

            <!-- Section 2 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">2</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">User Responsibilities</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    You are responsible for maintaining the confidentiality and security of your mobile device and any contact data stored locally within the app. You must ensure that you have the requisite consent or legitimate authority to scan and process any business cards or contact information using our tools.
                </p>
            </section>

            <hr class="border-slate-200">

            <!-- Section 3 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">3</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Intellectual Property</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    The ByndAI application, including its user interface design, logos, brand elements, visual assets, and proprietary technology, is owned by Path Idea InfoTech and protected by applicable copyright, trademark, and intellectual property laws. You may not copy, reproduce, modify, decompile, or reverse engineer any part of the service.
                </p>
            </section>

            <hr class="border-slate-200">

            <!-- Section 4 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">4</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Privacy and Data</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Your use of ByndAI is also governed by our
                    <a href="{{ url('/privacy') }}" class="font-semibold text-sky-600 hover:text-sky-700 underline decoration-sky-300 underline-offset-2">Privacy Policy</a>.
                    Because all card recognition and OCR computations occur directly on your device, you remain the primary custodian and controller of your processed data.
                </p>
            </section>

            <hr class="border-slate-200">

            <!-- Section 5 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">5</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Limitation of Liability</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    ByndAI is provided on an &quot;AS IS&quot; and &quot;AS AVAILABLE&quot; basis without warranties of any kind, whether express or implied. To the maximum extent permitted by applicable law, we shall not be liable for any indirect, incidental, punitive, or consequential damages, or any loss of data or business opportunity resulting from your use of the application.
                </p>
            </section>

            <hr class="border-slate-200">

            <!-- Section 6 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">6</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Changes to Terms</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    We reserve the right to revise these Terms of Service at any time. Any revisions become effective immediately upon posting. Your continued use of ByndAI following the posting of revised Terms confirms your acceptance of those changes.
                </p>
            </section>

            <hr class="border-slate-200">

            <!-- Section 7 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">7</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Contact Us</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    If you have questions, feedback, or legal inquiries concerning these Terms of Service, please reach out to our legal team:
                </p>

                <div class="mt-4 p-6 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-950 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-wider text-sky-400 font-semibold">ByndAI Legal & Support</p>
                        <p class="text-lg font-bold text-white">Path Idea InfoTech</p>
                        <p class="text-sm text-slate-300">Raipur, Chhattisgarh, India - 492001</p>
                    </div>
                    <div class="flex flex-col sm:items-end gap-2">
                        <a href="mailto:info@pathideainfotech.com"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-sm font-semibold transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>info@pathideainfotech.com</span>
                        </a>
                        <span class="text-xs text-slate-400">Response time: within 24-48 business hours</span>
                    </div>
                </div>
            </section>
        </article>
    </main>

    <!-- Footer -->
    <footer class="mt-auto bg-slate-950 text-slate-400 border-t border-slate-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col sm:flex-row items-center justify-between gap-6 text-sm">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/images/logo-white.png') }}" class="h-7 w-auto opacity-80" alt="Bynd AI">
                <span class="text-slate-500">|</span>
                <span>&copy; {{ date('Y') }} Bynd AI. All rights reserved.</span>
            </div>

            <div class="flex items-center gap-6">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a>
                <a href="{{ url('/privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="{{ url('/terms') }}" class="text-sky-400 font-semibold hover:text-sky-300 transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>

</body>

</html>