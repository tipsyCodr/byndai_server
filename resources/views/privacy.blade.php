<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Privacy Policy - {{ config('app.name', 'ByndAI') }}</title>
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
            <a href="{{ url('/') }}" class="text-[#f53003] dark:text-[#FF4433] font-medium hover:underline inline-flex items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                Back to Home
            </a>
        </header>

        <main class="max-w-4xl mx-auto w-full bg-white dark:bg-[#161615] p-8 lg:p-12 shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg">
            <h1 class="text-3xl font-bold mb-2">Privacy Policy</h1>
            <p class="text-[#706f6c] dark:text-[#A1A09A] mb-8">Last Updated: February 11, 2026</p>

            <div class="prose dark:prose-invert max-w-none space-y-8 text-[15px] leading-relaxed">
                <p>ByndAI ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how ByndAI collects, uses, and safeguards your information when you use our mobile application.</p>

                <section>
                    <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">1. Information We Collect</h2>
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-medium mb-1">A. Camera and Images</h3>
                            <p class="text-[#706f6c] dark:text-[#A1A09A]">ByndAI uses your device's camera to capture images of business cards or documents. These images are processed using Optical Character Recognition (OCR) technology to extract contact information.</p>
                            <ul class="list-disc ml-6 mt-2 text-[#706f6c] dark:text-[#A1A09A]">
                                <li><strong>Processing:</strong> OCR processing is performed on-device using Google ML Kit. We do not upload your images to our servers for processing.</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-medium mb-1">B. Contacts</h3>
                            <p class="text-[#706f6c] dark:text-[#A1A09A]">With your explicit permission, ByndAI may access your device's contacts to:</p>
                            <ul class="list-disc ml-6 mt-1 text-[#706f6c] dark:text-[#A1A09A]">
                                <li>Save extracted card information as a new contact.</li>
                                <li>Provide a seamless experience for managing your professional network.</li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="font-medium mb-1">C. Local Storage</h3>
                            <p class="text-[#706f6c] dark:text-[#A1A09A]">We use shared preferences and secure storage on your device to store user settings, application state, and potentially encrypted sensitive data. This data remains on your device.</p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">2. How We Use Your Information</h2>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">We use the information collected solely to provide the services offered by ByndAI, including:</p>
                    <ul class="list-disc ml-6 mt-2 text-[#706f6c] dark:text-[#A1A09A]">
                        <li>OCR-based data extraction.</li>
                        <li>Contact management.</li>
                        <li>Local data persistence for user convenience.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">3. Data Sharing</h2>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">We do not sell, trade, or otherwise transfer your personal information to outside parties. Data sharing (via the "Share" feature) is initiated only by you, the user, using standard system components.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">4. Third-Party Services</h2>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">ByndAI utilizes third-party libraries and services, including:</p>
                    <ul class="list-disc ml-6 mt-2 text-[#706f6c] dark:text-[#A1A09A]">
                        <li><strong>Google ML Kit:</strong> Used for on-device text recognition. Google may collect diagnostic data as part of these services.</li>
                        <li><strong>Flutter Plugins:</strong> Various community plugins for camera, storage, and contacts access.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">5. Security</h2>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">We implement a variety of security measures to maintain the safety of your information. Sensitive data is stored using secure storage mechanisms provided by the operating system.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">6. Your Rights</h2>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">You have the right to:</p>
                    <ul class="list-disc ml-6 mt-2 text-[#706f6c] dark:text-[#A1A09A]">
                        <li>Deny or revoke access to camera and contacts at any time through your device settings.</li>
                        <li>Delete locally stored data by clearing the application's cache or uninstalling the app.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">7. Changes to This Privacy Policy</h2>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
                </section>

                <section>
                    <h2 class="text-xl font-semibold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">8. Contact Us</h2>
                    <p class="text-[#706f6c] dark:text-[#A1A09A]">If you have any questions regarding this Privacy Policy, you may contact us at:</p>
                    <p class="mt-2 font-medium text-[#f53003] dark:text-[#FF4433]">info@pathideainfotech.com</p>
                </section>
            </div>
        </main>

        <footer class="max-w-4xl mx-auto w-full mt-12 text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
            &copy; {{ date('Y') }} ByndAI. All rights reserved.
        </footer>
    </body>
</html>
