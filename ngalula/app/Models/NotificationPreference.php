<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_type',
        'email_enabled',
        'sms_enabled',
        'push_enabled',
        'whatsapp_enabled',
        'is_enabled',
        'frequency',
        'quiet_hours',
        'channel_preferences',
        'metadata',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'push_enabled' => 'boolean',
        'whatsapp_enabled' => 'boolean',
        'is_enabled' => 'boolean',
        'quiet_hours' => 'array',
        'channel_preferences' => 'array',
        'metadata' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('notification_type', $type);
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getIsInQuietHoursAttribute()
    {
        if (!$this->quiet_hours) {
            return false;
        }

        $now = now();
        $currentTime = $now->format('H:i');
        $currentDay = strtolower($now->format('l'));

        // Check if current day has quiet hours
        if (isset($this->quiet_hours[$currentDay])) {
            $quietPeriod = $this->quiet_hours[$currentDay];
            $start = $quietPeriod['start'] ?? null;
            $end = $quietPeriod['end'] ?? null;

            if ($start && $end) {
                return $currentTime >= $start && $currentTime <= $end;
            }
        }

        return false;
    }

    public function getEnabledChannelsAttribute()
    {
        $channels = [];
        
        if ($this->email_enabled) $channels[] = 'email';
        if ($this->sms_enabled) $channels[] = 'sms';
        if ($this->push_enabled) $channels[] = 'push';
        if ($this->whatsapp_enabled) $channels[] = 'whatsapp';
        
        return $channels;
    }

    public function getFrequencyLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->frequency));
    }

    // Helper methods
    public function isChannelEnabled(string $channel): bool
    {
        if (!$this->is_enabled) {
            return false;
        }

        $channelField = $channel . '_enabled';
        return $this->$channelField ?? false;
    }

    public function enableChannel(string $channel): void
    {
        $channelField = $channel . '_enabled';
        $this->update([$channelField => true]);
    }

    public function disableChannel(string $channel): void
    {
        $channelField = $channel . '_enabled';
        $this->update([$channelField => false]);
    }

    public function enableAllChannels(): void
    {
        $this->update([
            'email_enabled' => true,
            'sms_enabled' => true,
            'push_enabled' => true,
            'whatsapp_enabled' => true,
        ]);
    }

    public function disableAllChannels(): void
    {
        $this->update([
            'email_enabled' => false,
            'sms_enabled' => false,
            'push_enabled' => false,
            'whatsapp_enabled' => false,
        ]);
    }

    public function enable(): void
    {
        $this->update(['is_enabled' => true]);
    }

    public function disable(): void
    {
        $this->update(['is_enabled' => false]);
    }

    public function setQuietHours(array $quietHours): void
    {
        $this->update(['quiet_hours' => $quietHours]);
    }

    public function setQuietHoursForDay(string $day, string $start, string $end): void
    {
        $quietHours = $this->quiet_hours ?? [];
        $quietHours[strtolower($day)] = [
            'start' => $start,
            'end' => $end,
        ];
        
        $this->update(['quiet_hours' => $quietHours]);
    }

    public function removeQuietHoursForDay(string $day): void
    {
        $quietHours = $this->quiet_hours ?? [];
        unset($quietHours[strtolower($day)]);
        
        $this->update(['quiet_hours' => $quietHours]);
    }

    public function setFrequency(string $frequency): void
    {
        $this->update(['frequency' => $frequency]);
    }

    public function setChannelPreference(string $channel, array $preferences): void
    {
        $channelPreferences = $this->channel_preferences ?? [];
        $channelPreferences[$channel] = $preferences;
        
        $this->update(['channel_preferences' => $channelPreferences]);
    }

    public function getChannelPreference(string $channel, string $key, $default = null)
    {
        $channelPreferences = $this->channel_preferences ?? [];
        return $channelPreferences[$channel][$key] ?? $default;
    }

    public function canSendNotification(string $channel): bool
    {
        // Check if notification type is enabled
        if (!$this->is_enabled) {
            return false;
        }

        // Check if channel is enabled for this type
        if (!$this->isChannelEnabled($channel)) {
            return false;
        }

        // Check quiet hours (except for urgent notifications)
        $isUrgent = $this->getChannelPreference($channel, 'bypass_quiet_hours', false);
        if (!$isUrgent && $this->is_in_quiet_hours) {
            return false;
        }

        return true;
    }

    public static function getFrequencies(): array
    {
        return [
            'immediate' => 'Immediate',
            'hourly' => 'Hourly',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
        ];
    }

    public static function getNotificationTypes(): array
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
            'marketing' => 'Marketing',
        ];
    }

    public static function getChannels(): array
    {
        return [
            'email' => 'Email',
            'sms' => 'SMS',
            'push' => 'Push Notification',
            'whatsapp' => 'WhatsApp',
        ];
    }

    public static function createDefaultPreferences($user): void
    {
        $types = self::getNotificationTypes();
        
        foreach ($types as $type => $label) {
            self::create([
                'user_id' => $user->id,
                'notification_type' => $type,
                'email_enabled' => true,
                'sms_enabled' => in_array($type, ['booking_reminder', 'security_alert']),
                'push_enabled' => true,
                'whatsapp_enabled' => false,
                'is_enabled' => true,
                'frequency' => 'immediate',
            ]);
        }
    }

    public static function getOrCreatePreference($user, string $type): self
    {
        return self::firstOrCreate(
            ['user_id' => $user->id, 'notification_type' => $type],
            [
                'email_enabled' => true,
                'sms_enabled' => in_array($type, ['booking_reminder', 'security_alert']),
                'push_enabled' => true,
                'whatsapp_enabled' => false,
                'is_enabled' => true,
                'frequency' => 'immediate',
            ]
        );
    }

    public function getSummary(): array
    {
        return [
            'notification_type' => $this->notification_type,
            'is_enabled' => $this->is_enabled,
            'enabled_channels' => $this->enabled_channels,
            'frequency' => $this->frequency_label,
            'is_in_quiet_hours' => $this->is_in_quiet_hours,
            'quiet_hours' => $this->quiet_hours,
            'channel_preferences' => $this->channel_preferences,
        ];
    }

    public function cloneForNotificationType(string $newType): self
    {
        $newPreference = $this->replicate();
        $newPreference->notification_type = $newType;
        $newPreference->save();
        
        return $newPreference;
    }

    public function resetToDefaults(): void
    {
        $this->update([
            'email_enabled' => true,
            'sms_enabled' => in_array($this->notification_type, ['booking_reminder', 'security_alert']),
            'push_enabled' => true,
            'whatsapp_enabled' => false,
            'is_enabled' => true,
            'frequency' => 'immediate',
            'quiet_hours' => null,
            'channel_preferences' => null,
        ]);
    }
}
