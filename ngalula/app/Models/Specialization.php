<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'is_active',
        'icon',
        'color',
        'sort_order',
        'minimum_experience_years',
        'required_certifications',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'required_certifications' => 'array',
    ];

    // Relationships
    public function therapists(): BelongsToMany
    {
        return $this->belongsToMany(Therapist::class, 'therapist_specializations')
            ->withPivot('primary_specialization', 'years_experience')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessors
    public function getCategoryLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->category));
    }

    // Helper methods
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function getTherapistCount(): int
    {
        return $this->therapists()->count();
    }

    public function getPrimaryTherapistCount(): int
    {
        return $this->therapists()->wherePivot('primary_specialization', true)->count();
    }

    public function getExperiencedTherapistCount(): int
    {
        return $this->therapists()
            ->wherePivot('years_experience', '>=', $this->minimum_experience_years)
            ->count();
    }

    public static function getCategories(): array
    {
        return [
            'mental_health' => 'Mental Health',
            'physical_therapy' => 'Physical Therapy',
            'occupational_therapy' => 'Occupational Therapy',
            'speech_therapy' => 'Speech Therapy',
            'behavioral_therapy' => 'Behavioral Therapy',
            'rehabilitation' => 'Rehabilitation',
            'pediatric' => 'Pediatric',
            'geriatric' => 'Geriatric',
            'sports_medicine' => 'Sports Medicine',
            'neurological' => 'Neurological',
            'cardiovascular' => 'Cardiovascular',
            'respiratory' => 'Respiratory',
            'other' => 'Other',
        ];
    }
}
