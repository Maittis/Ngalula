<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'status',
        'billing_cycle',
        'amount',
        'currency',
        'starts_at',
        'ends_at',
        'trial_ends_at',
        'current_period_starts_at',
        'current_period_ends_at',
        'canceled_at',
        'paused_at',
        'payment_method_id',
        'gateway_subscription_id',
        'auto_renew',
        'renewal_attempts',
        'last_renewal_attempt',
        'features',
        'usage_limits',
        'current_usage',
        'setup_fee',
        'trial_amount',
        'pricing_tiers',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'trial_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'current_period_starts_at' => 'datetime',
        'current_period_ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'paused_at' => 'datetime',
        'last_renewal_attempt' => 'datetime',
        'auto_renew' => 'boolean',
        'renewal_attempts' => 'integer',
        'features' => 'array',
        'usage_limits' => 'array',
        'current_usage' => 'array',
        'pricing_tiers' => 'array',
        'metadata' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('status', 'active')
            ->where('current_period_ends_at', '<=', now()->addDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
            ->orWhere(function ($q) {
                $q->where('status', 'active')
                    ->where('current_period_ends_at', '<', now());
            });
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedSetupFeeAttribute()
    {
        if ($this->setup_fee) {
            return number_format($this->setup_fee, 2) . ' ' . $this->currency;
        }
        return null;
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getBillingCycleLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->billing_cycle));
    }

    public function getIsOnTrialAttribute()
    {
        return $this->trial_ends_at && now()->isBefore($this->trial_ends_at);
    }

    public function getIsExpiredAttribute()
    {
        return $this->status === 'expired' || 
               ($this->current_period_ends_at && now()->isAfter($this->current_period_ends_at));
    }

    public function getIsCancelledAttribute()
    {
        return $this->status === 'cancelled' || $this->canceled_at !== null;
    }

    public function getDaysUntilRenewalAttribute()
    {
        if ($this->current_period_ends_at) {
            return now()->diffInDays($this->current_period_ends_at, false);
        }
        return null;
    }

    public function getDaysInTrialAttribute()
    {
        if ($this->is_on_trial && $this->trial_ends_at) {
            return now()->diffInDays($this->trial_ends_at, false);
        }
        return 0;
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->is_expired;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['active', 'past_due']) && !$this->is_cancelled;
    }

    public function canBePaused(): bool
    {
        return $this->status === 'active' && !$this->is_cancelled;
    }

    public function canBeResumed(): bool
    {
        return $this->status === 'paused';
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'canceled_at' => now(),
            'auto_renew' => false,
            'metadata' => array_merge($this->metadata ?? [], [
                'cancellation_reason' => $reason,
                'cancelled_at' => now()->toISOString(),
            ]),
        ]);
    }

    public function pause(): void
    {
        $this->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);
    }

    public function resume(): void
    {
        $this->update([
            'status' => 'active',
            'paused_at' => null,
        ]);
    }

    public function renew(): bool
    {
        if (!$this->auto_renew || $this->is_cancelled) {
            return false;
        }

        $this->increment('renewal_attempts');
        $this->update(['last_renewal_attempt' => now()]);

        // Calculate next period dates
        $nextPeriodStart = $this->current_period_ends_at;
        $nextPeriodEnd = $this->calculateNextPeriodEnd($nextPeriodStart);

        $this->update([
            'current_period_starts_at' => $nextPeriodStart,
            'current_period_ends_at' => $nextPeriodEnd,
            'renewal_attempts' => 0, // Reset on successful renewal
        ]);

        // Create invoice for renewal
        $this->createRenewalInvoice();

        return true;
    }

    private function calculateNextPeriodEnd($startDate)
    {
        switch ($this->billing_cycle) {
            case 'monthly':
                return $startDate->addMonth();
            case 'quarterly':
                return $startDate->addMonths(3);
            case 'semi_annually':
                return $startDate->addMonths(6);
            case 'annually':
                return $startDate->addYear();
            default:
                return $startDate->addMonth();
        }
    }

    private function createRenewalInvoice(): Invoice
    {
        return Invoice::create([
            'user_id' => $this->user_id,
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'type' => 'subscription',
            'status' => 'draft',
            'billing_name' => $this->user->name,
            'billing_email' => $this->user->email,
            'subtotal' => $this->amount,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $this->amount,
            'currency' => $this->currency,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'invoiceable_type' => self::class,
            'invoiceable_id' => $this->id,
            'line_items' => [
                [
                    'description' => "Subscription: {$this->name} ({$this->billing_cycle_label})",
                    'quantity' => 1,
                    'unit_price' => $this->amount,
                    'total' => $this->amount,
                ]
            ],
        ]);
    }

    public function updateUsage(string $feature, int $amount): bool
    {
        $currentUsage = $this->current_usage ?? [];
        $usageLimits = $this->usage_limits ?? [];

        // Check if feature has usage limit
        if (isset($usageLimits[$feature])) {
            $limit = $usageLimits[$feature];
            $current = $currentUsage[$feature] ?? 0;

            if ($current + $amount > $limit) {
                return false; // Exceeds limit
            }
        }

        $currentUsage[$feature] = ($currentUsage[$feature] ?? 0) + $amount;
        $this->current_usage = $currentUsage;
        $this->save();

        return true;
    }

    public function getUsagePercentage(string $feature): float
    {
        $currentUsage = $this->current_usage ?? [];
        $usageLimits = $this->usage_limits ?? [];

        if (!isset($usageLimits[$feature]) || $usageLimits[$feature] == 0) {
            return 0;
        }

        $current = $currentUsage[$feature] ?? 0;
        $limit = $usageLimits[$feature];

        return round(($current / $limit) * 100, 2);
    }

    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        return in_array($feature, $features);
    }

    public static function getBillingCycles(): array
    {
        return [
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'semi_annually' => 'Semi-Annually',
            'annually' => 'Annually',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'active' => 'Active',
            'paused' => 'Paused',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
            'past_due' => 'Past Due',
        ];
    }

    public function getSubscriptionSummary(): array
    {
        return [
            'name' => $this->name,
            'status' => $this->status_label,
            'billing_cycle' => $this->billing_cycle_label,
            'amount' => $this->formatted_amount,
            'current_period_start' => $this->current_period_starts_at->format('M d, Y'),
            'current_period_end' => $this->current_period_ends_at->format('M d, Y'),
            'days_until_renewal' => $this->days_until_renewal,
            'is_on_trial' => $this->is_on_trial,
            'days_in_trial' => $this->days_in_trial,
            'auto_renew' => $this->auto_renew,
            'features' => $this->features ?? [],
            'usage_stats' => $this->getUsageStats(),
        ];
    }

    public function getUsageStats(): array
    {
        $stats = [];
        $usageLimits = $this->usage_limits ?? [];
        $currentUsage = $this->current_usage ?? [];

        foreach ($usageLimits as $feature => $limit) {
            $current = $currentUsage[$feature] ?? 0;
            $stats[$feature] = [
                'current' => $current,
                'limit' => $limit,
                'percentage' => $this->getUsagePercentage($feature),
                'remaining' => max(0, $limit - $current),
            ];
        }

        return $stats;
    }
}
