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
     * Send a prompt to the Ollama API using the Chat API and get a response.
     */
    public function chat(string $prompt)
    {
        try {
            $response = Http::timeout(120)->post("{$this->baseUrl}/api/chat", [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Siz aqlli va yordam berishga tayyor AI yordamchisiz. Barcha savollarga faqat o\'zbek tilida, aniq va tushunarli javob bering. Hech qachon ingliz tilida tarjima qilishingizni so\'rashmasa, tarjima qilmang.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'stream' => false,
            ]);

            if ($response->successful()) {
                return $response->json('message.content');
            }

            Log::error('Ollama API Error: ' . $response->body());
            return 'Xatolik yuz berdi: Ollama javob bermadi.';
        } catch (\Exception $e) {
            Log::error('Ollama Connection Error: ' . $e->getMessage());
            return 'Xatolik yuz berdi: Serverga ulanib bo\'lmadi.';
        }
    }
}
