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
        Schema::create('inventory_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique(); // Stock Keeping Unit
            $table->text('description')->nullable();
            $table->string('category'); // e.g., 'oil', 'cream', 'equipment', 'supplies'
            $table->string('subcategory')->nullable(); // e.g., 'massage_oil', 'essential_oil', 'lotion', 'device'
            
            // Inventory details
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0); // Low stock threshold
            $table->integer('maximum_stock')->nullable(); // Maximum stock level
            $table->integer('reorder_quantity')->default(0); // Quantity to reorder
            
            // Pricing
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            
            // Physical properties
            $table->string('unit_of_measure'); // e.g., 'ml', 'liters', 'grams', 'kg', 'pieces', 'bottles'
            $table->decimal('unit_size', 8, 2)->nullable(); // Size per unit
            $table->string('size_unit')->nullable(); // e.g., 'ml', 'g', 'oz'
            
            // Location
            $table->string('storage_location')->nullable(); // e.g., 'Shelf A1', 'Room 2', 'Storage B'
            $table->string('warehouse')->nullable();
            
            // Supplier information
            $table->foreignId('primary_supplier_id')->nullable()->constrained('inventory_suppliers')->onDelete('set null');
            
            // Status and flags
            $table->enum('status', ['active', 'inactive', 'discontinued', 'out_of_stock'])->default('active');
            $table->boolean('is_trackable')->default(true); // Can be tracked individually
            $table->boolean('requires_refrigeration')->default(false);
            $table->boolean('is_hazardous')->default(false);
            $table->boolean('is_perishable')->default(false);
            
            // Expiration tracking
            $table->date('expiry_date')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->integer('shelf_life_days')->nullable(); // Days from manufacture to expiry
            
            // Quality control
            $table->string('batch_number')->nullable();
            $table->string('lot_number')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('brand')->nullable();
            
            // Barcode
            $table->string('barcode')->nullable()->unique();
            $table->string('qr_code')->nullable()->unique();
            
            // Usage tracking
            $table->integer('average_monthly_usage')->default(0);
            $table->date('last_used_date')->nullable();
            $table->date('last_restocked_date')->nullable();
            
            // Notes and attachments
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable(); // Array of file paths
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['category', 'subcategory']);
            $table->index(['status', 'current_stock']);
            $table->index(['storage_location', 'warehouse']);
            $table->index(['primary_supplier_id']);
            $table->index(['expiry_date']);
            $table->index(['barcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_products');
    }
};
