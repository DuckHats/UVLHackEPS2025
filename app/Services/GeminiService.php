<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Helpers\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected string $apiKey;
    protected string $endpoint;
    protected string $defaultModel;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->endpoint = config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions');
        $this->defaultModel = config('services.gemini.model', 'gemini-1.5-pro');
    }

    /**
     * Ask Gemini a question with optional context.
     *
     * @param string $prompt The user's question/prompt
     * @param string|null $context System context for the AI
     * @param string|null $model Override default model
     * @return array Response with 'error', 'text', and optionally 'raw' keys
     */
    public function ask(string $prompt, ?string $context = null, ?string $model = null): array
    {
        $cacheKey = $this->generateCacheKey($prompt, $context);

        return Cache::remember($cacheKey, AppConstants::CACHE_TTL_GEMINI_RESPONSE, function () use ($prompt, $context, $model) {
            return $this->makeRealRequest($prompt, $context, $model);
        });
    }

    /**
     * Makes the actual HTTP request to Gemini API.
     */
    protected function makeRealRequest(string $prompt, ?string $context = null, ?string $model = null): array
    {
        $model = $model ?? $this->defaultModel;
        $messages = $this->buildMessages($prompt, $context);

        try {
            $response = Http::withToken($this->apiKey)
                ->post($this->endpoint, [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => AppConstants::GEMINI_DEFAULT_TEMPERATURE,
                    'max_tokens' => AppConstants::GEMINI_MAX_TOKENS,
                ]);

            if (!$response->successful()) {
                Log::warning('Gemini API request failed', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);

                return [
                    'error' => true,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ];
            }

            return $this->parseSuccessfulResponse($response->json());
        } catch (\Exception $e) {
            Log::error('Gemini API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build messages array for API request.
     */
    protected function buildMessages(string $prompt, ?string $context): array
    {
        $messages = [];

        if ($context) {
            $messages[] = [
                'role' => 'system',
                'content' => $context
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $prompt
        ];

        return $messages;
    }

    /**
     * Parse successful API response.
     */
    protected function parseSuccessfulResponse(array $json): array
    {
        $text = $json['choices'][0]['message']['content'] ?? null;

        return [
            'error' => false,
            'text' => $text,
            'raw' => $json,
        ];
    }

    /**
     * Generate cache key for request.
     */
    protected function generateCacheKey(string $prompt, ?string $context): string
    {
        return 'gemini_' . md5($prompt . ($context ?? ''));
    }

    /**
     * Reads a prompt file from storage and replaces placeholders.
     *
     * @param string $name Prompt file name (without .md extension)
     * @param array $replacements Key-value pairs for placeholder replacement
     * @return string The processed prompt content
     */
    public function getPrompt(string $name, array $replacements = []): string
    {
        $path = storage_path("prompts/{$name}.md");
        
        if (!file_exists($path)) {
            Log::error("Prompt file not found: {$name}");
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
     *
     * @param string $userPrompt User's description of their preferences
     * @return array Parsed profile with 'archetype' and 'kpis', or error
     */
    public function analyzeProfile(string $userPrompt): array
    {
        $kpisJson = file_get_contents(storage_path('kpis.json'));
        $systemPrompt = $this->getPrompt('analyze_profile', ['kpis_json' => $kpisJson]);

        $response = $this->ask($userPrompt, $systemPrompt);

        if ($response['error']) {
            return ['error' => true, 'message' => 'Gemini API Error'];
        }

        $text = Utils::clearMdSyntax($response['text']);
        $parsed = json_decode($text, true);

        if (!$parsed) {
            Log::error('Invalid JSON from Gemini analyzeProfile', ['raw' => $text]);
            return ['error' => true, 'message' => 'Invalid JSON from Gemini', 'raw' => $text];
        }

        return $parsed;
    }

    /**
     * Generates a justification for the recommended neighborhood.
     *
     * @param array $userProfile User's profile data
     * @param array $neighborhoodData Best match neighborhood data
     * @return string Justification text
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
