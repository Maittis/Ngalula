<?php

namespace App\Services\Notifications;

use App\Models\Notification;
use App\Models\NotificationChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private $fcmServerKey;
    private $apnsKey;
    private $apnsKeyId;
    private $apnsTeamId;

    public function __construct()
    {
        $this->fcmServerKey = config('services.fcm.server_key');
        $this->apnsKey = config('services.apns.key');
        $this->apnsKeyId = config('services.apns.key_id');
        $this->apnsTeamId = config('services.apns.team_id');
    }

    /**
     * Send push notification
     */
    public function send(Notification $notification, NotificationChannel $channel): array
    {
        try {
            $deviceType = $channel->device_type;
            $deviceToken = $channel->device_token;

            if (!$deviceType || !$deviceToken) {
                throw new \Exception('Device type or token not found');
            }

            $result = match ($deviceType) {
                'ios' => $this->sendToAPNS($notification, $channel),
                'android' => $this->sendToFCM($notification, $channel),
                'web' => $this->sendToWeb($notification, $channel),
                default => throw new \Exception("Unsupported device type: {$deviceType}"),
            };

            if ($result['success']) {
                $notification->updateDeliveryStatus('push', 'delivered', [
                    'device_type' => $deviceType,
                    'device_token' => $deviceToken,
                    'message_id' => $result['message_id'],
                    'sent_at' => now()->toISOString(),
                ]);

                return [
                    'success' => true,
                    'message_id' => $result['message_id'],
                    'device_type' => $deviceType,
                    'provider_response' => $result,
                ];
            } else {
                $notification->updateDeliveryStatus('push', 'failed', [
                    'device_type' => $deviceType,
                    'error' => $result['error'],
                    'failed_at' => now()->toISOString(),
                ]);

                return [
                    'success' => false,
                    'error' => $result['error'],
                ];
            }

        } catch (\Exception $e) {
            Log::error('Push notification sending failed', [
                'notification_id' => $notification->id,
                'channel_id' => $channel->id,
                'error' => $e->getMessage(),
            ]);

            $notification->updateDeliveryStatus('push', 'failed', [
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
     * Send to Firebase Cloud Messaging (Android)
     */
    private function sendToFCM(Notification $notification, NotificationChannel $channel): array
    {
        $payload = [
            'message' => [
                'token' => $channel->device_token,
                'notification' => [
                    'title' => $notification->title,
                    'body' => $this->truncateMessage($notification->message, 200),
                ],
                'data' => [
                    'notification_id' => (string) $notification->id,
                    'type' => $notification->type,
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
                'android' => [
                    'priority' => $this->getFCMPriority($notification->priority),
                    'notification' => [
                        'sound' => 'default',
                        'badge' => $this->getBadgeCount($notification->user_id),
                        'icon' => '@mipmap/ic_launcher',
                        'color' => '#FF6B35',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => $this->getBadgeCount($notification->user_id),
                        ],
                    ],
                ],
            ],
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'key=' . $this->fcmServerKey,
                'Content-Type' => 'application/json',
            ])
            ->post('https://fcm.googleapis.com/fcm/send', $payload);

        $responseData = $response->json();

        if ($response->successful() && ($responseData['success'] ?? 0) > 0) {
            return [
                'success' => true,
                'message_id' => $responseData['results'][0]['message_id'] ?? 'FCM_' . uniqid(),
                'provider_response' => $responseData,
            ];
        } else {
            return [
                'success' => false,
                'error' => $responseData['error'] ?? 'FCM sending failed',
                'provider_response' => $responseData,
            ];
        }
    }

    /**
     * Send to Apple Push Notification Service (iOS)
     */
    private function sendToAPNS(Notification $notification, NotificationChannel $channel): array
    {
        $payload = [
            'aps' => [
                'alert' => [
                    'title' => $notification->title,
                    'body' => $this->truncateMessage($notification->message, 200),
                ],
                'sound' => 'default',
                'badge' => $this->getBadgeCount($notification->user_id),
                'content-available' => 1,
            ],
            'notification_id' => (string) $notification->id,
            'type' => $notification->type,
        ];

        // For development, use sandbox
        $url = 'https://api.sandbox.push.apple.com/3/device/' . $channel->device_token;
        if (app()->environment('production')) {
            $url = 'https://api.push.apple.com/3/device/' . $channel->device_token;
        }

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'bearer ' . $this->generateJWT(),
                'apns-topic' => config('app.bundle_id', 'com.ngalula.app'),
                'Content-Type' => 'application/json',
            ])
            ->post($url, $payload);

        if ($response->successful()) {
            return [
                'success' => true,
                'message_id' => 'APNS_' . uniqid(),
                'provider_response' => $response->json(),
            ];
        } else {
            return [
                'success' => false,
                'error' => $response->json('reason') ?? 'APNS sending failed',
                'provider_response' => $response->json(),
            ];
        }
    }

    /**
     * Send to Web Push (Web browsers)
     */
    private function sendToWeb(Notification $notification, NotificationChannel $channel): array
    {
        $payload = [
            'title' => $notification->title,
            'body' => $this->truncateMessage($notification->message, 200),
            'icon' => url('/images/icon-192x192.png'),
            'badge' => url('/images/badge-72x72.png'),
            'tag' => 'notification_' . $notification->id,
            'data' => [
                'notification_id' => (string) $notification->id,
                'type' => $notification->type,
                'url' => config('app.url') . '/notifications/' . $notification->id,
            ],
            'actions' => [
                [
                    'action' => 'view',
                    'title' => 'View',
                ],
                [
                    'action' => 'dismiss',
                    'title' => 'Dismiss',
                ],
            ],
        ];

        // Simulate web push sending
        // In production, integrate with Web Push Protocol libraries
        return [
            'success' => true,
            'message_id' => 'WEB_' . uniqid(),
            'provider_response' => ['status' => 'sent'],
        ];
    }

    /**
     * Send bulk push notifications
     */
    public function sendBulk(array $notifications, array $channels): array
    {
        $results = [];
        $successCount = 0;
        $failureCount = 0;

        foreach ($notifications as $index => $notification) {
            if (!isset($channels[$index])) {
                continue;
            }

            $result = $this->send($notification, $channels[$index]);
            $results[] = $result;

            if ($result['success']) {
                $successCount++;
            } else {
                $failureCount++;
            }
        }

        return [
            'success' => $failureCount === 0,
            'total' => count($notifications),
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'results' => $results,
        ];
    }

    /**
     * Generate JWT for APNS
     */
    private function generateJWT(): string
    {
        $header = [
            'alg' => 'ES256',
            'kid' => $this->apnsKeyId,
        ];

        $payload = [
            'iss' => $this->apnsTeamId,
            'iat' => time(),
        ];

        // Simplified JWT generation - in production, use proper JWT library
        return 'generated_jwt_token';
    }

    /**
     * Get FCM priority
     */
    private function getFCMPriority(string $priority): string
    {
        return match ($priority) {
            'urgent', 'high' => 'high',
            default => 'normal',
        };
    }

    /**
     * Get badge count for user
     */
    private function getBadgeCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->whereJsonContains('channels', 'push')
            ->count();
    }

    /**
     * Truncate message for push notification
     */
    private function truncateMessage(string $message, int $maxLength): string
    {
        if (strlen($message) <= $maxLength) {
            return $message;
        }

        return substr($message, 0, $maxLength - 3) . '...';
    }

    /**
     * Validate device token
     */
    public function validateDeviceToken(string $token, string $deviceType): array
    {
        switch ($deviceType) {
            case 'ios':
                // iOS tokens are 64 characters hexadecimal
                if (strlen($token) !== 64 || !ctype_xdigit($token)) {
                    return ['valid' => false, 'error' => 'Invalid iOS device token'];
                }
                break;

            case 'android':
                // Android tokens vary in length but are typically 152 characters
                if (strlen($token) < 100 || strlen($token) > 200) {
                    return ['valid' => false, 'error' => 'Invalid Android device token'];
                }
                break;

            case 'web':
                // Web push endpoints are URLs
                if (!filter_var($token, FILTER_VALIDATE_URL)) {
                    return ['valid' => false, 'error' => 'Invalid web push endpoint'];
                }
                break;

            default:
                return ['valid' => false, 'error' => 'Unsupported device type'];
        }

        return ['valid' => true];
    }

    /**
     * Get push notification statistics
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
            'by_device_type' => [],
            'by_type' => [],
        ];

        $notifications = Notification::where('status', 'sent')
            ->whereJsonContains('channels', 'push')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $stats['total_sent'] = $notifications->count();

        foreach ($notifications as $notification) {
            if ($notification->isChannelDelivered('push')) {
                $stats['total_delivered']++;
            } else {
                $stats['total_failed']++;
            }

            // Group by device type
            $deliveryStatus = $notification->getDeliveryStatusForChannel('push');
            $deviceType = $deliveryStatus['data']['device_type'] ?? 'unknown';
            
            if (!isset($stats['by_device_type'][$deviceType])) {
                $stats['by_device_type'][$deviceType] = ['sent' => 0, 'delivered' => 0];
            }
            $stats['by_device_type'][$deviceType]['sent']++;
            
            if ($notification->isChannelDelivered('push')) {
                $stats['by_device_type'][$deviceType]['delivered']++;
            }

            // Group by notification type
            $type = $notification->type;
            if (!isset($stats['by_type'][$type])) {
                $stats['by_type'][$type] = ['sent' => 0, 'delivered' => 0];
            }
            $stats['by_type'][$type]['sent']++;
            
            if ($notification->isChannelDelivered('push')) {
                $stats['by_type'][$type]['delivered']++;
            }
        }

        if ($stats['total_sent'] > 0) {
            $stats['delivery_rate'] = round(($stats['total_delivered'] / $stats['total_sent']) * 100, 2);
        }

        return $stats;
    }

    /**
     * Test push notification configuration
     */
    public function testConfiguration(): array
    {
        try {
            $results = [];

            // Test FCM configuration
            if ($this->fcmServerKey) {
                $results['fcm'] = [
                    'configured' => true,
                    'server_key_set' => !empty($this->fcmServerKey),
                ];
            } else {
                $results['fcm'] = [
                    'configured' => false,
                    'error' => 'FCM server key not configured',
                ];
            }

            // Test APNS configuration
            if ($this->apnsKey && $this->apnsKeyId && $this->apnsTeamId) {
                $results['apns'] = [
                    'configured' => true,
                    'key_id_set' => !empty($this->apnsKeyId),
                    'team_id_set' => !empty($this->apnsTeamId),
                ];
            } else {
                $results['apns'] = [
                    'configured' => false,
                    'error' => 'APNS credentials not fully configured',
                ];
            }

            $allConfigured = !array_filter($results, fn($r) => !$r['configured']);

            return [
                'success' => $allConfigured,
                'results' => $results,
                'message' => $allConfigured ? 'Push notification services are configured' : 'Some push services are not configured',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
