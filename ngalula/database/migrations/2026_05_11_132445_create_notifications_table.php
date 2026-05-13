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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Notification details
            $table->string('type'); // booking_reminder, payment_success, etc.
            $table->string('title');
            $table->text('message');
            $table->text('data')->nullable(); // JSON data for additional context
            
            // Channels
            $table->json('channels'); // ['email', 'sms', 'push', 'whatsapp']
            
            // Status
            $table->enum('status', ['pending', 'processing', 'sent', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            
            // Delivery tracking
            $table->json('delivery_status')->nullable(); // Track status per channel
            $table->integer('retry_count')->default(0);
            $table->timestamp('last_retry_at')->nullable();
            
            // Scheduling
            $table->timestamp('scheduled_at')->nullable();
            $table->boolean('is_scheduled')->default(false);
            
            // Priority and urgency
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            // Metadata
            $table->morphs('notifiable'); // Can be related to any model
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index(['priority', 'status']);
            $table->index(['scheduled_at']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
