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
        Schema::create('inventory_oils', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            
            // Oil-specific properties
            $table->enum('oil_type', ['essential', 'carrier', 'massage', 'aromatherapy', 'therapeutic']);
            $table->string('botanical_name')->nullable(); // Latin name
            $table->string('common_names')->nullable(); // Other common names
            $table->string('origin')->nullable(); // Country/region of origin
            $table->string('extraction_method')->nullable(); // e.g., 'cold_pressed', 'steam distilled'
            
            // Physical properties
            $table->decimal('volume_ml', 8, 2); // Current volume in milliliters
            $table->decimal('volume_liters', 8, 2)->nullable(); // For larger quantities
            $table->decimal('density', 8, 4)->nullable(); // Specific gravity
            $table->string('viscosity')->nullable(); // e.g., 'light', 'medium', 'heavy'
            $table->string('color')->nullable();
            $table->string('aroma_profile')->nullable(); // e.g., 'floral', 'woody', 'citrus'
            
            // Inventory tracking
            $table->integer('current_stock')->default(0); // Number of bottles/containers
            $table->integer('minimum_stock')->default(0);
            $table->decimal('container_size_ml', 8, 2); // Size of each container
            $table->string('container_type'); // e.g., 'bottle', 'dropper', 'spray'
            
            // Pricing
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('selling_price_per_ml', 10, 2)->nullable();
            $table->decimal('selling_price_per_bottle', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            
            // Quality and safety
            $table->enum('grade', ['therapeutic', 'cosmetic', 'industrial', 'food_grade'])->nullable();
            $table->boolean('is_organic')->default(false);
            $table->boolean('is_wildcrafted')->default(false);
            $table->boolean('is_pure')->default(true);
            $table->boolean('is_diluted')->default(false);
            $table->string('certification')->nullable(); // e.g., 'USDA Organic', 'ECOCERT'
            
            // Usage information
            $table->text('uses')->nullable(); // Common uses
            $table->text('contraindications')->nullable(); // When not to use
            $table->text('safety_notes')->nullable();
            $table->text('blending_notes')->nullable(); // How it blends with other oils
            $table->json('therapeutic_properties')->nullable(); // Array of properties
            
            // Storage requirements
            $table->boolean('requires_refrigeration')->default(false);
            $table->boolean('protect_from_light')->default(true);
            $table->string('storage_temperature')->nullable(); // e.g., 'cool', 'room temperature'
            
            // Expiration and shelf life
            $table->date('expiry_date')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->integer('shelf_life_months')->nullable();
            $table->date('opened_date')->nullable(); // When first opened
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
            $table->decimal('average_monthly_usage_ml', 8, 2)->default(0);
            $table->date('last_used_date')->nullable();
            $table->date('last_restocked_date')->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive', 'discontinued', 'expired'])->default('active');
            
            // Notes
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['oil_type', 'status']);
            $table->index(['origin', 'grade']);
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
        Schema::dropIfExists('inventory_oils');
    }
};
