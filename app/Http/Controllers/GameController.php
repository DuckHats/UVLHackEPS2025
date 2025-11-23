<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalyzeProfileRequest;
use App\Services\ResultProcessingService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GameController extends Controller
{
    protected ResultProcessingService $resultProcessor;

    public function __construct(ResultProcessingService $resultProcessor)
    {
        $this->resultProcessor = $resultProcessor;
    }

    /**
     * Display the home page.
     */
    public function index()
    {
        return Inertia::render('Home');
    }

    /**
     * Process the user's profile analysis and redirect to results page.
     *
     * @param AnalyzeProfileRequest $request Validated request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function analyze(AnalyzeProfileRequest $request)
    {
        $useGemini = config('services.gemini.enabled', false);

        try {
            $result = $this->resultProcessor->processUserPrompt(
                $request->validated()['prompt'],
                $useGemini
            );

            session()->flash('result', $result);

            return redirect()->route('analyze.result');
        } catch (\Exception $e) {
            Log::error('Analysis failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'prompt' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the result page.
     *
     * @return \Inertia\Response|\Illuminate\Http\RedirectResponse
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
