<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedPaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_profile_id',
        'method_type',
        'provider',
        'last_four',
        'expiry_month',
        'expiry_year',
        'token',
        'is_default',
        'is_active',
        'cardholder_name',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function customerProfile(): BelongsTo
    {
        return $this->belongsTo(CustomerProfile::class);
    }
}
