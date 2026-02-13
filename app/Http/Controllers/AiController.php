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
            'prompt' => 'required|string',
        ]);

        $response = $this->ollama->chat($request->prompt);

        return response()->json([
            'response' => $response,
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
