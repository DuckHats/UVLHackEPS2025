<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\GeminiService;

class GameController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function index()
    {
        return Inertia::render('Home');
    }

    public function analyze(Request $request, \App\Services\NeighborhoodService $neighborhoodService)
    {
        $request->validate([
            'prompt' => 'required|string|min:10',
        ]);

        // 1. Analyze Profile
        $profile = $this->gemini->analyzeProfile($request->prompt);

        if (isset($profile['error'])) {
            return back()->withErrors(['prompt' => 'The Maesters could not read your scroll. Try again.']);
        }

        // 2. Find Best Neighborhood
        $matches = $neighborhoodService->findBestMatch($profile['kpis']);
        $bestMatch = $matches[0];

        // 3. Justify
        $justification = $this->gemini->justifyRecommendation($profile, $bestMatch);

        return Inertia::render('Result', [
            'profile' => $profile,
            'bestMatch' => $bestMatch,
            'allMatches' => array_slice($matches, 0, 5), // Top 5 for heatmap/list
            'justification' => $justification,
        ]);
    }
}
