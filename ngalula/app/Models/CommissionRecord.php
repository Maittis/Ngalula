<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapist_id',
        'amount',
        'type',
        'description',
        'related_id',
        'related_type',
        'status',
        'calculated_at',
        'paid_at',
        'paid_by',
        'payment_method',
        'payment_reference',
        'tax_amount',
        'net_amount',
        'commission_rate',
        'base_amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'commission_rate' => 'decimal:4',
        'base_amount' => 'decimal:2',
        'calculated_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function therapist(): BelongsTo
    {
        return $this->belongsTo(Therapist::class);
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByTherapist($query, $therapistId)
    {
        return $query->where('therapist_id', $therapistId);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('calculated_at', [$startDate, $endDate]);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pending', 'approved']);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        return ucfirst($this->status);
    }

    public function getTypeLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getFormattedNetAmountAttribute()
    {
        return '$' . number_format($this->net_amount, 2);
    }

    public function getFormattedTaxAmountAttribute()
    {
        return '$' . number_format($this->tax_amount, 2);
    }

    public function getFormattedCommissionRateAttribute()
    {
        return ($this->commission_rate * 100) . '%';
    }

    public function getIsPaidAttribute()
    {
        return $this->status === 'paid';
    }

    public function getIsPendingAttribute()
    {
        return $this->status === 'pending';
    }

    public function getDaysSinceCalculatedAttribute()
    {
        return $this->calculated_at ? $this->calculated_at->diffInDays(now()) : 0;
    }

    // Helper methods
    public function approve(): void
    {
        $this->update(['status' => 'approved']);
    }

    public function markAsPaid($paidBy, $paymentMethod = null, $reference = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_by' => $paidBy,
            'payment_method' => $paymentMethod,
            'payment_reference' => $reference,
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function calculateTax($taxRate = 0.15): void
    {
        $taxAmount = $this->amount * $taxRate;
        $netAmount = $this->amount - $taxAmount;
        
        $this->update([
            'tax_amount' => $taxAmount,
            'net_amount' => $netAmount,
        ]);
    }

    public function recalculate($baseAmount, $commissionRate): void
    {
        $amount = $baseAmount * $commissionRate;
        
        $this->update([
            'base_amount' => $baseAmount,
            'commission_rate' => $commissionRate,
            'amount' => $amount,
        ]);
        
        // Recalculate tax if tax amount exists
        if ($this->tax_amount > 0) {
            $this->calculateTax($this->tax_amount / $this->amount);
        }
    }

    public function canBeModified(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, ['approved']);
    }

    public static function getTypes(): array
    {
        return [
            'session_commission' => 'Session Commission',
            'product_sales' => 'Product Sales',
            'service_fee' => 'Service Fee',
            'bonus' => 'Bonus',
            'overtime' => 'Overtime',
            'referral_bonus' => 'Referral Bonus',
            'performance_bonus' => 'Performance Bonus',
            'holiday_pay' => 'Holiday Pay',
            'adjustment' => 'Adjustment',
            'reversal' => 'Reversal',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            'disputed' => 'Disputed',
        ];
    }

    public static function getPaymentMethods(): array
    {
        return [
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'check' => 'Check',
            'mobile_money' => 'Mobile Money',
            'paypal' => 'PayPal',
            'other' => 'Other',
        ];
    }

    public function getSummary(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->formatted_amount,
            'net_amount' => $this->formatted_net_amount,
            'tax_amount' => $this->formatted_tax_amount,
            'commission_rate' => $this->formatted_commission_rate,
            'type' => $this->type_label,
            'status' => $this->status_label,
            'description' => $this->description,
            'calculated_at' => $this->calculated_at->format('Y-m-d H:i'),
            'paid_at' => $this->paid_at?->format('Y-m-d H:i'),
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'days_since_calculated' => $this->days_since_calculated,
            'is_paid' => $this->is_paid,
            'is_pending' => $this->is_pending,
            'can_be_modified' => $this->can_be_modified(),
            'can_be_paid' => $this->can_be_paid(),
            'therapist_id' => $this->therapist_id,
        ];
    }

    public static function getCommissionSummary($therapistId, $startDate = null, $endDate = null): array
    {
        $query = self::where('therapist_id', $therapistId);
        
        if ($startDate) {
            $query->where('calculated_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('calculated_at', '<=', $endDate);
        }
        
        $records = $query->get();
        
        return [
            'total_commission' => $records->sum('amount'),
            'total_paid' => $records->where('status', 'paid')->sum('amount'),
            'total_pending' => $records->whereIn('status', ['pending', 'approved'])->sum('amount'),
            'total_tax' => $records->sum('tax_amount'),
            'total_net' => $records->sum('net_amount'),
            'record_count' => $records->count(),
            'paid_count' => $records->where('status', 'paid')->count(),
            'pending_count' => $records->whereIn('status', ['pending', 'approved'])->count(),
            'by_type' => $records->groupBy('type')->map(function ($typeRecords) {
                return [
                    'amount' => $typeRecords->sum('amount'),
                    'count' => $typeRecords->count(),
                ];
            }),
        ];
    }
}
