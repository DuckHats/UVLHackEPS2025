<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GeminiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index'])->name('home');
Route::post('/analyze', [GameController::class, 'analyze'])->name('analyze');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/share-result', [ContactController::class, 'shareResult'])->name('share.result');

Route::get('/gemini/test', [GeminiController::class, 'test']);
