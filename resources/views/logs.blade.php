<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Logs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap');
        .mono { font-family: 'JetBrains Mono', monospace; }
        .log-error { color: #ef4444; }
        .log-info { color: #3b82f6; }
        .log-warning { color: #f59e0b; }
    </style>
</head>
<body class="bg-slate-950 text-slate-300 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="border-b border-slate-800 bg-slate-900/50 backdrop-blur-md sticky top-0 z-10 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="/" class="p-2 hover:bg-slate-800 rounded-lg transition-colors text-slate-400 hover:text-white">
                    <i class="ri-arrow-left-line text-xl"></i>
                </a>
                <h1 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="ri-terminal-box-line text-indigo-400"></i>
                    System Logs
                </h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-slate-800 text-slate-400">
                    laravel.log
                </span>
                <button onclick="window.location.reload()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center gap-2">
                    <i class="ri-refresh-line"></i> Yangilash
                </button>
            </div>
        </div>
    </header>

    <!-- Content -->
    <main class="flex-1 p-4 md:p-8 max-w-7xl mx-auto w-full">
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-2xl">
            <div class="bg-slate-800/50 p-3 border-b border-slate-800 flex items-center justify-between">
                <div class="flex gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-500/20 border border-red-500/50"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-500/20 border border-amber-500/50"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500/20 border border-emerald-500/50"></div>
                </div>
                <div class="text-[10px] uppercase tracking-widest font-bold text-slate-500">Output Console</div>
            </div>
            <div class="p-6 overflow-x-auto">
                @if(empty($logs))
                    <div class="py-20 text-center">
                        <i class="ri-ghost-line text-5xl text-slate-700 mb-4 block"></i>
                        <p class="text-slate-500">Hozircha loglar mavjud emas.</p>
                    </div>
                @else
                    <pre class="mono text-sm leading-relaxed whitespace-pre-wrap">
@foreach(explode("\n", $logs) as $line)
@php
    $class = '';
    if (str_contains(strtolower($line), '.error')) $class = 'log-error';
    elseif (str_contains(strtolower($line), '.warning')) $class = 'log-warning';
    elseif (str_contains(strtolower($line), '.info')) $class = 'log-info';
@endphp<span class="{{ $class }}">{{ $line }}</span>
@endforeach
                    </pre>
                @endif
            </div>
        </div>
    </main>

    <footer class="p-6 text-center text-slate-600 text-xs border-t border-slate-900">
        &copy; {{ date('Y') }} AI Project • Log Viewer
    </footer>
</body>
</html>
