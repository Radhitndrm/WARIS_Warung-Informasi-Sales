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
        $this->model = config('services.ollama.model', env('OLLAMA_MODEL', 'nous-hermes:7b'));
    }

    public function chat(array $messages, string $userMessage = ''): string
    {
        $context = '';
        if ($userMessage) {
            $context = (new DbContextService)->getContext($userMessage);
        }

        $currentQuestion = '';
        if (!empty($messages)) {
            $last = array_key_last($messages);
            if ($messages[$last]['role'] === 'user') {
                $currentQuestion = $messages[$last]['content'];
            }
        }

        $system = "Anda adalah asisten kasir WARIS yang selalu menjawab dalam Bahasa Indonesia.\n\n";
        $system .= "Gunakan data berikut untuk menjawab pertanyaan:\n{$context}";

        $payload = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $currentQuestion],
            ],
            'stream' => false,
            'options' => [
                'temperature' => 0.1,
            ],
        ];

        try {
            set_time_limit(120);
            $response = Http::timeout(120)
                ->post("{$this->baseUrl}/api/chat", $payload);

            if ($response->failed()) {
                Log::error('Ollama API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return 'Maaf, saya mengalami gangguan koneksi ke model AI.';
            }

            $data = $response->json();

            return $data['message']['content'] ?? 'Maaf, saya tidak dapat memproses permintaan Anda.';
        } catch (\Exception $e) {
            Log::error('Ollama connection error', [
                'message' => $e->getMessage(),
            ]);
            return 'Maaf, saya tidak dapat terhubung ke Ollama. Pastikan Ollama sudah berjalan.';
        }
    }
}
