<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GeminiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GameController::class, 'index'])->name('home');

Route::post('/analyze', [GameController::class, 'analyze'])->name('analyze');
Route::get('/analyze/result', [GameController::class, 'result'])->name('analyze.result');

Route::post('/send-whatsapp', [ContactController::class, 'sendWhatsApp'])->name('send.whatsapp');
Route::post('/share-result', [ContactController::class, 'shareResult'])->name('share.result');
Route::post('/download-pdf', [ContactController::class, 'downloadPdf'])->name('download.pdf');

Route::get('/gemini/test', [GeminiController::class, 'test']);
