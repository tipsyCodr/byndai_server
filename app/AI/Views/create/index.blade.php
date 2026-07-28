<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Page Creator - Lovable style</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="text-slate-100 font-sans h-screen w-screen overflow-hidden bg-[#0c0c0e] flex flex-row">

    <!-- Left Panel: Sidebar / Editor (35% width) -->
    <div id="sidebar-panel" class="flex flex-col h-full bg-[#0c0c0e] shrink-0" style="width: 400px; flex-shrink: 0;">
        
        <!-- Sidebar Header -->
        <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-linear-to-tr from-amber-500 via-rose-500 to-indigo-600 flex items-center justify-center font-bold text-white text-base">
                    L
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-white">AI Page Creator</h2>
                    <p class="text-[10px] text-slate-500">Previewing last saved version</p>
                </div>
            </div>
            <a href="{{ route('ai.chat') }}" class="text-xs text-slate-400 hover:text-white transition-colors bg-white/5 hover:bg-white/10 border border-white/5 rounded-lg px-2.5 py-1.5 font-medium flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                Chat
            </a>
        </div>

        <!-- Sidebar Content: Generation History -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6" id="history-container">
            <!-- Initial Welcome -->
            <div class="bg-white/5 border border-white/5 p-4 rounded-xl space-y-2">
                <h3 class="text-xs font-semibold text-slate-300">Welcome to Page Creator</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Describe your landing page, SaaS application, or dashboard layout below. The AI will generate fully functional HTML using Tailwind CSS.
                </p>
            </div>
            <!-- Dynamic history items will go here -->
        </div>

        <!-- Sidebar Footer: Chat Box Input -->
        <div class="p-4 border-t border-white/5 bg-[#0c0c0e]">
            <form id="generate-form" class="bg-[#18181b] border border-white/10 rounded-xl p-3 flex flex-col gap-2 shadow-2xl focus-within:border-indigo-500/50 transition-all">
                @csrf
                <textarea
                    id="prompt-input"
                    class="w-full bg-transparent border-0 p-0 text-white placeholder-slate-500 focus:outline-none focus:ring-0 text-sm font-medium resize-none min-h-[60px] max-h-40 overflow-y-auto leading-relaxed"
                    placeholder="Ask AI to create a landing page for my..."></textarea>
                
                <!-- Input Actions Row -->
                <div class="flex items-center justify-between pt-2 border-t border-white/5">
                    <div class="flex items-center gap-2">
                        <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-all flex items-center justify-center">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                        <div class="relative">
                            <button type="button" class="px-2.5 py-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-all flex items-center gap-1 text-[11px] font-semibold">
                                Build <i class="fa-solid fa-chevron-down text-[8px]"></i>
                            </button>
                        </div>
                        <button type="button" class="w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-all flex items-center justify-center">
                            <i class="fa-solid fa-microphone text-xs"></i>
                        </button>
                    </div>

                    <!-- Submit/Stop Button -->
                    <button type="submit" id="generate-btn" class="w-8 h-8 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white transition-all flex items-center justify-center shadow-lg shadow-indigo-500/20 active:scale-95">
                        <i class="fa-solid fa-arrow-up text-xs"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Resizer bar -->
    <div id="resizer" style="width:6px;cursor:col-resize;background:transparent;flex-shrink:0;position:relative;z-index:20;transition:background 0.15s;" onmouseover="this.style.background='rgba(99,102,241,0.5)'" onmouseout="this.style.background='transparent'"></div>

    <!-- Right Panel: Webpage Canvas (65% width) -->
    <div class="flex-1 flex flex-col h-full bg-[#08080a]">
        
        <!-- Canvas Top Bar Controls -->
        <div class="px-6 py-4 border-b border-white/5 flex items-center justify-between bg-[#0c0c0e]">
            <!-- Preview URL/Page dropdown -->
            <div class="flex items-center gap-3">
                <div class="bg-white/5 border border-white/5 rounded-lg px-3 py-1.5 flex items-center gap-2">
                    <i class="fa-solid fa-globe text-xs text-slate-500"></i>
                    <span class="text-xs text-slate-400 font-medium select-none">Homepage</span>
                </div>
            </div>

            <!-- Viewport Toggle & Actions -->
            <div class="flex items-center gap-4">
                <!-- Desktop/Mobile toggle -->
                <div class="bg-white/5 border border-white/5 rounded-lg p-0.5 flex gap-1">
                    <button id="btn-desktop" class="w-8 h-8 rounded bg-white/10 text-white flex items-center justify-center transition-all text-xs" title="Desktop View">
                        <i class="fa-solid fa-desktop"></i>
                    </button>
                    <button id="btn-mobile" class="w-8 h-8 rounded text-slate-500 hover:text-white flex items-center justify-center transition-all text-xs" title="Mobile View">
                        <i class="fa-solid fa-mobile-screen-button"></i>
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button class="px-3.5 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-xs font-semibold text-slate-300 transition-all border border-white/5">
                        Share
                    </button>
                    <button class="px-3.5 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-xs font-semibold text-white transition-all shadow-lg shadow-indigo-500/10">
                        Publish
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Canvas Area -->
        <div class="flex-1 p-6 flex justify-center items-center overflow-hidden">
            <!-- Canvas Frame Wrapper (animates width on viewport toggle) -->
            <div id="canvas-wrapper" class="w-full h-full bg-white rounded-2xl shadow-2xl overflow-hidden border border-white/5 transition-all duration-300">
                <iframe id="preview-frame" class="w-full h-full border-0 bg-white" sandbox="allow-scripts allow-same-origin"
                    srcdoc="<div style='display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif;color:#888;background:#fff;'>Generated page will appear here</div>"></iframe>
            </div>
        </div>

    </div>

    <!-- UI Scripts -->
    <script>
        const form = document.getElementById('generate-form');
        const input = document.getElementById('prompt-input');
        const iframe = document.getElementById('preview-frame');
        const btn = document.getElementById('generate-btn');
        const historyContainer = document.getElementById('history-container');
        let abortController = null;

        // Stores state of the generated site
        // Schema: { "homepage": { title: "Home", html: "...", sections: [ { name: "navbar", html: "..." } ] } }
        let siteData = {};
        let activePageSlug = '';

        // Resizable panels
        const resizer = document.getElementById('resizer');
        const sidebarPanel = document.getElementById('sidebar-panel');

        resizer.addEventListener('mousedown', (e) => {
            e.preventDefault();
            iframe.style.pointerEvents = 'none';
            document.body.style.cursor = 'col-resize';
            document.body.style.userSelect = 'none';

            const startX = e.clientX;
            const startWidth = sidebarPanel.getBoundingClientRect().width;

            const doDrag = (moveEvent) => {
                const newWidth = startWidth + (moveEvent.clientX - startX);
                const minW = window.innerWidth * 0.20;
                const maxW = window.innerWidth * 0.70;
                if (newWidth >= minW && newWidth <= maxW) {
                    sidebarPanel.style.width = newWidth + 'px';
                }
            };

            const stopDrag = () => {
                iframe.style.pointerEvents = 'auto';
                document.body.style.cursor = '';
                document.body.style.userSelect = '';
                document.removeEventListener('mousemove', doDrag);
                document.removeEventListener('mouseup', stopDrag);
            };

            document.addEventListener('mousemove', doDrag);
            document.addEventListener('mouseup', stopDrag);
        });
        
        // Viewport Switcher
        const canvasWrapper = document.getElementById('canvas-wrapper');
        const btnDesktop = document.getElementById('btn-desktop');
        const btnMobile = document.getElementById('btn-mobile');

        btnDesktop.addEventListener('click', () => {
            canvasWrapper.style.width = '100%';
            btnDesktop.classList.add('bg-white/10', 'text-white');
            btnDesktop.classList.remove('text-slate-500');
            btnMobile.classList.remove('bg-white/10', 'text-white');
            btnMobile.classList.add('text-slate-500');
        });

        btnMobile.addEventListener('click', () => {
            canvasWrapper.style.width = '375px';
            btnMobile.classList.add('bg-white/10', 'text-white');
            btnMobile.classList.remove('text-slate-500');
            btnDesktop.classList.remove('bg-white/10', 'text-white');
            btnDesktop.classList.add('text-slate-500');
        });

        // Auto-resize textarea with max height constraint
        input.addEventListener('input', function() {
            this.style.height = 'auto';
            const maxHeight = 160;
            this.style.height = Math.min(this.scrollHeight, maxHeight) + 'px';
        });

        // Submit on Enter, new line on Shift+Enter
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.dispatchEvent(new Event('submit', {
                    cancelable: true,
                    bubbles: true
                }));
            }
        });

        // Intercept navigation link clicks inside the iframe
        iframe.addEventListener('load', () => {
            try {
                const doc = iframe.contentDocument || iframe.contentWindow.document;
                doc.addEventListener('click', (e) => {
                    const link = e.target.closest('a');
                    if (link) {
                        e.preventDefault();
                        
                        // Parse link target slug
                        let href = link.getAttribute('href');
                        if (!href) return;
                        
                        // Remove base extensions/slashes
                        let slug = href.replace(/^\//, '').replace(/\.html$/, '');
                        if (slug === '' || slug === 'index') slug = 'homepage';

                        if (siteData[slug]) {
                            switchPage(slug);
                        } else {
                            console.log("Page link target slug not generated yet:", slug);
                        }
                    }
                });
            } catch (err) {
                // Ignore cross-origin warnings
            }
        });

        // Switching Active Page View in Iframe
        function switchPage(slug) {
            if (!siteData[slug]) return;
            activePageSlug = slug;
            
            // Update Preview Title/Dropdown
            const selectorText = document.querySelector('.bg-white\\/5 span');
            if (selectorText) selectorText.textContent = siteData[slug].title;

            // Assemble Full Page HTML
            const compiledHtml = assemblePage(slug);
            iframe.srcdoc = compiledHtml;
        }
        window.switchPage = switchPage;

        // Assemble sections into clean document structure
        function assemblePage(slug) {
            const page = siteData[slug];
            if (!page) return '';
            
            // Build body from generated section HTML blocks
            const bodyContent = page.sections.map(s => s.html).join('\n\n');

            return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${page.title}</title>
    <script src="https://cdn.tailwindcss.com"><\/script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">
    ${bodyContent}
</body>
</html>`;
        }

        // Main Submit Flow
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (abortController) {
                abortController.abort();
                return;
            }

            const prompt = input.value.trim();
            if (!prompt) return;

            // Add plan status item to history list
            const historyItem = document.createElement('div');
            historyItem.className = 'bg-[#18181b] border border-white/5 p-4 rounded-xl space-y-3';
            historyItem.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">User Request</span>
                    <span class="text-[10px] text-indigo-400 font-medium">Just now</span>
                </div>
                <p class="text-xs text-white leading-relaxed whitespace-pre-wrap">${escapeHtml(prompt)}</p>
                <div class="text-xs text-slate-400 space-y-1.5 pt-2 border-t border-white/5" id="step-log">
                    <div class="flex items-center gap-2 text-[11px] text-indigo-400">
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        <span>Planning website structure...</span>
                    </div>
                </div>
            `;
            historyContainer.appendChild(historyItem);
            historyContainer.scrollTop = historyContainer.scrollHeight;

            // Reset UI for generating state
            input.disabled = true;
            btn.classList.remove('bg-indigo-600', 'hover:bg-indigo-500');
            btn.classList.add('bg-rose-600', 'hover:bg-rose-500');
            btn.innerHTML = '<i class="fa-solid fa-stop text-xs"></i>';
            
            abortController = new AbortController();
            const stepLog = historyItem.querySelector('#step-log');

            try {
                // Step 1: Request Plan from Planner Endpoint
                const planResponse = await fetch("{{ route('ai.create.plan') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ prompt }),
                    signal: abortController.signal
                });

                let plan;
                if (!planResponse.ok) {
                    console.warn("Planner failed or timed out. Falling back to default multi-page layout.");
                    plan = {
                        pages: [
                            {
                                slug: "homepage",
                                title: "Home",
                                sections: ["navbar", "hero", "features", "footer"]
                            },
                            {
                                slug: "about",
                                title: "About Us",
                                sections: ["navbar", "story-details", "footer"]
                            },
                            {
                                slug: "contact",
                                title: "Contact Us",
                                sections: ["navbar", "contact-form", "footer"]
                            }
                        ]
                    };
                } else {
                    const errText = await planResponse.text().catch(() => '');
                    try {
                        plan = JSON.parse(errText);
                    } catch(e) {
                        // fallback if invalid JSON returned
                        plan = {
                            pages: [
                                {
                                    slug: "homepage",
                                    title: "Home",
                                    sections: ["navbar", "hero", "features", "footer"]
                                }
                            ]
                        };
                    }
                }

                // Setup siteData structure with planned outline
                siteData = {};
                plan.pages.forEach(p => {
                    siteData[p.slug] = {
                        title: p.title,
                        sections: p.sections.map(name => ({ name, html: '' }))
                    };
                });

                // Clear layout planner status, start printing progress checklist
                stepLog.innerHTML = '';
                plan.pages.forEach(p => {
                    const pageHeader = document.createElement('div');
                    pageHeader.className = 'font-semibold text-[11px] text-slate-300 mt-2 flex items-center gap-1.5';
                    pageHeader.innerHTML = `<i class="fa-solid fa-folder text-slate-500"></i> ${p.title}`;
                    stepLog.appendChild(pageHeader);

                    p.sections.forEach(secName => {
                        const secItem = document.createElement('div');
                        secItem.className = 'flex items-center gap-2 pl-4 text-[11px] text-slate-500';
                        secItem.id = `status-${p.slug}-${secName}`;
                        secItem.innerHTML = `<i class="fa-regular fa-circle"></i> <span>Build ${secName}...</span>`;
                        stepLog.appendChild(secItem);
                    });
                });

                // Step 2: Loop pages & sections sequentially to build code block by block
                for (const p of plan.pages) {
                    activePageSlug = p.slug;

                    for (const sec of p.sections) {
                        const statusEl = document.getElementById(`status-${p.slug}-${sec}`);
                        statusEl.className = 'flex items-center gap-2 pl-4 text-[11px] text-indigo-400 font-medium';
                        statusEl.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>Building ${sec}...</span>`;
                        historyContainer.scrollTop = historyContainer.scrollHeight;

                        // Compile sections completed so far on the active page to supply context
                        const existingSections = siteData[p.slug].sections.filter(s => s.html !== '');

                        const sectionResponse = await fetch("{{ route('ai.create.generate-section') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                prompt,
                                page_slug: p.slug,
                                section_name: sec,
                                existing_sections: existingSections
                            }),
                            signal: abortController.signal
                        });

                        if (!sectionResponse.ok) throw new Error(`Error building section: ${sec}`);

                        const reader = sectionResponse.body.getReader();
                        const decoder = new TextDecoder();
                        let accumulatedHtml = '';

                        while (true) {
                            const { done, value } = await reader.read();
                            if (done) break;

                            const chunk = decoder.decode(value, { stream: true });
                            const lines = chunk.split('\n');

                            for (const line of lines) {
                                if (line.startsWith('data: ')) {
                                    try {
                                        const parsed = JSON.parse(line.slice(6));
                                        if (parsed.content) {
                                            accumulatedHtml += parsed.content;
                                            
                                            // Realtime partial render updates
                                            const targetSec = siteData[p.slug].sections.find(s => s.name === sec);
                                            if (targetSec) {
                                                targetSec.html = cleanSnippet(accumulatedHtml);
                                                switchPage(p.slug);
                                            }
                                        }
                                    } catch (err) {
                                        // Ignore parser glitches on partial boundaries
                                    }
                                }
                            }
                        }

                        // Complete current section item status
                        statusEl.className = 'flex items-center gap-2 pl-4 text-[11px] text-emerald-400';
                        statusEl.innerHTML = `<i class="fa-solid fa-circle-check"></i> <span>${sec} generated!</span>`;
                    }
                }

                // Update Project Dropdown navigation options on the preview frame header
                updatePagesNavigationDropdown();

            } catch (err) {
                if (err.name === 'AbortError') {
                    const stoppedInfo = document.createElement('div');
                    stoppedInfo.className = 'text-[11px] text-amber-400 mt-2 flex items-center gap-1.5';
                    stoppedInfo.innerHTML = `<i class="fa-solid fa-circle-info"></i> Stopped by user.`;
                    stepLog.appendChild(stoppedInfo);
                } else {
                    console.error(err);
                    const errorEl = document.createElement('div');
                    errorEl.className = 'text-[11px] text-rose-500 mt-2 flex items-center gap-1.5';
                    errorEl.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> Error: ${escapeHtml(err.message)}`;
                    stepLog.appendChild(errorEl);
                }
            } finally {
                abortController = null;
                input.disabled = false;
                btn.classList.remove('bg-rose-600', 'hover:bg-rose-500');
                btn.classList.add('bg-indigo-600', 'hover:bg-indigo-500');
                btn.innerHTML = '<i class="fa-solid fa-arrow-up text-xs"></i>';
                input.value = '';
                input.style.height = 'auto';
            }
        });

        // Strip backticks or wrapper formats from generated string snippet
        function cleanSnippet(html) {
            let cleaned = html;
            if (cleaned.includes('```')) {
                const parts = cleaned.split(/```(?:html)?/i);
                if (parts.length > 1) {
                    cleaned = parts[1];
                    if (cleaned.includes('```')) {
                        cleaned = cleaned.split('```')[0];
                    }
                }
            }
            return cleaned.trim();
        }

        // Dynamically populate pages list dropdown in the top bar
        function updatePagesNavigationDropdown() {
            const controlsArea = document.querySelector('.bg-[#0c0c0e] .flex.items-center.gap-3');
            if (!controlsArea) return;

            // Clear old selector dropdown markup and re-build
            controlsArea.innerHTML = '';

            const container = document.createElement('div');
            container.className = 'relative inline-block text-left';
            container.innerHTML = `
                <button type="button" id="pages-menu-btn" class="bg-white/5 border border-white/5 rounded-lg px-3 py-1.5 flex items-center gap-2 text-xs text-slate-300 font-semibold hover:bg-white/10 active:scale-95 transition-all">
                    <i class="fa-solid fa-globe text-slate-500"></i>
                    <span>${siteData[activePageSlug]?.title || 'Homepage'}</span>
                    <i class="fa-solid fa-chevron-down text-[8px] ml-1"></i>
                </button>
                <div id="pages-dropdown-menu" class="hidden absolute left-0 mt-1.5 w-48 rounded-lg bg-[#18181b] border border-white/5 shadow-2xl z-50 p-1">
                    ${Object.keys(siteData).map(slug => `
                        <button type="button" onclick="switchPage('${slug}'); document.getElementById('pages-dropdown-menu').classList.add('hidden')" class="w-full text-left px-3 py-2 text-xs text-slate-400 hover:text-white hover:bg-white/5 rounded-md font-medium flex items-center gap-2">
                            <i class="fa-regular fa-file-code text-[10px]"></i>
                            <span>${siteData[slug].title}</span>
                        </button>
                    `).join('')}
                </div>
            `;
            controlsArea.appendChild(container);

            // Toggle active list dropdown on trigger button click
            const menuBtn = document.getElementById('pages-menu-btn');
            const dropdown = document.getElementById('pages-dropdown-menu');
            if (menuBtn && dropdown) {
                menuBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });
                document.addEventListener('click', () => dropdown.classList.add('hidden'));
            }
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>

</body>

</html>
