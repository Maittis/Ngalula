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
        Schema::create('inventory_creams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            
            // Cream-specific properties
            $table->enum('cream_type', ['moisturizer', 'massage_cream', 'therapeutic', 'sunscreen', 'medicated', 'cosmetic']);
            $table->enum('skin_type', ['all', 'dry', 'oily', 'combination', 'sensitive', 'mature', 'acne_prone'])->nullable();
            $table->enum('consistency', ['light', 'medium', 'heavy', 'gel', 'lotion', 'cream', 'ointment']);
            
            // Physical properties
            $table->decimal('weight_grams', 8, 2); // Current weight in grams
            $table->decimal('volume_ml', 8, 2)->nullable(); // Volume if applicable
            $table->decimal('density', 8, 4)->nullable(); // Specific gravity
            $table->string('color')->nullable();
            $table->string('texture')->nullable(); // e.g., 'smooth', 'creamy', 'grainy'
            $table->string('scent')->nullable(); // e.g., 'unscented', 'lavender', 'citrus'
            
            // Inventory tracking
            $table->integer('current_stock')->default(0); // Number of containers
            $table->integer('minimum_stock')->default(0);
            $table->decimal('container_size_grams', 8, 2); // Size of each container
            $table->decimal('container_size_ml', 8, 2)->nullable(); // Volume if applicable
            $table->string('container_type'); // e.g., 'tube', 'jar', 'pump', 'bottle'
            
            // Pricing
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('selling_price_per_gram', 10, 2)->nullable();
            $table->decimal('selling_price_per_ml', 10, 2)->nullable();
            $table->decimal('selling_price_per_unit', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            
            // Ingredients and formulation
            $table->text('ingredients')->nullable(); // Full ingredient list
            $table->json('active_ingredients')->nullable(); // Array of active ingredients
            $table->json('allergens')->nullable(); // Array of potential allergens
            $table->boolean('is_hypoallergenic')->default(false);
            $table->boolean('is_dermatologically_tested')->default(false);
            $table->boolean('is_cruelty_free')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_organic')->default(false);
            
            // Quality and safety
            $table->enum('grade', ['cosmetic', 'pharmaceutical', 'therapeutic', 'medical'])->nullable();
            $table->string('certification')->nullable(); // e.g., 'FDA Approved', 'CE Certified'
            $table->string('regulatory_number')->nullable(); // FDA or other regulatory number
            
            // Usage information
            $table->text('uses')->nullable(); // Common uses
            $table->text('application_instructions')->nullable();
            $table->text('contraindications')->nullable();
            $table->text('side_effects')->nullable();
            $table->text('storage_instructions')->nullable();
            $table->json('therapeutic_properties')->nullable(); // Array of properties
            
            // Age and target group
            $table->enum('age_group', ['all', 'infant', 'child', 'teen', 'adult', 'senior'])->nullable();
            $table->enum('target_group', ['general', 'professional', 'medical'])->nullable();
            
            // Storage requirements
            $table->boolean('requires_refrigeration')->default(false);
            $table->boolean('protect_from_light')->default(false);
            $table->boolean('protect_from_heat')->default(true);
            $table->string('storage_temperature')->nullable();
            
            // Expiration and shelf life
            $table->date('expiry_date')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->integer('shelf_life_months')->nullable();
            $table->date('opened_date')->nullable();
            $table->integer('shelf_life_after_opening_months')->nullable();
            
            // Supplier and batch info
            $table->string('batch_number')->nullable();
            $table->string('lot_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('brand')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('inventory_suppliers')->onDelete('set null');
            
            // Location tracking
            $table->string('storage_location')->nullable();
            $table->string('warehouse')->nullable();
            
            // Barcode and identification
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            
            // Usage tracking
            $table->decimal('average_monthly_usage_grams', 8, 2)->default(0);
            $table->date('last_used_date')->nullable();
            $table->date('last_restocked_date')->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive', 'discontinued', 'expired', 'recalled'])->default('active');
            
            // Notes
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable(); // Product images, safety sheets, etc.
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['cream_type', 'status']);
            $table->index(['skin_type', 'consistency']);
            $table->index(['supplier_id']);
            $table->index(['expiry_date']);
            $table->index(['barcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_creams');
    }
};
