<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_method_id',
        'transaction_id',
        'gateway_transaction_id',
        'type',
        'status',
        'payment_method_type',
        'payment_provider',
        'amount',
        'currency',
        'fee',
        'tax',
        'payable_type',
        'payable_id',
        'description',
        'gateway_response',
        'metadata',
        'processed_at',
        'completed_at',
        'failed_at',
        'refunded_at',
        'failure_reason',
        'failure_code',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'tax' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
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

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method_type', $method);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function getTotalAmountAttribute()
    {
        return $this->amount + $this->fee + $this->tax;
    }

    public function getFormattedTotalAmountAttribute()
    {
        return number_format($this->total_amount, 2) . ' ' . $this->currency;
    }

    public function getStatusLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getPaymentMethodLabelAttribute()
    {
        return PaymentMethod::getAvailableTypes()[$this->payment_method_type] ?? ucfirst($this->payment_method_type);
    }

    public function getIsSuccessfulAttribute()
    {
        return $this->status === 'completed';
    }

    public function getIsFailedAttribute()
    {
        return $this->status === 'failed';
    }

    public function getIsPendingAttribute()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function getCanRefundAttribute()
    {
        return $this->status === 'completed' && $this->type === 'payment';
    }

    // Helper methods
    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'processed_at' => now(),
        ]);
    }

    public function markAsCompleted(array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'gateway_response' => $gatewayResponse,
        ]);
    }

    public function markAsFailed(string $reason, string $code = null, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason,
            'failure_code' => $code,
            'gateway_response' => $gatewayResponse,
        ]);
    }

    public function markAsRefunded(): void
    {
        $this->update([
            'status' => 'refunded',
            'refunded_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    public function refund(float $amount = null, string $reason = null): PaymentTransaction
    {
        $refundAmount = $amount ?? $this->amount;
        
        return static::create([
            'user_id' => $this->user_id,
            'payment_method_id' => $this->payment_method_id,
            'transaction_id' => $this->generateTransactionId(),
            'gateway_transaction_id' => null,
            'type' => $amount && $amount < $this->amount ? 'partial_refund' : 'refund',
            'status' => 'pending',
            'payment_method_type' => $this->payment_method_type,
            'payment_provider' => $this->payment_provider,
            'amount' => $refundAmount,
            'currency' => $this->currency,
            'fee' => 0,
            'tax' => 0,
            'payable_type' => $this->payable_type,
            'payable_id' => $this->payable_id,
            'description' => $reason ?? "Refund for transaction {$this->transaction_id}",
            'metadata' => [
                'original_transaction_id' => $this->transaction_id,
                'refund_reason' => $reason,
            ],
        ]);
    }

    public static function generateTransactionId(): string
    {
        return 'TXN' . strtoupper(uniqid()) . time();
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'refunded' => 'Refunded',
        ];
    }

    public static function getTypes(): array
    {
        return [
            'payment' => 'Payment',
            'refund' => 'Refund',
            'partial_refund' => 'Partial Refund',
        ];
    }

    public function getTimeline(): array
    {
        $timeline = [];
        
        if ($this->created_at) {
            $timeline[] = [
                'status' => 'created',
                'label' => 'Transaction Created',
                'timestamp' => $this->created_at,
            ];
        }
        
        if ($this->processed_at) {
            $timeline[] = [
                'status' => 'processed',
                'label' => 'Processing Started',
                'timestamp' => $this->processed_at,
            ];
        }
        
        if ($this->completed_at) {
            $timeline[] = [
                'status' => 'completed',
                'label' => 'Payment Completed',
                'timestamp' => $this->completed_at,
            ];
        }
        
        if ($this->failed_at) {
            $timeline[] = [
                'status' => 'failed',
                'label' => 'Payment Failed',
                'timestamp' => $this->failed_at,
            ];
        }
        
        if ($this->refunded_at) {
            $timeline[] = [
                'status' => 'refunded',
                'label' => 'Payment Refunded',
                'timestamp' => $this->refunded_at,
            ];
        }
        
        return $timeline;
    }
}
