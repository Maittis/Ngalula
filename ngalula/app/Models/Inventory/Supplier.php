<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_suppliers';

    protected $fillable = [
        'name',
        'code',
        'description',
        'contact_person',
        'email',
        'phone',
        'fax',
        'website',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'supplier_type',
        'business_registration',
        'tax_id',
        'vat_number',
        'product_categories',
        'specialties',
        'primary_category',
        'currency',
        'payment_terms',
        'payment_methods',
        'bank_account',
        'bank_name',
        'routing_number',
        'swift_code',
        'shipping_methods',
        'default_shipping_method',
        'lead_time_days',
        'minimum_order_quantity',
        'minimum_order_amount',
        'shipping_zones',
        'is_certified',
        'certifications',
        'quality_rating',
        'last_audit_date',
        'next_audit_due',
        'on_time_delivery_rate',
        'quality_score',
        'total_orders',
        'total_order_value',
        'last_order_date',
        'contract_start_date',
        'contract_end_date',
        'contract_status',
        'contract_terms',
        'contract_file_path',
        'preferred_contact_methods',
        'preferred_language',
        'communication_frequency',
        'status',
        'is_preferred',
        'is_primary_supplier',
        'allows_returns',
        'return_policy_days',
        'assigned_buyer',
        'department',
        'first_order_date',
        'internal_notes',
        'tags',
        'attachments',
        'catalog_file_path',
        'price_list_file_path',
        'insurance_certificate_path',
        'erp_supplier_id',
        'accounting_system_id',
        'integration_data',
        'emergency_contact_person',
        'emergency_phone',
        'emergency_email',
    ];

    protected $casts = [
        'lead_time_days' => 'integer',
        'minimum_order_quantity' => 'integer',
        'minimum_order_amount' => 'decimal:2',
        'is_certified' => 'boolean',
        'on_time_delivery_rate' => 'decimal:2',
        'quality_score' => 'decimal:2',
        'total_orders' => 'integer',
        'total_order_value' => 'decimal:2',
        'last_order_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'last_audit_date' => 'date',
        'next_audit_due' => 'date',
        'first_order_date' => 'date',
        'is_preferred' => 'boolean',
        'is_primary_supplier' => 'boolean',
        'allows_returns' => 'boolean',
        'return_policy_days' => 'integer',
        'product_categories' => 'array',
        'specialties' => 'array',
        'certifications' => 'array',
        'shipping_methods' => 'array',
        'shipping_zones' => 'array',
        'preferred_contact_methods' => 'array',
        'tags' => 'array',
        'attachments' => 'array',
        'integration_data' => 'array',
    ];

    protected $dates = [
        'last_order_date',
        'contract_start_date',
        'contract_end_date',
        'last_audit_date',
        'next_audit_due',
        'first_order_date',
    ];

    // Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'primary_supplier_id');
    }

    public function oils(): HasMany
    {
        return $this->hasMany(Oil::class, 'supplier_id');
    }

    public function creams(): HasMany
    {
        return $this->hasMany(Cream::class, 'supplier_id');
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'supplier_id');
    }

    public function purchaseRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequest::class, 'preferred_supplier_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePreferred($query)
    {
        return $query->where('is_preferred', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary_supplier', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('supplier_type', $type);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('primary_category', $category);
    }

    public function scopeCertified($query)
    {
        return $query->where('is_certified', true);
    }

    public function scopeAllowsReturns($query)
    {
        return $query->where('allows_returns', true);
    }

    public function scopeByPaymentTerms($query, $terms)
    {
        return $query->where('payment_terms', $terms);
    }

    public function scopeByQualityRating($query, $rating)
    {
        return $query->where('quality_rating', $rating);
    }

    // Methods
    public function getSupplierTypeDisplay(): string
    {
        return match($this->supplier_type) {
            'manufacturer' => 'Manufacturer',
            'distributor' => 'Distributor',
            'wholesaler' => 'Wholesaler',
            'retailer' => 'Retailer',
            'importer' => 'Importer',
            'exporter' => 'Exporter',
            'local' => 'Local Supplier',
            default => ucfirst($this->supplier_type)
        };
    }

    public function getStatusDisplay(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'suspended' => 'Suspended',
            'blacklisted' => 'Blacklisted',
            'pending_approval' => 'Pending Approval',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'warning',
            'blacklisted' => 'danger',
            'pending_approval' => 'info',
            default => 'secondary'
        };
    }

    public function getPaymentTermsDisplay(): string
    {
        return match($this->payment_terms) {
            'net_15' => 'Net 15 Days',
            'net_30' => 'Net 30 Days',
            'net_60' => 'Net 60 Days',
            'net_90' => 'Net 90 Days',
            'cod' => 'Cash on Delivery',
            'prepaid' => 'Prepaid',
            default => $this->payment_terms
        };
    }

    public function getContractStatusDisplay(): string
    {
        return match($this->contract_status) {
            'active' => 'Active',
            'expired' => 'Expired',
            'terminated' => 'Terminated',
            'pending' => 'Pending',
            default => ucfirst($this->contract_status)
        };
    }

    public function getCommunicationFrequencyDisplay(): string
    {
        return match($this->communication_frequency) {
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'as_needed' => 'As Needed',
            default => ucfirst($this->communication_frequency)
        };
    }

    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country,
        ]);
        
        return implode(', ', $parts);
    }

    public function getProductCategories(): array
    {
        return $this->product_categories ?: [];
    }

    public function getSpecialties(): array
    {
        return $this->specialties ?: [];
    }

    public function getCertifications(): array
    {
        return $this->certifications ?: [];
    }

    public function hasCertification(string $certification): bool
    {
        return in_array($certification, $this->getCertifications());
    }

    public function getShippingMethods(): array
    {
        return $this->shipping_methods ?: [];
    }

    public function getShippingZones(): array
    {
        return $this->shipping_zones ?: [];
    }

    public function getPreferredContactMethods(): array
    {
        return $this->preferred_contact_methods ?: [];
    }

    public function getTags(): array
    {
        return $this->tags ?: [];
    }

    public function getAttachments(): array
    {
        return $this->attachments ?: [];
    }

    public function getIntegrationData(): array
    {
        return $this->integration_data ?: [];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPreferred(): bool
    {
        return $this->is_preferred;
    }

    public function isPrimary(): bool
    {
        return $this->is_primary_supplier;
    }

    public function isCertified(): bool
    {
        return $this->is_certified;
    }

    public function allowsReturns(): bool
    {
        return $this->allows_returns;
    }

    public function hasContract(): bool
    {
        return $this->contract_start_date && $this->contract_end_date;
    }

    public function isContractActive(): bool
    {
        return $this->hasContract() && 
               $this->contract_start_date->isPast() && 
               $this->contract_end_date->isFuture();
    }

    public function isContractExpired(): bool
    {
        return $this->hasContract() && $this->contract_end_date->isPast();
    }

    public function getDaysToContractExpiry(): ?int
    {
        if (!$this->contract_end_date) {
            return null;
        }
        
        if ($this->isContractExpired()) {
            return 0;
        }
        
        return $this->contract_end_date->diffInDays(now());
    }

    public function needsAudit(): bool
    {
        return $this->next_audit_due && $this->next_audit_due->isPast();
    }

    public function getDaysToNextAudit(): ?int
    {
        if (!$this->next_audit_due) {
            return null;
        }
        
        if ($this->needsAudit()) {
            return 0;
        }
        
        return $this->next_audit_due->diffInDays(now());
    }

    public function getPerformanceMetrics(): array
    {
        return [
            'on_time_delivery_rate' => $this->on_time_delivery_rate,
            'quality_score' => $this->quality_score,
            'total_orders' => $this->total_orders,
            'total_order_value' => $this->total_order_value,
            'average_order_value' => $this->total_orders > 0 ? $this->total_order_value / $this->total_orders : 0,
            'last_order_date' => $this->last_order_date,
            'days_since_last_order' => $this->last_order_date ? $this->last_order_date->diffInDays(now()) : null,
        ];
    }

    public function getQualityRating(): string
    {
        return match($this->quality_rating) {
            'A+' => 'A+ (Excellent)',
            'A' => 'A (Very Good)',
            'B' => 'B (Good)',
            'C' => 'C (Fair)',
            default => 'Not Rated'
        };
    }

    public function getQualityRatingColor(): string
    {
        return match($this->quality_rating) {
            'A+' => 'success',
            'A' => 'success',
            'B' => 'info',
            'C' => 'warning',
            default => 'secondary'
        };
    }

    public function getDeliveryPerformance(): string
    {
        if ($this->on_time_delivery_rate >= 95) {
            return 'Excellent';
        } elseif ($this->on_time_delivery_rate >= 90) {
            return 'Very Good';
        } elseif ($this->on_time_delivery_rate >= 80) {
            return 'Good';
        } elseif ($this->on_time_delivery_rate >= 70) {
            return 'Fair';
        } else {
            return 'Poor';
        }
    }

    public function getDeliveryPerformanceColor(): string
    {
        return match($this->getDeliveryPerformance()) {
            'Excellent' => 'success',
            'Very Good' => 'success',
            'Good' => 'info',
            'Fair' => 'warning',
            'Poor' => 'danger',
            default => 'secondary'
        };
    }

    public function canSupplyCategory(string $category): bool
    {
        return in_array($category, $this->getProductCategories()) || 
               $this->primary_category === $category;
    }

    public function hasSpecialty(string $specialty): bool
    {
        return in_array($specialty, $this->getSpecialties());
    }

    public function shipsToZone(string $zone): bool
    {
        return in_array($zone, $this->getShippingZones());
    }

    public function acceptsPaymentMethod(string $method): bool
    {
        $methods = explode(',', $this->payment_methods);
        return in_array($method, $methods);
    }

    public function prefersContactMethod(string $method): bool
    {
        return in_array($method, $this->getPreferredContactMethods());
    }

    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->getTags());
    }

    public function calculateOrderFrequency(): float
    {
        if (!$this->first_order_date || $this->total_orders <= 1) {
            return 0;
        }
        
        $daysSinceFirstOrder = $this->first_order_date->diffInDays(now());
        return $daysSinceFirstOrder > 0 ? $this->total_orders / ($daysSinceFirstOrder / 30) : 0; // Orders per month
    }

    public function getReliabilityScore(): float
    {
        // Calculate a reliability score based on various factors
        $deliveryScore = $this->on_time_delivery_rate / 100;
        $qualityScore = $this->quality_score / 100;
        $frequencyScore = min($this->calculateOrderFrequency() / 10, 1); // Normalize to 0-1
        
        // Weight the scores
        $weights = [
            'delivery' => 0.4,
            'quality' => 0.4,
            'frequency' => 0.2,
        ];
        
        $score = ($deliveryScore * $weights['delivery']) + 
                ($qualityScore * $weights['quality']) + 
                ($frequencyScore * $weights['frequency']);
        
        return round($score * 100, 2);
    }

    public function getReliabilityRating(): string
    {
        $score = $this->getReliabilityScore();
        
        if ($score >= 90) {
            return 'Excellent';
        } elseif ($score >= 80) {
            return 'Very Good';
        } elseif ($score >= 70) {
            return 'Good';
        } elseif ($score >= 60) {
            return 'Fair';
        } else {
            return 'Poor';
        }
    }

    public function getReliabilityColor(): string
    {
        return match($this->getReliabilityRating()) {
            'Excellent' => 'success',
            'Very Good' => 'success',
            'Good' => 'info',
            'Fair' => 'warning',
            'Poor' => 'danger',
            default => 'secondary'
        };
    }

    public function updatePerformanceMetrics(): void
    {
        // This would typically calculate based on actual order data
        // For now, keep existing values
    }

    public function recordOrder(float $orderValue): void
    {
        $this->increment('total_orders');
        $this->increment('total_order_value', $orderValue);
        $this->update(['last_order_date' => now()]);
    }

    public function updateQualityRating(string $rating): void
    {
        $this->update(['quality_rating' => $rating]);
    }

    public function updateDeliveryRate(float $rate): void
    {
        $this->update(['on_time_delivery_rate' => $rate]);
    }

    public function setAsPreferred(bool $preferred = true): void
    {
        $this->update(['is_preferred' => $preferred]);
    }

    public function setAsPrimary(bool $primary = true): void
    {
        $this->update(['is_primary_supplier' => $primary]);
    }

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function blacklist(): void
    {
        $this->update(['status' => 'blacklisted']);
    }

    public function getSummary(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'supplier_type' => $this->getSupplierTypeDisplay(),
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'primary_category' => $this->primary_category,
            'status' => $this->getStatusDisplay(),
            'status_color' => $this->getStatusColor(),
            'is_preferred' => $this->is_preferred,
            'is_primary_supplier' => $this->is_primary_supplier,
            'is_certified' => $this->is_certified,
            'payment_terms' => $this->getPaymentTermsDisplay(),
            'lead_time_days' => $this->lead_time_days,
            'minimum_order_quantity' => $this->minimum_order_quantity,
            'minimum_order_amount' => $this->minimum_order_amount,
            'allows_returns' => $this->allows_returns,
            'return_policy_days' => $this->return_policy_days,
            'performance_metrics' => $this->getPerformanceMetrics(),
            'quality_rating' => $this->getQualityRating(),
            'quality_rating_color' => $this->getQualityRatingColor(),
            'delivery_performance' => $this->getDeliveryPerformance(),
            'delivery_performance_color' => $this->getDeliveryPerformanceColor(),
            'reliability_score' => $this->getReliabilityScore(),
            'reliability_rating' => $this->getReliabilityRating(),
            'reliability_color' => $this->getReliabilityColor(),
            'has_contract' => $this->hasContract(),
            'contract_status' => $this->getContractStatusDisplay(),
            'days_to_contract_expiry' => $this->getDaysToContractExpiry(),
            'needs_audit' => $this->needsAudit(),
            'days_to_next_audit' => $this->getDaysToNextAudit(),
        ];
    }
}
