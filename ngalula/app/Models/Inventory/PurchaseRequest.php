<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_purchase_requests';

    protected $fillable = [
        'request_number',
        'title',
        'description',
        'request_type',
        'priority',
        'status',
        'requested_by',
        'requester_department',
        'requester_position',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'estimated_total',
        'currency',
        'budget_approval',
        'budget_approved_by',
        'budget_approved_at',
        'preferred_supplier_id',
        'supplier_contact',
        'alternative_suppliers',
        'requested_delivery_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'delivery_address',
        'delivery_instructions',
        'delivery_method',
        'total_items',
        'total_quantity',
        'date_needed_by',
        'lead_time_days',
        'is_time_sensitive',
        'time_sensitivity_notes',
        'justification',
        'purpose',
        'impact_of_not_purchasing',
        'alternative_solutions',
        'attachments',
        'quote_file_path',
        'specification_file_path',
        'supporting_documents',
        'project_code',
        'cost_center',
        'department_code',
        'internal_tags',
        'communication_history',
        'last_communication_date',
        'communication_notes',
        'purchase_order_number',
        'order_date',
        'ordered_by',
        'received_date',
        'received_by',
        'receiving_notes',
        'receiving_discrepancies',
        'quality_check_status',
        'quality_checked_at',
        'quality_checked_by',
        'quality_check_notes',
        'invoice_number',
        'actual_total',
        'invoice_date',
        'payment_due_date',
        'payment_status',
        'days_to_approval',
        'days_to_delivery',
        'budget_variance',
        'delivery_variance',
        'internal_notes',
        'supplier_notes',
        'comments',
    ];

    protected $casts = [
        'estimated_total' => 'decimal:2',
        'actual_total' => 'decimal:2',
        'budget_variance' => 'decimal:2',
        'delivery_variance' => 'decimal:2',
        'total_items' => 'decimal:2',
        'total_quantity' => 'decimal:2',
        'lead_time_days' => 'integer',
        'is_time_sensitive' => 'boolean',
        'requested_by' => 'integer',
        'approved_by' => 'integer',
        'rejected_by' => 'integer',
        'budget_approved_by' => 'integer',
        'preferred_supplier_id' => 'integer',
        'ordered_by' => 'integer',
        'received_by' => 'integer',
        'quality_checked_by' => 'integer',
        'requested_delivery_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'date_needed_by' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'budget_approved_at' => 'datetime',
        'order_date' => 'date',
        'received_date' => 'date',
        'quality_checked_at' => 'datetime',
        'invoice_date' => 'date',
        'payment_due_date' => 'date',
        'last_communication_date' => 'date',
        'days_to_approval' => 'integer',
        'days_to_delivery' => 'integer',
        'alternative_suppliers' => 'array',
        'alternative_solutions' => 'array',
        'attachments' => 'array',
        'supporting_documents' => 'array',
        'internal_tags' => 'array',
        'communication_history' => 'array',
        'receiving_discrepancies' => 'array',
        'comments' => 'array',
    ];

    protected $dates = [
        'requested_delivery_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'date_needed_by',
        'approved_at',
        'rejected_at',
        'budget_approved_at',
        'order_date',
        'received_date',
        'quality_checked_at',
        'invoice_date',
        'payment_due_date',
        'last_communication_date',
    ];

    // Relationships
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function budgetApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'budget_approved_by');
    }

    public function preferredSupplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'preferred_supplier_id');
    }

    public function orderer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function qualityChecker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'quality_checked_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('request_type', $type);
    }

    public function scopeByRequester($query, $requesterId)
    {
        return $query->where('requested_by', $requesterId);
    }

    public function scopeByDepartment($query, $department)
    {
        return $query->where('requester_department', $department);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('preferred_supplier_id', $supplierId);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeOrdered($query)
    {
        return $query->where('status', 'ordered');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('requested_delivery_date', '<', now())
                    ->whereIn('status', ['approved', 'ordered']);
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('priority', ['high', 'urgent', 'critical']);
    }

    public function scopeTimeSensitive($query)
    {
        return $query->where('is_time_sensitive', true);
    }

    // Methods
    public function getRequestTypeDisplay(): string
    {
        return match($this->request_type) {
            'new_item' => 'New Item',
            'reorder' => 'Reorder',
            'emergency' => 'Emergency Purchase',
            'bulk_purchase' => 'Bulk Purchase',
            'replacement' => 'Replacement',
            default => ucfirst($this->request_type)
        };
    }

    public function getPriorityDisplay(): string
    {
        return match($this->priority) {
            'low' => 'Low',
            'medium' => 'Medium',
            'high' => 'High',
            'urgent' => 'Urgent',
            'critical' => 'Critical',
            default => ucfirst($this->priority)
        };
    }

    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'low' => 'info',
            'medium' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger',
            'critical' => 'danger',
            default => 'secondary'
        };
    }

    public function getStatusDisplay(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'pending_approval' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'ordered' => 'Ordered',
            'received' => 'Received',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'pending_approval' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'ordered' => 'primary',
            'received' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getBudgetApprovalDisplay(): string
    {
        return match($this->budget_approval) {
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'not_required' => 'Not Required',
            default => ucfirst($this->budget_approval)
        };
    }

    public function getBudgetApprovalColor(): string
    {
        return match($this->budget_approval) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'not_required' => 'info',
            default => 'secondary'
        };
    }

    public function getDeliveryMethodDisplay(): string
    {
        return match($this->delivery_method) {
            'standard' => 'Standard',
            'express' => 'Express',
            'overnight' => 'Overnight',
            'pickup' => 'Pickup',
            default => ucfirst($this->delivery_method)
        };
    }

    public function getQualityCheckStatusDisplay(): string
    {
        return match($this->quality_check_status) {
            'pending' => 'Pending',
            'passed' => 'Passed',
            'failed' => 'Failed',
            'partial' => 'Partial',
            default => ucfirst($this->quality_check_status)
        };
    }

    public function getQualityCheckColor(): string
    {
        return match($this->quality_check_status) {
            'pending' => 'warning',
            'passed' => 'success',
            'failed' => 'danger',
            'partial' => 'info',
            default => 'secondary'
        };
    }

    public function getPaymentStatusDisplay(): string
    {
        return match($this->payment_status) {
            'pending' => 'Pending',
            'partial' => 'Partial',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            default => ucfirst($this->payment_status)
        };
    }

    public function getPaymentStatusColor(): string
    {
        return match($this->payment_status) {
            'pending' => 'warning',
            'partial' => 'info',
            'paid' => 'success',
            'overdue' => 'danger',
            default => 'secondary'
        };
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isOrdered(): bool
    {
        return $this->status === 'ordered';
    }

    public function isReceived(): bool
    {
        return $this->status === 'received';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isOverdue(): bool
    {
        return $this->requested_delivery_date && 
               $this->requested_delivery_date->isPast() && 
               in_array($this->status, ['approved', 'ordered']);
    }

    public function isUrgent(): bool
    {
        return in_array($this->priority, ['high', 'urgent', 'critical']);
    }

    public function isTimeSensitive(): bool
    {
        return $this->is_time_sensitive;
    }

    public function needsBudgetApproval(): bool
    {
        return $this->budget_approval === 'pending';
    }

    public function isBudgetApproved(): bool
    {
        return $this->budget_approval === 'approved';
    }

    public function isQualityChecked(): bool
    {
        return $this->quality_check_status === 'passed';
    }

    public function isQualityFailed(): bool
    {
        return $this->quality_check_status === 'failed';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isOverduePayment(): bool
    {
        return $this->payment_status === 'overdue';
    }

    public function getDaysToDelivery(): ?int
    {
        if (!$this->requested_delivery_date) {
            return null;
        }
        
        if ($this->requested_delivery_date->isPast()) {
            return 0;
        }
        
        return $this->requested_delivery_date->diffInDays(now());
    }

    public function getDaysToNeededBy(): ?int
    {
        if (!$this->date_needed_by) {
            return null;
        }
        
        if ($this->date_needed_by->isPast()) {
            return 0;
        }
        
        return $this->date_needed_by->diffInDays(now());
    }

    public function getDaysSinceApproval(): ?int
    {
        if (!$this->approved_at) {
            return null;
        }
        
        return $this->approved_at->diffInDays(now());
    }

    public function getDaysSinceOrder(): ?int
    {
        if (!$this->order_date) {
            return null;
        }
        
        return $this->order_date->diffInDays(now());
    }

    public function getDaysSinceReceiving(): ?int
    {
        if (!$this->received_date) {
            return null;
        }
        
        return $this->received_date->diffInDays(now());
    }

    public function getAttachments(): array
    {
        return $this->attachments ?: [];
    }

    public function getSupportingDocuments(): array
    {
        return $this->supporting_documents ?: [];
    }

    public function getAlternativeSuppliers(): array
    {
        return $this->alternative_suppliers ?: [];
    }

    public function getAlternativeSolutions(): array
    {
        return $this->alternative_solutions ?: [];
    }

    public function getInternalTags(): array
    {
        return $this->internal_tags ?: [];
    }

    public function getCommunicationHistory(): array
    {
        return $this->communication_history ?: [];
    }

    public function getReceivingDiscrepancies(): array
    {
        return $this->receiving_discrepancies ?: [];
    }

    public function getComments(): array
    {
        return $this->comments ?: [];
    }

    public function hasAttachment(string $attachment): bool
    {
        return in_array($attachment, $this->getAttachments());
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->getInternalTags());
    }

    public function submit(): void
    {
        $this->update(['status' => 'submitted']);
    }

    public function approve(User $approver, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
            'days_to_approval' => $this->created_at->diffInDays(now()),
        ]);
    }

    public function reject(User $rejecter, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_by' => $rejecter->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function approveBudget(User $approver): void
    {
        $this->update([
            'budget_approval' => 'approved',
            'budget_approved_by' => $approver->id,
            'budget_approved_at' => now(),
        ]);
    }

    public function rejectBudget(User $rejecter, string $reason): void
    {
        $this->update([
            'budget_approval' => 'rejected',
            'budget_approved_by' => $rejecter->id,
            'budget_approved_at' => now(),
        ]);
    }

    public function createOrder(string $poNumber, User $orderer): void
    {
        $this->update([
            'status' => 'ordered',
            'purchase_order_number' => $poNumber,
            'order_date' => now(),
            'ordered_by' => $orderer->id,
            'days_to_delivery' => $this->getDaysToDelivery(),
        ]);
    }

    public function receive(User $receiver, string $notes = null): void
    {
        $this->update([
            'status' => 'received',
            'received_date' => now(),
            'received_by' => $receiver->id,
            'receiving_notes' => $notes,
            'days_to_delivery' => $this->order_date ? $this->order_date->diffInDays(now()) : null,
            'delivery_variance' => $this->actual_delivery_date && $this->expected_delivery_date ? 
                $this->actual_delivery_date->diffInDays($this->expected_delivery_date) : null,
        ]);
    }

    public function completeQualityCheck(string $status, User $checker, string $notes = null): void
    {
        $this->update([
            'quality_check_status' => $status,
            'quality_checked_at' => now(),
            'quality_checked_by' => $checker->id,
            'quality_check_notes' => $notes,
        ]);
    }

    public function complete(string $poNumber = null, float $actualTotal = null): void
    {
        $this->update([
            'status' => 'completed',
            'purchase_order_number' => $poNumber ?? $this->purchase_order_number,
            'actual_total' => $actualTotal ?? $this->actual_total,
            'budget_variance' => $actualTotal ? $actualTotal - $this->estimated_total : $this->budget_variance,
        ]);
    }

    public function cancel(string $reason): void
    {
        $this->update([
            'status' => 'cancelled',
            'rejection_reason' => $reason,
        ]);
    }

    public function addCommunication(string $type, string $message, string $contactMethod = null): void
    {
        $history = $this->getCommunicationHistory();
        $history[] = [
            'type' => $type,
            'message' => $message,
            'contact_method' => $contactMethod,
            'date' => now()->toDateTimeString(),
            'user' => auth()->id(),
        ];
        
        $this->update([
            'communication_history' => $history,
            'last_communication_date' => now(),
            'communication_notes' => $message,
        ]);
    }

    public function addComment(string $comment): void
    {
        $comments = $this->getComments();
        $comments[] = [
            'comment' => $comment,
            'date' => now()->toDateTimeString(),
            'user' => auth()->id(),
        ];
        
        $this->update(['comments' => $comments]);
    }

    public function addReceivingDiscrepancy(string $description): void
    {
        $discrepancies = $this->getReceivingDiscrepancies();
        $discrepancies[] = [
            'description' => $description,
            'date' => now()->toDateTimeString(),
            'user' => auth()->id(),
        ];
        
        $this->update(['receiving_discrepancies' => $discrepancies]);
    }

    public function calculateTotalValue(): float
    {
        return $this->actual_total ?: $this->estimated_total;
    }

    public function getBudgetVariancePercentage(): ?float
    {
        if (!$this->budget_variance || $this->estimated_total === 0) {
            return null;
        }
        
        return ($this->budget_variance / $this->estimated_total) * 100;
    }

    public function getDeliveryVarianceDays(): ?int
    {
        if (!$this->delivery_variance) {
            return null;
        }
        
        return $this->delivery_variance;
    }

    public function getProgressPercentage(): float
    {
        $statusProgress = [
            'draft' => 0,
            'submitted' => 10,
            'pending_approval' => 20,
            'approved' => 40,
            'ordered' => 60,
            'received' => 80,
            'completed' => 100,
        ];
        
        return $statusProgress[$this->status] ?? 0;
    }

    public function getSummary(): array
    {
        return [
            'request_number' => $this->request_number,
            'title' => $this->title,
            'request_type' => $this->getRequestTypeDisplay(),
            'priority' => $this->getPriorityDisplay(),
            'priority_color' => $this->getPriorityColor(),
            'status' => $this->getStatusDisplay(),
            'status_color' => $this->getStatusColor(),
            'requester' => $this->requester->name ?? 'Unknown',
            'requester_department' => $this->requester_department,
            'estimated_total' => $this->estimated_total,
            'actual_total' => $this->actual_total,
            'currency' => $this->currency,
            'preferred_supplier' => $this->preferredSupplier->name ?? 'Not specified',
            'requested_delivery_date' => $this->requested_delivery_date,
            'expected_delivery_date' => $this->expected_delivery_date,
            'actual_delivery_date' => $this->actual_delivery_date,
            'days_to_delivery' => $this->getDaysToDelivery(),
            'is_overdue' => $this->isOverdue(),
            'is_urgent' => $this->isUrgent(),
            'is_time_sensitive' => $this->is_time_sensitive,
            'budget_approval' => $this->getBudgetApprovalDisplay(),
            'budget_approval_color' => $this->getBudgetApprovalColor(),
            'purchase_order_number' => $this->purchase_order_number,
            'quality_check_status' => $this->getQualityCheckStatusDisplay(),
            'quality_check_color' => $this->getQualityCheckColor(),
            'payment_status' => $this->getPaymentStatusDisplay(),
            'payment_status_color' => $this->getPaymentStatusColor(),
            'progress_percentage' => $this->getProgressPercentage(),
            'days_to_approval' => $this->days_to_approval,
            'days_to_delivery' => $this->days_to_delivery,
            'budget_variance' => $this->budget_variance,
            'delivery_variance' => $this->delivery_variance,
        ];
    }
}
