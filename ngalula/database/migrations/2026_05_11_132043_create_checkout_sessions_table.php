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
        Schema::create('checkout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('session_id')->unique();
            $table->enum('status', ['pending', 'processing', 'completed', 'expired', 'cancelled'])->default('pending');
            
            // Order details
            $table->morphs('orderable'); // Can be booking, product, etc.
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            
            // Payment breakdown
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->decimal('remaining_amount', 10, 2)->nullable();
            $table->enum('payment_type', ['full', 'deposit', 'partial'])->default('full');
            
            // Applied discounts
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('gift_card_id')->nullable()->constrained()->onDelete('set null');
            $table->string('promo_code')->nullable();
            
            // Payment methods
            $table->json('payment_methods')->nullable(); // Array of payment method IDs
            $table->json('payment_breakdown')->nullable(); // How much each method pays
            
            // Security
            $table->string('client_secret')->unique();
            $table->string('checkout_url')->nullable();
            $table->json('metadata')->nullable();
            
            // Timestamps
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['session_id']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_sessions');
    }
};
