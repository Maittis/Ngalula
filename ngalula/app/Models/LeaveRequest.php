<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'attachment',
        'emergency_contact',
        'coverage_arranged',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'coverage_arranged' => 'boolean',
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

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function ($subQ) use ($startDate, $endDate) {
                  $subQ->where('start_date', '<=', $startDate)
                       ->where('end_date', '>=', $endDate);
              });
        });
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())
            ->orderBy('start_date');
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }

    public function getTypeLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getFormattedStartDateAttribute()
    {
        return $this->start_date ? $this->start_date->format('Y-m-d') : null;
    }

    public function getFormattedEndDateAttribute()
    {
        return $this->end_date ? $this->end_date->format('Y-m-d') : null;
    }

    public function getDurationAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function getWorkingDaysAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $days = 0;
        $current = $this->start_date->copy();

        while ($current <= $this->end_date) {
            if ($current->isWeekday()) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }

    public function getIsOverlappingAttribute()
    {
        return $this->start_date <= now() && $this->end_date >= now();
    }

    // Helper methods
    public function approve($approvedBy): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    public function reject($rejectedBy, $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_by' => $rejectedBy,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function isActive(): bool
    {
        return $this->status === 'approved' && $this->is_overlapping;
    }

    public function canBeModified(): bool
    {
        return in_array($this->status, ['pending', 'approved']) && $this->start_date > now();
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment);
    }

    public function getAttachmentUrl(): ?string
    {
        return $this->attachment ? storage_path('app/' . $this->attachment) : null;
    }

    public static function getTypes(): array
    {
        return [
            'annual_leave' => 'Annual Leave',
            'sick_leave' => 'Sick Leave',
            'personal_leave' => 'Personal Leave',
            'maternity_leave' => 'Maternity Leave',
            'paternity_leave' => 'Paternity Leave',
            'emergency_leave' => 'Emergency Leave',
            'unpaid_leave' => 'Unpaid Leave',
            'study_leave' => 'Study Leave',
            'compassionate_leave' => 'Compassionate Leave',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ];
    }

    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'start_date' => $this->formatted_start_date,
            'end_date' => $this->formatted_end_date,
            'type' => $this->type_label,
            'status' => $this->status_label,
            'duration' => $this->duration,
            'working_days' => $this->working_days,
            'reason' => $this->reason,
            'is_active' => $this->is_active,
            'can_be_modified' => $this->can_be_modified,
            'approved_at' => $this->approved_at?->format('Y-m-d H:i'),
            'rejected_at' => $this->rejected_at?->format('Y-m-d H:i'),
            'rejection_reason' => $this->rejection_reason,
            'therapist_id' => $this->therapist_id,
        ];
    }
}
