<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->baseUrl = config('services.ollama.base_url', env('OLLAMA_BASE_URL', 'http://localhost:11434'));
        $this->model = config('services.ollama.model', env('OLLAMA_MODEL', 'llama3'));
    }

    /**
     * Send a prompt to the Ollama API and get a response.
     */
    public function chat(string $prompt)
    {
        try {
            $response = Http::timeout(60)->post("{$this->baseUrl}/api/generate", [
                'model' => $this->model,
                'prompt' => $prompt,
                'stream' => false,
            ]);

            if ($response->successful()) {
                return $response->json('response');
            }

            Log::error('Ollama API Error: ' . $response->body());
            return 'Xatolik yuz berdi: Ollama javob bermadi.';
        } catch (\Exception $e) {
            Log::error('Ollama Connection Error: ' . $e->getMessage());
            return 'Xatolik yuz berdi: Serverga ulanib bo\'lmadi.';
        }
    }
}
