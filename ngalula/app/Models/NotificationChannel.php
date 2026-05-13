<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationChannel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'address',
        'is_verified',
        'verified_at',
        'is_active',
        'is_primary',
        'device_type',
        'device_token',
        'app_version',
        'device_info',
        'whatsapp_phone_id',
        'whatsapp_waba_id',
        'verification_code',
        'verification_expires_at',
        'preferences',
        'metadata',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'device_info' => 'array',
        'verification_expires_at' => 'datetime',
        'preferences' => 'array',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'verification_code',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getTypeLabelAttribute()
    {
        return ucfirst($this->type);
    }

    public function getIsExpiredAttribute()
    {
        return $this->verification_expires_at && now()->isAfter($this->verification_expires_at);
    }

    public function getCanSendAttribute()
    {
        return $this->is_active && $this->is_verified && !$this->is_expired;
    }

    public function getFormattedAddressAttribute()
    {
        switch ($this->type) {
            case 'email':
                return $this->address;
            case 'sms':
            case 'whatsapp':
                return $this->formatPhoneNumber($this->address);
            case 'push':
                return $this->device_type . ' - ' . substr($this->device_token, 0, 20) . '...';
            default:
                return $this->address;
        }
    }

    // Helper methods
    public function verify(): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verification_code' => null,
            'verification_expires_at' => null,
        ]);
    }

    public function unverify(): void
    {
        $this->update([
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function makePrimary(): void
    {
        // Remove primary status from other channels of same type
        static::where('user_id', $this->user_id)
            ->where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        $this->update(['is_primary' => true]);
    }

    public function generateVerificationCode(): string
    {
        $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'verification_code' => $code,
            'verification_expires_at' => now()->addMinutes(15),
        ]);

        return $code;
    }

    public function isValidVerificationCode(string $code): bool
    {
        return $this->verification_code === $code && 
               $this->verification_expires_at && 
               now()->isBefore($this->verification_expires_at);
    }

    public function clearVerificationCode(): void
    {
        $this->update([
            'verification_code' => null,
            'verification_expires_at' => null,
        ]);
    }

    private function formatPhoneNumber(string $phone): string
    {
        // Basic phone formatting - can be enhanced based on requirements
        if (strlen($phone) === 10) {
            return substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
        }
        return $phone;
    }

    public static function createEmailChannel($user, string $email, bool $isPrimary = false): self
    {
        $channel = new self();
        $channel->user_id = $user->id;
        $channel->type = 'email';
        $channel->address = $email;
        $channel->is_verified = false;
        $channel->is_active = true;
        $channel->is_primary = $isPrimary;
        
        $channel->save();
        
        if ($isPrimary) {
            $channel->makePrimary();
        }
        
        return $channel;
    }

    public static function createSMSChannel($user, string $phone, bool $isPrimary = false): self
    {
        $channel = new self();
        $channel->user_id = $user->id;
        $channel->type = 'sms';
        $channel->address = $phone;
        $channel->is_verified = false;
        $channel->is_active = true;
        $channel->is_primary = $isPrimary;
        
        $channel->save();
        
        if ($isPrimary) {
            $channel->makePrimary();
        }
        
        return $channel;
    }

    public static function createPushChannel($user, string $deviceToken, string $deviceType, array $deviceInfo = []): self
    {
        $channel = new self();
        $channel->user_id = $user->id;
        $channel->type = 'push';
        $channel->address = $deviceToken;
        $channel->device_token = $deviceToken;
        $channel->device_type = $deviceType;
        $channel->device_info = $deviceInfo;
        $channel->is_verified = true; // Push tokens are inherently verified
        $channel->is_active = true;
        $channel->is_primary = false;
        
        $channel->save();
        
        return $channel;
    }

    public static function createWhatsAppChannel($user, string $phone, string $phoneId = null, string $wabaId = null): self
    {
        $channel = new self();
        $channel->user_id = $user->id;
        $channel->type = 'whatsapp';
        $channel->address = $phone;
        $channel->whatsapp_phone_id = $phoneId;
        $channel->whatsapp_waba_id = $wabaId;
        $channel->is_verified = false;
        $channel->is_active = true;
        $channel->is_primary = false;
        
        $channel->save();
        
        return $channel;
    }

    public static function getTypes(): array
    {
        return [
            'email' => 'Email',
            'sms' => 'SMS',
            'push' => 'Push Notification',
            'whatsapp' => 'WhatsApp',
        ];
    }

    public static function getDeviceTypes(): array
    {
        return [
            'ios' => 'iOS',
            'android' => 'Android',
            'web' => 'Web',
        ];
    }

    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type_label,
            'address' => $this->formatted_address,
            'is_verified' => $this->is_verified,
            'is_active' => $this->is_active,
            'is_primary' => $this->is_primary,
            'can_send' => $this->can_send,
            'verified_at' => $this->verified_at?->format('M d, Y'),
            'device_type' => $this->device_type,
            'app_version' => $this->app_version,
        ];
    }

    public function updatePreferences(array $preferences): void
    {
        $this->preferences = array_merge($this->preferences ?? [], $preferences);
        $this->save();
    }

    public function getPreference(string $key, $default = null)
    {
        $preferences = $this->preferences ?? [];
        return $preferences[$key] ?? $default;
    }

    public function setPreference(string $key, $value): void
    {
        $preferences = $this->preferences ?? [];
        $preferences[$key] = $value;
        $this->preferences = $preferences;
        $this->save();
    }
}
