<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_equipment';

    protected $fillable = [
        'name',
        'sku',
        'description',
        'equipment_type',
        'subcategory',
        'weight_kg',
        'dimensions',
        'color',
        'material',
        'finish',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
        'cost_price',
        'selling_price',
        'currency',
        'features',
        'specifications',
        'is_portable',
        'is_electronic',
        'requires_power',
        'power_requirements',
        'power_consumption',
        'condition',
        'purchase_date',
        'last_maintenance_date',
        'maintenance_interval_days',
        'next_maintenance_due',
        'maintenance_notes',
        'maintenance_cost',
        'warranty_expiry',
        'warranty_provider',
        'warranty_terms',
        'support_contact',
        'manual_location',
        'requires_certification',
        'certification_required',
        'certification_expiry',
        'safety_instructions',
        'safety_features',
        'is_hazardous',
        'hazard_notes',
        'usage_count',
        'last_used_date',
        'average_daily_usage',
        'usage_restrictions',
        'current_location',
        'home_location',
        'warehouse',
        'is_movable',
        'manufacturer',
        'brand',
        'model_number',
        'serial_number',
        'supplier_id',
        'barcode',
        'qr_code',
        'asset_tag',
        'replacement_parts',
        'consumables',
        'status',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'maximum_stock' => 'integer',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_portable' => 'boolean',
        'is_electronic' => 'boolean',
        'requires_power' => 'boolean',
        'purchase_date' => 'date',
        'last_maintenance_date' => 'date',
        'maintenance_interval_days' => 'integer',
        'next_maintenance_due' => 'date',
        'maintenance_cost' => 'decimal:2',
        'warranty_expiry' => 'date',
        'certification_expiry' => 'date',
        'usage_count' => 'integer',
        'last_used_date' => 'date',
        'average_daily_usage' => 'decimal:2',
        'is_movable' => 'boolean',
        'is_hazardous' => 'boolean',
        'features' => 'array',
        'specifications' => 'array',
        'safety_features' => 'array',
        'usage_restrictions' => 'array',
        'replacement_parts' => 'array',
        'consumables' => 'array',
        'attachments' => 'array',
        'dimensions' => 'array',
    ];

    protected $dates = [
        'purchase_date',
        'last_maintenance_date',
        'next_maintenance_due',
        'warranty_expiry',
        'certification_expiry',
        'last_used_date',
    ];

    // Relationships
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
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

    public function scopeByType($query, $type)
    {
        return $query->where('equipment_type', $type);
    }

    public function scopeBySubcategory($query, $subcategory)
    {
        return $query->where('subcategory', $subcategory);
    }

    public function scopeElectronic($query)
    {
        return $query->where('is_electronic', true);
    }

    public function scopePortable($query)
    {
        return $query->where('is_portable', true);
    }

    public function scopeRequiresMaintenance($query)
    {
        return $query->where('next_maintenance_due', '<=', now());
    }

    public function scopeUnderWarranty($query)
    {
        return $query->where('warranty_expiry', '>', now());
    }

    public function scopeRequiresCertification($query)
    {
        return $query->where('requires_certification', true);
    }

    public function scopeCertificationExpired($query)
    {
        return $query->where('certification_expiry', '<', now());
    }

    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('current_location', $location);
    }

    public function scopeByManufacturer($query, $manufacturer)
    {
        return $query->where('manufacturer', $manufacturer);
    }

    public function scopeByBrand($query, $brand)
    {
        return $query->where('brand', $brand);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock');
    }

    // Methods
    public function getDimensions(): array
    {
        return $this->dimensions ?: [];
    }

    public function getLength(): ?float
    {
        return $this->getDimensions()['length'] ?? null;
    }

    public function getWidth(): ?float
    {
        return $this->getDimensions()['width'] ?? null;
    }

    public function getHeight(): ?float
    {
        return $this->getDimensions()['height'] ?? null;
    }

    public function getDimensionUnit(): ?string
    {
        return $this->getDimensions()['unit'] ?? 'cm';
    }

    public function getTotalValue(): float
    {
        return $this->current_stock * $this->cost_price;
    }

    public function getDepreciatedValue(): float
    {
        // Simple straight-line depreciation over 5 years
        $yearsSincePurchase = $this->purchase_date ? $this->purchase_date->diffInYears(now()) : 0;
        $depreciationRate = min($yearsSincePurchase / 5, 1);
        
        return $this->getTotalValue() * (1 - $depreciationRate);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function isUnderMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    public function needsMaintenance(): bool
    {
        return $this->next_maintenance_due && $this->next_maintenance_due->isPast();
    }

    public function isUnderWarranty(): bool
    {
        return $this->warranty_expiry && $this->warranty_expiry->isFuture();
    }

    public function isCertificationExpired(): bool
    {
        return $this->certification_expiry && $this->certification_expiry->isPast();
    }

    public function isCertificationExpiringSoon(int $days = 30): bool
    {
        return $this->certification_expiry && 
               $this->certification_expiry->isFuture() && 
               $this->certification_expiry->diffInDays(now()) <= $days;
    }

    public function getDaysToMaintenance(): ?int
    {
        if (!$this->next_maintenance_due) {
            return null;
        }
        
        if ($this->next_maintenance_due->isPast()) {
            return 0;
        }
        
        return $this->next_maintenance_due->diffInDays(now());
    }

    public function getDaysToWarrantyExpiry(): ?int
    {
        if (!$this->warranty_expiry) {
            return null;
        }
        
        if ($this->warranty_expiry->isPast()) {
            return 0;
        }
        
        return $this->warranty_expiry->diffInDays(now());
    }

    public function getDaysToCertificationExpiry(): ?int
    {
        if (!$this->certification_expiry) {
            return null;
        }
        
        if ($this->certification_expiry->isPast()) {
            return 0;
        }
        
        return $this->certification_expiry->diffInDays(now());
    }

    public function getAgeInYears(): ?int
    {
        if (!$this->purchase_date) {
            return null;
        }
        
        return $this->purchase_date->diffInYears(now());
    }

    public function getAgeInDays(): ?int
    {
        if (!$this->purchase_date) {
            return null;
        }
        
        return $this->purchase_date->diffInDays(now());
    }

    public function getConditionDisplay(): string
    {
        return match($this->condition) {
            'new' => 'New',
            'excellent' => 'Excellent',
            'good' => 'Good',
            'fair' => 'Fair',
            'poor' => 'Poor',
            'needs_repair' => 'Needs Repair',
            'out_of_service' => 'Out of Service',
            default => ucfirst($this->condition)
        };
    }

    public function getEquipmentTypeDisplay(): string
    {
        return match($this->equipment_type) {
            'massage_table' => 'Massage Table',
            'chair' => 'Chair',
            'heating_device' => 'Heating Device',
            'cooling_device' => 'Cooling Device',
            'exercise_equipment' => 'Exercise Equipment',
            'diagnostic_tool' => 'Diagnostic Tool',
            'storage_unit' => 'Storage Unit',
            'furniture' => 'Furniture',
            'electronic_device' => 'Electronic Device',
            'tool' => 'Tool',
            default => ucfirst($this->equipment_type)
        };
    }

    public function getStatusDisplay(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'maintenance' => 'Under Maintenance',
            'repair' => 'Under Repair',
            'retired' => 'Retired',
            'lost' => 'Lost',
            'stolen' => 'Stolen',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'maintenance' => 'warning',
            'repair' => 'danger',
            'retired' => 'info',
            'lost' => 'danger',
            'stolen' => 'danger',
            default => 'secondary'
        };
    }

    public function getConditionColor(): string
    {
        return match($this->condition) {
            'new' => 'success',
            'excellent' => 'success',
            'good' => 'info',
            'fair' => 'warning',
            'poor' => 'danger',
            'needs_repair' => 'danger',
            'out_of_service' => 'secondary',
            default => 'secondary'
        };
    }

    public function getFeaturesList(): array
    {
        return $this->features ?: [];
    }

    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->getFeaturesList());
    }

    public function getSpecificationsList(): array
    {
        return $this->specifications ?: [];
    }

    public function getSafetyFeaturesList(): array
    {
        return $this->safety_features ?: [];
    }

    public function hasSafetyFeature(string $feature): bool
    {
        return in_array($feature, $this->getSafetyFeaturesList());
    }

    public function getReplacementPartsList(): array
    {
        return $this->replacement_parts ?: [];
    }

    public function getConsumablesList(): array
    {
        return $this->consumables ?: [];
    }

    public function getUsageRestrictionsList(): array
    {
        return $this->usage_restrictions ?: [];
    }

    public function hasUsageRestriction(string $restriction): bool
    {
        return in_array($restriction, $this->getUsageRestrictionsList());
    }

    public function isHazardous(): bool
    {
        return $this->is_hazardous;
    }

    public function requiresPower(): bool
    {
        return $this->requires_power;
    }

    public function isElectronic(): bool
    {
        return $this->is_electronic;
    }

    public function isPortable(): bool
    {
        return $this->is_portable;
    }

    public function isMovable(): bool
    {
        return $this->is_movable;
    }

    public function canBeUsedBy(string $userType): bool
    {
        $restrictions = $this->getUsageRestrictionsList();
        return empty($restrictions) || in_array($userType, $restrictions);
    }

    public function recordUsage(int $count = 1): void
    {
        $this->increment('usage_count', $count);
        $this->update(['last_used_date' => now()]);
        
        // Update average daily usage
        $this->updateAverageDailyUsage();
    }

    public function updateAverageDailyUsage(): void
    {
        if (!$this->purchase_date) {
            return;
        }
        
        $daysSincePurchase = $this->purchase_date->diffInDays(now());
        if ($daysSincePurchase > 0) {
            $averageUsage = $this->usage_count / $daysSincePurchase;
            $this->update(['average_daily_usage' => $averageUsage]);
        }
    }

    public function scheduleMaintenance(?int $daysFromNow = null): void
    {
        $interval = $daysFromNow ?? $this->maintenance_interval_days;
        if ($interval) {
            $nextMaintenance = now()->addDays($interval);
            $this->update(['next_maintenance_due' => $nextMaintenance]);
        }
    }

    public function completeMaintenance(array $maintenanceData = []): void
    {
        $this->update([
            'last_maintenance_date' => now(),
            'next_maintenance_due' => now()->addDays($this->maintenance_interval_days),
            'status' => 'active',
            'condition' => $maintenanceData['condition'] ?? 'good',
            'maintenance_notes' => $maintenanceData['notes'] ?? null,
            'maintenance_cost' => $maintenanceData['cost'] ?? null,
        ]);
        
        // Create maintenance transaction
        $this->transactions()->create([
            'transaction_type' => 'adjustment',
            'sub_type' => 'maintenance',
            'quantity' => 0,
            'stock_before' => $this->current_stock,
            'stock_after' => $this->current_stock,
            'unit_of_measure' => 'units',
            'transaction_date' => now(),
            'reason' => 'Scheduled maintenance completed',
            'created_by' => auth()->id(),
        ]);
    }

    public function moveToLocation(string $location): void
    {
        $this->update(['current_location' => $location]);
        
        // Create location transfer transaction
        $this->transactions()->create([
            'transaction_type' => 'transfer',
            'sub_type' => 'location_change',
            'quantity' => 0,
            'location_to' => $location,
            'stock_before' => $this->current_stock,
            'stock_after' => $this->current_stock,
            'unit_of_measure' => 'units',
            'transaction_date' => now(),
            'reason' => "Equipment moved to {$location}",
            'created_by' => auth()->id(),
        ]);
    }

    public function retire(): void
    {
        $this->update(['status' => 'retired']);
        
        // Create retirement transaction
        $this->transactions()->create([
            'transaction_type' => 'adjustment',
            'sub_type' => 'retirement',
            'quantity' => 0,
            'stock_before' => $this->current_stock,
            'stock_after' => $this->current_stock,
            'unit_of_measure' => 'units',
            'transaction_date' => now(),
            'reason' => 'Equipment retired from service',
            'created_by' => auth()->id(),
        ]);
    }

    public function createMaintenanceAlert(): InventoryAlert
    {
        return $this->alerts()->create([
            'alert_type' => 'maintenance_due',
            'severity' => 'warning',
            'title' => 'Maintenance Due: ' . $this->name,
            'message' => "Equipment {$this->name} (SKU: {$this->sku}) requires maintenance. Due: {$this->next_maintenance_due->format('Y-m-d')}",
            'current_value' => $this->getDaysToMaintenance(),
            'threshold_value' => 0,
            'unit' => 'days',
            'location' => $this->current_location,
            'triggered_at' => now(),
            'created_by' => auth()->id(),
        ]);
    }

    public function createWarrantyExpiryAlert(): InventoryAlert
    {
        return $this->alerts()->create([
            'alert_type' => 'warranty_expiry',
            'severity' => 'info',
            'title' => 'Warranty Expiring: ' . $this->name,
            'message' => "Equipment {$this->name} (SKU: {$this->sku}) warranty expires on {$this->warranty_expiry->format('Y-m-d')}",
            'current_value' => $this->getDaysToWarrantyExpiry(),
            'threshold_value' => 30,
            'unit' => 'days',
            'triggered_at' => now(),
            'created_by' => auth()->id(),
        ]);
    }

    public function createCertificationExpiryAlert(): InventoryAlert
    {
        return $this->alerts()->create([
            'alert_type' => 'certification_expiry',
            'severity' => 'warning',
            'title' => 'Certification Expiring: ' . $this->name,
            'message' => "Equipment {$this->name} (SKU: {$this->sku}) certification expires on {$this->certification_expiry->format('Y-m-d')}",
            'current_value' => $this->getDaysToCertificationExpiry(),
            'threshold_value' => 30,
            'unit' => 'days',
            'triggered_at' => now(),
            'created_by' => auth()->id(),
        ]);
    }

    public function getUtilizationRate(): float
    {
        if (!$this->purchase_date) {
            return 0;
        }
        
        $daysSincePurchase = $this->purchase_date->diffInDays(now());
        if ($daysSincePurchase === 0) {
            return 0;
        }
        
        // Calculate based on average daily usage vs expected usage
        // This is a simplified calculation - you might want to adjust based on your specific needs
        $expectedDailyUsage = 1; // Assume 1 usage per day as baseline
        return min(($this->average_daily_usage / $expectedDailyUsage) * 100, 100);
    }

    public function getMaintenanceHistory(int $limit = 10): HasMany
    {
        return $this->transactions()
                    ->where('sub_type', 'maintenance')
                    ->latest()
                    ->limit($limit);
    }

    public function getUsageHistory(int $days = 30): HasMany
    {
        return $this->transactions()
                    ->where('transaction_type', 'consumption')
                    ->where('transaction_date', '>=', now()->subDays($days))
                    ->orderBy('transaction_date');
    }

    public function getSummary(): array
    {
        return [
            'name' => $this->name,
            'sku' => $this->sku,
            'equipment_type' => $this->getEquipmentTypeDisplay(),
            'manufacturer' => $this->manufacturer,
            'brand' => $this->brand,
            'model_number' => $this->model_number,
            'serial_number' => $this->serial_number,
            'current_stock' => $this->current_stock,
            'condition' => $this->getConditionDisplay(),
            'status' => $this->getStatusDisplay(),
            'current_location' => $this->current_location,
            'cost_price' => $this->cost_price,
            'total_value' => $this->getTotalValue(),
            'depreciated_value' => $this->getDepreciatedValue(),
            'age_years' => $this->getAgeInYears(),
            'usage_count' => $this->usage_count,
            'average_daily_usage' => $this->average_daily_usage,
            'utilization_rate' => $this->getUtilizationRate(),
            'days_to_maintenance' => $this->getDaysToMaintenance(),
            'days_to_warranty_expiry' => $this->getDaysToWarrantyExpiry(),
            'is_under_warranty' => $this->isUnderWarranty(),
            'requires_maintenance' => $this->needsMaintenance(),
            'is_electronic' => $this->is_electronic(),
            'is_portable' => $this->is_portable(),
        ];
    }
}
