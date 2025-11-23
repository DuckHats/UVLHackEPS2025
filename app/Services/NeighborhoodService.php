<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Helpers\Utils;
use App\Providers\FallbackNeighborhoodProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
     *
     * @param array $userKpis User's KPI preferences with importance weights
     * @return array Sorted array of neighborhood matches with scores
     */
    public function findBestMatch(array $userKpis): array
    {
        $allNeighborhoods = $this->fetchAllNeighborhoods();
        $topCandidates = $this->filterCandidates($allNeighborhoods, $userKpis);
        $neighborhoodScores = $this->getNeighborhoodsData($topCandidates, array_keys($userKpis));

        $finalScores = $this->calculateFinalScores($neighborhoodScores, $userKpis, $allNeighborhoods);
        $this->enrichWithRealTimeAmenities($finalScores, $userKpis);

        return $finalScores;
    }

    /**
     * Calculate final scores for neighborhoods based on user KPIs.
     */
    protected function calculateFinalScores(array $neighborhoodScores, array $userKpis, array $allNeighborhoods): array
    {
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

        return $finalScores;
    }

    /**
     * Enrich neighborhood data with real-time amenities from Overpass.
     */
    protected function enrichWithRealTimeAmenities(array &$finalScores, array $userKpis): void
    {
        foreach ($finalScores as &$candidate) {
            $realKpis = $this->overpass->getKpisForNeighborhood(
                $candidate['coords']['lat'],
                $candidate['coords']['lon'],
                array_keys($userKpis)
            );
            $candidate['amenities'] = $realKpis;
        }
        unset($candidate); // Break reference
    }

    /**
     * Fetches all Los Angeles neighborhoods from Overpass API or cache.
     *
     * @return array Associative array of neighborhood names to coordinates
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
            $response = Http::timeout(AppConstants::OVERPASS_TIMEOUT_SECONDS)
                ->retry(AppConstants::OVERPASS_RETRY_ATTEMPTS, AppConstants::OVERPASS_RETRY_DELAY_MS)
                ->get($url);

            if ($response->failed()) {
                throw new \Exception("Overpass API request failed");
            }

            $json = $response->json();
            if (!isset($json['elements'])) {
                throw new \Exception("Invalid Overpass response format");
            }

            $neighborhoods = $this->parseOverpassElements($json['elements']);

            if (count($neighborhoods) > AppConstants::MIN_NEIGHBORHOODS_TO_CACHE) {
                Storage::put('neighborhoods.json', json_encode($neighborhoods, JSON_PRETTY_PRINT));
            }

            return $neighborhoods;
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch neighborhoods from Overpass, using fallback', [
                'error' => $e->getMessage()
            ]);
            return FallbackNeighborhoodProvider::getDefaultNeighborhoods();
        }
    }

    /**
     * Parse Overpass API elements into neighborhood array.
     */
    protected function parseOverpassElements(array $elements): array
    {
        $neighborhoods = [];
        foreach ($elements as $el) {
            if (!isset($el['tags']['name'])) continue;

            $name = $el['tags']['name'];
            $lat  = $el['lat'] ?? ($el['center']['lat'] ?? null);
            $lon  = $el['lon'] ?? ($el['center']['lon'] ?? null);

            if ($lat && $lon) {
                $neighborhoods[$name] = ['lat' => $lat, 'lon' => $lon];
            }
        }
        return $neighborhoods;
    }


    /**
     * Filters the list of all neighborhoods to the top candidates using Gemini.
     *
     * @param array $allNeighborhoods All available neighborhoods
     * @param array $userKpis User's KPI preferences
     * @return array Top candidate neighborhood names
     */
    protected function filterCandidates(array $allNeighborhoods, array $userKpis): array
    {
        $names = array_keys($allNeighborhoods);
        if (count($names) <= AppConstants::MIN_NEIGHBORHOODS_FOR_FILTERING) {
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
            return array_slice($names, 0, AppConstants::MAX_CANDIDATE_NEIGHBORHOODS);
        }

        $text = Utils::clearMdSyntax($response['text']);
        $candidates = json_decode($text, true);
        
        return is_array($candidates) 
            ? $candidates 
            : array_slice($names, 0, AppConstants::MAX_CANDIDATE_NEIGHBORHOODS);
    }

    /**
     * Uses Gemini to estimate scores for specific KPIs for selected neighborhoods.
     *
     * @param array $neighborhoodNames Selected neighborhood names
     * @param array $targetKpis KPIs to score
     * @return array Neighborhood scores indexed by name
     */
    protected function getNeighborhoodsData(array $neighborhoodNames, array $targetKpis): array
    {
        $kpiList = implode(", ", $targetKpis);
        $neighborhoodList = implode(", ", $neighborhoodNames);

        $cacheKey = 'neighborhood_scores_' . md5($neighborhoodList . $kpiList);

        return Cache::remember($cacheKey, AppConstants::CACHE_TTL_NEIGHBORHOODS, function () use ($kpiList, $neighborhoodList, $neighborhoodNames, $targetKpis) {
            $systemPrompt = $this->gemini->getPrompt('score_neighborhoods', [
                'neighborhood_list' => $neighborhoodList,
                'kpi_list' => $kpiList
            ]);

            $response = $this->gemini->ask("Score them.", $systemPrompt);

            if ($response['error']) {
                return FallbackNeighborhoodProvider::generateFallbackData($neighborhoodNames, $targetKpis);
            }

            $text = Utils::clearMdSyntax($response['text']);
            $parsed = json_decode($text, true);

            return $parsed ?? FallbackNeighborhoodProvider::generateFallbackData($neighborhoodNames, $targetKpis);
        });
    }


}
