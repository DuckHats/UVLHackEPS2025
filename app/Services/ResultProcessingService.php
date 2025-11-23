<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Helpers\Utils;
use Illuminate\Support\Facades\Log;

class ResultProcessingService
{
    protected GeminiService $gemini;
    protected NeighborhoodService $neighborhoodService;

    public function __construct(GeminiService $gemini, NeighborhoodService $neighborhoodService)
    {
        $this->gemini = $gemini;
        $this->neighborhoodService = $neighborhoodService;
    }

    /**
     * Process user prompt and generate complete result data.
     *
     * @param string $userPrompt User's description of preferences
     * @param bool $useGemini Whether to use Gemini API or fallback data
     * @return array Result data with profile, matches, and justification
     * @throws \Exception If profile analysis fails
     */
    public function processUserPrompt(string $userPrompt, bool $useGemini = true): array
    {
        // Analyze user profile
        $profile = $useGemini 
            ? $this->gemini->analyzeProfile($userPrompt)
            : $this->getDefaultProfile();

        if (isset($profile['error'])) {
            Log::error('Profile analysis failed', ['error' => $profile]);
            throw new \Exception('The Maesters could not read your scroll. Try again.');
        }

        Log::info('Profile analyzed', ['profile' => $profile]);

        // Find matching neighborhoods
        $matches = $useGemini
            ? $this->neighborhoodService->findBestMatch($profile['kpis'])
            : $this->getDefaultMatches();

        Log::info('Matches found', ['count' => count($matches)]);

        $bestMatch = $matches[0];

        // Generate justification
        $justification = $useGemini
            ? $this->gemini->justifyRecommendation($profile, $bestMatch)
            : $this->getDefaultJustification();

        Log::info('Justification generated');

        // Translate KPI data
        $kpiTranslations = config('kpisTranslations');
        $bestMatch['data'] = Utils::translateKpiData($bestMatch['data'], $kpiTranslations);

        return [
            'profile' => $profile,
            'bestMatch' => $bestMatch,
            'allMatches' => array_slice($matches, 0, AppConstants::MAX_RESULTS_TO_DISPLAY),
            'justification' => $justification,
        ];
    }

    /**
     * Get default profile for fallback.
     */
    protected function getDefaultProfile(): array
    {
        return config('defaultResponses.profile');
    }

    /**
     * Get default matches for fallback.
     */
    protected function getDefaultMatches(): array
    {
        return config('defaultResponses.matches');
    }

    /**
     * Get default justification for fallback.
     */
    protected function getDefaultJustification(): string
    {
        return config('defaultResponses.justification');
    }
}
