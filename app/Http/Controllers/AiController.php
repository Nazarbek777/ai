<?php

namespace App\Http\Controllers;

use App\Services\OllamaService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    protected OllamaService $ollama;

    public function __construct(OllamaService $ollama)
    {
        $this->ollama = $ollama;
    }

    public function index()
    {
        return view('chat');
    }

    public function ask(Request $request)
    {
        $request->validate([
            'prompt' => 'required_without:messages|string',
            'messages' => 'array',
        ]);

        $data = $request->has('messages') ? $request->messages : $request->prompt;

        return response()->stream(function () use ($data) {
            $this->ollama->chat($data, function ($chunk) {
                echo $chunk;
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();
            });
        }, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no', // For Nginx
        ]);
    }

    public function logs()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = '';

        if (file_exists($logPath)) {
            $logs = file_get_contents($logPath);
            // Reverse logs to show newest first
            $lines = explode("\n", trim($logs));
            $logs = implode("\n", array_reverse($lines));
        }

        return view('logs', compact('logs'));
    }
}
