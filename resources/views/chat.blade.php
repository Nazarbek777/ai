<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
        .chat-container::-webkit-scrollbar { width: 6px; }
        .chat-container::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#f8fafc] h-screen flex flex-col">
    <!-- Header -->
    <header class="glass border-b border-slate-200 py-4 px-6 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center gap-3">
            <div class="bg-indigo-600 p-2 rounded-xl text-white">
                <i class="ri-robot-2-line text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">AI Assistant</h1>
                <p class="text-xs text-indigo-600 font-medium flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Online (Ollama)
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="/logs" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1 bg-slate-100 hover:bg-slate-200 py-2 px-3 rounded-lg">
                <i class="ri-terminal-window-line"></i> Loglar
            </a>
        </div>
    </header>

    <!-- Chat Space -->
    <main id="chat-container" class="flex-1 overflow-y-auto p-6 space-y-6 chat-container">
        <!-- Welcome Message -->
        <div class="flex gap-4 max-w-3xl">
            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0">
                <i class="ri-robot-line text-slate-600"></i>
            </div>
            <div class="bg-white border border-slate-100 p-4 rounded-2xl rounded-tl-none shadow-sm">
                <p class="text-slate-700 leading-relaxed font-medium">Assalomu alaykum! Men sizning shaxsiy AI yordamchingizman. Menga xohlagan savolingizni berishingiz mumkin.</p>
                <div class="mt-3 flex gap-2">
                    <button onclick="quickAsk('Salom!')" class="text-xs bg-slate-50 hover:bg-slate-100 text-slate-600 py-1.5 px-3 rounded-lg border border-slate-100 transition-colors">Salom!</button>
                    <button onclick="quickAsk('PHP nima?')" class="text-xs bg-slate-50 hover:bg-slate-100 text-slate-600 py-1.5 px-3 rounded-lg border border-slate-100 transition-colors">PHP nima?</button>
                    <button onclick="quickAsk('Ollama haqida gapir')" class="text-xs bg-slate-50 hover:bg-slate-100 text-slate-600 py-1.5 px-3 rounded-lg border border-slate-100 transition-colors">Ollama haqida gapir</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Input Area -->
    <footer class="p-6 bg-white border-t border-slate-200">
        <form id="chat-form" class="max-w-4xl mx-auto relative">
            @csrf
            <input type="text" id="prompt-input" autocomplete="off" placeholder="Xabaringizni yozing..." 
                class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 pl-6 pr-14 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all text-slate-800">
            <button type="submit" id="send-btn" class="absolute right-2 top-2 bottom-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 rounded-xl transition-all flex items-center justify-center">
                <i class="ri-send-plane-2-fill text-lg"></i>
            </button>
        </form>
        <p class="text-[10px] text-center text-slate-400 mt-4 uppercase tracking-widest font-semibold">Powered by Ollama • Built with Laravel</p>
    </footer>

    <script>
        const chatContainer = document.getElementById('chat-container');
        const chatForm = document.getElementById('chat-form');
        const promptInput = document.getElementById('prompt-input');
        const sendBtn = document.getElementById('send-btn');

        function appendMessage(role, text) {
            const div = document.createElement('div');
            div.className = `flex gap-4 ${role === 'user' ? 'flex-row-reverse' : ''} max-w-3xl ${role === 'user' ? 'ml-auto' : ''}`;
            
            const avatar = role === 'user' 
                ? '<div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0"><i class="ri-user-line text-indigo-600"></i></div>'
                : '<div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0"><i class="ri-robot-line text-slate-600"></i></div>';
            
            const content = `
                <div class="${role === 'user' ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white border border-slate-100 text-slate-700 rounded-tl-none'} p-4 rounded-2xl shadow-sm">
                    <p class="leading-relaxed">${text}</p>
                </div>
            `;
            
            div.innerHTML = avatar + content;
            chatContainer.appendChild(div);
            chatContainer.scrollTo({ top: chatContainer.scrollHeight, behavior: 'smooth' });
        }

        function quickAsk(text) {
            promptInput.value = text;
            chatForm.dispatchEvent(new Event('submit'));
        }

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const prompt = promptInput.value.trim();
            if (!prompt) return;

            // Disable UI
            promptInput.value = '';
            promptInput.disabled = true;
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="ri-loader-4-line animate-spin text-lg"></i>';

            // Append User Message
            appendMessage('user', prompt);

            // Append Loading Indicator
            const loadingId = 'loading-' + Date.now();
            const loadingDiv = document.createElement('div');
            loadingDiv.id = loadingId;
            loadingDiv.className = 'flex gap-4 max-w-3xl';
            loadingDiv.innerHTML = `
                <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0">
                    <i class="ri-robot-line text-slate-600"></i>
                </div>
                <div class="bg-white border border-slate-100 p-4 rounded-2xl rounded-tl-none shadow-sm flex items-center gap-2">
                    <div class="w-2 h-2 bg-slate-300 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-slate-300 rounded-full animate-bounce [animation-delay:-.3s]"></div>
                    <div class="w-2 h-2 bg-slate-300 rounded-full animate-bounce [animation-delay:-.5s]"></div>
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
                    body: JSON.stringify({ prompt: prompt })
                });

                const data = await response.json();
                
                // Remove loading
                document.getElementById(loadingId).remove();

                // Append AI Response
                appendMessage('bot', data.response || 'Kechirasiz, javob olishda xatolik yuz berdi.');
            } catch (error) {
                document.getElementById(loadingId).remove();
                appendMessage('bot', 'Xatolik yuz berdi. Iltimos, server ulanishini tekshiring.');
            } finally {
                promptInput.disabled = false;
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="ri-send-plane-2-fill text-lg"></i>';
                promptInput.focus();
            }
        });
    </script>
</body>
</html>
