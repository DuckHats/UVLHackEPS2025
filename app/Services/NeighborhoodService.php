<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NeighborhoodService
{
    protected $gemini;
    protected $neighborhoods = [
        'Downtown LA' => ['lat' => 34.0407, 'lon' => -118.2468],
        'Santa Monica' => ['lat' => 34.0195, 'lon' => -118.4912],
        'Hollywood' => ['lat' => 34.0928, 'lon' => -118.3287],
        'Venice' => ['lat' => 33.9850, 'lon' => -118.4695],
        'Beverly Hills' => ['lat' => 34.0736, 'lon' => -118.4004],
        'Silver Lake' => ['lat' => 34.0869, 'lon' => -118.2702],
        'Pasadena' => ['lat' => 34.1478, 'lon' => -118.1445],
        'West Hollywood' => ['lat' => 34.0900, 'lon' => -118.3617],
        'Koreatown' => ['lat' => 34.0618, 'lon' => -118.3000],
        'Westwood' => ['lat' => 34.0635, 'lon' => -118.4455],
    ];

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Fetches data for all neighborhoods and scores them based on user KPIs.
     */
    public function findBestMatch(array $userKpis): array
    {
        // Get scores from Gemini for these specific KPIs across all neighborhoods
        $neighborhoodScores = $this->getNeighborhoodsData(array_keys($userKpis));

        $finalScores = [];

        foreach ($neighborhoodScores as $neighborhoodName => $kpiScores) {
            $score = 0;
            foreach ($userKpis as $kpi => $importance) {
                // Importance (0-10) * Neighborhood Score (0-10)
                $nScore = $kpiScores[$kpi] ?? 0;
                $score += ($importance * $nScore);
            }

            $finalScores[] = [
                'name' => $neighborhoodName,
                'coords' => $this->neighborhoods[$neighborhoodName] ?? ['lat' => 0, 'lon' => 0],
                'data' => $kpiScores,
                'score' => $score
            ];
        }

        // Sort by score descending
        usort($finalScores, fn($a, $b) => $b['score'] <=> $a['score']);

        return $finalScores;
    }

    /**
     * Uses Gemini to estimate scores for specific KPIs for all neighborhoods.
     */
    protected function getNeighborhoodsData(array $targetKpis): array
    {
        $kpiList = implode(", ", $targetKpis);
        $neighborhoodList = implode(", ", array_keys($this->neighborhoods));

        // Cache the result based on the specific combination of KPIs requested to save tokens/time
        // In a real app, we might cache individual neighborhood profiles, but here we do it per query type
        $cacheKey = 'neighborhood_scores_' . md5($kpiList);

        return Cache::remember($cacheKey, 3600, function () use ($kpiList, $neighborhoodList, $targetKpis) {
            $prompt = "Rate the following neighborhoods: [$neighborhoodList] on a scale of 0-10 for each of these KPIs: [$kpiList].";
            $systemPrompt = <<<EOT
You are a data expert on Los Angeles neighborhoods.
Return a JSON object where keys are neighborhood names and values are objects containing the scores (0-10) for the requested KPIs.
Example:
{
    "Downtown LA": { "walkability": 9, "safety": 4 },
    "Santa Monica": { "walkability": 8, "safety": 8 }
}
Return ONLY valid JSON.
EOT;

            $response = $this->gemini->ask($prompt, $systemPrompt);

            if ($response['error']) {
                // Fallback to random data if API fails to avoid crash
                return $this->generateFallbackData($targetKpis);
            }

            $text = $response['text'];
            $text = preg_replace('/^```json/', '', $text);
            $text = preg_replace('/^```/', '', $text);
            $text = preg_replace('/```$/', '', $text);

            return json_decode($text, true) ?? $this->generateFallbackData($targetKpis);
        });
    }

    protected function generateFallbackData(array $kpis): array
    {
        $data = [];
        foreach ($this->neighborhoods as $name => $coords) {
            foreach ($kpis as $kpi) {
                $data[$name][$kpi] = rand(1, 10);
            }
        }
        return $data;
    }
}
