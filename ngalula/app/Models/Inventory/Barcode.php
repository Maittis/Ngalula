<?php

namespace App\Models\Inventory;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barcode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_barcodes';

    protected $fillable = [
        'barcode',
        'barcode_type',
        'barcode_format',
        'barcodeable_id',
        'barcodeable_type',
        'generated_by',
        'generated_at',
        'generation_method',
        'encoded_data',
        'additional_data',
        'print_format',
        'print_dimensions',
        'printer_model',
        'label_template',
        'scan_count',
        'last_scanned_at',
        'last_scanned_by',
        'last_scanned_location',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_notes',
        'status',
        'status_notes',
        'current_location',
        'location_updated_at',
        'location_updated_by',
        'batch_number',
        'lot_number',
        'manufacture_date',
        'expiry_date',
        'quality_grade',
        'quality_notes',
        'quality_checked_at',
        'quality_checked_by',
        'external_system',
        'external_id',
        'integration_data',
        'barcode_image_path',
        'qr_code_image_path',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'last_scanned_at' => 'datetime',
        'verified_at' => 'datetime',
        'location_updated_at' => 'datetime',
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
        'quality_checked_at' => 'datetime',
        'scan_count' => 'integer',
        'is_verified' => 'boolean',
        'additional_data' => 'array',
        'print_dimensions' => 'array',
        'integration_data' => 'array',
        'attachments' => 'array',
    ];

    // Relationships
    public function barcodeable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('barcode_type', $type);
    }

    public function scopeByFormat($query, $format)
    {
        return $query->where('barcode_format', $format);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('current_location', $location);
    }

    public function scopeByBatch($query, $batchNumber)
    {
        return $query->where('batch_number', $batchNumber);
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

    // Methods
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(now()) <= $days;
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

    public function isQRCode(): bool
    {
        return $this->barcode_type === 'QR_CODE';
    }

    public function isStandardBarcode(): bool
    {
        return in_array($this->barcode_type, ['EAN13', 'UPC', 'CODE128', 'CODE39']);
    }

    public function getBarcodeTypeDisplay(): string
    {
        return match($this->barcode_type) {
            'EAN13' => 'EAN-13',
            'UPC' => 'UPC',
            'CODE128' => 'Code 128',
            'CODE39' => 'Code 39',
            'QR_CODE' => 'QR Code',
            'DATA_MATRIX' => 'Data Matrix',
            default => $this->barcode_type
        };
    }

    public function getBarcodeFormatDisplay(): string
    {
        return match($this->barcode_format) {
            'numeric' => 'Numeric',
            'alphanumeric' => 'Alphanumeric',
            'binary' => 'Binary',
            default => ucfirst($this->barcode_format)
        };
    }

    public function getStatusDisplay(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            'damaged' => 'Damaged',
            'lost' => 'Lost',
            'expired' => 'Expired',
            'duplicate' => 'Duplicate',
            default => ucfirst($this->status)
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'damaged' => 'warning',
            'lost' => 'danger',
            'expired' => 'danger',
            'duplicate' => 'info',
            default => 'secondary'
        };
    }

    public function getQualityGradeDisplay(): string
    {
        return match($this->quality_grade) {
            'A' => 'Grade A',
            'B' => 'Grade B',
            'C' => 'Grade C',
            'D' => 'Grade D',
            'REJECT' => 'Rejected',
            default => $this->quality_grade
        };
    }

    public function getQualityGradeColor(): string
    {
        return match($this->quality_grade) {
            'A' => 'success',
            'B' => 'info',
            'C' => 'warning',
            'D' => 'danger',
            'REJECT' => 'danger',
            default => 'secondary'
        };
    }

    public function recordScan(string $location = null, string $scannedBy = null): void
    {
        $this->increment('scan_count');
        $this->update([
            'last_scanned_at' => now(),
            'last_scanned_by' => $scannedBy ?? auth()->id(),
            'last_scanned_location' => $location,
        ]);
    }

    public function verify(string $verifiedBy = null, string $notes = null): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $verifiedBy ?? auth()->id(),
            'verification_notes' => $notes,
        ]);
    }

    public function unverify(string $verifiedBy = null, string $notes = null): void
    {
        $this->update([
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null,
            'verification_notes' => $notes,
        ]);
    }

    public function updateLocation(string $location, string $updatedBy = null): void
    {
        $this->update([
            'current_location' => $location,
            'location_updated_at' => now(),
            'location_updated_by' => $updatedBy ?? auth()->id(),
        ]);
    }

    public function markAsDamaged(string $notes = null): void
    {
        $this->update([
            'status' => 'damaged',
            'status_notes' => $notes,
        ]);
    }

    public function markAsLost(string $notes = null): void
    {
        $this->update([
            'status' => 'lost',
            'status_notes' => $notes,
        ]);
    }

    public function markAsExpired(string $notes = null): void
    {
        $this->update([
            'status' => 'expired',
            'status_notes' => $notes,
        ]);
    }

    public function markAsDuplicate(string $notes = null): void
    {
        $this->update([
            'status' => 'duplicate',
            'status_notes' => $notes,
        ]);
    }

    public function generateImage(string $type = 'barcode'): string
    {
        // This would integrate with a barcode generation library
        // For now, return a placeholder path
        $filename = $type . '_' . $this->barcode . '.png';
        $path = 'barcodes/' . $filename;
        
        if ($type === 'barcode') {
            $this->update(['barcode_image_path' => $path]);
        } elseif ($type === 'qr_code') {
            $this->update(['qr_code_image_path' => $path]);
        }
        
        return $path;
    }

    public function getPrintDimensions(): array
    {
        return $this->print_dimensions ?: [
            'width' => 50,
            'height' => 25,
            'unit' => 'mm'
        ];
    }

    public function getEncodedData(): array
    {
        return $this->additional_data ?: [];
    }

    public function getIntegrationData(): array
    {
        return $this->integration_data ?: [];
    }

    public function getAttachments(): array
    {
        return $this->attachments ?: [];
    }

    public function getScanningHistory(): array
    {
        // This would typically query a separate scanning history table
        // For now, return basic info
        return [
            'total_scans' => $this->scan_count,
            'last_scan' => $this->last_scanned_at,
            'last_scanned_by' => $this->last_scanned_by,
            'last_scan_location' => $this->last_scanned_location,
        ];
    }

    public function getQualityInfo(): array
    {
        return [
            'grade' => $this->quality_grade,
            'grade_display' => $this->getQualityGradeDisplay(),
            'grade_color' => $this->getQualityGradeColor(),
            'notes' => $this->quality_notes,
            'checked_at' => $this->quality_checked_at,
            'checked_by' => $this->quality_checked_by,
            'is_verified' => $this->is_verified,
            'verified_at' => $this->verified_at,
            'verified_by' => $this->verified_by,
            'verification_notes' => $this->verification_notes,
        ];
    }

    public function getBatchInfo(): array
    {
        return [
            'batch_number' => $this->batch_number,
            'lot_number' => $this->lot_number,
            'manufacture_date' => $this->manufacture_date,
            'expiry_date' => $this->expiry_date,
            'days_to_expiry' => $this->getDaysToExpiry(),
            'is_expired' => $this->isExpired(),
            'is_expiring_soon' => $this->isExpiringSoon(),
        ];
    }

    public function getLocationHistory(): array
    {
        return [
            'current_location' => $this->current_location,
            'location_updated_at' => $this->location_updated_at,
            'location_updated_by' => $this->location_updated_by,
            'last_scan_location' => $this->last_scanned_location,
        ];
    }

    public function getExternalSystemInfo(): array
    {
        return [
            'external_system' => $this->external_system,
            'external_id' => $this->external_id,
            'integration_data' => $this->getIntegrationData(),
        ];
    }

    public function getPrintInfo(): array
    {
        return [
            'print_format' => $this->print_format,
            'print_dimensions' => $this->getPrintDimensions(),
            'printer_model' => $this->printer_model,
            'label_template' => $this->label_template,
            'barcode_image_path' => $this->barcode_image_path,
            'qr_code_image_path' => $this->qr_code_image_path,
        ];
    }

    public function getGenerationInfo(): array
    {
        return [
            'generated_by' => $this->generated_by,
            'generated_at' => $this->generated_at,
            'generation_method' => $this->generation_method,
            'encoded_data' => $this->encoded_data,
            'additional_data' => $this->getEncodedData(),
        ];
    }

    public function getSummary(): array
    {
        return [
            'barcode' => $this->barcode,
            'barcode_type' => $this->getBarcodeTypeDisplay(),
            'barcode_format' => $this->getBarcodeFormatDisplay(),
            'status' => $this->getStatusDisplay(),
            'status_color' => $this->getStatusColor(),
            'is_verified' => $this->is_verified,
            'scan_count' => $this->scan_count,
            'last_scanned_at' => $this->last_scanned_at,
            'current_location' => $this->current_location,
            'batch_info' => $this->getBatchInfo(),
            'quality_info' => $this->getQualityInfo(),
            'barcodeable_type' => $this->barcodeable_type,
            'barcodeable_id' => $this->barcodeable_id,
        ];
    }

    public function canBeScanned(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function needsVerification(): bool
    {
        return !$this->is_verified;
    }

    public function isHighValue(): bool
    {
        // Check if the associated item is high value
        if ($this->barcodeable) {
            // This would depend on the specific item type
            // For now, return false
            return false;
        }
        
        return false;
    }

    public function requiresSpecialHandling(): bool
    {
        // Check if the associated item requires special handling
        if ($this->barcodeable) {
            // This would depend on the specific item type
            // For now, return false
            return false;
        }
        
        return false;
    }

    public function getComplianceStatus(): array
    {
        return [
            'is_verified' => $this->is_verified,
            'is_expired' => $this->isExpired(),
            'is_expiring_soon' => $this->isExpiringSoon(),
            'quality_grade' => $this->quality_grade,
            'status' => $this->status,
            'compliant' => $this->is_verified && !$this->isExpired() && in_array($this->status, ['active', 'inactive']),
        ];
    }
}
