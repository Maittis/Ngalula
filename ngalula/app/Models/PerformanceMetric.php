<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'metric_type',
        'value',
        'period',
        'notes',
        'target_value',
        'achieved_value',
        'percentage_change',
        'benchmark',
        'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'target_value' => 'decimal:2',
        'achieved_value' => 'decimal:2',
        'percentage_change' => 'decimal:2',
        'benchmark' => 'decimal:2',
    ];

    // Relationships
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    public function scopeByTherapist($query, $therapistId)
    {
        return $query->where('therapist_id', $therapistId);
    }

    public function scopeAboveTarget($query)
    {
        return $query->whereColumn('value', '>=', 'target_value');
    }

    public function scopeBelowTarget($query)
    {
        return $query->whereColumn('value', '<', 'target_value');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getTypeLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->metric_type));
    }

    public function getPeriodLabelAttribute()
    {
        return ucfirst($this->period);
    }

    public function getPerformanceStatusAttribute()
    {
        if (!$this->target_value) return 'No Target';
        
        if ($this->value >= $this->target_value) return 'Above Target';
        if ($this->value >= $this->target_value * 0.8) return 'Near Target';
        return 'Below Target';
    }

    public function getAchievementPercentageAttribute()
    {
        if (!$this->target_value) return null;
        
        return round(($this->value / $this->target_value) * 100, 2);
    }

    public function getIsPositiveChangeAttribute()
    {
        return $this->percentage_change > 0;
    }

    // Helper methods
    public function isAboveTarget(): bool
    {
        return $this->target_value && $this->value >= $this->target_value;
    }

    public function isBelowTarget(): bool
    {
        return $this->target_value && $this->value < $this->target_value;
    }

    public function calculatePercentageChange($previousValue): float
    {
        if ($previousValue == 0) {
            return $this->value > 0 ? 100 : 0;
        }

        $change = (($this->value - $previousValue) / $previousValue) * 100;
        
        $this->update(['percentage_change' => round($change, 2)]);
        
        return round($change, 2);
    }

    public function updateAchievedValue($achievedValue): void
    {
        $this->update(['achieved_value' => $achievedValue]);
    }

    public static function getMetricTypes(): array
    {
        return [
            'client_satisfaction' => 'Client Satisfaction',
            'appointment_completion_rate' => 'Appointment Completion Rate',
            'on_time_arrival' => 'On Time Arrival',
            'client_retention' => 'Client Retention',
            'revenue_generated' => 'Revenue Generated',
            'new_clients_acquired' => 'New Clients Acquired',
            'session_duration' => 'Session Duration',
            'cancellation_rate' => 'Cancellation Rate',
            'no_show_rate' => 'No Show Rate',
            'referral_rate' => 'Referral Rate',
            'productivity_score' => 'Productivity Score',
            'quality_score' => 'Quality Score',
            'teamwork_score' => 'Teamwork Score',
            'professional_development' => 'Professional Development',
        ];
    }

    public static function getPeriods(): array
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
        ];
    }

    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'metric_type' => $this->type_label,
            'value' => $this->value,
            'target_value' => $this->target_value,
            'achieved_value' => $this->achieved_value,
            'achievement_percentage' => $this->achievement_percentage,
            'performance_status' => $this->performance_status,
            'period' => $this->period_label,
            'percentage_change' => $this->percentage_change,
            'is_positive_change' => $this->is_positive_change,
            'benchmark' => $this->benchmark,
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'therapist_id' => $this->therapist_id,
        ];
    }

    public static function getMetricsSummary($therapistId, $period = 'monthly', $limit = 6): array
    {
        $metrics = self::where('therapist_id', $therapistId)
            ->where('period', $period)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->groupBy('metric_type');

        $summary = [];
        foreach ($metrics as $type => $typeMetrics) {
            $latest = $typeMetrics->first();
            $previous = $typeMetrics->skip(1)->first();
            
            $summary[$type] = [
                'current' => $latest->value,
                'target' => $latest->target_value,
                'achievement_percentage' => $latest->achievement_percentage,
                'performance_status' => $latest->performance_status,
                'trend' => $previous ? $latest->calculatePercentageChange($previous->value) : 0,
                'history' => $typeMetrics->map(function ($metric) {
                    return [
                        'value' => $metric->value,
                        'date' => $metric->created_at->format('Y-m-d'),
                    ];
                })->reverse()->values(),
            ];
        }

        return $summary;
    }
}
