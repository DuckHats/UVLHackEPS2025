<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;

class GeminiController extends Controller
{
    public function test(GeminiService $gemini)
    {
        $res = $gemini->ask(
            prompt: "Resumeix-me això en 3 punts claus.",
            context: "Context: informació d’ús intern bla bla..."
        );

        dd($res['text']);
    }
}
