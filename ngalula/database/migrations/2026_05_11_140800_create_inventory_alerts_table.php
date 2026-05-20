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
        Schema::create('inventory_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_number')->unique(); // ALERT-2024-001 format
            
            // Alert type and classification
            $table->enum('alert_type', ['low_stock', 'out_of_stock', 'expiring_soon', 'expired', 'overstock', 'reorder_needed', 'quality_issue', 'maintenance_due', 'price_change', 'supplier_issue', 'system_error']);
            $table->enum('severity', ['info', 'warning', 'critical', 'emergency'])->default('warning');
            $table->enum('status', ['active', 'acknowledged', 'resolved', 'dismissed', 'escalated'])->default('active');
            
            // Item information (polymorphic relationship)
            $table->morphs('alertable'); // Can be product, oil, cream, equipment, supplier
            
            // Alert details
            $table->string('title');
            $table->text('message');
            $table->text('description')->nullable();
            
            // Current and threshold values
            $table->decimal('current_value', 10, 2)->nullable(); // Current stock level, days to expiry, etc.
            $table->decimal('threshold_value', 10, 2)->nullable(); // Threshold that triggered the alert
            $table->string('unit')->nullable(); // e.g., 'units', 'days', 'percentage'
            
            // Location information
            $table->string('location')->nullable();
            $table->string('warehouse')->nullable();
            $table->string('storage_location')->nullable();
            
            // Timing information
            $table->date('triggered_at');
            $table->date('first_detected_at')->nullable();
            $table->date('last_occurrence_at')->nullable();
            $table->integer('occurrence_count')->default(1);
            
            // People involved
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            // Resolution information
            $table->date('acknowledged_at')->nullable();
            $table->date('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->enum('resolution_method', ['auto_resolved', 'manual_resolved', 'system_corrected', 'supplier_action', 'user_action', 'escalation'])->nullable();
            
            // Escalation information
            $table->boolean('is_escalated')->default(false);
            $table->date('escalated_at')->nullable();
            $table->foreignId('escalated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('escalated_to')->nullable()->constrained('users')->onDelete('set null');
            $table->text('escalation_reason')->nullable();
            
            // Notification settings
            $table->json('notification_channels')->nullable(); // Array of channels used
            $table->json('notified_users')->nullable(); // Array of user IDs notified
            $table->date('last_notification_sent_at')->nullable();
            $table->integer('notification_count')->default(0);
            $table->boolean('notifications_enabled')->default(true);
            
            // Recurring alerts
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // e.g., 'daily', 'weekly', 'monthly'
            $table->date('next_occurrence')->nullable();
            $table->date('recurrence_end')->nullable();
            
            // Impact assessment
            $table->enum('business_impact', ['low', 'medium', 'high', 'critical'])->nullable();
            $table->text('impact_description')->nullable();
            $table->decimal('potential_cost', 10, 2)->nullable(); // Potential financial impact
            $table->integer('affected_customers')->nullable(); // Number of customers potentially affected
            
            // Recommended actions
            $table->json('recommended_actions')->nullable(); // Array of suggested actions
            $table->text('action_taken')->nullable(); // What was actually done
            $table->date('action_taken_at')->nullable();
            
            // System information
            $table->string('source_system')->nullable(); // e.g., 'inventory_system', 'erp', 'manual'
            $table->string('trigger_rule')->nullable(); // Rule that triggered this alert
            $table->json('system_data')->nullable(); // Additional system data
            
            // Related entities
            $table->foreignId('related_transaction_id')->nullable()->constrained('inventory_transactions')->onDelete('set null');
            $table->foreignId('related_purchase_request_id')->nullable()->constrained('inventory_purchase_requests')->onDelete('set null');
            $table->json('related_alerts')->nullable(); // Array of related alert IDs
            
            // Categories and tags
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            
            // Attachments
            $table->json('attachments')->nullable(); // Array of file paths
            
            // External references
            $table->string('external_reference')->nullable();
            $table->string('external_system')->nullable();
            
            // Audit trail
            $table->json('history')->nullable(); // Array of status changes and actions
            $table->string('ip_address')->nullable();
            
            // Analytics and reporting
            $table->date('resolved_within_sla')->nullable(); // Resolved within SLA timeframe
            $table->integer('resolution_time_minutes')->nullable();
            $table->decimal('resolution_cost', 10, 2)->nullable();
            
            // Notes and comments
            $table->text('internal_notes')->nullable();
            $table->json('comments')->nullable(); // Array of comments with timestamps
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['alert_type', 'status']);
            $table->index(['severity', 'status']);
            $table->index(['triggered_at']);
            $table->index(['created_by']);
            $table->index(['assigned_to']);
            $table->index(['acknowledged_at']);
            $table->index(['resolved_at']);
            $table->index(['is_escalated']);
            $table->index(['next_occurrence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_alerts');
    }
};
