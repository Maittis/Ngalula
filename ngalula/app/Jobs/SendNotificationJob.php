<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\NotificationChannel;
use App\Models\NotificationPreference;
use App\Services\Notifications\EmailNotificationService;
use App\Services\Notifications\SmsNotificationService;
use App\Services\Notifications\PushNotificationService;
use App\Services\Notifications\WhatsAppNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min
    public $timeout = 120;

    protected $notificationId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $notificationId)
    {
        $this->notificationId = $notificationId;
    }

    /**
     * Execute the job.
     */
    public function handle(
        EmailNotificationService $emailService,
        SmsNotificationService $smsService,
        PushNotificationService $pushService,
        WhatsAppNotificationService $whatsappService
    ): void {
        $notification = Notification::find($this->notificationId);

        if (!$notification) {
            Log::error('Notification not found', ['notification_id' => $this->notificationId]);
            return;
        }

        if ($notification->status === 'sent') {
            Log::info('Notification already sent', ['notification_id' => $this->notificationId]);
            return;
        }

        try {
            $notification->markAsProcessing();

            // Get user's notification channels
            $channels = $notification->user->notificationChannels()
                ->active()
                ->verified()
                ->get();

            // Get user's notification preferences
            $preferences = NotificationPreference::where('user_id', $notification->user_id)
                ->where('notification_type', $notification->type)
                ->first();

            // Filter channels based on preferences
            $eligibleChannels = $this->filterChannelsByPreferences(
                $channels,
                $notification->channels,
                $preferences
            );

            if (empty($eligibleChannels)) {
                $notification->markAsFailed('No eligible channels found');
                return;
            }

            // Send notifications through each channel
            $successCount = 0;
            $failureCount = 0;
            $results = [];

            foreach ($eligibleChannels as $channel) {
                try {
                    $result = $this->sendThroughChannel(
                        $notification,
                        $channel,
                        $emailService,
                        $smsService,
                        $pushService,
                        $whatsappService
                    );

                    $results[] = $result;

                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $failureCount++;
                    }

                } catch (\Exception $e) {
                    Log::error('Channel sending failed', [
                        'notification_id' => $notification->id,
                        'channel_id' => $channel->id,
                        'error' => $e->getMessage(),
                    ]);

                    $notification->updateDeliveryStatus($channel->type, 'failed', [
                        'error' => $e->getMessage(),
                        'failed_at' => now()->toISOString(),
                    ]);

                    $failureCount++;
                }
            }

            // Update overall notification status
            if ($successCount > 0 && $failureCount === 0) {
                $notification->markAsSent();
            } elseif ($successCount > 0) {
                // Partial success
                $notification->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'failure_reason' => 'Partial delivery: ' . $failureCount . ' channels failed',
                ]);
            } else {
                $notification->markAsFailed('All channels failed');
            }

            Log::info('Notification processing completed', [
                'notification_id' => $notification->id,
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'results' => $results,
            ]);

        } catch (\Exception $e) {
            Log::error('Notification job failed', [
                'notification_id' => $this->notificationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $notification->markAsFailed($e->getMessage());
            throw $e;
        }
    }

    /**
     * Filter channels based on user preferences
     */
    private function filterChannelsByPreferences(
        $channels,
        array $requestedChannels,
        ?NotificationPreference $preferences
    ): array {
        $eligibleChannels = [];

        foreach ($channels as $channel) {
            // Check if channel is in requested channels
            if (!in_array($channel->type, $requestedChannels)) {
                continue;
            }

            // Check preferences if available
            if ($preferences) {
                if (!$preferences->canSendNotification($channel->type)) {
                    continue;
                }
            }

            $eligibleChannels[] = $channel;
        }

        return $eligibleChannels;
    }

    /**
     * Send notification through specific channel
     */
    private function sendThroughChannel(
        Notification $notification,
        NotificationChannel $channel,
        EmailNotificationService $emailService,
        SmsNotificationService $smsService,
        PushNotificationService $pushService,
        WhatsAppNotificationService $whatsappService
    ): array {
        switch ($channel->type) {
            case 'email':
                return $emailService->send($notification, $channel);
            case 'sms':
                return $smsService->send($notification, $channel);
            case 'push':
                return $pushService->send($notification, $channel);
            case 'whatsapp':
                return $whatsappService->send($notification, $channel);
            default:
                throw new \Exception("Unsupported channel type: {$channel->type}");
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendNotificationJob failed permanently', [
            'notification_id' => $this->notificationId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        $notification = Notification::find($this->notificationId);
        if ($notification) {
            $notification->markAsFailed($exception->getMessage());
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return ['notification', 'notification:' . $this->notificationId];
    }
}
