<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'name',
        'description',
        'duration_minutes',
        'price_adjustment',
        'price_type',
        'is_active',
        'sort_order',
        'options',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'price_adjustment' => 'decimal:2',
        'duration_minutes' => 'integer',
        'options' => 'array',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getCalculatedPriceAttribute(): float
    {
        $basePrice = $this->service->base_price;
        
        if ($this->price_type === 'percentage') {
            return $basePrice + ($basePrice * $this->price_adjustment / 100);
        } else {
            return $basePrice + $this->price_adjustment;
        }
    }
}
