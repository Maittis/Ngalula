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
        Schema::create('inventory_purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique(); // PR-2024-001 format
            $table->text('title');
            $table->text('description')->nullable();
            
            // Request information
            $table->enum('request_type', ['new_item', 'reorder', 'emergency', 'bulk_purchase', 'replacement'])->default('reorder');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent', 'critical'])->default('medium');
            $table->enum('status', ['draft', 'submitted', 'pending_approval', 'approved', 'rejected', 'ordered', 'received', 'completed', 'cancelled'])->default('draft');
            
            // Requester information
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->string('requester_department')->nullable();
            $table->string('requester_position')->nullable();
            
            // Approval workflow
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            
            // Financial information
            $table->decimal('estimated_total', 12, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->enum('budget_approval', ['pending', 'approved', 'rejected', 'not_required'])->default('pending');
            $table->foreignId('budget_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('budget_approved_at')->nullable();
            
            // Supplier information
            $table->foreignId('preferred_supplier_id')->nullable()->constrained('inventory_suppliers')->onDelete('set null');
            $table->string('supplier_contact')->nullable();
            $table->json('alternative_suppliers')->nullable(); // Array of alternative suppliers
            
            // Delivery information
            $table->date('requested_delivery_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->string('delivery_address')->nullable();
            $table->string('delivery_instructions')->nullable();
            $table->enum('delivery_method', ['standard', 'express', 'overnight', 'pickup'])->default('standard');
            
            // Request items (will be stored in separate table)
            $table->decimal('total_items', 10, 2)->default(0);
            $table->decimal('total_quantity', 10, 2)->default(0);
            
            // Timeline and deadlines
            $table->date('date_needed_by')->nullable();
            $table->integer('lead_time_days')->nullable();
            $table->boolean('is_time_sensitive')->default(false);
            $table->text('time_sensitivity_notes')->nullable();
            
            // Justification and purpose
            $table->text('justification')->nullable();
            $table->text('purpose')->nullable();
            $table->text('impact_of_not_purchasing')->nullable();
            $table->json('alternative_solutions')->nullable(); // Alternative to purchasing
            
            // Attachments and documents
            $table->json('attachments')->nullable(); // Array of file paths
            $table->string('quote_file_path')->nullable();
            $table->string('specification_file_path')->nullable();
            $table->json('supporting_documents')->nullable();
            
            // Internal tracking
            $table->string('project_code')->nullable(); // If tied to specific project
            $table->string('cost_center')->nullable();
            $table->string('department_code')->nullable();
            $table->json('internal_tags')->nullable();
            
            // Communication log
            $table->json('communication_history')->nullable(); // Array of communication entries
            $table->string('last_communication_date')->nullable();
            $table->text('communication_notes')->nullable();
            
            // Order information (when converted to actual order)
            $table->string('purchase_order_number')->nullable();
            $table->date('order_date')->nullable();
            $table->foreignId('ordered_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Receiving information
            $table->date('received_date')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('receiving_notes')->nullable();
            $table->json('receiving_discrepancies')->nullable(); // Issues with received items
            
            // Quality control
            $table->enum('quality_check_status', ['pending', 'passed', 'failed', 'partial'])->nullable();
            $table->date('quality_checked_at')->nullable();
            $table->foreignId('quality_checked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('quality_check_notes')->nullable();
            
            // Invoice and payment
            $table->string('invoice_number')->nullable();
            $table->decimal('actual_total', 12, 2)->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('payment_due_date')->nullable();
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'overdue'])->nullable();
            
            // Performance metrics
            $table->integer('days_to_approval')->nullable();
            $table->integer('days_to_delivery')->nullable();
            $table->decimal('budget_variance', 10, 2)->nullable(); // Difference between estimated and actual
            $table->decimal('delivery_variance', 10, 2)->nullable(); // Difference between expected and actual delivery
            
            // Notes and comments
            $table->text('internal_notes')->nullable();
            $table->text('supplier_notes')->nullable();
            $table->json('comments')->nullable(); // Array of comments with timestamps
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['request_type', 'status']);
            $table->index(['priority', 'status']);
            $table->index(['requested_by']);
            $table->index(['approved_by']);
            $table->index(['preferred_supplier_id']);
            $table->index(['requested_delivery_date']);
            $table->index(['purchase_order_number']);
            $table->index(['status', 'date_needed_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_purchase_requests');
    }
};
