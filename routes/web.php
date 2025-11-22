<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\GeminiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index'])->name('home');
Route::post('/analyze', [GameController::class, 'analyze'])->name('analyze');

Route::get('/gemini/test', [GeminiController::class, 'test']);
