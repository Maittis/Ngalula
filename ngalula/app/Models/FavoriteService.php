<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteService extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_profile_id',
        'service_id',
        'booking_count',
        'last_booked_at',
        'notes',
    ];

    protected $casts = [
        'last_booked_at' => 'datetime',
        'booking_count' => 'integer',
    ];

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
