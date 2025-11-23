<?php

namespace App\Services;

use App\Helpers\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class NeighborhoodService
{
    protected $gemini;
    protected $overpass;

    public function __construct(GeminiService $gemini, OverpassService $overpass)
    {
        $this->gemini = $gemini;
        $this->overpass = $overpass;
    }

    /**
     * Fetches data for all neighborhoods and scores them based on user KPIs.
     */
    public function findBestMatch(array $userKpis): array
    {
        $allNeighborhoods = $this->fetchAllNeighborhoods();

        $topCandidates = $this->filterCandidates($allNeighborhoods, $userKpis);

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

        usort($finalScores, fn($a, $b) => $b['score'] <=> $a['score']);

        foreach ($finalScores as &$candidate) {
            $realKpis = $this->overpass->getKpisForNeighborhood(
                $candidate['coords']['lat'],
                $candidate['coords']['lon'],
                array_keys($userKpis)
            );
            $candidate['amenities'] = $realKpis;
        }
        unset($candidate); // Break reference

        return $finalScores;
    }

    /**
     * Fetches all LA neighborhoods from Overpass.
     */
    protected function fetchAllNeighborhoods(): array
    {
        if (Storage::exists('neighborhoods.json')) {
            $json = json_decode(Storage::get('neighborhoods.json'), true);
            if (is_array($json) && count($json) > 0) {
                return $json;
            }
        }

        $url = Config::get('services.overpass.url');

        try {
            $response = Http::timeout(20)->retry(3, 300)->get($url);

            if ($response->failed()) {
                throw new \Exception("Overpass failed");
            }

            $json = $response->json();
            if (!isset($json['elements'])) {
                throw new \Exception("Invalid Overpass format");
            }

            $neighborhoods = [];
            foreach ($json['elements'] as $el) {
                if (!isset($el['tags']['name'])) continue;

                $name = $el['tags']['name'];
                $lat  = $el['lat'] ?? ($el['center']['lat'] ?? null);
                $lon  = $el['lon'] ?? ($el['center']['lon'] ?? null);

                if ($lat && $lon) {
                    $neighborhoods[$name] = ['lat' => $lat, 'lon' => $lon];
                }
            }

            if (count($neighborhoods) > 20) {
                Storage::put('neighborhoods.json', json_encode($neighborhoods, JSON_PRETTY_PRINT));
            }

            return $neighborhoods;
        } catch (\Throwable $e) {
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
    }


    /**
     * Filters the list of all neighborhoods to the top 10 candidates.
     */
    protected function filterCandidates(array $allNeighborhoods, array $userKpis): array
    {
        $names = array_keys($allNeighborhoods);
        if (count($names) <= 10) {
            return $names;
        }

        $userProfileJson = json_encode($userKpis);
        $neighborhoodListJson = json_encode($names);

        $systemPrompt = $this->gemini->getPrompt('filter_neighborhoods', [
            'user_profile' => $userProfileJson,
            'neighborhoods_list' => $neighborhoodListJson
        ]);

        $response = $this->gemini->ask("Select top 10.", $systemPrompt);

        if ($response['error']) {
            return array_slice($names, 0, 10);
        }

        $text = $response['text'];

        $text = Utils::clearMdSyntax($text);

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
            $text = Utils::clearMdSyntax($text);

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
