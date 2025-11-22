<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Contact');
    }

    public function shareResult(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'resultData' => 'required|array'
        ]);

        // In a real application, we would send an email here.
        // For now, we'll just log the request to simulate success.
        \Illuminate\Support\Facades\Log::info('Sharing result via email', [
            'to' => $request->email,
            'data' => $request->resultData
        ]);

        return back()->with('success', 'Raven sent successfully!');
    }
}
