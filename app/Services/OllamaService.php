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
                // Limit history to last 15 messages to avoid context confusion
                if (count($messages) > 15) {
                    $systemMessage = $messages[0]; // Keep system prompt
                    $history = array_slice($messages, -14);
                    $messages = array_merge([$systemMessage], $history);
                }
            } else {
                $messages = [
                    [
                        'role' => 'system',
                        'content' => 'Siz aqlli va yordam berishga tayyor AI yordamchisiz. Har doim faqat o\'zbek tilida tabiiy muloqot qiling. Javoblaringiz qisqa, aniq va grammatik jihatdan to\'g\'ri bo\'lsin.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $promptOrMessages
                    ]
                ];
            }

            Log::info("Ollama Request: model={$this->model}, messages=" . count($messages));
            
            $response = Http::timeout(120)->withOptions([
                'stream' => true,
            ])->post("{$this->baseUrl}/api/chat", [
                'model' => $this->model,
                'messages' => $messages,
                'stream' => (bool)$onChunk,
                'options' => [
                    'temperature' => 0.2,
                    'num_ctx' => 4096,
                    'top_k' => 40,
                    'top_p' => 0.9,
                    'repeat_penalty' => 1.1,
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Ollama API Error Status: ' . $response->status());
                Log::error('Ollama API Error Body: ' . $response->body());
                if ($onChunk) {
                    $onChunk("Xatolik: Ollama serveridan xato keldi (" . $response->status() . "). Ehtimol tanlangan model rasm bilan ishlashni qo'llab-quvvatlamaydi.");
                    return;
                }
            }

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
