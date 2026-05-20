<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapistRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'client_id',
        'appointment_id',
        'rating',
        'comment',
        'anonymous',
        'verified',
        'response',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'anonymous' => 'boolean',
        'verified' => 'boolean',
        'responded_at' => 'datetime',
    ];

    // Relationships
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // Scopes
    public function scopeByRating($query, $minRating, $maxRating = null)
    {
        $query->where('rating', '>=', $minRating);
        
        if ($maxRating !== null) {
            $query->where('rating', '<=', $maxRating);
        }
        
        return $query;
    }

    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }

    public function scopeWithComment($query)
    {
        return $query->whereNotNull('comment')->where('comment', '!=', '');
    }

    public function scopeAnonymous($query)
    {
        return $query->where('anonymous', true);
    }

    public function scopePublic($query)
    {
        return $query->where('anonymous', false);
    }

    public function scopeByTherapist($query, $therapistId)
    {
        return $query->where('therapist_id', $therapistId);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getRatingStarsAttribute()
    {
        return str_repeat('★', round($this->rating)) . str_repeat('☆', 5 - round($this->rating));
    }

    public function getClientNameAttribute()
    {
        return $this->anonymous ? 'Anonymous' : $this->client?->name;
    }

    public function hasResponse(): bool
    {
        return !empty($this->response) && !is_null($this->responded_at);
    }

    // Helper methods
    public function verify(): void
    {
        $this->update(['verified' => true]);
    }

    public function respond($response, $respondedBy): void
    {
        $this->update([
            'response' => $response,
            'responded_by' => $respondedBy,
            'responded_at' => now(),
        ]);
    }

    public function makeAnonymous(): void
    {
        $this->update(['anonymous' => true]);
    }

    public function makePublic(): void
    {
        $this->update(['anonymous' => false]);
    }

    public function getRatingCategory(): string
    {
        if ($this->rating >= 4.5) return 'Excellent';
        if ($this->rating >= 4.0) return 'Very Good';
        if ($this->rating >= 3.5) return 'Good';
        if ($this->rating >= 3.0) return 'Average';
        if ($this->rating >= 2.0) return 'Below Average';
        return 'Poor';
    }

    public function canBeModified(): bool
    {
        return $this->created_at->diffInDays(now()) <= 7; // Can modify within 7 days
    }

    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'rating' => $this->rating,
            'rating_stars' => $this->rating_stars,
            'rating_category' => $this->getRatingCategory(),
            'comment' => $this->comment,
            'client_name' => $this->client_name,
            'anonymous' => $this->anonymous,
            'verified' => $this->verified,
            'has_response' => $this->has_response(),
            'response' => $this->response,
            'responded_at' => $this->responded_at?->format('Y-m-d H:i'),
            'can_be_modified' => $this->can_be_modified(),
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'therapist_id' => $this->therapist_id,
        ];
    }

    public static function getRatingDistribution($therapistId): array
    {
        $ratings = self::where('therapist_id', $therapistId)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();

        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $distribution[$i] = $ratings->where('rating', $i)->first()?->count ?? 0;
        }

        return $distribution;
    }

    public static function getAverageRating($therapistId): float
    {
        return self::where('therapist_id', $therapistId)
            ->avg('rating') ?? 0.00;
    }

    public static function getTotalRatings($therapistId): int
    {
        return self::where('therapist_id', $therapistId)->count();
    }
}
