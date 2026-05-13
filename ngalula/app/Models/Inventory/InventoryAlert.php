<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryAlert extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_alerts';

    protected $fillable = [
        'alert_number',
        'alert_type',
        'severity',
        'status',
        'alertable_id',
        'alertable_type',
        'title',
        'message',
        'description',
        'current_value',
        'threshold_value',
        'unit',
        'location',
        'warehouse',
        'storage_location',
        'triggered_at',
        'first_detected_at',
        'last_occurrence_at',
        'occurrence_count',
        'created_by',
        'acknowledged_by',
        'resolved_by',
        'assigned_to',
        'acknowledged_at',
        'resolved_at',
        'resolution_notes',
        'resolution_method',
        'is_escalated',
        'escalated_at',
        'escalated_by',
        'escalated_to',
        'escalation_reason',
        'notification_channels',
        'notified_users',
        'last_notification_sent_at',
        'notification_count',
        'notifications_enabled',
        'is_recurring',
        'recurrence_pattern',
        'next_occurrence',
        'recurrence_end',
        'business_impact',
        'impact_description',
        'potential_cost',
        'affected_customers',
        'recommended_actions',
        'action_taken',
        'action_taken_at',
        'source_system',
        'trigger_rule',
        'system_data',
        'related_transaction_id',
        'related_purchase_request_id',
        'related_alerts',
        'category',
        'tags',
        'attachments',
        'external_reference',
        'external_system',
        'history',
        'ip_address',
        'resolved_within_sla',
        'resolution_time_minutes',
        'resolution_cost',
        'internal_notes',
        'comments',
    ];

    protected $casts = [
        'current_value' => 'decimal:2',
        'threshold_value' => 'decimal:2',
        'potential_cost' => 'decimal:2',
        'affected_customers' => 'integer',
        'occurrence_count' => 'integer',
        'created_by' => 'integer',
        'acknowledged_by' => 'integer',
        'resolved_by' => 'integer',
        'assigned_to' => 'integer',
        'escalated_by' => 'integer',
        'escalated_to' => 'integer',
        'notification_count' => 'integer',
        'notifications_enabled' => 'boolean',
        'is_escalated' => 'boolean',
        'is_recurring' => 'boolean',
        'triggered_at' => 'datetime',
        'first_detected_at' => 'datetime',
        'last_occurrence_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
        'escalated_at' => 'datetime',
        'last_notification_sent_at' => 'datetime',
        'next_occurrence' => 'datetime',
        'recurrence_end' => 'datetime',
        'action_taken_at' => 'datetime',
        'resolved_within_sla' => 'boolean',
        'resolution_time_minutes' => 'integer',
        'resolution_cost' => 'decimal:2',
        'notification_channels' => 'array',
        'notified_users' => 'array',
        'recommended_actions' => 'array',
        'related_alerts' => 'array',
        'tags' => 'array',
        'attachments' => 'array',
        'system_data' => 'array',
        'history' => 'array',
        'comments' => 'array',
    ];

    protected $dates = [
        'triggered_at',
        'first_detected_at',
        'last_occurrence_at',
        'acknowledged_at',
        'resolved_at',
        'escalated_at',
        'last_notification_sent_at',
        'next_occurrence',
        'recurrence_end',
        'action_taken_at',
    ];

    // Relationships
    public function alertable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function acknowledger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function escalator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_by');
    }

    public function escalationTarget(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }

    public function relatedTransaction(): BelongsTo
    {
        return $this->belongsTo(InventoryTransaction::class, 'related_transaction_id');
    }

    public function relatedPurchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class, 'related_purchase_request_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('alert_type', $type);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAcknowledged($query)
    {
        return $query->where('status', 'acknowledged');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeDismissed($query)
    {
        return $query->where('status', 'dismissed');
    }

    public function scopeEscalated($query)
    {
        return $query->where('is_escalated', true);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeByItem($query, $itemType, $itemId)
    {
        return $query->where('alertable_type', $itemType)
                    ->where('alertable_id', $itemId);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function scopeByWarehouse($query, $warehouse)
    {
        return $query->where('warehouse', $warehouse);
    }

    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('created_by', $creatorId);
    }

    public function scopeByAssignee($query, $assigneeId)
    {
        return $query->where('assigned_to', $assigneeId);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByImpact($query, $impact)
    {
        return $query->where('business_impact', $impact);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('triggered_at', [$startDate, $endDate]);
    }

    public function scopeUnacknowledged($query)
    {
        return $query->whereNull('acknowledged_at');
    }

    public function scopeUnresolved($query)
    {
        return $query->whereNotIn('status', ['resolved', 'dismissed']);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('severity', ['critical', 'emergency']);
    }

    public function scopeLowStock($query)
    {
        return $query->where('alert_type', 'low_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('alert_type', 'out_of_stock');
    }

    public function scopeExpiringSoon($query)
    {
        return $query->where('alert_type', 'expiring_soon');
    }

    public function scopeExpired($query)
    {
        return $query->where('alert_type', 'expired');
    }

    public function scopeOverstock($query)
    {
        return $query->where('alert_type', 'overstock');
    }

    public function scopeMaintenanceDue($query)
    {
        return $query->where('alert_type', 'maintenance_due');
    }

    public function scopeQualityIssue($query)
    {
        return $query->where('alert_type', 'quality_issue');
    }

    // Methods
    public function getAlertTypeDisplay(): string
    {
        return match($this->alert_type) {
            'low_stock' => 'Low Stock',
            'out_of_stock' => 'Out of Stock',
            'expiring_soon' => 'Expiring Soon',
            'expired' => 'Expired',
            'overstock' => 'Overstock',
            'reorder_needed' => 'Reorder Needed',
            'quality_issue' => 'Quality Issue',
            'maintenance_due' => 'Maintenance Due',
            'price_change' => 'Price Change',
            'supplier_issue' => 'Supplier Issue',
            'system_error' => 'System Error',
            default => ucfirst(str_replace('_', ' ', $this->alert_type))
        };
    }

    public function getSeverityDisplay(): string
    {
        return match($this->severity) {
            'info' => 'Info',
            'warning' => 'Warning',
            'critical' => 'Critical',
            'emergency' => 'Emergency',
            default => ucfirst($this->severity)
        };
    }

    public function getSeverityColor(): string
    {
        return match($this->severity) {
            'info' => 'info',
            'warning' => 'warning',
            'critical' => 'danger',
            'emergency' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusDisplay(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'acknowledged' => 'Acknowledged',
            'resolved' => 'Resolved',
            'dismissed' => 'Dismissed',
            'escalated' => 'Escalated',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'active' => 'warning',
            'acknowledged' => 'info',
            'resolved' => 'success',
            'dismissed' => 'secondary',
            'escalated' => 'danger',
            default => 'secondary'
        };
    }

    public function getBusinessImpactDisplay(): string
    {
        return match($this->business_impact) {
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'critical' => 'Critical',
            default => ucfirst($this->business_impact)
        };
    }

    public function getBusinessImpactColor(): string
    {
        return match($this->business_impact) {
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'danger',
            default => 'secondary'
        };
    }

    public function getResolutionMethodDisplay(): string
    {
        return match($this->resolution_method) {
            'auto_resolved' => 'Auto Resolved',
            'manual_resolved' => 'Manual Resolved',
            'system_corrected' => 'System Corrected',
            'supplier_action' => 'Supplier Action',
            'user_action' => 'User Action',
            'escalation' => 'Escalation',
            default => ucfirst(str_replace('_', ' ', $this->resolution_method))
        };
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isAcknowledged(): bool
    {
        return $this->status === 'acknowledged';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isDismissed(): bool
    {
        return $this->status === 'dismissed';
    }

    public function isEscalated(): bool
    {
        return $this->is_escalated;
    }

    public function isRecurring(): bool
    {
        return $this->is_recurring;
    }

    public function isCritical(): bool
    {
        return in_array($this->severity, ['critical', 'emergency']);
    }

    public function isLowStock(): bool
    {
        return $this->alert_type === 'low_stock';
    }

    public function isOutOfStock(): bool
    {
        return $this->alert_type === 'out_of_stock';
    }

    public function isExpiringSoon(): bool
    {
        return $this->alert_type === 'expiring_soon';
    }

    public function isExpired(): bool
    {
        return $this->alert_type === 'expired';
    }

    public function isOverstock(): bool
    {
        return $this->alert_type === 'overstock';
    }

    public function isMaintenanceDue(): bool
    {
        return $this->alert_type === 'maintenance_due';
    }

    public function isQualityIssue(): bool
    {
        return $this->alert_type === 'quality_issue';
    }

    public function isUnacknowledged(): bool
    {
        return $this->acknowledged_at === null;
    }

    public function isUnresolved(): bool
    {
        return !in_array($this->status, ['resolved', 'dismissed']);
    }

    public function getDaysSinceTriggered(): int
    {
        return $this->triggered_at->diffInDays(now());
    }

    public function getDaysSinceAcknowledged(): ?int
    {
        if (!$this->acknowledged_at) {
            return null;
        }
        
        return $this->acknowledged_at->diffInDays(now());
    }

    public function getDaysSinceResolved(): ?int
    {
        if (!$this->resolved_at) {
            return null;
        }
        
        return $this->resolved_at->diffInDays(now());
    }

    public function getResolutionTime(): ?int
    {
        if (!$this->resolved_at) {
            return null;
        }
        
        return $this->triggered_at->diffInMinutes($this->resolved_at);
    }

    public function getAcknowledgmentTime(): ?int
    {
        if (!$this->acknowledged_at) {
            return null;
        }
        
        return $this->triggered_at->diffInMinutes($this->acknowledged_at);
    }

    public function wasResolvedWithinSLA(): bool
    {
        return $this->resolved_within_sla;
    }

    public function getNotificationChannels(): array
    {
        return $this->notification_channels ?: [];
    }

    public function getNotifiedUsers(): array
    {
        return $this->notified_users ?: [];
    }

    public function getRecommendedActions(): array
    {
        return $this->recommended_actions ?: [];
    }

    public function getRelatedAlerts(): array
    {
        return $this->related_alerts ?: [];
    }

    public function getTags(): array
    {
        return $this->tags ?: [];
    }

    public function getAttachments(): array
    {
        return $this->attachments ?: [];
    }

    public function getHistory(): array
    {
        return $this->history ?: [];
    }

    public function getComments(): array
    {
        return $this->comments ?: [];
    }

    public function getSystemData(): array
    {
        return $this->system_data ?: [];
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->getTags());
    }

    public function hasNotificationChannel(string $channel): bool
    {
        return in_array($channel, $this->getNotificationChannels());
    }

    public function wasNotified(int $userId): bool
    {
        return in_array($userId, $this->getNotifiedUsers());
    }

    public function acknowledge(User $acknowledger, string $notes = null): void
    {
        $this->update([
            'status' => 'acknowledged',
            'acknowledged_by' => $acknowledger->id,
            'acknowledged_at' => now(),
            'internal_notes' => $notes,
        ]);
        
        $this->addHistoryEntry('acknowledged', "Alert acknowledged by {$acknowledger->name}", $acknowledger);
    }

    public function resolve(User $resolver, string $method, string $notes = null): void
    {
        $resolutionTime = $this->getResolutionTime();
        
        $this->update([
            'status' => 'resolved',
            'resolved_by' => $resolver->id,
            'resolved_at' => now(),
            'resolution_method' => $method,
            'resolution_notes' => $notes,
            'resolution_time_minutes' => $resolutionTime,
        ]);
        
        $this->addHistoryEntry('resolved', "Alert resolved by {$resolver->name} using {$method}", $resolver);
    }

    public function dismiss(User $dismissor, string $reason = null): void
    {
        $this->update([
            'status' => 'dismissed',
            'resolved_by' => $dismissor->id,
            'resolved_at' => now(),
            'resolution_method' => 'dismissed',
            'resolution_notes' => $reason,
            'resolution_time_minutes' => $this->getResolutionTime(),
        ]);
        
        $this->addHistoryEntry('dismissed', "Alert dismissed by {$dismissor->name}: {$reason}", $dismissor);
    }

    public function escalate(User $escalator, User $escalateTo, string $reason): void
    {
        $this->update([
            'is_escalated' => true,
            'escalated_by' => $escalator->id,
            'escalated_to' => $escalateTo->id,
            'escalated_at' => now(),
            'escalation_reason' => $reason,
            'assigned_to' => $escalateTo->id,
        ]);
        
        $this->addHistoryEntry('escalated', "Alert escalated to {$escalateTo->name} by {$escalator->name}: {$reason}", $escalator);
    }

    public function assignTo(User $assignee): void
    {
        $this->update(['assigned_to' => $assignee->id]);
        $this->addHistoryEntry('assigned', "Alert assigned to {$assignee->name}", auth()->user());
    }

    public function recordNotification(array $channels, array $users): void
    {
        $this->update([
            'last_notification_sent_at' => now(),
            'notification_count' => $this->notification_count + 1,
            'notification_channels' => array_unique(array_merge($this->getNotificationChannels(), $channels)),
            'notified_users' => array_unique(array_merge($this->getNotifiedUsers(), $users)),
        ]);
    }

    public function addHistoryEntry(string $action, string $details, User $user): void
    {
        $history = $this->getHistory();
        $history[] = [
            'action' => $action,
            'details' => $details,
            'user' => $user->id,
            'user_name' => $user->name,
            'timestamp' => now()->toDateTimeString(),
        ];
        $this->update(['history' => $history]);
    }

    public function addComment(string $comment): void
    {
        $comments = $this->getComments();
        $comments[] = [
            'comment' => $comment,
            'user' => auth()->id(),
            'user_name' => auth()->user()->name,
            'timestamp' => now()->toDateTimeString(),
        ];
        $this->update(['comments' => $comments]);
    }

    public function addTag(string $tag): void
    {
        $tags = $this->getTags();
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag(string $tag): void
    {
        $tags = $this->getTags();
        $key = array_search($tag, $tags);
        if ($key !== false) {
            unset($tags[$key]);
            $this->update(['tags' => array_values($tags)]);
        }
    }

    public function addAttachment(string $attachment): void
    {
        $attachments = $this->getAttachments();
        $attachments[] = $attachment;
        $this->update(['attachments' => $attachments]);
    }

    public function recordAction(string $action): void
    {
        $this->update([
            'action_taken' => $action,
            'action_taken_at' => now(),
        ]);
    }

    public function setNextOccurrence(): void
    {
        if (!$this->is_recurring || !$this->recurrence_pattern) {
            return;
        }
        
        $nextOccurrence = match($this->recurrence_pattern) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            default => now()->addDay()
        };
        
        $this->update(['next_occurrence' => $nextOccurrence]);
    }

    public function calculatePriorityScore(): int
    {
        $score = 0;
        
        // Severity scoring
        $severityScores = [
            'info' => 1,
            'warning' => 2,
            'critical' => 3,
            'emergency' => 4,
        ];
        
        $score += $severityScores[$this->severity] * 10;
        
        // Business impact scoring
        $impactScores = [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            'critical' => 4,
        ];
        
        $score += $impactScores[$this->business_impact] * 8;
        
        // Age-based scoring (older alerts get higher priority)
        $daysSinceTriggered = $this->getDaysSinceTriggered();
        $score += min($daysSinceTriggered, 10);
        
        // Escalation bonus
        if ($this->is_escalated) {
            $score += 15;
        }
        
        // Recurring penalty (recurring alerts are less urgent)
        if ($this->is_recurring) {
            $score -= 5;
        }
        
        return max(0, $score);
    }

    public function getPriorityLevel(): string
    {
        $score = $this->calculatePriorityScore();
        
        if ($score >= 40) {
            return 'critical';
        } elseif ($score >= 30) {
            return 'high';
        } elseif ($score >= 20) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    public function getPriorityColor(): string
    {
        return match($this->getPriorityLevel()) {
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'secondary'
        };
    }

    public function getSummary(): array
    {
        return [
            'alert_number' => $this->alert_number,
            'alert_type' => $this->getAlertTypeDisplay(),
            'severity' => $this->getSeverityDisplay(),
            'severity_color' => $this->getSeverityColor(),
            'status' => $this->getStatusDisplay(),
            'status_color' => $this->getStatusColor(),
            'title' => $this->title,
            'message' => $this->message,
            'current_value' => $this->current_value,
            'threshold_value' => $this->threshold_value,
            'unit' => $this->unit,
            'location' => $this->location,
            'warehouse' => $this->warehouse,
            'storage_location' => $this->storage_location,
            'triggered_at' => $this->triggered_at,
            'days_since_triggered' => $this->getDaysSinceTriggered(),
            'occurrence_count' => $this->occurrence_count,
            'creator' => $this->creator->name ?? 'System',
            'assigned_to' => $this->assignee->name ?? null,
            'is_escalated' => $this->is_escalated,
            'is_recurring' => $this->is_recurring,
            'business_impact' => $this->getBusinessImpactDisplay(),
            'business_impact_color' => $this->getBusinessImpactColor(),
            'potential_cost' => $this->potential_cost,
            'affected_customers' => $this->affected_customers,
            'priority_level' => $this->getPriorityLevel(),
            'priority_color' => $this->getPriorityColor(),
            'item_type' => $this->alertable_type,
            'item_id' => $this->alertable_id,
            'tags' => $this->getTags(),
            'has_attachments' => !empty($this->getAttachments()),
        ];
    }

    public function getFullDetails(): array
    {
        return array_merge(
            $this->getSummary(),
            [
                'description' => $this->description,
                'first_detected_at' => $this->first_detected_at,
                'last_occurrence_at' => $this->last_occurrence_at,
                'acknowledged_at' => $this->acknowledged_at,
                'acknowledged_by' => $this->acknowledger->name ?? null,
                'resolved_at' => $this->resolved_at,
                'resolved_by' => $this->resolver->name ?? null,
                'resolution_method' => $this->getResolutionMethodDisplay(),
                'resolution_notes' => $this->resolution_notes,
                'resolution_time_minutes' => $this->resolution_time_minutes,
                'was_resolved_within_sla' => $this->wasResolvedWithinSLA(),
                'escalated_at' => $this->escalated_at,
                'escalated_by' => $this->escalator->name ?? null,
                'escalated_to' => $this->escalationTarget->name ?? null,
                'escalation_reason' => $this->escalation_reason,
                'notification_channels' => $this->getNotificationChannels(),
                'notified_users' => $this->getNotifiedUsers(),
                'notification_count' => $this->notification_count,
                'last_notification_sent_at' => $this->last_notification_sent_at,
                'recurrence_pattern' => $this->recurrence_pattern,
                'next_occurrence' => $this->next_occurrence,
                'recurrence_end' => $this->recurrence_end,
                'recommended_actions' => $this->getRecommendedActions(),
                'action_taken' => $this->action_taken,
                'action_taken_at' => $this->action_taken_at,
                'source_system' => $this->source_system,
                'trigger_rule' => $this->trigger_rule,
                'related_transaction_id' => $this->related_transaction_id,
                'related_purchase_request_id' => $this->related_purchase_request_id,
                'related_alerts' => $this->getRelatedAlerts(),
                'category' => $this->category,
                'external_reference' => $this->external_reference,
                'external_system' => $this->external_system,
                'history' => $this->getHistory(),
                'comments' => $this->getComments(),
                'attachments' => $this->getAttachments(),
                'internal_notes' => $this->internal_notes,
            ]
        );
    }
}
