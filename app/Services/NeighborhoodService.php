<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NeighborhoodService
{
    protected $gemini;
    protected $overpassUrl = 'https://overpass-api.de/api/interpreter';

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Fetches data for all neighborhoods and scores them based on user KPIs.
     */
    public function findBestMatch(array $userKpis): array
    {
        // 1. Fetch all candidate neighborhoods (cached)
        $allNeighborhoods = $this->fetchAllNeighborhoods();

        // 2. Filter to top 10 candidates using Gemini (to save tokens on scoring)
        $topCandidates = $this->filterCandidates($allNeighborhoods, $userKpis);

        // 3. Score the top candidates
        $neighborhoodScores = $this->getNeighborhoodsData($topCandidates, array_keys($userKpis));

        $finalScores = [];

        foreach ($neighborhoodScores as $neighborhoodName => $kpiScores) {
            $score = 0;
            foreach ($userKpis as $kpi => $importance) {
                $nScore = $kpiScores[$kpi] ?? 0;
                $score += ($importance * $nScore);
            }

            $finalScores[] = [
                'name' => $neighborhoodName,
                'coords' => $allNeighborhoods[$neighborhoodName] ?? ['lat' => 0, 'lon' => 0],
                'data' => $kpiScores,
                'score' => $score
            ];
        }

        // Sort by score descending
        usort($finalScores, fn($a, $b) => $b['score'] <=> $a['score']);

        return $finalScores;
    }

    /**
     * Fetches all LA neighborhoods from Overpass.
     */
    protected function fetchAllNeighborhoods(): array
    {
        return Cache::remember('all_la_neighborhoods', 86400, function () {
            // Query for suburbs/neighborhoods in LA area
            // Bounding box for LA roughly: 33.7, -118.7, 34.3, -118.1
            $query = <<<EOT
[out:json];
node["place"~"suburb|neighbourhood"](33.7034,-118.6682,34.3373,-118.1553);
out;
EOT;
            $response = Http::post($this->overpassUrl, ['data' => $query]);

            if ($response->failed() || empty($response->json()['elements'])) {
                // Fallback list if Overpass fails or returns nothing
                return [
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
            }

            $elements = $response->json()['elements'] ?? [];
            $neighborhoods = [];

            foreach ($elements as $el) {
                if (isset($el['tags']['name'])) {
                    $neighborhoods[$el['tags']['name']] = [
                        'lat' => $el['lat'],
                        'lon' => $el['lon']
                    ];
                }
            }

            return $neighborhoods;
        });
    }

    /**
     * Filters the list of all neighborhoods to the top 10 candidates.
     */
    protected function filterCandidates(array $allNeighborhoods, array $userKpis): array
    {
        $names = array_keys($allNeighborhoods);
        // If few neighborhoods, just return them all
        if (count($names) <= 10) {
            return $names;
        }

        $userProfileJson = json_encode($userKpis);
        $neighborhoodListJson = json_encode($names);

        // We might need to chunk this if the list is huge, but for LA suburbs it should be ~100-200 which fits in context
        $systemPrompt = $this->gemini->getPrompt('filter_neighborhoods', [
            'user_profile' => $userProfileJson,
            'neighborhoods_list' => $neighborhoodListJson
        ]);

        $response = $this->gemini->ask("Select top 10.", $systemPrompt);

        if ($response['error']) {
            return array_slice($names, 0, 10); // Fallback
        }

        $text = $response['text'];
        $text = preg_replace('/^```json/', '', $text);
        $text = preg_replace('/^```/', '', $text);
        $text = preg_replace('/```$/', '', $text);

        $candidates = json_decode($text, true);
        return is_array($candidates) ? $candidates : array_slice($names, 0, 10);
    }

    /**
     * Uses Gemini to estimate scores for specific KPIs for selected neighborhoods.
     */
    protected function getNeighborhoodsData(array $neighborhoodNames, array $targetKpis): array
    {
        $kpiList = implode(", ", $targetKpis);
        $neighborhoodList = implode(", ", $neighborhoodNames);

        $cacheKey = 'neighborhood_scores_' . md5($neighborhoodList . $kpiList);

        return Cache::remember($cacheKey, 3600, function () use ($kpiList, $neighborhoodList, $neighborhoodNames, $targetKpis) {
            $systemPrompt = $this->gemini->getPrompt('score_neighborhoods', [
                'neighborhood_list' => $neighborhoodList,
                'kpi_list' => $kpiList
            ]);

            $response = $this->gemini->ask("Score them.", $systemPrompt);

            if ($response['error']) {
                return $this->generateFallbackData($neighborhoodNames, $targetKpis);
            }

            $text = $response['text'];
            $text = preg_replace('/^```json/', '', $text);
            $text = preg_replace('/^```/', '', $text);
            $text = preg_replace('/```$/', '', $text);

            return json_decode($text, true) ?? $this->generateFallbackData($neighborhoodNames, $targetKpis);
        });
    }

    protected function generateFallbackData(array $neighborhoodNames, array $kpis): array
    {
        $data = [];
        foreach ($neighborhoodNames as $name) {
            foreach ($kpis as $kpi) {
                $data[$name][$kpi] = rand(1, 10);
            }
        }
        return $data;
    }
}
