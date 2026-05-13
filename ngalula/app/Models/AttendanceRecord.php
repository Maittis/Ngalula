<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'break_start',
        'break_end',
        'total_hours',
        'overtime_hours',
        'notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'break_start' => 'datetime',
        'break_end' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
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

    public function scopeByTherapist($query, $therapistId)
    {
        return $query->where('therapist_id', $therapistId);
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('Y-m-d') : null;
    }

    public function getFormattedCheckInAttribute()
    {
        return $this->check_in ? $this->check_in->format('H:i') : null;
    }

    public function getFormattedCheckOutAttribute()
    {
        return $this->check_out ? $this->check_out->format('H:i') : null;
    }

    public function getWorkedHoursAttribute()
    {
        if (!$this->check_in || !$this->check_out) {
            return 0;
        }

        $totalMinutes = $this->check_out->diffInMinutes($this->check_in);
        
        // Subtract break time
        if ($this->break_start && $this->break_end) {
            $breakMinutes = $this->break_end->diffInMinutes($this->break_start);
            $totalMinutes -= $breakMinutes;
        }

        return round($totalMinutes / 60, 2);
    }

    public function getBreakDurationAttribute()
    {
        if (!$this->break_start || !$this->break_end) {
            return 0;
        }

        return round($this->break_end->diffInMinutes($this->break_start) / 60, 2);
    }

    public function getIsLateAttribute()
    {
        if (!$this->check_in || $this->status === 'absent') {
            return false;
        }

        // Check if check-in is after 9:00 AM (or therapist's preferred start time)
        $expectedStartTime = $this->therapist->preferred_start_time ?? '09:00:00';
        return $this->check_in->format('H:i:s') > $expectedStartTime;
    }

    // Helper methods
    public function checkIn(): void
    {
        $this->update([
            'check_in' => now(),
            'status' => 'present',
        ]);
    }

    public function checkOut(): void
    {
        $this->update([
            'check_out' => now(),
        ]);
        
        // Calculate total hours
        $this->update([
            'total_hours' => $this->worked_hours,
        ]);
    }

    public function startBreak(): void
    {
        $this->update(['break_start' => now()]);
    }

    public function endBreak(): void
    {
        $this->update(['break_end' => now()]);
    }

    public function markAbsent($reason = null): void
    {
        $this->update([
            'status' => 'absent',
            'notes' => $reason,
        ]);
    }

    public function markLate($reason = null): void
    {
        $this->update([
            'status' => 'late',
            'notes' => $reason,
        ]);
    }

    public function approve($approvedBy): void
    {
        $this->update([
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    public function calculateOvertime($standardHours = 8): float
    {
        $workedHours = $this->worked_hours;
        $overtime = max(0, $workedHours - $standardHours);
        
        $this->update(['overtime_hours' => $overtime]);
        
        return $overtime;
    }

    public static function getStatuses(): array
    {
        return [
            'present' => 'Present',
            'absent' => 'Absent',
            'late' => 'Late',
            'half_day' => 'Half Day',
            'on_leave' => 'On Leave',
        ];
    }

    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->formatted_date,
            'check_in' => $this->formatted_check_in,
            'check_out' => $this->formatted_check_out,
            'status' => $this->status_label,
            'worked_hours' => $this->worked_hours,
            'break_duration' => $this->break_duration,
            'overtime_hours' => $this->overtime_hours,
            'is_late' => $this->is_late,
            'notes' => $this->notes,
            'approved_at' => $this->approved_at?->format('Y-m-d H:i'),
            'therapist_id' => $this->therapist_id,
        ];
    }
}
