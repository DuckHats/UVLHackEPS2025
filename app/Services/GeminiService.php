<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected string $apiKey;
    protected string $endpoint;
    protected string $defaultModel;

    public function __construct()
    {
        $this->apiKey        = config('services.gemini.key');
        $this->endpoint      = config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions');
        $this->defaultModel  = config('services.gemini.model', 'gemini-1.5-pro');
    }

    /**
     * Fa una consulta al model de Gemini.
     *
     * @param  string  $prompt     → Prompt principal (usuari)
     * @param  string|null $context → Context addicional (opc)
     * @param  string|null $model   → Model Gemini (opc)
     *
     * @return array|null  → Resposta en format array amb text i cru
     */
    public function ask(string $prompt, ?string $context = null, ?string $model = null): ?array
    {
        $model = $model ?? $this->defaultModel;

        $messages = [];

        if ($context) {
            $messages[] = [
                'role'    => 'system',
                'content' => $context
            ];
        }

        $messages[] = [
            'role'    => 'user',
            'content' => $prompt
        ];
        $response = Http::withToken($this->apiKey)
            ->post($this->endpoint, [
                'model'    => $model,
                'messages' => $messages,
                'temperature' => 0.2,
                'max_tokens'  => 4096,
            ]);

        if (!$response->successful()) {
            return [
                'error'    => true,
                'status'   => $response->status(),
                'response' => $response->json(),
            ];
        }

        $json = $response->json();

        $text = $json['choices'][0]['message']['content'] ?? null;

        return [
            'error'    => false,
            'text'     => $text,
            'raw'      => $json,
        ];
    }
}
