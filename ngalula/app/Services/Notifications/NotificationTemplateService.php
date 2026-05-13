<?php

namespace App\Services\Notifications;

use App\Models\Notification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class NotificationTemplateService
{
    private $templates = [
        'booking_reminder' => [
            'email' => [
                'subject' => 'Booking Reminder - {{service_name}}',
                'template' => 'emails.booking.reminder',
            ],
            'sms' => [
                'message' => 'Reminder: Your booking for {{service_name}} is scheduled for {{booking_date}} at {{booking_time}}. Please arrive 10 minutes early.',
            ],
            'push' => [
                'title' => 'Booking Reminder',
                'body' => 'Your booking for {{service_name}} is tomorrow at {{booking_time}}',
            ],
            'whatsapp' => [
                'template' => 'booking_reminder',
            ],
        ],
        'booking_confirmed' => [
            'email' => [
                'subject' => 'Booking Confirmed - {{service_name}}',
                'template' => 'emails.booking.confirmed',
            ],
            'sms' => [
                'message' => 'Your booking for {{service_name}} on {{booking_date}} at {{booking_time}} has been confirmed. Booking ID: {{booking_id}}',
            ],
            'push' => [
                'title' => 'Booking Confirmed',
                'body' => 'Your booking has been confirmed for {{booking_date}}',
            ],
            'whatsapp' => [
                'template' => 'booking_confirmed',
            ],
        ],
        'booking_cancelled' => [
            'email' => [
                'subject' => 'Booking Cancelled - {{service_name}}',
                'template' => 'emails.booking.cancelled',
            ],
            'sms' => [
                'message' => 'Your booking for {{service_name}} on {{booking_date}} has been cancelled. Reason: {{cancellation_reason}}',
            ],
            'push' => [
                'title' => 'Booking Cancelled',
                'body' => 'Your booking has been cancelled',
            ],
            'whatsapp' => [
                'template' => 'booking_cancelled',
            ],
        ],
        'payment_success' => [
            'email' => [
                'subject' => 'Payment Successful - {{payment_amount}}',
                'template' => 'emails.payment.success',
            ],
            'sms' => [
                'message' => 'Payment of {{payment_amount}} {{currency}} was successful. Transaction ID: {{transaction_id}}',
            ],
            'push' => [
                'title' => 'Payment Successful',
                'body' => 'Your payment of {{payment_amount}} was successful',
            ],
            'whatsapp' => [
                'template' => 'payment_success',
            ],
        ],
        'payment_failed' => [
            'email' => [
                'subject' => 'Payment Failed - {{payment_amount}}',
                'template' => 'emails.payment.failed',
            ],
            'sms' => [
                'message' => 'Payment of {{payment_amount}} {{currency}} failed. Please update your payment method and try again.',
            ],
            'push' => [
                'title' => 'Payment Failed',
                'body' => 'Your payment failed. Please update your payment method.',
            ],
            'whatsapp' => [
                'template' => 'payment_failed',
            ],
        ],
        'subscription_renewed' => [
            'email' => [
                'subject' => 'Subscription Renewed - {{subscription_name}}',
                'template' => 'emails.subscription.renewed',
            ],
            'sms' => [
                'message' => 'Your subscription {{subscription_name}} has been renewed successfully. Next billing date: {{next_billing_date}}',
            ],
            'push' => [
                'title' => 'Subscription Renewed',
                'body' => 'Your subscription has been renewed',
            ],
            'whatsapp' => [
                'template' => 'subscription_renewed',
            ],
        ],
        'subscription_expired' => [
            'email' => [
                'subject' => 'Subscription Expired - {{subscription_name}}',
                'template' => 'emails.subscription.expired',
            ],
            'sms' => [
                'message' => 'Your subscription {{subscription_name}} has expired. Renew now to continue enjoying our services.',
            ],
            'push' => [
                'title' => 'Subscription Expired',
                'body' => 'Your subscription has expired. Renew now!',
            ],
            'whatsapp' => [
                'template' => 'subscription_expired',
            ],
        ],
        'welcome' => [
            'email' => [
                'subject' => 'Welcome to {{app_name}}!',
                'template' => 'emails.welcome',
            ],
            'sms' => [
                'message' => 'Welcome to {{app_name}}! Your account has been created successfully. Get started with our amazing services.',
            ],
            'push' => [
                'title' => 'Welcome!',
                'body' => 'Welcome to {{app_name}}! Get started now',
            ],
            'whatsapp' => [
                'template' => 'welcome_message',
            ],
        ],
        'security_alert' => [
            'email' => [
                'subject' => 'Security Alert - {{alert_type}}',
                'template' => 'emails.security.alert',
            ],
            'sms' => [
                'message' => 'Security Alert: {{alert_message}}. If this wasn\'t you, please secure your account immediately.',
            ],
            'push' => [
                'title' => 'Security Alert',
                'body' => '{{alert_message}}',
            ],
            'whatsapp' => [
                'template' => 'security_alert',
            ],
        ],
        'promotion' => [
            'email' => [
                'subject' => 'Special Offer - {{promotion_title}}',
                'template' => 'emails.promotion',
            ],
            'sms' => [
                'message' => 'Special Offer: {{promotion_message}}. Use code {{promo_code}}. Valid until {{expiry_date}}',
            ],
            'push' => [
                'title' => 'Special Offer!',
                'body' => '{{promotion_title}} - Limited time offer!',
            ],
            'whatsapp' => [
                'template' => 'promotion',
            ],
        ],
        'system_update' => [
            'email' => [
                'subject' => 'System Update - {{update_title}}',
                'template' => 'emails.system.update',
            ],
            'sms' => [
                'message' => 'System Update: {{update_message}}. Thank you for your patience.',
            ],
            'push' => [
                'title' => 'System Update',
                'body' => '{{update_title}}',
            ],
            'whatsapp' => [
                'template' => 'system_update',
            ],
        ],
    ];

    /**
     * Get template for notification type and channel
     */
    public function getTemplate(string $type, string $channel): ?array
    {
        return $this->templates[$type][$channel] ?? null;
    }

    /**
     * Render template with data
     */
    public function renderTemplate(string $type, string $channel, array $data = []): array
    {
        $template = $this->getTemplate($type, $channel);
        
        if (!$template) {
            return [
                'success' => false,
                'error' => "Template not found for type: {$type}, channel: {$channel}",
            ];
        }

        try {
            $rendered = [];

            foreach ($template as $key => $value) {
                if ($key === 'template') {
                    // Render Blade template
                    $rendered[$key] = View::make($value, $data)->render();
                } else {
                    // Replace placeholders in string
                    $rendered[$key] = $this->replacePlaceholders($value, $data);
                }
            }

            return [
                'success' => true,
                'template' => $rendered,
            ];

        } catch (\Exception $e) {
            Log::error('Template rendering failed', [
                'type' => $type,
                'channel' => $channel,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Replace placeholders in template string
     */
    private function replacePlaceholders(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $template = str_replace($placeholder, $value, $template);
        }

        // Replace app-specific placeholders
        $template = str_replace('{{app_name}}', config('app.name'), $template);
        $template = str_replace('{{app_url}}', config('app.url'), $template);
        $template = str_replace('{{support_email}}', config('mail.support_address', 'support@ngalula.com'), $template);

        return $template;
    }

    /**
     * Create notification from template
     */
    public function createFromTemplate(string $type, $user, array $data = [], array $channels = ['email']): array
    {
        $templateData = array_merge([
            'user' => $user,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'current_date' => now()->format('Y-m-d'),
            'current_time' => now()->format('H:i'),
        ], $data);

        $notificationData = [
            'user_id' => $user->id,
            'type' => $type,
            'channels' => $channels,
            'data' => $templateData,
        ];

        // Use template for title and message
        foreach ($channels as $channel) {
            $rendered = $this->renderTemplate($type, $channel, $templateData);
            
            if ($rendered['success']) {
                $template = $rendered['template'];
                
                if (!isset($notificationData['title']) && isset($template['subject'])) {
                    $notificationData['title'] = $template['subject'];
                } elseif (!isset($notificationData['title']) && isset($template['title'])) {
                    $notificationData['title'] = $template['title'];
                }
                
                if (!isset($notificationData['message']) && isset($template['message'])) {
                    $notificationData['message'] = $template['message'];
                } elseif (!isset($notificationData['message']) && isset($template['body'])) {
                    $notificationData['message'] = $template['body'];
                }
                
                break; // Use first successful template for title/message
            }
        }

        // Fallback if no template found
        if (!isset($notificationData['title'])) {
            $notificationData['title'] = $this->getDefaultTitle($type);
        }
        
        if (!isset($notificationData['message'])) {
            $notificationData['message'] = $this->getDefaultMessage($type);
        }

        return $notificationData;
    }

    /**
     * Get default title for notification type
     */
    private function getDefaultTitle(string $type): string
    {
        $titles = [
            'booking_reminder' => 'Booking Reminder',
            'booking_confirmed' => 'Booking Confirmed',
            'booking_cancelled' => 'Booking Cancelled',
            'payment_success' => 'Payment Successful',
            'payment_failed' => 'Payment Failed',
            'subscription_renewed' => 'Subscription Renewed',
            'subscription_expired' => 'Subscription Expired',
            'welcome' => 'Welcome!',
            'security_alert' => 'Security Alert',
            'promotion' => 'Special Offer',
            'system_update' => 'System Update',
        ];

        return $titles[$type] ?? 'Notification';
    }

    /**
     * Get default message for notification type
     */
    private function getDefaultMessage(string $type): string
    {
        $messages = [
            'booking_reminder' => 'You have an upcoming booking. Please check your schedule.',
            'booking_confirmed' => 'Your booking has been confirmed successfully.',
            'booking_cancelled' => 'Your booking has been cancelled.',
            'payment_success' => 'Your payment was processed successfully.',
            'payment_failed' => 'Your payment could not be processed. Please try again.',
            'subscription_renewed' => 'Your subscription has been renewed.',
            'subscription_expired' => 'Your subscription has expired.',
            'welcome' => 'Welcome to our platform! We\'re excited to have you.',
            'security_alert' => 'A security event was detected on your account.',
            'promotion' => 'Check out our latest special offers!',
            'system_update' => 'System maintenance or update information.',
        ];

        return $messages[$type] ?? 'You have a new notification.';
    }

    /**
     * Get all available templates
     */
    public function getAvailableTemplates(): array
    {
        return $this->templates;
    }

    /**
     * Add custom template
     */
    public function addTemplate(string $type, string $channel, array $template): void
    {
        if (!isset($this->templates[$type])) {
            $this->templates[$type] = [];
        }
        
        $this->templates[$type][$channel] = $template;
    }

    /**
     * Validate template data
     */
    public function validateTemplate(array $template): array
    {
        $required = ['subject', 'template'];
        $missing = [];

        foreach ($required as $field) {
            if (!isset($template[$field]) || empty($template[$field])) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            return [
                'valid' => false,
                'missing' => $missing,
            ];
        }

        return ['valid' => true];
    }

    /**
     * Create email template file
     */
    public function createEmailTemplate(string $name, string $content): array
    {
        try {
            $templatePath = resource_path("views/emails/{$name}.blade.php");
            
            // Create directory if it doesn't exist
            $directory = dirname($templatePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            file_put_contents($templatePath, $content);

            return [
                'success' => true,
                'path' => $templatePath,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
