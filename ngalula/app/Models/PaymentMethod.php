<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'provider',
        'account_number',
        'phone_number',
        'card_last_four',
        'card_brand',
        'card_expiry_month',
        'card_expiry_year',
        'cardholder_name',
        'token',
        'gateway_id',
        'is_default',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'token',
        'gateway_id',
        'account_number',
        'metadata',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByProvider($query, $provider)
    {
        return $query->where('provider', $provider);
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        switch ($this->type) {
            case 'airtel_money':
                return "Airtel Money ({$this->phone_number})";
            case 'mtn_money':
                return "MTN Money ({$this->phone_number})";
            case 'visa':
                return "Visa ending in {$this->card_last_four}";
            case 'mastercard':
                return "Mastercard ending in {$this->card_last_four}";
            default:
                return ucfirst($this->type);
        }
    }

    public function getFormattedExpiryAttribute()
    {
        if ($this->card_expiry_month && $this->card_expiry_year) {
            return sprintf('%02d/%d', $this->card_expiry_month, $this->card_expiry_year);
        }
        return null;
    }

    public function getIsExpiredAttribute()
    {
        if ($this->card_expiry_month && $this->card_expiry_year) {
            $expiryDate = \Carbon\Carbon::createFromDate($this->card_expiry_year, $this->card_expiry_month, 1)->endOfMonth();
            return now()->isAfter($expiryDate);
        }
        return false;
    }

    public function getIconAttribute()
    {
        switch ($this->type) {
            case 'airtel_money':
                return 'fa-mobile-alt';
            case 'mtn_money':
                return 'fa-mobile-alt';
            case 'visa':
                return 'fa-cc-visa';
            case 'mastercard':
                return 'fa-cc-mastercard';
            default:
                return 'fa-credit-card';
        }
    }

    // Helper methods
    public function markAsDefault(): void
    {
        // Remove default status from other payment methods
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
        
        // If this was the default payment method, set another as default
        if ($this->is_default) {
            $newDefault = static::where('user_id', $this->user_id)
                ->where('id', '!=', $this->id)
                ->where('is_active', true)
                ->first();
            
            if ($newDefault) {
                $newDefault->markAsDefault();
            }
        }
    }

    public function canBeUsed(): bool
    {
        return $this->is_active && !$this->is_expired;
    }

    public static function getAvailableTypes(): array
    {
        return [
            'airtel_money' => 'Airtel Money',
            'mtn_money' => 'MTN Money',
            'visa' => 'Visa',
            'mastercard' => 'Mastercard',
        ];
    }

    public static function getValidationRules(string $type): array
    {
        switch ($type) {
            case 'airtel_money':
            case 'mtn_money':
                return [
                    'phone_number' => 'required|string|regex:/^[0-9]{10,15}$/',
                    'account_number' => 'nullable|string|max:50',
                ];
            case 'visa':
            case 'mastercard':
                return [
                    'card_number' => 'required|string|regex:/^[0-9]{13,19}$/',
                    'card_expiry_month' => 'required|string|regex:/^(0[1-9]|1[0-2])$/',
                    'card_expiry_year' => 'required|string|regex:/^[0-9]{4}$/',
                    'cardholder_name' => 'required|string|max:100',
                    'cvv' => 'required|string|regex:/^[0-9]{3,4}$/',
                ];
            default:
                return [];
        }
    }

    public function maskSensitiveData(): array
    {
        $data = $this->toArray();
        
        // Mask sensitive information
        if (isset($data['account_number']) && $data['account_number']) {
            $data['account_number'] = str_repeat('*', strlen($data['account_number']) - 4) . substr($data['account_number'], -4);
        }
        
        if (isset($data['phone_number']) && $data['phone_number']) {
            $data['phone_number'] = substr($data['phone_number'], 0, 3) . str_repeat('*', strlen($data['phone_number']) - 6) . substr($data['phone_number'], -3);
        }
        
        unset($data['token'], $data['gateway_id'], $data['metadata']);
        
        return $data;
    }
}
