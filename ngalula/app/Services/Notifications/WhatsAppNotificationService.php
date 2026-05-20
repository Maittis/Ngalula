<?php

namespace App\Services\Notifications;

use App\Models\Notification;
use App\Models\NotificationChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    private $accessToken;
    private $phoneNumberId;
    private $version;
    private $baseUrl;

    public function __construct()
    {
        $this->accessToken = config('services.whatsapp.access_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->version = config('services.whatsapp.version', 'v18.0');
        $this->baseUrl = 'https://graph.facebook.com/' . $this->version;
    }

    /**
     * Send WhatsApp notification
     */
    public function send(Notification $notification, NotificationChannel $channel): array
    {
        try {
            $result = $this->sendWhatsAppMessage($notification, $channel);

            if ($result['success']) {
                $notification->updateDeliveryStatus('whatsapp', 'delivered', [
                    'message_id' => $result['message_id'],
                    'sent_at' => now()->toISOString(),
                ]);

                return [
                    'success' => true,
                    'message_id' => $result['message_id'],
                    'provider_response' => $result,
                ];
            } else {
                $notification->updateDeliveryStatus('whatsapp', 'failed', [
                    'error' => $result['error'],
                    'failed_at' => now()->toISOString(),
                ]);

                return [
                    'success' => false,
                    'error' => $result['error'],
                ];
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp sending failed', [
                'notification_id' => $notification->id,
                'channel_id' => $channel->id,
                'error' => $e->getMessage(),
            ]);

            $notification->updateDeliveryStatus('whatsapp', 'failed', [
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
     * Send WhatsApp message
     */
    private function sendWhatsAppMessage(Notification $notification, NotificationChannel $channel): array
    {
        $phoneNumber = $this->formatPhoneNumber($channel->address);
        
        // Determine message type based on notification content
        $messageType = $this->getMessageType($notification);
        
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phoneNumber,
            'type' => $messageType,
        ];

        switch ($messageType) {
            case 'template':
                $payload['template'] = $this->buildTemplateMessage($notification);
                break;
            case 'text':
                $payload['text'] = $this->buildTextMessage($notification);
                break;
            case 'interactive':
                $payload['interactive'] = $this->buildInteractiveMessage($notification);
                break;
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->accessToken,
                'Content-Type' => 'application/json',
            ])
            ->post($this->baseUrl . '/' . $this->phoneNumberId . '/messages', $payload);

        $responseData = $response->json();

        if ($response->successful()) {
            return [
                'success' => true,
                'message_id' => $responseData['messages'][0]['id'] ?? 'WA_' . uniqid(),
                'provider_response' => $responseData,
            ];
        } else {
            return [
                'success' => false,
                'error' => $responseData['error']['message'] ?? 'WhatsApp sending failed',
                'provider_response' => $responseData,
            ];
        }
    }

    /**
     * Build template message
     */
    private function buildTemplateMessage(Notification $notification): array
    {
        $templateName = $this->getTemplateName($notification->type);
        
        return [
            'name' => $templateName,
            'language' => [
                'code' => 'en',
            ],
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => $notification->title,
                        ],
                        [
                            'type' => 'text',
                            'text' => $this->truncateMessage($notification->message, 100),
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Build text message
     */
    private function buildTextMessage(Notification $notification): array
    {
        return [
            'preview_url' => false,
            'body' => $this->formatTextMessage($notification),
        ];
    }

    /**
     * Build interactive message
     */
    private function buildInteractiveMessage(Notification $notification): array
    {
        return [
            'type' => 'button',
            'header' => [
                'type' => 'text',
                'text' => $notification->title,
            ],
            'body' => [
                'text' => $this->truncateMessage($notification->message, 200),
            ],
            'footer' => [
                'text' => config('app.name'),
            ],
            'action' => [
                'buttons' => [
                    [
                        'type' => 'reply',
                        'reply' => [
                            'id' => 'view_' . $notification->id,
                            'title' => 'View Details',
                        ],
                    ],
                    [
                        'type' => 'reply',
                        'reply' => [
                            'id' => 'dismiss_' . $notification->id,
                            'title' => 'Dismiss',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Send verification code via WhatsApp
     */
    public function sendVerificationCode(NotificationChannel $channel, string $code): array
    {
        try {
            $phoneNumber = $this->formatPhoneNumber($channel->address);
            
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $phoneNumber,
                'type' => 'template',
                'template' => [
                    'name' => 'verification_code',
                    'language' => [
                        'code' => 'en',
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => $code,
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/' . $this->phoneNumberId . '/messages', $payload);

            $responseData = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $responseData['messages'][0]['id'] ?? 'WA_' . uniqid(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $responseData['error']['message'] ?? 'WhatsApp verification failed',
                ];
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp verification sending failed', [
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
     * Get message status
     */
    public function getMessageStatus(string $messageId): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ])
                ->get($this->baseUrl . '/' . $messageId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'status' => $response->json('conversation_status'),
                    'provider_response' => $response->json(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json('error')['message'] ?? 'Failed to get status',
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add country code if missing
        if (strlen($phone) === 9 && !str_starts_with($phone, '243')) {
            $phone = '243' . $phone;
        } elseif (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            $phone = '243' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Get message type based on notification
     */
    private function getMessageType(Notification $notification): string
    {
        // Use template messages for transactional notifications
        $transactionalTypes = [
            'booking_reminder',
            'booking_confirmed',
            'booking_cancelled',
            'payment_success',
            'payment_failed',
            'security_alert',
        ];

        if (in_array($notification->type, $transactionalTypes)) {
            return 'template';
        }

        // Use interactive messages for notifications that require action
        $interactiveTypes = [
            'subscription_expired',
            'promotion',
        ];

        if (in_array($notification->type, $interactiveTypes)) {
            return 'interactive';
        }

        // Default to text message
        return 'text';
    }

    /**
     * Get template name based on notification type
     */
    private function getTemplateName(string $type): string
    {
        $templates = [
            'booking_reminder' => 'booking_reminder',
            'booking_confirmed' => 'booking_confirmed',
            'booking_cancelled' => 'booking_cancelled',
            'payment_success' => 'payment_success',
            'payment_failed' => 'payment_failed',
            'security_alert' => 'security_alert',
            'welcome' => 'welcome_message',
        ];

        return $templates[$type] ?? 'general_notification';
    }

    /**
     * Format text message
     */
    private function formatTextMessage(Notification $notification): string
    {
        $message = "*{$notification->title}*\n\n";
        $message .= $notification->message . "\n\n";
        $message .= "—" . config('app.name');

        return $message;
    }

    /**
     * Truncate message
     */
    private function truncateMessage(string $message, int $maxLength): string
    {
        if (strlen($message) <= $maxLength) {
            return $message;
        }

        return substr($message, 0, $maxLength - 3) . '...';
    }

    /**
     * Validate phone number for WhatsApp
     */
    public function validatePhoneNumber(string $phone): array
    {
        $formatted = $this->formatPhoneNumber($phone);
        
        // WhatsApp supports phone numbers with country code
        $digitsOnly = preg_replace('/[^0-9]/', '', $formatted);
        
        if (strlen($digitsOnly) < 10 || strlen($digitsOnly) > 15) {
            return [
                'valid' => false,
                'error' => 'Invalid phone number format for WhatsApp',
                'formatted' => null,
            ];
        }

        return [
            'valid' => true,
            'formatted' => $formatted,
        ];
    }

    /**
     * Get WhatsApp statistics
     */
    public function getStatistics(\DateTime $startDate = null, \DateTime $endDate = null): array
    {
        $startDate = $startDate ?: now()->subDays(30);
        $endDate = $endDate ?: now();

        $stats = [
            'total_sent' => 0,
            'total_delivered' => 0,
            'total_failed' => 0,
            'delivery_rate' => 0,
            'by_type' => [],
        ];

        $notifications = Notification::where('status', 'sent')
            ->whereJsonContains('channels', 'whatsapp')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $stats['total_sent'] = $notifications->count();

        foreach ($notifications as $notification) {
            if ($notification->isChannelDelivered('whatsapp')) {
                $stats['total_delivered']++;
            } else {
                $stats['total_failed']++;
            }

            $type = $notification->type;
            if (!isset($stats['by_type'][$type])) {
                $stats['by_type'][$type] = ['sent' => 0, 'delivered' => 0];
            }
            $stats['by_type'][$type]['sent']++;
            
            if ($notification->isChannelDelivered('whatsapp')) {
                $stats['by_type'][$type]['delivered']++;
            }
        }

        if ($stats['total_sent'] > 0) {
            $stats['delivery_rate'] = round(($stats['total_delivered'] / $stats['total_sent']) * 100, 2);
        }

        return $stats;
    }

    /**
     * Test WhatsApp configuration
     */
    public function testConfiguration(): array
    {
        try {
            if (!$this->accessToken || !$this->phoneNumberId) {
                return [
                    'success' => false,
                    'error' => 'WhatsApp credentials not configured',
                ];
            }

            // Test API access
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ])
                ->get($this->baseUrl . '/me');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'WhatsApp API is accessible',
                    'phone_number_id' => $this->phoneNumberId,
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json('error')['message'] ?? 'WhatsApp API test failed',
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
