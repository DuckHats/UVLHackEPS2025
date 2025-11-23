<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PdfService
{
    /**
     * Generate PDF report from result data
     *
     * @param array $resultData Result data to include in PDF
     * @return \Illuminate\Http\Response PDF download response
     */
    public function generateResultPdf(array $resultData)
    {
        try {
            $pdf = Pdf::loadView('pdf.pdf-report', ['data' => $resultData]);

            $filename = sprintf(
                'realm-decree-%s-%s.pdf',
                str_replace(' ', '-', strtolower($resultData['neighborhood'] ?? 'unknown')),
                now()->format('Y-m-d')
            );

            Log::info('PDF generated successfully', [
                'neighborhood' => $resultData['neighborhood'] ?? 'Unknown',
                'filename' => $filename
            ]);

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF generation exception', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Generate PDF and return as stream (for preview)
     *
     * @param array $resultData Result data to include in PDF
     * @return \Illuminate\Http\Response PDF stream response
     */
    public function streamResultPdf(array $resultData)
    {
        try {
            $pdf = Pdf::loadView('pdf.pdf-report', ['data' => $resultData]);

            return $pdf->stream();
        } catch (\Exception $e) {
            Log::error('PDF stream exception', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
