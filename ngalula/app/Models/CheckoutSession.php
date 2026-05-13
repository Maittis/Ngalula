<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class CheckoutSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'status',
        'orderable_type',
        'orderable_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'deposit_amount',
        'remaining_amount',
        'payment_type',
        'coupon_id',
        'gift_card_id',
        'promo_code',
        'payment_methods',
        'payment_breakdown',
        'client_secret',
        'checkout_url',
        'metadata',
        'expires_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'payment_methods' => 'array',
        'payment_breakdown' => 'array',
        'metadata' => 'array',
        'expires_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected $hidden = [
        'client_secret',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderable(): MorphTo
    {
        return $this->morphTo();
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function giftCard(): BelongsTo
    {
        return $this->belongsTo(GiftCard::class);
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')->orWhere('expires_at', '<', now());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getFormattedTotalAmountAttribute()
    {
        return number_format($this->total_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 2) . ' ' . $this->currency;
    }

    public function getFormattedDepositAmountAttribute()
    {
        if ($this->deposit_amount) {
            return number_format($this->deposit_amount, 2) . ' ' . $this->currency;
        }
        return null;
    }

    public function getFormattedRemainingAmountAttribute()
    {
        if ($this->remaining_amount) {
            return number_format($this->remaining_amount, 2) . ' ' . $this->currency;
        }
        return null;
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    public function getCanCompleteAttribute()
    {
        return $this->status === 'pending' && !$this->is_expired;
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    // Helper methods
    public static function createSession(array $data): self
    {
        $session = new self();
        $session->session_id = 'CS_' . strtoupper(uniqid()) . time();
        $session->client_secret = 'cs_' . Str::random(64);
        $session->status = 'pending';
        $session->currency = $data['currency'] ?? 'USD';
        $session->expires_at = now()->addHours(24); // 24 hour expiry
        $session->checkout_url = url("/checkout/{$session->session_id}");
        
        $session->fill($data);
        $session->save();
        
        return $session;
    }

    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    public function markAsExpired(): void
    {
        $this->update([
            'status' => 'expired',
        ]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], [
                'cancellation_reason' => $reason,
                'cancelled_at' => now()->toISOString(),
            ]),
        ]);
    }

    public function applyCoupon(Coupon $coupon): bool
    {
        if (!$coupon->isValid()) {
            return false;
        }

        $this->coupon_id = $coupon->id;
        $this->discount_amount = $coupon->calculateDiscount($this->subtotal);
        $this->total_amount = $this->subtotal - $this->discount_amount + $this->tax_amount;
        
        $this->save();
        
        return true;
    }

    public function applyGiftCard(GiftCard $giftCard, float $amount): bool
    {
        if (!$giftCard->isValid() || $giftCard->current_balance < $amount) {
            return false;
        }

        $this->gift_card_id = $giftCard->id;
        $this->discount_amount += $amount;
        $this->total_amount = max(0, $this->total_amount - $amount);
        
        $this->save();
        
        return true;
    }

    public function setDepositPayment(float $depositAmount): void
    {
        $this->payment_type = 'deposit';
        $this->deposit_amount = $depositAmount;
        $this->remaining_amount = $this->total_amount - $depositAmount;
        
        $this->save();
    }

    public function addPaymentMethod(int $paymentMethodId, float $amount): void
    {
        $paymentMethods = $this->payment_methods ?? [];
        $paymentBreakdown = $this->payment_breakdown ?? [];
        
        $paymentMethods[] = $paymentMethodId;
        $paymentBreakdown[] = [
            'payment_method_id' => $paymentMethodId,
            'amount' => $amount,
            'timestamp' => now()->toISOString(),
        ];
        
        $this->payment_methods = $paymentMethods;
        $this->payment_breakdown = $paymentBreakdown;
        
        $this->save();
    }

    public function calculateTotal(): void
    {
        $this->total_amount = $this->subtotal - $this->discount_amount + $this->tax_amount;
        
        if ($this->payment_type === 'deposit' && $this->deposit_amount) {
            $this->remaining_amount = $this->total_amount - $this->deposit_amount;
        }
        
        $this->save();
    }

    public static function generateSessionId(): string
    {
        return 'CS_' . strtoupper(uniqid()) . time();
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
        ];
    }

    public function getPaymentSummary(): array
    {
        return [
            'subtotal' => $this->formatted_subtotal,
            'tax_amount' => number_format($this->tax_amount, 2) . ' ' . $this->currency,
            'discount_amount' => number_format($this->discount_amount, 2) . ' ' . $this->currency,
            'total_amount' => $this->formatted_total_amount,
            'deposit_amount' => $this->formatted_deposit_amount,
            'remaining_amount' => $this->formatted_remaining_amount,
            'payment_type' => $this->payment_type,
            'currency' => $this->currency,
        ];
    }
}
