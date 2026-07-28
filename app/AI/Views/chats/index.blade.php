<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-100 font-sans h-screen flex items-center justify-center overflow-hidden" style="background-color: #0b0f19;">
    <div class="w-full max-w-4xl h-[90vh] bg-slate-900/60 backdrop-blur-xl border border-white/10 rounded-3xl flex flex-col shadow-2xl m-4">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <h2 class="text-base font-semibold text-white tracking-wide">AI Assistant</h2>
            </div>
            <form action="{{ route('ai.chat.clear') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-slate-400 hover:text-rose-400 transition-colors bg-white/5 hover:bg-rose-500/10 border border-white/10 rounded-lg px-3 py-1.5 font-medium">
                    Clear Chat
                </button>
            </form>
        </div>

        <!-- Chat Messages -->
        <div class="flex-1 overflow-y-auto p-6 flex flex-col gap-4" id="messages">
            @forelse($messages as $msg)
                @if($msg['role'] === 'user')
                    <div class="max-w-[80%] px-5 py-3.5 rounded-2xl text-sm leading-relaxed bg-indigo-600 text-white rounded-tr-none self-end shadow-lg shadow-indigo-500/20">
                        {{ $msg['content'] }}
                    </div>
                @else
                    <div class="max-w-[80%] px-5 py-3.5 rounded-2xl text-sm leading-relaxed bg-white/5 border border-white/5 text-slate-200 rounded-tl-none self-start">
                        {{ $msg['content'] }}
                    </div>
                @endif
            @empty
                <div class="max-w-[80%] px-5 py-3.5 rounded-2xl text-sm leading-relaxed bg-white/5 border border-white/5 text-slate-200 rounded-tl-none self-start">
                    Hello! How can I help you today?
                </div>
            @endforelse
        </div>

        <!-- Chat Input -->
        <div class="p-5 border-t border-white/10">
            <form class="flex gap-3" id="message-form" action="{{ route('ai.chat.store') }}" method="POST">
                @csrf
                <input class="flex-1 bg-slate-950/60 border border-white/10 rounded-xl px-5 py-3.5 text-sm text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all" 
                       type="text" 
                       name="message"
                       id="chat-input" 
                       placeholder="Ask anything..." 
                       required 
                       autocomplete="off" />
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 active:scale-95 text-white font-semibold rounded-xl px-6 text-sm transition-all">
                    Send
                </button>
            </form>
        </div>
    </div>

    <script>
        const messageForm = document.getElementById('message-form');
        const chatInput = document.getElementById('chat-input');
        const messagesDiv = document.getElementById('messages');

        const scrollToBottom = () => {
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        };
        scrollToBottom();

        messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (!message) return;

            chatInput.value = '';

            // User Message
            const userMsgHtml = `
                <div class="max-w-[80%] px-5 py-3.5 rounded-2xl text-sm leading-relaxed bg-indigo-600 text-white rounded-tr-none self-end shadow-lg shadow-indigo-500/20">
                    ${escapeHtml(message)}
                </div>
            `;
            messagesDiv.insertAdjacentHTML('beforeend', userMsgHtml);
            scrollToBottom();

            // Assistant Placeholder
            const assistantMsgId = 'assistant-' + Date.now();
            const assistantMsgHtml = `
                <div id="${assistantMsgId}" class="max-w-[80%] px-5 py-3.5 rounded-2xl text-sm leading-relaxed bg-white/5 border border-white/5 text-slate-200 rounded-tl-none self-start">
                    <span class="opacity-50">Thinking...</span>
                </div>
            `;
            messagesDiv.insertAdjacentHTML('beforeend', assistantMsgHtml);
            scrollToBottom();

            const assistantMsgDiv = document.getElementById(assistantMsgId);

            try {
                const response = await fetch("{{ route('ai.chat.stream') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: message })
                });

                if (!response.ok) throw new Error('Response error');

                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let accumulatedText = '';
                let isFirstChunk = true;

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
                                    if (isFirstChunk) {
                                        assistantMsgDiv.innerHTML = '';
                                        isFirstChunk = false;
                                    }
                                    accumulatedText += parsed.content;
                                    assistantMsgDiv.textContent = accumulatedText;
                                    scrollToBottom();
                                }
                            } catch (err) {
                                // ignore parse errors on partial streams
                            }
                        }
                    }
                }
            } catch (error) {
                console.error(error);
                assistantMsgDiv.innerHTML = `<span class="text-rose-400">Failed to connect to AI.</span>`;
            }
        });

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
