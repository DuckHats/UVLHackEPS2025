<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;

class GeminiController extends Controller
{
    public GeminiService $gemini;
    public $promptAnalysisContext;
    public $kpisAnalysisContext;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function test()
    {
        $res = $this->gemini->ask(
            prompt: "Hello world",
            context: "Context: Això és un prompt d'exemple per provar que Gemini funciona correctament"
        );

        echo ($res['text']);
    }

    public function processPrompt(Request $request)
    {
        $res = $this->gemini->ask(
            prompt: $request->prompt,
            context: $this->promptAnalysisContext
        );

        return response()->json($res);
    }
}
