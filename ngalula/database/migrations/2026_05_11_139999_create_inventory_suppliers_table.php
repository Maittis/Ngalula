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
        Schema::create('inventory_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // Supplier code/ID
            $table->text('description')->nullable();
            
            // Contact information
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('website')->nullable();
            
            // Address
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            
            // Business information
            $table->enum('supplier_type', ['manufacturer', 'distributor', 'wholesaler', 'retailer', 'importer', 'exporter', 'local'])->default('distributor');
            $table->string('business_registration')->nullable(); // Business registration number
            $table->string('tax_id')->nullable(); // Tax identification number
            $table->string('vat_number')->nullable(); // VAT number
            
            // Categories and products
            $table->json('product_categories')->nullable(); // Categories they supply
            $table->json('specialties')->nullable(); // Specialized products
            $table->string('primary_category')->nullable(); // Main product category
            
            // Financial information
            $table->string('currency', 3)->default('USD');
            $table->enum('payment_terms', ['net_15', 'net_30', 'net_60', 'net_90', 'cod', 'prepaid'])->default('net_30');
            $table->enum('payment_methods', ['bank_transfer', 'credit_card', 'check', 'cash', 'paypal', 'stripe'])->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('swift_code')->nullable();
            
            // Shipping and delivery
            $table->json('shipping_methods')->nullable(); // Available shipping methods
            $table->string('default_shipping_method')->nullable();
            $table->integer('lead_time_days')->default(7); // Average lead time
            $table->integer('minimum_order_quantity')->default(1);
            $table->decimal('minimum_order_amount', 10, 2)->default(0);
            $table->json('shipping_zones')->nullable(); // Geographic zones they serve
            
            // Quality and compliance
            $table->boolean('is_certified')->default(false);
            $table->json('certifications')->nullable(); // Array of certifications
            $table->string('quality_rating')->nullable(); // e.g., 'A+', 'A', 'B', 'C'
            $table->date('last_audit_date')->nullable();
            $table->date('next_audit_due')->nullable();
            
            // Performance metrics
            $table->decimal('on_time_delivery_rate', 5, 2)->default(0); // Percentage
            $table->decimal('quality_score', 5, 2)->default(0); // Quality rating
            $table->integer('total_orders')->default(0);
            $table->decimal('total_order_value', 12, 2)->default(0);
            $table->date('last_order_date')->nullable();
            
            // Contract information
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->enum('contract_status', ['active', 'expired', 'terminated', 'pending'])->nullable();
            $table->text('contract_terms')->nullable();
            $table->string('contract_file_path')->nullable();
            
            // Communication preferences
            $table->json('preferred_contact_methods')->nullable(); // Array of preferred methods
            $table->string('preferred_language')->default('English');
            $table->enum('communication_frequency', ['daily', 'weekly', 'monthly', 'as_needed'])->default('as_needed');
            
            // Status and flags
            $table->enum('status', ['active', 'inactive', 'suspended', 'blacklisted', 'pending_approval'])->default('active');
            $table->boolean('is_preferred')->default(false);
            $table->boolean('is_primary_supplier')->default(false);
            $table->boolean('allows_returns')->default(true);
            $table->integer('return_policy_days')->default(30);
            
            // Internal tracking
            $table->string('assigned_buyer')->nullable(); // Internal staff member
            $table->string('department')->nullable(); // Internal department
            $table->date('first_order_date')->nullable();
            $table->text('internal_notes')->nullable();
            $table->json('tags')->nullable(); // Internal tags for categorization
            
            // Documents and attachments
            $table->json('attachments')->nullable(); // Array of file paths
            $table->string('catalog_file_path')->nullable();
            $table->string('price_list_file_path')->nullable();
            $table->string('insurance_certificate_path')->nullable();
            
            // Integration data
            $table->string('erp_supplier_id')->nullable(); // ID in ERP system
            $table->string('accounting_system_id')->nullable(); // ID in accounting system
            $table->json('integration_data')->nullable();
            
            // Emergency contact
            $table->string('emergency_contact_person')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('emergency_email')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['supplier_type', 'status']);
            $table->index(['country']);
            $table->index(['primary_category']);
            $table->index(['payment_terms']);
            $table->index(['contract_end_date']);
            $table->index(['last_order_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_suppliers');
    }
};
