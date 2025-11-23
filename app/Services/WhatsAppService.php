<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $enabled;
    protected $phoneNumberId;
    protected $accessToken;
    protected $baseUrl;
    protected $apiVersion;
    protected $templateName;
    protected $templateLanguage;

    public function __construct()
    {
        $this->enabled = config('whatsapp.enabled');
        $this->phoneNumberId = config('whatsapp.phone_number_id');
        $this->accessToken = config('whatsapp.access_token');
        $this->baseUrl = config('whatsapp.base_url');
        $this->apiVersion = config('whatsapp.api_version');
        $this->templateName = config('whatsapp.template.name');
        $this->templateLanguage = config('whatsapp.template.language');
    }

    /**
     * Send WhatsApp template message with result data
     *
     * @param string $phoneNumber Phone number in international format (without +)
     * @param array $resultData Result data to send
     * @return array Response with success status and message
     */
    public function sendResultTemplate(string $phoneNumber, array $resultData): array
    {
        if (!$this->enabled) {
            return [
                'success' => false,
                'message' => 'WhatsApp integration is not enabled.',
                'fallback_url' => $this->generateWhatsAppWebUrl($phoneNumber, $resultData)
            ];
        }

        if (!$this->phoneNumberId || !$this->accessToken) {
            Log::error('WhatsApp credentials not configured');
            return [
                'success' => false,
                'message' => 'WhatsApp credentials not configured.',
                'fallback_url' => $this->generateWhatsAppWebUrl($phoneNumber, $resultData)
            ];
        }

        try {
            $url = "{$this->baseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $phoneNumber,
                'type' => 'template',
                'template' => [
                    'name' => $this->templateName,
                    'language' => [
                        'code' => $this->templateLanguage
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => $resultData['archetype'] ?? 'Unknown'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => $resultData['neighborhood'] ?? 'Unknown'
                                ],
                                [
                                    'type' => 'text',
                                    'text' => (string)($resultData['score'] ?? '0')
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withToken($this->accessToken)
                ->post($url, $payload);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'to' => $phoneNumber,
                    'message_id' => $response->json('messages.0.id')
                ]);

                return [
                    'success' => true,
                    'message' => 'The raven has been sent via WhatsApp!',
                    'message_id' => $response->json('messages.0.id')
                ];
            } else {
                Log::error('WhatsApp API error', [
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send WhatsApp message. Please try again.',
                    'fallback_url' => $this->generateWhatsAppWebUrl($phoneNumber, $resultData)
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp service exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred while sending WhatsApp message.',
                'fallback_url' => $this->generateWhatsAppWebUrl($phoneNumber, $resultData)
            ];
        }
    }

    /**
     * Generate WhatsApp Web URL as fallback
     *
     * @param string $phoneNumber
     * @param array $resultData
     * @return string
     */
    protected function generateWhatsAppWebUrl(string $phoneNumber, array $resultData): string
    {
        $text = sprintf(
            "I am a %s and my ideal realm is %s (%s%% match)! Find your domain at: %s",
            $resultData['archetype'] ?? 'seeker',
            $resultData['neighborhood'] ?? 'unknown',
            $resultData['score'] ?? '0',
            url('/')
        );

        return "https://wa.me/{$phoneNumber}?text=" . urlencode($text);
    }
}
