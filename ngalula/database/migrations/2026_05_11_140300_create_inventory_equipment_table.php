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
        Schema::create('inventory_equipment', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            
            // Equipment-specific properties
            $table->enum('equipment_type', ['massage_table', 'chair', 'heating_device', 'cooling_device', 'exercise_equipment', 'diagnostic_tool', 'storage_unit', 'furniture', 'electronic_device', 'tool']);
            $table->string('subcategory')->nullable(); // e.g., 'portable_table', 'electric_table', 'reclining_chair'
            
            // Physical specifications
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->json('dimensions')->nullable(); // {length, width, height, unit}
            $table->string('color')->nullable();
            $table->string('material')->nullable(); // e.g., 'wood', 'metal', 'plastic', 'leather'
            $table->string('finish')->nullable(); // e.g., 'matte', 'glossy', 'brushed'
            
            // Inventory tracking
            $table->integer('current_stock')->default(0); // Number of units
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->nullable();
            
            // Pricing
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            
            // Equipment features
            $table->json('features')->nullable(); // Array of features
            $table->json('specifications')->nullable(); // Technical specifications
            $table->boolean('is_portable')->default(false);
            $table->boolean('is_electronic')->default(false);
            $table->boolean('requires_power')->default(false);
            $table->string('power_requirements')->nullable(); // e.g., '110V', '220V', 'battery'
            $table->string('power_consumption')->nullable(); // e.g., '100W', '200W'
            
            // Condition and maintenance
            $table->enum('condition', ['new', 'excellent', 'good', 'fair', 'poor', 'needs_repair', 'out_of_service'])->default('good');
            $table->date('purchase_date')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->integer('maintenance_interval_days')->nullable(); // Days between maintenance
            $table->date('next_maintenance_due')->nullable();
            $table->text('maintenance_notes')->nullable();
            $table->decimal('maintenance_cost', 10, 2)->nullable();
            
            // Warranty and support
            $table->date('warranty_expiry')->nullable();
            $table->string('warranty_provider')->nullable();
            $table->text('warranty_terms')->nullable();
            $table->string('support_contact')->nullable();
            $table->string('manual_location')->nullable(); // Path to user manual
            
            // Safety and compliance
            $table->boolean('requires_certification')->default(false);
            $table->string('certification_required')->nullable(); // e.g., 'OSHA', 'FDA', 'CE'
            $table->date('certification_expiry')->nullable();
            $table->text('safety_instructions')->nullable();
            $table->json('safety_features')->nullable(); // Array of safety features
            $table->boolean('is_hazardous')->default(false);
            $table->text('hazard_notes')->nullable();
            
            // Usage tracking
            $table->integer('usage_count')->default(0);
            $table->date('last_used_date')->nullable();
            $table->decimal('average_daily_usage', 8, 2)->default(0);
            $table->json('usage_restrictions')->nullable(); // Who can use this equipment
            
            // Location tracking
            $table->string('current_location')->nullable(); // Current location
            $table->string('home_location')->nullable(); // Default/assigned location
            $table->string('warehouse')->nullable();
            $table->boolean('is_movable')->default(true);
            
            // Supplier and manufacturer
            $table->string('manufacturer')->nullable();
            $table->string('brand')->nullable();
            $table->string('model_number')->nullable();
            $table->string('serial_number')->nullable()->unique();
            $table->foreignId('supplier_id')->nullable()->constrained('inventory_suppliers')->onDelete('set null');
            
            // Barcode and identification
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            $table->string('asset_tag')->nullable()->unique(); // Internal asset tracking
            
            // Replacement parts
            $table->json('replacement_parts')->nullable(); // Array of required parts
            $table->json('consumables')->nullable(); // Consumable items needed
            
            // Status
            $table->enum('status', ['active', 'inactive', 'maintenance', 'repair', 'retired', 'lost', 'stolen'])->default('active');
            
            // Notes and attachments
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable(); // Manual, photos, certificates
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['equipment_type', 'status']);
            $table->index(['condition', 'next_maintenance_due']);
            $table->index(['supplier_id']);
            $table->index(['current_location']);
            $table->index(['barcode']);
            $table->index(['serial_number']);
            $table->index(['asset_tag']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_equipment');
    }
};
