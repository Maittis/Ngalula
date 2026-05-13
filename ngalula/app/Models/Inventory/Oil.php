<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Oil extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_oils';

    protected $fillable = [
        'name',
        'sku',
        'description',
        'oil_type',
        'botanical_name',
        'common_names',
        'origin',
        'extraction_method',
        'volume_ml',
        'volume_liters',
        'density',
        'viscosity',
        'color',
        'aroma_profile',
        'current_stock',
        'minimum_stock',
        'container_size_ml',
        'container_type',
        'cost_price',
        'selling_price_per_ml',
        'selling_price_per_bottle',
        'currency',
        'grade',
        'is_organic',
        'is_wildcrafted',
        'is_pure',
        'is_diluted',
        'certification',
        'uses',
        'contraindications',
        'safety_notes',
        'blending_notes',
        'therapeutic_properties',
        'requires_refrigeration',
        'protect_from_light',
        'storage_temperature',
        'expiry_date',
        'manufacture_date',
        'shelf_life_months',
        'opened_date',
        'shelf_life_after_opening_months',
        'batch_number',
        'lot_number',
        'manufacturer',
        'brand',
        'supplier_id',
        'storage_location',
        'warehouse',
        'barcode',
        'qr_code',
        'average_monthly_usage_ml',
        'last_used_date',
        'last_restocked_date',
        'status',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'volume_ml' => 'decimal:2',
        'volume_liters' => 'decimal:2',
        'density' => 'decimal:4',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'container_size_ml' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'selling_price_per_ml' => 'decimal:2',
        'selling_price_per_bottle' => 'decimal:2',
        'is_organic' => 'boolean',
        'is_wildcrafted' => 'boolean',
        'is_pure' => 'boolean',
        'is_diluted' => 'boolean',
        'requires_refrigeration' => 'boolean',
        'protect_from_light' => 'boolean',
        'expiry_date' => 'date',
        'manufacture_date' => 'date',
        'opened_date' => 'date',
        'shelf_life_months' => 'integer',
        'shelf_life_after_opening_months' => 'integer',
        'last_used_date' => 'date',
        'last_restocked_date' => 'date',
        'average_monthly_usage_ml' => 'decimal:2',
        'therapeutic_properties' => 'array',
        'uses' => 'array',
        'contraindications' => 'array',
        'blending_notes' => 'array',
        'attachments' => 'array',
    ];

    protected $dates = [
        'expiry_date',
        'manufacture_date',
        'opened_date',
        'last_used_date',
        'last_restocked_date',
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

    public function scopeEssential($query)
    {
        return $query->where('oil_type', 'essential');
    }

    public function scopeCarrier($query)
    {
        return $query->where('oil_type', 'carrier');
    }

    public function scopeMassage($query)
    {
        return $query->where('oil_type', 'massage');
    }

    public function scopeOrganic($query)
    {
        return $query->where('is_organic', true);
    }

    public function scopeWildcrafted($query)
    {
        return $query->where('is_wildcrafted', true);
    }

    public function scopePure($query)
    {
        return $query->where('is_pure', true);
    }

    public function scopeByOrigin($query, $origin)
    {
        return $query->where('origin', $origin);
    }

    public function scopeByExtractionMethod($query, $method)
    {
        return $query->where('extraction_method', $method);
    }

    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now());
    }

    // Methods
    public function getTotalVolume(): float
    {
        return $this->volume_ml + ($this->volume_liters * 1000);
    }

    public function getTotalVolumeInLiters(): float
    {
        return $this->getTotalVolume() / 1000;
    }

    public function getVolumePerContainer(): float
    {
        return $this->container_size_ml;
    }

    public function getTotalContainers(): int
    {
        return $this->current_stock;
    }

    public function getTotalVolumeInStock(): float
    {
        return $this->current_stock * $this->container_size_ml;
    }

    public function getTotalVolumeInStockLiters(): float
    {
        return $this->getTotalVolumeInStock() / 1000;
    }

    public function getValuePerMl(): float
    {
        return $this->selling_price_per_ml;
    }

    public function getValuePerBottle(): float
    {
        return $this->selling_price_per_bottle;
    }

    public function getTotalValue(): float
    {
        return $this->getTotalVolumeInStock() * $this->selling_price_per_ml;
    }

    public function getCostPerMl(): float
    {
        return $this->cost_price / $this->container_size_ml;
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
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

    public function isOpened(): bool
    {
        return $this->opened_date !== null;
    }

    public function getDaysSinceOpened(): ?int
    {
        if (!$this->opened_date) {
            return null;
        }
        
        return $this->opened_date->diffInDays(now());
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

    public function getShelfLifeRemaining(): ?int
    {
        if (!$this->opened_date || !$this->shelf_life_after_opening_months) {
            return $this->getDaysToExpiry();
        }
        
        $expiryDate = $this->opened_date->addMonths($this->shelf_life_after_opening_months);
        return max(0, $expiryDate->diffInDays(now()));
    }

    public function getStockStatus(): string
    {
        if ($this->isExpired()) {
            return 'expired';
        } elseif ($this->isLowStock()) {
            return 'low_stock';
        } elseif ($this->isExpiringSoon()) {
            return 'expiring_soon';
        } elseif ($this->current_stock <= 0) {
            return 'out_of_stock';
        } else {
            return 'normal';
        }
    }

    public function getStockStatusColor(): string
    {
        return match($this->getStockStatus()) {
            'expired' => 'danger',
            'low_stock' => 'warning',
            'expiring_soon' => 'warning',
            'out_of_stock' => 'danger',
            default => 'success'
        };
    }

    public function getQualityGrade(): string
    {
        return match($this->grade) {
            'therapeutic' => 'Therapeutic Grade',
            'cosmetic' => 'Cosmetic Grade',
            'industrial' => 'Industrial Grade',
            'food_grade' => 'Food Grade',
            default => 'Standard'
        };
    }

    public function getExtractionMethodDisplay(): string
    {
        return match($this->extraction_method) {
            'cold_pressed' => 'Cold Pressed',
            'steam_distilled' => 'Steam Distilled',
            'solvent_extracted' => 'Solvent Extracted',
            'co2_extracted' => 'CO2 Extracted',
            'enfleurage' => 'Enfleurage',
            default => $this->extraction_method
        };
    }

    public function getOilTypeDisplay(): string
    {
        return match($this->oil_type) {
            'essential' => 'Essential Oil',
            'carrier' => 'Carrier Oil',
            'massage' => 'Massage Oil',
            'aromatherapy' => 'Aromatherapy Oil',
            'therapeutic' => 'Therapeutic Oil',
            default => ucfirst($this->oil_type)
        };
    }

    public function getContainerTypeDisplay(): string
    {
        return match($this->container_type) {
            'bottle' => 'Bottle',
            'dropper' => 'Dropper Bottle',
            'spray' => 'Spray Bottle',
            'jug' => 'Jug',
            'drum' => 'Drum',
            default => ucfirst($this->container_type)
        };
    }

    public function getStorageRequirements(): array
    {
        $requirements = [];
        
        if ($this->requires_refrigeration) {
            $requirements[] = 'refrigeration';
        }
        
        if ($this->protect_from_light) {
            $requirements[] = 'dark_storage';
        }
        
        if ($this->storage_temperature) {
            $requirements[] = $this->storage_temperature . ' temperature';
        }
        
        return $requirements;
    }

    public function getCertifications(): array
    {
        if (!$this->certification) {
            return [];
        }
        
        return explode(',', $this->certification);
    }

    public function hasCertification(string $certification): bool
    {
        return in_array($certification, $this->getCertifications());
    }

    public function isTherapeuticGrade(): bool
    {
        return $this->grade === 'therapeutic';
    }

    public function isFoodGrade(): bool
    {
        return $this->grade === 'food_grade';
    }

    public function isSuitableForAromatherapy(): bool
    {
        return $this->oil_type === 'essential' && $this->is_pure && !$this->is_diluted;
    }

    public function isSuitableForMassage(): bool
    {
        return in_array($this->oil_type, ['massage', 'carrier']) || 
               ($this->oil_type === 'essential' && !$this->is_diluted);
    }

    public function addVolume(float $volumeMl, array $transactionData = []): InventoryTransaction
    {
        $containersToAdd = ceil($volumeMl / $this->container_size_ml);
        
        $this->increment('current_stock', $containersToAdd);
        $this->increment('volume_ml', $volumeMl);
        $this->update(['last_restocked_date' => now()]);
        
        return $this->transactions()->create(array_merge([
            'transaction_type' => 'purchase',
            'sub_type' => 'restock',
            'quantity' => $containersToAdd,
            'stock_before' => $this->current_stock - $containersToAdd,
            'stock_after' => $this->current_stock,
            'unit_of_measure' => 'bottles',
            'unit_cost' => $this->cost_price,
            'total_cost' => $containersToAdd * $this->cost_price,
            'transaction_date' => now(),
            'created_by' => auth()->id(),
        ], $transactionData));
    }

    public function removeVolume(float $volumeMl, array $transactionData = []): InventoryTransaction
    {
        $containersToRemove = ceil($volumeMl / $this->container_size_ml);
        
        if ($containersToRemove > $this->current_stock) {
            throw new \Exception("Insufficient stock. Available: {$this->current_stock} bottles, Requested: {$containersToRemove} bottles");
        }
        
        if ($volumeMl > $this->volume_ml) {
            throw new \Exception("Insufficient volume. Available: {$this->volume_ml}ml, Requested: {$volumeMl}ml");
        }
        
        $this->decrement('current_stock', $containersToRemove);
        $this->decrement('volume_ml', $volumeMl);
        $this->update(['last_used_date' => now()]);
        
        return $this->transactions()->create(array_merge([
            'transaction_type' => 'consumption',
            'sub_type' => 'usage',
            'quantity' => -$containersToRemove,
            'stock_before' => $this->current_stock + $containersToRemove,
            'stock_after' => $this->current_stock,
            'unit_of_measure' => 'bottles',
            'unit_cost' => $this->cost_price,
            'total_cost' => $containersToRemove * $this->cost_price,
            'transaction_date' => now(),
            'created_by' => auth()->id(),
        ], $transactionData));
    }

    public function openContainer(): void
    {
        if (!$this->opened_date) {
            $this->update(['opened_date' => now()]);
        }
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
        $this->update(['average_monthly_usage_ml' => $averageUsage * $this->container_size_ml]);
    }

    public function getBlendingCompatibility(): array
    {
        // This would typically be based on aromatherapy knowledge
        // For now, return a basic implementation
        $compatibleTypes = [];
        
        if ($this->oil_type === 'essential') {
            $compatibleTypes = ['carrier', 'massage'];
        } elseif ($this->oil_type === 'carrier') {
            $compatibleTypes = ['essential'];
        } elseif ($this->oil_type === 'massage') {
            $compatibleTypes = ['essential'];
        }
        
        return $compatibleTypes;
    }

    public function canBlendWith(Oil $otherOil): bool
    {
        return in_array($otherOil->oil_type, $this->getBlendingCompatibility());
    }

    public function getSafetyProfile(): array
    {
        return [
            'is_pure' => $this->is_pure,
            'is_diluted' => $this->is_diluted,
            'grade' => $this->grade,
            'contraindications' => $this->contraindications,
            'safety_notes' => $this->safety_notes,
            'is_hazardous' => false, // Essential oils are generally not hazardous when used properly
        ];
    }

    public function getTherapeuticPropertiesList(): array
    {
        return $this->therapeutic_properties ?: [];
    }

    public function hasTherapeuticProperty(string $property): bool
    {
        return in_array($property, $this->getTherapeuticPropertiesList());
    }

    public function getUsageInstructions(): string
    {
        if ($this->oil_type === 'essential') {
            return "Use 2-3 drops per 10ml of carrier oil for topical application. Always dilute properly.";
        } elseif ($this->oil_type === 'carrier') {
            return "Can be used directly on skin or as a base for essential oils.";
        } elseif ($this->oil_type === 'massage') {
            return "Apply directly to skin for massage. Can be warmed for better absorption.";
        }
        
        return "Follow manufacturer instructions for safe use.";
    }

    public function createLowStockAlert(): InventoryAlert
    {
        return $this->alerts()->create([
            'alert_type' => 'low_stock',
            'severity' => 'warning',
            'title' => 'Low Stock Alert: ' . $this->name,
            'message' => "Oil {$this->name} (SKU: {$this->sku}) has low stock. Current: {$this->current_stock} bottles, Minimum: {$this->minimum_stock} bottles",
            'current_value' => $this->current_stock,
            'threshold_value' => $this->minimum_stock,
            'unit' => 'bottles',
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
            'message' => "Oil {$this->name} (SKU: {$this->sku}) expires in {$daysToExpiry} days",
            'current_value' => $daysToExpiry,
            'threshold_value' => 30,
            'unit' => 'days',
            'triggered_at' => now(),
            'created_by' => auth()->id(),
        ]);
    }

    public function getSummary(): array
    {
        return [
            'name' => $this->name,
            'sku' => $this->sku,
            'oil_type' => $this->getOilTypeDisplay(),
            'botanical_name' => $this->botanical_name,
            'origin' => $this->origin,
            'current_stock' => $this->current_stock,
            'total_volume_ml' => $this->getTotalVolumeInStock(),
            'total_volume_liters' => $this->getTotalVolumeInStockLiters(),
            'cost_price' => $this->cost_price,
            'selling_price_per_ml' => $this->selling_price_per_ml,
            'total_value' => $this->getTotalValue(),
            'grade' => $this->getQualityGrade(),
            'is_organic' => $this->is_organic,
            'is_pure' => $this->is_pure,
            'stock_status' => $this->getStockStatus(),
            'days_to_expiry' => $this->getDaysToExpiry(),
            'storage_requirements' => $this->getStorageRequirements(),
        ];
    }
}
