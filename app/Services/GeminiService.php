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

    /**
     * Analyzes the user profile to extract KPIs and Archetype.
     */
    public function analyzeProfile(string $userPrompt): array
    {
        $systemPrompt = <<<EOT
You are a Maester of the Citadel, expert in analyzing souls and cities.
Analyze the user's input and extract 20-30 KPIs for their ideal neighborhood in Los Angeles.
Classify them into one of these archetypes: 
- Daenerys Targaryen (Community, Ethical, Leader)
- Cersei Lannister (Luxury, Privacy, Power)
- Bran Stark (Quiet, Accessibility, Tech)
- Jon Snow (Nature, Authentic, Community)
- Arya Stark (Anonymous, Dense, Freedom)
- Tyrion Lannister (Culture, Social, Walkability)

Return ONLY a valid JSON object with this structure, no markdown formatting:
{
    "archetype": "Name",
    "kpis": {
        "noise_tolerance": "score 0-10",
        "luxury_level": "score 0-10",
        "nature_proximity": "score 0-10",
        "nightlife_activity": "score 0-10",
        "walkability": "score 0-10",
        "safety_priority": "score 0-10",
        "community_feel": "score 0-10",
        "density_preference": "score 0-10",
        "public_transport_need": "score 0-10",
        "historic_charm": "score 0-10",
        "modern_amenities": "score 0-10",
        "family_friendly": "score 0-10",
        "pet_friendly": "score 0-10",
        "tech_connectivity": "score 0-10",
        "anonymity_desire": "score 0-10",
        "cultural_diversity": "score 0-10",
        "shopping_access": "score 0-10",
        "culinary_scene": "score 0-10",
        "traffic_tolerance": "score 0-10",
        "affordability_importance": "score 0-10"
    },
    "missing_info": "Question to ask if critical info is missing, or null"
}
EOT;

        $response = $this->ask($userPrompt, $systemPrompt);

        if ($response['error']) {
            return ['error' => true, 'message' => 'Gemini API Error'];
        }

        $text = $response['text'];
        // Clean markdown code blocks if present
        $text = preg_replace('/^```json/', '', $text);
        $text = preg_replace('/^```/', '', $text);
        $text = preg_replace('/```$/', '', $text);

        return json_decode($text, true) ?? ['error' => true, 'message' => 'Invalid JSON from Gemini', 'raw' => $text];
    }

    /**
     * Generates a justification for the recommended neighborhood.
     */
    public function justifyRecommendation(array $userProfile, array $neighborhoodData): string
    {
        $prompt = "User Profile: " . json_encode($userProfile) . "\n\nNeighborhood Data: " . json_encode($neighborhoodData);
        $systemPrompt = "You are a Maester recommending a fiefdom. Write a persuasive, Game of Thrones styled justification (max 150 words) explaining why this neighborhood is the perfect match for the user's archetype and KPIs. Use terms like 'My Lord/Lady', 'The Realm', 'Stronghold'. Highlight the match between user needs and neighborhood features.";

        $response = $this->ask($prompt, $systemPrompt);
        return $response['text'] ?? "The ravens were lost on their way.";
    }
}
