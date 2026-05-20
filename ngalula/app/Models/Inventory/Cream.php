<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cream extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_creams';

    protected $fillable = [
        'name',
        'sku',
        'description',
        'cream_type',
        'skin_type',
        'consistency',
        'weight_grams',
        'volume_ml',
        'density',
        'color',
        'texture',
        'scent',
        'current_stock',
        'minimum_stock',
        'container_size_grams',
        'container_size_ml',
        'container_type',
        'cost_price',
        'selling_price_per_gram',
        'selling_price_per_ml',
        'selling_price_per_unit',
        'currency',
        'ingredients',
        'active_ingredients',
        'allergens',
        'is_hypoallergenic',
        'is_dermatologically_tested',
        'is_cruelty_free',
        'is_vegan',
        'is_organic',
        'grade',
        'certification',
        'regulatory_number',
        'uses',
        'application_instructions',
        'contraindications',
        'side_effects',
        'storage_instructions',
        'therapeutic_properties',
        'age_group',
        'target_group',
        'requires_refrigeration',
        'protect_from_light',
        'protect_from_heat',
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
        'average_monthly_usage_grams',
        'last_used_date',
        'last_restocked_date',
        'status',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'weight_grams' => 'decimal:2',
        'volume_ml' => 'decimal:2',
        'density' => 'decimal:4',
        'current_stock' => 'integer',
        'minimum_stock' => 'integer',
        'container_size_grams' => 'decimal:2',
        'container_size_ml' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'selling_price_per_gram' => 'decimal:2',
        'selling_price_per_ml' => 'decimal:2',
        'selling_price_per_unit' => 'decimal:2',
        'is_hypoallergenic' => 'boolean',
        'is_dermatologically_tested' => 'boolean',
        'is_cruelty_free' => 'boolean',
        'is_vegan' => 'boolean',
        'is_organic' => 'boolean',
        'expiry_date' => 'date',
        'manufacture_date' => 'date',
        'opened_date' => 'date',
        'shelf_life_months' => 'integer',
        'shelf_life_after_opening_months' => 'integer',
        'last_used_date' => 'date',
        'last_restocked_date' => 'date',
        'average_monthly_usage_grams' => 'decimal:2',
        'ingredients' => 'array',
        'active_ingredients' => 'array',
        'allergens' => 'array',
        'therapeutic_properties' => 'array',
        'uses' => 'array',
        'contraindications' => 'array',
        'side_effects' => 'array',
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

    public function scopeMoisturizer($query)
    {
        return $query->where('cream_type', 'moisturizer');
    }

    public function scopeMassageCream($query)
    {
        return $query->where('cream_type', 'massage_cream');
    }

    public function scopeTherapeutic($query)
    {
        return $query->where('cream_type', 'therapeutic');
    }

    public function scopeSunscreen($query)
    {
        return $query->where('cream_type', 'sunscreen');
    }

    public function scopeMedicated($query)
    {
        return $query->where('cream_type', 'medicated');
    }

    public function scopeCosmetic($query)
    {
        return $query->where('cream_type', 'cosmetic');
    }

    public function scopeOrganic($query)
    {
        return $query->where('is_organic', true);
    }

    public function scopeVegan($query)
    {
        return $query->where('is_vegan', true);
    }

    public function scopeHypoallergenic($query)
    {
        return $query->where('is_hypoallergenic', true);
    }

    public function scopeForSkinType($query, $skinType)
    {
        return $query->where('skin_type', $skinType);
    }

    public function scopeByConsistency($query, $consistency)
    {
        return $query->where('consistency', $consistency);
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
    public function getTotalWeight(): float
    {
        return $this->current_stock * $this->container_size_grams;
    }

    public function getTotalVolume(): float
    {
        return $this->current_stock * ($this->container_size_ml ?: ($this->container_size_grams / $this->density));
    }

    public function getValuePerGram(): float
    {
        return $this->selling_price_per_gram;
    }

    public function getValuePerMl(): float
    {
        return $this->selling_price_per_ml;
    }

    public function getValuePerUnit(): float
    {
        return $this->selling_price_per_unit;
    }

    public function getTotalValue(): float
    {
        return $this->getTotalWeight() * $this->selling_price_per_gram;
    }

    public function getCostPerGram(): float
    {
        return $this->cost_price / $this->container_size_grams;
    }

    public function getCostPerMl(): float
    {
        return $this->cost_price / ($this->container_size_ml ?: ($this->container_size_grams / $this->density));
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

    public function getCreamTypeDisplay(): string
    {
        return match($this->cream_type) {
            'moisturizer' => 'Moisturizer',
            'massage_cream' => 'Massage Cream',
            'therapeutic' => 'Therapeutic Cream',
            'sunscreen' => 'Sunscreen',
            'medicated' => 'Medicated Cream',
            'cosmetic' => 'Cosmetic Cream',
            default => ucfirst($this->cream_type)
        };
    }

    public function getSkinTypeDisplay(): string
    {
        return match($this->skin_type) {
            'all' => 'All Skin Types',
            'dry' => 'Dry Skin',
            'oily' => 'Oily Skin',
            'combination' => 'Combination Skin',
            'sensitive' => 'Sensitive Skin',
            'mature' => 'Mature Skin',
            'acne_prone' => 'Acne-Prone Skin',
            default => ucfirst($this->skin_type)
        };
    }

    public function getConsistencyDisplay(): string
    {
        return match($this->consistency) {
            'light' => 'Light',
            'medium' => 'Medium',
            'heavy' => 'Heavy',
            'gel' => 'Gel',
            'lotion' => 'Lotion',
            'cream' => 'Cream',
            'ointment' => 'Ointment',
            default => ucfirst($this->consistency)
        };
    }

    public function getContainerTypeDisplay(): string
    {
        return match($this->container_type) {
            'tube' => 'Tube',
            'jar' => 'Jar',
            'pump' => 'Pump Bottle',
            'bottle' => 'Bottle',
            'spray' => 'Spray Bottle',
            'tub' => 'Tub',
            default => ucfirst($this->container_type)
        };
    }

    public function getGradeDisplay(): string
    {
        return match($this->grade) {
            'cosmetic' => 'Cosmetic Grade',
            'pharmaceutical' => 'Pharmaceutical Grade',
            'therapeutic' => 'Therapeutic Grade',
            'medical' => 'Medical Grade',
            default => ucfirst($this->grade)
        };
    }

    public function getAgeGroupDisplay(): string
    {
        return match($this->age_group) {
            'all' => 'All Ages',
            'infant' => 'Infant',
            'child' => 'Child',
            'teen' => 'Teen',
            'adult' => 'Adult',
            'senior' => 'Senior',
            default => ucfirst($this->age_group)
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
        
        if ($this->protect_from_heat) {
            $requirements[] = 'cool_storage';
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

    public function isPharmaceuticalGrade(): bool
    {
        return $this->grade === 'pharmaceutical';
    }

    public function isMedicalGrade(): bool
    {
        return $this->grade === 'medical';
    }

    public function isTherapeuticGrade(): bool
    {
        return $this->grade === 'therapeutic';
    }

    public function isSuitableForSensitiveSkin(): bool
    {
        return $this->is_hypoallergenic || $this->skin_type === 'sensitive' || $this->skin_type === 'all';
    }

    public function isSuitableForAllSkinTypes(): bool
    {
        return $this->skin_type === 'all';
    }

    public function isSuitableForAgeGroup(string $ageGroup): bool
    {
        return $this->age_group === 'all' || $this->age_group === $ageGroup;
    }

    public function addWeight(float $weightGrams, array $transactionData = []): InventoryTransaction
    {
        $containersToAdd = ceil($weightGrams / $this->container_size_grams);
        
        $this->increment('current_stock', $containersToAdd);
        $this->increment('weight_grams', $weightGrams);
        $this->update(['last_restocked_date' => now()]);
        
        return $this->transactions()->create(array_merge([
            'transaction_type' => 'purchase',
            'sub_type' => 'restock',
            'quantity' => $containersToAdd,
            'stock_before' => $this->current_stock - $containersToAdd,
            'stock_after' => $this->current_stock,
            'unit_of_measure' => 'containers',
            'unit_cost' => $this->cost_price,
            'total_cost' => $containersToAdd * $this->cost_price,
            'transaction_date' => now(),
            'created_by' => auth()->id(),
        ], $transactionData));
    }

    public function removeWeight(float $weightGrams, array $transactionData = []): InventoryTransaction
    {
        $containersToRemove = ceil($weightGrams / $this->container_size_grams);
        
        if ($containersToRemove > $this->current_stock) {
            throw new \Exception("Insufficient stock. Available: {$this->current_stock} containers, Requested: {$containersToRemove} containers");
        }
        
        if ($weightGrams > $this->weight_grams) {
            throw new \Exception("Insufficient weight. Available: {$this->weight_grams}g, Requested: {$weightGrams}g");
        }
        
        $this->decrement('current_stock', $containersToRemove);
        $this->decrement('weight_grams', $weightGrams);
        $this->update(['last_used_date' => now()]);
        
        return $this->transactions()->create(array_merge([
            'transaction_type' => 'consumption',
            'sub_type' => 'usage',
            'quantity' => -$containersToRemove,
            'stock_before' => $this->current_stock + $containersToRemove,
            'stock_after' => $this->current_stock,
            'unit_of_measure' => 'containers',
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
        $this->update(['average_monthly_usage_grams' => $averageUsage * $this->container_size_grams]);
    }

    public function getIngredientList(): array
    {
        return $this->ingredients ?: [];
    }

    public function hasIngredient(string $ingredient): bool
    {
        return in_array($ingredient, $this->getIngredientList());
    }

    public function getActiveIngredientList(): array
    {
        return $this->active_ingredients ?: [];
    }

    public function hasActiveIngredient(string $ingredient): bool
    {
        return in_array($ingredient, $this->getActiveIngredientList());
    }

    public function getAllergenList(): array
    {
        return $this->allergens ?: [];
    }

    public function containsAllergen(string $allergen): bool
    {
        return in_array($allergen, $this->getAllergenList());
    }

    public function isAllergenFree(): bool
    {
        return empty($this->getAllergenList());
    }

    public function getTherapeuticPropertiesList(): array
    {
        return $this->therapeutic_properties ?: [];
    }

    public function hasTherapeuticProperty(string $property): bool
    {
        return in_array($property, $this->getTherapeuticPropertiesList());
    }

    public function getSafetyProfile(): array
    {
        return [
            'is_hypoallergenic' => $this->is_hypoallergenic,
            'is_dermatologically_tested' => $this->is_dermatologically_tested,
            'is_cruelty_free' => $this->is_cruelty_free,
            'is_vegan' => $this->is_vegan,
            'is_organic' => $this->is_organic,
            'grade' => $this->grade,
            'allergens' => $this->getAllergenList(),
            'contraindications' => $this->contraindications,
            'side_effects' => $this->side_effects,
        ];
    }

    public function getApplicationInstructions(): string
    {
        return $this->application_instructions ?? 'Apply to clean, dry skin as needed. Perform patch test before first use.';
    }

    public function getStorageInstructions(): string
    {
        $instructions = [];
        
        if ($this->requires_refrigeration) {
            $instructions[] = 'Store in refrigerator';
        }
        
        if ($this->protect_from_light) {
            $instructions[] = 'Store in dark container';
        }
        
        if ($this->protect_from_heat) {
            $instructions[] = 'Store in cool place';
        }
        
        if ($this->storage_temperature) {
            $instructions[] = 'Store at ' . $this->storage_temperature;
        }
        
        return implode(', ', $instructions) ?: 'Store at room temperature';
    }

    public function createLowStockAlert(): InventoryAlert
    {
        return $this->alerts()->create([
            'alert_type' => 'low_stock',
            'severity' => 'warning',
            'title' => 'Low Stock Alert: ' . $this->name,
            'message' => "Cream {$this->name} (SKU: {$this->sku}) has low stock. Current: {$this->current_stock} containers, Minimum: {$this->minimum_stock} containers",
            'current_value' => $this->current_stock,
            'threshold_value' => $this->minimum_stock,
            'unit' => 'containers',
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
            'message' => "Cream {$this->name} (SKU: {$this->sku}) expires in {$daysToExpiry} days",
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
            'cream_type' => $this->getCreamTypeDisplay(),
            'skin_type' => $this->getSkinTypeDisplay(),
            'consistency' => $this->getConsistencyDisplay(),
            'current_stock' => $this->current_stock,
            'total_weight_grams' => $this->getTotalWeight(),
            'total_volume_ml' => $this->getTotalVolume(),
            'cost_price' => $this->cost_price,
            'selling_price_per_gram' => $this->selling_price_per_gram,
            'total_value' => $this->getTotalValue(),
            'grade' => $this->getGradeDisplay(),
            'is_organic' => $this->is_organic,
            'is_hypoallergenic' => $this->is_hypoallergenic,
            'is_vegan' => $this->is_vegan,
            'stock_status' => $this->getStockStatus(),
            'days_to_expiry' => $this->getDaysToExpiry(),
            'storage_requirements' => $this->getStorageRequirements(),
        ];
    }
}
