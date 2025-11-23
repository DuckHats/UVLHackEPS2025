<?php

namespace App\Providers;

class FallbackNeighborhoodProvider
{
    /**
     * Returns default LA neighborhoods when API fails.
     */
    public static function getDefaultNeighborhoods(): array
    {
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
    
    /**
     * Generates random fallback KPI data for neighborhoods.
     */
    public static function generateFallbackData(array $neighborhoodNames, array $kpis): array
    {
        $data = [];
        foreach ($neighborhoodNames as $name) {
            foreach ($kpis as $kpi) {
                $data[$name][$kpi] = rand(
                    \App\Constants\AppConstants::FALLBACK_KPI_MIN,
                    \App\Constants\AppConstants::FALLBACK_KPI_MAX
                );
            }
        }
        return $data;
    }
}
