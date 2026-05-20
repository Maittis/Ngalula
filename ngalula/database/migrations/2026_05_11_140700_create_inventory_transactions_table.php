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
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique(); // TXN-2024-001 format
            
            // Transaction type and details
            $table->enum('transaction_type', ['purchase', 'sale', 'transfer', 'adjustment', 'return', 'damage', 'loss', 'expiration', 'consumption', 'restock', 'disposal']);
            $table->enum('sub_type', ['new_purchase', 'reorder', 'bulk_purchase', 'emergency_purchase', 'customer_sale', 'internal_use', 'transfer_in', 'transfer_out', 'stock_adjustment', 'manual_adjustment', 'system_adjustment', 'customer_return', 'supplier_return', 'damaged_goods', 'lost_goods', 'expired_goods', 'consumed', 'restocked', 'disposed']);
            
            // Item information (polymorphic relationship)
            $table->string('transactionable_type');
            $table->unsignedBigInteger('transactionable_id');
            $table->index(['transactionable_type', 'transactionable_id'], 'transactions_item_idx');
            
            // Quantity information
            $table->decimal('quantity', 10, 2); // Positive for additions, negative for deductions
            $table->string('unit_of_measure'); // e.g., 'ml', 'grams', 'pieces', 'bottles'
            $table->decimal('unit_cost', 10, 2)->nullable(); // Cost per unit
            $table->decimal('total_cost', 12, 2)->nullable(); // Total cost for this transaction
            
            // Stock levels before and after
            $table->decimal('stock_before', 10, 2)->default(0);
            $table->decimal('stock_after', 10, 2)->default(0);
            $table->decimal('reorder_point', 10, 2)->nullable();
            
            // Location information
            $table->string('location_from')->nullable(); // Source location for transfers
            $table->string('location_to')->nullable(); // Destination location for transfers
            $table->string('warehouse')->nullable();
            $table->string('storage_location')->nullable();
            
            // Reference information
            $table->string('reference_number')->nullable(); // Purchase order, invoice, etc.
            $table->string('reference_type')->nullable(); // 'purchase_order', 'invoice', 'return_authorization'
            $table->foreignId('purchase_request_id')->nullable()->constrained('inventory_purchase_requests')->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained('inventory_suppliers')->onDelete('set null');
            
            // People involved
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null'); // Person who physically handled the transaction
            
            // Dates and timing
            $table->date('transaction_date');
            $table->time('transaction_time')->nullable();
            $table->date('approved_at')->nullable();
            $table->date('performed_at')->nullable();
            $table->date('recorded_at')->nullable();
            
            // Reason and notes
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Quality and condition
            $table->enum('condition', ['new', 'good', 'fair', 'poor', 'damaged', 'expired'])->nullable();
            $table->text('condition_notes')->nullable();
            $table->boolean('quality_checked')->default(false);
            $table->foreignId('quality_checked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('quality_checked_at')->nullable();
            
            // Batch and lot tracking
            $table->string('batch_number')->nullable();
            $table->string('lot_number')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();
            
            // Financial information
            $table->string('currency', 3)->default('USD');
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->decimal('handling_cost', 10, 2)->nullable();
            
            // Approval and authorization
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('completed');
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // System information
            $table->string('source')->nullable(); // 'manual', 'system', 'import', 'api'
            $table->string('import_batch_id')->nullable(); // For bulk imports
            $table->json('metadata')->nullable(); // Additional system data
            
            // Attachments
            $table->json('attachments')->nullable(); // Array of file paths
            $table->string('receipt_file_path')->nullable();
            $table->string('invoice_file_path')->nullable();
            
            // Barcode/QR code tracking
            $table->string('barcode_scanned')->nullable();
            $table->string('qr_code_scanned')->nullable();
            $table->json('scanned_barcodes')->nullable(); // Array of scanned barcodes
            
            // Audit trail
            $table->json('audit_trail')->nullable(); // Array of changes and history
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            // Reconciliation
            $table->boolean('is_reconciled')->default(false);
            $table->date('reconciled_at')->nullable();
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reconciliation_notes')->nullable();
            
            // Reporting and analytics
            $table->string('category')->nullable(); // For reporting purposes
            $table->json('tags')->nullable(); // Internal tags
            $table->string('project_code')->nullable(); // If tied to specific project
            $table->string('cost_center')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['transaction_type', 'transaction_date']);
            $table->index(['created_by']);
            $table->index(['supplier_id']);
            $table->index(['purchase_request_id']);
            $table->index(['location_from', 'location_to']);
            $table->index(['status']);
            $table->index(['transaction_date', 'transaction_type']);
            $table->index(['batch_number']);
            $table->index(['reference_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
