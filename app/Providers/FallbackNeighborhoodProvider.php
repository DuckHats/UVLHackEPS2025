<?php

namespace App\Providers;

class FallbackNeighborhoodProvider
{
    /**
     * Returns default Barcelona neighborhoods when API fails.
     */
    public static function getDefaultNeighborhoods(): array
    {
        return [
            'Eixample' => ['lat' => 41.3888, 'lon' => 2.1590],
            'Gràcia' => ['lat' => 41.4036, 'lon' => 2.1561],
            'Ciutat Vella' => ['lat' => 41.3825, 'lon' => 2.1769],
            'Sants-Montjuïc' => ['lat' => 41.3748, 'lon' => 2.1478],
            'Les Corts' => ['lat' => 41.3874, 'lon' => 2.1282],
            'Sarrià-Sant Gervasi' => ['lat' => 41.4036, 'lon' => 2.1364],
            'Horta-Guinardó' => ['lat' => 41.4204, 'lon' => 2.1640],
            'Nou Barris' => ['lat' => 41.4420, 'lon' => 2.1769],
            'Sant Andreu' => ['lat' => 41.4350, 'lon' => 2.1890],
            'Sant Martí' => ['lat' => 41.4100, 'lon' => 2.2000],
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
