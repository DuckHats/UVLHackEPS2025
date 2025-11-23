<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send result report via email
     *
     * @param string $email Recipient email address
     * @param array $resultData Result data to send
     * @return array Response with success status and message
     */
    public function sendResultReport(string $email, array $resultData): array
    {
        try {
            Mail::send('emails.result-report', ['data' => $resultData], function ($message) use ($email, $resultData) {
                $message->to($email)
                    ->subject('Your Realm Awaits: ' . ($resultData['neighborhood'] ?? 'Unknown'));
            });

            Log::info('Result email sent successfully', [
                'to' => $email,
                'neighborhood' => $resultData['neighborhood'] ?? 'Unknown'
            ]);

            return [
                'success' => true,
                'message' => 'The raven has been sent to your email!'
            ];
        } catch (\Exception $e) {
            Log::error('Email service exception', [
                'error' => $e->getMessage(),
                'to' => $email
            ]);

            return [
                'success' => false,
                'message' => 'The raven was lost in flight. Please try again.'
            ];
        }
    }
}
