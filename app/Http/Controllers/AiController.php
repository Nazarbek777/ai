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
}
