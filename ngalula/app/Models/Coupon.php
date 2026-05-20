<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_customer',
        'usage_count',
        'starts_at',
        'expires_at',
        'is_active',
        'applicable_services',
        'applicable_categories',
        'applies_to_all',
        'first_time_only',
        'exclude_sale_items',
        'metadata',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_limit_per_customer' => 'integer',
        'usage_count' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'applicable_services' => 'array',
        'applicable_categories' => 'array',
        'applies_to_all' => 'boolean',
        'first_time_only' => 'boolean',
        'exclude_sale_items' => 'boolean',
        'metadata' => 'array',
    ];

    // Relationships
    public function checkoutSessions(): HasMany
    {
        return $this->hasMany(CheckoutSession::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    // Accessors
    public function getFormattedDiscountValueAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        }
        return number_format($this->discount_value, 2) . ' USD';
    }

    public function getFormattedMinimumAmountAttribute()
    {
        if ($this->minimum_amount) {
            return number_format($this->minimum_amount, 2) . ' USD';
        }
        return null;
    }

    public function getFormattedMaximumDiscountAttribute()
    {
        if ($this->maximum_discount) {
            return number_format($this->maximum_discount, 2) . ' USD';
        }
        return null;
    }

    public function getRemainingUsageAttribute()
    {
        if ($this->usage_limit === null) {
            return 'Unlimited';
        }
        return max(0, $this->usage_limit - $this->usage_count);
    }

    public function getIsValidAttribute()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->starts_at && now()->isBefore($this->starts_at)) {
            return false;
        }

        if ($this->expires_at && now()->isAfter($this->expires_at)) {
            return false;
        }

        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    // Helper methods
    public function isValid(): bool
    {
        return $this->is_valid;
    }

    public function canBeUsedByUser($user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->first_time_only && $user->paymentTransactions()->count() > 0) {
            return false;
        }

        if ($this->usage_limit_per_customer) {
            $userUsage = $this->checkoutSessions()
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->count();

            if ($userUsage >= $this->usage_limit_per_customer) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount(float $subtotal, array $items = []): float
    {
        if ($this->minimum_amount && $subtotal < $this->minimum_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->discount_type === 'percentage') {
            $discount = $subtotal * ($this->discount_value / 100);
        } else {
            $discount = $this->discount_value;
        }

        if ($this->maximum_discount && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }

        return min($discount, $subtotal);
    }

    public function isApplicableToItem($item): bool
    {
        if ($this->applies_to_all) {
            return true;
        }

        // Check if item is in applicable services
        if ($this->applicable_services && isset($item['service_id'])) {
            return in_array($item['service_id'], $this->applicable_services);
        }

        // Check if item category is applicable
        if ($this->applicable_categories && isset($item['category_id'])) {
            return in_array($item['category_id'], $this->applicable_categories);
        }

        return false;
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function markAsUsed(): void
    {
        $this->incrementUsage();
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public static function findByCode(string $code): ?self
    {
        return self::where('code', strtoupper($code))->first();
    }

    public static function validateCoupon(string $code, $user, float $subtotal = 0): array
    {
        $coupon = self::findByCode($code);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code'];
        }

        if (!$coupon->isValid()) {
            return ['valid' => false, 'message' => 'Coupon is expired or inactive'];
        }

        if (!$coupon->canBeUsedByUser($user)) {
            return ['valid' => false, 'message' => 'Coupon cannot be used by this customer'];
        }

        if ($coupon->minimum_amount && $subtotal < $coupon->minimum_amount) {
            return [
                'valid' => false, 
                'message' => "Minimum order amount of {$coupon->formatted_minimum_amount} required"
            ];
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount_amount' => $coupon->calculateDiscount($subtotal)
        ];
    }

    public static function getDiscountTypes(): array
    {
        return [
            'percentage' => 'Percentage',
            'fixed_amount' => 'Fixed Amount',
        ];
    }

    public function getUsageStats(): array
    {
        return [
            'total_usage' => $this->usage_count,
            'remaining_usage' => $this->remaining_usage,
            'usage_percentage' => $this->usage_limit ? 
                round(($this->usage_count / $this->usage_limit) * 100, 2) : 0,
        ];
    }
}
