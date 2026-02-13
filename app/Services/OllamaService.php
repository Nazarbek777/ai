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
     * Send a prompt or message history to the Ollama API using the Chat API.
     * Supports streaming if a callback is provided.
     */
    public function chat($promptOrMessages, ?callable $onChunk = null)
    {
        try {
            if (is_array($promptOrMessages)) {
                $messages = $promptOrMessages;
            } else {
                $messages = [
                    [
                        'role' => 'system',
                        'content' => 'Siz dunyo darajasidagi professional dasturchi va texnik ekspertsiz. Barcha dasturlash tillari (PHP, JavaScript, Python, Dart/Flutter, C++, Go, Rust va boshqalar), arxitektura, ma\'lumotlar bazasi va sun\'iy intelekt sohalarida chuqur bilimga egasiz. Foydalanuvchi savollariga har doim professional, aniq va texnik jihatdan mukammal javob bering. Javoblaringiz faqat o\'zbek tilida bo\'lsin. Kod misollarini har doim tegishli markdown formatida taqdim eting.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $promptOrMessages
                    ]
                ];
            }

            $response = Http::timeout(120)->withOptions([
                'stream' => true,
            ])->post("{$this->baseUrl}/api/chat", [
                'model' => $this->model,
                'messages' => $messages,
                'stream' => (bool)$onChunk,
            ]);

            if ($onChunk) {
                $body = $response->toPsrResponse()->getBody();
                $buffer = '';
                while (!$body->eof()) {
                    $chunk = $body->read(1024);
                    $buffer .= $chunk;
                    
                    // Ollama sends multiple JSON objects, one per line or in chunks
                    while (($pos = strpos($buffer, "\n")) !== false) {
                        $line = substr($buffer, 0, $pos);
                        $buffer = substr($buffer, $pos + 1);
                        
                        if (trim($line)) {
                            $data = json_decode($line, true);
                            if (isset($data['message']['content'])) {
                                $onChunk($data['message']['content']);
                            }
                        }
                    }
                }
                return;
            }

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
