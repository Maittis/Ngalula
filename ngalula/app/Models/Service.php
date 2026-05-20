<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'detailed_description',
        'short_description',
        'images',
        'video_url',
        'thumbnail',
        'duration_minutes',
        'base_price',
        'dynamic_pricing',
        'is_active',
        'is_featured',
        'is_trending',
        'popularity_score',
        'booking_count',
        'sort_order',
        'requirements',
        'benefits',
        'what_to_expect',
        'aftercare_instructions',
        'meta_title',
        'meta_description',
        'tags',
    ];

    protected $casts = [
        'images' => 'array',
        'short_description' => 'array',
        'dynamic_pricing' => 'array',
        'requirements' => 'array',
        'benefits' => 'array',
        'what_to_expect' => 'array',
        'aftercare_instructions' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'popularity_score' => 'integer',
        'booking_count' => 'integer',
        'sort_order' => 'integer',
        'base_price' => 'decimal:2',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function subServices(): HasMany
    {
        return $this->hasMany(SubService::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function addOns(): HasMany
    {
        return $this->hasMany(ServiceAddOn::class)->where('is_active', true)->orderBy('sort_order');
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(ServicePromotion::class)
            ->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function activePromotion(): HasMany
    {
        return $this->promotions()->where('is_active', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_trending', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('base_price', [$min, $max]);
    }

    public function scopeByDuration($query, $min, $max = null)
    {
        if ($max) {
            return $query->whereBetween('duration_minutes', [$min, $max]);
        }
        return $query->where('duration_minutes', '>=', $min);
    }

    // Accessors
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    public function getImagesUrlAttribute()
    {
        if (!$this->images) {
            return [];
        }
        
        return array_map(function ($image) {
            return asset('storage/' . $image);
        }, $this->images);
    }

    public function getCurrentPriceAttribute()
    {
        $promotion = $this->activePromotion()->first();
        
        if (!$promotion) {
            return $this->base_price;
        }

        if ($promotion->discount_type === 'percentage') {
            return $this->base_price * (1 - $promotion->discount_value / 100);
        } else {
            return max(0, $this->base_price - $promotion->discount_value);
        }
    }

    public function getDiscountPercentageAttribute()
    {
        $promotion = $this->activePromotion()->first();
        
        if (!$promotion) {
            return 0;
        }

        if ($promotion->discount_type === 'percentage') {
            return $promotion->discount_value;
        } else {
            return round(($promotion->discount_value / $this->base_price) * 100, 2);
        }
    }

    public function getDisplayPriceAttribute()
    {
        $price = $this->current_price;
        $originalPrice = $this->base_price;
        
        return [
            'current' => $price,
            'original' => $originalPrice,
            'discount_percentage' => $this->discount_percentage,
            'currency' => 'USD', // Make this configurable
            'formatted_current' => '$' . number_format($price, 2),
            'formatted_original' => '$' . number_format($originalPrice, 2),
        ];
    }

    // Mutators
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // Helper methods
    public function getDynamicPriceForDateTime($dateTime): float
    {
        if (!$this->dynamic_pricing) {
            return $this->base_price;
        }

        $hour = $dateTime->hour;
        $dayOfWeek = $dateTime->dayOfWeek; // 0 = Sunday, 6 = Saturday
        
        $priceMultiplier = 1.0;

        // Peak hours pricing
        if (isset($this->dynamic_pricing['peak_hours'])) {
            foreach ($this->dynamic_pricing['peak_hours'] as $peakHour) {
                if ($hour >= $peakHour['start'] && $hour < $peakHour['end']) {
                    $priceMultiplier += $peakHour['surcharge'] / 100;
                    break;
                }
            }
        }

        // Weekend pricing
        if (isset($this->dynamic_pricing['weekend_surcharge']) && ($dayOfWeek === 0 || $dayOfWeek === 6)) {
            $priceMultiplier += $this->dynamic_pricing['weekend_surcharge'] / 100;
        }

        // Seasonal pricing
        if (isset($this->dynamic_pricing['seasonal'])) {
            $month = $dateTime->month;
            foreach ($this->dynamic_pricing['seasonal'] as $seasonal) {
                if ($month >= $seasonal['start_month'] && $month <= $seasonal['end_month']) {
                    $priceMultiplier += $seasonal['surcharge'] / 100;
                    break;
                }
            }
        }

        return $this->base_price * $priceMultiplier;
    }

    public function incrementBookingCount(): void
    {
        $this->increment('booking_count');
        $this->updatePopularityScore();
    }

    public function updatePopularityScore(): void
    {
        // Calculate popularity based on recent bookings, ratings, and other factors
        $recentBookings = $this->booking_count; // This could be refined to only recent bookings
        
        // Simple scoring algorithm - can be made more sophisticated
        $score = $recentBookings * 10;
        
        if ($this->is_featured) {
            $score += 50;
        }

        // Update trending status based on score
        $this->popularity_score = $score;
        $this->is_trending = $score > 100; // Threshold for trending
        
        $this->save();
    }

    public function addImage($imagePath): void
    {
        $images = $this->images ?? [];
        $images[] = $imagePath;
        $this->update(['images' => $images]);
    }

    public function removeImage($imagePath): void
    {
        $images = $this->images ?? [];
        $key = array_search($imagePath, $images);
        
        if ($key !== false) {
            unset($images[$key]);
            $this->update(['images' => array_values($images)]);
        }
    }

    public function hasActivePromotion(): bool
    {
        return $this->activePromotion()->exists();
    }

    public function getRequiredAddOns(): HasMany
    {
        return $this->addOns()->where('is_required', true);
    }

    public function getOptionalAddOns(): HasMany
    {
        return $this->addOns()->where('is_required', false);
    }

    public function calculateTotalPrice($subServiceId = null, $addOnIds = []): float
    {
        $totalPrice = $this->current_price;

        // Add sub-service price adjustment
        if ($subServiceId) {
            $subService = $this->subServices()->find($subServiceId);
            if ($subService) {
                if ($subService->price_type === 'percentage') {
                    $totalPrice += $totalPrice * ($subService->price_adjustment / 100);
                } else {
                    $totalPrice += $subService->price_adjustment;
                }
            }
        }

        // Add add-on prices
        if (!empty($addOnIds)) {
            $addOns = $this->addOns()->whereIn('id', $addOnIds)->get();
            foreach ($addOns as $addOn) {
                $totalPrice += $addOn->price;
            }
        }

        return $totalPrice;
    }

    public function calculateTotalDuration($subServiceId = null, $addOnIds = []): int
    {
        $totalDuration = $this->duration_minutes;

        // Add sub-service duration
        if ($subServiceId) {
            $subService = $this->subServices()->find($subServiceId);
            if ($subService) {
                $totalDuration += $subService->duration_minutes;
            }
        }

        // Add add-on durations
        if (!empty($addOnIds)) {
            $addOns = $this->addOns()->whereIn('id', $addOnIds)->get();
            foreach ($addOns as $addOn) {
                $totalDuration += $addOn->duration_addition_minutes;
            }
        }

        return $totalDuration;
    }
}
