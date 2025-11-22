<?php

namespace App\Services;

use App\Helpers\Utils;
use Illuminate\Support\Facades\Cache;
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

    public function ask(string $prompt, ?string $context = null, ?string $model = null): ?array
    {
        $cacheKey = 'gemini_dev_' . md5($prompt . $context);

        return Cache::remember($cacheKey, 86400, function () use ($prompt, $context, $model) {
            return $this->makeRealRequest($prompt, $context, $model);
        });
    }

    protected function makeRealRequest(string $prompt, ?string $context = null, ?string $model = null): ?array
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

    /**
     * Reads a prompt file from storage.
     */
    public function getPrompt(string $name, array $replacements = []): string
    {
        $path = storage_path("prompts/{$name}.md");
        if (!file_exists($path)) {
            return "Error: Prompt file {$name} not found.";
        }

        $content = file_get_contents($path);

        foreach ($replacements as $key => $value) {
            $content = str_replace("{{" . $key . "}}", $value, $content);
        }

        return $content;
    }

    /**
     * Analyzes the user profile to extract KPIs and Archetype.
     */
    public function analyzeProfile(string $userPrompt): array
    {
        $kpisJson = file_get_contents(storage_path('kpis.json'));
        $systemPrompt = $this->getPrompt('analyze_profile', ['kpis_json' => $kpisJson]);

        $response = $this->ask($userPrompt, $systemPrompt);

        if ($response['error']) {
            return ['error' => true, 'message' => 'Gemini API Error'];
        }

        $text = $response['text'];

        $text = Utils::clearMdSyntax($text);

        return json_decode($text, true) ?? ['error' => true, 'message' => 'Invalid JSON from Gemini', 'raw' => $text];
    }

    /**
     * Generates a justification for the recommended neighborhood.
     */
    public function justifyRecommendation(array $userProfile, array $neighborhoodData): string
    {
        $systemPrompt = $this->getPrompt('justify_recommendation', [
            'user_profile' => json_encode($userProfile),
            'neighborhood_data' => json_encode($neighborhoodData)
        ]);

        $response = $this->ask("Generate justification.", $systemPrompt);
        return $response['text'] ?? "The ravens were lost on their way.";
    }
}
