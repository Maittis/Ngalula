<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'channels',
        'status',
        'sent_at',
        'failed_at',
        'failure_reason',
        'delivery_status',
        'retry_count',
        'last_retry_at',
        'scheduled_at',
        'is_scheduled',
        'priority',
        'is_read',
        'read_at',
        'notifiable_type',
        'notifiable_id',
        'metadata',
    ];

    protected $casts = [
        'data' => 'array',
        'channels' => 'array',
        'delivery_status' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'last_retry_at' => 'datetime',
        'read_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeScheduled($query)
    {
        return $query->where('is_scheduled', true)
            ->where('scheduled_at', '<=', now());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getPriorityLabelAttribute()
    {
        return ucfirst($this->priority);
    }

    public function getIsDeliveredAttribute()
    {
        return $this->status === 'sent';
    }

    public function getCanRetryAttribute()
    {
        return $this->status === 'failed' && $this->retry_count < 3;
    }

    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at->format('M d, Y H:i');
    }

    public function getFormattedReadAtAttribute()
    {
        return $this->read_at ? $this->read_at->format('M d, Y H:i') : null;
    }

    // Helper methods
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason,
        ]);
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function scheduleFor($datetime): void
    {
        $this->update([
            'is_scheduled' => true,
            'scheduled_at' => $datetime,
        ]);
    }

    public function retry(): bool
    {
        if (!$this->can_retry) {
            return false;
        }

        $this->increment('retry_count');
        $this->update([
            'last_retry_at' => now(),
            'status' => 'pending',
            'failure_reason' => null,
        ]);

        return true;
    }

    public function updateDeliveryStatus(string $channel, string $status, array $data = []): void
    {
        $deliveryStatus = $this->delivery_status ?? [];
        $deliveryStatus[$channel] = [
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'data' => $data,
        ];

        $this->delivery_status = $deliveryStatus;
        $this->save();
    }

    public function getDeliveryStatusForChannel(string $channel): ?array
    {
        $deliveryStatus = $this->delivery_status ?? [];
        return $deliveryStatus[$channel] ?? null;
    }

    public function isChannelDelivered(string $channel): bool
    {
        $status = $this->getDeliveryStatusForChannel($channel);
        return $status && $status['status'] === 'delivered';
    }

    public function isChannelFailed(string $channel): bool
    {
        $status = $this->getDeliveryStatusForChannel($channel);
        return $status && $status['status'] === 'failed';
    }

    public function getFailedChannels(): array
    {
        $failed = [];
        $deliveryStatus = $this->delivery_status ?? [];

        foreach ($deliveryStatus as $channel => $status) {
            if ($status['status'] === 'failed') {
                $failed[] = $channel;
            }
        }

        return $failed;
    }

    public function getSuccessfulChannels(): array
    {
        $successful = [];
        $deliveryStatus = $this->delivery_status ?? [];

        foreach ($deliveryStatus as $channel => $status) {
            if ($status['status'] === 'delivered') {
                $successful[] = $channel;
            }
        }

        return $successful;
    }

    public static function createNotification(array $data): self
    {
        $notification = new self();
        $notification->status = 'pending';
        $notification->priority = $data['priority'] ?? 'normal';
        $notification->is_read = false;
        
        $notification->fill($data);
        $notification->save();
        
        return $notification;
    }

    public static function getTypes(): array
    {
        return [
            'booking_reminder' => 'Booking Reminder',
            'booking_confirmed' => 'Booking Confirmed',
            'booking_cancelled' => 'Booking Cancelled',
            'payment_success' => 'Payment Successful',
            'payment_failed' => 'Payment Failed',
            'subscription_renewed' => 'Subscription Renewed',
            'subscription_expired' => 'Subscription Expired',
            'promotion' => 'Promotion',
            'system_update' => 'System Update',
            'security_alert' => 'Security Alert',
            'welcome' => 'Welcome',
        ];
    }

    public static function getPriorities(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'sent' => 'Sent',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
        ];
    }

    public function getChannels(): array
    {
        return $this->channels ?? [];
    }

    public function hasChannel(string $channel): bool
    {
        return in_array($channel, $this->getChannels());
    }

    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'status' => $this->status_label,
            'priority' => $this->priority_label,
            'is_read' => $this->is_read,
            'channels' => $this->getChannels(),
            'successful_channels' => $this->getSuccessfulChannels(),
            'failed_channels' => $this->getFailedChannels(),
            'created_at' => $this->formatted_created_at,
            'read_at' => $this->formatted_read_at,
            'can_retry' => $this->can_retry,
            'retry_count' => $this->retry_count,
        ];
    }
}
