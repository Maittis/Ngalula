<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_products';

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'subcategory',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'reorder_quantity',
        'cost_price',
        'selling_price',
        'currency',
        'unit_of_measure',
        'unit_size',
        'size_unit',
        'storage_location',
        'warehouse',
        'primary_supplier_id',
        'status',
        'is_trackable',
        'requires_refrigeration',
        'is_hazardous',
        'is_perishable',
        'expiry_date',
        'manufacture_date',
        'shelf_life_days',
        'batch_number',
        'lot_number',
        'manufacturer',
        'brand',
        'barcode',
        'qr_code',
        'average_monthly_usage',
        'last_used_date',
        'last_restocked_date',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'unit_size' => 'decimal:2',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
        'reorder_quantity' => 'integer',
        'average_monthly_usage' => 'decimal:2',
        'is_trackable' => 'boolean',
        'requires_refrigeration' => 'boolean',
        'is_hazardous' => 'boolean',
        'is_perishable' => 'boolean',
        'expiry_date' => 'date',
        'manufacture_date' => 'date',
        'shelf_life_days' => 'integer',
        'last_used_date' => 'date',
        'last_restocked_date' => 'date',
        'attachments' => 'array',
    ];

    protected $dates = [
        'expiry_date',
        'manufacture_date',
        'last_used_date',
        'last_restocked_date',
    ];

    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'primary_supplier_id');
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(InventoryTransaction::class, 'transactionable');
    }

    public function barcodes(): MorphMany
    {
        return $this->morphMany(Barcode::class, 'barcodeable');
    }

    public function alerts(): MorphMany
    {
        return $this->morphMany(InventoryAlert::class, 'alertable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('primary_supplier_id', $supplierId);
    }

    public function scopeRequiresRefrigeration($query)
    {
        return $query->where('requires_refrigeration', true);
    }

    public function scopeHazardous($query)
    {
        return $query->where('is_hazardous', true);
    }

    // Methods
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->isAfter(now()) && 
               $this->expiry_date->diffInDays(now()) <= $days;
    }

    public function getStockStatus(): string
    {
        if ($this->isOutOfStock()) {
            return 'out_of_stock';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        } elseif ($this->isExpiringSoon()) {
            return 'expiring_soon';
        } elseif ($this->isExpired()) {
            return 'expired';
        } elseif ($this->current_stock > $this->maximum_stock) {
            return 'overstock';
        } else {
            return 'normal';
        }
    }

    public function getStockStatusColor(): string
    {
        return match($this->getStockStatus()) {
            'out_of_stock' => 'danger',
            'low_stock' => 'warning',
            'expiring_soon' => 'warning',
            'expired' => 'danger',
            'overstock' => 'info',
            default => 'success'
        };
    }

    public function getDaysToExpiry(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        if ($this->isExpired()) {
            return 0;
        }
        
        return $this->expiry_date->diffInDays(now());
    }

    public function getRemainingShelfLifeDays(): ?int
    {
        if (!$this->manufacture_date || !$this->shelf_life_days) {
            return null;
        }
        
        $expiryDate = $this->manufacture_date->addDays($this->shelf_life_days);
        return max(0, $expiryDate->diffInDays(now()));
    }

    public function getTotalValue(): float
    {
        return $this->current_stock * $this->cost_price;
    }

    public function getReorderValue(): float
    {
        return $this->reorder_quantity * $this->cost_price;
    }

    public function getStockTurnoverRate(): float
    {
        if ($this->average_monthly_usage <= 0) {
            return 0;
        }
        
        return $this->current_stock / $this->average_monthly_usage;
    }

    public function needsReorder(): bool
    {
        return $this->isLowStock() && $this->reorder_quantity > 0;
    }

    public function canTrackIndividually(): bool
    {
        return $this->is_trackable;
    }

    public function isPerishable(): bool
    {
        return $this->is_perishable || ($this->expiry_date && $this->shelf_life_days);
    }

    public function getStorageRequirements(): array
    {
        $requirements = [];
        
        if ($this->requires_refrigeration) {
            $requirements[] = 'refrigeration';
        }
        
        if ($this->is_hazardous) {
            $requirements[] = 'hazardous_storage';
        }
        
        if ($this->is_perishable) {
            $requirements[] = 'perishable_storage';
        }
        
        return $requirements;
    }

    public function addStock(int $quantity, array $transactionData = []): InventoryTransaction
    {
        $this->increment('current_stock', $quantity);
        $this->update(['last_restocked_date' => now()]);
        
        return $this->transactions()->create(array_merge([
            'transaction_type' => 'purchase',
            'sub_type' => 'restock',
            'quantity' => $quantity,
            'stock_before' => $this->current_stock - $quantity,
            'stock_after' => $this->current_stock,
            'unit_of_measure' => $this->unit_of_measure,
            'unit_cost' => $this->cost_price,
            'total_cost' => $quantity * $this->cost_price,
            'transaction_date' => now(),
            'created_by' => auth()->id(),
        ], $transactionData));
    }

    public function removeStock(int $quantity, array $transactionData = []): InventoryTransaction
    {
        if ($quantity > $this->current_stock) {
            throw new \Exception("Insufficient stock. Available: {$this->current_stock}, Requested: {$quantity}");
        }
        
        $this->decrement('current_stock', $quantity);
        $this->update(['last_used_date' => now()]);
        
        return $this->transactions()->create(array_merge([
            'transaction_type' => 'consumption',
            'sub_type' => 'usage',
            'quantity' => -$quantity,
            'stock_before' => $this->current_stock + $quantity,
            'stock_after' => $this->current_stock,
            'unit_of_measure' => $this->unit_of_measure,
            'unit_cost' => $this->cost_price,
            'total_cost' => $quantity * $this->cost_price,
            'transaction_date' => now(),
            'created_by' => auth()->id(),
        ], $transactionData));
    }

    public function adjustStock(int $newQuantity, string $reason = ''): InventoryTransaction
    {
        $oldQuantity = $this->current_stock;
        $difference = $newQuantity - $oldQuantity;
        
        $this->update(['current_stock' => $newQuantity]);
        
        $subType = $difference > 0 ? 'increase' : 'decrease';
        
        return $this->transactions()->create([
            'transaction_type' => 'adjustment',
            'sub_type' => $subType,
            'quantity' => $difference,
            'stock_before' => $oldQuantity,
            'stock_after' => $newQuantity,
            'unit_of_measure' => $this->unit_of_measure,
            'unit_cost' => $this->cost_price,
            'total_cost' => abs($difference) * $this->cost_price,
            'transaction_date' => now(),
            'reason' => $reason,
            'created_by' => auth()->id(),
        ]);
    }

    public function generateBarcode(): string
    {
        $barcode = 'PROD' . str_pad($this->id, 6, '0', STR_PAD_LEFT) . rand(100, 999);
        
        $this->update(['barcode' => $barcode]);
        
        return $barcode;
    }

    public function generateQRCode(): string
    {
        $qrCode = 'QR-' . $this->sku . '-' . date('Ymd');
        
        $this->update(['qr_code' => $qrCode]);
        
        return $qrCode;
    }

    public function createLowStockAlert(): InventoryAlert
    {
        return $this->alerts()->create([
            'alert_type' => 'low_stock',
            'severity' => 'warning',
            'title' => 'Low Stock Alert: ' . $this->name,
            'message' => "Product {$this->name} (SKU: {$this->sku}) has low stock. Current: {$this->current_stock}, Minimum: {$this->minimum_stock}",
            'current_value' => $this->current_stock,
            'threshold_value' => $this->minimum_stock,
            'unit' => $this->unit_of_measure,
            'location' => $this->storage_location,
            'triggered_at' => now(),
            'created_by' => auth()->id(),
        ]);
    }

    public function createExpiryAlert(): InventoryAlert
    {
        $daysToExpiry = $this->getDaysToExpiry();
        
        return $this->alerts()->create([
            'alert_type' => 'expiring_soon',
            'severity' => $daysToExpiry <= 7 ? 'critical' : 'warning',
            'title' => 'Expiry Alert: ' . $this->name,
            'message' => "Product {$this->name} (SKU: {$this->sku}) expires in {$daysToExpiry} days",
            'current_value' => $daysToExpiry,
            'threshold_value' => 30,
            'unit' => 'days',
            'triggered_at' => now(),
            'created_by' => auth()->id(),
        ]);
    }

    public function getRecentTransactions(int $limit = 10): HasMany
    {
        return $this->transactions()->latest()->limit($limit);
    }

    public function getStockHistory(int $days = 30): HasMany
    {
        return $this->transactions()
                    ->where('transaction_date', '>=', now()->subDays($days))
                    ->orderBy('transaction_date');
    }

    public function calculateMonthlyUsage(int $months = 3): float
    {
        $endDate = now();
        $startDate = now()->subMonths($months);
        
        $usage = $this->transactions()
                    ->where('transaction_type', 'consumption')
                    ->where('transaction_date', '>=', $startDate)
                    ->where('transaction_date', '<=', $endDate)
                    ->sum('quantity');
        
        return abs($usage) / $months;
    }

    public function updateAverageMonthlyUsage(): void
    {
        $averageUsage = $this->calculateMonthlyUsage(3);
        $this->update(['average_monthly_usage' => $averageUsage]);
    }

    public function getRecommendedReorderQuantity(): int
    {
        if ($this->average_monthly_usage <= 0) {
            return $this->reorder_quantity;
        }
        
        // Calculate based on 3 months of usage plus safety stock
        $threeMonthUsage = $this->average_monthly_usage * 3;
        $safetyStock = $this->minimum_stock;
        
        return max($this->reorder_quantity, ceil($threeMonthUsage + $safetyStock));
    }

    public function getStockValue(): array
    {
        return [
            'current_value' => $this->getTotalValue(),
            'reorder_value' => $this->getReorderValue(),
            'unit_cost' => $this->cost_price,
            'total_units' => $this->current_stock,
        ];
    }

    public function getAlertSummary(): array
    {
        return [
            'is_low_stock' => $this->isLowStock(),
            'is_out_of_stock' => $this->isOutOfStock(),
            'is_expired' => $this->isExpired(),
            'is_expiring_soon' => $this->isExpiringSoon(),
            'needs_reorder' => $this->needsReorder(),
            'days_to_expiry' => $this->getDaysToExpiry(),
            'stock_turnover_rate' => $this->getStockTurnoverRate(),
        ];
    }
}
