<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Pro - Premium Assistant</title>
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <!-- Markdown & Syntax Highlighting -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');
        
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --bg-main: #0f172a;
            --bg-card: #1e293b;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--bg-main);
            color: #f1f5f9;
        }

        .mono { font-family: 'JetBrains Mono', monospace; }
        
        .glass { 
            background: rgba(30, 41, 59, 0.7); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Markdown Styles */
        .prose pre { 
            background: #0d1117 !important;
            border-radius: 12px;
            padding: 1rem;
            margin: 1rem 0;
            border: 1px solid rgba(255, 255, 255, 0.05);
            position: relative;
        }
        .prose code { font-family: 'JetBrains Mono', monospace; font-size: 0.9em; }
        .prose p { margin-bottom: 0.75rem; line-height: 1.7; }
        .prose h1, .prose h2, .prose h3 { font-weight: 700; margin-top: 1.5rem; margin-bottom: 0.5rem; color: #fff; }
        .prose ul, .prose ol { margin-left: 1.5rem; margin-bottom: 1rem; list-style-type: disc; }

        .chat-container::-webkit-scrollbar { width: 5px; }
        .chat-container::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        
        .message-fade-in { animation: fadeIn 0.3s ease-out forwards; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .copy-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            padding: 4px 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
            font-size: 10px;
            color: #94a3b8;
            opacity: 0;
            transition: all 0.2s;
        }
        pre:hover .copy-btn { opacity: 1; }
        .copy-btn:hover { background: var(--primary); color: white; }
    </style>
</head>
<body class="h-screen flex flex-col overflow-hidden">
    <!-- Blue Gradient Background Blobs -->
    <div class="fixed top-0 left-0 w-full h-full -z-10 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-indigo-500/10 blur-[120px] rounded-full"></div>
        <div class="absolute top-[20%] -right-[10%] w-[35%] h-[35%] bg-blue-500/10 blur-[120px] rounded-full"></div>
        <div class="absolute -bottom-[10%] left-[20%] w-[30%] h-[30%] bg-violet-500/10 blur-[120px] rounded-full"></div>
    </div>

    <!-- Header -->
    <header class="glass sticky top-0 z-20 px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                <i class="ri-flashlight-line text-2xl text-white"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-white">AI Pro</h1>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">System Online • Ollama</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.location.reload()" class="p-2.5 rounded-xl hover:bg-white/5 text-slate-400 hover:text-white transition-all">
                <i class="ri-refresh-line text-xl"></i>
            </button>
        </div>
    </header>

    <!-- Chat Space -->
    <main id="chat-container" class="flex-1 overflow-y-auto p-6 md:p-8 space-y-8 chat-container">
        <!-- Welcome Message -->
        <div class="flex gap-5 max-w-4xl message-fade-in">
            <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center flex-shrink-0">
                <i class="ri-robot-2-line text-indigo-400 text-xl"></i>
            </div>
            <div class="glass p-5 rounded-3xl rounded-tl-none">
                <p class="text-[15px] leading-relaxed text-slate-200">
                    Assalomu alaykum! Men sizning universal AI yordamchingizman. 
                    Dasturlash, arxitektura yoki har qanday texnik masalada savollaringizga professional javob berishga tayyorman.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <button onclick="quickAsk('Python-da API yozishni o\'rgat')" class="text-xs glass hover:bg-white/10 text-slate-300 py-2 px-4 rounded-xl transition-all border-none">Python API</button>
                    <button onclick="quickAsk('Siz qaysi tillarni bilasiz?')" class="text-xs glass hover:bg-white/10 text-slate-300 py-2 px-4 rounded-xl transition-all border-none">Imkoniyatlar</button>
                    <button onclick="quickAsk('Optimal SQL sorov yozish')" class="text-xs glass hover:bg-white/10 text-slate-300 py-2 px-4 rounded-xl transition-all border-none">SQL Optimization</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Input Area -->
    <footer class="p-6 md:p-8 border-t border-white/5 bg-slate-900/50 backdrop-blur-xl">
        <form id="chat-form" class="max-w-5xl mx-auto relative group">
            @csrf
            <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-3xl blur opacity-20 group-focus-within:opacity-40 transition duration-500"></div>
            <div class="relative flex items-center">
                <input type="text" id="prompt-input" autocomplete="off" placeholder="Savolingizni bu yerga yozing..." 
                    class="w-full bg-slate-800/80 border border-white/10 rounded-2xl py-5 pl-7 pr-16 focus:outline-none focus:border-indigo-500/50 transition-all text-[15px] text-white placeholder-slate-500">
                <button type="submit" id="send-btn" class="absolute right-2.5 p-3.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95">
                    <i class="ri-send-plane-fill text-xl"></i>
                </button>
            </div>
        </form>
        <div class="mt-4 flex justify-center items-center gap-6 opacity-30">
            <div class="flex items-center gap-2 text-[10px] uppercase tracking-widest font-bold">
                <i class="ri-markdown-line text-sm"></i> Markdown Supported
            </div>
            <div class="flex items-center gap-2 text-[10px] uppercase tracking-widest font-bold">
                <i class="ri-code-s-slash-line text-sm"></i> Syntax Highlighting
            </div>
        </div>
    </footer>

    <script>
        const chatContainer = document.getElementById('chat-container');
        const chatForm = document.getElementById('chat-form');
        const promptInput = document.getElementById('prompt-input');
        const sendBtn = document.getElementById('send-btn');

        // Chat History Storage
        let messages = [
            {
                role: 'system',
                content: 'Siz dunyo darajasidagi professional dasturchi va texnik ekspertsiz. Barcha dasturlash tillari (PHP, JavaScript, Python, Dart/Flutter, C++, Go, Rust va boshqalar), arxitektura, ma\'lumotlar bazasi va sun\'iy intelekt sohalarida chuqur bilimga egasiz. Foydalanuvchi savollariga har doim professional, aniq va texnik jihatdan mukammal javob bering. Javoblaringiz faqat o\'zbek tilida bo\'lsin. Kod misollarini har doim tegishli markdown formatida taqdim eting.'
            }
        ];

        // Configure Marked
        marked.setOptions({
            highlight: function(code, lang) {
                if (lang && hljs.getLanguage(lang)) {
                    return hljs.highlight(code, { language: lang }).value;
                }
                return hljs.highlightAuto(code).value;
            },
            breaks: true
        });

        function appendMessage(role, text) {
            const div = document.createElement('div');
            div.className = `flex gap-5 ${role === 'user' ? 'flex-row-reverse' : ''} max-w-4xl ${role === 'user' ? 'ml-auto' : ''} message-fade-in`;
            
            const avatar = role === 'user' 
                ? '<div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center flex-shrink-0 shadow-lg shadow-indigo-600/20"><i class="ri-user-3-line text-white text-xl"></i></div>'
                : '<div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center flex-shrink-0"><i class="ri-robot-2-line text-indigo-400 text-xl"></i></div>';
            
            const content = `
                <div class="${role === 'user' ? 'bg-indigo-600 text-white rounded-tr-none' : 'glass text-slate-200 rounded-tl-none'} p-5 rounded-3xl shadow-xl prose prose-invert max-w-none">
                    ${role === 'user' ? `<p class="leading-relaxed m-0">${text}</p>` : marked.parse(text)}
                </div>
            `;
            
            div.innerHTML = avatar + content;
            chatContainer.appendChild(div);
            
            if (role === 'bot') {
                div.querySelectorAll('pre code').forEach((block) => {
                    hljs.highlightElement(block);
                    addCopyButton(block.parentElement);
                });
            }

            chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });
        }

        function addCopyButton(pre) {
            if (pre.querySelector('.copy-btn')) return;
            const btn = document.createElement('button');
            btn.className = 'copy-btn';
            btn.innerHTML = '<i class="ri-file-copy-line"></i> Copy';
            btn.onclick = () => {
                navigator.clipboard.writeText(pre.querySelector('code').innerText);
                btn.innerHTML = '<i class="ri-check-line"></i> Copied!';
                setTimeout(() => btn.innerHTML = '<i class="ri-file-copy-line"></i> Copy', 2000);
            };
            pre.appendChild(btn);
        }

        function quickAsk(text) {
            promptInput.value = text;
            chatForm.dispatchEvent(new Event('submit'));
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const prompt = promptInput.value.trim();
            if (!prompt) return;

            // Update UI/History
            promptInput.value = '';
            promptInput.disabled = true;
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="ri-loader-5-line animate-spin text-xl"></i>';

            appendMessage('user', prompt);
            messages.push({ role: 'user', content: prompt });

            const loadingId = 'loading-' + Date.now();
            const loadingDiv = document.createElement('div');
            loadingDiv.id = loadingId;
            loadingDiv.className = 'flex gap-5 max-w-4xl message-fade-in';
            loadingDiv.innerHTML = `
                <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center flex-shrink-0">
                    <i class="ri-robot-2-line text-indigo-400 text-xl"></i>
                </div>
                <div class="glass p-5 rounded-3xl rounded-tl-none flex items-center gap-3">
                    <div class="w-2 h-2 bg-indigo-500 rounded-full animate-bounce [animation-duration:0.6s]"></div>
                    <div class="w-2 h-2 bg-indigo-500 rounded-full animate-bounce [animation-duration:0.6s] [animation-delay:0.2s]"></div>
                    <div class="w-2 h-2 bg-indigo-500 rounded-full animate-bounce [animation-duration:0.6s] [animation-delay:0.4s]"></div>
                </div>
            `;
            chatContainer.appendChild(loadingDiv);
            chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });

            try {
                const response = await fetch('{{ route("ask") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ messages: messages })
                });

                if (!response.ok) throw new Error('API Error');

                loadingDiv.remove();

                const botMsgDiv = document.createElement('div');
                botMsgDiv.className = 'flex gap-5 max-w-4xl message-fade-in';
                botMsgDiv.innerHTML = `
                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center flex-shrink-0">
                        <i class="ri-robot-2-line text-indigo-400 text-xl"></i>
                    </div>
                    <div class="glass p-5 rounded-3xl rounded-tl-none prose prose-invert max-w-none text-slate-200">
                        <div class="markdown-body"></div>
                    </div>
                `;
                chatContainer.appendChild(botMsgDiv);
                
                const mdBody = botMsgDiv.querySelector('.markdown-body');
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let fullText = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;
                    
                    const chunk = decoder.decode(value, { stream: true });
                    fullText += chunk;
                    mdBody.innerHTML = marked.parse(fullText);
                    
                    mdBody.querySelectorAll('pre code').forEach((block) => {
                        hljs.highlightElement(block);
                        addCopyButton(block.parentElement);
                    });
                    
                    chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'instant' });
                }

                // Push bot response to history
                messages.push({ role: 'assistant', content: fullText });

            } catch (error) {
                console.error(error);
                if (document.getElementById(loadingId)) loadingDiv.remove();
                appendMessage('bot', 'Xatolik yuz berdi. Server ulanishini tekshiring.');
            } finally {
                promptInput.disabled = false;
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="ri-send-plane-fill text-xl"></i>';
                promptInput.focus();
            }
        });
    </script>
</body>
</html>
