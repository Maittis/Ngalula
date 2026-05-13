<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'phone',
        'otp_code',
        'otp_expires_at',
        'is_active',
        'google_id',
        'facebook_id',
        'apple_id',
        'biometric_token',
        'two_factor_enabled',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
        'two_factor_secret',
        'biometric_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];

    // Role-based methods
    public function isCustomer(): bool
    {
        return $this->user_type === 'customer';
    }

    public function isTherapist(): bool
    {
        return $this->user_type === 'therapist';
    }

    public function isReceptionist(): bool
    {
        return $this->user_type === 'receptionist';
    }

    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->user_type === 'super_admin';
    }

    public function isStaff(): bool
    {
        return in_array($this->user_type, ['therapist', 'receptionist', 'admin', 'super_admin']);
    }

    public function canAccessAdminPanel(): bool
    {
        return in_array($this->user_type, ['admin', 'super_admin']);
    }

    public function isPhoneVerified(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    public function generateOTP(): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_code = $otp;
        $this->otp_expires_at = now()->addMinutes(10);
        $this->save();
        
        return $otp;
    }

    public function verifyOTP(string $otp): bool
    {
        if ($this->otp_code === $otp && $this->otp_expires_at->isFuture()) {
            $this->phone_verified_at = now();
            $this->otp_code = null;
            $this->otp_expires_at = null;
            $this->save();
            
            return true;
        }
        
        return false;
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('user_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Customer profile relationship
    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }

    // Bookings relationship (will be implemented later)
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Payment relationships
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function defaultPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::class)->where('is_default', true)->where('is_active', true);
    }

    // Checkout relationships
    public function checkoutSessions()
    {
        return $this->hasMany(CheckoutSession::class);
    }

    // Invoice relationships
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Subscription relationships
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptions()
    {
        return $this->subscriptions()->where('status', 'active');
    }

    // Gift Card relationships
    public function purchasedGiftCards()
    {
        return $this->hasMany(GiftCard::class, 'purchased_by');
    }

    public function redeemedGiftCards()
    {
        return $this->hasMany(GiftCard::class, 'redeemed_by');
    }

    // Notification relationships
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->unread();
    }

    public function readNotifications()
    {
        return $this->notifications()->read();
    }

    public function notificationChannels()
    {
        return $this->hasMany(NotificationChannel::class);
    }

    public function activeNotificationChannels()
    {
        return $this->notificationChannels()->active();
    }

    public function verifiedNotificationChannels()
    {
        return $this->notificationChannels()->verified();
    }

    public function notificationPreferences()
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function emailChannels()
    {
        return $this->notificationChannels()->byType('email')->active()->verified();
    }

    public function smsChannels()
    {
        return $this->notificationChannels()->byType('sms')->active()->verified();
    }

    public function pushChannels()
    {
        return $this->notificationChannels()->byType('push')->active()->verified();
    }

    public function whatsappChannels()
    {
        return $this->notificationChannels()->byType('whatsapp')->active()->verified();
    }

    public function primaryEmailChannel()
    {
        return $this->notificationChannels()->byType('email')->active()->verified()->primary()->first();
    }

    public function primarySmsChannel()
    {
        return $this->notificationChannels()->byType('sms')->active()->verified()->primary()->first();
    }

    // Therapist relationships
    public function therapist()
    {
        return $this->hasOne(Therapist::class);
    }

    // Therapist rating relationships (as client)
    public function therapistRatings()
    {
        return $this->hasMany(TherapistRating::class, 'client_id');
    }

    // Therapist note relationships (as author)
    public function therapistNotes()
    {
        return $this->hasMany(TherapistNote::class, 'author_id');
    }

    // Attendance record relationships (as approver)
    public function approvedAttendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'approved_by');
    }

    // Leave request relationships (as approver/rejecter)
    public function approvedLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'approved_by');
    }

    public function rejectedLeaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'rejected_by');
    }

    // Commission record relationships (as payer)
    public function paidCommissions()
    {
        return $this->hasMany(CommissionRecord::class, 'paid_by');
    }

    // Therapist note relationships (as resolver)
    public function resolvedTherapistNotes()
    {
        return $this->hasMany(TherapistNote::class, 'resolved_by');
    }

    // Performance metric relationships (as creator)
    public function createdPerformanceMetrics()
    {
        return $this->hasMany(PerformanceMetric::class, 'created_by');
    }

    // Therapist rating response relationships
    public function respondedTherapistRatings()
    {
        return $this->hasMany(TherapistRating::class, 'responded_by');
    }

    // Helper methods for therapist-specific functionality
    public function getTherapistProfile()
    {
        if (!$this->isTherapist() || !$this->therapist) {
            return null;
        }

        return $this->therapist->load([
            'user',
            'skills',
            'specializations',
            'workingSchedules',
            'ratings' => function ($query) {
                $query->latest()->limit(5);
            }
        ]);
    }

    public function hasTherapistProfile(): bool
    {
        return $this->isTherapist() && $this->therapist !== null;
    }

    public function canManageTherapists(): bool
    {
        return in_array($this->user_type, ['admin', 'super_admin', 'receptionist']);
    }

    public function canViewTherapistDetails(): bool
    {
        return $this->canManageTherapists() || $this->isTherapist();
    }

    public function canRateTherapists(): bool
    {
        return $this->isCustomer();
    }

    public function getTherapistRating($therapistId)
    {
        return $this->therapistRatings()->where('therapist_id', $therapistId)->first();
    }

    public function hasRatedTherapist($therapistId): bool
    {
        return $this->getTherapistRating($therapistId) !== null;
    }

    // Scope methods for therapist-related queries
    public function scopeTherapists($query)
    {
        return $query->where('user_type', 'therapist');
    }

    public function scopeWithTherapistProfile($query)
    {
        return $query->with('therapist');
    }

    public function scopeActiveTherapists($query)
    {
        return $query->therapists()->active()->whereHas('therapist', function ($subQuery) {
            $subQuery->where('status', 'active');
        });
    }

    public function scopeAvailableTherapists($query)
    {
        return $query->activeTherapists()->whereHas('therapist', function ($subQuery) {
            $subQuery->where('accepts_new_clients', true)
                      ->where('license_verified', true);
        });
    }
}
