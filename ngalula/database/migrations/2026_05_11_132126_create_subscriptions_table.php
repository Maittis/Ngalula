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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Subscription details
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'paused', 'cancelled', 'expired', 'past_due'])->default('active');
            
            // Billing cycle
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'semi_annually', 'annually']);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            
            // Dates
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_starts_at')->nullable();
            $table->timestamp('current_period_ends_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            
            // Payment method
            $table->foreignId('payment_method_id')->nullable()->constrained()->onDelete('set null');
            $table->string('gateway_subscription_id')->nullable();
            
            // Auto-renewal
            $table->boolean('auto_renew')->default(true);
            $table->integer('renewal_attempts')->default(0);
            $table->timestamp('last_renewal_attempt')->nullable();
            
            // Usage tracking
            $table->json('features')->nullable(); // Features included in subscription
            $table->json('usage_limits')->nullable(); // Usage limits
            $table->json('current_usage')->nullable(); // Current usage
            
            // Pricing details
            $table->decimal('setup_fee', 10, 2)->default(0);
            $table->decimal('trial_amount', 10, 2)->default(0);
            $table->json('pricing_tiers')->nullable(); // Tiered pricing
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['status']);
            $table->index(['current_period_ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
