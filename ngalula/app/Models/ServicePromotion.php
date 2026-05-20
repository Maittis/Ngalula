<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePromotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'starts_at',
        'ends_at',
        'is_active',
        'promo_code',
        'usage_limit',
        'usage_count',
        'conditions',
        'banner_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_value' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'conditions' => 'array',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getBannerImageUrlAttribute()
    {
        return $this->banner_image ? asset('storage/' . $this->banner_image) : null;
    }

    public function isActive(): bool
    {
        return $this->is_active && 
               $this->starts_at->isPast() && 
               $this->ends_at->isFuture() &&
               (!$this->usage_limit || $this->usage_count < $this->usage_limit);
    }

    public function canBeUsed(): bool
    {
        return $this->isActive() && 
               (!$this->usage_limit || $this->usage_count < $this->usage_limit);
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function calculateDiscountedPrice($originalPrice): float
    {
        if ($this->discount_type === 'percentage') {
            return $originalPrice * (1 - $this->discount_value / 100);
        } else {
            return max(0, $originalPrice - $this->discount_value);
        }
    }
}
