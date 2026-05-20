<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_picture',
        'date_of_birth',
        'gender',
        'bio',
        'wellness_preferences',
        'allergies',
        'medical_notes',
        'membership_status',
        'loyalty_points',
        'lifetime_spend',
        'notification_preferences',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'allow_marketing_emails',
        'allow_sms_promotions',
    ];

    protected $casts = [
        'wellness_preferences' => 'array',
        'allergies' => 'array',
        'notification_preferences' => 'array',
        'date_of_birth' => 'date',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'allow_marketing_emails' => 'boolean',
        'allow_sms_promotions' => 'boolean',
        'loyalty_points' => 'integer',
        'lifetime_spend' => 'decimal:2',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function preferredTherapists(): HasMany
    {
        return $this->hasMany(PreferredTherapist::class)->orderBy('preference_order');
    }

    public function favoriteServices(): HasMany
    {
        return $this->hasMany(FavoriteService::class)->orderBy('booking_count', 'desc');
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(SavedPaymentMethod::class)->where('is_active', true);
    }

    public function defaultPaymentMethod(): HasMany
    {
        return $this->hasMany(SavedPaymentMethod::class)->where('is_default', true)->where('is_active', true);
    }

    // Helper methods
    public function addLoyaltyPoints(int $points): void
    {
        $this->increment('loyalty_points', $points);
        $this->updateMembershipStatus();
    }

    public function redeemLoyaltyPoints(int $points): bool
    {
        if ($this->loyalty_points >= $points) {
            $this->decrement('loyalty_points', $points);
            return true;
        }
        return false;
    }

    public function addToLifetimeSpend(float $amount): void
    {
        $this->increment('lifetime_spend', $amount);
        $this->updateMembershipStatus();
    }

    private function updateMembershipStatus(): void
    {
        $spend = $this->lifetime_spend;
        
        if ($spend >= 5000) {
            $status = 'platinum';
        } elseif ($spend >= 2500) {
            $status = 'gold';
        } elseif ($spend >= 1000) {
            $status = 'silver';
        } elseif ($spend >= 500) {
            $status = 'bronze';
        } else {
            $status = 'none';
        }

        if ($this->membership_status !== $status) {
            $this->membership_status = $status;
            $this->save();
        }
    }

    public function addPreferredTherapist(int $therapistId, string $notes = null): PreferredTherapist
    {
        $maxOrder = $this->preferredTherapists()->max('preference_order') ?? 0;
        
        return $this->preferredTherapists()->create([
            'therapist_id' => $therapistId,
            'preference_order' => $maxOrder + 1,
            'notes' => $notes,
        ]);
    }

    public function removePreferredTherapist(int $therapistId): bool
    {
        return $this->preferredTherapists()->where('therapist_id', $therapistId)->delete() > 0;
    }

    public function addFavoriteService(int $serviceId, string $notes = null): FavoriteService
    {
        $favorite = $this->favoriteServices()->firstOrCreate(
            ['service_id' => $serviceId],
            ['notes' => $notes]
        );
        
        $favorite->increment('booking_count');
        $favorite->update(['last_booked_at' => now()]);
        
        return $favorite;
    }

    public function removeFavoriteService(int $serviceId): bool
    {
        return $this->favoriteServices()->where('service_id', $serviceId)->delete() > 0;
    }

    public function addPaymentMethod(array $paymentData): SavedPaymentMethod
    {
        // If this is set as default, unset other defaults
        if ($paymentData['is_default'] ?? false) {
            $this->paymentMethods()->update(['is_default' => false]);
        }

        return $this->paymentMethods()->create($paymentData);
    }

    public function setDefaultPaymentMethod(int $paymentMethodId): bool
    {
        // Unset all defaults
        $this->paymentMethods()->update(['is_default' => false]);
        
        // Set new default
        return $this->paymentMethods()
            ->where('id', $paymentMethodId)
            ->update(['is_default' => true]) > 0;
    }

    public function removePaymentMethod(int $paymentMethodId): bool
    {
        $paymentMethod = $this->paymentMethods()->find($paymentMethodId);
        
        if (!$paymentMethod) {
            return false;
        }

        // If removing default, set another as default if available
        if ($paymentMethod->is_default) {
            $newDefault = $this->paymentMethods()->where('id', '!=', $paymentMethodId)->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return $paymentMethod->delete();
    }
}
