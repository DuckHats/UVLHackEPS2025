<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\GeminiService;
use App\Services\NeighborhoodService;
use Illuminate\Support\Facades\Log;

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

    public function analyze(Request $request, NeighborhoodService $neighborhoodService)
    {
        $request->validate([
            'prompt' => 'required|string|min:10',
        ]);

        $profile = $this->gemini->analyzeProfile($request->prompt);

        Log::info('Profile:', ['profile' => $profile]);

        if (isset($profile['error'])) {
            return back()->withErrors(['prompt' => 'The Maesters could not read your scroll. Try again.']);
        }

        $matches = $neighborhoodService->findBestMatch($profile['kpis']);

        Log::info('Matches:', ['matches' => $matches]);

        $bestMatch = $matches[0];

        $justification = $this->gemini->justifyRecommendation($profile, $bestMatch);

        Log::info('Justification:', ['justification' => $justification]);

        return Inertia::render('Result', [
            'profile' => $profile,
            'bestMatch' => $bestMatch,
            'allMatches' => array_slice($matches, 0, 10), // Top 5 for heatmap/list
            'justification' => $justification,
        ]);
    }
}
