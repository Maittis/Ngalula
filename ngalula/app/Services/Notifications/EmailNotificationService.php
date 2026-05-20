<?php

namespace App\Services\Notifications;

use App\Models\Notification;
use App\Models\NotificationChannel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class EmailNotificationService
{
    /**
     * Send email notification
     */
    public function send(Notification $notification, NotificationChannel $channel): array
    {
        try {
            // Prepare email data
            $emailData = [
                'to' => $channel->address,
                'subject' => $notification->title,
                'notification' => $notification,
                'user' => $notification->user,
            ];

            // Send email
            $result = $this->sendEmail($emailData);

            if ($result['success']) {
                $notification->updateDeliveryStatus('email', 'delivered', [
                    'provider' => 'smtp',
                    'message_id' => $result['message_id'],
                    'sent_at' => now()->toISOString(),
                ]);

                return [
                    'success' => true,
                    'message_id' => $result['message_id'],
                    'provider_response' => $result,
                ];
            } else {
                $notification->updateDeliveryStatus('email', 'failed', [
                    'error' => $result['error'],
                    'failed_at' => now()->toISOString(),
                ]);

                return [
                    'success' => false,
                    'error' => $result['error'],
                ];
            }

        } catch (\Exception $e) {
            Log::error('Email sending failed', [
                'notification_id' => $notification->id,
                'channel_id' => $channel->id,
                'error' => $e->getMessage(),
            ]);

            $notification->updateDeliveryStatus('email', 'failed', [
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
     * Send verification email
     */
    public function sendVerificationEmail(NotificationChannel $channel, string $code): array
    {
        try {
            $data = [
                'user' => $channel->user,
                'verification_code' => $code,
                'channel' => $channel,
            ];

            $result = $this->sendEmail([
                'to' => $channel->address,
                'subject' => 'Verify Your Email Address',
                'template' => 'emails.verify',
                'data' => $data,
            ]);

            return [
                'success' => $result['success'],
                'message_id' => $result['message_id'] ?? null,
                'error' => $result['error'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('Email verification sending failed', [
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
     * Send email using Laravel Mail
     */
    private function sendEmail(array $data): array
    {
        try {
            $template = $data['template'] ?? 'emails.notification';
            $emailData = $data['data'] ?? $data;

            // Create mailable class
            $mailable = new class($template, $emailData) extends \Illuminate\Mail\Mailable {
                private $template;
                private $data;

                public function __construct($template, $data)
                {
                    $this->template = $template;
                    $this->data = $data;
                }

                public function build()
                {
                    return $this->view($this->template)
                        ->with($this->data)
                        ->subject($this->data['subject'] ?? 'Notification');
                }
            };

            // Send email
            $sent = Mail::to($data['to'])->send($mailable);

            if ($sent) {
                return [
                    'success' => true,
                    'message_id' => 'EMAIL_' . uniqid(),
                    'sent_at' => now()->toISOString(),
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Email sending failed',
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
     * Send bulk email
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
     * Validate email address
     */
    public function validateEmail(string $email): array
    {
        // Basic validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'error' => 'Invalid email format',
            ];
        }

        // Check for common disposable email domains
        $disposableDomains = [
            '10minutemail.com',
            'tempmail.org',
            'guerrillamail.com',
            'mailinator.com',
        ];

        $domain = substr(strrchr($email, '@'), 1);
        if (in_array($domain, $disposableDomains)) {
            return [
                'valid' => false,
                'error' => 'Disposable email addresses are not allowed',
            ];
        }

        return [
            'valid' => true,
            'domain' => $domain,
        ];
    }

    /**
     * Get email template content
     */
    public function getTemplateContent(string $template, array $data = []): array
    {
        try {
            $view = View::make($template, $data);
            
            return [
                'success' => true,
                'html' => $view->render(),
                'subject' => $data['subject'] ?? 'Notification',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create email template
     */
    public function createTemplate(string $name, string $subject, string $content): array
    {
        try {
            $templatePath = resource_path("views/emails/{$name}.blade.php");
            
            // Create directory if it doesn't exist
            $directory = dirname($templatePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Create template file
            $templateContent = "@component('mail::message')\n";
            $templateContent .= "# {{ \$subject ?? 'Notification' }}\n\n";
            $templateContent .= $content . "\n\n";
            $templateContent .= "@component('mail::button', ['url' => config('app.url')])\n";
            $templateContent .= "View Dashboard\n";
            $templateContent .= "@endcomponent\n\n";
            $templateContent .= "Thanks,<br>";
            $templateContent .= config('app.name') . "\n";
            $templateContent .= "@endcomponent";

            file_put_contents($templatePath, $templateContent);

            return [
                'success' => true,
                'template' => $name,
                'path' => $templatePath,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get email statistics
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
            ->whereJsonContains('channels', 'email')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $stats['total_sent'] = $notifications->count();

        foreach ($notifications as $notification) {
            if ($notification->isChannelDelivered('email')) {
                $stats['total_delivered']++;
            } else {
                $stats['total_failed']++;
            }

            $type = $notification->type;
            if (!isset($stats['by_type'][$type])) {
                $stats['by_type'][$type] = ['sent' => 0, 'delivered' => 0];
            }
            $stats['by_type'][$type]['sent']++;
            
            if ($notification->isChannelDelivered('email')) {
                $stats['by_type'][$type]['delivered']++;
            }
        }

        if ($stats['total_sent'] > 0) {
            $stats['delivery_rate'] = round(($stats['total_delivered'] / $stats['total_sent']) * 100, 2);
        }

        return $stats;
    }

    /**
     * Test email configuration
     */
    public function testConfiguration(): array
    {
        try {
            // Test mail configuration
            $config = config('mail');
            
            if (empty($config['mailers'])) {
                return [
                    'success' => false,
                    'error' => 'No mail configuration found',
                ];
            }

            // Try to send a test email to configured test address
            $testEmail = config('mail.test_address');
            if (!$testEmail) {
                return [
                    'success' => false,
                    'error' => 'No test email address configured',
                ];
            }

            $result = $this->sendEmail([
                'to' => $testEmail,
                'subject' => 'Email Configuration Test',
                'template' => 'emails.test',
                'data' => [
                    'subject' => 'Email Configuration Test',
                    'test_time' => now()->format('Y-m-d H:i:s'),
                ],
            ]);

            return [
                'success' => $result['success'],
                'message' => $result['success'] ? 'Email configuration is working' : 'Email configuration test failed',
                'error' => $result['error'] ?? null,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
