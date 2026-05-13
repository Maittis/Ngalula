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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Discount details
            $table->enum('discount_type', ['percentage', 'fixed_amount']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('minimum_amount', 10, 2)->nullable(); // Minimum order amount
            $table->decimal('maximum_discount', 10, 2)->nullable(); // Maximum discount amount
            
            // Usage limits
            $table->integer('usage_limit')->nullable(); // Total usage limit
            $table->integer('usage_limit_per_customer')->nullable(); // Per customer limit
            $table->integer('usage_count')->default(0); // Current usage count
            
            // Validity
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Applicability
            $table->json('applicable_services')->nullable(); // Service IDs this applies to
            $table->json('applicable_categories')->nullable(); // Category IDs this applies to
            $table->boolean('applies_to_all')->default(true); // Applies to all services
            
            // Restrictions
            $table->boolean('first_time_only')->default(false); // First time customers only
            $table->boolean('exclude_sale_items')->default(false); // Exclude sale items
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->index(['code']);
            $table->index(['is_active']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
