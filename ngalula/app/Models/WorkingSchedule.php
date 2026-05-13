<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkingSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
        'break_start_time',
        'break_end_time',
        'max_appointments',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'break_start_time' => 'datetime',
        'break_end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDay($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    public function scopeByTherapist($query, $therapistId)
    {
        return $query->where('therapist_id', $therapistId);
    }

    // Accessors
    public function getDayLabelAttribute()
    {
        $days = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        return $days[$this->day_of_week] ?? ucfirst($this->day_of_week);
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? $this->start_time->format('H:i') : null;
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? $this->end_time->format('H:i') : null;
    }

    public function getWorkingHoursAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = $this->start_time;
        $end = $this->end_time;
        $breakDuration = 0;

        if ($this->break_start_time && $this->break_end_time) {
            $breakStart = $this->break_start_time;
            $breakEnd = $this->break_end_time;
            $breakDuration = $breakEnd->diffInMinutes($breakStart);
        }

        return ($end->diffInMinutes($start) - $breakDuration) / 60;
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

    public function isWithinWorkingHours($dateTime): bool
    {
        if (!$this->is_active || $this->day_of_week !== strtolower($dateTime->format('l'))) {
            return false;
        }

        $time = $dateTime->format('H:i');
        $startTime = $this->start_time->format('H:i');
        $endTime = $this->end_time->format('H:i');

        if ($this->break_start_time && $this->break_end_time) {
            $breakStartTime = $this->break_start_time->format('H:i');
            $breakEndTime = $this->break_end_time->format('H:i');

            // Check if time is during break
            if ($time >= $breakStartTime && $time <= $breakEndTime) {
                return false;
            }
        }

        return $time >= $startTime && $time <= $endTime;
    }

    public function getAvailableSlots($duration = 60): array
    {
        if (!$this->is_active) {
            return [];
        }

        $slots = [];
        $currentTime = $this->start_time->copy();
        $endTime = $this->end_time->copy();

        while ($currentTime->diffInMinutes($endTime) >= $duration) {
            // Skip break time
            if ($this->break_start_time && $this->break_end_time) {
                if ($currentTime >= $this->break_start_time && $currentTime < $this->break_end_time) {
                    $currentTime = $this->break_end_time->copy();
                    continue;
                }
            }

            $slots[] = $currentTime->copy();
            $currentTime->addMinutes($duration);
        }

        return $slots;
    }

    public static function getDaysOfWeek(): array
    {
        return [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
    }
}
