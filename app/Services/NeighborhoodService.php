<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NeighborhoodService
{
    protected $overpassUrl = 'https://overpass-api.de/api/interpreter';

    // Predefined list of neighborhoods to analyze
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

    /**
     * Fetches data for all neighborhoods and scores them based on user KPIs.
     */
    public function findBestMatch(array $userKpis): array
    {
        $scores = [];

        foreach ($this->neighborhoods as $name => $coords) {
            $data = $this->getNeighborhoodData($name, $coords['lat'], $coords['lon']);
            $score = $this->calculateScore($data, $userKpis);

            $scores[] = [
                'name' => $name,
                'coords' => $coords,
                'data' => $data,
                'score' => $score,
            ];
        }

        // Sort by score descending
        usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

        return $scores;
    }

    /**
     * Fetches data from Overpass (cached).
     */
    protected function getNeighborhoodData(string $name, float $lat, float $lon): array
    {
        // Versioned cache key to invalidate old bad data
        return Cache::remember("neighborhood_data_v2_{$name}", 3600, function () use ($lat, $lon) {
            // Query for amenities around the center (radius 1000m)
            $query = <<<EOT
[out:json];
(
  node["amenity"="restaurant"](around:1500,{$lat},{$lon});
  node["amenity"="bar"](around:1500,{$lat},{$lon});
  node["leisure"="park"](around:1500,{$lat},{$lon});
  node["public_transport"="platform"](around:1500,{$lat},{$lon});
  node["shop"="mall"](around:1500,{$lat},{$lon});
  node["amenity"="school"](around:1500,{$lat},{$lon});
);
out count;
EOT;

            $response = Http::post($this->overpassUrl, ['data' => $query]);

            if ($response->failed()) {
                return ['restaurants' => 0, 'bars' => 0, 'parks' => 0, 'transport' => 0, 'shops' => 0, 'schools' => 0];
            }

            $json = $response->json();
            $elements = $json['elements'] ?? [];

            // Parse counts from "out count" (Overpass returns stats in a specific way or we can just count elements if we used "out;")
            // "out count" returns a summary. Let's use "out tags" and count manually to be safe and simple for now, 
            // or better, use specific queries for each type if we want accurate counts.
            // For simplicity in this hackathon context, I'll assume "out count" returns a list of counts by type if grouped, 
            // but Overpass "out count" aggregates total.
            // Let's do separate queries or one query and count locally.
            // To save bandwidth, I'll just fetch counts via separate statements in one request if possible, or just fetch all nodes (lightweight) and count.

            // Revised Query: Fetch all relevant nodes and count in PHP.
            $query = <<<EOT
[out:json];
(
  node["amenity"~"restaurant|bar|cafe|school"](around:1500,{$lat},{$lon});
  node["leisure"="park"](around:1500,{$lat},{$lon});
  node["public_transport"="platform"](around:1500,{$lat},{$lon});
  node["shop"](around:1500,{$lat},{$lon});
);
out tags;
EOT;
            $response = Http::post($this->overpassUrl, ['data' => $query]);
            $elements = $response->json()['elements'] ?? [];

            $stats = [
                'restaurants' => 0,
                'nightlife' => 0,
                'nature' => 0,
                'transport' => 0,
                'shopping' => 0,
                'schools' => 0,
            ];

            foreach ($elements as $el) {
                $tags = $el['tags'] ?? [];
                if (isset($tags['amenity'])) {
                    if (in_array($tags['amenity'], ['restaurant', 'cafe'])) $stats['restaurants']++;
                    if (in_array($tags['amenity'], ['bar', 'pub', 'nightclub'])) $stats['nightlife']++;
                    if ($tags['amenity'] === 'school') $stats['schools']++;
                }
                if (isset($tags['leisure']) && $tags['leisure'] === 'park') $stats['nature']++;
                if (isset($tags['public_transport'])) $stats['transport']++;
                if (isset($tags['shop'])) $stats['shopping']++;
            }

            return $stats;
        });
    }

    protected function calculateScore(array $data, array $kpis): int
    {
        $score = 0;

        // Normalize KPIs (0-10)
        $nightlifePref = $kpis['nightlife_activity'] ?? 5; // 0-10
        $naturePref = $kpis['nature_proximity'] ?? 5;
        $transportPref = $kpis['public_transport_need'] ?? 5;
        $shoppingPref = $kpis['shopping_access'] ?? 5;
        $familyPref = $kpis['family_friendly'] ?? 5;

        // Simple weighted sum with safety checks
        $score += (($data['nightlife'] ?? 0) * $nightlifePref);
        $score += (($data['nature'] ?? 0) * $naturePref * 2); // Weight nature more?
        $score += (($data['transport'] ?? 0) * $transportPref);
        $score += (($data['shopping'] ?? 0) * $shoppingPref);
        $score += (($data['schools'] ?? 0) * $familyPref);

        return $score;
    }
}
