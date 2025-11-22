<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function shareResult(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'resultData' => 'required|array'
        ]);

        Log::info('Sharing result via email', [
            'to' => $request->email,
            'data' => $request->resultData
        ]);

        return back()->with('success', 'Raven sent successfully!');
    }
}
