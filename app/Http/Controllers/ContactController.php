<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use App\Services\EmailService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    protected $whatsappService;
    protected $emailService;
    protected $pdfService;

    public function __construct(
        WhatsAppService $whatsappService,
        EmailService $emailService,
        PdfService $pdfService
    ) {
        $this->whatsappService = $whatsappService;
        $this->emailService = $emailService;
        $this->pdfService = $pdfService;
    }

    /**
     * Send result via WhatsApp
     */
    public function sendWhatsApp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|min:10|max:15',
            'resultData' => 'required|array',
            'resultData.archetype' => 'required|string',
            'resultData.neighborhood' => 'required|string',
            'resultData.score' => 'required|numeric',
        ]);

        $phoneNumber = preg_replace('/[^0-9]/', '', $request->phone);

        $result = $this->whatsappService->sendResultTemplate($phoneNumber, $request->resultData);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'fallback_url' => $result['fallback_url'] ?? null
            ], 200); // Return 200 to allow frontend to handle fallback
        }
    }

    /**
     * Send result via email
     */
    public function shareResult(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'resultData' => 'required|array',
            'resultData.archetype' => 'required|string',
            'resultData.neighborhood' => 'required|string',
            'resultData.score' => 'required|numeric',
        ]);

        $result = $this->emailService->sendResultReport($request->email, $request->resultData);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        }
    }

    /**
     * Download result as PDF
     */
    public function downloadPdf(Request $request)
    {
        $request->validate([
            'resultData' => 'required|array',
            'resultData.archetype' => 'required|string',
            'resultData.neighborhood' => 'required|string',
            'resultData.score' => 'required|numeric',
        ]);

        try {
            return $this->pdfService->generateResultPdf($request->resultData);
        } catch (\Exception $e) {
            Log::error('PDF download failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF. Please try again.'
            ], 500);
        }
    }
}
