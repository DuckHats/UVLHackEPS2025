<?php

namespace App\Http\Controllers;

use App\Helpers\Utils;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\GeminiService;
use App\Services\NeighborhoodService;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    protected $gemini;
    protected $defaultProfile;
    protected $defaultJustification;
    protected $defaultMatches;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
        $this->defaultJustification = config('defaultResponses.justification');
        $this->defaultMatches = config('defaultResponses.matches');
        $this->defaultProfile = config('defaultResponses.profile');
    }

    public function index()
    {
        return Inertia::render('Home');
    }

    /**
     * Processa el POST de l'anàlisi i redirigeix a la pàgina de resultat.
     */
    public function analyze(Request $request, NeighborhoodService $neighborhoodService)
    {
        $request->validate([
            'prompt' => 'required|string|min:10',
        ]);

        $profile = config('services.gemini.enabled')
            ? $this->gemini->analyzeProfile($request->prompt)
            : $this->defaultProfile;

        Log::info('Profile:', ['profile' => $profile]);

        if (isset($profile['error'])) {
            return back()->withErrors(['prompt' => 'The Maesters could not read your scroll. Try again.']);
        }

        $matches = config('services.gemini.enabled')
            ? $neighborhoodService->findBestMatch($profile['kpis'])
            : $this->defaultMatches;

        Log::info('Matches:', ['matches' => $matches]);

        $bestMatch = $matches[0];

        $justification = config('services.gemini.enabled')
            ? $this->gemini->justifyRecommendation($profile, $bestMatch)
            : $this->defaultJustification;

        Log::info('Justification:', ['justification' => $justification]);

        $kpiTranslations = config('kpisTranslations');
        $bestMatch['data'] = Utils::translateKpiData($bestMatch['data'], $kpiTranslations);

        foreach ($matches as &$match) {
            $match['data'] = Utils::translateKpiData($match['data'], $kpiTranslations);
        }

        session()->flash('result', [
            'profile' => $profile,
            'bestMatch' => $bestMatch,
            'allMatches' => array_slice($matches, 0, 10),
            'justification' => $justification,
        ]);

        return redirect()->route('analyze.result');
    }

    /**
     * Mostra la vista de resultat (GET).
     */
    public function result()
    {
        if (!session()->has('result')) {
            return redirect()->route('home');
        }

        $data = session('result');

        return Inertia::render('Result', [
            'profile' => $data['profile'],
            'bestMatch' => $data['bestMatch'],
            'allMatches' => $data['allMatches'],
            'justification' => $data['justification'],
        ]);
    }
}
