<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'type',
        'status',
        'billing_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'due_amount',
        'currency',
        'invoice_date',
        'due_date',
        'paid_at',
        'sent_at',
        'invoiceable_type',
        'invoiceable_id',
        'payment_transaction_id',
        'line_items',
        'coupon_id',
        'gift_card_id',
        'promo_code',
        'payment_schedule',
        'payment_history',
        'notes',
        'terms',
        'pdf_path',
        'pdf_generated',
        'metadata',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'sent_at' => 'datetime',
        'line_items' => 'array',
        'payment_schedule' => 'array',
        'payment_history' => 'array',
        'pdf_generated' => 'boolean',
        'metadata' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoiceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function paymentTransaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function giftCard(): BelongsTo
    {
        return $this->belongsTo(GiftCard::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'sent')
            ->where('due_date', '<', now());
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['draft', 'sent'])
            ->whereRaw('paid_amount < total_amount');
    }

    // Accessors
    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 2) . ' ' . $this->currency;
    }

    public function getFormattedTaxAmountAttribute()
    {
        return number_format($this->tax_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedDiscountAmountAttribute()
    {
        return number_format($this->discount_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedTotalAmountAttribute()
    {
        return number_format($this->total_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedPaidAmountAttribute()
    {
        return number_format($this->paid_amount, 2) . ' ' . $this->currency;
    }

    public function getFormattedDueAmountAttribute()
    {
        return number_format($this->due_amount, 2) . ' ' . $this->currency;
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getTypeLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }

    public function getIsPaidAttribute()
    {
        return $this->status === 'paid' || $this->paid_amount >= $this->total_amount;
    }

    public function getIsOverdueAttribute()
    {
        return $this->status === 'sent' && $this->due_date->isPast();
    }

    public function getIsPartiallyPaidAttribute()
    {
        return $this->paid_amount > 0 && $this->paid_amount < $this->total_amount;
    }

    public function getPaymentPercentageAttribute()
    {
        if ($this->total_amount == 0) {
            return 0;
        }
        return round(($this->paid_amount / $this->total_amount) * 100, 2);
    }

    public function getDaysOverdueAttribute()
    {
        if ($this->is_overdue) {
            return now()->diffInDays($this->due_date);
        }
        return 0;
    }

    // Helper methods
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsPaid(float $amount = null): void
    {
        $paymentAmount = $amount ?? $this->due_amount;
        
        $this->update([
            'paid_amount' => $this->paid_amount + $paymentAmount,
            'paid_at' => $this->paid_amount + $paymentAmount >= $this->total_amount ? now() : $this->paid_at,
        ]);

        if ($this->paid_amount >= $this->total_amount) {
            $this->update(['status' => 'paid']);
        }

        $this->addPaymentHistory($paymentAmount);
    }

    public function markAsOverdue(): void
    {
        if ($this->status === 'sent' && $this->due_date->isPast()) {
            $this->update(['status' => 'overdue']);
        }
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function addPaymentHistory(float $amount, string $method = null, string $description = null): void
    {
        $paymentHistory = $this->payment_history ?? [];
        $paymentHistory[] = [
            'amount' => $amount,
            'method' => $method,
            'description' => $description,
            'timestamp' => now()->toISOString(),
            'balance_before' => $this->paid_amount,
            'balance_after' => $this->paid_amount + $amount,
        ];

        $this->payment_history = $paymentHistory;
        $this->save();
    }

    public function addLineItem(string $description, float $unitPrice, int $quantity = 1, array $metadata = []): void
    {
        $lineItems = $this->line_items ?? [];
        $lineItems[] = [
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $unitPrice * $quantity,
            'metadata' => $metadata,
        ];

        $this->line_items = $lineItems;
        $this->recalculateTotals();
    }

    public function recalculateTotals(): void
    {
        $subtotal = 0;
        $lineItems = $this->line_items ?? [];

        foreach ($lineItems as $item) {
            $subtotal += $item['total'] ?? 0;
        }

        $this->subtotal = $subtotal;
        $this->total_amount = $subtotal + $this->tax_amount - $this->discount_amount;
        $this->due_amount = $this->total_amount - $this->paid_amount;

        $this->save();
    }

    public function applyDiscount(float $discountAmount): void
    {
        $this->discount_amount = $discountAmount;
        $this->total_amount = $this->subtotal + $this->tax_amount - $discountAmount;
        $this->due_amount = $this->total_amount - $this->paid_amount;
        $this->save();
    }

    public function generatePDF(): string
    {
        // This would generate a PDF file and return the path
        // For now, we'll simulate PDF generation
        $pdfPath = 'invoices/' . $this->invoice_number . '.pdf';
        
        $this->update([
            'pdf_path' => $pdfPath,
            'pdf_generated' => true,
        ]);

        return $pdfPath;
    }

    public static function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');
        $sequence = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return "INV-{$year}{$month}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public static function getTypes(): array
    {
        return [
            'sale' => 'Sale',
            'refund' => 'Refund',
            'subscription' => 'Subscription',
            'deposit' => 'Deposit',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'draft' => 'Draft',
            'sent' => 'Sent',
            'paid' => 'Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ];
    }

    public function getInvoiceSummary(): array
    {
        return [
            'invoice_number' => $this->invoice_number,
            'type' => $this->type_label,
            'status' => $this->status_label,
            'billing_name' => $this->billing_name,
            'billing_email' => $this->billing_email,
            'invoice_date' => $this->invoice_date->format('M d, Y'),
            'due_date' => $this->due_date->format('M d, Y'),
            'subtotal' => $this->formatted_subtotal,
            'tax_amount' => $this->formatted_tax_amount,
            'discount_amount' => $this->formatted_discount_amount,
            'total_amount' => $this->formatted_total_amount,
            'paid_amount' => $this->formatted_paid_amount,
            'due_amount' => $this->formatted_due_amount,
            'payment_percentage' => $this->payment_percentage . '%',
            'is_paid' => $this->is_paid,
            'is_overdue' => $this->days_overdue,
            'line_items_count' => count($this->line_items ?? []),
            'pdf_generated' => $this->pdf_generated,
        ];
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, ['draft', 'sent']) && $this->due_amount > 0;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['draft', 'sent']) && !$this->is_paid;
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'paid' && $this->paid_amount > 0;
    }
}
