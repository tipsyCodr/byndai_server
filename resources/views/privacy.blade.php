<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy - {{ config('app.name', 'Bynd AI') }}</title>
    <meta name="description" content="Learn how Bynd AI collects, uses, and safeguards your information with on-device processing and strict privacy controls.">

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
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
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

                <a href="{{ url('/terms') }}"
                    class="hidden sm:inline-flex text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors py-2 px-3 rounded-lg hover:bg-slate-100">
                    Terms of Service
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Banner -->
    <div class="relative overflow-hidden bg-gradient-to-b from-sky-50 via-white to-slate-50 border-b border-slate-200/70 pt-12 pb-16">
        <div class="absolute inset-0 pointer-events-none opacity-40">
            <div class="absolute -top-24 -left-20 w-96 h-96 bg-sky-200/50 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 right-0 w-80 h-80 bg-amber-200/40 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sky-600 mb-3">
                <span class="inline-block w-2 h-2 rounded-full bg-sky-500"></span>
                <span>Legal & Transparency</span>
            </div>

            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-950 tracking-tight">
                Privacy Policy
            </h1>
            <p class="mt-3 text-base sm:text-lg text-slate-600 max-w-2xl">
                How ByndAI collects, uses, and safeguards your information when you use our mobile application.
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-3 sm:gap-6 text-xs sm:text-sm text-slate-500">
                <span class="inline-flex items-center gap-1.5 bg-white border border-slate-200 shadow-2xs px-3 py-1.5 rounded-full text-slate-700 font-medium">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Last Updated: February 11, 2026
                </span>
                <span class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-full text-emerald-800 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    On-Device Processing First
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-5xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-10 sm:py-14">

        <!-- Privacy Highlights Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center text-sky-600 mb-3 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 text-sm mb-1">On-Device OCR</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Card extraction uses Google ML Kit right on your phone. Images are never uploaded to remote servers.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 mb-3 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 text-sm mb-1">No Data Selling</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    We do not sell, rent, or trade your contacts or personal information to third-party brokers.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-xs">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-3 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-slate-900 text-sm mb-1">User Control</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Permissions can be granted or revoked at any moment via your standard device system settings.
                </p>
            </div>
        </div>

        <!-- Document Body Card -->
        <article class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-10 lg:p-12 space-y-10 text-slate-700 leading-relaxed">

            <!-- Intro -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-sm sm:text-base text-slate-700">
                <p class="leading-relaxed">
                    <strong>ByndAI</strong> (&quot;we,&quot; &quot;our,&quot; or &quot;us&quot;) is committed to protecting your privacy. This Privacy Policy explains how ByndAI collects, uses, and safeguards your information when you use our mobile application.
                </p>
            </div>

            <!-- Section 1 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">1</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Information We Collect</h2>
                </div>

                <div class="grid grid-cols-1 gap-4 pt-2">
                    <div class="rounded-2xl border border-slate-200 p-5 bg-white space-y-2 hover:border-slate-300 transition-colors">
                        <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                            A. Camera and Images
                        </h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            ByndAI uses your device's camera to capture images of business cards or documents. These images are processed using Optical Character Recognition (OCR) technology to extract contact information.
                        </p>
                        <div class="mt-3 p-3.5 rounded-xl bg-sky-50/70 border border-sky-100 text-xs sm:text-sm text-sky-950 flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span><strong>Processing Guarantee:</strong> OCR processing is performed entirely on-device using Google ML Kit. We do <strong>not</strong> upload your captured images to remote servers for processing.</span>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 p-5 bg-white space-y-2 hover:border-slate-300 transition-colors">
                        <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                            B. Contacts
                        </h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            With your explicit permission, ByndAI may access your device's contacts to:
                        </p>
                        <ul class="list-disc list-inside space-y-1.5 text-sm text-slate-600 ml-2">
                            <li>Save extracted card information directly as a new contact on your phone.</li>
                            <li>Provide a seamless, integrated experience for managing your professional network.</li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-slate-200 p-5 bg-white space-y-2 hover:border-slate-300 transition-colors">
                        <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>
                            C. Local Storage
                        </h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            We use shared preferences and secure storage on your device to store user settings, application state, and potentially encrypted sensitive data. This data remains on your physical device.
                        </p>
                    </div>
                </div>
            </section>

            <hr class="border-slate-200">

            <!-- Section 2 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">2</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">How We Use Your Information</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    We use the information collected solely to provide and enhance the services offered by ByndAI, including:
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium text-slate-800 flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>OCR-based data extraction</span>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium text-slate-800 flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Contact management</span>
                    </div>
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium text-slate-800 flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-sky-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Local data persistence</span>
                    </div>
                </div>
            </section>

            <hr class="border-slate-200">

            <!-- Section 3 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">3</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Data Sharing & Disclosure</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    We do not sell, trade, rent, or otherwise transfer your personal information to outside third parties. Data sharing (via the &quot;Share&quot; feature) is initiated exclusively by you, the user, utilizing standard system share sheets and operating system mechanisms.
                </p>
            </section>

            <hr class="border-slate-200">

            <!-- Section 4 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">4</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Third-Party Services</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    ByndAI utilizes vetted third-party libraries and framework components, including:
                </p>
                <div class="space-y-3">
                    <div class="p-4 rounded-2xl border border-slate-200 bg-white">
                        <span class="font-semibold text-slate-900 text-sm">Google ML Kit:</span>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">Used for high-accuracy on-device text recognition. Google may collect diagnostic and performance metadata in accordance with standard SDK telemetry.</p>
                    </div>
                    <div class="p-4 rounded-2xl border border-slate-200 bg-white">
                        <span class="font-semibold text-slate-900 text-sm">Flutter Plugins:</span>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">Standard open-source plugins facilitating direct native camera access, device storage, and contact access permissions.</p>
                    </div>
                </div>
            </section>

            <hr class="border-slate-200">

            <!-- Section 5 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">5</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Security</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    We implement industry-standard technical measures to maintain the safety and integrity of your information. Sensitive application data is retained using secure, hardware-backed storage mechanisms provided natively by your mobile operating system (such as iOS Keychain and Android EncryptedSharedPreferences).
                </p>
            </section>

            <hr class="border-slate-200">

            <!-- Section 6 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">6</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Your Rights & Choices</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    You maintain complete control over your privacy rights:
                </p>
                <ul class="space-y-2 text-sm text-slate-600">
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Permission Revocation:</strong> You can deny or revoke access to the device camera and contacts at any time through your device system settings.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span><strong>Local Data Removal:</strong> You can purge all locally cached data at any time by clearing the application storage cache or uninstalling ByndAI.</span>
                    </li>
                </ul>
            </section>

            <hr class="border-slate-200">

            <!-- Section 7 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">7</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Changes to This Privacy Policy</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    We may update our Privacy Policy periodically to reflect enhancements or legal obligations. We will notify you of any material changes by updating the &quot;Last Updated&quot; date at the top of this document and hosting the latest version directly at this URL.
                </p>
            </section>

            <hr class="border-slate-200">

            <!-- Section 8 -->
            <section class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-sky-100 text-sky-700 font-bold text-sm">8</span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Contact Us</h2>
                </div>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    If you have questions, feedback, or privacy-related inquiries regarding this policy or our data practices, please reach out to our team:
                </p>

                <div class="mt-4 p-6 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-950 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <p class="text-xs uppercase tracking-wider text-sky-400 font-semibold">ByndAI Privacy Team</p>
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
                <a href="{{ url('/privacy') }}" class="text-sky-400 font-semibold hover:text-sky-300 transition-colors">Privacy Policy</a>
                <a href="{{ url('/terms') }}" class="hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </footer>

</body>

</html>