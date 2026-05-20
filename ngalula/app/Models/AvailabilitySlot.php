<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilitySlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
        'appointment_id',
        'notes',
        'recurring_pattern',
        'parent_slot_id',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_available' => 'boolean',
        'recurring_pattern' => 'array',
    ];

    // Relationships
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function parentSlot(): BelongsTo
    {
        return $this->belongsTo(AvailabilitySlot::class, 'parent_slot_id');
    }

    public function childSlots(): HasMany
    {
        return $this->hasMany(AvailabilitySlot::class, 'parent_slot_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeBooked($query)
    {
        return $query->where('is_available', false)
            ->whereNotNull('appointment_id');
    }

    public function scopeByTherapist($query, $therapistId)
    {
        return $query->where('therapist_id', $therapistId);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('date', '>=', now())
            ->orderBy('date')
            ->orderBy('start_time');
    }

    // Accessors
    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('Y-m-d') : null;
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? $this->start_time->format('H:i') : null;
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? $this->end_time->format('H:i') : null;
    }

    public function getDurationAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        return $this->end_time->diffInMinutes($this->start_time);
    }

    public function getStatusLabelAttribute()
    {
        if (!$this->is_available) {
            return $this->appointment_id ? 'Booked' : 'Unavailable';
        }

        return 'Available';
    }

    // Helper methods
    public function makeAvailable(): void
    {
        $this->update([
            'is_available' => true,
            'appointment_id' => null,
        ]);
    }

    public function makeUnavailable(): void
    {
        $this->update(['is_available' => false]);
    }

    public function book($appointmentId): void
    {
        $this->update([
            'is_available' => false,
            'appointment_id' => $appointmentId,
        ]);
    }

    public function cancelBooking(): void
    {
        $this->update([
            'is_available' => true,
            'appointment_id' => null,
        ]);
    }

    public function isOverlapping($startTime, $endTime): bool
    {
        return ($startTime < $this->end_time) && ($endTime > $this->start_time);
    }

    public function canAccommodate($duration): bool
    {
        return $this->is_available && $this->duration >= $duration;
    }

    public function createRecurringSlots($pattern, $endDate): array
    {
        $slots = [];
        $currentDate = $this->date->copy();
        
        while ($currentDate <= $endDate) {
            if ($this->shouldCreateSlotForDate($currentDate, $pattern)) {
                $slot = $this->replicate();
                $slot->date = $currentDate;
                $slot->parent_slot_id = $this->id;
                $slot->save();
                $slots[] = $slot;
            }
            
            $currentDate->addDay();
        }
        
        return $slots;
    }

    private function shouldCreateSlotForDate($date, $pattern): bool
    {
        if (!isset($pattern['days']) || !in_array(strtolower($date->format('l')), $pattern['days'])) {
            return false;
        }

        if (isset($pattern['exclude_dates'])) {
            foreach ($pattern['exclude_dates'] as $excludeDate) {
                if ($date->format('Y-m-d') === $excludeDate) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getConflictingSlots(): HasMany
    {
        return $this->hasMany(AvailabilitySlot::class, 'therapist_id', 'therapist_id')
            ->where('date', $this->date)
            ->where('id', '!=', $this->id)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('start_time', '<', $this->end_time)
                      ->where('end_time', '>', $this->start_time);
                });
            });
    }

    public function hasConflicts(): bool
    {
        return $this->getConflictingSlots()->exists();
    }

    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->formatted_date,
            'start_time' => $this->formatted_start_time,
            'end_time' => $this->formatted_end_time,
            'duration' => $this->duration,
            'status' => $this->status_label,
            'is_available' => $this->is_available,
            'appointment_id' => $this->appointment_id,
            'notes' => $this->notes,
            'therapist_id' => $this->therapist_id,
        ];
    }
}
