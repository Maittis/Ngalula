<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_number',
        'pin',
        'code',
        'initial_value',
        'current_balance',
        'currency',
        'status',
        'expires_at',
        'issued_at',
        'last_used_at',
        'purchased_by',
        'redeemed_by',
        'recipient_name',
        'recipient_email',
        'message',
        'theme',
        'usage_count',
        'usage_history',
        'metadata',
    ];

    protected $casts = [
        'initial_value' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'expires_at' => 'datetime',
        'issued_at' => 'datetime',
        'last_used_at' => 'datetime',
        'usage_count' => 'integer',
        'usage_history' => 'array',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'pin',
    ];

    // Relationships
    public function purchaser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'purchased_by');
    }

    public function redeemer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }

    public function checkoutSessions(): HasMany
    {
        return $this->hasMany(CheckoutSession::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'active')
            ->where('current_balance', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function scopeByCardNumber($query, $cardNumber)
    {
        return $query->where('card_number', $cardNumber);
    }

    // Accessors
    public function getFormattedInitialValueAttribute()
    {
        return number_format($this->initial_value, 2) . ' ' . $this->currency;
    }

    public function getFormattedCurrentBalanceAttribute()
    {
        return number_format($this->current_balance, 2) . ' ' . $this->currency;
    }

    public function getFormattedUsedAmountAttribute()
    {
        $used = $this->initial_value - $this->current_balance;
        return number_format($used, 2) . ' ' . $this->currency;
    }

    public function getRemainingPercentageAttribute()
    {
        if ($this->initial_value == 0) {
            return 0;
        }
        return round(($this->current_balance / $this->initial_value) * 100, 2);
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    public function getIsEmptyAttribute()
    {
        return $this->current_balance <= 0;
    }

    public function getIsValidAttribute()
    {
        return $this->status === 'active' && 
               !$this->is_expired && 
               !$this->is_empty;
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    // Helper methods
    public function isValid(): bool
    {
        return $this->is_valid;
    }

    public function canBeUsed(): bool
    {
        return $this->isValid();
    }

    public function redeem(float $amount, $user = null, string $description = null): bool
    {
        if (!$this->canBeUsed() || $amount > $this->current_balance) {
            return false;
        }

        $this->current_balance -= $amount;
        $this->usage_count += 1;
        $this->last_used_at = now();

        if ($user && !$this->redeemed_by) {
            $this->redeemed_by = $user->id;
        }

        // Add to usage history
        $usageHistory = $this->usage_history ?? [];
        $usageHistory[] = [
            'amount' => $amount,
            'balance_before' => $this->current_balance + $amount,
            'balance_after' => $this->current_balance,
            'user_id' => $user ? $user->id : null,
            'description' => $description,
            'timestamp' => now()->toISOString(),
        ];
        $this->usage_history = $usageHistory;

        // Update status if empty
        if ($this->current_balance <= 0) {
            $this->status = 'used';
        }

        $this->save();

        return true;
    }

    public function addFunds(float $amount): bool
    {
        if ($amount <= 0) {
            return false;
        }

        $this->current_balance += $amount;
        $this->initial_value += $amount;
        
        if ($this->status === 'used') {
            $this->status = 'active';
        }

        $this->save();

        return true;
    }

    public function deactivate(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    public static function generateCardNumber(): string
    {
        do {
            $cardNumber = 'GC' . str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (self::where('card_number', $cardNumber)->exists());

        return $cardNumber;
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(Str::random(12));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public static function generatePin(): string
    {
        return str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    public static function createGiftCard(array $data): self
    {
        $giftCard = new self();
        $giftCard->card_number = self::generateCardNumber();
        $giftCard->code = self::generateCode();
        $giftCard->pin = self::generatePin();
        $giftCard->status = 'active';
        $giftCard->issued_at = now();
        $giftCard->usage_count = 0;
        $giftCard->currency = $data['currency'] ?? 'USD';
        
        $giftCard->fill($data);
        $giftCard->save();
        
        return $giftCard;
    }

    public static function findByCode(string $code): ?self
    {
        return self::where('code', strtoupper($code))->first();
    }

    public static function findByCardNumber(string $cardNumber): ?self
    {
        return self::where('card_number', $cardNumber)->first();
    }

    public static function validateGiftCard(string $code, float $amount = 0): array
    {
        $giftCard = self::findByCode($code);

        if (!$giftCard) {
            return ['valid' => false, 'message' => 'Invalid gift card code'];
        }

        if (!$giftCard->isValid()) {
            if ($giftCard->is_expired) {
                return ['valid' => false, 'message' => 'Gift card has expired'];
            } elseif ($giftCard->is_empty) {
                return ['valid' => false, 'message' => 'Gift card has no remaining balance'];
            } else {
                return ['valid' => false, 'message' => 'Gift card is not active'];
            }
        }

        if ($amount > 0 && $amount > $giftCard->current_balance) {
            return [
                'valid' => false, 
                'message' => 'Insufficient balance. Available: ' . $giftCard->formatted_current_balance
            ];
        }

        return [
            'valid' => true,
            'gift_card' => $giftCard,
            'balance' => $giftCard->current_balance,
            'max_usable_amount' => min($amount ?: $giftCard->current_balance, $giftCard->current_balance)
        ];
    }

    public function getUsageSummary(): array
    {
        return [
            'initial_value' => $this->formatted_initial_value,
            'current_balance' => $this->formatted_current_balance,
            'used_amount' => $this->formatted_used_amount,
            'remaining_percentage' => $this->remaining_percentage . '%',
            'usage_count' => $this->usage_count,
            'last_used_at' => $this->last_used_at ? $this->last_used_at->format('M d, Y') : 'Never',
            'status' => $this->status_label,
            'expires_at' => $this->expires_at ? $this->expires_at->format('M d, Y') : 'Never',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'active' => 'Active',
            'used' => 'Used',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled',
        ];
    }

    public function getThemes(): array
    {
        return [
            'birthday' => 'Birthday',
            'holiday' => 'Holiday',
            'thank_you' => 'Thank You',
            'congratulations' => 'Congratulations',
            'wedding' => 'Wedding',
            'anniversary' => 'Anniversary',
            'general' => 'General',
        ];
    }
}
