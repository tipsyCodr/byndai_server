<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms of Service - {{ config('app.name', 'ByndAI') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>

<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-[#EDEDEC] min-h-screen flex flex-col p-6 lg:p-20">
    <header class="max-w-4xl mx-auto w-full mb-12">
        <a href="{{ url('/') }}"
            class="text-[#1ea9ff] dark:text-[#1ea9ff] font-medium hover:underline inline-flex items-center gap-2">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6" />
            </svg>
            Back to Home
        </a>
    </header>

    <main
        class="max-w-4xl mx-auto w-full bg-white dark:bg-[#161615] p-8 lg:p-12 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg">
        <h1 class="text-3xl font-bold mb-2">Terms of Service</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A] mb-8">Last Updated: February 11, 2026</p>

        <div class="prose dark:prose-invert max-w-none space-y-8 text-[15px] leading-relaxed">
            <p>Welcome to ByndAI. By accessing or using our mobile application, you agree to be bound by these Terms of
                Service. Please read them carefully.</p>

            <section>
                <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">1. Use of Service</h2>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">ByndAI provides on-device contact extraction and
                    management tools. You agree to use the application only for lawful purposes and in accordance with
                    these Terms.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">2. User Responsibilities</h2>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">You are responsible for maintaining the confidentiality of
                    your device and any data stored locally within the app. You must ensure that you have the right to
                    process any business cards or contact information you scan using our tools.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">3. Intellectual Property</h2>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">The ByndAI application, including its design, logos, and
                    proprietary technology, is owned by we and protected by intellectual property laws. You may not
                    copy, modify, or reverse engineer any part of the service.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">4. Privacy and Data</h2>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">Your use of ByndAI is also governed by our <a
                        href="{{ url('/privacy') }}" class="text-[#1ea9ff] hover:underline">Privacy Policy</a>. As the
                    processing is done on-device, you remain the primary custodian of your data.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">5. Limitation of Liability
                </h2>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">ByndAI is provided "as is" without any warranties. We
                    shall not be liable for any data loss, device issues, or indirect damages arising from your use of
                    the application.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">6. Changes to Terms</h2>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">We reserve the right to update these Terms at any time.
                    Your continued use of ByndAI after changes are posted constitutes acceptance of the new Terms.</p>
            </section>

            <section>
                <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">7. Contact Us</h2>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">If you have any questions about these Terms, please
                    contact us at:</p>
                <p class="mt-2 font-medium text-[#1ea9ff] dark:text-[#1ea9ff]">info@pathideainfotech.com</p>
            </section>
        </div>
    </main>

    <footer
        class="max-w-4xl mx-auto w-full mt-12 flex flex-col md:flex-row justify-between items-center gap-6 border-t border-[#19140035] dark:border-[#3e3e3a] py-12">
        <div class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
            &copy; {{ date('Y') }} ByndAI. All rights reserved.
        </div>
        <div class="flex gap-8 text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">
            <a href="{{ url('/privacy') }}" class="hover:text-[#1ea9ff] transition-colors">Privacy Policy</a>
            <a href="{{ url('/terms') }}" class="hover:text-[#1ea9ff] transition-colors font-bold text-[#1ea9ff]">Terms
                of Service</a>
        </div>
    </footer>
</body>

</html>
