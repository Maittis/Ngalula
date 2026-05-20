<?php

namespace App\Services\Notifications;

use App\Models\Notification;
use App\Models\NotificationChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsNotificationService
{
    private $apiKey;
    private $senderId;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.sms.api_key');
        $this->senderId = config('services.sms.sender_id', 'NGALULA');
        $this->baseUrl = config('services.sms.base_url', 'https://api.sms-provider.com');
    }

    /**
     * Send SMS notification
     */
    public function send(Notification $notification, NotificationChannel $channel): array
    {
        try {
            // Prepare SMS data
            $smsData = [
                'to' => $this->formatPhoneNumber($channel->address),
                'from' => $this->senderId,
                'message' => $this->formatMessage($notification->message),
                'message_id' => $notification->id,
            ];

            // Send SMS via provider
            $response = $this->sendSmsViaProvider($smsData);

            if ($response['success']) {
                $notification->updateDeliveryStatus('sms', 'delivered', [
                    'provider' => 'sms_provider',
                    'message_id' => $response['message_id'],
                    'sent_at' => now()->toISOString(),
                ]);

                return [
                    'success' => true,
                    'message_id' => $response['message_id'],
                    'provider_response' => $response,
                ];
            } else {
                $notification->updateDeliveryStatus('sms', 'failed', [
                    'error' => $response['error'],
                    'failed_at' => now()->toISOString(),
                ]);

                return [
                    'success' => false,
                    'error' => $response['error'],
                ];
            }

        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'notification_id' => $notification->id,
                'channel_id' => $channel->id,
                'error' => $e->getMessage(),
            ]);

            $notification->updateDeliveryStatus('sms', 'failed', [
                'error' => $e->getMessage(),
                'failed_at' => now()->toISOString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send verification code via SMS
     */
    public function sendVerificationCode(NotificationChannel $channel, string $code): array
    {
        try {
            $message = "Your verification code is: {$code}. This code will expire in 15 minutes.";
            
            $smsData = [
                'to' => $this->formatPhoneNumber($channel->address),
                'from' => $this->senderId,
                'message' => $message,
                'type' => 'verification',
            ];

            $response = $this->sendSmsViaProvider($smsData);

            return [
                'success' => $response['success'],
                'message_id' => $response['message_id'] ?? null,
                'error' => $response['error'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('SMS verification code sending failed', [
                'channel_id' => $channel->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS via provider
     */
    private function sendSmsViaProvider(array $data): array
    {
        // Simulate SMS provider integration
        // In production, replace with actual SMS provider API calls
        
        if (app()->environment('testing')) {
            return [
                'success' => true,
                'message_id' => 'SMS_' . uniqid(),
                'status' => 'delivered',
            ];
        }

        // Example integration with a generic SMS provider
        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])
            ->post($this->baseUrl . '/sms/send', [
                'recipient' => $data['to'],
                'sender' => $data['from'],
                'message' => $data['message'],
                'reference_id' => $data['message_id'] ?? null,
                'type' => $data['type'] ?? 'transactional',
            ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message_id' => $response->json('message_id'),
                'status' => $response->json('status'),
                'provider_response' => $response->json(),
            ];
        } else {
            return [
                'success' => false,
                'error' => $response->json('message') ?? 'SMS sending failed',
                'provider_response' => $response->json(),
            ];
        }
    }

    /**
     * Format phone number for SMS
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add country code if missing (assuming DRC)
        if (strlen($phone) === 9 && !str_starts_with($phone, '243')) {
            $phone = '243' . $phone;
        } elseif (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            $phone = '243' . substr($phone, 1);
        }

        return '+' . $phone;
    }

    /**
     * Format message for SMS
     */
    private function formatMessage(string $message): string
    {
        // SMS character limit is 160 characters for GSM-7 encoding
        // For longer messages, we split them into multiple parts
        $maxLength = 160;
        
        if (strlen($message) <= $maxLength) {
            return $message;
        }

        // For long messages, truncate and add indicator
        return substr($message, 0, $maxLength - 3) . '...';
    }

    /**
     * Get SMS delivery status
     */
    public function getDeliveryStatus(string $messageId): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->get($this->baseUrl . '/sms/status/' . $messageId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->json('status'),
                    'delivered_at' => $response->json('delivered_at'),
                    'provider_response' => $response->json(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json('message') ?? 'Failed to get status',
                ];
            }

        } catch (\Exception $e) {
            Log::error('SMS status check failed', [
                'message_id' => $messageId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Validate phone number
     */
    public function validatePhoneNumber(string $phone): array
    {
        $formatted = $this->formatPhoneNumber($phone);
        
        // Basic validation - phone should be 10-15 digits after formatting
        $digitsOnly = preg_replace('/[^0-9]/', '', $formatted);
        
        if (strlen($digitsOnly) < 10 || strlen($digitsOnly) > 15) {
            return [
                'valid' => false,
                'error' => 'Invalid phone number format',
                'formatted' => null,
            ];
        }

        return [
            'valid' => true,
            'formatted' => $formatted,
        ];
    }

    /**
     * Get SMS cost estimate
     */
    public function getCostEstimate(string $message, string $country = 'CD'): array
    {
        // SMS pricing varies by country and message length
        $baseCost = 0.05; // Base cost per SMS
        $messageLength = strlen($message);
        
        // Calculate number of SMS parts
        $parts = ceil($messageLength / 160);
        
        // Country-specific pricing
        $countryMultipliers = [
            'CD' => 1.0,  // Democratic Republic of Congo
            'US' => 1.2,  // United States
            'GB' => 1.1,  // United Kingdom
            'FR' => 1.1,  // France
        ];
        
        $multiplier = $countryMultipliers[$country] ?? 1.0;
        $totalCost = $baseCost * $parts * $multiplier;
        
        return [
            'cost' => $totalCost,
            'parts' => $parts,
            'characters' => $messageLength,
            'currency' => 'USD',
        ];
    }

    /**
     * Check SMS balance
     */
    public function getBalance(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])
                ->get($this->baseUrl . '/account/balance');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'balance' => $response->json('balance'),
                    'currency' => $response->json('currency', 'USD'),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json('message') ?? 'Failed to get balance',
                ];
            }

        } catch (\Exception $e) {
            Log::error('SMS balance check failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
