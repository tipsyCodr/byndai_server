@extends('layouts.landing')

@section('title', 'Bynd AI - AI-Powered Business Networking App | Download Free')
@section('meta_description', 'Transform your business networking with Bynd AI. Discover AI-powered lead capture, digital business cards, event workflows, and mini business websites.')

@section('content')
    @php
        $playStoreLink = 'https://play.google.com/store/apps/details?id=com.pathidea.byndai';
        $whatsappLink = 'https://wa.me/919039055005';
        $websiteLink = 'https://byndai.in';
        $linkedinLink = 'https://www.linkedin.com/showcase/byndaiofficial';
        $instagramLink = 'https://www.instagram.com/byndaiofficial';
        $googleBusinessLink = 'https://share.google/UIXRThyhXDkJGDeVA';
        $qrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . urlencode($playStoreLink);
        $trustSignals = ['Available on Google Play for Android users', 'Founded on February 14, 2026 and built for early adopters', 'Powered by Path Idea InfoTech and proudly made in India'];
        $socialSignals = [
            ['title' => 'Verified business presence', 'text' => 'Google Business profile, official website, and local contact details make Bynd AI easy to verify before you download.'],
            ['title' => 'Founder-led product vision', 'text' => 'Built under the leadership of Mr. Pramod Sahu to solve real networking friction for Indian businesses and event communities.'],
            ['title' => 'Always-on brand channels', 'text' => 'LinkedIn, Instagram, and Facebook are live so new users can follow the launch journey and stay close to product updates.'],
        ];
        $problemPoints = ['Paper business cards disappear before follow-up begins', 'Lead data must be typed manually after every meeting', 'Hot prospects go cold because reminders are scattered', 'Most professionals still lack a shareable digital presence', 'Event networking stops the moment people leave the venue'];
        $solutionPoints = ['Share digital business cards instantly with QR and links', 'Capture lead details with AI-assisted workflows in seconds', 'Keep follow-up actions visible with organized contact history', 'Launch a mini business website without extra development cost', 'Keep networking active beyond physical meetings and events'];
        $features = [
            ['number' => '01', 'title' => 'AI-powered business networking platform', 'description' => 'Match with relevant professionals, surface shared interests, and keep conversations moving even after the event ends.', 'benefit' => 'Grow your network with more intent and less guesswork.'],
            ['number' => '02', 'title' => 'AI-powered lead capture tool', 'description' => 'Collect lead details quickly at expos, meetings, and networking sessions so no valuable contact slips away.', 'benefit' => 'Turn event conversations into trackable business opportunities.'],
            ['number' => '03', 'title' => 'AI-powered business profile builder', 'description' => 'Create a polished profile, sharpen your positioning, and present your brand clearly with guided AI support.', 'benefit' => 'Show up professionally even if you do not have a full website yet.'],
            ['number' => '04', 'title' => 'AI-powered business card scanner', 'description' => 'Digitize paper cards and move contact information into a cleaner system without repetitive manual entry.', 'benefit' => 'Organize contacts faster and stay ready for follow-up.'],
            ['number' => '05', 'title' => 'Digital business card and QR connect', 'description' => 'Share your profile instantly with a personal QR code, direct link, or mobile-first digital visiting card.', 'benefit' => 'Make every introduction easy, memorable, and paper-free.'],
            ['number' => '06', 'title' => 'Business feed', 'description' => 'Stay close to updates, opportunities, and visibility inside your professional network from one feed.', 'benefit' => 'Spot collaboration signals earlier and keep your brand visible.'],
            ['number' => '07', 'title' => 'Event management', 'description' => 'Plan, manage, and measure networking moments from attendee touchpoints to follow-up outcomes.', 'benefit' => 'Get more business value from every event you host or attend.'],
            ['number' => '08', 'title' => 'Occasional and greeting flyer creator', 'description' => 'Create business greetings, festive creatives, and promotional materials without leaving the platform.', 'benefit' => 'Stay top of mind with less design effort.'],
            ['number' => '09', 'title' => 'Mini business website', 'description' => 'Give prospects a mobile-ready page for your business, services, and contact information the moment they discover you.', 'benefit' => 'Build online credibility instantly without a separate website project.'],
        ];
        $steps = [
            ['number' => '1', 'title' => 'Download and sign up', 'text' => 'Install Bynd AI from Google Play and create your account in a few quick steps.'],
            ['number' => '2', 'title' => 'Build your profile', 'text' => 'Set up your digital business card, profile, and mini website so every introduction looks professional.'],
            ['number' => '3', 'title' => 'Start networking', 'text' => 'Share your QR code, capture leads, follow up with confidence, and keep business conversations alive.'],
        ];
        $screens = [
            ['eyebrow' => 'Screen 1', 'title' => 'Fast onboarding', 'summary' => 'Help users understand the value instantly and get them from install to profile setup without friction.', 'stat' => '30 sec setup'],
            ['eyebrow' => 'Screen 2', 'title' => 'Digital business card', 'summary' => 'Share one polished identity across meetings, events, and follow-up messages.', 'stat' => 'QR ready'],
            ['eyebrow' => 'Screen 3', 'title' => 'Lead capture flow', 'summary' => 'Record prospect information while the conversation is still fresh and actionable.', 'stat' => 'AI assisted'],
            ['eyebrow' => 'Screen 4', 'title' => 'Business feed', 'summary' => 'Keep opportunities visible with a stream designed for professional updates instead of noise.', 'stat' => 'Live updates'],
            ['eyebrow' => 'Screen 5', 'title' => 'Event management dashboard', 'summary' => 'Coordinate registration, attendee interactions, and business outcomes from a single event view.', 'stat' => 'ROI focused'],
            ['eyebrow' => 'Screen 6', 'title' => 'Mini website and flyers', 'summary' => 'Create a stronger digital presence and marketing collateral without juggling multiple tools.', 'stat' => 'Ready to publish'],
        ];
        $comparisonRows = [
            ['feature' => 'Business card exchange', 'traditional' => 'Paper cards that are easy to lose', 'generic' => 'Basic profile share with limited context', 'bynd' => 'Digital cards, QR sharing, and richer business identity'],
            ['feature' => 'Lead management', 'traditional' => 'Manual note-taking and spreadsheet entry', 'generic' => 'Contacts without workflow support', 'bynd' => 'AI-assisted lead capture and organized follow-up flow'],
            ['feature' => 'Professional web presence', 'traditional' => 'Separate website project and added cost', 'generic' => 'Profile page only', 'bynd' => 'Mini business website built into the app experience'],
            ['feature' => 'Event networking', 'traditional' => 'Scattered attendee info and delayed follow-up', 'generic' => 'Limited event workflow coverage', 'bynd' => 'Integrated event management and relationship continuity'],
            ['feature' => 'Brand visibility', 'traditional' => 'Manual outreach and design requests', 'generic' => 'Minimal content tools', 'bynd' => 'Business feed plus flyer creation in one platform'],
        ];
        $useCases = [
            ['title' => 'Entrepreneurs', 'text' => 'Build a credible network from day one and keep every new contact connected to your growing business story.'],
            ['title' => 'Sales professionals', 'text' => 'Capture leads in real time, tag buying intent, and keep the next follow-up clear while the opportunity is warm.'],
            ['title' => 'Business owners', 'text' => 'Expand your customer base, present a professional brand, and avoid the chaos of scattered networking data.'],
            ['title' => 'Consultants', 'text' => 'Package your expertise in a polished digital profile that makes it easier for prospects to understand and trust your offer.'],
            ['title' => 'Event organizers', 'text' => 'Keep attendee engagement, lead flow, and event visibility tied together in a more measurable system.'],
            ['title' => 'Freelancers', 'text' => 'Look more established online, share your profile instantly, and turn casual introductions into real business conversations.'],
        ];
        $faqs = [
            ['question' => 'Is Bynd AI really free?', 'answer' => 'Yes. Bynd AI is available to download from Google Play at no cost, so professionals can get started without a credit card barrier.'],
            ['question' => 'How is Bynd AI different from other networking apps?', 'answer' => 'Bynd AI brings business networking, digital cards, lead capture, event workflows, flyers, and a mini website into one experience with an India-first business focus.'],
            ['question' => 'Is my business data secure?', 'answer' => 'The product is positioned around trusted business use. This landing page includes privacy-first messaging and contact transparency, while production security controls should be validated directly with the Bynd AI team.'],
            ['question' => 'Can I use Bynd AI for events?', 'answer' => 'Yes. Event-related workflows are part of the platform vision, helping organizers and attendees capture contacts and continue conversations after the event.'],
            ['question' => 'Do I need a website if I have Bynd AI?', 'answer' => 'Bynd AI includes a mini business website concept so you can share a professional presence quickly, even if your full website is still in progress.'],
            ['question' => 'How does the AI business card scanner work?', 'answer' => 'The app is positioned to digitize card details with AI-assisted recognition so users can reduce manual entry and keep contacts organized faster.'],
            ['question' => 'Can I integrate with other tools?', 'answer' => 'The current page does not claim specific integrations. If integrations are on the roadmap, the landing page is already structured to support that future messaging.'],
            ['question' => 'Is there customer support?', 'answer' => 'Yes. Visitors can reach the team through phone, WhatsApp, email, the website, and official social channels listed on the page.'],
        ];
        $activityFeed = ['A business owner in Raipur just opened the Play Store listing.', 'A sales professional is exploring the digital card workflow.', 'An event organizer is checking the event management demo.', 'A consultant is viewing the mini website feature.'];
    @endphp

    <main id="top" class="pt-20">
        <section class="relative overflow-hidden bg-slate-950 text-white">
            <div class="absolute inset-0">
                <div
                    class="absolute left-1/2 top-0 h-[44rem] w-[44rem] -translate-x-1/2 rounded-full bg-sky-500/20 blur-3xl">
                </div>
                <div class="absolute right-0 top-24 h-80 w-80 rounded-full bg-violet-500/20 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 h-80 w-80 rounded-full bg-cyan-500/10 blur-3xl"></div>
            </div>
            <div class="relative mx-auto max-w-7xl px-6 pb-24 pt-12 lg:px-8 lg:pb-28 lg:pt-16">
                <div class="grid gap-14 lg:grid-cols-[1.06fr_0.94fr] lg:items-center">
                    <div class="space-y-8">
                        <div class="space-y-5">
                            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-sky-200">Powered by Path Idea
                                InfoTech</p>
                            <h1 class="max-w-4xl text-5xl font-black tracking-[-0.04em] text-white md:text-7xl"><span
                                    class="block">Bynd AI</span><span
                                    class="mt-4 block max-w-3xl text-3xl font-semibold leading-tight tracking-[-0.03em] text-slate-100 md:text-5xl">Turn
                                    every handshake into a qualified business pipeline.</span></h1>
                            <p class="max-w-2xl text-lg leading-8 text-slate-300 md:text-xl">Bynd AI helps Indian
                                professionals exchange digital business cards, capture leads instantly, and follow up faster
                                from one AI-powered networking app.</p>
                        </div>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                            <a href="{{ $playStoreLink }}" target="_blank" rel="noreferrer"
                                class="inline-flex min-w-[220px] items-center justify-center gap-3 rounded-full bg-amber-400 px-6 py-3.5 text-sm font-semibold text-slate-950 transition duration-200 hover:bg-amber-300">Download
                                Free Now</a>
                            <a href="#demo"
                                class="inline-flex items-center justify-center gap-3 rounded-full border border-white/15 px-6 py-3.5 text-sm font-semibold text-white transition hover:border-white/35 hover:bg-white/5">See
                                2-minute walkthrough</a>
                        </div>
                        <div class="grid gap-6 border-t border-white/10 pt-6 sm:grid-cols-3">
                            @foreach ($trustSignals as $signal)
                                <div class="space-y-2">
                                    <div class="h-px w-10 bg-gradient-to-r from-sky-400 to-violet-500"></div>
                                    <p class="text-sm leading-6 text-slate-300">{{ $signal }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex flex-wrap gap-x-6 gap-y-3 text-sm text-slate-300">
                            <a href="{{ $websiteLink }}" target="_blank" rel="noreferrer"
                                class="transition hover:text-white">Website</a>
                            <a href="{{ $googleBusinessLink }}" target="_blank" rel="noreferrer"
                                class="transition hover:text-white">Google Business</a>
                            <a href="{{ $linkedinLink }}" target="_blank" rel="noreferrer"
                                class="transition hover:text-white">LinkedIn</a>
                            <a href="{{ $instagramLink }}" target="_blank" rel="noreferrer"
                                class="transition hover:text-white">Instagram</a>
                        </div>
                    </div>

                    <div class="relative mx-auto w-full max-w-[560px]">
                        <div class="absolute left-6 top-20 hidden h-40 w-40 rounded-full bg-sky-400/20 blur-3xl md:block">
                        </div>
                        <div
                            class="absolute bottom-16 right-4 hidden h-36 w-36 rounded-full bg-violet-500/25 blur-3xl md:block">
                        </div>
                        <div class="relative">
                            <div class="absolute inset-x-6 top-10 h-[26rem] rounded-full bg-sky-500/20 blur-[120px]"></div>
                            <div
                                class="relative mx-auto w-full max-w-[380px] rounded-[3rem] border border-white/10 bg-white/6 p-3 shadow-[0_50px_120px_-45px_rgba(59,130,246,0.8)] backdrop-blur animate-float">
                                <div class="rounded-[2.5rem] border border-white/10 bg-slate-950 p-3">
                                    <div
                                        class="overflow-hidden rounded-[2rem] bg-gradient-to-b from-slate-100 via-white to-sky-50 text-slate-950">
                                        <div class="bg-slate-950 px-5 py-4 text-white">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="text-xs uppercase tracking-[0.24em] text-sky-200">Bynd AI
                                                        network</p>
                                                    <h2 class="mt-2 text-lg font-semibold">Your next lead should never be
                                                        lost.</h2>
                                                </div>
                                                <div
                                                    class="h-11 w-11 rounded-2xl bg-gradient-to-br from-sky-400 to-violet-500">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="space-y-4 p-5">
                                            <div
                                                class="rounded-[1.6rem] bg-white p-4 shadow-[0_20px_70px_-50px_rgba(15,23,42,0.8)]">
                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                                        <p class="text-xs uppercase tracking-[0.22em] text-slate-400">
                                                            Digital business card</p>
                                                        <p class="mt-2 text-lg font-semibold">Pramod Sahu</p>
                                                        <p class="text-sm text-slate-500">Founder and CEO, Bynd AI</p>
                                                    </div>
                                                    <div class="grid grid-cols-3 gap-1 rounded-2xl bg-slate-950 p-2">
                                                        <span class="h-2.5 w-2.5 rounded-sm bg-white"></span><span
                                                            class="h-2.5 w-2.5 rounded-sm bg-slate-700"></span><span
                                                            class="h-2.5 w-2.5 rounded-sm bg-white"></span>
                                                        <span class="h-2.5 w-2.5 rounded-sm bg-slate-700"></span><span
                                                            class="h-2.5 w-2.5 rounded-sm bg-white"></span><span
                                                            class="h-2.5 w-2.5 rounded-sm bg-slate-700"></span>
                                                        <span class="h-2.5 w-2.5 rounded-sm bg-white"></span><span
                                                            class="h-2.5 w-2.5 rounded-sm bg-slate-700"></span><span
                                                            class="h-2.5 w-2.5 rounded-sm bg-white"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="rounded-[1.5rem] bg-sky-50 p-4">
                                                    <p class="text-xs uppercase tracking-[0.2em] text-sky-700">Lead capture
                                                    </p>
                                                    <p class="mt-2 text-2xl font-semibold text-slate-950">Instant</p>
                                                    <p class="mt-2 text-sm text-slate-600">Scan, tag, and prioritize
                                                        contacts while you are still in the conversation.</p>
                                                </div>
                                                <div class="rounded-[1.5rem] bg-violet-50 p-4">
                                                    <p class="text-xs uppercase tracking-[0.2em] text-violet-700">Mini
                                                        website</p>
                                                    <p class="mt-2 text-2xl font-semibold text-slate-950">Ready</p>
                                                    <p class="mt-2 text-sm text-slate-600">Share your business presence
                                                        before prospects forget your name.</p>
                                                </div>
                                            </div>

                                            <div class="space-y-3 rounded-[1.8rem] border border-slate-200 bg-white p-4">
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="h-9 w-9 rounded-2xl bg-gradient-to-br from-sky-500 to-cyan-500"></span>
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-900">Business feed
                                                            visibility</p>
                                                        <p class="text-xs text-slate-500">Purpose-built for high-value
                                                            networking moments.</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="h-9 w-9 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-500"></span>
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-900">Event ROI tracking
                                                        </p>
                                                        <p class="text-xs text-slate-500">Purpose-built for high-value
                                                            networking moments.</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="h-9 w-9 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500"></span>
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-900">Occasional flyer
                                                            creation</p>
                                                        <p class="text-xs text-slate-500">Purpose-built for high-value
                                                            networking moments.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-b border-slate-200 bg-white py-20">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="max-w-3xl space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-600">Trust at launch</p>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-950 md:text-5xl">Credibility signals that
                        help visitors say yes faster.</h2>
                    <p class="text-base leading-7 text-slate-600 md:text-lg">Instead of leaning on inflated claims, the page
                        uses verifiable trust markers: official business channels, real contact details, local presence, and
                        a clear founder story.</p>
                </div>
                <div class="mt-14 grid gap-8 lg:grid-cols-3">@foreach ($socialSignals as $index => $signal)<div
                    class="border-t border-slate-200 pt-6 reveal-on-scroll" style="transition-delay: {{ $index * 150 }}ms">
                    <p class="text-lg font-semibold text-slate-950">{{ $signal['title'] }}</p>
                    <p class="mt-3 text-base leading-7 text-slate-600">{{ $signal['text'] }}</p>
                </div>@endforeach</div>
            </div>
        </section>

        <section class="bg-slate-50 py-20 md:py-24">
            <div class="mx-auto grid max-w-7xl gap-12 px-6 lg:grid-cols-[0.95fr_1.05fr] lg:px-8">
                <div class="max-w-3xl space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-600">Problem to progress</p>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-950 md:text-5xl">Replace manual networking
                        friction with one connected workflow.</h2>
                    <p class="text-base leading-7 text-slate-600 md:text-lg">The most valuable moment in business networking
                        is the few minutes after a conversation. Bynd AI is designed to make that moment easier to capture,
                        organize, and convert.</p>
                </div>
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="rounded-[2rem] bg-white p-8 shadow-[0_30px_90px_-60px_rgba(15,23,42,0.35)] reveal-on-scroll">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">The old way</p>
                        <div class="mt-6 space-y-4">@foreach ($problemPoints as $point)<div class="flex gap-3"><span
                                class="mt-1.5 h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                            <p class="text-base leading-7 text-slate-600">{{ $point }}</p>
                        </div>@endforeach</div>
                    </div>
                    <div class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-[0_30px_90px_-60px_rgba(2,6,23,0.95)] reveal-on-scroll" style="transition-delay: 150ms">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-200">The Bynd AI way</p>
                        <div class="mt-6 space-y-4">@foreach ($solutionPoints as $point)<div class="flex gap-3"><span
                                class="mt-1.5 h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                            <p class="text-base leading-7 text-slate-200">{{ $point }}</p>
                        </div>@endforeach</div>
                    </div>
                </div>
            </div>
        </section>

        <section id="features" class="bg-white py-20 md:py-24">
            <div class="mx-auto grid max-w-7xl gap-14 px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                <div class="lg:sticky lg:top-28 lg:self-start">
                    <div class="max-w-3xl space-y-4 reveal-on-scroll">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-600">Feature stack</p>
                        <h2 class="text-3xl font-semibold tracking-tight text-slate-950 md:text-5xl">Nine reasons Bynd AI
                            feels larger than a typical networking app.</h2>
                        <p class="text-base leading-7 text-slate-600 md:text-lg">Each capability supports the same
                            conversion goal: help users create stronger first impressions, capture more intent, and continue
                            conversations after the meeting ends.</p>
                    </div>
                    <div class="mt-10 space-y-5 border-t border-slate-200 pt-6 text-sm leading-7 text-slate-600">
                        <p>Built for entrepreneurs, consultants, event organizers, and sales teams who need more than a
                            contact list.</p><a href="{{ $playStoreLink }}" target="_blank" rel="noreferrer"
                            class="inline-flex items-center justify-center rounded-full bg-amber-400 px-6 py-3.5 text-sm font-semibold text-slate-950 transition duration-200 hover:bg-amber-300">Join
                            the Android launch</a>
                    </div>
                </div>
                <div class="space-y-3">@foreach ($features as $index => $feature)<div
                    class="grid gap-4 border-t border-slate-200 py-7 md:grid-cols-[110px_72px_1fr] md:items-start reveal-on-scroll" style="transition-delay: {{ $index * 100 }}ms">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-400">
                            {{ $feature['number'] }}</p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-600 text-white">
                        <span class="text-xs font-semibold">AI</span></div>
                    <div class="space-y-3">
                        <div class="flex flex-col gap-2 md:flex-row md:items-baseline md:justify-between">
                            <h3 class="text-2xl font-semibold tracking-tight text-slate-950">{{ $feature['title'] }}
                            </h3>
                            <p class="text-sm font-medium text-sky-700">{{ $feature['benefit'] }}</p>
                        </div>
                        <p class="max-w-3xl text-base leading-7 text-slate-600">{{ $feature['description'] }}</p>
                    </div>
                </div>@endforeach</div>
            </div>
        </section>

        <section id="how-it-works" class="bg-slate-950 py-20 text-white md:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="max-w-3xl space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-200">How it works</p>
                    <h2 class="text-3xl font-semibold tracking-tight text-white md:text-5xl">A three-step flow that keeps
                        the journey clear.</h2>
                    <p class="text-base leading-7 text-slate-300 md:text-lg">Visitors should understand the app within
                        seconds: download it, build a polished identity, then use it to create better networking outcomes.
                    </p>
                </div>
                <div class="mt-14 grid gap-8 md:grid-cols-3">@foreach ($steps as $index => $step)<div
                    class="border-t border-white/10 pt-6 reveal-on-scroll" style="transition-delay: {{ $index * 150 }}ms">
                    <p class="text-5xl font-black tracking-[-0.04em] text-white/20">{{ $step['number'] }}</p>
                    <h3 class="mt-5 text-2xl font-semibold">{{ $step['title'] }}</h3>
                    <p class="mt-4 text-base leading-7 text-slate-300">{{ $step['text'] }}</p>
                </div>@endforeach</div>
                <div class="mt-12"><a href="{{ $playStoreLink }}" target="_blank" rel="noreferrer"
                        class="inline-flex items-center justify-center rounded-full bg-amber-400 px-6 py-3.5 text-sm font-semibold text-slate-950 transition duration-200 hover:bg-amber-300">Get
                        started on Google Play</a></div>
            </div>
        </section>

        <section id="demo" class="bg-slate-50 py-20 md:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="max-w-3xl space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-600">Product walkthrough</p>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-950 md:text-5xl">See the product experience
                        before the install click.</h2>
                    <p class="text-base leading-7 text-slate-600 md:text-lg">The carousel below acts like a guided product
                        story, helping visitors preview the flows that matter most for install intent and post-install
                        confidence.</p>
                </div>
                <div class="mt-14 grid gap-10 lg:grid-cols-[0.88fr_1.12fr] lg:items-center">
                    <div
                        class="mx-auto w-full max-w-[340px] rounded-[2.7rem] border border-slate-200 bg-slate-950 p-3 shadow-[0_40px_120px_-50px_rgba(15,23,42,0.9)] reveal-on-scroll">
                        <div class="rounded-[2.2rem] bg-white">
                            <div
                                class="rounded-t-[2.2rem] bg-gradient-to-r from-sky-500 to-blue-600 px-5 pb-6 pt-5 text-white transition-colors duration-500" id="carousel-header">
                                <div
                                    class="flex items-center justify-between text-xs uppercase tracking-[0.24em] text-white/80">
                                    <span id="carousel-eyebrow">Screen 1</span><span id="carousel-stat">30 sec setup</span></div>
                                <div class="mt-8 space-y-2">
                                    <p class="text-sm text-white/80">Bynd AI</p>
                                    <h3 id="carousel-title" class="text-2xl font-semibold tracking-tight">Welcome to smarter networking</h3>
                                </div>
                            </div>
                            <div class="space-y-4 p-5">
                                <div id="carousel-summary"
                                    class="rounded-2xl border border-dashed border-slate-200 px-4 py-3 text-sm text-slate-500 transition-all duration-300">
                                    Optimized for business networking in meetings, expos, and local growth communities.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">@foreach ($screens as $index => $screen)<div
                        class="carousel-btn w-full rounded-[1.8rem] border border-slate-200 bg-white px-5 py-5 text-left cursor-pointer transition hover:border-sky-500 reveal-on-scroll {{ $loop->first ? 'active' : '' }}"
                        data-eyebrow="{{ $screen['eyebrow'] }}"
                        data-stat="{{ $screen['stat'] }}"
                        data-title="{{ $screen['title'] }}"
                        data-summary="{{ $screen['summary'] }}"
                        style="transition-delay: {{ $index * 100 }}ms">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">
                                    {{ $screen['eyebrow'] }}</p>
                                <h3 class="mt-2 text-xl font-semibold tracking-tight">{{ $screen['title'] }}</h3>
                            </div><span class="text-sm text-slate-500">{{ $screen['stat'] }}</span>
                        </div>
                        <p class="mt-3 text-base leading-7 text-slate-600">{{ $screen['summary'] }}</p>
                    </div>@endforeach</div>
                </div>
            </div>
        </section>

        <section class="bg-white py-20 md:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-3xl space-y-4 text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-600">Why it stands out</p>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-950 md:text-5xl">Position Bynd AI against
                        the old way and against generic networking apps.</h2>
                    <p class="text-base leading-7 text-slate-600 md:text-lg">The messaging avoids direct competitor attacks
                        while making the value difference easy to understand for first-time visitors.</p>
                </div>
                <div class="mt-14 overflow-x-auto">
                    <table
                        class="min-w-full border-separate border-spacing-0 overflow-hidden rounded-[2rem] border border-slate-200 bg-white text-left">
                        <thead>
                            <tr class="bg-slate-950 text-white">
                                <th class="px-6 py-4 text-sm font-semibold">Feature</th>
                                <th class="px-6 py-4 text-sm font-semibold">Traditional networking</th>
                                <th class="px-6 py-4 text-sm font-semibold">Typical networking app</th>
                                <th class="px-6 py-4 text-sm font-semibold">Bynd AI</th>
                            </tr>
                        </thead>
                        <tbody>@foreach ($comparisonRows as $row)<tr class="align-top">
                            <td class="border-t border-slate-200 px-6 py-5 text-sm font-semibold text-slate-950">
                                {{ $row['feature'] }}</td>
                            <td class="border-t border-slate-200 px-6 py-5 text-sm leading-7 text-slate-600">
                                {{ $row['traditional'] }}</td>
                            <td class="border-t border-slate-200 px-6 py-5 text-sm leading-7 text-slate-600">
                                {{ $row['generic'] }}</td>
                            <td class="border-t border-slate-200 px-6 py-5 text-sm leading-7 text-sky-800">
                                {{ $row['bynd'] }}</td>
                        </tr>@endforeach</tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="bg-slate-50 py-20 md:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="max-w-3xl space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-600">Who it is for</p>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-950 md:text-5xl">Built for professionals who
                        grow through relationships.</h2>
                    <p class="text-base leading-7 text-slate-600 md:text-lg">Each audience block is written to help visitors
                        see themselves on the page and recognize the app as a business growth tool, not just another profile
                        app.</p>
                </div>
                <div class="mt-14 grid gap-6 md:grid-cols-2 xl:grid-cols-3">@foreach ($useCases as $index => $useCase)<div
                    class="border-t border-slate-200 pt-6 reveal-on-scroll" style="transition-delay: {{ $index * 100 }}ms">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-950 text-sm font-semibold text-white">
                            {{ strtoupper(substr($useCase['title'], 0, 2)) }}</div>
                        <div>
                            <h3 class="text-xl font-semibold tracking-tight text-slate-950">{{ $useCase['title'] }}</h3>
                            <p class="mt-3 text-base leading-7 text-slate-600">{{ $useCase['text'] }}</p>
                        </div>
                    </div>
                </div>@endforeach</div>
            </div>
        </section>

        <section id="pricing" class="bg-slate-950 py-20 text-white md:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                    <div class="max-w-3xl space-y-4 reveal-on-scroll">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-200">Value</p>
                        <h2 class="text-3xl font-semibold tracking-tight text-white md:text-5xl">Free to download, easier to
                            try, and built to prove value quickly.</h2>
                        <p class="text-base leading-7 text-slate-300 md:text-lg">The pricing message keeps the decision
                            light: no credit card wall, no complex pricing math, and a clear reason to install now instead
                            of later.</p>
                    </div>
                    <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 backdrop-blur reveal-on-scroll" style="transition-delay: 150ms">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-200">Launch offer</p>
                        <p class="mt-4 text-5xl font-black tracking-[-0.05em]">FREE</p>
                        <p class="mt-4 text-lg text-slate-300">Download from Google Play and start building your digital
                            networking presence right away.</p>
                        <div class="mt-8 space-y-4 text-sm leading-7 text-slate-300">
                            <p>No credit card required.</p>
                            <p>Made for Android users in the current launch phase.</p>
                            <p>Founding users can join early and shape how the platform evolves.</p>
                        </div>
                        <div class="mt-8 flex flex-col gap-4 sm:flex-row"><a href="{{ $playStoreLink }}" target="_blank"
                                rel="noreferrer"
                                class="inline-flex items-center justify-center rounded-full bg-amber-400 px-6 py-3.5 text-sm font-semibold text-slate-950 transition duration-200 hover:bg-amber-300">Try
                                Free on Android</a><a href="{{ $whatsappLink }}" target="_blank" rel="noreferrer"
                                class="inline-flex items-center justify-center rounded-full border border-white/15 px-6 py-3.5 text-sm font-semibold text-white transition hover:border-white/35 hover:bg-white/5">Talk
                                on WhatsApp</a></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white py-20 md:py-24">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="max-w-3xl space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-600">Trust and credibility</p>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-950 md:text-5xl">A local company story gives
                        the product more weight.</h2>
                    <p class="text-base leading-7 text-slate-600 md:text-lg">Trust grows faster when visitors can see who
                        built the product, where the company is based, and how to contact the team without friction.</p>
                </div>
                <div class="mt-14 grid gap-10 lg:grid-cols-[0.9fr_1.1fr]">
                    <div class="rounded-[2rem] bg-slate-950 p-8 text-white shadow-[0_35px_110px_-60px_rgba(15,23,42,0.9)]">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-sky-400 to-violet-500 text-xl font-black text-white">
                                PS</div>
                            <div>
                                <p class="text-sm uppercase tracking-[0.24em] text-sky-200">Founder message</p>
                                <p class="mt-2 text-xl font-semibold">Mr. Pramod Sahu</p>
                                <p class="text-sm text-slate-300">Founder and CEO, Bynd AI</p>
                            </div>
                        </div>
                        <p class="mt-8 text-xl leading-9 text-slate-100">"Business networking should feel intelligent,
                            immediate, and useful after the meeting ends. Bynd AI is being built to help professionals carry
                            momentum forward, not lose it in a stack of paper cards."</p>
                        <div class="mt-8 border-t border-white/10 pt-6 text-sm leading-7 text-slate-300">Founded February
                            14, 2026. Powered by Path Idea InfoTech. Proudly made in India for business communities that
                            value speed, trust, and follow-through.</div>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="border-t border-slate-200 pt-6 reveal-on-scroll">
                            <h3 class="text-xl font-semibold tracking-tight text-slate-950">Path Idea InfoTech</h3>
                            <p class="mt-3 text-base leading-7 text-slate-600">Technology partner powering the launch,
                                product vision, and business identity behind Bynd AI.</p>
                        </div>
                        <div class="border-t border-slate-200 pt-6 reveal-on-scroll" style="transition-delay: 100ms">
                            <h3 class="text-xl font-semibold tracking-tight text-slate-950">Security-first messaging</h3>
                            <p class="mt-3 text-base leading-7 text-slate-600">The page emphasizes privacy, transparency,
                                and direct contact paths so visitors feel safer taking the next step.</p>
                        </div>
                        <div class="border-t border-slate-200 pt-6 reveal-on-scroll" style="transition-delay: 200ms">
                            <h3 class="text-xl font-semibold tracking-tight text-slate-950">Made in India</h3>
                            <p class="mt-3 text-base leading-7 text-slate-600">Positioned for Indian professionals, with a
                                strong local story rooted in Raipur and Central India.</p>
                        </div>
                        <div class="border-t border-slate-200 pt-6 reveal-on-scroll" style="transition-delay: 300ms">
                            <h3 class="text-xl font-semibold tracking-tight text-slate-950">Reach the team easily</h3>
                            <p class="mt-3 text-base leading-7 text-slate-600">Phone, WhatsApp, email, website, social
                                profiles, and a physical address are all surfaced clearly below.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="faq" class="bg-slate-50 py-20 md:py-24">
            <div class="mx-auto max-w-5xl px-6 lg:px-8">
                <div class="mx-auto max-w-3xl space-y-4 text-center reveal-on-scroll">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-600">FAQ</p>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-950 md:text-5xl">Answer the questions that
                        block the install.</h2>
                    <p class="text-base leading-7 text-slate-600 md:text-lg">The accordion handles the most common
                        objections around price, differentiation, support, events, and product fit while staying honest
                        about what still needs launch-stage validation.</p>
                </div>
                <div class="mt-14 space-y-4">@foreach ($faqs as $index => $item)<details
                    class="group overflow-hidden rounded-[1.6rem] border border-slate-200 bg-white reveal-on-scroll" style="transition-delay: {{ $index * 50 }}ms" @if ($loop->first)
                    open @endif>
                    <summary
                        class="flex cursor-pointer list-none items-center justify-between gap-6 px-6 py-5 text-left">
                        <span
                            class="text-lg font-semibold tracking-tight text-slate-950">{{ $item['question'] }}</span><span
                            class="text-2xl text-slate-400 group-open:hidden">+</span><span
                            class="hidden text-2xl text-slate-400 group-open:block">-</span></summary>
                    <div class="border-t border-slate-100 px-6 py-5 text-base leading-7 text-slate-600">
                        {{ $item['answer'] }}</div>
                </details>@endforeach</div>
            </div>
        </section>

        <section class="bg-gradient-to-br from-sky-600 via-blue-700 to-slate-950 py-20 text-white md:py-24">
            <div class="mx-auto grid max-w-7xl gap-12 px-6 lg:grid-cols-[1.1fr_0.9fr] lg:items-center lg:px-8">
                <div class="reveal-on-scroll">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-sky-200">Final call to action</p>
                    <h2 class="mt-4 text-4xl font-black tracking-[-0.04em] md:text-6xl">Download Bynd AI and give every new
                        connection a better chance to become business.</h2>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-200">Join the early adopters, launch your digital
                        business identity, and start networking with more speed, clarity, and follow-through.</p>
                    <div class="mt-10 flex flex-col gap-4 sm:flex-row"><a href="{{ $playStoreLink }}" target="_blank"
                            rel="noreferrer"
                            class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3.5 text-sm font-semibold text-slate-950 transition hover:bg-slate-100">Download
                            from Google Play</a><a href="{{ $whatsappLink }}" target="_blank" rel="noreferrer"
                            class="inline-flex items-center justify-center rounded-full border border-white/20 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-white/10">Chat
                            on WhatsApp</a></div>
                    <div class="mt-8 text-sm leading-7 text-slate-200">Launch note: the page is optimized to convert
                        visitors into Play Store clicks, while the data layer is ready for CTA, FAQ, scroll depth, and
                        time-on-page tracking.</div>
                </div>
                <div class="reveal-on-scroll" style="transition-delay: 150ms">
                    <div class="mx-auto max-w-sm rounded-[2rem] bg-white/10 p-6 backdrop-blur">
                        <div class="rounded-[1.6rem] bg-white p-5 text-slate-950"><img src="{{ $qrImage }}"
                                alt="QR code linking to the Bynd AI Google Play Store page" loading="lazy"
                                class="mx-auto h-56 w-56 rounded-2xl">
                            <div class="mt-5 text-center">
                                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-400">Scan to install
                                </p>
                                <p class="mt-2 text-xl font-semibold">Open the Play Store instantly on your phone.</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Perfect for desktop visitors who are ready
                                    to continue on Android.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="pointer-events-none fixed bottom-6 left-6 z-30 hidden md:block">
        <div id="activity-feed-card"
            class="max-w-xs rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-[0_24px_80px_-60px_rgba(15,23,42,0.75)] transition-all duration-500 ease-in-out">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Recent activity</p>
            <p id="activity-feed-text" class="mt-2 text-sm leading-6 text-slate-600">
                {{ $activityFeed[0] }}
            </p>
        </div>
    </div>
    <div
        class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 px-4 py-3 shadow-[0_-24px_80px_-60px_rgba(15,23,42,0.6)] backdrop-blur md:hidden">
        <div class="mx-auto flex max-w-md items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Android launch</p>
                <p class="text-sm font-semibold text-slate-950">Download Bynd AI free</p>
            </div><a href="{{ $playStoreLink }}" target="_blank" rel="noreferrer"
                class="shrink-0 rounded-full bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950 transition duration-200 hover:bg-amber-300">Install</a>
        </div>
    </div>
    <div id="exit-intent-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm">
        <div class="w-full max-w-lg rounded-[2rem] bg-white p-8 shadow-[0_40px_120px_-60px_rgba(15,23,42,0.75)]">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-sky-600">Before you go</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Do not lose your next business
                        lead to a paper card.</h2>
                </div><button type="button" id="exit-intent-close"
                    class="rounded-full border border-slate-200 px-3 py-1.5 text-sm text-slate-500 transition hover:border-slate-300 hover:text-slate-700">Close</button>
            </div>
            <p class="mt-5 text-base leading-7 text-slate-600">Install Bynd AI from Google Play and start with a digital
                business card, QR sharing, lead capture, and a stronger business presence built for modern networking.</p>
            <div class="mt-8 flex flex-col gap-4 sm:flex-row"><a href="{{ $playStoreLink }}" target="_blank"
                    rel="noreferrer"
                    class="inline-flex flex-1 items-center justify-center rounded-full bg-amber-400 px-6 py-3.5 text-sm font-semibold text-slate-950 transition duration-200 hover:bg-amber-300">Claim
                    my free download</a><a href="{{ $whatsappLink }}" target="_blank" rel="noreferrer"
                    class="inline-flex flex-1 items-center justify-center rounded-full border border-slate-200 px-6 py-3.5 text-sm font-semibold text-slate-950 transition hover:border-slate-300 hover:bg-slate-50">Need
                    help first?</a></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const activityFeed = @json($activityFeed);
            const activityCard = document.getElementById('activity-feed-card');
            const activityNode = document.getElementById('activity-feed-text');
            let activityIndex = 0;
            if (activityCard && activityNode && activityFeed.length > 1) {
                setInterval(() => {
                    // Start Exit Animation on whole card (fade out & slide slightly down)
                    activityCard.classList.add('opacity-0', 'translate-y-4');
                    
                    setTimeout(() => {
                        // Swap Text Once Hidden
                        activityIndex = (activityIndex + 1) % activityFeed.length;
                        activityNode.textContent = activityFeed[activityIndex];
                        
                        // Start Entry Animation on whole card (fade in & slide to original position)
                        activityCard.classList.remove('opacity-0', 'translate-y-4');
                    }, 500); // 500ms matches the duration-500 CSS transition
                }, 4200);
            }

            const modal = document.getElementById('exit-intent-modal');
            const closeBtn = document.getElementById('exit-intent-close');
            const seen = sessionStorage.getItem('byndai-exit-intent-seen');

            const closeModal = () => {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            if (!seen && modal) {
                const onMouseLeave = (event) => {
                    if (event.clientY <= 0) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        sessionStorage.setItem('byndai-exit-intent-seen', 'true');
                        document.removeEventListener('mouseout', onMouseLeave);
                    }
                };
                document.addEventListener('mouseout', onMouseLeave);
            }

            closeBtn?.addEventListener('click', closeModal);
            modal?.addEventListener('click', (event) => {
                if (event.target === modal) closeModal();
            });

            // Scroll Reveal Observer
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

            document.querySelectorAll('.reveal-on-scroll').forEach((el) => {
                revealObserver.observe(el);
            });

            // Interactive Carousel
            const carouselBtns = document.querySelectorAll('.carousel-btn');
            const carouselHeader = document.getElementById('carousel-header');
            const carouselEyebrow = document.getElementById('carousel-eyebrow');
            const carouselStat = document.getElementById('carousel-stat');
            const carouselTitle = document.getElementById('carousel-title');
            const carouselSummary = document.getElementById('carousel-summary');

            if (carouselBtns.length && carouselTitle) {
                // Ensure text elements have transition
                carouselTitle.style.transition = 'opacity 300ms ease';
                
                carouselBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (btn.classList.contains('active')) return;

                        // Remove active class from all and set active on current
                        carouselBtns.forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');

                        // Briefly fade out text box for transition
                        carouselTitle.style.opacity = '0';
                        carouselSummary.classList.add('opacity-0');
                        carouselHeader.classList.add('opacity-70');
                        
                        setTimeout(() => {
                            // Update content
                            carouselEyebrow.textContent = btn.dataset.eyebrow;
                            carouselStat.textContent = btn.dataset.stat;
                            carouselTitle.textContent = btn.dataset.title;
                            carouselSummary.textContent = btn.dataset.summary;
                            
                            // Fade back in
                            carouselTitle.style.opacity = '1';
                            carouselSummary.classList.remove('opacity-0');
                            carouselHeader.classList.remove('opacity-70');
                        }, 300);
                    });
                });
            }
        })();
    </script>
@endpush