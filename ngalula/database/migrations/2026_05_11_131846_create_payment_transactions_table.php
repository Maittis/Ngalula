<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->nullable()->constrained()->onDelete('set null');
            
            // Transaction details
            $table->string('transaction_id')->unique(); // Internal transaction ID
            $table->string('gateway_transaction_id')->nullable(); // Gateway transaction ID
            $table->enum('type', ['payment', 'refund', 'partial_refund'])->default('payment');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            
            // Payment method details
            $table->enum('payment_method_type', ['airtel_money', 'mtn_money', 'visa', 'mastercard']);
            $table->string('payment_provider'); // 'airtel', 'mtn', 'stripe', etc.
            
            // Amount details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('fee', 10, 2)->default(0); // Processing fees
            $table->decimal('tax', 10, 2)->default(0); // Tax amount
            
            // Related entities
            $table->morphs('payable'); // Can be for bookings, products, etc.
            $table->string('description')->nullable();
            
            // Gateway response
            $table->json('gateway_response')->nullable(); // Full gateway response
            $table->json('metadata')->nullable(); // Additional transaction data
            
            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            
            // Error handling
            $table->text('failure_reason')->nullable();
            $table->string('failure_code')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['payment_method_type', 'status']);
            $table->index(['transaction_id']);
            $table->index(['gateway_transaction_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
