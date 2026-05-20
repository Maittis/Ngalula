<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Therapist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'professional_title',
        'bio',
        'profile_image',
        'hire_date',
        'termination_date',
        'employment_type',
        'status',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'years_of_experience',
        'education',
        'certifications',
        'languages',
        'hourly_rate',
        'commission_rate',
        'bank_account',
        'bank_name',
        'accepts_new_clients',
        'working_days',
        'preferred_start_time',
        'preferred_end_time',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'license_verified',
        'license_verified_at',
        'license_document',
        'background_check_passed',
        'background_check_at',
        'average_rating',
        'total_sessions',
        'total_revenue',
        'total_commission',
        'preferences',
        'metadata',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
        'languages' => 'array',
        'working_days' => 'array',
        'preferred_start_time' => 'datetime',
        'preferred_end_time' => 'datetime',
        'license_verified_at' => 'datetime',
        'background_check_at' => 'datetime',
        'preferences' => 'array',
        'metadata' => 'array',
    ];

    protected $hidden = [
        'bank_account',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'therapist_skills')
            ->withPivot('proficiency_level', 'years_experience', 'certified')
            ->withTimestamps();
    }

    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(Specialization::class, 'therapist_specializations')
            ->withPivot('primary_specialization', 'years_experience')
            ->withTimestamps();
    }

    public function workingSchedules(): HasMany
    {
        return $this->hasMany(WorkingSchedule::class);
    }

    public function availabilitySlots(): HasMany
    {
        return $this->hasMany(AvailabilitySlot::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(TherapistRating::class);
    }

    public function performanceMetrics(): HasMany
    {
        return $this->hasMany(PerformanceMetric::class);
    }

    public function commissionRecords(): HasMany
    {
        return $this->hasMany(CommissionRecord::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(TherapistNote::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByEmploymentType($query, $type)
    {
        return $query->where('employment_type', $type);
    }

    public function scopeVerified($query)
    {
        return $query->where('license_verified', true);
    }

    public function scopeAcceptingNewClients($query)
    {
        return $query->where('accepts_new_clients', true);
    }

    public function scopeBySpecialization($query, $specializationId)
    {
        return $query->whereHas('specializations', function ($q) use ($specializationId) {
            $q->where('specialization_id', $specializationId);
        });
    }

    public function scopeBySkill($query, $skillId)
    {
        return $query->whereHas('skills', function ($q) use ($skillId) {
            $q->where('skill_id', $skillId);
        });
    }

    public function scopeByRating($query, $minRating = 0)
    {
        return $query->where('average_rating', '>=', $minRating);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getEmploymentTypeLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->employment_type));
    }

    public function getFullNameAttribute()
    {
        return $this->user->name;
    }

    public function getEmailAttribute()
    {
        return $this->user->email;
    }

    public function getIsAvailableAttribute()
    {
        return $this->status === 'active' && $this->accepts_new_clients;
    }

    public function getFormattedHourlyRateAttribute()
    {
        return $this->hourly_rate ? '$' . number_format($this->hourly_rate, 2) : 'Not set';
    }

    public function getExperienceLevelAttribute()
    {
        if ($this->years_of_experience < 2) return 'Junior';
        if ($this->years_of_experience < 5) return 'Mid-level';
        if ($this->years_of_experience < 10) return 'Senior';
        return 'Expert';
    }

    // Helper methods
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function setOnLeave(): void
    {
        $this->update(['status' => 'on_leave']);
    }

    public function verifyLicense(): void
    {
        $this->update([
            'license_verified' => true,
            'license_verified_at' => now(),
        ]);
    }

    public function passBackgroundCheck(): void
    {
        $this->update([
            'background_check_passed' => true,
            'background_check_at' => now(),
        ]);
    }

    public function updateRating(): void
    {
        $averageRating = $this->ratings()->avg('rating');
        $this->update(['average_rating' => $averageRating]);
    }

    public function addSkill($skillId, $proficiencyLevel = 'intermediate', $yearsExperience = 0, $certified = false): void
    {
        $this->skills()->syncWithoutDetaching([
            $skillId => [
                'proficiency_level' => $proficiencyLevel,
                'years_experience' => $yearsExperience,
                'certified' => $certified,
            ],
        ]);
    }

    public function removeSkill($skillId): void
    {
        $this->skills()->detach($skillId);
    }

    public function addSpecialization($specializationId, $isPrimary = false, $yearsExperience = 0): void
    {
        // If this is primary, unset other primary specializations
        if ($isPrimary) {
            $this->specializations()->newPivotStatement()
                ->where('therapist_id', $this->id)
                ->update(['primary_specialization' => false]);
        }

        $this->specializations()->syncWithoutDetaching([
            $specializationId => [
                'primary_specialization' => $isPrimary,
                'years_experience' => $yearsExperience,
            ],
        ]);
    }

    public function getPrimarySpecialization()
    {
        return $this->specializations()->wherePivot('primary_specialization', true)->first();
    }

    public function getAvailabilityForDate($date)
    {
        return $this->availabilitySlots()
            ->whereDate('date', $date)
            ->where('is_available', true)
            ->orderBy('start_time')
            ->get();
    }

    public function isAvailableAt($dateTime)
    {
        return $this->availabilitySlots()
            ->where('date', $dateTime->format('Y-m-d'))
            ->where('start_time', '<=', $dateTime->format('H:i'))
            ->where('end_time', '>', $dateTime->format('H:i'))
            ->where('is_available', true)
            ->exists();
    }

    public function createAvailabilitySlot($date, $startTime, $endTime, $isAvailable = true)
    {
        return $this->availabilitySlots()->create([
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_available' => $isAvailable,
        ]);
    }

    public function recordAttendance($date, $checkIn, $checkOut = null, $status = 'present')
    {
        return $this->attendanceRecords()->create([
            'date' => $date,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => $status,
        ]);
    }

    public function requestLeave($startDate, $endDate, $type, $reason)
    {
        return $this->leaveRequests()->create([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'type' => $type,
            'reason' => $reason,
            'status' => 'pending',
        ]);
    }

    public function addRating($clientId, $rating, $comment = null)
    {
        return $this->ratings()->create([
            'client_id' => $clientId,
            'rating' => $rating,
            'comment' => $comment,
        ]);
    }

    public function addCommission($amount, $type, $description = null, $relatedId = null)
    {
        return $this->commissionRecords()->create([
            'amount' => $amount,
            'type' => $type,
            'description' => $description,
            'related_id' => $relatedId,
            'status' => 'pending',
        ]);
    }

    public function addPerformanceMetric($metricType, $value, $period, $notes = null)
    {
        return $this->performanceMetrics()->create([
            'metric_type' => $metricType,
            'value' => $value,
            'period' => $period,
            'notes' => $notes,
        ]);
    }

    public function addNote($content, $type = 'general', $authorId = null)
    {
        return $this->notes()->create([
            'content' => $content,
            'type' => $type,
            'author_id' => $authorId ?? auth()->id(),
        ]);
    }

    public function getMonthlyRevenue($year, $month)
    {
        return $this->commissionRecords()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function getMonthlySessions($year, $month)
    {
        return $this->attendanceRecords()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('status', 'present')
            ->count();
    }

    public function getUpcomingAvailability($days = 7)
    {
        return $this->availabilitySlots()
            ->where('date', '>=', now())
            ->where('date', '<=', now()->addDays($days))
            ->where('is_available', true)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    public static function getEmploymentTypes(): array
    {
        return [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'freelance' => 'Freelance',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'on_leave' => 'On Leave',
            'suspended' => 'Suspended',
        ];
    }

    public function getProfileSummary(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'email' => $this->email,
            'professional_title' => $this->professional_title,
            'license_number' => $this->license_number,
            'status' => $this->status_label,
            'employment_type' => $this->employment_type_label,
            'average_rating' => $this->average_rating,
            'total_sessions' => $this->total_sessions,
            'years_of_experience' => $this->years_of_experience,
            'experience_level' => $this->experience_level,
            'hourly_rate' => $this->formatted_hourly_rate,
            'accepts_new_clients' => $this->accepts_new_clients,
            'is_available' => $this->is_available,
            'primary_specialization' => $this->getPrimarySpecialization()?->name,
            'skills' => $this->skills->pluck('name'),
            'languages' => $this->languages,
            'profile_image' => $this->profile_image,
            'license_verified' => $this->license_verified,
            'background_check_passed' => $this->background_check_passed,
        ];
    }
}
