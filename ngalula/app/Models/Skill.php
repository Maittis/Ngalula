<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'is_active',
        'requires_certification',
        'icon',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_certification' => 'boolean',
    ];

    // Relationships
    public function therapists(): BelongsToMany
    {
        return $this->belongsToMany(Therapist::class, 'therapist_skills')
            ->withPivot('proficiency_level', 'years_experience', 'certified')
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

    public function scopeRequiresCertification($query)
    {
        return $query->where('requires_certification', true);
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

    public function getCertifiedTherapistCount(): int
    {
        return $this->therapists()->wherePivot('certified', true)->count();
    }

    public static function getCategories(): array
    {
        return [
            'therapy_techniques' => 'Therapy Techniques',
            'assessment_tools' => 'Assessment Tools',
            'treatment_methods' => 'Treatment Methods',
            'specialized_training' => 'Specialized Training',
            'language_skills' => 'Language Skills',
            'administrative' => 'Administrative',
            'technology' => 'Technology',
            'other' => 'Other',
        ];
    }

    public static function getProficiencyLevels(): array
    {
        return [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            'expert' => 'Expert',
        ];
    }
}
