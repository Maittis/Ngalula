<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_transactions';

    protected $fillable = [
        'transaction_number',
        'transaction_type',
        'sub_type',
        'transactionable_id',
        'transactionable_type',
        'quantity',
        'unit_of_measure',
        'unit_cost',
        'total_cost',
        'stock_before',
        'stock_after',
        'location_from',
        'location_to',
        'warehouse',
        'storage_location',
        'reference_number',
        'reference_type',
        'purchase_request_id',
        'supplier_id',
        'created_by',
        'approved_by',
        'performed_by',
        'transaction_date',
        'transaction_time',
        'approved_at',
        'performed_at',
        'recorded_at',
        'reason',
        'notes',
        'internal_notes',
        'condition',
        'condition_notes',
        'quality_checked',
        'quality_checked_by',
        'quality_checked_at',
        'batch_number',
        'lot_number',
        'manufacture_date',
        'expiry_date',
        'currency',
        'tax_amount',
        'discount_amount',
        'shipping_cost',
        'handling_cost',
        'approval_notes',
        'rejection_reason',
        'status',
        'source',
        'import_batch_id',
        'metadata',
        'attachments',
        'receipt_file_path',
        'invoice_file_path',
        'barcode_scanned',
        'qr_code_scanned',
        'scanned_barcodes',
        'audit_trail',
        'ip_address',
        'user_agent',
        'is_reconciled',
        'reconciled_at',
        'reconciled_by',
        'reconciliation_notes',
        'category',
        'tags',
        'project_code',
        'cost_center',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'handling_cost' => 'decimal:2',
        'created_by' => 'integer',
        'approved_by' => 'integer',
        'performed_by' => 'integer',
        'quality_checked_by' => 'integer',
        'reconciled_by' => 'integer',
        'transaction_date' => 'date',
        'transaction_time' => 'time',
        'approved_at' => 'datetime',
        'performed_at' => 'datetime',
        'recorded_at' => 'datetime',
        'quality_checked_at' => 'datetime',
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
        'reconciled_at' => 'datetime',
        'quality_checked' => 'boolean',
        'is_reconciled' => 'boolean',
        'attachments' => 'array',
        'scanned_barcodes' => 'array',
        'audit_trail' => 'array',
        'metadata' => 'array',
        'tags' => 'array',
    ];

    protected $dates = [
        'transaction_date',
        'approved_at',
        'performed_at',
        'recorded_at',
        'quality_checked_at',
        'manufacture_date',
        'expiry_date',
        'reconciled_at',
    ];

    // Relationships
    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function qualityChecker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'quality_checked_by');
    }

    public function reconciler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeBySubType($query, $subType)
    {
        return $query->where('sub_type', $subType);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('transaction_date', $date);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeByItem($query, $itemType, $itemId)
    {
        return $query->where('transactionable_type', $itemType)
                    ->where('transactionable_id', $itemId);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where(function($q) use ($location) {
            $q->where('location_from', $location)
              ->orWhere('location_to', $location);
        });
    }

    public function scopeByWarehouse($query, $warehouse)
    {
        return $query->where('warehouse', $warehouse);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('created_by', $creatorId);
    }

    public function scopeAdditions($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeDeductions($query)
    {
        return $query->where('quantity', '<', 0);
    }

    public function scopePurchases($query)
    {
        return $query->where('transaction_type', 'purchase');
    }

    public function scopeSales($query)
    {
        return $query->where('transaction_type', 'sale');
    }

    public function scopeTransfers($query)
    {
        return $query->where('transaction_type', 'transfer');
    }

    public function scopeAdjustments($query)
    {
        return $query->where('transaction_type', 'adjustment');
    }

    public function scopeConsumption($query)
    {
        return $query->where('transaction_type', 'consumption');
    }

    public function scopeReturns($query)
    {
        return $query->where('transaction_type', 'return');
    }

    public function scopeDamage($query)
    {
        return $query->where('transaction_type', 'damage');
    }

    public function scopeLoss($query)
    {
        return $query->where('transaction_type', 'loss');
    }

    public function scopeExpiration($query)
    {
        return $query->where('transaction_type', 'expiration');
    }

    public function scopeRestock($query)
    {
        return $query->where('transaction_type', 'restock');
    }

    public function scopeDisposal($query)
    {
        return $query->where('transaction_type', 'disposal');
    }

    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    public function scopeQualityChecked($query)
    {
        return $query->where('quality_checked', true);
    }

    public function scopeReconciled($query)
    {
        return $query->where('is_reconciled', true);
    }

    public function scopeNotReconciled($query)
    {
        return $query->where('is_reconciled', false);
    }

    // Methods
    public function getTransactionTypeDisplay(): string
    {
        return match($this->transaction_type) {
            'purchase' => 'Purchase',
            'sale' => 'Sale',
            'transfer' => 'Transfer',
            'adjustment' => 'Adjustment',
            'return' => 'Return',
            'damage' => 'Damage',
            'loss' => 'Loss',
            'expiration' => 'Expiration',
            'consumption' => 'Consumption',
            'restock' => 'Restock',
            'disposal' => 'Disposal',
            default => ucfirst($this->transaction_type)
        };
    }

    public function getSubTypeDisplay(): string
    {
        return match($this->sub_type) {
            'new_purchase' => 'New Purchase',
            'reorder' => 'Reorder',
            'bulk_purchase' => 'Bulk Purchase',
            'emergency_purchase' => 'Emergency Purchase',
            'customer_sale' => 'Customer Sale',
            'internal_use' => 'Internal Use',
            'transfer_in' => 'Transfer In',
            'transfer_out' => 'Transfer Out',
            'stock_adjustment' => 'Stock Adjustment',
            'manual_adjustment' => 'Manual Adjustment',
            'system_adjustment' => 'System Adjustment',
            'customer_return' => 'Customer Return',
            'supplier_return' => 'Supplier Return',
            'damaged_goods' => 'Damaged Goods',
            'lost_goods' => 'Lost Goods',
            'expired_goods' => 'Expired Goods',
            'consumed' => 'Consumed',
            'restocked' => 'Restocked',
            'disposed' => 'Disposed',
            default => ucfirst(str_replace('_', ' ', $this->sub_type))
        };
    }

    public function getStatusDisplay(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'completed' => 'info',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    public function getConditionDisplay(): string
    {
        return match($this->condition) {
            'new' => 'New',
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor',
            'damaged' => 'Damaged',
            'expired' => 'Expired',
            default => ucfirst($this->condition)
        };
    }

    public function getConditionColor(): string
    {
        return match($this->condition) {
            'new' => 'success',
            'good' => 'success',
            'fair' => 'warning',
            'poor' => 'danger',
            'damaged' => 'danger',
            'expired' => 'danger',
            default => 'secondary'
        };
    }

    public function getSourceDisplay(): string
    {
        return match($this->source) {
            'manual' => 'Manual Entry',
            'system' => 'System Generated',
            'import' => 'Import',
            'api' => 'API',
            'barcode_scan' => 'Barcode Scan',
            'auto' => 'Automatic',
            default => ucfirst($this->source)
        };
    }

    public function isAddition(): bool
    {
        return $this->quantity > 0;
    }

    public function isDeduction(): bool
    {
        return $this->quantity < 0;
    }

    public function isPurchase(): bool
    {
        return $this->transaction_type === 'purchase';
    }

    public function isSale(): bool
    {
        return $this->transaction_type === 'sale';
    }

    public function isTransfer(): bool
    {
        return $this->transaction_type === 'transfer';
    }

    public function isAdjustment(): bool
    {
        return $this->transaction_type === 'adjustment';
    }

    public function isConsumption(): bool
    {
        return $this->transaction_type === 'consumption';
    }

    public function isReturn(): bool
    {
        return $this->transaction_type === 'return';
    }

    public function isDamage(): bool
    {
        return $this->transaction_type === 'damage';
    }

    public function isLoss(): bool
    {
        return $this->transaction_type === 'loss';
    }

    public function isExpiration(): bool
    {
        return $this->transaction_type === 'expiration';
    }

    public function isRestock(): bool
    {
        return $this->transaction_type === 'restock';
    }

    public function isDisposal(): bool
    {
        return $this->transaction_type === 'disposal';
    }

    public function isApproved(): bool
    {
        return $this->approved_at !== null;
    }

    public function isPending(): bool
    {
        return $this->approved_at === null;
    }

    public function isQualityChecked(): bool
    {
        return $this->quality_checked;
    }

    public function isReconciled(): bool
    {
        return $this->is_reconciled;
    }

    public function hasBarcode(): bool
    {
        return !empty($this->barcode_scanned) || !empty($this->qr_code_scanned);
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    public function getAbsoluteQuantity(): float
    {
        return abs($this->quantity);
    }

    public function getQuantityChange(): string
    {
        if ($this->quantity > 0) {
            return '+' . $this->quantity;
        } elseif ($this->quantity < 0) {
            return (string) $this->quantity;
        }
        
        return '0';
    }

    public function getQuantityColor(): string
    {
        if ($this->quantity > 0) {
            return 'success';
        } elseif ($this->quantity < 0) {
            return 'danger';
        }
        
        return 'secondary';
    }

    public function getStockChange(): float
    {
        return $this->stock_after - $this->stock_before;
    }

    public function getStockChangePercentage(): float
    {
        if ($this->stock_before == 0) {
            return $this->stock_change > 0 ? 100 : 0;
        }
        
        return ($this->stock_change / $this->stock_before) * 100;
    }

    public function getTotalCostWithExtras(): float
    {
        $total = $this->total_cost;
        
        if ($this->tax_amount) {
            $total += $this->tax_amount;
        }
        
        if ($this->shipping_cost) {
            $total += $this->shipping_cost;
        }
        
        if ($this->handling_cost) {
            $total += $this->handling_cost;
        }
        
        if ($this->discount_amount) {
            $total -= $this->discount_amount;
        }
        
        return $total;
    }

    public function getAttachments(): array
    {
        return $this->attachments ?: [];
    }

    public function getScannedBarcodes(): array
    {
        return $this->scanned_barcodes ?: [];
    }

    public function getAuditTrail(): array
    {
        return $this->audit_trail ?: [];
    }

    public function getMetadata(): array
    {
        return $this->metadata ?: [];
    }

    public function getTags(): array
    {
        return $this->tags ?: [];
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->getTags());
    }

    public function approve(User $approver, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    public function reject(User $rejecter, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $rejecter->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function perform(User $performer): void
    {
        $this->update([
            'performed_by' => $performer->id,
            'performed_at' => now(),
        ]);
    }

    public function completeQualityCheck(User $checker, string $notes = null): void
    {
        $this->update([
            'quality_checked' => true,
            'quality_checked_by' => $checker->id,
            'quality_checked_at' => now(),
            'condition_notes' => $notes,
        ]);
    }

    public function reconcile(User $reconciler, string $notes = null): void
    {
        $this->update([
            'is_reconciled' => true,
            'reconciled_by' => $reconciler->id,
            'reconciled_at' => now(),
            'reconciliation_notes' => $notes,
        ]);
    }

    public function addAttachment(string $attachment): void
    {
        $attachments = $this->getAttachments();
        $attachments[] = $attachment;
        $this->update(['attachments' => $attachments]);
    }

    public function addScannedBarcode(string $barcode): void
    {
        $scannedBarcodes = $this->getScannedBarcodes();
        $scannedBarcodes[] = $barcode;
        $this->update(['scanned_barcodes' => $scannedBarcodes]);
    }

    public function addAuditEntry(string $action, string $details, User $user): void
    {
        $trail = $this->getAuditTrail();
        $trail[] = [
            'action' => $action,
            'details' => $details,
            'user' => $user->id,
            'timestamp' => now()->toDateTimeString(),
        ];
        $this->update(['audit_trail' => $trail]);
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

    public function calculateValuePerUnit(): float
    {
        return $this->quantity != 0 ? $this->total_cost / $this->quantity : 0;
    }

    public function getTransactionValue(): float
    {
        return $this->getTotalCostWithExtras();
    }

    public function getTransactionSummary(): array
    {
        return [
            'type' => $this->getTransactionTypeDisplay(),
            'sub_type' => $this->getSubTypeDisplay(),
            'quantity' => $this->getQuantityChange(),
            'quantity_color' => $this->getQuantityColor(),
            'unit_of_measure' => $this->unit_of_measure,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'total_cost_with_extras' => $this->getTotalCostWithExtras(),
            'stock_before' => $this->stock_before,
            'stock_after' => $this->stock_after,
            'stock_change' => $this->getStockChange(),
            'stock_change_percentage' => $this->getStockChangePercentage(),
            'location_from' => $this->location_from,
            'location_to' => $this->location_to,
            'warehouse' => $this->warehouse,
            'storage_location' => $this->storage_location,
            'condition' => $this->getConditionDisplay(),
            'condition_color' => $this->getConditionColor(),
            'status' => $this->getStatusDisplay(),
            'status_color' => $this->getStatusColor(),
            'transaction_date' => $this->transaction_date,
            'transaction_time' => $this->transaction_time,
            'creator' => $this->creator->name ?? 'Unknown',
            'approved_by' => $this->approver->name ?? null,
            'performed_by' => $this->performer->name ?? null,
            'is_approved' => $this->isApproved(),
            'is_quality_checked' => $this->isQualityChecked(),
            'is_reconciled' => $this->isReconciled(),
            'has_barcode' => $this->hasBarcode(),
            'has_attachments' => $this->hasAttachments(),
            'source' => $this->getSourceDisplay(),
        ];
    }

    public function getFinancialSummary(): array
    {
        return [
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'shipping_cost' => $this->shipping_cost,
            'handling_cost' => $this->handling_cost,
            'total_cost_with_extras' => $this->getTotalCostWithExtras(),
            'value_per_unit' => $this->calculateValuePerUnit(),
            'currency' => $this->currency,
        ];
    }

    public function getTrackingInfo(): array
    {
        return [
            'batch_number' => $this->batch_number,
            'lot_number' => $this->lot_number,
            'manufacture_date' => $this->manufacture_date,
            'expiry_date' => $this->expiry_date,
            'barcode_scanned' => $this->barcode_scanned,
            'qr_code_scanned' => $this->qr_code_scanned,
            'scanned_barcodes' => $this->getScannedBarcodes(),
        ];
    }

    public function getWorkflowInfo(): array
    {
        return [
            'status' => $this->getStatusDisplay(),
            'status_color' => $this->getStatusColor(),
            'created_at' => $this->created_at,
            'approved_at' => $this->approved_at,
            'performed_at' => $this->performed_at,
            'quality_checked_at' => $this->quality_checked_at,
            'reconciled_at' => $this->reconciled_at,
            'creator' => $this->creator->name ?? 'Unknown',
            'approver' => $this->approver->name ?? null,
            'performer' => $this->performer->name ?? null,
            'quality_checker' => $this->quality_checker->name ?? null,
            'reconciler' => $this->reconciler->name ?? null,
            'is_approved' => $this->isApproved(),
            'is_quality_checked' => $this->isQualityChecked(),
            'is_reconciled' => $this->isReconciled(),
        ];
    }

    public function getFullSummary(): array
    {
        return array_merge(
            $this->getTransactionSummary(),
            [
                'financial_summary' => $this->getFinancialSummary(),
                'tracking_info' => $this->getTrackingInfo(),
                'workflow_info' => $this->getWorkflowInfo(),
                'item_type' => $this->transactionable_type,
                'item_id' => $this->transactionable_id,
                'reference_number' => $this->reference_number,
                'reference_type' => $this->reference_type,
                'supplier' => $this->supplier->name ?? null,
                'purchase_request' => $this->purchaseRequest->request_number ?? null,
                'reason' => $this->reason,
                'notes' => $this->notes,
                'internal_notes' => $this->internal_notes,
                'source' => $this->getSourceDisplay(),
                'tags' => $this->getTags(),
                'attachments' => $this->getAttachments(),
            ]
        );
    }
}
