<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Helpers\Utils;

class OverpassService
{
    protected $gemini;
    protected $baseUrl = 'https://overpass-api.de/api/interpreter';

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    /**
     * Fetches KPIs for a specific location based on user interests.
     *
     * @param float $lat
     * @param float $lon
     * @param array $interests List of user interests (strings)
     * @param int $radius Radius in meters (default 1000m)
     * @return array
     */
    public function getKpisForNeighborhood(float $lat, float $lon, array $interests, int $radius = 1000): array
    {
        $mapping = $this->mapInterestsToTags($interests);

        // Build query to fetch elements
        $query = "[out:json][timeout:25];\n(\n";
        foreach ($mapping as $category => $tags) {
            foreach ($tags as $tag) {
                if (isset($tag['key']) && isset($tag['value'])) {
                    // node["key"="value"](around:radius,lat,lon);
                    $query .= "node[\"{$tag['key']}\"=\"{$tag['value']}\"](around:{$radius},{$lat},{$lon});\n";
                    $query .= "way[\"{$tag['key']}\"=\"{$tag['value']}\"](around:{$radius},{$lat},{$lon});\n";
                    // Relations might be too heavy/complex for simple counts, skipping for speed
                }
            }
        }
        $query .= ");\nout tags center qt;";

        $data = $this->executeQuery($query);

        return $this->countElements($data['elements'] ?? [], $mapping);
    }

    protected function mapInterestsToTags(array $interests): array
    {
        $interestsList = json_encode($interests);
        $cacheKey = 'overpass_mapping_v4_' . md5($interestsList);

        $callback = function () use ($interestsList) {
            $prompt = $this->gemini->getPrompt('map_interests_to_overpass', ['user_interests' => $interestsList]);
            $response = $this->gemini->ask("Map these interests to Overpass tags.", $prompt);

            if ($response['error']) {
                Log::error("Gemini failed to map interests: " . json_encode($response));
                return [];
            }

            $text = $response['text'];
            // Extract JSON using regex
            if (preg_match('/\{.*\}/s', $text, $matches)) {
                $json = $matches[0];
            } else {
                $json = $text;
            }

            return json_decode($json, true) ?? [];
        };

        try {
            return Cache::remember($cacheKey, 86400, $callback);
        } catch (\Exception $e) {
            Log::warning("Cache failed in mapInterestsToTags: " . $e->getMessage());
            return $callback();
        }
    }

    protected function executeQuery(string $query): array
    {
        $cacheKey = 'overpass_query_' . md5($query);

        $callback = function () use ($query) {
            try {
                $response = Http::asForm()->post($this->baseUrl, [
                    'data' => $query
                ]);

                if ($response->failed()) {
                    Log::error("Overpass API failed: " . $response->body());
                    return [];
                }

                return $response->json();
            } catch (\Exception $e) {
                Log::error("Overpass API exception: " . $e->getMessage());
                return [];
            }
        };

        try {
            return Cache::remember($cacheKey, 3600, $callback);
        } catch (\Exception $e) {
            Log::warning("Cache failed in executeQuery: " . $e->getMessage());
            return $callback();
        }
    }

    protected function countElements(array $elements, array $mapping): array
    {
        $counts = [];
        foreach ($mapping as $category => $tags) {
            $counts[$category] = 0;
        }

        foreach ($elements as $element) {
            $elementTags = $element['tags'] ?? [];

            foreach ($mapping as $category => $tags) {
                foreach ($tags as $tag) {
                    $key = $tag['key'];
                    $value = $tag['value'];

                    if (isset($elementTags[$key]) && $elementTags[$key] === $value) {
                        $counts[$category]++;
                        break;
                    }
                }
            }
        }

        return $counts;
    }
}
